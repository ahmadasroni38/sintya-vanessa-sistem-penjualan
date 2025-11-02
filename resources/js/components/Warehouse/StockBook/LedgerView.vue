<template>
    <div class="space-y-6">
        <!-- Product and Location Selection -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Select Product
                    </label>
                    <FormSelect
                        v-model="selectedProductId"
                        :options="stockBookStore.productOptions"
                        placeholder="Select product to view ledger"
                        searchable
                        @change="onProductChange"
                    />
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Select Location
                    </label>
                    <FormSelect
                        v-model="selectedLocationId"
                        :options="stockBookStore.locationOptions"
                        placeholder="Select location to view ledger"
                        @change="onLocationChange"
                    />
                </div>
            </div>

            <!-- Date Range -->
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

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6">
                <button
                    @click="exportLedger"
                    :disabled="!canExport || exporting"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    <span v-if="exporting">Exporting...</span>
                    <span v-else>Export Ledger</span>
                </button>
                <button
                    @click="loadLedgerData"
                    :disabled="!canLoadData || loading"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
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
                        Loading...
                    </span>
                    <span v-else>View Ledger</span>
                </button>
            </div>
        </div>

        <!-- Ledger Table -->
        <LedgerTable
            v-if="showLedger"
            :product="stockBookStore.selectedProduct"
            :location="stockBookStore.selectedLocation"
            :period="period"
            :opening-balance="ledgerData?.opening_balance"
            :transactions="ledgerData?.transactions"
            :summary="ledgerData?.summary"
            :pagination="ledgerData?.pagination"
            :loading="loading"
            @page-change="onPageChange"
        />

        <!-- Empty State -->
        <div
            v-else-if="!loading"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12"
        >
            <div class="flex flex-col items-center text-center">
                <DocumentTextIcon class="w-16 h-16 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    Select Product and Location
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    Please select a product and location to view the detailed
                    stock ledger with all transactions.
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
import LedgerTable from "./LedgerTable.vue";
import FormInput from "../../Forms/FormInput.vue";
import FormSelect from "../../Forms/FormSelect.vue";
import { DocumentTextIcon, ArrowDownTrayIcon } from "@heroicons/vue/24/outline";

const stockBookStore = useStockBookStore();
const notificationStore = useNotificationStore();

// Local state
const selectedProductId = ref(null);
const selectedLocationId = ref(null);
const startDate = ref("");
const endDate = ref("");
const datePreset = ref("");
const showLedger = ref(false);
const loading = ref(false);
const exporting = ref(false);
const ledgerData = ref(null);

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

const period = computed(() => {
    if (!startDate.value || !endDate.value) return null;
    return {
        start_date: startDate.value,
        end_date: endDate.value,
    };
});

// Computed properties
const canLoadData = computed(() => {
    return (
        selectedProductId.value &&
        selectedLocationId.value &&
        startDate.value &&
        endDate.value
    );
});

const canExport = computed(() => {
    return (
        ledgerData.value &&
        ledgerData.value.transactions &&
        ledgerData.value.transactions.length > 0
    );
});

// Methods
const onProductChange = () => {
    stockBookStore.updateFilters({ product_id: selectedProductId.value });
    showLedger.value = false;
};

const onLocationChange = () => {
    stockBookStore.updateFilters({ location_id: selectedLocationId.value });
    showLedger.value = false;
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
    showLedger.value = false;
};

const loadLedgerData = async () => {
    if (!canLoadData.value) return;

    loading.value = true;
    try {
        await stockBookStore.fetchLedgerData();
        ledgerData.value = stockBookStore.ledgerData;
        showLedger.value = true;
    } catch (error) {
        notificationStore.error("Failed to load ledger data");
        console.error("Failed to load ledger data:", error);
        showLedger.value = false;
    } finally {
        loading.value = false;
    }
};

const onPageChange = async (page) => {
    stockBookStore.setPagination(page);
    await loadLedgerData();
};

const exportLedger = async () => {
    if (!canExport.value) return;

    exporting.value = true;
    try {
        await stockBookService.export({
            product_id: selectedProductId.value,
            location_id: selectedLocationId.value,
            start_date: startDate.value,
            end_date: endDate.value,
            format: "xlsx",
            type: "ledger",
        });
        notificationStore.success("Ledger exported successfully");
    } catch (error) {
        notificationStore.error("Failed to export ledger");
        console.error("Export error:", error);
    } finally {
        exporting.value = false;
    }
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
    if (stockBookStore.filters.product_id) {
        selectedProductId.value = stockBookStore.filters.product_id;
    }
    if (stockBookStore.filters.location_id) {
        selectedLocationId.value = stockBookStore.filters.location_id;
    }
    if (stockBookStore.filters.start_date) {
        startDate.value = stockBookStore.filters.start_date;
    }
    if (stockBookStore.filters.end_date) {
        endDate.value = stockBookStore.filters.end_date;
    }

    // Load ledger data if all filters are set
    if (canLoadData.value) {
        loadLedgerData();
    }
});
</script>
