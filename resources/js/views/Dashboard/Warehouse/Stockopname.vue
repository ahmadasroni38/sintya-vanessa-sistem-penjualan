<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader
            title="Stock Opname"
            description="Manage stock counting and inventory verification"
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
                    New Stock Opname
                </button>
            </template>
        </PageHeader>

        <!-- Stats Cards -->
        <OpnameStats ref="statsRef" />

        <!-- Stock Opnames Table -->
        <DataTable
            title="Stock Opnames"
            :data="opnameList"
            :columns="columns"
            :loading="loading"
            @refresh="loadOpnames"
            :show-refresh="true"
            :refresh-loading="refreshing"
            search-placeholder="Search stock opnames..."
            :showAddButton="false"
            :showExport="false"
            :showFilters="false"
            :server-side-pagination="true"
            :pagination="pagination"
            @page-change="handlePageChange"
            @sort="handleSort"
            @search="handleSearch"
        >
            <template #column-total_items="{ value }">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                >
                    {{ value || 0 }} item(s)
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
                    <!-- View Details -->
                    <button
                        @click="viewDetails(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>

                    <!-- Edit (draft only) -->
                    <button
                        v-if="item.status === 'draft'"
                        @click="editOpname(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="Edit"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>

                    <!-- Start Counting (draft only) -->
                    <button
                        v-if="item.status === 'draft'"
                        @click="startCounting(item)"
                        class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 dark:hover:text-purple-400 dark:hover:bg-purple-900/20"
                        title="Start Counting"
                    >
                        <PlayIcon class="w-4 h-4" />
                    </button>

                    <!-- Complete (in_progress only) -->
                    <button
                        v-if="item.status === 'in_progress'"
                        @click="completeOpname(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Complete Opname"
                    >
                        <CheckIcon class="w-4 h-4" />
                    </button>

                    <!-- Delete (draft only) -->
                    <button
                        v-if="item.status === 'draft'"
                        @click="deleteOpname(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Add/Edit Opname Modal -->
        <OpnameFormModal
            :show="showAddModal || !!editingOpname"
            :opname="editingOpname"
            :products="products"
            :locations="locations"
            :saving="saving"
            @close="closeModal"
            @submit="saveOpname"
            @get-system-quantity="handleGetSystemQuantity"
            @batch-get-system-quantities="handleBatchGetSystemQuantities"
        />

        <!-- Details Modal -->
        <OpnameDetails
            :key="selectedOpname?.id"
            :show="showDetailsModal"
            :opname="selectedOpname"
            @close="showDetailsModal = false"
        />

        <!-- Complete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showCompleteModal"
            title="Complete Stock Opname"
            :message="
                completingOpname
                    ? `Are you sure you want to complete stock opname ${completingOpname.opname_number}? This will create stock adjustments for all differences.`
                    : 'Are you sure you want to complete this opname?'
            "
            confirm-text="Complete Opname"
            cancel-text="Cancel"
            :loading="completing"
            @confirm="confirmComplete"
            @cancel="showCompleteModal = false"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showDeleteModal"
            title="Delete Stock Opname"
            message="Are you sure you want to delete this stock opname? This action cannot be undone."
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
import OpnameStats from "../../../components/Warehouse/OpnameStats.vue";
import OpnameFormModal from "../../../components/Warehouse/OpnameFormModal.vue";
import OpnameDetails from "../../../components/Warehouse/OpnameDetails.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    PencilIcon,
    CheckIcon,
    TrashIcon,
    PlayIcon,
} from "@heroicons/vue/24/outline";
import {
    stockOpnameService,
    productService,
    locationService,
} from "../../../services/warehouseService";

const notificationStore = useNotificationStore();

// Refs
const statsRef = ref(null);

