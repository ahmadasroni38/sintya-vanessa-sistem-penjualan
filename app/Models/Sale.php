<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\StockBalanceService;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'customer_id',
        'location_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_method',
        'notes',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    protected $appends = ['total_quantity', 'total_items'];

    /**
     * Relationship to sale details
     */
    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Relationship to customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship to location
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relationship to user who created
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to user who posted
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Accessor for total quantity
     */
    public function getTotalQuantityAttribute()
    {
        return $this->details->sum('quantity');
    }

    /**
     * Accessor for total items
     */
    public function getTotalItemsAttribute()
    {
        return $this->details->count();
    }

    /**
     * Method to post the sale
     */
    public function post($userId)
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft transactions can be posted');
        }

        if ($this->details()->count() === 0) {
            throw new \Exception('Cannot post sale without details');
        }

        DB::transaction(function () use ($userId) {
            // Update status
            $this->update([
                'status' => 'posted',
                'posted_by' => $userId,
                'posted_at' => now(),
            ]);

            // Create stock card entries for each detail
            $this->createStockCards();

            // Update stock balances for all affected products
            $this->updateStockBalances();

            // Create journal entry for accounting
            $this->createJournalEntry();
        });

        return $this;
    }

    /**
     * Method to create stock card entries
     */
    protected function createStockCards()
    {
        foreach ($this->details as $detail) {
            // Get previous balance
            $previousCard = StockCard::where('product_id', $detail->product_id)
                ->where('location_id', $this->location_id)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $previousBalance = $previousCard ? $previousCard->balance : 0;

            // Create new stock card
            StockCard::create([
                'product_id' => $detail->product_id,
                'location_id' => $this->location_id,
                'transaction_date' => $this->transaction_date,
                'transaction_type' => 'sale',
                'reference_id' => $this->id,
                'reference_number' => $this->transaction_number,
                'quantity_in' => 0,
                'quantity_out' => $detail->quantity,
                'balance' => $previousBalance - $detail->quantity,
                'unit_price' => $detail->unit_price,
                'notes' => $detail->notes ?? $this->notes,
            ]);
        }
    }

    /**
     * Update stock balances for all products in this sale
     */
    protected function updateStockBalances()
    {
        $productLocations = [];

        foreach ($this->details as $detail) {
            $productLocations[] = [
                'product_id' => $detail->product_id,
                'location_id' => $this->location_id,
            ];
        }

        // Remove duplicates and update balances
        $uniqueProductLocations = array_unique($productLocations, SORT_REGULAR);

        StockBalanceService::updateBalancesFromTransaction(
            $uniqueProductLocations,
            'sale',
            $this->transaction_date->format('Y-m-d')
        );
    }

    /**
     * Method to create journal entry for accounting
     */
    protected function createJournalEntry()
    {
        // Create journal entry for sales transaction
        $journalEntry = JournalEntry::create([
            'entry_number' => JournalEntry::generateEntryNumber(),
            'entry_date' => $this->transaction_date,
            'description' => 'Sales Transaction - ' . $this->transaction_number . ($this->customer ? ' - ' . $this->customer->customer_name : ''),
            'reference_number' => $this->transaction_number,
            'entry_type' => 'general',
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->posted_by,
            'posted_at' => $this->posted_at,
        ]);

        // Debit Cash/Bank account (depending on payment method)
        // 1-1100 = Kas (Cash), 1-1200 = Bank
        $accountCode = $this->payment_method === 'cash' ? '1-1100' : '1-1200';
        $cashAccount = ChartOfAccount::where('account_code', $accountCode)->active()->first();

        if ($cashAccount) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $cashAccount->id,
                'transaction_type' => 'debit',
                'amount' => $this->total_amount,
                'description' => $this->payment_method === 'cash' ? 'Cash received from sale' : 'Bank transfer received from sale',
            ]);
        }

        // Credit Sales Revenue account
        // 4-1000 = Pendapatan Penjualan (Sales Revenue)
        $salesAccount = ChartOfAccount::where('account_code', '4-1000')->active()->first();

        if ($salesAccount) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salesAccount->id,
                'transaction_type' => 'credit',
                'amount' => $this->subtotal,
                'description' => 'Sales revenue - ' . $this->transaction_number,
            ]);
        }

        // Credit Tax Payable account if applicable
        if ($this->tax_amount > 0) {
            // 2-1300 = Hutang Pajak (Tax Payable)
            $taxAccount = ChartOfAccount::where('account_code', '2-1300')->active()->first();

            if ($taxAccount) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $taxAccount->id,
                    'transaction_type' => 'credit',
                    'amount' => $this->tax_amount,
                    'description' => 'Sales tax payable - ' . $this->transaction_number,
                ]);
            }
        }

        // Update account balances
        if ($cashAccount) {
            $cashAccount->updateCurrentBalance();
        }
        if ($salesAccount) {
            $salesAccount->updateCurrentBalance();
        }
        if (isset($taxAccount)) {
            $taxAccount->updateCurrentBalance();
        }

        return $journalEntry;
    }

    /**
     * Calculate total amounts from details
     */
    public function calculateTotal()
    {
        $subtotal = $this->details()->sum('total_price');
        $taxAmount = $this->details()->sum('tax_amount');
        $discountAmount = $this->details()->sum('discount_amount');

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $subtotal + $taxAmount - $discountAmount,
        ]);
    }

    /**
     * Auto-generate transaction number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->transaction_number)) {
                $lastSale = static::withTrashed()
                    ->whereYear('created_at', date('Y'))
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastSale ? intval(substr($lastSale->transaction_number, -5)) + 1 : 1;
                $sale->transaction_number = 'SO-' . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Export sales to CSV
     */
    public static function exportToCSV($query)
    {
        $filename = 'sales_export_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Transaction Number',
                'Transaction Date',
                'Customer Code',
                'Customer Name',
                'Location',
                'Payment Method',
                'Subtotal',
                'Tax Amount',
                'Discount Amount',
                'Total Amount',
                'Paid Amount',
                'Change Amount',
                'Status',
                'Created By',
                'Posted By',
                'Notes',
            ]);

            // Data
            $sales = $query->with(['customer', 'location', 'creator', 'poster'])->get();

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->transaction_number,
                    $sale->transaction_date->format('Y-m-d'),
                    $sale->customer?->customer_code ?? '-',
                    $sale->customer?->customer_name ?? 'Walk-in Customer',
                    $sale->location?->name ?? '-',
                    ucfirst($sale->payment_method),
                    $sale->subtotal,
                    $sale->tax_amount,
                    $sale->discount_amount,
                    $sale->total_amount,
                    $sale->paid_amount,
                    $sale->change_amount,
                    ucfirst($sale->status),
                    $sale->creator?->name ?? '-',
                    $sale->poster?->name ?? '-',
                    $sale->notes ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
