<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RepairRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RepairRequestController extends Controller
{
    /**
     * Display a listing of repair requests.
     */
    public function index(Request $request): JsonResponse
    {
        $query = RepairRequest::with(['asset', 'location', 'requester']);

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

        if ($request->has('asset_id') && !empty($request->asset_id)) {
            $query->byAsset($request->asset_id);
        }

        if ($request->has('location_id') && !empty($request->location_id)) {
            $query->byLocation($request->location_id);
        }

        if ($request->has('requester_id') && !empty($request->requester_id)) {
            $query->byRequester($request->requester_id);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $repairRequests = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $repairRequests,
        ]);
    }

    /**
     * Store a newly created repair request.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'issue' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:repair_requests,code',
            'description' => 'nullable|string|max:2000',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'nullable|in:pending,approved,in_progress,completed,rejected',
            'asset_id' => 'required|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Set requester to current user if not provided
        $requestData = $request->all();
        $requestData['requester_id'] = $requestData['requester_id'] ?? Auth::id();
        $requestData['status'] = $requestData['status'] ?? 'pending';

        $repairRequest = RepairRequest::create($requestData);

        // Load relationships for response
        $repairRequest->load(['asset', 'location', 'requester']);

        return response()->json([
            'success' => true,
            'message' => 'Repair request created successfully',
            'data' => $repairRequest,
        ], 201);
    }

    /**
     * Display the specified repair request.
     */
    public function show(RepairRequest $repairRequest): JsonResponse
    {
        $repairRequest->load(['asset', 'location', 'requester']);

        return response()->json([
            'success' => true,
            'data' => $repairRequest,
        ]);
    }

    /**
     * Update the specified repair request.
     */
    public function update(Request $request, RepairRequest $repairRequest): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'issue' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:repair_requests,code,' . $repairRequest->id,
            'description' => 'nullable|string|max:2000',
            'priority' => 'sometimes|required|in:low,medium,high,critical',
            'status' => 'sometimes|required|in:pending,approved,in_progress,completed,rejected',
            'asset_id' => 'sometimes|required|exists:assets,id',
            'location_id' => 'nullable|exists:locations,id',
            'notes' => 'nullable|string|max:2000',
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
            'issue', 'code', 'description', 'priority', 'status',
            'asset_id', 'location_id', 'notes'
        ];

        foreach ($allowedFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        if (!empty($updateData)) {
            $repairRequest->update($updateData);
        }

        // Reload relationships
        $repairRequest->load(['asset', 'location', 'requester']);

        return response()->json([
            'success' => true,
            'message' => 'Repair request updated successfully',
            'data' => $repairRequest,
        ]);
    }

    /**
     * Remove the specified repair request.
     */
    public function destroy(RepairRequest $repairRequest): JsonResponse
    {
        $repairRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Repair request deleted successfully',
        ]);
    }

    /**
     * Get repair request statistics.
     */
    public function statistics(): JsonResponse
    {
        $totalRequests = RepairRequest::count();
        $pendingRequests = RepairRequest::pending()->count();
        $completedRequests = RepairRequest::completed()->count();
        $urgentRequests = RepairRequest::where('priority', 'critical')
            ->orWhere('priority', 'high')
            ->count();

        $requestsByStatus = RepairRequest::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $requestsByPriority = RepairRequest::select('priority')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        return response()->json([
            'success' => true,
            'data' => [
                'total_repair_requests' => $totalRequests,
                'pending_repair_requests' => $pendingRequests,
                'completed_repair_requests' => $completedRequests,
                'urgent_repair_requests' => $urgentRequests,
                'repair_requests_by_status' => $requestsByStatus,
                'repair_requests_by_priority' => $requestsByPriority,
            ],
        ]);
    }

    /**
     * Export repair requests to CSV.
     */
    public function export(Request $request)
    {
        $query = RepairRequest::with(['asset', 'location', 'requester']);

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

        if ($request->has('asset_id') && !empty($request->asset_id)) {
            $query->byAsset($request->asset_id);
        }

        $repairRequests = $query->orderBy('created_at', 'desc')->get();

        $filename = 'repair_requests_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($repairRequests) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Code',
                'Issue',
                'Priority',
                'Status',
                'Asset',
                'Location',
                'Requester',
                'Description',
                'Notes',
                'Created At',
            ]);

            // CSV data
            foreach ($repairRequests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->code,
                    $request->issue,
                    $request->priority,
                    $request->status,
                    $request->asset ? $request->asset->name : '',
                    $request->location ? $request->location->name : '',
                    $request->requester ? $request->requester->name : '',
                    $request->description,
                    $request->notes,
                    $request->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
