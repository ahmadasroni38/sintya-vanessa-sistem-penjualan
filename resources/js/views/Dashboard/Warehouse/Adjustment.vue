<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader
            title="Stock Adjustment"
            description="Manage stock adjustments and corrections"
        >
            <template #actions>
                <!-- <button
                    @click="exportData"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Export
                </button> -->
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
        >
            <template #column-total_items="{ value }">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                >
                    {{ value || 0 }} product(s)
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

        <!-- Approve Confirmation Modal -->
        <ConfirmationModal
            :is-open="showApproveModal"
            title="Approve Adjustment"
            :message="
                approvingAdjustment
                    ? `Are you sure you want to approve adjustment ${approvingAdjustment.adjustment_number}? This will finalize the stock adjustment and create stock card entries.`
                    : 'Are you sure you want to approve this adjustment?'
            "
            confirm-text="Approve Adjustment"
            cancel-text="Cancel"
            :loading="approving"
            @confirm="confirmApprove"
            @cancel="showApproveModal = false"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showDeleteModal"
            title="Delete Adjustment"
            message="Are you sure you want to delete this stock adjustment? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            :loading="deleting"
            @confirm="confirmDelete"
            @cancel="showDeleteModal = false"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useNotificationStore } from "../../../stores/notification";
import DataTable from "../../../components/UI/DataTable.vue";
import ConfirmationModal from "../../../components/Overlays/ConfirmationModal.vue";
import PageHeader from "../../../components/Warehouse/PageHeader.vue";
import AdjustmentStats from "../../../components/Warehouse/AdjustmentStats.vue";
import AdjustmentFormModal from "../../../components/Warehouse/AdjustmentFormModal.vue";
import AdjustmentDetails from "../../../components/Warehouse/AdjustmentDetails.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    PencilIcon,
    CheckIcon,
    TrashIcon,
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
const adjustmentList = ref([]);
const products = ref([]);
const locations = ref([]);
const showAddModal = ref(false);
const editingAdjustment = ref(null);
const showDeleteModal = ref(false);
const deletingAdjustment = ref(null);
const deleting = ref(false);
const showApproveModal = ref(false);
const approvingAdjustment = ref(null);
const approving = ref(false);
const showDetailsModal = ref(false);
const selectedAdjustment = ref(null);

// Table columns - Updated for Master-Detail structure
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
        key: "location.name",
        label: "Location",
        sortable: true,
    },
    {
        key: "total_items",
        label: "Total Items",
        sortable: true,
    },
    {
        key: "description",
        label: "Description",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
    },
];

// Methods
const loadAdjustments = async () => {
    loading.value = true;
    try {
        const response = await stockAdjustmentService.getAll();
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

const editAdjustment = async (adjustment) => {
    try {
        console.log(
            "[Edit] Fetching full adjustment data for ID:",
            adjustment.id
        );

        // Fetch full adjustment data with details
        const response = await stockAdjustmentService.getById(adjustment.id);
        const fullAdjustment = response.data || response;

        console.log("[Edit] Full adjustment data:", fullAdjustment);

        // Set to editing state with full data including details
        editingAdjustment.value = fullAdjustment;
    } catch (error) {
        console.error("Failed to load adjustment for edit:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to load adjustment data"
        );
    }
};

const deleteAdjustment = (adjustment) => {
    deletingAdjustment.value = adjustment;
    showDeleteModal.value = true;
};

const viewDetails = async (adjustment) => {
    try {
        console.log("Fetching details for adjustment ID:", adjustment.id);
        const response = await stockAdjustmentService.getById(adjustment.id);
        console.log("API Response:", response);

        // Extract data from response
        const adjustmentData = response.data || response;
        console.log("Adjustment Data:", adjustmentData);

        selectedAdjustment.value = adjustmentData;
        showDetailsModal.value = true;

        console.log(
            "Modal should be visible now, showDetailsModal:",
            showDetailsModal.value
        );
    } catch (error) {
        console.error("Failed to load adjustment details:", error);
        console.error("Error details:", error.response?.data);
        notificationStore.error(
            error.response?.data?.message || "Failed to load adjustment details"
        );
    }
};

const approveAdjustment = (adjustment) => {
    approvingAdjustment.value = adjustment;
    showApproveModal.value = true;
};

const confirmApprove = async () => {
    if (!approvingAdjustment.value) return;

    approving.value = true;
    try {
        await stockAdjustmentService.approve(approvingAdjustment.value.id);
        notificationStore.success("Adjustment approved successfully");
        showApproveModal.value = false;
        approvingAdjustment.value = null;
        await loadAdjustments();
    } catch (error) {
        console.error("Failed to approve adjustment:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to approve adjustment"
        );
    } finally {
        approving.value = false;
    }
};

const confirmDelete = async () => {
    if (!deletingAdjustment.value) return;

    deleting.value = true;
    try {
        await stockAdjustmentService.delete(deletingAdjustment.value.id);
        notificationStore.success("Adjustment deleted successfully");
        showDeleteModal.value = false;
        deletingAdjustment.value = null;
        await loadAdjustments();
    } catch (error) {
        console.error("Failed to delete adjustment:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to delete adjustment"
        );
    } finally {
        deleting.value = false;
        if (showDeleteModal.value) {
            showDeleteModal.value = false;
            deletingAdjustment.value = null;
        }
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

const closeModal = () => {
    showAddModal.value = false;
    editingAdjustment.value = null;
    showApproveModal.value = false;
    approvingAdjustment.value = null;
    approving.value = false;
    showDeleteModal.value = false;
    deletingAdjustment.value = null;
    deleting.value = false;
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

const exportData = async () => {
    try {
        const blob = await stockAdjustmentService.export();
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
        console.error("Failed to export adjustments:", error);
        notificationStore.error("Failed to export data");
    }
};

onMounted(async () => {
    await Promise.all([loadAdjustments(), loadProducts(), loadLocations()]);
});
</script>
