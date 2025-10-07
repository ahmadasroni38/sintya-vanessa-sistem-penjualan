<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the work orders.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorkOrder::with(['asset', 'location', 'requester', 'assignedUser']);

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->byStatus($request->status);
        }

        if ($request->has('priority') && !empty($request->priority)) {
            $query->byPriority($request->priority);
        }

        if ($request->has('type') && !empty($request->type)) {
            $query->byType($request->type);
        }

        if ($request->has('asset_id') && !empty($request->asset_id)) {
            $query->byAsset($request->asset_id);
        }

        if ($request->has('location_id') && !empty($request->location_id)) {
            $query->byLocation($request->location_id);
        }

        if ($request->has('assigned_to') && !empty($request->assigned_to)) {
            $query->byAssignedUser($request->assigned_to);
        }

        if ($request->has('requester_id') && !empty($request->requester_id)) {
            $query->byRequester($request->requester_id);
        }

        // Filter by due date range
        if ($request->has('due_date_from') && !empty($request->due_date_from)) {
            $query->where('due_date', '>=', $request->due_date_from);
        }

        if ($request->has('due_date_to') && !empty($request->due_date_to)) {
            $query->where('due_date', '<=', $request->due_date_to);
        }

        // Special filters
        if ($request->has('overdue') && $request->overdue) {
            $query->overdue();
        }

        if ($request->has('due_soon') && $request->due_soon) {
            $query->dueSoon($request->get('due_soon_days', 7));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $workOrders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $workOrders,
        ]);
    }

    /**
     * Store a newly created work order.
     */
    public function store(Request $request): JsonResponse
    {
        // Prepare materials and attachments data
        $requestData = $request->all();
        if (isset($requestData['materials_needed'])) {
            if (is_string($requestData['materials_needed'])) {
                $materials = json_decode($requestData['materials_needed'], true);
                $requestData['materials_needed'] = is_array($materials) ? $materials : [];
            } elseif (!is_array($requestData['materials_needed'])) {
                $requestData['materials_needed'] = [];
            }
        }

        if (isset($requestData['attachments'])) {
            if (is_string($requestData['attachments'])) {
                $attachments = json_decode($requestData['attachments'], true);
                $requestData['attachments'] = is_array($attachments) ? $attachments : [];
            } elseif (!is_array($requestData['attachments'])) {
                $requestData['attachments'] = [];
            }
        }

        $validator = Validator::make($requestData, [
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:work_orders,code',
            'description' => 'nullable|string|max:2000',
            'type' => 'required|in:maintenance,repair,installation,inspection,upgrade,replacement',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'nullable|in:draft,pending,assigned,in_progress,on_hold,completed,cancelled',
            'asset_id' => 'nullable|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:now',
            'estimated_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'materials_needed' => 'nullable|array',
            'attachments' => 'nullable|array',
            'notes' => 'nullable|string|max:2000',
            'is_recurring' => 'nullable|boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurring_interval' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Set requester to current user if not provided
        $requestData['requester_id'] = $requestData['requester_id'] ?? Auth::id();
        $requestData['status'] = $requestData['status'] ?? 'pending';

        $workOrder = WorkOrder::create($requestData);

        // Load relationships for response
        $workOrder->load(['asset', 'location', 'requester', 'assignedUser']);

        return response()->json([
            'success' => true,
            'message' => 'Work order created successfully',
            'data' => $this->formatWorkOrderResponse($workOrder),
        ], 201);
    }

    /**
     * Display the specified work order.
     */
    public function show(WorkOrder $workOrder): JsonResponse
    {
        $workOrder->load(['asset', 'location', 'requester', 'assignedUser']);

        return response()->json([
            'success' => true,
            'data' => $this->formatWorkOrderResponse($workOrder),
        ]);
    }

    /**
     * Update the specified work order.
     */
    public function update(Request $request, WorkOrder $workOrder): JsonResponse
    {
        // Prepare materials and attachments data for update
        $requestData = $request->all();
        if (isset($requestData['materials_needed'])) {
            if (is_string($requestData['materials_needed'])) {
                $materials = json_decode($requestData['materials_needed'], true);
                $requestData['materials_needed'] = is_array($materials) ? $materials : [];
            } elseif (!is_array($requestData['materials_needed'])) {
                $requestData['materials_needed'] = [];
            }
        }

        if (isset($requestData['attachments'])) {
            if (is_string($requestData['attachments'])) {
                $attachments = json_decode($requestData['attachments'], true);
                $requestData['attachments'] = is_array($attachments) ? $attachments : [];
            } elseif (!is_array($requestData['attachments'])) {
                $requestData['attachments'] = [];
            }
        }

        $validator = Validator::make($requestData, [
            'title' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:work_orders,code,' . $workOrder->id,
            'description' => 'nullable|string|max:2000',
            'type' => 'sometimes|required|in:maintenance,repair,installation,inspection,upgrade,replacement',
            'priority' => 'sometimes|required|in:low,medium,high,critical',
            'status' => 'sometimes|required|in:draft,pending,assigned,in_progress,on_hold,completed,cancelled',
            'asset_id' => 'nullable|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'materials_needed' => 'nullable|array',
            'attachments' => 'nullable|array',
            'notes' => 'nullable|string|max:2000',
            'completion_notes' => 'nullable|string|max:2000',
            'is_recurring' => 'nullable|boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurring_interval' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [];

        // Only update provided fields
        $allowedFields = [
            'title', 'code', 'description', 'type', 'priority', 'status',
            'asset_id', 'location_id', 'assigned_to', 'due_date', 'started_at',
            'completed_at', 'estimated_hours', 'actual_hours', 'estimated_cost',
            'actual_cost', 'materials_needed', 'attachments', 'notes',
            'completion_notes', 'is_recurring', 'recurring_frequency', 'recurring_interval'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $updateData[$field] = $requestData[$field];
            }
        }

        if (!empty($updateData)) {
            $workOrder->update($updateData);
        }

        // Reload relationships
        $workOrder->load(['asset', 'location', 'requester', 'assignedUser']);

        return response()->json([
            'success' => true,
            'message' => 'Work order updated successfully',
            'data' => $this->formatWorkOrderResponse($workOrder),
        ]);
    }

    /**
     * Remove the specified work order.
     */
    public function destroy(WorkOrder $workOrder): JsonResponse
    {
        $workOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Work order deleted successfully',
        ]);
    }

    /**
     * Start a work order.
     */
    public function start(WorkOrder $workOrder): JsonResponse
    {
        if ($workOrder->status === 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Work order is already in progress',
            ], 422);
        }

        if ($workOrder->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Work order is already completed',
            ], 422);
        }

        if ($workOrder->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot start a cancelled work order',
            ], 422);
        }

        $workOrder->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Work order started successfully',
            'data' => $this->formatWorkOrderResponse($workOrder->load(['asset', 'location', 'requester', 'assignedUser'])),
        ]);
    }

    /**
     * Update work order status.
     */
    public function updateStatus(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,pending,assigned,in_progress,on_hold,completed,cancelled',
            'completion_notes' => 'nullable|string|max:2000',
            'actual_hours' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $workOrder->status;
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];

        // Handle status-specific logic
        if ($newStatus === 'in_progress' && $workOrder->status !== 'in_progress') {
            $updateData['started_at'] = now();
        }

        if ($newStatus === 'completed' && $workOrder->status !== 'completed') {
            $updateData['completed_at'] = now();
            if ($request->has('completion_notes')) {
                $updateData['completion_notes'] = $request->completion_notes;
            }
            if ($request->has('actual_hours')) {
                $updateData['actual_hours'] = $request->actual_hours;
            }
            if ($request->has('actual_cost')) {
                $updateData['actual_cost'] = $request->actual_cost;
            }
        }

        $workOrder->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Work order status updated successfully",
            'data' => [
                'work_order' => $this->formatWorkOrderResponse($workOrder->load(['asset', 'location', 'requester', 'assignedUser'])),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
        ]);
    }

    /**
     * Bulk update work order status.
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'work_order_ids' => 'required|array|min:1',
            'work_order_ids.*' => 'required|integer|exists:work_orders,id',
            'status' => 'required|in:draft,pending,assigned,in_progress,on_hold,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $workOrderIds = $request->work_order_ids;
        $status = $request->status;

        // Update all work orders at once
        WorkOrder::whereIn('id', $workOrderIds)->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => count($workOrderIds) . ' work order' . (count($workOrderIds) === 1 ? '' : 's') . ' status updated successfully',
            'data' => [
                'updated_count' => count($workOrderIds),
                'new_status' => $status,
            ],
        ]);
    }

    /**
     * Export work orders to CSV.
     */
    public function export(Request $request)
    {
        $query = WorkOrder::with(['asset', 'location', 'requester', 'assignedUser']);

        // Apply the same filters as index method
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->byStatus($request->status);
        }

        if ($request->has('priority') && !empty($request->priority)) {
            $query->byPriority($request->priority);
        }

        if ($request->has('type') && !empty($request->type)) {
            $query->byType($request->type);
        }

        if ($request->has('asset_id') && !empty($request->asset_id)) {
            $query->byAsset($request->asset_id);
        }

        if ($request->has('location_id') && !empty($request->location_id)) {
            $query->byLocation($request->location_id);
        }

        if ($request->has('assigned_to') && !empty($request->assigned_to)) {
            $query->byAssignedUser($request->assigned_to);
        }

        $workOrders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'work_orders_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($workOrders) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Code',
                'Title',
                'Type',
                'Priority',
                'Status',
                'Asset',
                'Location',
                'Requester',
                'Assigned To',
                'Requested Date',
                'Due Date',
                'Started At',
                'Completed At',
                'Estimated Hours',
                'Actual Hours',
                'Estimated Cost',
                'Actual Cost',
                'Description',
                'Notes',
                'Completion Notes',
                'Created At',
            ]);

            // CSV data
            foreach ($workOrders as $workOrder) {
                fputcsv($file, [
                    $workOrder->id,
                    $workOrder->code,
                    $workOrder->title,
                    $workOrder->type,
                    $workOrder->priority,
                    $workOrder->status,
                    $workOrder->asset ? $workOrder->asset->name : '',
                    $workOrder->location ? $workOrder->location->name : '',
                    $workOrder->requester ? $workOrder->requester->name : '',
                    $workOrder->assignedUser ? $workOrder->assignedUser->name : '',
                    $workOrder->requested_date ? $workOrder->requested_date->format('Y-m-d H:i:s') : '',
                    $workOrder->due_date ? $workOrder->due_date->format('Y-m-d H:i:s') : '',
                    $workOrder->started_at ? $workOrder->started_at->format('Y-m-d H:i:s') : '',
                    $workOrder->completed_at ? $workOrder->completed_at->format('Y-m-d H:i:s') : '',
                    $workOrder->estimated_hours,
                    $workOrder->actual_hours,
                    $workOrder->estimated_cost,
                    $workOrder->actual_cost,
                    $workOrder->description,
                    $workOrder->notes,
                    $workOrder->completion_notes,
                    $workOrder->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get work order statistics.
     */
    public function statistics(): JsonResponse
    {
        $totalWorkOrders = WorkOrder::count();
        $pendingWorkOrders = WorkOrder::pending()->count();
        $inProgressWorkOrders = WorkOrder::inProgress()->count();
        $completedWorkOrders = WorkOrder::completed()->count();
        $overdueWorkOrders = WorkOrder::overdue()->count();

        $workOrdersByStatus = WorkOrder::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $workOrdersByPriority = WorkOrder::select('priority')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        $workOrdersByType = WorkOrder::select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return response()->json([
            'success' => true,
            'data' => [
                'total_work_orders' => $totalWorkOrders,
                'pending_work_orders' => $pendingWorkOrders,
                'in_progress_work_orders' => $inProgressWorkOrders,
                'completed_work_orders' => $completedWorkOrders,
                'overdue_work_orders' => $overdueWorkOrders,
                'work_orders_by_status' => $workOrdersByStatus,
                'work_orders_by_priority' => $workOrdersByPriority,
                'work_orders_by_type' => $workOrdersByType,
            ],
        ]);
    }

    /**
     * Get user options for assignment.
     */
    public function userOptions(): JsonResponse
    {
        $users = User::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($user) {
                return [
                    'value' => $user->id,
                    'label' => $user->name,
                    'email' => $user->email,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get asset options for work orders.
     */
    public function assetOptions(): JsonResponse
    {
        $assets = Asset::active()
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get()
            ->map(function ($asset) {
                return [
                    'value' => $asset->id,
                    'label' => $asset->name,
                    'code' => $asset->code,
                    'category' => $asset->category ? $asset->category->name : null,
                    'location' => $asset->location ? $asset->location->name : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * Get location options for work orders.
     */
    public function locationOptions(): JsonResponse
    {
        $locations = Location::orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(function ($location) {
                return [
                    'value' => $location->id,
                    'label' => $location->name,
                    'code' => $location->code,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    /**
     * Format work order response data.
     */
    private function formatWorkOrderResponse(WorkOrder $workOrder): array
    {
        return [
            'id' => $workOrder->id,
            'title' => $workOrder->title,
            'code' => $workOrder->code,
            'description' => $workOrder->description,
            'type' => $workOrder->type,
            'priority' => $workOrder->priority,
            'status' => $workOrder->status,
            'asset_id' => $workOrder->asset_id,
            'asset' => $workOrder->asset ? [
                'id' => $workOrder->asset->id,
                'name' => $workOrder->asset->name,
                'code' => $workOrder->asset->code,
            ] : null,
            'location_id' => $workOrder->location_id,
            'location' => $workOrder->location ? [
                'id' => $workOrder->location->id,
                'name' => $workOrder->location->name,
                'code' => $workOrder->location->code,
            ] : null,
            'requester_id' => $workOrder->requester_id,
            'requester' => $workOrder->requester ? [
                'id' => $workOrder->requester->id,
                'name' => $workOrder->requester->name,
                'email' => $workOrder->requester->email,
            ] : null,
            'assigned_to' => $workOrder->assigned_to,
            'assigned_user' => $workOrder->assignedUser ? [
                'id' => $workOrder->assignedUser->id,
                'name' => $workOrder->assignedUser->name,
                'email' => $workOrder->assignedUser->email,
            ] : null,
            'requested_date' => $workOrder->requested_date,
            'due_date' => $workOrder->due_date,
            'started_at' => $workOrder->started_at,
            'completed_at' => $workOrder->completed_at,
            'estimated_hours' => $workOrder->estimated_hours,
            'actual_hours' => $workOrder->actual_hours,
            'estimated_cost' => $workOrder->estimated_cost,
            'actual_cost' => $workOrder->actual_cost,
            'materials_needed' => $workOrder->materials_needed,
            'attachments' => $workOrder->attachments,
            'notes' => $workOrder->notes,
            'completion_notes' => $workOrder->completion_notes,
            'is_recurring' => $workOrder->is_recurring,
            'recurring_frequency' => $workOrder->recurring_frequency,
            'recurring_interval' => $workOrder->recurring_interval,
            'next_occurrence' => $workOrder->next_occurrence,
            'duration' => $workOrder->duration,
            'days_until_due' => $workOrder->days_until_due,
            'cost_variance' => $workOrder->cost_variance,
            'hours_variance' => $workOrder->hours_variance,
            'is_overdue' => $workOrder->isOverdue(),
            'is_due_soon' => $workOrder->isDueSoon(),
            'is_completed' => $workOrder->isCompleted(),
            'is_in_progress' => $workOrder->isInProgress(),
            'is_pending' => $workOrder->isPending(),
            'created_at' => $workOrder->created_at,
            'updated_at' => $workOrder->updated_at,
        ];
    }
}
