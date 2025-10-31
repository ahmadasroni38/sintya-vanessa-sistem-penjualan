<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader
            title="Stock Adjustment"
            description="Manage stock adjustments and corrections"
        >
            <template #actions>
                <button
                    @click="toggleFilters"
                    :class="[
                        'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
                        showFilters
                            ? 'text-white bg-blue-600 hover:bg-blue-700'
                            : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600',
                    ]"
                >
                    <FunnelIcon class="w-4 h-4" />
                    Filters
                    <span
                        v-if="hasActiveFilters"
                        class="px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full"
                    >
                        {{ activeFilterCount }}
                    </span>
                </button>
                <button
                    @click="handleExport"
                    :disabled="exporting"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    {{ exporting ? "Exporting..." : "Export" }}
                </button>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <PlusIcon class="w-4 h-4" />
                    New Adjustment
                </button>
            </template>
        </PageHeader>

        <!-- Stats Cards -->
        <AdjustmentStats :adjustments="adjustmentList" />

        <!-- Filters -->
        <AdjustmentFilters
            v-if="showFilters"
            :filters="filters"
            :products="products"
            :locations="locations"
            @update:filters="handleFiltersUpdate"
        />

        <!-- Adjustments Table -->
        <DataTable
            title="Stock Adjustments"
            :data="adjustmentList"
            :columns="columns"
            :loading="loading"
            @refresh="loadAdjustments"
            :show-refresh="true"
            :refresh-loading="refreshing"
            search-placeholder="Search adjustments..."
            :showAddButton="false"
            :showExport="false"
            :showFilters="false"
            :server-side-pagination="true"
            :selectable="true"
            @selection-change="handleSelectionChange"
        >
            <template #column-adjustment_type="{ value }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        value === 'increase'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                    ]"
                >
                    <ArrowTrendingUpIcon
                        v-if="value === 'increase'"
                        class="w-3 h-3 mr-1"
                    />
                    <ArrowTrendingDownIcon v-else class="w-3 h-3 mr-1" />
                    {{ value }}
                </span>
            </template>

            <template #column-difference_quantity="{ value }">
                <span
                    :class="[
                        'font-semibold',
                        value > 0
                            ? 'text-green-600 dark:text-green-400'
                            : value < 0
                            ? 'text-red-600 dark:text-red-400'
                            : 'text-gray-900 dark:text-white',
                    ]"
                >
                    {{ value > 0 ? "+" : "" }}{{ value }}
                </span>
            </template>

            <template #column-status="{ value }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getStatusBadgeClass(value),
                    ]"
                >
                    {{ value }}
                </span>
            </template>

            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="viewDetails(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="editAdjustment(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="Edit"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="approveAdjustment(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Approve"
                    >
                        <CheckIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="deleteAdjustment(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Bulk Actions Bar -->
        <BulkActionsBar
            :selected-items="selectedItems"
            :processing="bulkProcessing"
            @clear-selection="clearSelection"
            @bulk-approve="handleBulkApprove"
            @bulk-delete="handleBulkDelete"
            @bulk-export="handleBulkExport"
        />

        <!-- Add/Edit Adjustment Modal -->
        <AdjustmentFormModal
            :show="showAddModal || !!editingAdjustment"
            :adjustment="editingAdjustment"
            :products="products"
            :locations="locations"
            :saving="saving"
            @close="closeModal"
            @submit="saveAdjustment"
            @get-system-quantity="handleGetSystemQuantity"
        />

        <!-- Details Modal -->
        <AdjustmentDetails
            :show="showDetailsModal"
            :adjustment="selectedAdjustment"
            @close="showDetailsModal = false"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            v-if="showDeleteModal"
            title="Delete Adjustment"
            message="Are you sure you want to delete this stock adjustment? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            @confirm="confirmDelete"
            @cancel="showDeleteModal = false"
        />

        <!-- Bulk Delete Confirmation Modal -->
        <ConfirmationModal
            v-if="showBulkDeleteModal"
            title="Delete Multiple Adjustments"
            :message="`Are you sure you want to delete ${selectedItems.length} adjustment(s)? This action cannot be undone.`"
            confirm-text="Delete All"
            cancel-text="Cancel"
            @confirm="confirmBulkDelete"
            @cancel="showBulkDeleteModal = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useNotificationStore } from "../../../stores/notification";
import DataTable from "../../../components/UI/DataTable.vue";
import ConfirmationModal from "../../../components/Overlays/ConfirmationModal.vue";
import PageHeader from "../../../components/Warehouse/PageHeader.vue";
import AdjustmentStats from "../../../components/Warehouse/AdjustmentStats.vue";
import AdjustmentFormModal from "../../../components/Warehouse/AdjustmentFormModal.vue";
import AdjustmentDetails from "../../../components/Warehouse/AdjustmentDetails.vue";
import AdjustmentFilters from "../../../components/Warehouse/AdjustmentFilters.vue";
import BulkActionsBar from "../../../components/Warehouse/BulkActionsBar.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    EyeIcon,
    PencilIcon,
    CheckIcon,
    TrashIcon,
    FunnelIcon,
} from "@heroicons/vue/24/outline";
import {
    stockAdjustmentService,
    productService,
    locationService,
} from "../../../services/warehouseService";

