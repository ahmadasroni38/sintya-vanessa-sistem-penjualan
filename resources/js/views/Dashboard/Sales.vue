<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Sales Management
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Manage your sales transactions and customer orders
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="showFilters = !showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <FunnelIcon class="w-4 h-4" />
                    Filters
                </button>
                <button
                    @click="handleExport"
                    :disabled="exporting"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    {{ exporting ? "Exporting..." : "Export" }}
                </button>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <PlusIcon class="w-4 h-4" />
                    New Sale
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <SalesStats :stats="stats" />

        <!-- Filters -->
        <SalesFilters
            :show="showFilters"
            :filters="filters"
            :location-options="locationOptions"
            :customer-options="customerOptions"
            @update:filters="handleFilterChange"
            @reset="handleResetFilters"
        />

        <!-- Sales Table -->
        <DataTable
            title="Sales Transactions"
            :data="sales"
            :columns="columns"
            :loading="loading"
            :server-side-pagination="true"
            :pagination="pagination"
            @edit="handleEdit"
            @delete="handleDelete"
            @refresh="handleRefresh"
            @page-change="handlePageChange"
            @sort="handleSort"
            :show-refresh="true"
            :refresh-loading="refreshing"
            :showAddButton="false"
            :showFilters="false"
            :showExport="false"
            :custom-actions="customActions"
            @custom-action="handleCustomAction"
        />

        <!-- Add/Edit Sale Modal -->
        <SalesFormModal
            :is-open="showAddModal || !!editingSale"
            :editing-item="editingSale"
            :location-options="locationOptions"
            :customer-options="customerOptions"
            :product-options="productOptions"
            @close="closeModal"
            @saved="handleFormSave"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showDeleteModal"
            title="Delete Sale"
            :message="`Are you sure you want to delete ${deletingSale?.transaction_number || 'this sale'}?`"
            description="This action cannot be undone. Only draft sales can be deleted."
            confirm-text="Delete"
            cancel-text="Cancel"
            :loading="deleting"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />

        <!-- Post Confirmation Modal -->
        <ConfirmationModal
            :is-open="showPostModal"
            title="Post Sale Transaction"
            :message="`Are you sure you want to post ${postingSale?.transaction_number}?`"
            description="This will update stock levels and create journal entries. This action cannot be undone."
            confirm-text="Post"
            cancel-text="Cancel"
            type="warning"
            :loading="saving"
            @confirm="confirmPost"
            @cancel="cancelPost"
        />

        <!-- Cancel Confirmation Modal -->
        <ConfirmationModal
            :is-open="showCancelModal"
            title="Cancel Sale Transaction"
            :message="`Are you sure you want to cancel ${cancellingSale?.transaction_number}?`"
            description="This will reverse stock levels and journal entries. This action cannot be undone."
            confirm-text="Cancel Sale"
            cancel-text="Close"
            type="danger"
            :loading="saving"
            @confirm="confirmCancel"
            @cancel="cancelCancelModal"
        />

        <!-- Details Modal -->
        <SalesDetailsModal
            :is-open="showDetailsModal"
            :sale="viewingSale"
            @close="closeDetailsModal"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useSales } from "@/composables/useSales";
import { useSelectOptions } from "@/composables/useSelectOptions";
import DataTable from "@/components/UI/DataTable.vue";
import ConfirmationModal from "@/components/Overlays/ConfirmationModal.vue";
import SalesStats from "@/components/Sales/SalesStats.vue";
import SalesFilters from "@/components/Sales/SalesFilters.vue";
import SalesFormModal from "@/components/Sales/SalesFormModal.vue";
import SalesDetailsModal from "@/components/Sales/SalesDetailsModal.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    FunnelIcon,
} from "@heroicons/vue/24/outline";

// Use Sales Composable
const {
    sales: salesData,
    loading,
    saving,
    deleting,
    pagination: paginationData,
    filters,
    fetchSales,
    fetchSale,
    createSale,
    updateSale,
    deleteSale,
    postSale,
    cancelSale,
    fetchSaleStatistics,
    fetchOptions,
    exportSales,
    applyFilters,
    resetFilters,
    changePage,
    getStatusClass,
    getStatusLabel,
    formatCurrency,
    formatDate,
} = useSales();

// Use Select Options Composable
const {
    locationOptions,
    loadLocations,
} = useSelectOptions();

// Local State
const refreshing = ref(false);
const exporting = ref(false);
const showAddModal = ref(false);
const editingSale = ref(null);
const showDeleteModal = ref(false);
const deletingSale = ref(null);
const showPostModal = ref(false);
const postingSale = ref(null);
const showCancelModal = ref(false);
const cancellingSale = ref(null);
const showDetailsModal = ref(false);
const viewingSale = ref(null);
const showFilters = ref(false);
const statistics = ref({
    total_transactions: 0,
    draft_count: 0,
    posted_count: 0,
    cancelled_count: 0,
    total_revenue: 0,
});
const customerOptions = ref([]);
const productOptions = ref([]);

// Table columns
const columns = [
    {
        key: "transaction_number",
        label: "Transaction #",
        sortable: true,
    },
    {
        key: "transaction_date",
        label: "Date",
        sortable: true,
        type: "date",
    },
    {
        key: "customer.customer_name",
        label: "Customer",
        sortable: false,
        formatter: (value, row) => value || "Walk-in Customer",
    },
    {
        key: "location.name",
        label: "Location",
        sortable: false,
    },
    {
        key: "total_amount",
        label: "Total",
        sortable: true,
        type: "currency",
    },
    {
        key: "payment_method",
        label: "Payment",
        sortable: false,
        formatter: (value) => {
            const methods = { cash: "Cash", transfer: "Transfer", credit: "Credit" };
            return methods[value] || value;
        },
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
        type: "badge",
        badgeColors: {
            draft: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
            posted: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
            cancelled: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        },
        formatter: (value) => getStatusLabel(value),
    },
];

