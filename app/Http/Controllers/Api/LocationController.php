<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationController extends Controller
{
    /**
     * Display a listing of the locations.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Location::with(['parent', 'children']);

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by parent
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Search by code or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'code', 'city', 'country', 'created_at', 'is_active'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $locations = $query->paginate($perPage, ['*'], 'page', $page);

        // Format the response data
        $formattedLocations = $locations->getCollection()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'code' => $location->code,
                'description' => $location->description,
                'address' => $location->address,
                'city' => $location->city,
                'state' => $location->state,
                'country' => $location->country,
                'postal_code' => $location->postal_code,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'full_address' => $location->full_address,
                'color' => $location->color,
                'is_active' => $location->is_active,
                'parent_id' => $location->parent_id,
                'parent' => $location->parent ? [
                    'id' => $location->parent->id,
                    'name' => $location->parent->name,
                    'code' => $location->parent->code,
                    'color' => $location->parent->color,
                ] : null,
                'children_count' => $location->children->count(),
                'full_path' => $location->full_path,
                'hierarchy_level' => $location->hierarchy_level,
                'metadata' => $location->metadata,
                'created_at' => $location->created_at,
                'updated_at' => $location->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedLocations,
            'current_page' => $locations->currentPage(),
            'per_page' => $locations->perPage(),
            'total' => $locations->total(),
            'last_page' => $locations->lastPage(),
        ]);
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request): JsonResponse
    {
        // Convert string "null" to actual null
        $data = $request->all();
        if (isset($data['parent_id']) && ($data['parent_id'] === 'null' || $data['parent_id'] === '')) {
            $data['parent_id'] = null;
        }

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:locations,code',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'parent_id' => 'nullable|exists:locations,id',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for circular reference if parent_id is provided
        if ($data['parent_id']) {
            $parent = Location::find($data['parent_id']);
            if ($parent && $this->wouldCreateCircularReference($parent, null)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot set parent: this would create a circular reference',
                ], 422);
            }
        }

        $location = Location::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'postal_code' => $data['postal_code'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'color' => $data['color'] ?: '#10B981',
            'parent_id' => $data['parent_id'],
            'metadata' => $data['metadata'] ?? [],
            'is_active' => true,
        ]);

        // Load relationships for response
        $location->load(['parent', 'children']);

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'data' => $this->formatLocationResponse($location),
        ], 201);
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location): JsonResponse
    {
        $location->load(['parent', 'children']);

        return response()->json([
            'success' => true,
            'data' => $this->formatLocationResponse($location),
        ]);
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, Location $location): JsonResponse
    {
        // Convert string "null" to actual null
        $data = $request->all();
        if (isset($data['parent_id']) && ($data['parent_id'] === 'null' || $data['parent_id'] === '')) {
            $data['parent_id'] = null;
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:locations,code,' . $location->id,
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'parent_id' => 'nullable|exists:locations,id',
            'is_active' => 'sometimes|boolean',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for circular reference if parent_id is being changed
        if (isset($data['parent_id']) && $data['parent_id'] != $location->parent_id) {
            if ($data['parent_id']) {
                $parent = Location::find($data['parent_id']);
                if ($parent && $this->wouldCreateCircularReference($parent, $location->id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot set parent: this would create a circular reference',
                    ], 422);
                }
            }
        }

        $updateData = [];

        // Only update provided fields
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        if (isset($data['code'])) {
            $updateData['code'] = $data['code'];
        }
        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }
        if (isset($data['address'])) {
            $updateData['address'] = $data['address'];
        }
        if (isset($data['city'])) {
            $updateData['city'] = $data['city'];
        }
        if (isset($data['state'])) {
            $updateData['state'] = $data['state'];
        }
        if (isset($data['country'])) {
            $updateData['country'] = $data['country'];
        }
        if (isset($data['postal_code'])) {
            $updateData['postal_code'] = $data['postal_code'];
        }
        if (isset($data['latitude'])) {
            $updateData['latitude'] = $data['latitude'];
        }
        if (isset($data['longitude'])) {
            $updateData['longitude'] = $data['longitude'];
        }
        if (isset($data['color'])) {
            $updateData['color'] = $data['color'];
        }
        if (isset($data['parent_id'])) {
            $updateData['parent_id'] = $data['parent_id'];
        }
        if (isset($data['is_active'])) {
            $updateData['is_active'] = $data['is_active'];
        }
        if (isset($data['metadata'])) {
            $updateData['metadata'] = $data['metadata'];
        }

        if (!empty($updateData)) {
            $location->update($updateData);
        }

        // Reload relationships
        $location->load(['parent', 'children']);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => $this->formatLocationResponse($location),
        ]);
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Location $location): JsonResponse
    {
        // Check if location can be deleted
        if (!$location->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete location: it has child locations, stock cards, or stock balances assigned to it',
            ], 422);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully',
        ]);
    }

    /**
     * Toggle location status (active/inactive).
     */
    public function toggleStatus(Location $location): JsonResponse
    {
        $oldStatus = $location->is_active;
        $location->toggleStatus();
        $newStatus = $location->is_active;

        return response()->json([
            'success' => true,
            'message' => "Location " . ($newStatus ? 'activated' : 'deactivated') . " successfully",
            'data' => [
                'location' => $this->formatLocationResponse($location->load(['parent', 'children'])),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
        ]);
    }

    /**
     * Get hierarchical tree structure of locations.
     */
    public function tree(): JsonResponse
    {
        $locations = Location::with(['children' => function ($query) {
            $query->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->orderBy('name')
        ->get()
        ->map(function ($location) {
            return $this->buildLocationTree($location);
        });

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    /**
     * Get locations suitable for parent selection (excluding descendants).
     */
    public function parentOptions(Location $location = null): JsonResponse
    {
        $query = Location::active()->orderBy('name');

        // If updating existing location, exclude itself and its descendants
        if ($location) {
            $excludeIds = [$location->id];
            $this->collectDescendantIds($location, $excludeIds);
            $query->whereNotIn('id', $excludeIds);
        }

        $locations = $query->get();

        // If editing an existing location, ensure its current parent is included
        // even if it would normally be excluded (e.g., if it's a descendant)
        if ($location && $location->parent_id) {
            $currentParent = Location::active()->find($location->parent_id);
            if ($currentParent && !in_array($currentParent->id, $locations->pluck('id')->toArray())) {
                $locations->prepend($currentParent);
            }
        }

        $formattedLocations = $locations->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'code' => $location->code,
                'full_path' => $location->full_path,
                'hierarchy_level' => $location->hierarchy_level,
                'city' => $location->city,
                'country' => $location->country,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedLocations,
        ]);
    }

    /**
     * Get location statistics.
     */
    public function statistics(): JsonResponse
    {
        $total = Location::count();
        $active = Location::where('is_active', true)->count();
        $root = Location::whereNull('parent_id')->count();

        // Count locations created this month
        $newThisMonth = Location::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'root' => $root,
                'new_this_month' => $newThisMonth,
            ],
        ]);
    }

    /**
     * Format location response data.
     */
    private function formatLocationResponse(Location $location): array
    {
        return [
            'id' => $location->id,
            'name' => $location->name,
            'code' => $location->code,
            'description' => $location->description,
            'address' => $location->address,
            'city' => $location->city,
            'state' => $location->state,
            'country' => $location->country,
            'postal_code' => $location->postal_code,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'full_address' => $location->full_address,
            'color' => $location->color,
            'is_active' => $location->is_active,
            'parent_id' => $location->parent_id,
            'parent' => $location->parent ? [
                'id' => $location->parent->id,
                'name' => $location->parent->name,
                'code' => $location->parent->code,
            ] : null,
            'children_count' => $location->children->count(),
            'full_path' => $location->full_path,
            'hierarchy_level' => $location->hierarchy_level,
            'metadata' => $location->metadata,
            'created_at' => $location->created_at,
            'updated_at' => $location->updated_at,
        ];
    }

    /**
     * Build hierarchical tree structure.
     */
    private function buildLocationTree(Location $location): array
    {
        $data = $this->formatLocationResponse($location);

        if ($location->children->isNotEmpty()) {
            $data['children'] = $location->children->map(function ($child) {
                return $this->buildLocationTree($child);
            });
        }

        return $data;
    }

    /**
     * Check if setting a parent would create a circular reference.
     */
    private function wouldCreateCircularReference(Location $potentialParent, ?int $locationId): bool
    {
        if (!$locationId) {
            return false;
        }

        // Check if the potential parent is the same as the location
        if ($potentialParent->id === $locationId) {
            return true;
        }

        // Check if the potential parent is a descendant of the location
        $parent = $potentialParent->parent;
        while ($parent) {
            if ($parent->id === $locationId) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Collect all descendant IDs recursively.
     */
    private function collectDescendantIds(Location $location, array &$ids): void
    {
        foreach ($location->children as $child) {
            $ids[] = $child->id;
            $this->collectDescendantIds($child, $ids);
        }
    }
}
