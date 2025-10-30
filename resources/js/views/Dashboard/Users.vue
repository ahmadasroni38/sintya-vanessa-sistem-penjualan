<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    User Management
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage your user accounts and permissions
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <UsersIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Users
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalUsers }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg"
                    >
                        <CheckCircleIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Active Users
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activeUsers }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg"
                    >
                        <ClockIcon
                            class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pending
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ pendingUsers }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg"
                    >
                        <UserPlusIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            New This Month
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ newUsers }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Users"
            description="A list of all users in your account including their name, title, email and role."
            :data="users"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="Create User"
            :show-filters="false"
            :show-bulk-actions="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search users..."
            empty-title="No users found"
            empty-description="Get started by adding your first user to the system."
            @add="handleAddUser"
            @edit="handleEditUser"
            @delete="handleDeleteUser"
            @bulk-action="handleBulkAction"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshUsers"
        >
            <!-- Custom Avatar Column -->
            <template #column-avatar="{ item }">
                <div class="flex items-center">
                    <img
                        class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700"
                        :src="
                            item.avatar ||
                            `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                item.name
                            )}&background=6366f1&color=ffffff`
                        "
                        :alt="item.name"
                    />
                    <div class="ml-4">
                        <p
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.name }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ item.email }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Role Column with Badge -->
            <template #column-role="{ item }">
                <div class="space-y-1">
                    <div
                        v-for="role in item.roles"
                        :key="role.id"
                        class="flex items-center gap-1"
                    >
                        <span
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                getRoleBadgeClasses(role.name),
                            ]"
                        >
                            {{ role.display_name }}
                            <span
                                v-if="role.pivot.is_active"
                                class="ml-1 text-xs"
                                >(Active)</span
                            >
                        </span>
                    </div>
                </div>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            item.status === 'active'
                                ? 'bg-red-500'
                                : item.status === 'inactive'
                                ? 'bg-red-500'
                                : 'bg-yellow-500',
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                        >{{ item.status }}</span
                    >
                </div>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleEditUser(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Edit User"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleToggleStatus(item)"
                        :class="[
                            'p-1.5 rounded-lg transition-colors duration-200',
                            item.status === 'active'
                                ? 'text-gray-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:text-orange-400 dark:hover:bg-orange-900/20'
                                : 'text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20',
                        ]"
                        :title="
                            item.status === 'active'
                                ? 'Deactivate User'
                                : 'Activate User'
                        "
                    >
                        <PlayIcon
                            v-if="item.status !== 'active'"
                            class="w-4 h-4"
                        />
                        <PauseIcon v-else class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleManageRoles(item)"
                        class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 dark:hover:text-purple-400 dark:hover:bg-purple-900/20"
                        title="Manage Roles"
                    >
                        <UserPlusIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleManagePermissions(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Manage Permissions"
                    >
                        <CheckCircleIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeleteUser(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete User"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>

            <!-- Custom Filters -->
            <template #filters>
                <div class="p-3 space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Role
                        </label>
                        <select
                            v-model="roleFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">All Roles</option>
                            <option
                                v-for="role in availableRoles"
                                :key="role.value"
                                :value="role.value"
                            >
                                {{ role.label }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Status
                        </label>
                        <select
                            v-model="statusFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="applyFilters"
                            :disabled="applyingFilters"
                            class="flex-1 px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg
                                v-if="applyingFilters"
                                class="animate-spin -ml-1 mr-2 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{
                                applyingFilters
                                    ? "Applying..."
                                    : "Apply Filters"
                            }}
                        </button>
                        <button
                            @click="
                                roleFilter = '';
                                statusFilter = '';
                            "
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </template>
        </DataTable>

        <!-- Manage User Roles Modal -->
        <Modal :is-open="showRolesModal" @close="closeModals" size="lg">
            <template #title>
                Manage Roles for {{ selectedUser?.name }}
            </template>

            <div class="space-y-6">
                <!-- Header with bulk actions -->
                <div class="flex justify-between items-center">
                    <div>
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            Available Roles
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ selectedUserRoles.length }} of
                            {{ allRoles.length }} roles selected
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="selectAllRoles"
                            class="px-3 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded"
                        >
                            Select All
                        </button>
                        <button
                            @click="deselectAllRoles"
                            class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/20 rounded"
                        >
                            Deselect All
                        </button>
                    </div>
                </div>

                <!-- Current active role indicator -->
                <div
                    v-if="activeRoleId"
                    class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-red-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p
                                class="text-sm font-medium text-red-800 dark:text-red-200"
                            >
                                Active Role:
                                {{ getRoleById(activeRoleId)?.display_name }}
                            </p>
                            <p class="text-sm text-red-600 dark:text-red-300">
                                This role will be used for permission checks
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Roles list -->
                <div class="grid grid-cols-1 gap-3 max-h-96 overflow-y-auto">
                    <div
                        v-for="role in allRoles"
                        :key="role.id"
                        :class="[
                            'flex items-center justify-between p-4 border rounded-lg transition-colors',
                            selectedUserRoles.includes(role.id)
                                ? 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600',
                        ]"
                    >
                        <div class="flex items-center flex-1">
                            <input
                                :id="'user-role-' + role.id"
                                v-model="selectedUserRoles"
                                :value="role.id"
                                type="checkbox"
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                @change="validateRoleSelection"
                            />
                            <label
                                :for="'user-role-' + role.id"
                                class="ml-3 block text-sm cursor-pointer flex-1"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ role.display_name }}
                                        </span>
                                        <span
                                            class="text-gray-500 dark:text-gray-400 ml-2"
                                        >
                                            ({{ role.name }})
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <!-- Currently assigned indicator -->
                                        <span
                                            v-if="
                                                isRoleCurrentlyAssigned(role.id)
                                            "
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200"
                                        >
                                            Assigned
                                        </span>
                                        <!-- Active role indicator -->
                                        <span
                                            v-if="isRoleActive(role.id)"
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200"
                                        >
                                            Active
                                        </span>
                                    </div>
                                </div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    {{
                                        role.description ||
                                        "No description available"
                                    }}
                                </p>
                            </label>
                        </div>
                        <div class="flex items-center ml-4">
                            <input
                                :id="'active-role-' + role.id"
                                v-model="activeRoleId"
                                :value="role.id"
                                :disabled="!selectedUserRoles.includes(role.id)"
                                type="radio"
                                name="activeRole"
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 disabled:opacity-50"
                                @change="validateActiveRole"
                            />
                            <label
                                :for="'active-role-' + role.id"
                                class="ml-2 block text-sm text-gray-700 dark:text-gray-300 cursor-pointer"
                                :class="{
                                    'opacity-50 cursor-not-allowed':
                                        !selectedUserRoles.includes(role.id),
                                }"
                            >
                                Set as Active
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Validation messages -->
                <div
                    v-if="roleErrors.length > 0"
                    class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-red-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3
                                class="text-sm font-medium text-red-800 dark:text-red-200"
                            >
                                Please fix the following issues:
                            </h3>
                            <ul
                                class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside"
                            >
                                <li v-for="error in roleErrors" :key="error">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <Button
                    @click="closeModals"
                    variant="secondary"
                    :disabled="savingRoles"
                >
                    Cancel
                </Button>
                <Button
                    @click="saveUserRoles"
                    :loading="savingRoles"
                    :disabled="savingRoles || roleErrors.length > 0"
                    class="bg-red-600 hover:bg-red-700"
                >
                    Save Roles
                </Button>
            </div>
        </Modal>

        <!-- Manage User Permissions Modal -->
        <Modal :is-open="showPermissionsModal" @close="closeModals" size="lg">
            <template #title>
                Manage Direct Permissions for {{ selectedUser?.name }}
            </template>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Available Permissions
                    </h3>
                    <!-- Permission Group Select -->
                    <div class="relative w-48">
                        <select
                            v-model="permissionGroup"
                            data-hs-select='{
                              "hasSearch": true,
                              "searchPlaceholder": "Search groups...",
                              "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                              "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-900",
                              "placeholder": "Filter by group",
                              "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                              "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                              "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700",
                              "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                              "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-gray-800 dark:text-neutral-200 \" data-title></div></div></div>",
                              "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                            }'
                            class="hidden"
                        >
                            <option value="">Filter by group</option>
                            <option
                                v-for="group in permissionGroups"
                                :key="group.value"
                                :value="group.value"
                                :data-hs-select-option="`{&quot;icon&quot;: &quot;<svg class=\&quot;w-4 h-4 text-gray-400\&quot; fill=\&quot;none\&quot; stroke=\&quot;currentColor\&quot; viewBox=\&quot;0 0 24 24\&quot;><path stroke-linecap=\&quot;round\&quot; stroke-linejoin=\&quot;round\&quot; stroke-width=\&quot;2\&quot; d=\&quot;M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\&quot;></path></svg>&quot;}`"
                            >
                                {{ group.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                    <div
                        v-for="permission in filteredPermissions"
                        :key="permission.id"
                        class="flex items-center"
                    >
                        <input
                            :id="'user-perm-' + permission.id"
                            v-model="selectedUserPermissions"
                            :value="permission.id"
                            type="checkbox"
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                        />
                        <label
                            :for="'user-perm-' + permission.id"
                            class="ml-2 block text-sm text-gray-900 dark:text-white"
                        >
                            {{ permission.display_name }}
                            <span class="text-gray-500"
                                >({{ permission.name }})</span
                            >
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <Button @click="closeModals" variant="secondary">
                    Cancel
                </Button>
                <Button
                    @click="saveUserPermissions"
                    :loading="savingPermissions"
                >
                    Save Permissions
                </Button>
            </div>
        </Modal>

        <!-- Create/Edit User Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="lg"
        >
            <template #title>
                {{ showCreateModal ? "Create User" : "Edit User" }}
            </template>

            <form @submit.prevent="saveUser" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Basic Information
                    </h3>
                    <FormInput
                        v-model="userForm.name"
                        label="Name"
                        placeholder="Full Name"
                        :error="getFieldError('name')"
                        required
                        @blur="handleFieldBlur('name')"
                    />
                    <FormInput
                        v-model="userForm.email"
                        label="Email"
                        type="email"
                        placeholder="user@example.com"
                        :error="getFieldError('email')"
                        required
                        @blur="handleFieldBlur('email')"
                    />
                    <FormInput
                        v-if="showCreateModal"
                        v-model="userForm.password"
                        label="Password"
                        type="password"
                        placeholder="Enter password (min 8 chars, 1 uppercase, 1 lowercase, 1 number)"
                        :error="getFieldError('password')"
                        required
                        @blur="handleFieldBlur('password')"
                    />
                    <FormInput
                        v-if="showCreateModal"
                        v-model="userForm.password_confirmation"
                        label="Confirm Password"
                        type="password"
                        placeholder="Confirm password"
                        :error="getFieldError('password_confirmation')"
                        required
                        @blur="handleFieldBlur('password_confirmation')"
                    />
                </div>

                <!-- Roles Assignment (Create Modal Only) -->
                <div v-if="showCreateModal" class="space-y-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Assign Roles
                    </h3>
                    <div
                        class="grid grid-cols-1 gap-4 max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                    >
                        <div
                            v-for="role in allRoles"
                            :key="role.id"
                            class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg"
                        >
                            <div class="flex items-center">
                                <input
                                    :id="'create-user-role-' + role.id"
                                    v-model="selectedUserRoles"
                                    :value="role.id"
                                    type="checkbox"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                />
                                <label
                                    :for="'create-user-role-' + role.id"
                                    class="ml-3 block text-sm"
                                >
                                    <span
                                        class="font-medium text-gray-900 dark:text-white"
                                        >{{ role.display_name }}</span
                                    >
                                    <span class="text-gray-500"
                                        >({{ role.name }})</span
                                    >
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    :id="'create-active-role-' + role.id"
                                    v-model="activeRoleId"
                                    :value="role.id"
                                    type="radio"
                                    name="createActiveRole"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                />
                                <label
                                    :for="'create-active-role-' + role.id"
                                    class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
                                >
                                    Set as Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Assignment (Create Modal Only) -->
                <div v-if="showCreateModal" class="space-y-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Assign Direct Permissions
                    </h3>
                    <div class="flex justify-between items-center mb-3">
                        <!-- Permission Group Select -->
                        <div class="relative w-48">
                            <select
                                v-model="permissionGroup"
                                data-hs-select='{
                                  "hasSearch": true,
                                  "searchPlaceholder": "Search groups...",
                                  "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                                  "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-900",
                                  "placeholder": "Filter by group",
                                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                                  "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                                  "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700",
                                  "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                  "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-gray-800 dark:text-neutral-200 \" data-title></div></div></div>",
                                  "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                }'
                                class="hidden"
                            >
                                <option value="">Filter by group</option>
                                <option
                                    v-for="group in permissionGroups"
                                    :key="group.value"
                                    :value="group.value"
                                    :data-hs-select-option="`{&quot;icon&quot;: &quot;<svg class=\&quot;w-4 h-4 text-gray-400\&quot; fill=\&quot;none\&quot; stroke=\&quot;currentColor\&quot; viewBox=\&quot;0 0 24 24\&quot;><path stroke-linecap=\&quot;round\&quot; stroke-linejoin=\&quot;round\&quot; stroke-width=\&quot;2\&quot; d=\&quot;M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\&quot;></path></svg>&quot;}`"
                                >
                                    {{ group.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div
                        class="grid grid-cols-2 gap-4 max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                    >
                        <div
                            v-for="permission in filteredPermissions"
                            :key="permission.id"
                            class="flex items-center"
                        >
                            <input
                                :id="'create-user-perm-' + permission.id"
                                v-model="selectedUserPermissions"
                                :value="permission.id"
                                type="checkbox"
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                            />
                            <label
                                :for="'create-user-perm-' + permission.id"
                                class="ml-2 block text-sm text-gray-900 dark:text-white"
                            >
                                {{ permission.display_name }}
                                <span class="text-gray-500"
                                    >({{ permission.name }})</span
                                >
                            </label>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0"
                >
                    <Button
                        @click="closeModals"
                        variant="secondary"
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :loading="saving"
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        {{ showCreateModal ? "Create User" : "Update User" }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View User Details Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="lg">
            <template #title> User Details: {{ selectedUser?.name }} </template>

            <div v-if="selectedUser" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Basic Information
                    </h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Name
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ selectedUser.name }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Email
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ selectedUser.email }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Status
                            </dt>
                            <dd class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                        selectedUser.status === 'active'
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ]"
                                >
                                    {{
                                        selectedUser.status === "active"
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Created At
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDate(selectedUser.created_at) }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Last Updated
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDate(selectedUser.updated_at) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Roles -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Roles ({{ selectedUser.roles?.length || 0 }})
                    </h3>
                    <div
                        v-if="
                            selectedUser.roles && selectedUser.roles.length > 0
                        "
                        class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="role in selectedUser.roles"
                            :key="role.id"
                            class="flex items-center p-2 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-500"
                        >
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-4 w-4 text-red-500"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-2">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ role.display_name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ role.name }}
                                    <span
                                        v-if="role.pivot.is_active"
                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 ml-1"
                                    >
                                        Active
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                        <h3
                            class="mt-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            No roles assigned
                        </h3>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            This user doesn't have any roles assigned yet.
                        </p>
                    </div>
                </div>

                <!-- Direct Permissions -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Direct Permissions ({{
                            selectedUser.permissions?.length || 0
                        }})
                    </h3>
                    <div
                        v-if="
                            selectedUser.permissions &&
                            selectedUser.permissions.length > 0
                        "
                        class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="permission in selectedUser.permissions"
                            :key="permission.id"
                            class="flex items-center p-2 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-500"
                        >
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-4 w-4 text-red-500"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-2">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ permission.display_name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ permission.name }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                        <h3
                            class="mt-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            No direct permissions
                        </h3>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            This user doesn't have any direct permissions
                            assigned.
                        </p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Statistics
                    </h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                        <div class="text-center">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Total Roles
                            </dt>
                            <dd
                                class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white"
                            >
                                {{ selectedUser.roles?.length || 0 }}
                            </dd>
                        </div>
                        <div class="text-center">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Direct Permissions
                            </dt>
                            <dd
                                class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white"
                            >
                                {{ selectedUser.permissions?.length || 0 }}
                            </dd>
                        </div>
                        <div class="text-center">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Account Status
                            </dt>
                            <dd class="mt-1 text-lg font-semibold">
                                <span
                                    :class="[
                                        selectedUser.status === 'active'
                                            ? 'text-red-600'
                                            : 'text-red-600',
                                    ]"
                                >
                                    {{
                                        selectedUser.status === "active"
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Modal>

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :is-open="confirmationModal.isOpen"
            :title="confirmationModal.config.title"
            :message="confirmationModal.config.message"
            :description="confirmationModal.config.description"
            :confirm-text="confirmationModal.config.confirmText"
            :cancel-text="confirmationModal.config.cancelText"
            :loading="confirmationModal.loading"
            @confirm="confirmationModal.handleConfirm"
            @cancel="confirmationModal.handleCancel"
            @close="confirmationModal.handleClose"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import DataTable from "../../components/UI/DataTable.vue";
import Modal from "../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../components/Forms/FormInput.vue";
import Button from "../../components/Base/Button.vue";
import { useNotificationStore } from "@/stores/notification";
import { useConfirmationModalStore } from "@/stores/confirmationModal";
import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";
import {
    UsersIcon,
    CheckCircleIcon,
    ClockIcon,
    UserPlusIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    PauseIcon,
    PlayIcon,
} from "@heroicons/vue/24/outline";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const users = ref([]);
const selectedUsers = ref([]);
const allRoles = ref([]);
const allPermissions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showRolesModal = ref(false);
const showPermissionsModal = ref(false);
const selectedUser = ref(null);
const selectedUserRoles = ref([]);
const selectedUserPermissions = ref([]);
const activeRoleId = ref(null);
const saving = ref(false);
const savingRoles = ref(false);
const savingPermissions = ref(false);
const permissionGroup = ref("");

// Filter data
const roleFilter = ref("");
const statusFilter = ref("");
const applyingFilters = ref(false);

// Form data
const userForm = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Role validation
const roleErrors = ref([]);

// Validation functions
const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "name":
            if (!value || value.trim() === "") {
                errors.push("Name is required");
            } else if (value.length < 2) {
                errors.push("Name must be at least 2 characters");
            } else if (value.length > 255) {
                errors.push("Name must be less than 255 characters");
            } else if (!/^[a-zA-Z\s]+$/.test(value.trim())) {
                errors.push("Name can only contain letters and spaces");
            }
            break;
        case "email":
            if (!value || value.trim() === "") {
                errors.push("Email is required");
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                errors.push("Please enter a valid email address");
            } else if (value.length > 255) {
                errors.push("Email must be less than 255 characters");
            }
            break;
        case "password":
            if (showCreateModal.value) {
                if (!value || value.trim() === "") {
                    errors.push("Password is required");
                } else if (value.length < 8) {
                    errors.push("Password must be at least 8 characters");
                } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(value)) {
                    errors.push(
                        "Password must contain at least one uppercase letter, one lowercase letter, and one number"
                    );
                } else if (value.length > 255) {
                    errors.push("Password must be less than 255 characters");
                }
            }
            break;
        case "password_confirmation":
            if (showCreateModal.value) {
                if (!value || value.trim() === "") {
                    errors.push("Password confirmation is required");
                } else if (value !== userForm.value.password) {
                    errors.push("Passwords do not match");
                }
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(userForm.value).forEach((field) => {
        const fieldErrors = validateField(field, userForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, userForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

// Role management methods
const selectAllRoles = () => {
    selectedUserRoles.value = allRoles.value.map((role) => role.id);
    validateRoleSelection();
};

const deselectAllRoles = () => {
    selectedUserRoles.value = [];
    activeRoleId.value = null;
    validateRoleSelection();
};

const validateRoleSelection = () => {
    roleErrors.value = [];

    // Validate that at least one role is selected if we want an active role
    if (
        activeRoleId.value &&
        !selectedUserRoles.value.includes(activeRoleId.value)
    ) {
        roleErrors.value.push("The active role must be selected first");
    }

    // Validate that active role is from selected roles
    if (
        activeRoleId.value &&
        selectedUserRoles.value.length > 0 &&
        !selectedUserRoles.value.includes(activeRoleId.value)
    ) {
        activeRoleId.value = null;
    }
};

const validateActiveRole = () => {
    roleErrors.value = [];

    if (
        activeRoleId.value &&
        !selectedUserRoles.value.includes(activeRoleId.value)
    ) {
        roleErrors.value.push("Cannot set an unselected role as active");
        activeRoleId.value = null;
    }
};

const getRoleById = (roleId) => {
    return allRoles.value.find((role) => role.id === roleId);
};

const isRoleCurrentlyAssigned = (roleId) => {
    return (
        selectedUser.value?.roles?.some((role) => role.id === roleId) || false
    );
};

const isRoleActive = (roleId) => {
    return (
        selectedUser.value?.roles?.some(
            (role) => role.id === roleId && role.pivot?.is_active
        ) || false
    );
};

// Utility methods
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Table columns configuration
const columns = [
    {
        key: "avatar",
        label: "User",
        sortable: false,
    },
    {
        key: "role",
        label: "Roles",
        sortable: false,
    },
    {
        key: "email",
        label: "Email",
        sortable: true,
    },
    {
        key: "created_at",
        label: "Created",
        type: "date",
        sortable: true,
    },
];

// Computed properties
const totalUsers = computed(() => users.value.length);
const activeUsers = computed(
    () => users.value.filter((u) => u.status === "active").length
);
const pendingUsers = computed(
    () => users.value.filter((u) => u.status === "pending").length
);
const newUsers = computed(() => {
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
    return users.value.filter((u) => new Date(u.createdAt) > oneMonthAgo)
        .length;
});

const availableRoles = computed(() => {
    return allRoles.value.map((role) => ({
        value: role.id,
        label: role.display_name,
    }));
});

const permissionGroups = computed(() => {
    const groups = [
        ...new Set(allPermissions.value.map((p) => p.group).filter((g) => g)),
    ];
    return groups.map((group) => ({ value: group, label: group }));
});

const filteredPermissions = computed(() => {
    if (!permissionGroup.value) return allPermissions.value;
    return allPermissions.value.filter(
        (p) => p.group === permissionGroup.value
    );
});

// Methods
const getRoleBadgeClasses = (roleName) => {
    const classes = {
        admin: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        manager:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        user: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
    };
    return (
        classes[roleName] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const handleManageRoles = (user) => {
    selectedUser.value = user;
    selectedUserRoles.value = user.roles.map((r) => r.id);
    activeRoleId.value = user.roles.find((r) => r.pivot.is_active)?.id || null;
    showRolesModal.value = true;
};

const handleManagePermissions = (user) => {
    selectedUser.value = user;
    selectedUserPermissions.value = user.permissions.map((p) => p.id);
    showPermissionsModal.value = true;
};

const saveUserRoles = async () => {
    // Final validation before save
    validateRoleSelection();
    if (roleErrors.value.length > 0) {
        notification.error("Please fix the validation errors before saving");
        return;
    }

    savingRoles.value = true;
    try {
        const rolesData = selectedUserRoles.value.map((roleId) => ({
            id: roleId,
            is_active: roleId === activeRoleId.value,
        }));

        const data = await apiPost(`users/${selectedUser.value.id}/roles`, {
            roles: rolesData,
        });

        if (data.success) {
            const assignedCount = selectedUserRoles.value.length;
            const activeRoleName = activeRoleId.value
                ? getRoleById(activeRoleId.value)?.display_name
                : "None";

            notification.success(
                `Successfully assigned ${assignedCount} role(s) to ${selectedUser.value.name}. Active role: ${activeRoleName}`
            );
            closeModals();
            loadUsers();
        } else {
            // Handle server validation errors
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                notification.error(
                    errorMessages[0] || "Failed to update user roles"
                );
            } else {
                notification.error(
                    data.message || "Failed to update user roles"
                );
            }
        }
    } catch (error) {
        console.error("Save user roles error:", error);
        notification.error("Failed to update user roles. Please try again.");
    } finally {
        savingRoles.value = false;
    }
};

const saveUserPermissions = async () => {
    savingPermissions.value = true;
    try {
        const data = await apiPost(
            `users/${selectedUser.value.id}/permissions`,
            {
                permissions: selectedUserPermissions.value,
            }
        );

        if (data.success) {
            notification.success("User permissions updated successfully");
            closeModals();
            loadUsers();
        } else {
            notification.error(
                data.message || "Failed to update user permissions"
            );
        }
    } catch (error) {
        console.error("Save user permissions error:", error);
        notification.error("Failed to update user permissions");
    } finally {
        savingPermissions.value = false;
    }
};

const handleRefreshUsers = async () => {
    refreshLoading.value = true;
    try {
        await loadUsers();
        notification.success("Users data refreshed successfully");
    } catch (error) {
        console.error("Refresh users error:", error);
        notification.error("Failed to refresh users data");
    } finally {
        refreshLoading.value = false;
    }
};

const applyFilters = async () => {
    applyingFilters.value = true;
    try {
        // Here you could implement server-side filtering
        // For now, we'll just show a success message
        notification.success("Filters applied successfully");
    } catch (error) {
        notification.error("Failed to apply filters");
    } finally {
        applyingFilters.value = false;
    }
};

const saveUser = async () => {
    // Validate form before submission
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "users"
            : `users/${selectedUser.value.id}`;

        // Prepare user data
        const userData = { ...userForm.value };

        const data = showCreateModal.value
            ? await apiPost(url, userData)
            : await apiPut(url, userForm.value);

        if (data.success) {
            const user = data.data;

            // Handle roles assignment for new users
            if (showCreateModal.value && selectedUserRoles.value.length > 0) {
                const rolesData = selectedUserRoles.value.map((roleId) => ({
                    id: roleId,
                    is_active: roleId === activeRoleId.value,
                }));

                try {
                    await apiPost(`users/${user.id}/roles`, {
                        roles: rolesData,
                    });
                } catch (roleError) {
                    console.error("Failed to assign roles:", roleError);
                    notification.warning(
                        "User created but failed to assign some roles"
                    );
                }
            }

            // Handle permissions assignment for new users
            if (
                showCreateModal.value &&
                selectedUserPermissions.value.length > 0
            ) {
                try {
                    await apiPost(`users/${user.id}/permissions`, {
                        permissions: selectedUserPermissions.value,
                    });
                } catch (permError) {
                    console.error("Failed to assign permissions:", permError);
                    notification.warning(
                        "User created but failed to assign some permissions"
                    );
                }
            }

            notification.success(
                `User ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            loadUsers();
        } else {
            // Handle server validation errors
            if (data.errors) {
                formErrors.value = data.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(data.message || "Failed to save user");
            }
        }
    } catch (error) {
        console.error("Save user error:", error);
        notification.error("Failed to save user. Please try again.");
    } finally {
        saving.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    showRolesModal.value = false;
    showPermissionsModal.value = false;
    selectedUser.value = null;
    selectedUserRoles.value = [];
    selectedUserPermissions.value = [];
    activeRoleId.value = null;
    permissionGroup.value = "";
    userForm.value = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    };
    formErrors.value = {};
    formTouched.value = {};
    roleErrors.value = [];
};

const handleAddUser = () => {
    showCreateModal.value = true;
};

const handleEditUser = (user) => {
    selectedUser.value = user;
    userForm.value = {
        name: user.name,
        email: user.email,
        password: "",
        password_confirmation: "",
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
};

const handleDeleteUser = (user) => {
    confirmationModal.showModal({
        title: "Delete User",
        message: `Are you sure you want to delete "${user.name}"?`,
        description:
            "This action cannot be undone. All associated roles and permissions will also be removed.",
        confirmText: "Delete User",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiDelete(`users/${user.id}`);
            if (data.success) {
                notification.success("User deleted successfully");
                loadUsers();
                return data;
            } else {
                throw new Error(data.message || "Failed to delete user");
            }
        },
        onSuccess: (result) => {
            console.log("User deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete user:", error);
            notification.error(error.message || "Failed to delete user");
        },
    });
};

const handleBulkAction = (selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select users to perform bulk action");
        return;
    }

    confirmationModal.showModal({
        title: "Bulk Delete Users",
        message: `Are you sure you want to delete ${selectedItems.length} user(s)?`,
        description:
            "This action cannot be undone. All associated roles and permissions will also be removed.",
        confirmText: "Delete Users",
        cancelText: "Cancel",
        onConfirm: async () => {
            try {
                const deletePromises = selectedItems.map((item) =>
                    apiDelete(`users/${item.id}`)
                );
                await Promise.all(deletePromises);
                notification.success(
                    `${selectedItems.length} users deleted successfully`
                );
                loadUsers();
                return { success: true };
            } catch (error) {
                throw new Error("Failed to delete some users");
            }
        },
        onSuccess: (result) => {
            console.log("Bulk delete completed:", result);
        },
        onError: (error) => {
            console.error("Bulk delete failed:", error);
            notification.error(error.message || "Failed to delete users");
        },
    });
};

const handleSelectionChange = (selectedItems) => {
    selectedUsers.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const handleToggleStatus = async (user) => {
    const newStatus = user.status === "active" ? "inactive" : "active";
    const action = newStatus === "active" ? "activate" : "deactivate";

    try {
        const data = await apiPost(`users/${user.id}/toggle-status`);

        if (data.success) {
            notification.success(`User ${action}d successfully`);
            loadUsers();
        } else {
            notification.error(data.message || `Failed to ${action} user`);
        }
    } catch (error) {
        console.error(`Toggle status error:`, error);
        notification.error(`Failed to ${action} user`);
    }
};

const loadUsers = async () => {
    try {
        loading.value = true;
        const data = await apiGet("users");
        if (data.success) {
            users.value = data.data;
        }
    } catch (error) {
        console.error("Error loading users:", error);
        notification.error("Failed to load users");
    } finally {
        loading.value = false;
    }
};

const loadRolesAndPermissions = async () => {
    try {
        const [rolesData, permissionsData] = await Promise.all([
            apiGet("roles"),
            apiGet("permissions"),
        ]);

        if (rolesData.success) {
            allRoles.value = rolesData.data;
        }
        if (permissionsData.success) {
            allPermissions.value = permissionsData.data;
        }
    } catch (error) {
        console.error("Error loading roles and permissions:", error);
        notification.error("Failed to load roles and permissions");
    }
};

// Lifecycle
onMounted(() => {
    loadUsers();
    loadRolesAndPermissions();
});
</script>