const notificationStore = useNotificationStore();

// State
const loading = ref(false);
const refreshing = ref(false);
const saving = ref(false);
const exporting = ref(false);
const bulkProcessing = ref(false);
const adjustmentList = ref([]);
const products = ref([]);
const locations = ref([]);
const showAddModal = ref(false);
const editingAdjustment = ref(null);
const showDeleteModal = ref(false);
const deletingAdjustment = ref(null);
const showDetailsModal = ref(false);
const selectedAdjustment = ref(null);
const showFilters = ref(false);
const showBulkDeleteModal = ref(false);
const selectedItems = ref([]);

// Filters
const filters = ref({
    status: "",
    adjustment_type: "",
    location_id: "",
    product_id: "",
    start_date: "",
    end_date: "",
    search: "",
});

// Table columns
const columns = [
    {
        key: "adjustment_number",
        label: "Number",
        sortable: true,
    },
    {
        key: "adjustment_date",
        label: "Date",
        sortable: true,
        type: "date",
    },
    {
        key: "product.product_name",
        label: "Product",
        sortable: true,
    },
    {
        key: "location.name",
        label: "Location",
        sortable: true,
    },
    {
        key: "adjustment_type",
        label: "Type",
        sortable: true,
    },
    {
        key: "difference_quantity",
        label: "Difference",
        sortable: true,
    },
    {
        key: "reason",
        label: "Reason",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
    },
];

// Computed
const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some((value) => value !== "");
});

const activeFilterCount = computed(() => {
    return Object.values(filters.value).filter((value) => value !== "").length;
});

// Methods
const loadAdjustments = async () => {
    loading.value = true;
    try {
        const params = { ...filters.value };
        const response = await stockAdjustmentService.getAll(params);
        adjustmentList.value = Array.isArray(response)
            ? response
            : response.data || [];
    } catch (error) {
        console.error("Failed to load adjustments:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to load adjustment data"
        );
    } finally {
        loading.value = false;
    }
};

const loadProducts = async () => {
    try {
        const response = await productService.getAll();
        products.value = Array.isArray(response)
            ? response
            : response.data || [];
    } catch (error) {
        console.error("Failed to load products:", error);
    }
};

