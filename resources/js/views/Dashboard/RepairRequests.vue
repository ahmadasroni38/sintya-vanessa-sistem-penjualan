<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Repair Requests
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage and track repair requests for assets
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
                        <ClipboardDocumentListIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Requests
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalRequests }}
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
                            {{ pendingRequests }}
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
                            Completed
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ completedRequests }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <ExclamationTriangleIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Urgent
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ urgentRequests }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <FormInput
                    v-model="filters.search"
                    placeholder="Search repair requests..."
                    @input="debouncedSearch"
                >
                    <template #prefix>
                        <MagnifyingGlassIcon class="w-4 h-4" />
                    </template>
                </FormInput>

                <!-- Status Filter -->
                <FormSelect
                    v-model="filters.status"
                    placeholder="All Statuses"
                    :options="statusOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Priority Filter -->
                <FormSelect
                    v-model="filters.priority"
                    placeholder="All Priorities"
                    :options="priorityOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Asset Filter -->
                <FormSelect
                    v-model="filters.asset_id"
                    placeholder="All Assets"
                    :options="assetOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Requester Filter -->
                <FormSelect
                    v-model="filters.requester_id"
                    placeholder="All Requesters"
                    :options="userOptions"
                    @update:modelValue="handleFilterChange"
                />
            </div>
            <div class="flex justify-end mt-4">
                <Button
                    variant="secondary"
                    @click="clearFilters"
                    :disabled="!hasActiveFilters"
                >
                    Clear Filters
                </Button>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Repair Requests"
            description="A comprehensive list of all repair requests for assets."
            :data="repairRequests.data || []"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="New Request"
            :show-filters="false"
            :show-bulk-actions="false"
            :show-export="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search requests..."
            empty-title="No repair requests found"
            empty-description="Get started by creating your first repair request."
            @add="handleAddRequest"
            @edit="handleEditRequest"
            @delete="handleDeleteRequest"
            @bulk-action="handleBulkAction"
            @export="handleExportRequests"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshRequests"
        >
            <!-- Custom Code Column -->
            <template #column-code="{ item }">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ item.code }}
                    </span>
                </div>
            </template>

            <!-- Custom Issue Column -->
            <template #column-issue="{ item }">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ item.issue }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ item.description?.substring(0, 50) }}{{ item.description?.length > 50 ? '...' : '' }}
                    </p>
                </div>
            </template>

            <!-- Custom Asset Column -->
            <template #column-asset="{ item }">
                <div v-if="item.asset">
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ item.asset.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ item.asset.code }}
                    </p>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    No Asset
                </span>
            </template>

            <!-- Custom Requester Column -->
            <template #column-requester="{ item }">
                <div v-if="item.requester">
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ item.requester.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ item.requester.email }}
                    </p>
                </div>
            </template>

            <!-- Custom Priority Column -->
            <template #column-priority="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getPriorityBadgeClass(item.priority),
                    ]"
                >
                    {{ item.priority }}
                </span>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getStatusBadgeClass(item.status),
                    ]"
                >
                    {{ item.status }}
                </span>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleViewRequest(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleEditRequest(item)"
                        class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 dark:hover:text-yellow-400 dark:hover:bg-yellow-900/20"
                        title="Edit Request"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeleteRequest(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Request"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Pagination -->
        <div v-if="repairRequests.last_page > 1" class="flex justify-center">
            <div class="flex items-center gap-2">
                <Button
                    variant="secondary"
                    :disabled="repairRequests.current_page === 1"
                    @click="goToPage(repairRequests.current_page - 1)"
                >
                    Previous
                </Button>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Page {{ repairRequests.current_page }} of {{ repairRequests.last_page }}
                </span>
                <Button
                    variant="secondary"
                    :disabled="repairRequests.current_page === repairRequests.last_page"
                    @click="goToPage(repairRequests.current_page + 1)"
                >
                    Next
                </Button>
            </div>
        </div>

        <!-- Create/Edit Request Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                {{ showCreateModal ? "Create Repair Request" : "Edit Repair Request" }}
            </template>

            <form @submit.prevent="saveRequest" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="requestForm.issue"
                            label="Issue Title"
                            placeholder="Enter issue title"
                            :error="getFieldError('issue')"
                            required
                            @blur="handleFieldBlur('issue')"
                        />
                        <FormInput
                            v-model="requestForm.code"
                            label="Request Code"
                            placeholder="Auto-generated if empty"
                            hint="Leave empty to auto-generate"
                            :error="getFieldError('code')"
                            @blur="handleFieldBlur('code')"
                        />
                    </div>
                    <FormTextarea
                        v-model="requestForm.description"
                        label="Description"
                        placeholder="Enter detailed description of the issue"
                        :error="getFieldError('description')"
                        rows="3"
                        @blur="handleFieldBlur('description')"
                    />
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Priority Select -->
                        <FormSelect
                            v-model="requestForm.priority"
                            label="Priority"
                            placeholder="Select priority"
                            :options="priorityOptions"
                            :error="getFieldError('priority')"
                            required
                            @blur="handleFieldBlur('priority')"
                            @update:modelValue="handleFieldBlur('priority')"
                        />

                        <!-- Status Select -->
                        <FormSelect
                            v-model="requestForm.status"
                            label="Status"
                            placeholder="Select status"
                            :options="statusOptions"
                            :error="getFieldError('status')"
                            @blur="handleFieldBlur('status')"
                            @update:modelValue="handleFieldBlur('status')"
                        />
                    </div>
                </div>

                <!-- Asset & Location -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Asset & Location
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Asset Select -->
                        <FormSelect
                            v-model="requestForm.asset_id"
                            label="Asset"
                            placeholder="Select asset"
                            :options="assetOptions"
                            :error="getFieldError('asset_id')"
                            required
                            @blur="handleFieldBlur('asset_id')"
                            @update:modelValue="handleFieldBlur('asset_id')"
                        />

                        <!-- Location Select -->
                        <FormSelect
                            v-model="requestForm.location_id"
                            label="Location"
                            placeholder="Select location (optional)"
                            :options="locationOptions"
                            :error="getFieldError('location_id')"
                            @blur="handleFieldBlur('location_id')"
                            @update:modelValue="handleFieldBlur('location_id')"
                        />
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Additional Details
                    </h3>
                    <FormTextarea
                        v-model="requestForm.notes"
                        label="Notes"
                        placeholder="Enter any additional notes"
                        :error="getFieldError('notes')"
                        rows="3"
                        @blur="handleFieldBlur('notes')"
                    />
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
                        {{ showCreateModal ? "Create Request" : "Update Request" }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View Request Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="xl">
            <template #title>Repair Request Details</template>

            <div v-if="selectedRequest" class="space-y-6">
                <!-- Request Header -->
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3
                            class="text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ selectedRequest.issue }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ selectedRequest.code }}
                        </p>
                        <div class="flex items-center gap-4 mt-2">
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getPriorityBadgeClass(selectedRequest.priority),
                                ]"
                            >
                                {{ selectedRequest.priority }} Priority
                            </span>
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getStatusBadgeClass(selectedRequest.status),
                                ]"
                            >
                                {{ selectedRequest.status }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button
                            @click="handleEditRequest(selectedRequest)"
                            variant="primary"
                            size="sm"
                        >
                            <PencilIcon class="w-4 h-4 mr-2" />
                            Edit
                        </Button>
                    </div>
                </div>

                <!-- Request Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Asset & Location
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Asset:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedRequest.asset ? selectedRequest.asset.name : "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Location:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedRequest.location ? selectedRequest.location.name : "N/A"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Request Information
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Requester:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedRequest.requester ? selectedRequest.requester.name : "Unknown"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Reported Date:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedRequest.created_at
                                            ? formatDate(selectedRequest.created_at)
                                            : "N/A"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description & Notes -->
                <div class="space-y-4">
                    <div v-if="selectedRequest.description">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Description
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedRequest.description }}
                        </p>
                    </div>

                    <div v-if="selectedRequest.notes">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Notes
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedRequest.notes }}
                        </p>
                    </div>
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
import { useRoute } from "vue-router";
import DataTable from "../../components/UI/DataTable.vue";
import Modal from "../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../components/Forms/FormInput.vue";
import FormSelect from "../../components/Forms/FormSelect.vue";
import FormTextarea from "../../components/Forms/FormTextarea.vue";
import Button from "../../components/Base/Button.vue";
import { useNotificationStore } from "@/stores/notification";
import { useConfirmationModalStore } from "@/stores/confirmationModal";
import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";
import {
    ClipboardDocumentListIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/outline";

const route = useRoute();
const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const repairRequests = ref({});
const statistics = ref({});
const selectedRequests = ref([]);
const userOptions = ref([]);
const assetOptions = ref([]);
const locationOptions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedRequest = ref(null);
const saving = ref(false);

// Filters
const filters = ref({
    search: "",
    status: "",
    priority: "",
    asset_id: "",
    requester_id: "",
});

// Form data
const requestForm = ref({
    issue: "",
    code: "",
    description: "",
    priority: "",
    status: "pending",
    asset_id: "",
    location_id: "",
    notes: "",
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Options
const statusOptions = [
    { value: "pending", label: "Pending" },
    { value: "approved", label: "Approved" },
    { value: "in_progress", label: "In Progress" },
    { value: "completed", label: "Completed" },
    { value: "rejected", label: "Rejected" },
];

const priorityOptions = [
    { value: "low", label: "Low" },
    { value: "medium", label: "Medium" },
    { value: "high", label: "High" },
    { value: "critical", label: "Critical" },
];

// Computed properties
const totalRequests = computed(() => statistics.value.total_repair_requests || 0);
const pendingRequests = computed(() => statistics.value.pending_repair_requests || 0);
const completedRequests = computed(() => statistics.value.completed_repair_requests || 0);
const urgentRequests = computed(() => statistics.value.urgent_repair_requests || 0);

const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some(
        (value) => value !== "" && value !== null
    );
});

// Table columns configuration
const columns = [
    {
        key: "code",
        label: "Code",
        sortable: true,
    },
    {
        key: "issue",
        label: "Issue",
        sortable: true,
    },
    {
        key: "asset",
        label: "Asset",
        sortable: false,
    },
    {
        key: "requester",
        label: "Requester",
        sortable: false,
    },
    {
        key: "priority",
        label: "Priority",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
    },
    {
        key: "created_at",
        label: "Reported",
        type: "date",
        sortable: true,
    },
];

// Methods
let searchTimeout = null;
const debouncedSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        loadRepairRequests();
    }, 500);
};

