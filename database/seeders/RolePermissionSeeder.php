<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('role_permissions')->delete();
        DB::table('user_permissions')->delete();
        DB::table('user_roles')->delete();
        Permission::query()->delete();
        Role::query()->delete();

        // Create Permissions
        $permissions = [
            // Dashboard
            ['name' => 'view-dashboard', 'description' => 'View Dashboard'],

            // Chart of Accounts
            ['name' => 'view-coa', 'description' => 'View Chart of Accounts'],
            ['name' => 'create-coa', 'description' => 'Create Chart of Accounts'],
            ['name' => 'edit-coa', 'description' => 'Edit Chart of Accounts'],
            ['name' => 'delete-coa', 'description' => 'Delete Chart of Accounts'],

            // Journal Entry
            ['name' => 'view-journal', 'description' => 'View Journal Entries'],
            ['name' => 'create-journal', 'description' => 'Create Journal Entries'],
            ['name' => 'edit-journal', 'description' => 'Edit Journal Entries'],
            ['name' => 'delete-journal', 'description' => 'Delete Journal Entries'],
            ['name' => 'post-journal', 'description' => 'Post Journal Entries'],

            // Reports
            ['name' => 'view-neraca-lajur', 'description' => 'View Neraca Lajur'],
            ['name' => 'view-neraca', 'description' => 'View Neraca'],
            ['name' => 'view-laba-rugi', 'description' => 'View Laba Rugi'],
            ['name' => 'view-perubahan-modal', 'description' => 'View Perubahan Modal'],
            ['name' => 'view-arus-kas', 'description' => 'View Arus Kas'],

            // Products
            ['name' => 'view-products', 'description' => 'View Products'],
            ['name' => 'create-products', 'description' => 'Create Products'],
            ['name' => 'edit-products', 'description' => 'Edit Products'],
            ['name' => 'delete-products', 'description' => 'Delete Products'],

            // Stock In
            ['name' => 'view-stock-in', 'description' => 'View Stock In'],
            ['name' => 'create-stock-in', 'description' => 'Create Stock In'],
            ['name' => 'edit-stock-in', 'description' => 'Edit Stock In'],
            ['name' => 'delete-stock-in', 'description' => 'Delete Stock In'],
            ['name' => 'post-stock-in', 'description' => 'Post Stock In'],

            // Stock Mutation
            ['name' => 'view-mutation', 'description' => 'View Stock Mutation'],
            ['name' => 'create-mutation', 'description' => 'Create Stock Mutation'],
            ['name' => 'edit-mutation', 'description' => 'Edit Stock Mutation'],
            ['name' => 'delete-mutation', 'description' => 'Delete Stock Mutation'],
            ['name' => 'approve-mutation', 'description' => 'Approve Stock Mutation'],
            ['name' => 'receive-mutation', 'description' => 'Receive Stock Mutation'],

            // Stock Adjustment
            ['name' => 'view-adjustment', 'description' => 'View Stock Adjustment'],
            ['name' => 'create-adjustment', 'description' => 'Create Stock Adjustment'],
            ['name' => 'edit-adjustment', 'description' => 'Edit Stock Adjustment'],
            ['name' => 'delete-adjustment', 'description' => 'Delete Stock Adjustment'],
            ['name' => 'approve-adjustment', 'description' => 'Approve Stock Adjustment'],

            // Stock Opname
            ['name' => 'view-opname', 'description' => 'View Stock Opname'],
            ['name' => 'create-opname', 'description' => 'Create Stock Opname'],
            ['name' => 'edit-opname', 'description' => 'Edit Stock Opname'],
            ['name' => 'delete-opname', 'description' => 'Delete Stock Opname'],
            ['name' => 'complete-opname', 'description' => 'Complete Stock Opname'],

            // Stock Card
            ['name' => 'view-stock-card', 'description' => 'View Stock Card'],

            // Locations
            ['name' => 'view-locations', 'description' => 'View Locations'],
            ['name' => 'create-locations', 'description' => 'Create Locations'],
            ['name' => 'edit-locations', 'description' => 'Edit Locations'],
            ['name' => 'delete-locations', 'description' => 'Delete Locations'],

            // Users
            ['name' => 'view-users', 'description' => 'View Users'],
            ['name' => 'create-users', 'description' => 'Create Users'],
            ['name' => 'edit-users', 'description' => 'Edit Users'],
            ['name' => 'delete-users', 'description' => 'Delete Users'],

            // Roles
            ['name' => 'view-roles', 'description' => 'View Roles'],
            ['name' => 'create-roles', 'description' => 'Create Roles'],
            ['name' => 'edit-roles', 'description' => 'Edit Roles'],
            ['name' => 'delete-roles', 'description' => 'Delete Roles'],

            // Permissions
            ['name' => 'view-permissions', 'description' => 'View Permissions'],
            ['name' => 'assign-permissions', 'description' => 'Assign Permissions'],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            $created = Permission::create([
                'name' => $permission['name'],
                'display_name' => $permission['description'], // Use description as display_name
                'description' => $permission['description'],
                'group' => 'General', // Default group
                'is_active' => true,
            ]);
            $permissionIds[$permission['name']] = $created->id;
        }

        // Create Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access',
                'permissions' => array_values($permissionIds), // All permissions
            ],
            [
                'name' => 'Accounting Manager',
                'description' => 'Manage accounting module',
                'permissions' => [
                    $permissionIds['view-dashboard'],
                    $permissionIds['view-coa'], $permissionIds['create-coa'], $permissionIds['edit-coa'],
                    $permissionIds['view-journal'], $permissionIds['create-journal'], $permissionIds['edit-journal'], $permissionIds['post-journal'],
                    $permissionIds['view-neraca-lajur'], $permissionIds['view-neraca'], $permissionIds['view-laba-rugi'],
                    $permissionIds['view-perubahan-modal'], $permissionIds['view-arus-kas'],
                ],
            ],
            [
                'name' => 'Warehouse Manager',
                'description' => 'Manage warehouse operations',
                'permissions' => [
                    $permissionIds['view-dashboard'],
                    $permissionIds['view-products'], $permissionIds['create-products'], $permissionIds['edit-products'],
                    $permissionIds['view-stock-in'], $permissionIds['create-stock-in'], $permissionIds['post-stock-in'],
                    $permissionIds['view-mutation'], $permissionIds['create-mutation'], $permissionIds['approve-mutation'], $permissionIds['receive-mutation'],
                    $permissionIds['view-adjustment'], $permissionIds['create-adjustment'], $permissionIds['approve-adjustment'],
                    $permissionIds['view-opname'], $permissionIds['create-opname'], $permissionIds['complete-opname'],
                    $permissionIds['view-stock-card'],
                    $permissionIds['view-locations'],
                ],
            ],
            [
                'name' => 'Staff',
                'description' => 'Basic operations',
                'permissions' => [
                    $permissionIds['view-dashboard'],
                    $permissionIds['view-products'],
                    $permissionIds['view-stock-in'], $permissionIds['create-stock-in'],
                    $permissionIds['view-mutation'],
                    $permissionIds['view-stock-card'],
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'display_name' => $roleData['name'], // Use name as display_name
                'description' => $roleData['description'],
                'is_active' => true,
            ]);

            // Attach permissions to role
            foreach ($roleData['permissions'] as $permissionId) {
                DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Roles and Permissions seeded successfully!');
    }
}
