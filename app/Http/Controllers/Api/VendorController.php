<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(): JsonResponse
    {
        $vendors = Vendor::orderBy('name')
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'code' => $vendor->code,
                    'company_name' => $vendor->company_name,
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                    'website' => $vendor->website,
                    'address' => $vendor->address,
                    'city' => $vendor->city,
                    'state' => $vendor->state,
                    'postal_code' => $vendor->postal_code,
                    'country' => $vendor->country,
                    'contact_person' => $vendor->contact_person,
                    'contact_phone' => $vendor->contact_phone,
                    'contact_email' => $vendor->contact_email,
                    'vendor_type' => $vendor->vendor_type,
                    'vendor_type_label' => $vendor->vendor_type_label,
                    'description' => $vendor->description,
                    'is_active' => $vendor->is_active,
                    'credit_limit' => $vendor->credit_limit,
                    'payment_terms' => $vendor->payment_terms,
                    'payment_terms_label' => $vendor->payment_terms_label,
                    'tax_id' => $vendor->tax_id,
                    'display_name' => $vendor->display_name,
                    'full_address' => $vendor->full_address,
                    'metadata' => $vendor->metadata,
                    'created_at' => $vendor->created_at,
                    'updated_at' => $vendor->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $vendors,
        ]);
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:vendors,code',
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'vendor_type' => 'required|in:supplier,service_provider,contractor,other',
            'description' => 'nullable|string|max:1000',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'required|in:cash,net_15,net_30,net_45,net_60,custom',
            'tax_id' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $vendor = Vendor::create([
            'name' => $request->name,
            'code' => $request->code,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'website' => $request->website,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?: 'Indonesia',
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'vendor_type' => $request->vendor_type,
            'description' => $request->description,
            'is_active' => true,
            'credit_limit' => $request->credit_limit,
            'payment_terms' => $request->payment_terms,
            'tax_id' => $request->tax_id,
            'metadata' => $request->metadata ?: [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor created successfully',
            'data' => $this->formatVendorResponse($vendor),
        ], 201);
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatVendorResponse($vendor),
        ]);
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, Vendor $vendor): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:vendors,code,' . $vendor->id,
            'company_name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'vendor_type' => 'sometimes|required|in:supplier,service_provider,contractor,other',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'sometimes|required|in:cash,net_15,net_30,net_45,net_60,custom',
            'tax_id' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
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
        $fillableFields = [
            'name', 'code', 'company_name', 'email', 'phone', 'website',
            'address', 'city', 'state', 'postal_code', 'country',
            'contact_person', 'contact_phone', 'contact_email', 'vendor_type',
            'description', 'is_active', 'credit_limit', 'payment_terms',
            'tax_id', 'metadata'
        ];

        foreach ($fillableFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        if (!empty($updateData)) {
            $vendor->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vendor updated successfully',
            'data' => $this->formatVendorResponse($vendor->fresh()),
        ]);
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(Vendor $vendor): JsonResponse
    {
        // Check if vendor can be deleted
        if (!$vendor->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete vendor: it has assets or purchase orders assigned to it',
            ], 422);
        }

        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully',
        ]);
    }

    /**
     * Toggle vendor status (active/inactive).
     */
    public function toggleStatus(Vendor $vendor): JsonResponse
    {
        $oldStatus = $vendor->is_active;
        $vendor->toggleStatus();
        $newStatus = $vendor->is_active;

        return response()->json([
            'success' => true,
            'message' => "Vendor " . ($newStatus ? 'activated' : 'deactivated') . " successfully",
            'data' => [
                'vendor' => $this->formatVendorResponse($vendor),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
        ]);
    }

    /**
     * Get vendors filtered by type.
     */
    public function getByType(Request $request): JsonResponse
    {
        $type = $request->get('type');

        $vendors = Vendor::when($type, function ($query, $type) {
                return $query->ofType($type);
            })
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'code' => $vendor->code,
                    'company_name' => $vendor->company_name,
                    'display_name' => $vendor->display_name,
                    'vendor_type' => $vendor->vendor_type,
                    'vendor_type_label' => $vendor->vendor_type_label,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $vendors,
        ]);
    }

    /**
     * Format vendor response data.
     */
    private function formatVendorResponse(Vendor $vendor): array
    {
        return [
            'id' => $vendor->id,
            'name' => $vendor->name,
            'code' => $vendor->code,
            'company_name' => $vendor->company_name,
            'email' => $vendor->email,
            'phone' => $vendor->phone,
            'website' => $vendor->website,
            'address' => $vendor->address,
            'city' => $vendor->city,
            'state' => $vendor->state,
            'postal_code' => $vendor->postal_code,
            'country' => $vendor->country,
            'contact_person' => $vendor->contact_person,
            'contact_phone' => $vendor->contact_phone,
            'contact_email' => $vendor->contact_email,
            'vendor_type' => $vendor->vendor_type,
            'vendor_type_label' => $vendor->vendor_type_label,
            'description' => $vendor->description,
            'is_active' => $vendor->is_active,
            'credit_limit' => $vendor->credit_limit,
            'payment_terms' => $vendor->payment_terms,
            'payment_terms_label' => $vendor->payment_terms_label,
            'tax_id' => $vendor->tax_id,
            'display_name' => $vendor->display_name,
            'full_address' => $vendor->full_address,
            'metadata' => $vendor->metadata,
            'created_at' => $vendor->created_at,
            'updated_at' => $vendor->updated_at,
        ];
    }
}