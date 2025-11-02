<template>
    <div class="space-y-6">
        <!-- Product Selection and Filters -->
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
                        Select Product
                    </label>
                    <FormSelect
                        v-model="selectedProductId"
                        :options="stockBookStore.productOptions"
                        placeholder="Select product to view movement across all locations"
                        searchable
                        @change="onProductChange"
                    />
                </div>

                <div class="flex gap-3">
                    <button
                        @click="refreshData"
                        :disabled="!selectedProductId || refreshing"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        <ArrowPathIcon
                            :class="['w-4 h-4', refreshing && 'animate-spin']"
                        />
                        Refresh
                    </button>
                    <button
                        @click="exportProductData"
                        :disabled="!selectedProductId || exporting"
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

        <!-- Product Summary -->
        <div
            v-if="selectedProduct && productSummary"
            class="grid grid-cols-1 md:grid-cols-4 gap-6"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Locations
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ formatNumber(productSummary.total_locations) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <BuildingOfficeIcon
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
                            {{ formatNumber(productSummary.total_in) }}
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
                            {{ formatNumber(productSummary.total_out) }}
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
                            {{ formatNumber(productSummary.current_balance) }}
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
                >Loading product movements...</span
            >
        </div>

        <!-- Location Movements Table -->
        <div
            v-else-if="locationMovements && locationMovements.length > 0"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Movement by Location
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Stock movement for {{ selectedProduct?.product_code }} -
                    {{ selectedProduct?.product_name }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Location
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Opening Balance
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Total In
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Total Out
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Ending Balance
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Net Change
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="(movement, index) in locationMovements"
                            :key="movement.location_id"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-900/20'
                            "
                        >
                            <td class="px-6 py-4">
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ movement.location_name }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ movement.location_code }}
                                    </p>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(movement.opening_balance) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span
                                    class="text-green-600 dark:text-green-400 font-semibold"
                                >
                                    +{{ formatNumber(movement.total_in) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span
                                    class="text-red-600 dark:text-red-400 font-semibold"
                                >
                                    -{{ formatNumber(movement.total_out) }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(movement.ending_balance) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span
                                    :class="[
                                        'font-semibold',
                                        movement.net_change >= 0
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400',
                                    ]"
                                >
                                    {{ movement.net_change >= 0 ? "+" : ""
                                    }}{{ formatNumber(movement.net_change) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <button
                                    @click="viewLocationLedger(movement)"
                                    class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View detailed ledger"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else-if="selectedProductId"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12"
        >
            <div class="flex flex-col items-center text-center">
                <CubeIcon class="w-16 h-16 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No movements found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    No stock movements found for this product in the selected
                    period.
                </p>
            </div>
        </div>

        <!-- No Product Selected -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12"
        >
            <div class="flex flex-col items-center text-center">
                <CubeIcon class="w-16 h-16 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    Select a Product
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    Please select a product to view stock movement across all
                    locations.
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
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ScaleIcon,
    CubeIcon,
    ArrowPathIcon,
    ArrowDownTrayIcon,
    EyeIcon,
} from "@heroicons/vue/24/outline";

const stockBookStore = useStockBookStore();
const notificationStore = useNotificationStore();

// Local state
const selectedProductId = ref(null);
const startDate = ref("");
const endDate = ref("");
const datePreset = ref("");
const loading = ref(false);
const refreshing = ref(false);
const exporting = ref(false);
const locationMovements = ref([]);
const productSummary = ref(null);

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

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

// Computed properties
const selectedProduct = computed(() => {
    return stockBookStore.products.find(
        (p) => p.id === selectedProductId.value
    );
});

// Methods
const onProductChange = () => {
    stockBookStore.updateFilters({ product_id: selectedProductId.value });
    if (selectedProductId.value) {
        loadProductMovements();
    } else {
        locationMovements.value = [];
        productSummary.value = null;
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
    if (selectedProductId.value) {
        loadProductMovements();
    }
};

const loadProductMovements = async () => {
    if (!selectedProductId.value) return;

    loading.value = true;
    try {
        const params = {
            product_id: selectedProductId.value,
            start_date: startDate.value,
            end_date: endDate.value,
        };

        // Get stock cards grouped by location
        const response = await stockBookService.getAll(params);

        // Group by location and calculate summaries
        const locationGroups = {};
        let totalIn = 0;
        let totalOut = 0;
        let currentBalance = 0;

        response.data.forEach((card) => {
            const locationId = card.location_id;
            if (!locationGroups[locationId]) {
                locationGroups[locationId] = {
                    location_id: locationId,
                    location_code: card.location?.code || "",
                    location_name: card.location?.name || "",
                    opening_balance: 0,
                    total_in: 0,
                    total_out: 0,
                    ending_balance: 0,
                    net_change: 0,
                };
            }

            const group = locationGroups[locationId];
            group.total_in += card.quantity_in || 0;
            group.total_out += card.quantity_out || 0;

            // Use the last balance as ending balance
            if (card.balance !== undefined) {
                group.ending_balance = card.balance;
            }
        });

        // Calculate opening balance and net change for each location
        Object.values(locationGroups).forEach((group) => {
            group.opening_balance =
                group.ending_balance - group.total_in + group.total_out;
            group.net_change = group.total_in - group.total_out;

            totalIn += group.total_in;
            totalOut += group.total_out;
            currentBalance += group.ending_balance;
        });

        locationMovements.value = Object.values(locationGroups);

        productSummary.value = {
            total_locations: locationMovements.value.length,
            total_in: totalIn,
            total_out: totalOut,
            current_balance: currentBalance,
        };
    } catch (error) {
        notificationStore.error("Failed to load product movements");
        console.error("Failed to load product movements:", error);
    } finally {
        loading.value = false;
    }
};

const refreshData = async () => {
    refreshing.value = true;
    try {
        await loadProductMovements();
    } finally {
        refreshing.value = false;
    }
};

const viewLocationLedger = (movement) => {
    // Navigate to ledger view with selected product and location
    stockBookStore.updateFilters({
        product_id: selectedProductId.value,
        location_id: movement.location_id,
        start_date: startDate.value,
        end_date: endDate.value,
    });
    stockBookStore.setViewMode("ledger");
};

const exportProductData = async () => {
    if (!selectedProductId.value) return;

    exporting.value = true;
    try {
        await stockBookService.export({
            product_id: selectedProductId.value,
            start_date: startDate.value,
            end_date: endDate.value,
            format: "xlsx",
            type: "product_view",
        });
        notificationStore.success("Product data exported successfully");
    } catch (error) {
        notificationStore.error("Failed to export product data");
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
    () => stockBookStore.filters.product_id,
    (newProductId) => {
        if (newProductId !== selectedProductId.value) {
            selectedProductId.value = newProductId;
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
    if (stockBookStore.filters.product_id) {
        selectedProductId.value = stockBookStore.filters.product_id;
    }
    if (stockBookStore.filters.start_date) {
        startDate.value = stockBookStore.filters.start_date;
    }
    if (stockBookStore.filters.end_date) {
        endDate.value = stockBookStore.filters.end_date;
    }

    // Load data if product is selected
    if (selectedProductId.value) {
        loadProductMovements();
    }
});
</script>
