<template>
    <div class="space-y-6">
        <!-- Location Selection and Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
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
                        placeholder="Select location to view all products"
                        @change="onLocationChange"
                    />
                </div>

                <div class="flex gap-3">
                    <button
                        @click="refreshData"
                        :disabled="!selectedLocationId || refreshing"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        <ArrowPathIcon
                            :class="['w-4 h-4', refreshing && 'animate-spin']"
                        />
                        Refresh
                    </button>
                    <button
                        @click="exportLocationData"
                        :disabled="!selectedLocationId || exporting"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span v-if="exporting">Exporting...</span>
                        <span v-else>Export</span>
                    </button>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div>
                    <FormSelect
                        v-model="datePreset"
                        label="Date Range"
                        :options="datePresetOptions"
                        @change="onDatePresetChange"
                    />
                </div>
                <div>
                    <FormInput
                        v-model="startDate"
                        label="Start Date"
                        type="date"
                        :max="today"
                        @change="onDateChange"
                    />
                </div>
                <div>
                    <FormInput
                        v-model="endDate"
                        label="End Date"
                        type="date"
                        :max="today"
                        :min="startDate"
                        @change="onDateChange"
                    />
                </div>
            </div>
        </div>

        <!-- Location Summary -->
        <div
            v-if="selectedLocation && locationSummary"
            class="grid grid-cols-1 md:grid-cols-4 gap-6"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Products
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ formatNumber(locationSummary.total_products) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <CubeIcon
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Stock In
                        </p>
                        <p
                            class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1"
                        >
                            {{ formatNumber(locationSummary.total_in) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ArrowTrendingUpIcon
                            class="w-6 h-6 text-green-600 dark:text-green-400"
                        />
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Stock Out
                        </p>
                        <p
                            class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1"
                        >
                            {{ formatNumber(locationSummary.total_out) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ArrowTrendingDownIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Current Balance
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ formatNumber(locationSummary.current_balance) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ScaleIcon
                            class="w-6 h-6 text-purple-600 dark:text-purple-400"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
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
                        v-model="stockStatusFilter"
                        label="Stock Status"
                        :options="stockStatusOptions"
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
                >Loading location products...</span
            >
        </div>

        <!-- Products Grid -->
        <div
            v-else-if="filteredProducts && filteredProducts.length > 0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
            <div
                v-for="product in filteredProducts"
                :key="product.product_id"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200 cursor-pointer"
                @click="viewProductLedger(product)"
            >
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white truncate"
                        >
                            {{ product.product_code }}
                        </h3>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 truncate"
                        >
                            {{ product.product_name }}
                        </p>
                    </div>
                    <div class="ml-4">
                        <span
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                getStockStatusClass(product),
                            ]"
                        >
                            {{ getStockStatusLabel(product) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Current Balance
                        </p>
                        <p
                            :class="[
                                'text-xl font-bold',
                                product.current_balance === 0
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-gray-900 dark:text-white',
                            ]"
                        >
                            {{ formatNumber(product.current_balance) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Movement
                        </p>
                        <p
                            :class="[
                                'text-xl font-bold',
                                product.net_change >= 0
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-red-600 dark:text-red-400',
                            ]"
                        >
                            {{ product.net_change >= 0 ? "+" : ""
                            }}{{ formatNumber(product.net_change) }}
                        </p>
                    </div>
                </div>

                <!-- Stock Level Indicator -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400"
                            >Stock Level</span
                        >
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ getStockPercentage(product) }}%
                        </span>
                    </div>
                    <div
                        class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2"
                    >
                        <div
                            :class="[
                                'h-2 rounded-full transition-all duration-300',
                                getStockLevelBarClass(product),
                            ]"
                            :style="{
                                width: `${Math.min(
                                    getStockPercentage(product),
                                    100
                                )}%`,
                            }"
                        ></div>
                    </div>
                </div>

                <!-- Last Transaction Info -->
                <div
                    v-if="product.last_transaction_date"
                    class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400"
                >
                    <div class="flex items-center">
                        <ClockIcon class="w-3 h-3 mr-1" />
                        Last: {{ formatDate(product.last_transaction_date) }}
                    </div>
                    <div class="flex items-center">
                        <span
                            :class="[
                                'inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium',
                                getTransactionTypeClass(
                                    product.last_transaction_type
                                ),
                            ]"
                        >
                            {{
                                getTransactionTypeLabel(
                                    product.last_transaction_type
                                )
                            }}
                        </span>
                    </div>
                </div>
            </div>
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
                        searchQuery || stockStatusFilter !== "all"
                            ? "No products found"
                            : "No products in this location"
                    }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    {{
                        searchQuery || stockStatusFilter !== "all"
                            ? "Try adjusting your search terms or filters to find what you're looking for."
                            : "There are no products with stock transactions in this location yet."
                    }}
                </p>
                <button
                    v-if="searchQuery || stockStatusFilter !== 'all'"
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
                    Please select a location to view all products and their
                    stock movements in that location.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useStockBookStore } from "../../../stores/stockBookStore";
