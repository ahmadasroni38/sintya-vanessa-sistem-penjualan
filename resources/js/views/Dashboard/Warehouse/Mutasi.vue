<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Stock Mutation
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Manage stock transfers between locations
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="toggleFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <FunnelIcon v-if="!showFilters" class="w-4 h-4" />
                    <XMarkIcon v-else class="w-4 h-4" />
                    {{ showFilters ? "Hide" : "Show" }} Filters
                </button>
                <button
                    @click="exportData"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Export
                </button>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <PlusIcon class="w-4 h-4" />
                    New Mutation
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <StockMutationStats :statistics="statistics" />

        <!-- Filters -->
        <Transition
            name="filter-slide"
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="transform -translate-y-4 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-4 opacity-0"
        >
            <div v-show="showFilters">
                <StockMutationFilters
                    :locations="options?.locations || []"
                    :products="options?.products || []"
                    :loading-products="loading"
                    @filter-change="handleFilterChange"
                />
            </div>
        </Transition>

        <!-- Mutations Table -->
        <DataTable
            title="Stock Mutations"
            :data="stockMutations"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            @refresh="loadMutations"
            @page-change="handlePageChange"
            @sort-change="handleSortChange"
            :show-refresh="true"
            :refresh-loading="refreshing"
            search-placeholder="Search mutations..."
            :showAddButton="false"
            :showExport="false"
            :showFilters="false"
            :server-side-pagination="true"
        >
            <template #column-transaction_number="{ value }">
                <span class="font-mono text-sm">{{ value }}</span>
            </template>

            <template #column-transaction_date="{ value }">
                {{ formatDate(value) }}
            </template>

            <template #column-from_location_name="{ value }">
                <div class="flex items-center">
                    <MapPinIcon class="w-4 h-4 text-gray-400 mr-2" />
                    {{ value }}
                </div>
            </template>

            <template #column-to_location_name="{ value }">
                <div class="flex items-center">
                    <MapPinIcon class="w-4 h-4 text-gray-400 mr-2" />
                    {{ value }}
                </div>
            </template>

            <template #column-status="{ value }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getStatusClass(value),
                    ]"
                >
                    {{ getStatusLabel(value) }}
                </span>
            </template>

            <template #column-details_count="{ value }">
                <span class="text-sm font-medium">{{ value }} item(s)</span>
            </template>

            <template #column-created_at="{ value }">
                {{ formatDateTime(value) }}
            </template>

            <template #actions="{ item }">
                <StockMutationActions
                    :item="item"
                    @view="viewDetails"
                    @edit="editMutation"
                    @submit="submitMutation"
                    @approve="approveMutation"
                    @complete="completeMutation"
                    @cancel="cancelMutation"
                    @delete="deleteMutation"
                />
            </template>
        </DataTable>

        <!-- Add/Edit Mutation Modal -->
        <Modal
            :is-open="showAddModal || !!editingMutation"
            :title="editingMutation ? 'Edit Mutasi Stok' : 'Mutasi Stok Baru'"
            size="6xl"
            @close="closeModal"
        >
            <div
                v-if="loadingEdit"
                class="flex items-center justify-center py-12"
            >
                <div class="flex items-center space-x-3">
                    <div
                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                    ></div>
                    <span class="text-gray-600 dark:text-gray-400"
                        >Loading mutation data...</span
                    >
                </div>
            </div>
            <StockMutationFormModal
                v-else
                :is-open="true"
                :editing-item="editingMutation"
                :location-options="locationOptions"
                :products="products"
                :loading-products="loadingProducts"
                :saving="saving"
                :errors="errors"
                @close="closeModal"
                @save="saveMutation"
                @from-location-changed="handleFromLocationChange"
            />
        </Modal>

        <!-- Details Modal -->
        <Modal
            :is-open="showDetailsModal"
            title="Mutation Details"
            size="large"
            @close="closeDetailsModal"
        >
            <div
                v-if="loadingDetails"
                class="flex items-center justify-center py-12"
            >
                <div class="flex items-center space-x-3">
                    <div
                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                    ></div>
                    <span class="text-gray-600 dark:text-gray-400"
                        >Loading mutation details...</span
                    >
                </div>
            </div>
            <StockMutationDetails
                v-else-if="selectedMutation"
                :mutation="selectedMutation"
                :products="products"
            />
            <div v-else class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div class="text-gray-400 mb-2">
                        <svg
                            class="mx-auto h-12 w-12"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-.98-5.5-2.5m.5-4H7a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5M12 7v4"
                            />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">
                        No mutation data available
                    </p>
                </div>
            </div>
        </Modal>

        <!-- Submit Confirmation Modal -->
        <ConfirmationModal
            :is-open="showSubmitModal"
            title="Submit for Approval"
            :message="
                submittingMutation
                    ? `Are you sure you want to submit mutation ${submittingMutation.transaction_number} for approval? This will validate stock availability and change the status to pending.`
                    : 'Are you sure you want to submit this mutation for approval?'
            "
            confirm-text="Submit for Approval"
            cancel-text="Cancel"
            :loading="submitting"
            @confirm="confirmSubmitMutation"
            @cancel="showSubmitModal = false"
        />

        <!-- Approve Confirmation Modal -->
        <ConfirmationModal
            :is-open="showApproveModal"
            title="Approve Mutation"
            :message="
                approvingMutation
                    ? `Are you sure you want to approve mutation ${approvingMutation.transaction_number}? This will validate stock availability and change the status to approved.`
                    : 'Are you sure you want to approve this mutation?'
            "
            confirm-text="Approve Mutation"
            cancel-text="Cancel"
            :loading="approving"
            @confirm="confirmApproveMutation"
            @cancel="showApproveModal = false"
        />

        <!-- Complete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showCompleteModal"
            title="Complete Mutation"
            :message="
                completingMutation
                    ? `Are you sure you want to complete mutation ${completingMutation.transaction_number}? This will finalize the stock transfer and update inventory.`
                    : 'Are you sure you want to complete this mutation?'
            "
            confirm-text="Complete Mutation"
            cancel-text="Cancel"
            :loading="completing"
            @confirm="confirmCompleteMutation"
            @cancel="showCompleteModal = false"
        />

        <!-- Cancel Confirmation Modal -->
        <ConfirmationModal
            :is-open="showCancelModal"
            title="Cancel Mutation"
            :message="
                cancellingMutation
                    ? `Are you sure you want to cancel mutation ${cancellingMutation.transaction_number}? This will revert the mutation status and cannot be undone.`
                    : 'Are you sure you want to cancel this mutation?'
            "
            confirm-text="Cancel Mutation"
            cancel-text="Cancel"
            :loading="cancelling"
            @confirm="confirmCancelMutation"
            @cancel="showCancelModal = false"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showDeleteModal"
            title="Delete Mutation"
            message="Are you sure you want to delete this stock mutation? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            :loading="deleting"
            @confirm="confirmDelete"
            @cancel="showDeleteModal = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useNotificationStore } from "@/stores/notification";