// State
const loading = ref(false);
const refreshing = ref(false);
const saving = ref(false);
const opnameList = ref([]);
const products = ref([]);
const locations = ref([]);
const showAddModal = ref(false);
const editingOpname = ref(null);
const showDeleteModal = ref(false);
const deletingOpname = ref(null);
const deleting = ref(false);
const showCompleteModal = ref(false);
const completingOpname = ref(null);
const completing = ref(false);
const showDetailsModal = ref(false);
const selectedOpname = ref(null);
const pagination = ref({
    current_page: 1,
    per_page: 15,
    total: 0,
    last_page: 1,
});

// Table columns
const columns = [
    {
        key: "opname_number",
        label: "Opname Number",
        sortable: true,
    },
    {
        key: "opname_date",
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
const refreshStats = () => {
    if (statsRef.value) {
        statsRef.value.refresh();
    }
};

const loadOpnames = async (params = {}) => {
    loading.value = true;
    try {
        const response = await stockOpnameService.getAll(params);

        if (response.data) {
            opnameList.value = Array.isArray(response.data.data) ? response.data.data : response.data;
            pagination.value = {
                current_page: response.data.current_page || 1,
                per_page: response.data.per_page || 15,
                total: response.data.total || 0,
                last_page: response.data.last_page || 1,
            };
        } else {
            opnameList.value = Array.isArray(response) ? response : [];
        }

        console.log("[StockOpname] Loaded opnames:", opnameList.value.length);
    } catch (error) {
        console.error("[StockOpname] Failed to load opnames:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to load stock opname data"
        );
    } finally {
        loading.value = false;
    }
};

const handlePageChange = (page, itemsPerPage) => {
    console.log("[StockOpname] handlePageChange", page, itemsPerPage);
    if (itemsPerPage) {
        pagination.value.per_page = itemsPerPage;
    }
    loadOpnames({
        page,
        per_page: itemsPerPage,
    });
};

const handleSort = ({ column, direction }) => {
    console.log("[StockOpname] handleSort", column, direction);
    loadOpnames({
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
        sort_by: column,
        sort_order: direction,
    });
};

const handleSearch = (searchQuery) => {
    console.log("[StockOpname] handleSearch", searchQuery);
    loadOpnames({
        page: 1,
        per_page: pagination.value.per_page,
        search: searchQuery,
    });
};

const loadProducts = async () => {
    try {
        const response = await productService.getAll();
        products.value = Array.isArray(response)
            ? response
            : response.data || [];

        console.log("[StockOpname] Loaded products:", products.value.length);
    } catch (error) {
        console.error("[StockOpname] Failed to load products:", error);
    }
};

const loadLocations = async () => {
    try {
        const response = await locationService.getAll();
        locations.value = Array.isArray(response)
            ? response
            : response.data || [];

        console.log("[StockOpname] Loaded locations:", locations.value.length);
    } catch (error) {
        console.error("[StockOpname] Failed to load locations:", error);
    }
};

const editOpname = async (opname) => {
    try {
        console.log(
            "[StockOpname] Fetching full opname data for ID:",
            opname.id
        );

        // Fetch full opname data with details
        const response = await stockOpnameService.getById(opname.id);
        const fullOpname = response.data || response;

        console.log("[StockOpname] Full opname data:", fullOpname);

        // Set to editing state with full data including details
        editingOpname.value = fullOpname;
    } catch (error) {
        console.error("[StockOpname] Failed to load opname for edit:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to load opname data"
        );
    }
};

const deleteOpname = (opname) => {
    console.log("[StockOpname] Delete opname:", opname);
    deletingOpname.value = opname;
    showDeleteModal.value = true;
};

const viewDetails = async (opname) => {
    try {
        console.log("[StockOpname] Fetching details for opname ID:", opname.id);
        const response = await stockOpnameService.getById(opname.id);

        const opnameData = response.data || response;
        console.log("[StockOpname] Opname data:", opnameData);

        selectedOpname.value = opnameData;
        showDetailsModal.value = true;
    } catch (error) {
        console.error("[StockOpname] Failed to load opname details:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to load opname details"
        );
    }
};

const startCounting = async (opname) => {
    try {
        console.log("[StockOpname] Starting counting for opname:", opname);
        await stockOpnameService.startCounting(opname.id);
        notificationStore.success("Stock counting started successfully");
        await loadOpnames();
        refreshStats();
    } catch (error) {
        console.error("[StockOpname] Failed to start counting:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to start counting"
        );
    }
};

const completeOpname = (opname) => {
    console.log("[StockOpname] Complete opname:", opname);
    completingOpname.value = opname;
    showCompleteModal.value = true;
};

const confirmComplete = async () => {
    if (!completingOpname.value) return;

    completing.value = true;
    try {
        await stockOpnameService.complete(completingOpname.value.id);
        notificationStore.success(
            "Stock opname completed successfully. Adjustment has been created for differences."
        );
        showCompleteModal.value = false;
        completingOpname.value = null;
        await loadOpnames();
        refreshStats();
    } catch (error) {
        console.error("[StockOpname] Failed to complete opname:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to complete opname"
        );
    } finally {
        completing.value = false;
    }
};

const confirmDelete = async () => {
    if (!deletingOpname.value) return;

    deleting.value = true;
    try {
        await stockOpnameService.delete(deletingOpname.value.id);
        notificationStore.success("Stock opname deleted successfully");
        showDeleteModal.value = false;
        deletingOpname.value = null;
        await loadOpnames();
        refreshStats();
    } catch (error) {
        console.error("[StockOpname] Failed to delete opname:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to delete opname"
        );
    } finally {
        deleting.value = false;
        if (showDeleteModal.value) {
            showDeleteModal.value = false;
            deletingOpname.value = null;
        }
    }
};

const saveOpname = async (formData) => {
    saving.value = true;
    try {
        if (editingOpname.value) {
            await stockOpnameService.update(editingOpname.value.id, formData);
            notificationStore.success("Stock opname updated successfully");
        } else {
            await stockOpnameService.create(formData);
            notificationStore.success("Stock opname created successfully");
        }
        closeModal();
        await loadOpnames();
        refreshStats();
    } catch (error) {
        console.error("[StockOpname] Failed to save opname:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to save opname"
        );
    } finally {
        saving.value = false;
    }
};

const handleGetSystemQuantity = async ({ productId, locationId, callback }) => {
    try {
        const response = await stockOpnameService.calculateSystemQuantity(
            productId,
            locationId
        );
        const quantity =
            response.system_quantity || response.data?.system_quantity || 0;
        callback(quantity);
    } catch (error) {
        console.error("[StockOpname] Failed to get system quantity:", error);
        notificationStore.error("Failed to get system quantity");
        callback(0);
    }
};

// Batch handle system quantities for all products
const handleBatchGetSystemQuantities = async ({
    productIds,
    locationId,
    callback,
}) => {
    try {
        console.log(
            "[StockOpname] Fetching batch system quantities for",
            productIds.length,
            "products at location",
            locationId
        );

        const response =
            await stockOpnameService.batchCalculateSystemQuantities(
                productIds,
                locationId
            );

        // Handle different response formats
        const quantities =
            response.data || response.quantities || response || {};

        console.log(
            "[StockOpname] Batch system quantities received:",
            quantities
        );

        callback(quantities);
    } catch (error) {
        console.error(
            "[StockOpname] Failed to get batch system quantities:",
            error
        );
        notificationStore.error("Failed to get system quantities");
        // Return empty object on failure
        callback({});
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingOpname.value = null;
    showCompleteModal.value = false;
    completingOpname.value = null;
    completing.value = false;
    showDeleteModal.value = false;
    deletingOpname.value = null;
    deleting.value = false;
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        in_progress:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        completed:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
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
        const blob = await stockOpnameService.export();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = `stock_opnames_${
            new Date().toISOString().split("T")[0]
        }.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        notificationStore.success("Export completed successfully");
    } catch (error) {
        console.error("[StockOpname] Failed to export opnames:", error);
        notificationStore.error("Failed to export data");
    }
};

onMounted(async () => {
    console.log("[StockOpname] Component mounted, loading data...");
    await Promise.all([loadOpnames(), loadProducts(), loadLocations()]);
});
</script>