import { useNotificationStore } from "../../../stores/notification";
import { stockBookService } from "../../../services/warehouseService";
import FormInput from "../../Forms/FormInput.vue";
import FormSelect from "../../Forms/FormSelect.vue";
import {
    BuildingOfficeIcon,
    CubeIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ScaleIcon,
    ArrowPathIcon,
    ArrowDownTrayIcon,
    ClockIcon,
    EyeIcon,
} from "@heroicons/vue/24/outline";

const stockBookStore = useStockBookStore();
const notificationStore = useNotificationStore();

// Local state
const selectedLocationId = ref(null);
const startDate = ref("");
const endDate = ref("");
const datePreset = ref("");
const searchQuery = ref("");
const stockStatusFilter = ref("all");
const loading = ref(false);
const refreshing = ref(false);
const exporting = ref(false);
const locationProducts = ref([]);
const locationSummary = ref(null);

// Options
const datePresetOptions = [
    { value: "", label: "Custom Range" },
    { value: "today", label: "Today" },
    { value: "yesterday", label: "Yesterday" },
    { value: "last_7_days", label: "Last 7 Days" },
    { value: "last_30_days", label: "Last 30 Days" },
    { value: "this_month", label: "This Month" },
    { value: "last_month", label: "Last Month" },
    { value: "this_year", label: "This Year" },
];

const stockStatusOptions = [
    { value: "all", label: "All Status" },
    { value: "in_stock", label: "In Stock" },
    { value: "low_stock", label: "Low Stock" },
    { value: "out_of_stock", label: "Out of Stock" },
    { value: "overstock", label: "Overstock" },
];

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

// Computed properties
const selectedLocation = computed(() => {
    return stockBookStore.locations.find(
        (l) => l.id === selectedLocationId.value
    );
});

const filteredProducts = computed(() => {
    if (!locationProducts.value) return [];

    let products = [...locationProducts.value];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        products = products.filter(
            (product) =>
                product.product_code.toLowerCase().includes(query) ||
                product.product_name.toLowerCase().includes(query)
        );
    }

    // Apply status filter
    if (stockStatusFilter.value !== "all") {
        products = products.filter(
            (product) => getStockStatus(product) === stockStatusFilter.value
        );
    }

    return products;
});

// Methods
const onLocationChange = () => {
    stockBookStore.updateFilters({ location_id: selectedLocationId.value });
    if (selectedLocationId.value) {
        loadLocationProducts();
    } else {
        locationProducts.value = [];
        locationSummary.value = null;
    }
};

const onDatePresetChange = () => {
    const preset = datePreset.value;
    const today = new Date();
    let start = new Date();
    let end = new Date();

    switch (preset) {
        case "today":
            start = today;
            end = today;
            break;
        case "yesterday":
            start = new Date(today);
            start.setDate(today.getDate() - 1);
            end = start;
            break;
        case "last_7_days":
            start = new Date(today);
            start.setDate(today.getDate() - 7);
            end = today;
            break;
        case "last_30_days":
            start = new Date(today);
            start.setDate(today.getDate() - 30);
            end = today;
            break;
        case "this_month":
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = today;
            break;
        case "last_month":
            start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            end = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case "this_year":
            start = new Date(today.getFullYear(), 0, 1);
            end = today;
            break;
        default:
            return; // Custom range, don't auto-set dates
    }

    startDate.value = start.toISOString().split("T")[0];
    endDate.value = end.toISOString().split("T")[0];
    onDateChange();
};

const onDateChange = () => {
    stockBookStore.updateFilters({
        start_date: startDate.value,
        end_date: endDate.value,
    });
    if (selectedLocationId.value) {
        loadLocationProducts();
    }
};

const onSearchInput = () => {
    // Search is handled by computed property
};

const onStatusFilterChange = () => {
    // Filter is handled by computed property
};

const clearFilters = () => {
    searchQuery.value = "";
    stockStatusFilter.value = "all";
};

const loadLocationProducts = async () => {
    if (!selectedLocationId.value) return;

    loading.value = true;
    try {
        const params = {
            location_id: selectedLocationId.value,
            start_date: startDate.value,
            end_date: endDate.value,
        };

        // Get stock cards grouped by product
        const response = await stockBookService.getAll(params);

        // Group by product and calculate summaries
        const productGroups = {};
        let totalIn = 0;
        let totalOut = 0;
        let currentBalance = 0;

        response.data.forEach((card) => {
            const productId = card.product_id;
            if (!productGroups[productId]) {
                productGroups[productId] = {
                    product_id: productId,
                    product_code: card.product?.product_code || "",
                    product_name: card.product?.product_name || "",
                    opening_balance: 0,
                    total_in: 0,
                    total_out: 0,
                    ending_balance: 0,
                    net_change: 0,
                    minimum_stock: card.product?.minimum_stock || 0,
                    maximum_stock: card.product?.maximum_stock || 0,
                    last_transaction_date: card.transaction_date,
                    last_transaction_type: card.transaction_type,
                };
            }

            const group = productGroups[productId];
            group.total_in += card.quantity_in || 0;
            group.total_out += card.quantity_out || 0;

            // Use the latest transaction date and type
            if (card.transaction_date >= group.last_transaction_date) {
                group.last_transaction_date = card.transaction_date;
                group.last_transaction_type = card.transaction_type;
            }

            // Use the last balance as ending balance
            if (card.balance !== undefined) {
                group.ending_balance = card.balance;
            }
        });

        // Calculate opening balance and net change for each product
        Object.values(productGroups).forEach((group) => {
            group.opening_balance =
                group.ending_balance - group.total_in + group.total_out;
            group.net_change = group.total_in - group.total_out;

            totalIn += group.total_in;
            totalOut += group.total_out;
            currentBalance += group.ending_balance;
        });

        locationProducts.value = Object.values(productGroups);

        locationSummary.value = {
            total_products: locationProducts.value.length,
            total_in: totalIn,
            total_out: totalOut,
            current_balance: currentBalance,
        };
    } catch (error) {
        notificationStore.error("Failed to load location products");
        console.error("Failed to load location products:", error);
    } finally {
        loading.value = false;
    }
};