import { useStockMutation } from "@/composables/useStockMutation";
import DataTable from "@/components/UI/DataTable.vue";
import Modal from "@/components/Overlays/Modal.vue";
import ConfirmationModal from "@/components/Overlays/ConfirmationModal.vue";
import StockMutationStats from "@/components/Warehouse/StockMutationStats.vue";
import StockMutationFilters from "@/components/Warehouse/StockMutationFilters.vue";
import StockMutationFormModal from "@/components/Warehouse/StockMutationFormModal.vue";
import StockMutationActions from "@/components/Warehouse/StockMutationActions.vue";
import StockMutationDetails from "@/components/Warehouse/StockMutationDetails.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    MapPinIcon,
    FunnelIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";

const notificationStore = useNotificationStore();

// Use composable
const {
    stockMutations,
    loading,
    pagination,
    statistics,
    options,
    fetchStockMutations,
    fetchStockMutation,
    createStockMutation,
    updateStockMutation,
    deleteStockMutation,
    submitStockMutation,
    approveStockMutation,
    completeStockMutation,
    cancelStockMutation,
    fetchStatistics,
    fetchOptions,
    fetchLocations,
    getStatusLabel,
    getStatusClass,
    formatNumber,
    formatDate,
    formatDateTime,
} = useStockMutation();

