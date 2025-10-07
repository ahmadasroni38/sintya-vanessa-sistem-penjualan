<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::with('roles')->get();

        return response()->json([
            'success' => true,
            'data' => $permissions,
        ]);
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'group' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission = Permission::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully',
            'data' => $permission,
        ], 201);
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $permission,
        ]);
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'group' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully',
            'data' => $permission,
        ]);
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        // Check if permission is assigned to any roles or users
        if ($permission->roles()->exists() || $permission->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete permission that is assigned to roles or users',
            ], 422);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully',
        ]);
    }

    /**
     * Assign roles to a permission.
     */
    public function assignRoles(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission->roles()->sync($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'Roles assigned successfully',
            'data' => $permission->load('roles'),
        ]);
    }

    /**
     * Remove roles from a permission.
     */
    public function removeRoles(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission->roles()->detach($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'Roles removed successfully',
            'data' => $permission->load('roles'),
        ]);
    }
}
