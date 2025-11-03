<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Display a listing of the users.
     */
    public function index(): JsonResponse
    {
        $users = User::with(['roles', 'permissions'])->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'email_verified_at' => now(), // Auto-verify new users
            'status' => 'active', // Set default status to active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user->load(['roles', 'permissions']),
        ], 201);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'email_verified_at' => 'nullable|date',
            'status' => 'sometimes|required|in:active,inactive',
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
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }
        if ($request->has('email_verified_at')) {
            $updateData['email_verified_at'] = $request->email_verified_at;
        }
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->load(['roles', 'permissions']),
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $user->load(['roles', 'permissions']),
        ]);
    }

    /**
     * Assign roles to a user.
     */
    public function assignRoles(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*.id' => 'required|exists:roles,id',
            'roles.*.is_active' => 'boolean',
            'roles.*.expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rolesData = [];
        foreach ($request->roles as $roleData) {
            $rolesData[$roleData['id']] = [
                'is_active' => $roleData['is_active'] ?? false,
                'expires_at' => $roleData['expires_at'] ?? null,
                'assigned_at' => now(),
            ];
        }

        $user->roles()->sync($rolesData);

        return response()->json([
            'success' => true,
            'message' => 'Roles assigned successfully',
            'data' => $user->load('roles'),
        ]);
    }

    /**
     * Remove roles from a user.
     */
    public function removeRoles(Request $request, User $user): JsonResponse
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

        $user->roles()->detach($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'Roles removed successfully',
            'data' => $user->load('roles'),
        ]);
    }

    /**
     * Set active role for a user.
     */
    public function setActiveRole(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = Role::find($request->role_id);

        // Check if user has this role
        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have this role assigned',
            ], 422);
        }

        $user->setActiveRole($role);

        return response()->json([
            'success' => true,
            'message' => 'Active role set successfully',
            'data' => [
                'user' => $user->load('roles'),
                'active_role' => $user->activeRole(),
            ],
        ]);
    }

    /**
     * Assign direct permissions to a user.
     */
    public function assignPermissions(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->permissions()->sync($request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned successfully',
            'data' => $user->load('permissions'),
        ]);
    }

    /**
     * Remove direct permissions from a user.
     */
    public function removePermissions(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->permissions()->detach($request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions removed successfully',
            'data' => $user->load('permissions'),
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting the current authenticated user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user): JsonResponse
    {
        // Prevent deactivating the current authenticated user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot deactivate your own account',
            ], 422);
        }

        $oldStatus = $user->status;
        $user->toggleStatus();
        $newStatus = $user->status;

        return response()->json([
            'success' => true,
            'message' => "User {$newStatus} successfully",
            'data' => [
                'user' => $user->load(['roles', 'permissions']),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
        ]);
    }

    /**
     * Get user's permissions.
     */
    public function getPermissions(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'direct_permissions' => $user->permissions,
                'role_permissions' => $user->activeRole() ? $user->activeRole()->permissions : [],
                'all_permissions' => $user->getAllPermissions(),
            ],
        ]);
    }

    /**
     * Get authenticated user's profile.
     */
    public function getProfile(): JsonResponse
    {
        try {
            $user = auth()->user();
            $profileData = $this->profileService->getProfile($user);

            return response()->json([
                'success' => true,
                'data' => $profileData,
            ]);
        } catch (\Exception $e) {
            Log::error('Get profile error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
            ], 500);
        }
    }

    /**
     * Update authenticated user's profile.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $updatedUser = $this->profileService->updateProfile($user, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => new UserResource($updatedUser),
            ]);
        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred',
            ], 500);
        }
    }

    /**
     * Update authenticated user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $this->profileService->updatePassword(
                $user,
                $request->current_password,
                $request->new_password
            );

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Update password error: ' . $e->getMessage());

            // Check if it's a validation error (incorrect current password)
            if (str_contains($e->getMessage(), 'incorrect')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => [
                        'current_password' => [$e->getMessage()]
                    ],
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update password',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred',
            ], 500);
        }
    }
}