// State
const refreshing = ref(false);
const saving = ref(false);
const showAddModal = ref(false);
const editingMutation = ref(null);
const showDeleteModal = ref(false);
const deletingMutation = ref(null);
const deleting = ref(false);
const showSubmitModal = ref(false);
const submittingMutation = ref(null);
const submitting = ref(false);
const showApproveModal = ref(false);
const approvingMutation = ref(null);
const approving = ref(false);
const showCompleteModal = ref(false);
const completingMutation = ref(null);
const completing = ref(false);
const showCancelModal = ref(false);
const cancellingMutation = ref(null);
const cancelling = ref(false);
const showDetailsModal = ref(false);
const selectedMutation = ref(null);
const loadingDetails = ref(false);
const loadingEdit = ref(false);
const errors = ref({});
const products = ref([]);
const loadingProducts = ref(false);
const showFilters = ref(false);

// Filters
const filters = ref({
    search: "",
    status: "",
    from_location_id: "",
    to_location_id: "",
    product_id: "",
    start_date: "",
    end_date: "",
    per_page: 15,
    page: 1,
    sort_by: "transaction_date",
    sort_order: "desc",
});

// Computed
const locationOptions = computed(() => {
    return options.value?.locations
        ? options.value?.locations.map((loc) => ({
              value: loc.id,
              label: `${loc.code} - ${loc.name}`,
          }))
        : [];
});

// Table columns
const columns = [
    {
        key: "transaction_number",
        label: "Transaction Number",
        sortable: true,
    },
    {
        key: "transaction_date",
        label: "Date",
        sortable: true,
        type: "date",
    },
    {
        key: "from_location_name",
        label: "From",
        sortable: true,
    },
    {
        key: "to_location_name",
        label: "To",
        sortable: true,
    },
    {
        key: "details_count",
        label: "Items",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
        type: "badge",
    },
    {
        key: "created_by",
        label: "Created By",
        sortable: true,
    },
    {
        key: "created_at",
        label: "Created At",
        sortable: true,
        type: "date",
    },
];

// Methods
const loadMutations = async () => {
    try {
        await fetchStockMutations(filters.value);
    } catch (error) {
        console.error("Error loading mutations:", error);
        notificationStore.error("Failed to load mutation data");
    }
};

const refreshMutations = async () => {
    refreshing.value = true;
    try {
        await loadMutations();
        await loadStatistics();
    } catch (error) {
        notificationStore.error("Failed to refresh mutation data");
    } finally {
        refreshing.value = false;
    }
};

const loadStatistics = async () => {
    try {
        const result = await fetchStatistics();
        console.log("Statistics loaded in component:", result);
        console.log("Current statistics value:", statistics.value);
    } catch (error) {
        console.error("Failed to load statistics:", error);
    }
};

const loadOptions = async () => {
    try {
        await fetchOptions();
    } catch (error) {
        console.error("Failed to load options:", error);
    }
};

const handleFilterChange = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters, page: 1 };
    loadMutations();
};

const handlePageChange = (page) => {
    filters.value.page = page;
    loadMutations();
};

const handleSortChange = ({ sortBy, sortOrder }) => {
    filters.value.sort_by = sortBy;
    filters.value.sort_order = sortOrder;
    filters.value.page = 1;
    loadMutations();
};

const handleFromLocationChange = async (locationId) => {
    if (locationId) {
        loadingProducts.value = true;
        try {
            const response = await fetchOptions({
                from_location_id: locationId,
            });
            products.value = response.products || [];
        } catch (error) {
            console.error("Failed to load products with stock:", error);
        } finally {
            loadingProducts.value = false;
        }
    } else {
        products.value = options.value?.products || [];
    }
};

