<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $customers = $query->paginate($perPage);

            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('Error fetching customers: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active customers for dropdown/select
     */
    public function active(Request $request)
    {
        try {
            $query = Customer::active();

            // Search filter for autocomplete
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $customers = $query->orderBy('customer_name')
                ->select('id', 'customer_code', 'customer_name', 'phone', 'email')
                ->get()
                ->map(function ($customer) {
                    return [
                        'value' => $customer->id,
                        'label' => $customer->full_info,
                        'customer_code' => $customer->customer_code,
                        'customer_name' => $customer->customer_name,
                        'phone' => $customer->phone,
                        'email' => $customer->email,
                    ];
                });

            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('Error fetching active customers: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching active customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            DB::beginTransaction();

            $customer = Customer::create($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Customer created successfully',
                'data' => $customer
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating customer: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        try {
            $customer->load(['sales' => function ($query) {
                $query->orderBy('transaction_date', 'desc')->limit(10);
            }]);

            // Calculate customer statistics
            $stats = [
                'total_sales' => $customer->sales()->count(),
                'total_revenue' => $customer->sales()->where('status', 'posted')->sum('total_amount'),
                'last_purchase_date' => $customer->sales()->where('status', 'posted')->max('transaction_date'),
            ];

            return response()->json([
                'data' => $customer,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching customer: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        try {
            DB::beginTransaction();

            $customer->update($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Customer updated successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating customer: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer (soft delete).
     */
    public function destroy(Customer $customer)
    {
        try {
            // Check if customer has sales
            if ($customer->sales()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete customer with existing sales transactions. Please set status to inactive instead.'
                ], 422);
            }

            DB::beginTransaction();

            // Soft delete the customer
            $customer->delete();

            DB::commit();

            return response()->json([
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting customer: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error deleting customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted customer.
     */
    public function restore($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);

            DB::beginTransaction();

            $customer->restore();

            DB::commit();

            return response()->json([
                'message' => 'Customer restored successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring customer: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error restoring customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force delete a customer (permanent deletion).
     */
    public function forceDelete($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);

            // Check if customer has sales
            if ($customer->sales()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot permanently delete customer with existing sales transactions.'
                ], 422);
            }

            DB::beginTransaction();

            $customer->forceDelete();

            DB::commit();

            return response()->json([
                'message' => 'Customer permanently deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting customer: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error permanently deleting customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trashed (soft deleted) customers.
     */
    public function trashed(Request $request)
    {
        try {
            $query = Customer::onlyTrashed();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'deleted_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $customers = $query->paginate($perPage);

            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('Error fetching trashed customers: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching trashed customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle customer status (active/inactive)
     */
    public function toggleStatus(Customer $customer)
    {
        try {
            DB::beginTransaction();

            $newStatus = $customer->status === 'active' ? 'inactive' : 'active';
            $customer->update(['status' => $newStatus]);

            DB::commit();

            return response()->json([
                'message' => 'Customer status updated successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling customer status: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error toggling customer status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer statistics
     */
    public function statistics(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $query = Customer::query();

            $stats = [
                'total_customers' => $query->count(),
                'active_customers' => $query->where('status', 'active')->count(),
                'inactive_customers' => $query->where('status', 'inactive')->count(),
                'new_customers_this_month' => Customer::whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'))
                    ->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error fetching customer statistics: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export customers to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Customer::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $customers = $query->orderBy('customer_name')->get();

            $filename = 'customers_export_' . now()->format('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($customers) {
                $file = fopen('php://output', 'w');

                // Headers
                fputcsv($file, [
                    'Customer Code',
                    'Customer Name',
                    'Address',
                    'Phone',
                    'Email',
                    'Status',
                    'Notes',
                    'Created At',
                ]);

                // Data
                foreach ($customers as $customer) {
                    fputcsv($file, [
                        $customer->customer_code,
                        $customer->customer_name,
                        $customer->address ?? '-',
                        $customer->phone ?? '-',
                        $customer->email ?? '-',
                        ucfirst($customer->status),
                        $customer->notes ?? '-',
                        $customer->created_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting customers: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error exporting customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
