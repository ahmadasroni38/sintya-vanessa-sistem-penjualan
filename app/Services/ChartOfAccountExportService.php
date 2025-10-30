<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ChartOfAccountExportService
{
    /**
     * Export chart of accounts to specified format
     *
     * @param array $options Export options
     * @return string Generated filename
     */
    public function export(array $options): string
    {
        $format = $options['format'] ?? 'excel';
        $filters = $options['filters'] ?? [];

        try {
            switch (strtolower($format)) {
                case 'excel':
                    return $this->exportToExcel($filters);
                case 'pdf':
                    return $this->exportToPdf($filters);
                default:
                    throw new \InvalidArgumentException("Unsupported export format: {$format}");
            }
        } catch (\Exception $e) {
            Log::error('Export failed', [
                'format' => $format,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Export to Excel format
     *
     * @param array $filters Filter options
     * @return string Generated filename
     */
    protected function exportToExcel(array $filters): string
    {
        // Get filtered accounts
        $accounts = $this->getFilteredAccounts($filters);

        // Prepare data for export
        $exportData = $accounts->map(function ($account) {
            return [
                'Account Code' => $account->account_code,
                'Account Name' => $account->account_name,
                'Account Type' => ucfirst($account->account_type),
                'Normal Balance' => ucfirst($account->normal_balance),
                'Parent Account' => $account->parent ? $account->parent->account_code . ' - ' . $account->parent->account_name : 'None',
                'Level' => $account->level,
                'Opening Balance' => number_format($account->opening_balance, 2, ',', '.'),
                'Current Balance' => number_format($account->current_balance, 2, ',', '.'),
                'Status' => $account->is_active ? 'Active' : 'Inactive',
                'Description' => $account->description ?? '',
                'Created At' => $account->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $account->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // Generate filename
        $filename = 'chart-of-accounts-' . date('Y-m-d-H-i-s') . '.xlsx';
        $filepath = 'exports/' . $filename;

        // Create Excel file
        Excel::store(
            new class($exportData->toArray()) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
                public function collection()
                {
                    return $this->collection;
                }

                public function headings(): array
                {
                    return [
                        'Account Code',
                        'Account Name',
                        'Account Type',
                        'Normal Balance',
                        'Parent Account',
                        'Level',
                        'Opening Balance',
                        'Current Balance',
                        'Status',
                        'Description',
                        'Created At',
                        'Updated At'
                    ];
                }

                public function title(): string
                {
                    return 'Chart of Accounts';
                }
            }
        ), $filepath, 'exports');

        // Add summary sheet
        $this->addExcelSummarySheet($filepath, $accounts);

        return $filename;
    }

    /**
     * Export to PDF format
     *
     * @param array $filters Filter options
     * @return string Generated filename
     */
    protected function exportToPdf(array $filters): string
    {
        // Get filtered accounts
        $accounts = $this->getFilteredAccounts($filters);

        // Generate filename
        $filename = 'chart-of-accounts-' . date('Y-m-d-H-i-s') . '.pdf';
        $filepath = 'exports/' . $filename;

        // Create PDF content
        $pdf = Pdf::loadView('exports.chart-of-accounts', [
            'accounts' => $accounts,
            'filters' => $filters,
            'exportDate' => now()->format('Y-m-d H:i:s'),
            'companyName' => config('app.name', 'Company Name'),
            'summary' => $this->generateSummary($accounts)
        ])
        ->setPaper('a4')
        ->setOrientation('landscape')
        ->setOption('margin-top', 20)
        ->setOption('margin-bottom', 20)
        ->setOption('margin-left', 15)
        ->setOption('margin-right', 15);

        // Save PDF
        Storage::put($filepath, $pdf->output());

        return $filename;
    }

    /**
     * Get filtered accounts based on filters
     *
     * @param array $filters Filter options
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getFilteredAccounts(array $filters)
    {
        $query = ChartOfAccount::with(['parent' => function ($query) {
                $query->select(['id', 'account_code', 'account_name']);
            }]);

        // Apply filters
        if (!empty($filters['type'])) {
            $query->where('account_type', $filters['type']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active'] === '1' || $filters['is_active'] === true);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('account_code')->get();
    }

    /**
     * Add summary sheet to Excel file
     *
     * @param string $filepath Excel file path
     * @param \Illuminate\Database\Eloquent\Collection $accounts Accounts data
     */
    protected function addExcelSummarySheet(string $filepath, $accounts): void
    {
        $summary = [
            'Total Accounts' => $accounts->count(),
            'Active Accounts' => $accounts->where('is_active', true)->count(),
            'Inactive Accounts' => $accounts->where('is_active', false)->count(),
            'Total Assets' => $accounts->where('account_type', 'asset')->count(),
            'Total Liabilities' => $accounts->where('account_type', 'liability')->count(),
            'Total Equity' => $accounts->where('account_type', 'equity')->count(),
            'Total Revenue' => $accounts->where('account_type', 'revenue')->count(),
            'Total Expenses' => $accounts->where('account_type', 'expense')->count(),
            'Export Date' => now()->format('Y-m-d H:i:s'),
            'Exported By' => auth()->user()->name ?? 'System'
        ];

        // Create summary collection
        $summaryData = collect([
            ['Metric', 'Count'],
            ['Total Accounts', $summary['Total Accounts']],
            ['Active Accounts', $summary['Active Accounts']],
            ['Inactive Accounts', $summary['Inactive Accounts']],
            ['Total Assets', $summary['Total Assets']],
            ['Total Liabilities', $summary['Total Liabilities']],
            ['Total Equity', $summary['Total Equity']],
            ['Total Revenue', $summary['Total Revenue']],
            ['Total Expenses', $summary['Total Expenses']],
            ['Export Date', $summary['Export Date']],
            ['Exported By', $summary['Exported By']]
        ]);

        // Append summary to existing Excel file
        Excel::store(
            new class($summaryData->toArray()) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithTitle {
                public function collection()
                {
                    return $this->collection;
                }

                public function headings(): array
                {
                    return ['Metric', 'Count'];
                }

                public function title(): string
                {
                    return 'Summary';
                }
            }
        }, $filepath, 'exports');
    }

    /**
     * Generate summary statistics
     *
     * @param \Illuminate\Database\Eloquent\Collection $accounts Accounts data
     * @return array Summary statistics
     */
    protected function generateSummary($accounts): array
    {
        return [
            'total_accounts' => $accounts->count(),
            'active_accounts' => $accounts->where('is_active', true)->count(),
            'inactive_accounts' => $accounts->where('is_active', false)->count(),
            'by_type' => [
                'asset' => $accounts->where('account_type', 'asset')->count(),
                'liability' => $accounts->where('account_type', 'liability')->count(),
                'equity' => $accounts->where('account_type', 'equity')->count(),
                'revenue' => $accounts->where('account_type', 'revenue')->count(),
                'expense' => $accounts->where('account_type', 'expense')->count(),
            ],
            'by_level' => $accounts->groupBy('level')->map(function ($group) {
                return $group->count();
            })->toArray(),
            'total_opening_balance' => $accounts->sum('opening_balance'),
            'total_current_balance' => $accounts->sum('current_balance'),
        ];
    }

    /**
     * Clean up old export files
     *
     * @param int $days Number of days to keep files
     */
    public function cleanupOldExports(int $days = 7): void
    {
        try {
            $files = Storage::files('exports');
            $cutoffTime = now()->subDays($days);

            foreach ($files as $file) {
                $filePath = 'exports/' . $file['name'];

                if (Storage::lastModified($filePath) < $cutoffTime) {
                    Storage::delete($filePath);
                    Log::info('Deleted old export file', ['file' => $file['name']]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old exports', [
                'error' => $e->getMessage(),
                'days' => $days
            ]);
        }
    }

    /**
     * Get export file info
     *
     * @param string $filename Filename
     * @return array|null File information
     */
    public function getExportFileInfo(string $filename): ?array
    {
        $filepath = 'exports/' . $filename;

        if (!Storage::exists($filepath)) {
            return null;
        }

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => Storage::url($filepath),
            'size' => Storage::size($filepath),
            'last_modified' => Storage::lastModified($filepath),
            'mime_type' => Storage::mimeType($filepath),
        ];
    }

    /**
     * Generate unique filename
     *
     * @param string $format File format
     * @return string Unique filename
     */
    public function generateFilename(string $format): string
    {
        return 'chart-of-accounts-' . date('Y-m-d-H-i-s') . '.' . $format;
    }
}