const handleFilterChange = () => {
    loadRepairRequests();
};

const clearFilters = () => {
    filters.value = {
        search: "",
        status: "",
        priority: "",
        asset_id: "",
        requester_id: "",
    };
    loadRepairRequests();
};

const handleRefreshRequests = async () => {
    refreshLoading.value = true;
    try {
        await Promise.all([loadRepairRequests(), loadStatistics()]);
        notification.success("Repair requests refreshed successfully");
    } catch (error) {
        console.error("Refresh error:", error);
        notification.error("Failed to refresh repair requests");
    } finally {
        refreshLoading.value = false;
    }
};

const goToPage = (page) => {
    loadRepairRequests(page);
};

const getPriorityBadgeClass = (priority) => {
    const classes = {
        low: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        medium: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        high: "bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400",
        critical: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return classes[priority] || classes.medium;
};

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        approved: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        in_progress: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        completed: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        rejected: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return classes[status] || classes.pending;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "issue":
            if (!value || value.trim() === "") {
                errors.push("Issue title is required");
            } else if (value.length < 3) {
                errors.push("Issue title must be at least 3 characters");
            } else if (value.length > 255) {
                errors.push("Issue title must be less than 255 characters");
            }
            break;
        case "priority":
            if (!value) {
                errors.push("Priority is required");
            }
            break;
        case "asset_id":
            if (!value) {
                errors.push("Asset is required");
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(requestForm.value).forEach((field) => {
        const fieldErrors = validateField(field, requestForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, requestForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

const saveRequest = async () => {
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "repair-requests"
            : `repair-requests/${selectedRequest.value.id}`;

        const response = showCreateModal.value
            ? await apiPost(url, requestForm.value)
            : await apiPut(url, requestForm.value);

        if (response.success) {
            notification.success(
                `Repair request ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            await Promise.all([loadRepairRequests(), loadStatistics()]);
        } else {
            if (response.errors) {
                formErrors.value = response.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(response.message || "Failed to save repair request");
            }
        }
    } catch (error) {
        console.error("Save request error:", error);
        notification.error("Failed to save repair request. Please try again.");
    } finally {
        saving.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    selectedRequest.value = null;
    requestForm.value = {
        issue: "",
        code: "",
        description: "",
        priority: "",
        status: "pending",
        asset_id: "",
        location_id: "",
        notes: "",
    };
    formErrors.value = {};
    formTouched.value = {};
};

const handleAddRequest = () => {
    showCreateModal.value = true;
    loadOptions();
};

const handleEditRequest = (request) => {
    selectedRequest.value = request;
    requestForm.value = {
        issue: request.issue,
        code: request.code,
        description: request.description || "",
        priority: request.priority,
        status: request.status,
        asset_id: request.asset_id || "",
        location_id: request.location_id || "",
        notes: request.notes || "",
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
    loadOptions();
};

const handleViewRequest = (request) => {
    selectedRequest.value = request;
    showViewModal.value = true;
};

const handleDeleteRequest = (request) => {
    confirmationModal.showModal({
        title: "Delete Repair Request",
        message: `Are you sure you want to delete "${request.issue}"?`,
        description:
            "This action cannot be undone. This will permanently delete the repair request.",
        confirmText: "Delete Request",
        cancelText: "Cancel",
        onConfirm: async () => {
            const response = await apiDelete(`repair-requests/${request.id}`);
            if (response.success) {
                notification.success("Repair request deleted successfully");
                await Promise.all([loadRepairRequests(), loadStatistics()]);
                return response;
            } else {
                throw new Error(response.message || "Failed to delete repair request");
            }
        },
        onSuccess: (result) => {
            console.log("Request deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete request:", error);
            notification.error(error.message || "Failed to delete repair request");
        },
    });
};

const handleBulkAction = (action, selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select repair requests to perform bulk action");
        return;
    }

    if (action === "delete") {
        confirmationModal.showModal({
            title: "Bulk Delete Repair Requests",
            message: `Are you sure you want to delete ${
                selectedItems.length
            } repair request${selectedItems.length === 1 ? "" : "s"}?`,
            description:
                "This action cannot be undone. This will permanently delete the selected repair requests.",
            confirmText: "Delete Requests",
            cancelText: "Cancel",
            onConfirm: async () => {
                try {
                    const deletePromises = selectedItems.map((item) =>
                        apiDelete(`repair-requests/${item.id}`)
                    );
                    await Promise.all(deletePromises);
                    notification.success(
                        `${selectedItems.length} repair request${
                            selectedItems.length === 1 ? "" : "s"
                        } deleted successfully`
                    );
                    await Promise.all([loadRepairRequests(), loadStatistics()]);
                    return { success: true };
                } catch (error) {
                    throw new Error("Failed to delete some repair requests");
                }
            },
            onSuccess: (result) => {
                console.log("Bulk delete completed:", result);
            },
            onError: (error) => {
                console.error("Bulk delete failed:", error);
                notification.error(error.message || "Failed to delete repair requests");
            },
        });
    }
};

const handleSelectionChange = (selectedItems) => {
    selectedRequests.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const handleExportRequests = async () => {
    try {
        const params = new URLSearchParams(filters.value);
        const link = document.createElement("a");
        link.href = `/api/repair-requests-export?${params}`;
        link.download = `repair_requests_${new Date().toISOString().split("T")[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        notification.success(
            "Repair requests export started. Download will begin shortly."
        );
    } catch (error) {
        console.error("Export error:", error);
        notification.error("Failed to export repair requests");
    }
};

const loadRepairRequests = async (page = 1) => {
    try {
        loading.value = true;
        const params = new URLSearchParams({
            page: page.toString(),
            ...filters.value,
        });

        const response = await apiGet(`repair-requests?${params}`);
        if (response.success) {
            repairRequests.value = response.data;
        }
    } catch (error) {
        console.error("Error loading repair requests:", error);
        notification.error("Failed to load repair requests");
    } finally {
        loading.value = false;
    }
};

const loadStatistics = async () => {
    try {
        const response = await apiGet("repair-requests-statistics");
        if (response.success) {
            statistics.value = response.data;
        }
    } catch (error) {
        console.error("Error loading statistics:", error);
        notification.error("Failed to load statistics");
    }
};

const loadOptions = async () => {
    try {
        // Load users
        const usersResponse = await apiGet("work-orders-user-options");
        if (usersResponse.success) {
            userOptions.value = usersResponse.data;
        }

        // Load assets
        const assetsResponse = await apiGet("work-orders-asset-options");
        if (assetsResponse.success) {
            assetOptions.value = assetsResponse.data;
        }

        // Load locations
        const locationsResponse = await apiGet("work-orders-location-options");
        if (locationsResponse.success) {
            locationOptions.value = locationsResponse.data;
        }
    } catch (error) {
        console.error("Error loading options:", error);
        notification.error("Failed to load form options");
    }
};

// Lifecycle
onMounted(async () => {
    // Check for prefill data from QR code scan
    if (route.query.action === 'create') {
        // Load options first before prefilling
        await loadOptions();

        // Prefill form with data from query params
        if (route.query.asset_id) {
            requestForm.value.asset_id = parseInt(route.query.asset_id);
        }
        if (route.query.location_id) {
            requestForm.value.location_id = parseInt(route.query.location_id);
        }
        if (route.query.asset_name) {
            // Set issue with asset name
            requestForm.value.issue = `Repair needed for ${route.query.asset_name}`;
        }
        if (route.query.asset_code) {
            // Add asset code to description
            requestForm.value.description = `Asset Code: ${route.query.asset_code}\n\nIssue Description:\n`;
        }

        // Auto-open create modal
        showCreateModal.value = true;

        notification.info('Form has been pre-filled with asset information from QR code');
    }

    loadRepairRequests();
    loadStatistics();
    if (!route.query.action) {
        loadOptions();
    }
});
</script>