const loadLocations = async () => {
    try {
        const response = await locationService.getAll();
        locations.value = Array.isArray(response)
            ? response
            : response.data || [];
    } catch (error) {
        console.error("Failed to load locations:", error);
    }
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

const handleFiltersUpdate = (newFilters) => {
    filters.value = { ...newFilters };
    loadAdjustments();
};

const handleSelectionChange = (items) => {
    selectedItems.value = items;
};

const clearSelection = () => {
    selectedItems.value = [];
};

const editAdjustment = (adjustment) => {
    editingAdjustment.value = adjustment;
};

const deleteAdjustment = (adjustment) => {
    deletingAdjustment.value = adjustment;
    showDeleteModal.value = true;
};

const viewDetails = async (adjustment) => {
    try {
        const response = await stockAdjustmentService.getById(adjustment.id);
        selectedAdjustment.value = response.data || response;
        showDetailsModal.value = true;
    } catch (error) {
        console.error("Failed to load adjustment details:", error);
        notificationStore.error("Failed to load adjustment details");
    }
};

const approveAdjustment = async (adjustment) => {
    try {
        await stockAdjustmentService.approve(adjustment.id);
        notificationStore.success("Adjustment approved successfully");
        await loadAdjustments();
    } catch (error) {
        console.error("Failed to approve adjustment:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to approve adjustment"
        );
    }
};

const confirmDelete = async () => {
    try {
        await stockAdjustmentService.delete(deletingAdjustment.value.id);
        notificationStore.success("Adjustment deleted successfully");
        await loadAdjustments();
    } catch (error) {
        console.error("Failed to delete adjustment:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to delete adjustment"
        );
    } finally {
        showDeleteModal.value = false;
        deletingAdjustment.value = null;
    }
};

const saveAdjustment = async (formData) => {
    saving.value = true;
    try {
        if (editingAdjustment.value) {
            await stockAdjustmentService.update(
                editingAdjustment.value.id,
                formData
            );
            notificationStore.success("Adjustment updated successfully");
        } else {
            await stockAdjustmentService.create(formData);
            notificationStore.success("Adjustment created successfully");
        }
        closeModal();
        await loadAdjustments();
    } catch (error) {
        console.error("Failed to save adjustment:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to save adjustment"
        );
    } finally {
        saving.value = false;
    }
};

const handleGetSystemQuantity = async ({ productId, locationId, callback }) => {
    try {
        const response = await stockAdjustmentService.calculateSystemQuantity(
            productId,
            locationId
        );
        const quantity = response.system_quantity || 0;
        callback(quantity);
    } catch (error) {
        console.error("Failed to get system quantity:", error);
        notificationStore.error("Failed to get system quantity");
        callback(0);
    }
};

const handleExport = async () => {
    exporting.value = true;
    try {
        const params = { ...filters.value };
        const blob = await stockAdjustmentService.export(params);

        // Create download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = `stock_adjustments_${
            new Date().toISOString().split("T")[0]
        }.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        notificationStore.success("Export completed successfully");
    } catch (error) {
        console.error("Failed to export:", error);
        notificationStore.error("Failed to export data");
    } finally {
        exporting.value = false;
    }
};

const handleBulkApprove = async () => {
    bulkProcessing.value = true;
    try {
        const ids = selectedItems.value.map((item) => item.id);
        const response = await stockAdjustmentService.bulkApprove(ids);

        if (response.failed && response.failed.length > 0) {
            notificationStore.warning(
                `${response.approved} approved, ${response.failed.length} failed`
            );
        } else {
            notificationStore.success(response.message);
        }

        clearSelection();
        await loadAdjustments();
    } catch (error) {
        console.error("Failed to bulk approve:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to approve adjustments"
        );
    } finally {
        bulkProcessing.value = false;
    }
};

const handleBulkDelete = () => {
    showBulkDeleteModal.value = true;
};

const confirmBulkDelete = async () => {
    bulkProcessing.value = true;
    try {
        const ids = selectedItems.value.map((item) => item.id);
        const response = await stockAdjustmentService.bulkDelete(ids);

        if (response.failed && response.failed.length > 0) {
            notificationStore.warning(
                `${response.deleted} deleted, ${response.failed.length} failed`
            );
        } else {
            notificationStore.success(response.message);
        }

        clearSelection();
        await loadAdjustments();
    } catch (error) {
        console.error("Failed to bulk delete:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to delete adjustments"
        );
    } finally {
        bulkProcessing.value = false;
        showBulkDeleteModal.value = false;
    }
};

const handleBulkExport = async () => {
    exporting.value = true;
    try {
        const ids = selectedItems.value.map((item) => item.id);
        const blob = await stockAdjustmentService.export({ ids });

        // Create download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = `stock_adjustments_selected_${
            new Date().toISOString().split("T")[0]
        }.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        notificationStore.success("Export completed successfully");
    } catch (error) {
        console.error("Failed to export:", error);
        notificationStore.error("Failed to export selected items");
    } finally {
        exporting.value = false;
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingAdjustment.value = null;
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        posted: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        cancelled:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return (
        classes[status] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

onMounted(async () => {
    await Promise.all([loadAdjustments(), loadProducts(), loadLocations()]);
});
</script>
