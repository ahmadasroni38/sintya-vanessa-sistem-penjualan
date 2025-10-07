<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Permission Management
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage permissions and their assignments in your system
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <KeyIcon
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Permissions
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalPermissions }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg"
                    >
                        <CheckCircleIcon
                            class="w-6 h-6 text-green-600 dark:text-green-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Active Permissions
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activePermissions }}
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
                            Inactive Permissions
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ inactivePermissions }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg"
                    >
                        <ShieldCheckIcon
                            class="w-6 h-6 text-indigo-600 dark:text-indigo-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Assigned to Roles
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ permissionsWithRoles }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Permissions"
            description="A list of all permissions in your system including their groups and status."
            :data="permissions"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="Create Permission"
            :show-filters="true"
            :show-export="false"
            :show-bulk-actions="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search permissions..."
            empty-title="No permissions found"
            empty-description="Get started by creating your first permission."
            @add="handleAddPermission"
            @edit="handleEditPermission"
            @delete="handleDeletePermission"
            @bulk-action="handleBulkAction"
            @export="handleExportPermissions"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshPermissions"
        >
            <!-- Custom Permission Name Column -->
            <template #column-name="{ item }">
                <div class="flex items-center">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3"
                    >
                        <KeyIcon class="w-4 h-4 text-white" />
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

            <!-- Custom Group Column -->
            <template #column-group="{ item }">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
                >
                    {{ item.group || "No Group" }}
                </span>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            item.is_active ? 'bg-green-500' : 'bg-red-500',
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                    >
                        {{ item.is_active ? "Active" : "Inactive" }}
                    </span>
                </div>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleViewPermission(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleEditPermission(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Edit Permission"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeletePermission(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Permission"
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
                            Group
                        </label>
                        <select
                            v-model="groupFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Groups</option>
                            <option
                                v-for="group in availableGroups"
                                :key="group"
                                :value="group"
                            >
                                {{ group }}
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
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                            class="flex-1 px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
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
                                groupFilter = '';
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

        <!-- Create/Edit Permission Modal -->
        <Modal :is-open="showCreateModal || showEditModal" @close="closeModals">
            <template #title>
                {{ showCreateModal ? "Create Permission" : "Edit Permission" }}
            </template>

            <form @submit.prevent="savePermission" class="space-y-6">
                <div class="space-y-5">
                    <FormInput
                        v-model="permissionForm.name"
                        label="Name"
                        placeholder="permission_name"
                        :error="getFieldError('name')"
                        required
                        @blur="handleFieldBlur('name')"
                    />
                    <FormInput
                        v-model="permissionForm.display_name"
                        label="Display Name"
                        placeholder="Permission Display Name"
                        :error="getFieldError('display_name')"
                        required
                        @blur="handleFieldBlur('display_name')"
                    />
                    <FormInput
                        v-model="permissionForm.description"
                        label="Description"
                        placeholder="Permission description"
                        @blur="handleFieldBlur('description')"
                    />
                    <FormInput
                        v-model="permissionForm.group"
                        label="Group"
                        placeholder="e.g., users, roles, products"
                        @blur="handleFieldBlur('group')"
                    />
                    <div class="flex items-center">
                        <input
                            id="perm_is_active"
                            v-model="permissionForm.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded focus:ring-2"
                        />
                        <label
                            for="perm_is_active"
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
                        {{
                            showCreateModal
                                ? "Create Permission"
                                : "Update Permission"
                        }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View Permission Details Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="lg">
            <template #title>
                Permission Details: {{ selectedPermission?.display_name }}
            </template>

            <div v-if="selectedPermission" class="space-y-6">
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
                                {{ selectedPermission.name }}
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
                                {{ selectedPermission.display_name }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Group
                            </dt>
                            <dd class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
                                >
                                    {{ selectedPermission.group || "No Group" }}
                                </span>
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
                                        selectedPermission.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    ]"
                                >
                                    {{
                                        selectedPermission.is_active
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
                                {{ formatDate(selectedPermission.created_at) }}
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
                                    selectedPermission.description ||
                                    "No description provided"
                                }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Assigned Roles -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Assigned Roles ({{
                            selectedPermission.roles?.length || 0
                        }})
                    </h3>
                    <div
                        v-if="
                            selectedPermission.roles &&
                            selectedPermission.roles.length > 0
                        "
                        class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="role in selectedPermission.roles"
                            :key="role.id"
                            class="flex items-center p-2 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-500"
                        >
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-4 w-4 text-green-500"
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
                            No roles assigned
                        </h3>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            This permission is not assigned to any roles yet.
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
                                {{ selectedPermission.roles?.length || 0 }}
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
                                        selectedPermission.is_active
                                            ? 'text-green-600'
                                            : 'text-red-600',
                                    ]"
                                >
                                    {{
                                        selectedPermission.is_active
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
                                        selectedPermission.updated_at ||
                                            selectedPermission.created_at
                                    )
                                }}
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
    KeyIcon,
    CheckCircleIcon,
    XCircleIcon,
    ShieldCheckIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/outline";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const permissions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedPermission = ref(null);
const saving = ref(false);

// Filter data
const groupFilter = ref("");
const statusFilter = ref("");
const applyingFilters = ref(false);

// Form data
const permissionForm = ref({
    name: "",
    display_name: "",
    description: "",
    group: "",
    is_active: true,
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Computed properties
const totalPermissions = computed(() => permissions.value.length);
const activePermissions = computed(
    () => permissions.value.filter((p) => p.is_active).length
);
const inactivePermissions = computed(
    () => permissions.value.filter((p) => !p.is_active).length
);
const permissionsWithRoles = computed(
    () => permissions.value.filter((p) => p.roles && p.roles.length > 0).length
);
const availableGroups = computed(() => {
    return [...new Set(permissions.value.map((p) => p.group).filter((g) => g))];
});

// Validation functions
const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "name":
            if (!value || value.trim() === "") {
                errors.push("Permission name is required");
            } else if (value.length < 3) {
                errors.push("Permission name must be at least 3 characters");
            } else if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
                errors.push(
                    "Permission name can only contain letters, numbers, underscores, and hyphens"
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

    Object.keys(permissionForm.value).forEach((field) => {
        const fieldErrors = validateField(field, permissionForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, permissionForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

// Methods
const fetchPermissions = async () => {
    try {
        loading.value = true;
        const data = await apiGet("permissions");
        if (data.success) {
            permissions.value = data.data;
        }
    } catch (error) {
        notification.error("Failed to fetch permissions");
    } finally {
        loading.value = false;
    }
};

const handleAddPermission = () => {
    showCreateModal.value = true;
};

const handleEditPermission = (permission) => {
    selectedPermission.value = permission;
    permissionForm.value = {
        name: permission.name,
        display_name: permission.display_name,
        description: permission.description,
        group: permission.group,
        is_active: permission.is_active,
    };
    showEditModal.value = true;
};

const handleViewPermission = (permission) => {
    selectedPermission.value = permission;
    showViewModal.value = true;
};

const handleDeletePermission = (permission) => {
    confirmationModal.showModal({
        title: "Delete Permission",
        message: `Are you sure you want to delete "${permission.display_name}"?`,
        description:
            "This action cannot be undone. All role assignments will also be removed.",
        confirmText: "Delete Permission",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiDelete(`permissions/${permission.id}`);
            if (data.success) {
                notification.success("Permission deleted successfully");
                fetchPermissions();
                return data;
            } else {
                throw new Error(data.message || "Failed to delete permission");
            }
        },
        onSuccess: (result) => {
            console.log("Permission deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete permission:", error);
            notification.error(error.message || "Failed to delete permission");
        },
    });
};

const handleBulkAction = (selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning(
            "Please select permissions to perform bulk action"
        );
        return;
    }

    confirmationModal.showModal({
        title: "Bulk Delete Permissions",
        message: `Are you sure you want to delete ${selectedItems.length} permission(s)?`,
        description:
            "This action cannot be undone. All role assignments will also be removed.",
        confirmText: "Delete Permissions",
        cancelText: "Cancel",
        onConfirm: async () => {
            try {
                const deletePromises = selectedItems.map((item) =>
                    apiDelete(`permissions/${item.id}`)
                );
                await Promise.all(deletePromises);
                notification.success(
                    `${selectedItems.length} permissions deleted successfully`
                );
                fetchPermissions();
                return { success: true };
            } catch (error) {
                throw new Error("Failed to delete some permissions");
            }
        },
        onSuccess: (result) => {
            console.log("Bulk delete completed:", result);
        },
        onError: (error) => {
            console.error("Bulk delete failed:", error);
            notification.error(error.message || "Failed to delete permissions");
        },
    });
};

const handleExportPermissions = (data) => {
    try {
        // Create CSV content
        const headers = [
            "Name",
            "Display Name",
            "Group",
            "Description",
            "Status",
            "Created At",
        ];
        const csvContent = [
            headers.join(","),
            ...data.map((item) =>
                [
                    item.name,
                    `"${item.display_name}"`,
                    item.group || "",
                    `"${item.description || ""}"`,
                    item.is_active ? "Active" : "Inactive",
                    formatDate(item.created_at),
                ].join(",")
            ),
        ].join("\n");

        // Create and download file
        const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;",
        });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute(
            "download",
            `permissions_export_${new Date().toISOString().split("T")[0]}.csv`
        );
        link.style.visibility = "hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        notification.success("Permissions exported successfully");
    } catch (error) {
        console.error("Export error:", error);
        notification.error("Failed to export permissions");
    }
};

const handleSelectionChange = (selectedItems) => {
    console.log("Selection changed:", selectedItems);
};

const savePermission = async () => {
    // Validate form before submission
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "permissions"
            : `permissions/${selectedPermission.value.id}`;

        const data = showCreateModal.value
            ? await apiPost(url, permissionForm.value)
            : await apiPut(url, permissionForm.value);

        if (data.success) {
            notification.success(
                `Permission ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            fetchPermissions();
        } else {
            // Handle server validation errors
            if (data.errors) {
                formErrors.value = data.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(data.message || "Failed to save permission");
            }
        }
    } catch (error) {
        console.error("Save permission error:", error);
        notification.error("Failed to save permission. Please try again.");
    } finally {
        saving.value = false;
    }
};

const handleRefreshPermissions = async () => {
    refreshLoading.value = true;
    try {
        await fetchPermissions();
        notification.success("Permissions data refreshed successfully");
    } catch (error) {
        console.error("Refresh permissions error:", error);
        notification.error("Failed to refresh permissions data");
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

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    selectedPermission.value = null;
    permissionForm.value = {
        name: "",
        display_name: "",
        description: "",
        group: "",
        is_active: true,
    };
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
        label: "Permission",
        sortable: true,
    },
    {
        key: "group",
        label: "Group",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
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
    fetchPermissions();
});
</script>
