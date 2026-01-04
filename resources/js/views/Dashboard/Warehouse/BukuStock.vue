<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Buku Stock
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Stock card reporting and inventory movement tracking
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="showExportModal = true"
                    :disabled="
                        stockBookStore.loading || stockBookStore.exporting
                    "
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    <span v-if="stockBookStore.exporting">Exporting...</span>
                    <span v-else>Export</span>
                </button>
                <button
                    @click="showFilterModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <FunnelIcon class="w-4 h-4" />
                    Filter
                </button>
            </div>
        </div>

        <!-- View Mode Tabs -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px">
                    <button
                        v-for="tab in viewTabs"
                        :key="tab.key"
                        @click="stockBookStore.setViewMode(tab.key)"
                        :class="[
                            'py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200',
                            stockBookStore.viewMode === tab.key
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </nav>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <StockBookStats
            v-if="stockBookStore.viewMode !== 'balance'"
            :stats="stockBookStore.statistics"
            :show-trend="true"
            :show-percentage="true"
            :show-additional-stats="stockBookStore.viewMode === 'ledger'"
        />

        <!-- Filters Component -->
        <StockBookFilters
            v-if="showFilterModal"
            :loading="stockBookStore.loading"
            :show-status-filter="stockBookStore.viewMode === 'balance'"
            @apply="onFiltersApplied"
            @cancel="showFilterModal = false"
            @search="onSearch"
        />

        <!-- View Components -->
        <div v-if="!showFilterModal">
            <!-- Ledger View -->
            <LedgerView v-if="stockBookStore.viewMode === 'ledger'" />

            <!-- Product-Centric View -->
            <ProductView v-else-if="stockBookStore.viewMode === 'product'" />

            <!-- Location-Centric View -->
            <LocationView v-else-if="stockBookStore.viewMode === 'location'" />

            <!-- Current Balance View -->
            <CurrentBalanceView
                v-else-if="stockBookStore.viewMode === 'balance'"
            />

            <!-- Default/Stock Cards View -->
            <div v-else class="space-y-6">
                <StockBookTable
                    :stock-cards="stockBookStore.stockCards"
                    :loading="stockBookStore.loading"
                    :refreshing="stockBookStore.refreshing"
                    @refresh="stockBookStore.refreshCurrentView"
                    @view-details="onViewDetails"
                />
            </div>
        </div>

        <!-- Export Modal -->
        <ExportModal
            v-if="showExportModal"
            :loading="stockBookStore.exporting"
            @export="onExport"
            @close="showExportModal = false"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useStockBookStore } from "../../../stores/stockBookStore";
import { useNotificationStore } from "../../../stores/notification";
import StockBookStats from "../../../components/Warehouse/StockBook/StockBookStats.vue";
import StockBookFilters from "../../../components/Warehouse/StockBook/StockBookFilters.vue";
import LedgerView from "../../../components/Warehouse/StockBook/LedgerView.vue";
import ProductView from "../../../components/Warehouse/StockBook/ProductView.vue";
import LocationView from "../../../components/Warehouse/StockBook/LocationView.vue";
import CurrentBalanceView from "../../../components/Warehouse/StockBook/CurrentBalanceView.vue";
import StockBookTable from "../../../components/Warehouse/StockBook/StockBookTable.vue";
import ExportModal from "../../../components/Warehouse/StockBook/ExportModal.vue";
import { ArrowDownTrayIcon, FunnelIcon } from "@heroicons/vue/24/outline";

const stockBookStore = useStockBookStore();
const notificationStore = useNotificationStore();

// Local state
const showFilterModal = ref(false);
const showExportModal = ref(false);

// View tabs configuration
const viewTabs = [
    { key: "default", label: "Stock Cards" },
    { key: "ledger", label: "Ledger View" },
    { key: "product", label: "Product View" },
    { key: "location", label: "Location View" },
    { key: "balance", label: "Current Balance" },
];

// Methods
const onFiltersApplied = () => {
    showFilterModal.value = false;
    stockBookStore.refreshCurrentView();
};

const onSearch = (searchQuery) => {
    stockBookStore.updateFilters({ search: searchQuery });
    stockBookStore.debouncedSearch();
};

const onViewDetails = (stockCard) => {
    // Navigate to ledger view with selected product and location
    // Set default date range to current month
    const today = new Date();
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    stockBookStore.updateFilters({
        product_id: stockCard.product_id,
        location_id: stockCard.location_id,
        start_date: startOfMonth.toISOString().split('T')[0],
        end_date: today.toISOString().split('T')[0],
    });
    stockBookStore.setViewMode("ledger");
};

const onExport = async (options) => {
    try {
        await stockBookStore.exportData(options.format);
        notificationStore.success("Data exported successfully");
        showExportModal.value = false;
    } catch (error) {
        notificationStore.error("Failed to export data");
        console.error("Export error:", error);
    }
};

// Initialize store on mount
onMounted(async () => {
    try {
        await stockBookStore.initialize();
        await stockBookStore.refreshCurrentView();
    } catch (error) {
        notificationStore.error("Failed to initialize Stock Book");
        console.error("Initialization error:", error);
    }
});

// Cleanup on unmount
onUnmounted(() => {
    // Clear any pending timeouts or intervals
});
</script>