const refreshData = async () => {
    refreshing.value = true;
    try {
        await loadLocationProducts();
    } finally {
        refreshing.value = false;
    }
};

const viewProductLedger = (product) => {
    // Navigate to ledger view with selected product and location
    stockBookStore.updateFilters({
        product_id: product.product_id,
        location_id: selectedLocationId.value,
        start_date: startDate.value,
        end_date: endDate.value,
    });
    stockBookStore.setViewMode("ledger");
};

const exportLocationData = async () => {
    if (!selectedLocationId.value) return;

    exporting.value = true;
    try {
        await stockBookService.export({
            location_id: selectedLocationId.value,
            start_date: startDate.value,
            end_date: endDate.value,
            format: "xlsx",
            type: "location_view",
        });
        notificationStore.success("Location data exported successfully");
    } catch (error) {
        notificationStore.error("Failed to export location data");
        console.error("Export error:", error);
    } finally {
        exporting.value = false;
    }
};

// Helper methods
const getStockStatus = (product) => {
    const balance = product.current_balance;
    const min = product.minimum_stock;
    const max = product.maximum_stock;

    if (balance === 0) return "out_of_stock";
    if (balance < min) return "low_stock";
    if (balance > max) return "overstock";
    return "in_stock";
};

const getStockStatusClass = (product) => {
    const status = getStockStatus(product);
    const classes = {
        in_stock:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        low_stock:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        out_of_stock:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        overstock:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
    };
    return (
        classes[status] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const getStockStatusLabel = (product) => {
    const status = getStockStatus(product);
    const labels = {
        in_stock: "In Stock",
        low_stock: "Low Stock",
        out_of_stock: "Out of Stock",
        overstock: "Overstock",
    };
    return labels[status] || "Unknown";
};

const getStockPercentage = (product) => {
    const { current_balance, maximum_stock } = product;
    if (!maximum_stock || maximum_stock === 0) return 0;
    return Math.round((current_balance / maximum_stock) * 100);
};

const getStockLevelBarClass = (product) => {
    const percentage = getStockPercentage(product);
    if (percentage === 0) return "bg-red-600 dark:bg-red-400";
    if (percentage < 25) return "bg-orange-600 dark:bg-orange-400";
    if (percentage < 50) return "bg-yellow-600 dark:bg-yellow-400";
    if (percentage > 100) return "bg-blue-600 dark:bg-blue-400";
    return "bg-green-600 dark:bg-green-400";
};

const getTransactionTypeClass = (type) => {
    const classes = {
        stock_in:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        stock_out:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        mutation_in:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        mutation_out:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        adjustment_in:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        adjustment_out:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        opname_in:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
        opname_out:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
    };
    return (
        classes[type] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const getTransactionTypeLabel = (type) => {
    const labels = {
        stock_in: "Stock In",
        stock_out: "Stock Out",
        mutation_in: "Mutation In",
        mutation_out: "Mutation Out",
        adjustment_in: "Adj In",
        adjustment_out: "Adj Out",
        opname_in: "Opname In",
        opname_out: "Opname Out",
    };
    return labels[type] || type;
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0";
    return new Intl.NumberFormat("en-US").format(value);
};

const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
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

watch(
    () => stockBookStore.filters.start_date,
    (newStartDate) => {
        if (newStartDate !== startDate.value) {
            startDate.value = newStartDate;
        }
    }
);

watch(
    () => stockBookStore.filters.end_date,
    (newEndDate) => {
        if (newEndDate !== endDate.value) {
            endDate.value = newEndDate;
        }
    }
);

onMounted(() => {
    // Initialize with values from store if available
    if (stockBookStore.filters.location_id) {
        selectedLocationId.value = stockBookStore.filters.location_id;
    }
    if (stockBookStore.filters.start_date) {
        startDate.value = stockBookStore.filters.start_date;
    }
    if (stockBookStore.filters.end_date) {
        endDate.value = stockBookStore.filters.end_date;
    }

    // Load data if location is selected
    if (selectedLocationId.value) {
        loadLocationProducts();
    }
});
</script>
