<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreventiveMaintenance;
use App\Models\PreventiveMaintenanceExecution;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PreventiveMaintenanceController extends Controller
{
    /**
     * Display a listing of preventive maintenances.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PreventiveMaintenance::with(['asset', 'location', 'creator', 'assignedUser'])
                                     ->nonTemplates();

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

        if ($request->has('maintenance_type') && !empty($request->maintenance_type)) {
            $query->byMaintenanceType($request->maintenance_type);
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

        if ($request->has('frequency_type') && !empty($request->frequency_type)) {
            $query->byFrequency($request->frequency_type);
        }

        // Special filters
        if ($request->has('due') && $request->due) {
            $query->due();
        }

        if ($request->has('overdue') && $request->overdue) {
            $query->overdue();
        }

        if ($request->has('due_soon') && $request->due_soon) {
            $query->dueSoon($request->get('due_soon_days', 7));
        }

        if ($request->has('templates') && $request->templates) {
            $query = PreventiveMaintenance::with(['asset', 'location', 'creator', 'assignedUser'])
                                          ->templates();
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $preventiveMaintenances = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $preventiveMaintenances,
        ]);
    }

    /**
     * Store a newly created preventive maintenance.
     */
    public function store(Request $request): JsonResponse
    {
        // Prepare JSON data
        $requestData = $request->all();
        $jsonFields = ['custom_frequency_settings', 'skip_dates', 'required_materials', 'required_tools', 'safety_requirements', 'checklist_items', 'attachments', 'notification_settings'];

        foreach ($jsonFields as $field) {
            if (isset($requestData[$field])) {
                if (is_string($requestData[$field])) {
                    $decoded = json_decode($requestData[$field], true);
                    $requestData[$field] = is_array($decoded) ? $decoded : [];
                } elseif (!is_array($requestData[$field])) {
                    $requestData[$field] = [];
                }
            }
        }

        $validator = Validator::make($requestData, [
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:preventive_maintenances,code',
            'description' => 'nullable|string|max:2000',
            'maintenance_type' => 'required|in:inspection,cleaning,lubrication,calibration,replacement,testing,adjustment,other',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'nullable|in:active,inactive,suspended,completed',
            'asset_id' => 'required|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_to' => 'nullable|exists:users,id',
            'frequency_type' => 'required|in:daily,weekly,monthly,quarterly,semi_annual,annual,custom',
            'frequency_value' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'estimated_duration_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'work_instructions' => 'nullable|string|max:5000',
            'safety_notes' => 'nullable|string|max:2000',
            'compliance_standard' => 'nullable|string|max:100',
            'requires_certification' => 'nullable|boolean',
            'certification_required' => 'nullable|string|max:100',
            'auto_create_work_orders' => 'nullable|boolean',
            'advance_notice_days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string|max:2000',
            'is_template' => 'nullable|boolean',
            'template_source_id' => 'nullable|exists:preventive_maintenances,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Set creator to current user
        $requestData['created_by'] = Auth::id();
        $requestData['status'] = $requestData['status'] ?? 'active';

        // Initialize statistics fields
        $requestData['total_occurrences'] = $requestData['total_occurrences'] ?? 0;
        $requestData['completed_occurrences'] = $requestData['completed_occurrences'] ?? 0;
        $requestData['missed_occurrences'] = $requestData['missed_occurrences'] ?? 0;
        $requestData['compliance_rate'] = $requestData['compliance_rate'] ?? 0;

        $preventiveMaintenance = PreventiveMaintenance::create($requestData);

        // Create initial execution if active
        if ($preventiveMaintenance->status === 'active') {
            $preventiveMaintenance->createNextExecution();
        }

        // Load relationships for response
        $preventiveMaintenance->load(['asset', 'location', 'creator', 'assignedUser']);

        return response()->json([
            'success' => true,
            'message' => 'Preventive maintenance created successfully',
            'data' => $this->formatPreventiveMaintenanceResponse($preventiveMaintenance),
        ], 201);
    }

    /**
     * Display the specified preventive maintenance.
     */
    public function show(PreventiveMaintenance $preventiveMaintenance): JsonResponse
    {
        $preventiveMaintenance->load(['asset', 'location', 'creator', 'assignedUser', 'executions.assignedUser']);

        return response()->json([
            'success' => true,
            'data' => $this->formatPreventiveMaintenanceResponse($preventiveMaintenance),
        ]);
    }

    /**
     * Update the specified preventive maintenance.
     */
    public function update(Request $request, PreventiveMaintenance $preventiveMaintenance): JsonResponse
    {
        // Prepare JSON data for update
        $requestData = $request->all();
        $jsonFields = ['custom_frequency_settings', 'skip_dates', 'required_materials', 'required_tools', 'safety_requirements', 'checklist_items', 'attachments', 'notification_settings'];

        foreach ($jsonFields as $field) {
            if (isset($requestData[$field])) {
                if (is_string($requestData[$field])) {
                    $decoded = json_decode($requestData[$field], true);
                    $requestData[$field] = is_array($decoded) ? $decoded : [];
                } elseif (!is_array($requestData[$field])) {
                    $requestData[$field] = [];
                }
            }
        }

        $validator = Validator::make($requestData, [
            'title' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:preventive_maintenances,code,' . $preventiveMaintenance->id,
            'description' => 'nullable|string|max:2000',
            'maintenance_type' => 'sometimes|required|in:inspection,cleaning,lubrication,calibration,replacement,testing,adjustment,other',
            'priority' => 'sometimes|required|in:low,medium,high,critical',
            'status' => 'sometimes|required|in:active,inactive,suspended,completed',
            'asset_id' => 'sometimes|required|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_to' => 'nullable|exists:users,id',
            'frequency_type' => 'sometimes|required|in:daily,weekly,monthly,quarterly,semi_annual,annual,custom',
            'frequency_value' => 'nullable|integer|min:1',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after:start_date',
            'estimated_duration_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'work_instructions' => 'nullable|string|max:5000',
            'safety_notes' => 'nullable|string|max:2000',
            'compliance_standard' => 'nullable|string|max:100',
            'requires_certification' => 'nullable|boolean',
            'certification_required' => 'nullable|string|max:100',
            'auto_create_work_orders' => 'nullable|boolean',
            'advance_notice_days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string|max:2000',
            'is_template' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [];
        $allowedFields = [
            'title', 'code', 'description', 'maintenance_type', 'priority', 'status',
            'asset_id', 'location_id', 'assigned_to', 'frequency_type', 'frequency_value',
            'start_date', 'end_date', 'estimated_duration_hours', 'estimated_cost',
            'custom_frequency_settings', 'skip_dates', 'required_materials', 'required_tools',
            'safety_requirements', 'work_instructions', 'checklist_items', 'safety_notes',
            'attachments', 'compliance_standard', 'requires_certification', 'certification_required',
            'notification_settings', 'auto_create_work_orders', 'advance_notice_days',
            'notes', 'is_template'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $updateData[$field] = $requestData[$field];
            }
        }

        if (!empty($updateData)) {
            $preventiveMaintenance->update($updateData);
        }

        // Reload relationships
        $preventiveMaintenance->load(['asset', 'location', 'creator', 'assignedUser']);

        return response()->json([
            'success' => true,
            'message' => 'Preventive maintenance updated successfully',
            'data' => $this->formatPreventiveMaintenanceResponse($preventiveMaintenance),
        ]);
    }

    /**
     * Remove the specified preventive maintenance.
     */
    public function destroy(PreventiveMaintenance $preventiveMaintenance): JsonResponse
    {
        $preventiveMaintenance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Preventive maintenance deleted successfully',
        ]);
    }

    /**
     * Get statistics.
     */
    public function statistics(): JsonResponse
    {
        $totalPM = PreventiveMaintenance::nonTemplates()->count();
        $activePM = PreventiveMaintenance::nonTemplates()->active()->count();
        $inactivePM = PreventiveMaintenance::nonTemplates()->inactive()->count();
        $overduePM = PreventiveMaintenance::nonTemplates()->overdue()->count();
        $dueSoonPM = PreventiveMaintenance::nonTemplates()->dueSoon()->count();

        // Execution statistics
        $totalExecutions = PreventiveMaintenanceExecution::count();
        $scheduledExecutions = PreventiveMaintenanceExecution::scheduled()->count();
        $completedExecutions = PreventiveMaintenanceExecution::completed()->count();
        $overdueExecutions = PreventiveMaintenanceExecution::overdue()->count();

        $pmByStatus = PreventiveMaintenance::nonTemplates()
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $pmByPriority = PreventiveMaintenance::nonTemplates()
            ->select('priority')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        $avgComplianceRate = PreventiveMaintenance::nonTemplates()
            ->where('total_occurrences', '>', 0)
            ->avg('compliance_rate') ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_preventive_maintenances' => $totalPM,
                'active_preventive_maintenances' => $activePM,
                'inactive_preventive_maintenances' => $inactivePM,
                'overdue_preventive_maintenances' => $overduePM,
                'due_soon_preventive_maintenances' => $dueSoonPM,
                'total_executions' => $totalExecutions,
                'scheduled_executions' => $scheduledExecutions,
                'completed_executions' => $completedExecutions,
                'overdue_executions' => $overdueExecutions,
                'average_compliance_rate' => round($avgComplianceRate, 2),
                'pm_by_status' => $pmByStatus,
                'pm_by_priority' => $pmByPriority,
            ],
        ]);
    }

    /**
     * Get options for dropdowns.
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
     * Format response data.
     */
    private function formatPreventiveMaintenanceResponse(PreventiveMaintenance $pm): array
    {
        return [
            'id' => $pm->id,
            'title' => $pm->title,
            'code' => $pm->code,
            'description' => $pm->description,
            'maintenance_type' => $pm->maintenance_type,
            'priority' => $pm->priority,
            'status' => $pm->status,
            'asset_id' => $pm->asset_id,
            'asset' => $pm->asset ? [
                'id' => $pm->asset->id,
                'name' => $pm->asset->name,
                'code' => $pm->asset->code,
            ] : null,
            'location_id' => $pm->location_id,
            'location' => $pm->location ? [
                'id' => $pm->location->id,
                'name' => $pm->location->name,
                'code' => $pm->location->code,
            ] : null,
            'assigned_to' => $pm->assigned_to,
            'assigned_user' => $pm->assignedUser ? [
                'id' => $pm->assignedUser->id,
                'name' => $pm->assignedUser->name,
                'email' => $pm->assignedUser->email,
            ] : null,
            'frequency_type' => $pm->frequency_type,
            'frequency_value' => $pm->frequency_value,
            'start_date' => $pm->start_date,
            'next_due_date' => $pm->next_due_date,
            'last_completed_date' => $pm->last_completed_date,
            'estimated_duration_hours' => $pm->estimated_duration_hours,
            'estimated_cost' => $pm->estimated_cost,
            'work_instructions' => $pm->work_instructions,
            'compliance_rate' => $pm->compliance_rate,
            'completion_rate' => $pm->completion_rate,
            'total_occurrences' => $pm->total_occurrences,
            'completed_occurrences' => $pm->completed_occurrences,
            'notes' => $pm->notes,
            'days_until_due' => $pm->days_until_due,
            'is_due' => $pm->isDue(),
            'is_overdue' => $pm->isOverdue(),
            'is_due_soon' => $pm->isDueSoon(),
            'created_at' => $pm->created_at,
            'updated_at' => $pm->updated_at,
        ];
    }
}
