<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ title }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ description }}
                </p>
            </div>
            <div class="flex gap-2">
                <!-- Date Range Selector -->
                <div v-if="showDateRange" class="flex gap-2">
                    <input
                        v-model="startDate"
                        type="date"
                        class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                    <input
                        v-model="endDate"
                        type="date"
                        class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                </div>

                <!-- Single Date Selector -->
                <div v-else-if="showSingleDate">
                    <input
                        v-model="endDate"
                        type="date"
                        class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                </div>

                <!-- Generate Button -->
                <Button
                    @click="generateReport"
                    :loading="loading"
                    variant="primary"
                    size="sm"
                >
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m9-4h-4"
                        />
                    </svg>
                    Generate Report
                </Button>

                <!-- Export Button -->
                <div class="hs-dropdown relative inline-flex" v-if="showExport">
                    <button
                        id="hs-dropdown-export"
                        type="button"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        Export
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <div
                        class="hs-dropdown-menu transition-all duration-200 hs-dropdown-open:opacity-100 opacity-0 hidden min-w-48 bg-white shadow-lg rounded-lg border border-gray-200 p-2 dark:bg-gray-800 dark:border-gray-700 mt-2"
                    >
                        <button
                            @click="exportReport('pdf')"
                            class="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                />
                            </svg>
                            Export as PDF
                        </button>
                        <button
                            @click="exportReport('excel')"
                            class="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m9-4h-4"
                                />
                            </svg>
                            Export as Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div
                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto mb-4"
                    ></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Generating report...
                    </p>
                </div>
            </div>

            <!-- Error State -->
            <div
                v-else-if="error"
                class="flex items-center justify-center py-12"
            >
                <div class="text-center">
                    <svg
                        class="w-16 h-16 mx-auto text-red-400 mb-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                    >
                        Error Loading Report
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        {{ error }}
                    </p>
                    <Button @click="generateReport" variant="primary" size="sm">
                        Try Again
                    </Button>
                </div>
            </div>

            <!-- Report Data -->
            <div v-else>
                <slot :data="reportData" :period="period"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import Button from "../Base/Button.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: "",
    },
    showDateRange: {
        type: Boolean,
        default: true,
    },
    showSingleDate: {
        type: Boolean,
        default: false,
    },
    showExport: {
        type: Boolean,
        default: true,
    },
    defaultStartDate: {
        type: String,
        default: "",
    },
    defaultEndDate: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["generate", "export"]);

// Reactive data
const loading = ref(false);
const error = ref(null);
const reportData = ref(null);
const startDate = ref(props.defaultStartDate);
const endDate = ref(props.defaultEndDate);

// Computed properties
const period = computed(() => {
    if (props.showSingleDate) {
        return {
            end_date: endDate.value,
        };
    } else {
        return {
            start_date: startDate.value,
            end_date: endDate.value,
        };
    }
});

// Methods
const generateReport = async () => {
    if (!validateDates()) return;

    loading.value = true;
    error.value = null;

    try {
        await emit("generate", period.value);
    } catch (err) {
        error.value = err.message || "Failed to generate report";
    } finally {
        loading.value = false;
    }
};

const exportReport = async (format) => {
    if (!validateDates()) return;

    try {
        await emit("export", { ...period.value, format });
    } catch (err) {
        error.value = err.message || "Failed to export report";
    }
};

const validateDates = () => {
    if (props.showDateRange) {
        if (!startDate.value || !endDate.value) {
            error.value = "Please select both start and end dates";
            return false;
        }
        if (new Date(startDate.value) > new Date(endDate.value)) {
            error.value = "Start date must be before end date";
            return false;
        }
    } else if (props.showSingleDate) {
        if (!endDate.value) {
            error.value = "Please select an end date";
            return false;
        }
    }
    return true;
};

// Set default dates
onMounted(() => {
    if (!startDate.value && props.showDateRange) {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        startDate.value = firstDay.toISOString().split("T")[0];
    }

    if (!endDate.value) {
        const today = new Date();
        endDate.value = today.toISOString().split("T")[0];
    }
});

// Watch for date changes
watch([startDate, endDate], () => {
    error.value = null;
});

// Expose methods for parent component
defineExpose({
    setReportData: (data) => {
        reportData.value = data;
    },
    setLoading: (isLoading) => {
        loading.value = isLoading;
    },
    setError: (errorMessage) => {
        error.value = errorMessage;
    },
});
</script>
