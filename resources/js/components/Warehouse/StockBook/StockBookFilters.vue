<template>
    <div class="space-y-4">
        <!-- Filter Summary -->
        <div
            v-if="stockBookStore.hasActiveFilters"
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span
                        class="text-sm font-medium text-blue-800 dark:text-blue-200"
                    >
                        Active Filters ({{
                            stockBookStore.activeFiltersCount
                        }}):
                    </span>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-if="stockBookStore.filters.product_id"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            Product:
                            {{ stockBookStore.selectedProduct?.product_code }} -
                            {{ stockBookStore.selectedProduct?.product_name }}
                        </span>
                        <span
                            v-if="stockBookStore.filters.location_id"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            Location:
                            {{ stockBookStore.selectedLocation?.code }} -
                            {{ stockBookStore.selectedLocation?.name }}
                        </span>
                        <span
                            v-if="stockBookStore.filters.start_date"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            From:
                            {{ formatDate(stockBookStore.filters.start_date) }}
                        </span>
                        <span
                            v-if="stockBookStore.filters.end_date"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            To:
                            {{ formatDate(stockBookStore.filters.end_date) }}
                        </span>
                        <span
                            v-if="stockBookStore.filters.transaction_type"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            Type:
                            {{
                                getTransactionTypeLabel(
                                    stockBookStore.filters.transaction_type
                                )
                            }}
                        </span>
                        <span
                            v-if="stockBookStore.filters.search"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            Search: "{{ stockBookStore.filters.search }}"
                        </span>
                    </div>
                </div>
                <button
                    @click="clearAllFilters"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    Clear All
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Filters
                </h3>
                <button
                    v-if="!showAdvanced"
                    @click="showAdvanced = true"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                >
                    Show Advanced
                </button>
                <button
                    v-else
                    @click="showAdvanced = false"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                >
                    Hide Advanced
                </button>
            </div>

            <form @submit.prevent="applyFilters" class="space-y-4">
                <!-- Basic Filters -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                >
                    <FormSelect
                        v-model="localFilters.product_id"
                        label="Product"
                        :options="stockBookStore.productOptions"
                        clearable
                        searchable
                        placeholder="Select product..."
                    />

                    <FormSelect
                        v-model="localFilters.location_id"
                        label="Location"
                        :options="stockBookStore.locationOptions"
                        clearable
                        placeholder="Select location..."
                    />

                    <FormInput
                        v-model="localFilters.search"
                        label="Search"
                        type="text"
                        placeholder="Product code, name, or reference..."
                        @input="onSearchInput"
                    />

                    <FormSelect
                        v-model="localFilters.transaction_type"
                        label="Transaction Type"
                        :options="transactionTypeOptions"
                        clearable
                        placeholder="All types"
                    />
                </div>

                <!-- Advanced Filters -->
                <div
                    v-if="showAdvanced"
                    class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4"
                >
                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <FormSelect
                            v-model="datePreset"
                            label="Date Range"
                            :options="datePresetOptions"
                            @change="onDatePresetChange"
                        />

                        <FormInput
                            v-model="localFilters.start_date"
                            label="Start Date"
                            type="date"
                            :max="today"
                        />

                        <FormInput
                            v-model="localFilters.end_date"
                            label="End Date"
                            type="date"
                            :max="today"
                            :min="localFilters.start_date"
                        />
                    </div>

                    <!-- Status Filter (for Current Balance view) -->
                    <FormSelect
                        v-if="showStatusFilter"
                        v-model="localFilters.status_filter"
                        label="Stock Status"
                        :options="statusFilterOptions"
                    />
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="clearAllFilters"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                        >
                            Clear All
                        </button>
                        <button
                            type="button"
                            @click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                        >
                            Reset
                        </button>
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="$emit('cancel')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span
                                v-if="loading"
                                class="flex items-center gap-2"
                            >
                                <svg
                                    class="animate-spin h-4 w-4"
                                    viewBox="0 0 24 24"
                                >
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
                                Applying...
                            </span>
                            <span v-else>Apply Filters</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useStockBookStore } from "../../../stores/stockBookStore";
import FormInput from "../../Forms/FormInput.vue";
import FormSelect from "../../Forms/FormSelect.vue";

const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    showStatusFilter: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["apply", "cancel", "search"]);

const stockBookStore = useStockBookStore();

// Local state
const showAdvanced = ref(false);
const datePreset = ref("");
const localFilters = ref({});

// Options
const transactionTypeOptions = [
    { value: "stock_in", label: "Stock In" },
    { value: "stock_out", label: "Stock Out" },
    { value: "mutation_in", label: "Mutation In" },
    { value: "mutation_out", label: "Mutation Out" },
    { value: "adjustment_in", label: "Adjustment In" },
    { value: "adjustment_out", label: "Adjustment Out" },
    { value: "opname_in", label: "Opname In" },
    { value: "opname_out", label: "Opname Out" },
];

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

const statusFilterOptions = [
    { value: "all", label: "All Status" },
    { value: "in_stock", label: "In Stock" },
    { value: "low_stock", label: "Low Stock" },
    { value: "out_of_stock", label: "Out of Stock" },
    { value: "overstock", label: "Overstock" },
];

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

// Initialize local filters
const initializeFilters = () => {
    localFilters.value = { ...stockBookStore.filters };
};

// Watch for store changes
watch(() => stockBookStore.filters, initializeFilters, { deep: true });

// Methods
const applyFilters = () => {
    stockBookStore.updateFilters(localFilters.value);
    emit("apply");
};

const clearAllFilters = () => {
    stockBookStore.clearFilters();
    initializeFilters();
    datePreset.value = "";
    emit("apply");
};

const resetForm = () => {
    initializeFilters();
    datePreset.value = "";
};

const onDatePresetChange = () => {
    const preset = datePreset.value;
    const today = new Date();
    let startDate = new Date();
    let endDate = new Date();

    switch (preset) {
        case "today":
            startDate = today;
            endDate = today;
            break;
        case "yesterday":
            startDate = new Date(today);
            startDate.setDate(today.getDate() - 1);
            endDate = startDate;
            break;
        case "last_7_days":
            startDate = new Date(today);
            startDate.setDate(today.getDate() - 7);
            endDate = today;
            break;
        case "last_30_days":
            startDate = new Date(today);
            startDate.setDate(today.getDate() - 30);
            endDate = today;
            break;
        case "this_month":
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = today;
            break;
        case "last_month":
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case "this_year":
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = today;
            break;
        default:
            return; // Custom range, don't auto-set dates
    }

    localFilters.value.start_date = startDate.toISOString().split("T")[0];
    localFilters.value.end_date = endDate.toISOString().split("T")[0];
};

const onSearchInput = () => {
    emit("search", localFilters.value.search);
};

const getTransactionTypeLabel = (type) => {
    const labels = {
        stock_in: "Stock In",
        stock_out: "Stock Out",
        mutation_in: "Mutation In",
        mutation_out: "Mutation Out",
        adjustment_in: "Adjustment In",
        adjustment_out: "Adjustment Out",
        opname_in: "Opname In",
        opname_out: "Opname Out",
    };
    return labels[type] || type;
};

const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

onMounted(() => {
    initializeFilters();
});
</script>
