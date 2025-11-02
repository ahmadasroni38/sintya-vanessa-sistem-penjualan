<template>
    <div class="space-y-6">
        <!-- Location Selector and Summary -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <div class="flex-1">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Select Location
                    </label>
                    <FormSelect
                        v-model="selectedLocationId"
                        :options="stockBookStore.locationOptions"
                        placeholder="Select location to view current balances"
                        @change="onLocationChange"
                    />
                </div>

                <div
                    v-if="summary"
                    class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 sm:mt-0"
                >
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Products
                        </p>
                        <p
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ formatNumber(summary.total_products) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            In Stock
                        </p>
                        <p
                            class="text-xl font-bold text-green-600 dark:text-green-400"
                        >
                            {{ formatNumber(summary.in_stock) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Low Stock
                        </p>
                        <p
                            class="text-xl font-bold text-yellow-600 dark:text-yellow-400"
                        >
                            {{ formatNumber(summary.low_stock) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Out of Stock
                        </p>
                        <p
                            class="text-xl font-bold text-red-600 dark:text-red-400"
                        >
                            {{ formatNumber(summary.out_of_stock) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            v-if="selectedLocationId"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <FormInput
                        v-model="searchQuery"
                        label="Search Products"
                        type="text"
                        placeholder="Search by product code or name..."
                        @input="onSearchInput"
                    />
                </div>
                <div class="sm:w-48">
                    <FormSelect
                        v-model="statusFilter"
                        label="Stock Status"
                        :options="statusFilterOptions"
                        @change="onStatusFilterChange"
                    />
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
            <svg class="animate-spin h-8 w-8 text-blue-600" viewBox="0 0 24 24">
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                    fill="none"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            <span class="ml-2 text-gray-600 dark:text-gray-400"
                >Loading current balances...</span
            >
        </div>

        <!-- Balance Cards Grid -->
        <div
            v-else-if="filteredBalances && filteredBalances.length > 0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
            <BalanceCard
                v-for="balance in filteredBalances"
                :key="balance.product_id"
                :balance="balance"
                @view-ledger="onViewLedger"
                @reorder="onReorder"
            />
        </div>

        <!-- Empty State -->
        <div
            v-else-if="selectedLocationId"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12"
        >
            <div class="flex flex-col items-center text-center">
                <CubeIcon class="w-16 h-16 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    {{
                        searchQuery || statusFilter !== "all"
                            ? "No products found"
                            : "No products in this location"
                    }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    {{
                        searchQuery || statusFilter !== "all"
                            ? "Try adjusting your search terms or filters to find what you're looking for."
                            : "There are no products with stock transactions in this location yet."
                    }}
                </p>
                <button
                    v-if="searchQuery || statusFilter !== 'all'"
                    @click="clearFilters"
                    class="mt-4 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30"
                >
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- No Location Selected -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12"
        >
            <div class="flex flex-col items-center text-center">
                <BuildingOfficeIcon class="w-16 h-16 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    Select a Location
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    Please select a location to view current stock balances for
                    all products in that location.
                </p>
            </div>
        </div>

        <!-- Export Button -->
        <div
            v-if="
                selectedLocationId &&
                filteredBalances &&
                filteredBalances.length > 0
            "
            class="flex justify-end"
        >
            <button
                @click="exportCurrentBalances"
                :disabled="exporting"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
            >
                <ArrowDownTrayIcon class="w-4 h-4" />
                <span v-if="exporting">Exporting...</span>
                <span v-else>Export Current Balances</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useStockBookStore } from "../../../stores/stockBookStore";
import { useNotificationStore } from "../../../stores/notification";
import BalanceCard from "./BalanceCard.vue";
import FormInput from "../../Forms/FormInput.vue";
import FormSelect from "../../Forms/FormSelect.vue";
import {
    CubeIcon,
    BuildingOfficeIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/outline";

const stockBookStore = useStockBookStore();
const notificationStore = useNotificationStore();

// Local state
const selectedLocationId = ref(null);
const searchQuery = ref("");
const statusFilter = ref("all");
const exporting = ref(false);

// Options
const statusFilterOptions = [
    { value: "all", label: "All Status" },
    { value: "in_stock", label: "In Stock" },
    { value: "low_stock", label: "Low Stock" },
    { value: "out_of_stock", label: "Out of Stock" },
    { value: "overstock", label: "Overstock" },
];

// Computed properties
const currentBalances = computed(() => stockBookStore.currentBalances);

const summary = computed(() => {
    if (!currentBalances.value || currentBalances.value.length === 0)
        return null;

    const balances = currentBalances.value;
    return {
        total_products: balances.length,
        in_stock: balances.filter((b) => b.status === "in_stock").length,
        low_stock: balances.filter((b) => b.status === "low_stock").length,
        out_of_stock: balances.filter((b) => b.status === "out_of_stock")
            .length,
        overstock: balances.filter((b) => b.status === "overstock").length,
    };
});

const filteredBalances = computed(() => {
    if (!currentBalances.value) return [];

    let balances = [...currentBalances.value];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        balances = balances.filter(
            (balance) =>
                balance.product_code.toLowerCase().includes(query) ||
                balance.product_name.toLowerCase().includes(query)
        );
    }

    // Apply status filter
    if (statusFilter.value !== "all") {
        balances = balances.filter(
            (balance) => balance.status === statusFilter.value
        );
    }

    return balances;
});

// Methods
const onLocationChange = async () => {
    if (!selectedLocationId.value) {
        stockBookStore.currentBalances = [];
        return;
    }

    stockBookStore.updateFilters({ location_id: selectedLocationId.value });
    await loadCurrentBalances();
};

const onSearchInput = () => {
    stockBookStore.updateFilters({ search: searchQuery.value });
};

const onStatusFilterChange = () => {
    stockBookStore.updateFilters({ status_filter: statusFilter.value });
};

const clearFilters = () => {
    searchQuery.value = "";
    statusFilter.value = "all";
    stockBookStore.updateFilters({ search: "", status_filter: "all" });
};

const loadCurrentBalances = async () => {
    try {
        await stockBookStore.fetchCurrentBalances();
    } catch (error) {
        notificationStore.error("Failed to load current balances");
        console.error("Failed to load current balances:", error);
    }
};

const onViewLedger = (balance) => {
    // Navigate to ledger view with selected product and location
    stockBookStore.updateFilters({
        product_id: balance.product_id,
        location_id: selectedLocationId.value,
    });
    stockBookStore.setViewMode("ledger");
};

const onReorder = (balance) => {
    // Handle reorder action (could open modal or navigate to purchase order)
    notificationStore.info(
        `Reorder functionality for ${balance.product_name} coming soon`
    );
};

const exportCurrentBalances = async () => {
    exporting.value = true;
    try {
        await stockBookService.export({
            location_id: selectedLocationId.value,
            format: "xlsx",
            type: "current_balances",
        });
        notificationStore.success("Current balances exported successfully");
    } catch (error) {
        notificationStore.error("Failed to export current balances");
        console.error("Export error:", error);
    } finally {
        exporting.value = false;
    }
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0";
    return new Intl.NumberFormat("en-US").format(value);
};

// Watch for store changes
watch(
    () => stockBookStore.filters.location_id,
    (newLocationId) => {
        if (newLocationId !== selectedLocationId.value) {
            selectedLocationId.value = newLocationId;
        }
    }
);

onMounted(() => {
    // Initialize with location from store if available
    if (stockBookStore.filters.location_id) {
        selectedLocationId.value = stockBookStore.filters.location_id;
        loadCurrentBalances();
    }
});
</script>