const viewDetails = async (mutation) => {
    showDetailsModal.value = true;
    loadingDetails.value = true;
    selectedMutation.value = null;

    try {
        selectedMutation.value = await fetchStockMutation(mutation.id);
        // Success - data loaded and modal remains open
    } catch (error) {
        console.error("Error loading mutation details:", error);

        // Check if this is a retryable network error
        const isRetryableError =
            error.code === "NETWORK_ERROR" ||
            error.code === "TIMEOUT" ||
            error.response?.status >= 500 ||
            !error.response; // No response means network issue

        if (isRetryableError) {
            // Implement simple retry for network issues only
            console.warn("Network error detected, attempting retry...");

            try {
                // Wait 1 second then retry once
                await new Promise((resolve) => setTimeout(resolve, 1000));
                selectedMutation.value = await fetchStockMutation(mutation.id);
                // Success on retry
            } catch (retryError) {
                console.error("Retry also failed:", retryError);
                notificationStore.error(
                    "Failed to load mutation details. Please check your connection and try again."
                );
                showDetailsModal.value = false;
            }
        } else {
            // Non-retryable error (like 404, 403, or data validation errors)
            const errorMessage =
                error.response?.data?.message ||
                error.message ||
                "Failed to load mutation details";
            notificationStore.error(errorMessage);
            showDetailsModal.value = false;
        }
    } finally {
        loadingDetails.value = false;
    }
};

const editMutation = async (mutation) => {
    showAddModal.value = true;
    loadingEdit.value = true;

    try {
        selectedMutation.value = await fetchStockMutation(mutation.id);
        editingMutation.value = selectedMutation.value;

        // Load products with stock for from_location
        if (mutation.from_location_id) {
            await handleFromLocationChange(mutation.from_location_id);
        }
    } catch (error) {
        console.error("Error loading mutation for editing:", error);

        // Check if this is a retryable network error
        const isRetryableError =
            error.code === "NETWORK_ERROR" ||
            error.code === "TIMEOUT" ||
            error.response?.status >= 500 ||
            !error.response; // No response means network issue

        if (isRetryableError) {
            // Implement simple retry for network issues only
            console.warn(
                "Network error detected, attempting retry for edit..."
            );

            try {
                // Wait 1 second then retry once
                await new Promise((resolve) => setTimeout(resolve, 1000));
                selectedMutation.value = await fetchStockMutation(mutation.id);
                editingMutation.value = selectedMutation.value;

                // Load products with stock for from_location after successful retry
                if (mutation.from_location_id) {
                    await handleFromLocationChange(mutation.from_location_id);
                }
            } catch (retryError) {
                console.error("Retry also failed for edit:", retryError);
                notificationStore.error(
                    "Failed to load mutation for editing. Please check your connection and try again."
                );
                showAddModal.value = false;
                editingMutation.value = null;
            }
        } else {
            // Non-retryable error (like 404, 403, or data validation errors)
            const errorMessage =
                error.response?.data?.message ||
                error.message ||
                "Failed to load mutation for editing";
            notificationStore.error(errorMessage);
            showAddModal.value = false;
            editingMutation.value = null;
        }
    } finally {
        loadingEdit.value = false;
    }
};

const saveMutation = async (formData) => {
    saving.value = true;
    errors.value = {};

    try {
        if (editingMutation.value) {
            await updateStockMutation(editingMutation.value.id, formData);
            notificationStore.success("Mutation updated successfully");
        } else {
            await createStockMutation(formData);
            notificationStore.success("Mutation created successfully");
        }

        closeModal();
        await loadMutations();
        await loadStatistics();
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            notificationStore.error(
                error.response?.data?.message || "Failed to save mutation"
            );
        }
    } finally {
        saving.value = false;
    }
};