// Custom actions for table
const customActions = [
    {
        label: "View",
        icon: "eye",
        handler: (item) => handleView(item),
        condition: () => true,
    },
    {
        label: "Post",
        icon: "check",
        handler: (item) => handlePost(item),
        condition: (item) => item.status === "draft",
        class: "text-green-600 hover:text-green-900 dark:text-green-400",
    },
    {
        label: "Cancel",
        icon: "x",
        handler: (item) => handleCancelTransaction(item),
        condition: (item) => item.status === "posted",
        class: "text-red-600 hover:text-red-900 dark:text-red-400",
    },
];

// Computed
const sales = computed(() => salesData.value || []);
const pagination = computed(() => paginationData.value || {});

const stats = computed(() => ({
    total_transactions: statistics.value.total_transactions || 0,
    draft_count: statistics.value.draft_count || 0,
    posted_count: statistics.value.posted_count || 0,
    total_revenue: statistics.value.total_revenue || 0,
}));

// Methods
const loadStatistics = async () => {
    try {
        const data = await fetchSaleStatistics();
        if (data) {
            statistics.value = data;
        }
    } catch (error) {
        console.error("Error loading statistics:", error);
        // Set default values on error
        statistics.value = {
            total_transactions: 0,
            draft_count: 0,
            posted_count: 0,
            cancelled_count: 0,
            total_revenue: 0,
        };
    }
};

const loadOptions = async () => {
    try {
        const data = await fetchOptions();
        if (data) {
            customerOptions.value = data.customers || [];
            productOptions.value = data.products || [];
        }
    } catch (error) {
        console.error("Error loading options:", error);
        // Set default values on error
        customerOptions.value = [];
        productOptions.value = [];
    }
};

const handleFilterChange = () => {
    applyFilters();
    loadStatistics();
};

const handleResetFilters = () => {
    resetFilters();
    loadStatistics();
};

const handleRefresh = async () => {
    refreshing.value = true;
    await fetchSales(pagination.value.current_page);
    await loadStatistics();
    refreshing.value = false;
};

const handlePageChange = (page) => {
    changePage(page);
};

const handleSort = (column, direction) => {
    filters.value.sort_by = column;
    filters.value.sort_order = direction;
    applyFilters();
};

const handleCustomAction = ({ action, item }) => {
    action.handler(item);
};

const handleView = async (sale) => {
    try {
        const fullSale = await fetchSale(sale.id);
        viewingSale.value = fullSale;
        showDetailsModal.value = true;
    } catch (error) {
        console.error("Error viewing sale:", error);
    }
};

const handleEdit = async (sale) => {
    if (sale.status !== "draft") {
        return;
    }
    try {
        const fullSale = await fetchSale(sale.id);
        editingSale.value = fullSale;
        showAddModal.value = true;
    } catch (error) {
        console.error("Error loading sale for edit:", error);
    }
};

const handleDelete = (sale) => {
    if (sale.status !== "draft") {
        return;
    }
    deletingSale.value = sale;
    showDeleteModal.value = true;
};

const handlePost = (sale) => {
    postingSale.value = sale;
    showPostModal.value = true;
};

const handleCancelTransaction = (sale) => {
    cancellingSale.value = sale;
    showCancelModal.value = true;
};

const confirmDelete = async () => {
    if (!deletingSale.value || deleting.value) return;

    try {
        await deleteSale(deletingSale.value.id);
        cancelDelete();
        await handleRefresh();
    } catch (error) {
        console.error("Error deleting sale:", error);
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deletingSale.value = null;
};

const confirmPost = async () => {
    if (!postingSale.value || saving.value) return;

    try {
        await postSale(postingSale.value.id);
        cancelPost();
        await handleRefresh();
    } catch (error) {
        console.error("Error posting sale:", error);
    }
};

const cancelPost = () => {
    showPostModal.value = false;
    postingSale.value = null;
};

const confirmCancel = async () => {
    if (!cancellingSale.value || saving.value) return;

    try {
        await cancelSale(cancellingSale.value.id);
        cancelCancelModal();
        await handleRefresh();
    } catch (error) {
        console.error("Error cancelling sale:", error);
    }
};

const cancelCancelModal = () => {
    showCancelModal.value = false;
    cancellingSale.value = null;
};

const handleFormSave = async ({ formData, isEditing, saleId }) => {
    try {
        if (isEditing) {
            await updateSale(saleId, formData);
        } else {
            await createSale(formData);
        }

        closeModal();
        await handleRefresh();
    } catch (error) {
        console.error("Error saving sale:", error);
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingSale.value = null;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    viewingSale.value = null;
};

const handleExport = async () => {
    exporting.value = true;
    try {
        await exportSales();
    } catch (error) {
        console.error("Error exporting sales:", error);
    } finally {
        exporting.value = false;
    }
};

// Lifecycle
onMounted(async () => {
    try {
        // Load options first (non-blocking)
        await Promise.all([
            loadLocations(),
            loadOptions(),
        ]);

        // Then load sales data
        await fetchSales(1);

        // Finally load statistics
        await loadStatistics();
    } catch (error) {
        console.error("Error during initialization:", error);
        // Continue even if some calls fail
    }
});
</script>
