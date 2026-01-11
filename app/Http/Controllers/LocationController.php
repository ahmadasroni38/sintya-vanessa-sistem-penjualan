<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    /**
     * Display a listing of locations.
     */
    public function index(Request $request)
    {
        $query = Location::query()->with('parent');

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by code or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Order by code
        $query->orderBy('code');

        $locations = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => true,
            'message' => 'Data lokasi berhasil diambil',
            'data' => $locations,
        ]);
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:locations,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:locations,id',
            'metadata' => 'nullable|array',
        ]);

        // Encode metadata as JSON if provided
        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_encode($validated['metadata']);
        }

        $location = Location::create($validated);

        return redirect()->back()->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location)
    {
        $location->load(['parent', 'children', 'stockCards.product']);

        // Get stock summary for this location
        $stockSummary = $location->stockCards()
            ->selectRaw('product_id, MAX(balance) as current_balance')
            ->groupBy('product_id')
            ->with('product')
            ->get();

        return Inertia::render('Dashboard/LocationDetail', [
            'location' => $location,
            'stock_summary' => $stockSummary,
        ]);
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:locations,code,' . $location->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:locations,id',
            'metadata' => 'nullable|array',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $location->id) {
            return redirect()->back()->withErrors(['parent_id' => 'Location cannot be its own parent.']);
        }

        // Encode metadata as JSON if provided
        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_encode($validated['metadata']);
        }

        $location->update($validated);

        return redirect()->back()->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Location $location)
    {
        // Check if location has children
        if ($location->children()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete location with child locations.']);
        }

        // Check if location has stock movements
        if ($location->stockCards()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete location with stock movements.']);
        }

        $location->delete();

        return redirect()->back()->with('success', 'Location deleted successfully.');
    }

    /**
     * Get all active locations for dropdown/select.
     */
    public function active()
    {
        $locations = Location::active()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'city', 'color']);

        return response()->json($locations);
    }

    /**
     * Get hierarchical tree of locations.
     */
    public function tree()
    {
        $locations = Location::with('children')
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        return response()->json($locations);
    }

    public function statistics(Request $request)
    {
        // total locations, location active, primary locations, location new in this month
        $totalLocations = Location::count();
        $activeLocations = Location::where('is_active', true)->count();
        $primaryLocations = Location::whereNull('parent_id')->count();
        $newThisMonth = Location::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        return response()->json([
            'total_locations' => $totalLocations,
            'active_locations' => $activeLocations,
            'primary_locations' => $primaryLocations,
            'new_this_month' => $newThisMonth,
        ]);
    }
}
