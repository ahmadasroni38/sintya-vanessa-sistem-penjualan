<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Role Management
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage roles and their permissions in your system
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
                        <ShieldCheckIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Roles
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalRoles }}
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
                            Active Roles
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activeRoles }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <XCircleIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Inactive Roles
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ inactiveRoles }}
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
                        <KeyIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            With Permissions
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ rolesWithPermissions }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Roles"
            description="A list of all roles in your system including their permissions and status."
            :data="roles"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="Create Role"
            :show-filters="false"
            :show-export="false"
            :show-bulk-actions="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search roles..."
            empty-title="No roles found"
            empty-description="Get started by creating your first role."
            @add="handleAddRole"
            @edit="handleEditRole"
            @delete="handleDeleteRole"
            @bulk-action="handleBulkAction"
            @export="handleExportRoles"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshRoles"
        >
            <!-- Custom Role Name Column -->
            <template #column-name="{ item }">
                <div class="flex items-center">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center mr-3"
                    >
                        <ShieldCheckIcon class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <p
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.display_name }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ item.name }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            item.is_active ? 'bg-red-500' : 'bg-red-500',
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                    >
                        {{ item.is_active ? "Active" : "Inactive" }}
                    </span>
                </div>
            </template>

            <!-- Custom Permissions Column -->
            <template #column-permissions="{ item }">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ item.permissions.length }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        permissions
                    </span>
                </div>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleViewRole(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleEditRole(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Edit Role"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleManagePermissions(item)"
                        class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 dark:hover:text-purple-400 dark:hover:bg-purple-900/20"
                        title="Manage Permissions"
                    >
                        <KeyIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeleteRole(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Role"
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
                            Status
                        </label>
                        <select
                            v-model="statusFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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
                            @click="statusFilter = ''"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </template>
        </DataTable>

        <!-- Create/Edit Role Modal -->
        <Modal :is-open="showCreateModal || showEditModal" @close="closeModals">
            <template #title>
                {{ showCreateModal ? "Create Role" : "Edit Role" }}
            </template>

            <form @submit.prevent="saveRole" class="space-y-6">
                <div class="space-y-5">
                    <FormInput
                        v-model="roleForm.name"
                        label="Name"
                        placeholder="role_name"
                        :error="getFieldError('name')"
                        required
                        @blur="handleFieldBlur('name')"
                    />
                    <FormInput
                        v-model="roleForm.display_name"
                        label="Display Name"
                        placeholder="Role Display Name"
                        :error="getFieldError('display_name')"
                        required
                        @blur="handleFieldBlur('display_name')"
                    />
                    <FormInput
                        v-model="roleForm.description"
                        label="Description"
                        placeholder="Role description"
                        @blur="handleFieldBlur('description')"
                    />
                    <div class="flex items-center">
                        <input
                            id="is_active"
                            v-model="roleForm.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded focus:ring-2"
                        />
                        <label
                            for="is_active"
                            class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            Active
                        </label>
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
                        {{ showCreateModal ? "Create Role" : "Update Role" }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View Role Details Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="lg">
            <template #title>
                Role Details: {{ selectedRole?.display_name }}
            </template>

            <div v-if="selectedRole" class="space-y-6">
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
                                {{ selectedRole.name }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Display Name
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ selectedRole.display_name }}
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
                                        selectedRole.is_active
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ]"
                                >
                                    {{
                                        selectedRole.is_active
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
                                {{ formatDate(selectedRole.created_at) }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Description
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{
                                    selectedRole.description ||
                                    "No description provided"
                                }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Permissions -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Permissions ({{ selectedRole.permissions.length }})
                    </h3>
                    <div
                        v-if="selectedRole.permissions.length > 0"
                        class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="permission in selectedRole.permissions"
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
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
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
                            No permissions assigned
                        </h3>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            This role doesn't have any permissions assigned yet.
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
                                Total Permissions
                            </dt>
                            <dd
                                class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white"
                            >
                                {{ selectedRole.permissions.length }}
                            </dd>
                        </div>
                        <div class="text-center">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Status
                            </dt>
                            <dd class="mt-1 text-lg font-semibold">
                                <span
                                    :class="[
                                        selectedRole.is_active
                                            ? 'text-red-600'
                                            : 'text-red-600',
                                    ]"
                                >
                                    {{
                                        selectedRole.is_active
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </span>
                            </dd>
                        </div>
                        <div class="text-center">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Last Updated
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{
                                    formatDate(
                                        selectedRole.updated_at ||
                                            selectedRole.created_at
                                    )
                                }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Modal>

        <!-- Permissions Modal -->
        <Modal :is-open="showPermissionsModal" @close="closeModals" size="lg">
            <template #title>
                Manage Permissions for {{ selectedRole?.display_name }}
            </template>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            Available Permissions
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ selectedPermissions.length }} of
                            {{ filteredPermissions.length }} selected
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex gap-2">
                            <button
                                @click="checkAllPermissions"
                                class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700 focus:ring-2 focus:ring-red-500 disabled:opacity-50"
                                :disabled="filteredPermissions.length === 0"
                            >
                                Check All
                            </button>
                            <button
                                @click="uncheckAllPermissions"
                                class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 disabled:opacity-50"
                                :disabled="filteredPermissions.length === 0"
                            >
                                Uncheck All
                            </button>
                        </div>
                        <div class="relative w-48">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Filter by group
                            </label>
                            <select
                                v-model="permissionGroup"
                                data-hs-select='{
                                  "hasSearch": true,
                                  "searchPlaceholder": "Search groups...",
                                  "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                                  "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-900",
                                  "placeholder": "All groups...",
                                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                                  "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                                  "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700",
                                  "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                  "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-gray-800 dark:text-neutral-200 \" data-title></div></div></div>",
                                  "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                }'
                                class="hidden"
                            >
                                <option value="">All groups</option>
                                <option
                                    v-for="group in permissionGroups"
                                    :key="group.value"
                                    :value="group.value"
                                >
                                    {{ group.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                    <div
                        v-for="permission in filteredPermissions"
                        :key="permission.id"
                        :class="[
                            'flex items-center p-2 rounded-md border',
                            selectedPermissions.includes(permission.id)
                                ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700'
                                : 'bg-white dark:bg-gray-600 border-gray-200 dark:border-gray-500',
                        ]"
                    >
                        <input
                            :id="'perm-' + permission.id"
                            v-model="selectedPermissions"
                            :value="permission.id"
                            type="checkbox"
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                        />
                        <label
                            :for="'perm-' + permission.id"
                            class="ml-2 block text-sm cursor-pointer flex-1"
                        >
                            <span
                                class="text-gray-900 dark:text-white font-medium"
                            >
                                {{ permission.display_name }}
                            </span>
                            <span
                                class="text-gray-500 dark:text-gray-400 block text-xs"
                            >
                                {{ permission.name }}
                            </span>
                            <span
                                v-if="
                                    selectedRole.permissions.some(
                                        (p) => p.id === permission.id
                                    )
                                "
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1"
                            >
                                Currently Assigned
                            </span>
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
                    :disabled="savingPermissions"
                    class="w-full sm:w-auto"
                >
                    Cancel
                </Button>
                <Button
                    @click="savePermissions"
                    :loading="savingPermissions"
                    :disabled="savingPermissions"
                    class="w-full sm:w-auto"
                >
                    Save Permissions
                </Button>
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
    ShieldCheckIcon,
    CheckCircleIcon,
    XCircleIcon,
    KeyIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const roles = ref([]);
const permissions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showPermissionsModal = ref(false);
const selectedRole = ref(null);
const saving = ref(false);
const savingPermissions = ref(false);
const permissionGroup = ref("");
const selectedPermissions = ref([]);

// Filter data
const statusFilter = ref("");
const applyingFilters = ref(false);

// Form data
const roleForm = ref({
    name: "",
    display_name: "",
    description: "",
    is_active: true,
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Computed properties
const totalRoles = computed(() => roles.value.length);
const activeRoles = computed(
    () => roles.value.filter((r) => r.is_active).length
);
const inactiveRoles = computed(
    () => roles.value.filter((r) => !r.is_active).length
);
const rolesWithPermissions = computed(
    () => roles.value.filter((r) => r.permissions.length > 0).length
);

const permissionGroups = computed(() => {
    const groups = [
        ...new Set(permissions.value.map((p) => p.group).filter((g) => g)),
    ];
    return groups.map((group) => ({ value: group, label: group }));
});

const filteredPermissions = computed(() => {
    if (!permissionGroup.value) return permissions.value;
    return permissions.value.filter((p) => p.group === permissionGroup.value);
});

// Validation functions
const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "name":
            if (!value || value.trim() === "") {
                errors.push("Role name is required");
            } else if (value.length < 3) {
                errors.push("Role name must be at least 3 characters");
            } else if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
                errors.push(
                    "Role name can only contain letters, numbers, underscores, and hyphens"
                );
            }
            break;
        case "display_name":
            if (!value || value.trim() === "") {
                errors.push("Display name is required");
            } else if (value.length < 3) {
                errors.push("Display name must be at least 3 characters");
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(roleForm.value).forEach((field) => {
        const fieldErrors = validateField(field, roleForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, roleForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

// Methods
const fetchRoles = async () => {
    try {
        loading.value = true;
        const data = await apiGet("roles");
        if (data.success) {
            roles.value = data.data;
        }
    } catch (error) {
        notification.error("Failed to fetch roles");
    } finally {
        loading.value = false;
    }
};

const fetchPermissions = async () => {
    try {
        const data = await apiGet("permissions");
        if (data.success) {
            permissions.value = data.data;
        }
    } catch (error) {
        notification.error("Failed to fetch permissions");
    }
};

const handleAddRole = () => {
    showCreateModal.value = true;
};

const handleEditRole = (role) => {
    selectedRole.value = role;
    roleForm.value = {
        name: role.name,
        display_name: role.display_name,
        description: role.description,
        is_active: role.is_active,
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
};

const handleViewRole = (role) => {
    selectedRole.value = role;
    showViewModal.value = true;
};

const handleManagePermissions = async (role) => {
    try {
        // Ensure permissions are loaded
        if (permissions.value.length === 0) {
            await fetchPermissions();
        }

        selectedRole.value = role;
        selectedPermissions.value = role.permissions.map((p) => p.id);
        permissionGroup.value = ""; // Reset filter
        showPermissionsModal.value = true;
    } catch (error) {
        notification.error("Failed to load permissions data");
        console.error("Error loading permissions:", error);
    }
};

const handleDeleteRole = (role) => {
    confirmationModal.showModal({
        title: "Delete Role",
        message: `Are you sure you want to delete "${role.display_name}"?`,
        description:
            "This action cannot be undone. All associated permissions will also be removed.",
        confirmText: "Delete Role",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiDelete(`roles/${role.id}`);
            if (data.success) {
                notification.success("Role deleted successfully");
                fetchRoles();
                return data;
            } else {
                throw new Error(data.message || "Failed to delete role");
            }
        },
        onSuccess: (result) => {
            console.log("Role deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete role:", error);
            notification.error(error.message || "Failed to delete role");
        },
    });
};

const handleBulkAction = (selectedItems) => {
    console.log("Bulk action for items:", selectedItems);
    // Implement bulk actions logic
};

const handleExportRoles = (data) => {
    console.log("Export roles:", data);
    // Implement export logic
};

const handleSelectionChange = (selectedItems) => {
    console.log("Selection changed:", selectedItems);
};

const handleRefreshRoles = async () => {
    refreshLoading.value = true;
    try {
        await fetchRoles();
        notification.success("Roles data refreshed successfully");
    } catch (error) {
        console.error("Refresh roles error:", error);
        notification.error("Failed to refresh roles data");
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

const saveRole = async () => {
    // Validate form before submission
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "roles"
            : `roles/${selectedRole.value.id}`;

        const data = showCreateModal.value
            ? await apiPost(url, roleForm.value)
            : await apiPut(url, roleForm.value);

        if (data.success) {
            notification.success(
                `Role ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            fetchRoles();
        } else {
            // Handle server validation errors
            if (data.errors) {
                formErrors.value = data.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(data.message || "Failed to save role");
            }
        }
    } catch (error) {
        console.error("Save role error:", error);
        notification.error("Failed to save role. Please try again.");
    } finally {
        saving.value = false;
    }
};

const savePermissions = async () => {
    if (!selectedRole.value) {
        notification.error("No role selected");
        return;
    }

    savingPermissions.value = true;
    try {
        const data = await apiPost(
            `roles/${selectedRole.value.id}/permissions`,
            {
                permissions: selectedPermissions.value,
            }
        );

        if (data.success) {
            notification.success(
                `Permissions updated successfully for "${selectedRole.value?.display_name || 'Role'}"`
            );
            closeModals();
            await fetchRoles(); // Refresh roles to show updated permissions
        } else {
            // Handle server validation errors
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                notification.error(
                    errorMessages[0] || "Failed to update permissions"
                );
            } else {
                notification.error(
                    data.message || "Failed to update permissions"
                );
            }
        }
    } catch (error) {
        console.error("Save permissions error:", error);
        const errorMessage =
            error.response?.data?.message ||
            error.message ||
            "Failed to update permissions. Please try again.";
        notification.error(errorMessage);
    } finally {
        savingPermissions.value = false;
    }
};

const checkAllPermissions = () => {
    const allIds = filteredPermissions.value.map(p => p.id);
    selectedPermissions.value = [...new Set([...selectedPermissions.value, ...allIds])];
};

const uncheckAllPermissions = () => {
    const filteredIds = filteredPermissions.value.map(p => p.id);
    selectedPermissions.value = selectedPermissions.value.filter(id => !filteredIds.includes(id));
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    showPermissionsModal.value = false;
    selectedRole.value = null;
    roleForm.value = {
        name: "",
        display_name: "",
        description: "",
        is_active: true,
    };
    selectedPermissions.value = [];
    permissionGroup.value = "";
    formErrors.value = {};
    formTouched.value = {};
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
        key: "name",
        label: "Role",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
    },
    {
        key: "permissions",
        label: "Permissions",
        sortable: false,
    },
    {
        key: "created_at",
        label: "Created",
        type: "date",
        sortable: true,
    },
];

// Lifecycle
onMounted(() => {
    fetchRoles();
    fetchPermissions();
});
</script>