const submitMutation = (mutation) => {
    submittingMutation.value = mutation;
    showSubmitModal.value = true;
};

const confirmSubmitMutation = async () => {
    if (!submittingMutation.value) return;

    submitting.value = true;
    try {
        await submitStockMutation(submittingMutation.value.id);
        notificationStore.success(
            "Mutation submitted successfully for approval"
        );
        showSubmitModal.value = false;
        submittingMutation.value = null;
        await loadMutations();
        await loadStatistics();
    } catch (error) {
        notificationStore.error(
            error.response?.data?.message || "Failed to submit mutation"
        );
    } finally {
        submitting.value = false;
    }
};

const approveMutation = (mutation) => {
    approvingMutation.value = mutation;
    showApproveModal.value = true;
};

const confirmApproveMutation = async () => {
    if (!approvingMutation.value) return;

    approving.value = true;
    try {
        await approveStockMutation(approvingMutation.value.id);
        notificationStore.success("Mutation approved successfully");
        showApproveModal.value = false;
        approvingMutation.value = null;
        await loadMutations();
        await loadStatistics();
    } catch (error) {
        notificationStore.error(
            error.response?.data?.message || "Failed to approve mutation"
        );
    } finally {
        approving.value = false;
    }
};

const completeMutation = (mutation) => {
    completingMutation.value = mutation;
    showCompleteModal.value = true;
};

const confirmCompleteMutation = async () => {
    if (!completingMutation.value) return;

    completing.value = true;
    try {
        await completeStockMutation(completingMutation.value.id);
        notificationStore.success("Mutation completed successfully");
        showCompleteModal.value = false;
        completingMutation.value = null;
        await loadMutations();
        await loadStatistics();
    } catch (error) {
        notificationStore.error(
            error.response?.data?.message || "Failed to complete mutation"
        );
    } finally {
        completing.value = false;
    }
};

const cancelMutation = (mutation) => {
    cancellingMutation.value = mutation;
    showCancelModal.value = true;
};

const confirmCancelMutation = async () => {
    if (!cancellingMutation.value) return;

    cancelling.value = true;
    try {
        await cancelStockMutation(cancellingMutation.value.id);
        notificationStore.success("Mutation cancelled successfully");
        showCancelModal.value = false;
        cancellingMutation.value = null;
        await loadMutations();
        await loadStatistics();
    } catch (error) {
        notificationStore.error(
            error.response?.data?.message || "Failed to cancel mutation"
        );
    } finally {
        cancelling.value = false;
    }
};

const deleteMutation = (mutation) => {
    deletingMutation.value = mutation;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    if (!deletingMutation.value) return;

    deleting.value = true;
    try {
        await deleteStockMutation(deletingMutation.value.id);
        notificationStore.success("Mutation deleted successfully");
        showDeleteModal.value = false;
        deletingMutation.value = null;
        await loadMutations();
        await loadStatistics();
    } catch (error) {
        notificationStore.error(
            error.response?.data?.message || "Failed to delete mutation"
        );
    } finally {
        deleting.value = false;
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingMutation.value = null;
    selectedMutation.value = null;
    loadingEdit.value = false;
    showSubmitModal.value = false;
    submittingMutation.value = null;
    submitting.value = false;
    showApproveModal.value = false;
    approvingMutation.value = null;
    approving.value = false;
    showCompleteModal.value = false;
    completingMutation.value = null;
    completing.value = false;
    showCancelModal.value = false;
    cancellingMutation.value = null;
    cancelling.value = false;
    showDeleteModal.value = false;
    deletingMutation.value = null;
    deleting.value = false;
    errors.value = {};
    products.value = options.value?.products || [];
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedMutation.value = null;
    loadingDetails.value = false;
};

const exportData = () => {
    notificationStore.info("Export feature coming soon");
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

// Initialize
onMounted(async () => {
    await Promise.all([loadMutations(), loadStatistics(), loadOptions()]);
    products.value = options.value?.products || [];
});
</script>
