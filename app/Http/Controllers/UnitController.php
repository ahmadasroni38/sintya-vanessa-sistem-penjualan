<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnitController extends Controller
{
    /**
     * Display a listing of active units.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Unit::query();

        // Filter active units by default
        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'code');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        if ($request->has('paginate') && !$request->paginate) {
            $units = $query->get();
            return response()->json([
                'data' => $units,
            ]);
        }

        $units = $query->paginate($request->per_page ?? 15);

        return response()->json($units);
    }

    /**
     * Get all active units for dropdown/select.
     */
    public function active(): JsonResponse
    {
        $units = Unit::active()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'symbol']);

        return response()->json([
            'data' => $units,
        ]);
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:20|unique:units,code',
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateUnitCode($validated['name']);
        }

        // Set is_active to true by default
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $unit = Unit::create($validated);

        return response()->json([
            'message' => 'Unit created successfully.',
            'data' => [
                'id' => $unit->id,
                'value' => $unit->id,
                'label' => $unit->symbol ? "{$unit->name} ({$unit->symbol})" : $unit->name,
                'name' => $unit->name,
                'code' => $unit->code,
                'symbol' => $unit->symbol,
            ],
        ], 201);
    }

    /**
     * Display the specified unit.
     */
    public function show(Unit $unit): JsonResponse
    {
        return response()->json([
            'data' => $unit,
        ]);
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, Unit $unit): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:units,code,' . $unit->id,
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $unit->update($validated);

        return response()->json([
            'message' => 'Unit updated successfully.',
            'data' => $unit,
        ]);
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(Unit $unit): JsonResponse
    {
        // Check if unit is being used by products
        if (!$unit->canBeDeleted()) {
            return response()->json([
                'message' => 'Cannot delete unit that is being used by products.',
            ], 422);
        }

        $unit->delete();

        return response()->json([
            'message' => 'Unit deleted successfully.',
        ]);
    }

    /**
     * Generate a unique unit code from name.
     */
    private function generateUnitCode(string $name): string
    {
        // Create code from first 3 letters of name, uppercase
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));

        if (empty($baseCode)) {
            $baseCode = 'UNT';
        }

        // Check if code exists, if yes add number suffix
        $code = $baseCode;
        $counter = 1;

        while (Unit::where('code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return $code;
    }
}
