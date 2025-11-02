<template>
    <Modal title="Export Stock Book Data" @close="$emit('close')" size="md">
        <form @submit.prevent="onSubmit" class="space-y-6">
            <!-- Export Type -->
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Export Type
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center">
                        <input
                            v-model="form.exportType"
                            type="radio"
                            value="current_view"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                        />
                        <span
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            Current View
                        </span>
                    </label>
                    <label class="flex items-center">
                        <input
                            v-model="form.exportType"
                            type="radio"
                            value="all_data"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                        />
                        <span
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            All Data
                        </span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ getExportTypeDescription() }}
                </p>
            </div>

            <!-- Date Range -->
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Date Range
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormSelect
                        v-model="form.datePreset"
                        label="Preset"
                        :options="datePresetOptions"
                        @change="onDatePresetChange"
                    />
                    <FormInput
                        v-model="form.startDate"
                        label="Start Date"
                        type="date"
                        :max="today"
                    />
                    <FormInput
                        v-model="form.endDate"
                        label="End Date"
                        type="date"
                        :max="today"
                        :min="form.startDate"
                    />
                </div>
            </div>

            <!-- Format -->
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Export Format
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center">
                        <input
                            v-model="form.format"
                            type="radio"
                            value="xlsx"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                        />
                        <span
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            Excel (.xlsx)
                        </span>
                    </label>
                    <label class="flex items-center">
                        <input
                            v-model="form.format"
                            type="radio"
                            value="csv"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                        />
                        <span
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            CSV (.csv)
                        </span>
                    </label>
                    <label class="flex items-center">
                        <input
                            v-model="form.format"
                            type="radio"
                            value="pdf"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                        />
                        <span
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            PDF (.pdf)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Additional Options -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Additional Options
                </h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input
                            v-model="form.includeHeaders"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                        />
                        <span
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            Include column headers
                        </span>
                    </label>
                    <label class="flex items-center">
                        <input
                            v-model="form.includeSummary"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                        />
                        <span
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            Include summary statistics
                        </span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div
                class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700"
            >
                <button
                    type="button"
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="loading || !isValidForm"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="loading" class="flex items-center">
                        <svg
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                        Exporting...
                    </span>
                    <span v-else>Export Data</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import Modal from "../../Overlays/Modal.vue";
import FormInput from "../../Forms/FormInput.vue";
import FormSelect from "../../Forms/FormSelect.vue";

const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "export"]);

// Form state
const form = ref({
    exportType: "current_view",
    datePreset: "",
    startDate: "",
    endDate: "",
    format: "xlsx",
    includeHeaders: true,
    includeSummary: false,
});

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
const isValidForm = computed(() => {
    return (
        form.value.format &&
        (form.value.exportType === "all_data" ||
            (form.value.startDate && form.value.endDate))
    );
});

// Methods
const getExportTypeDescription = () => {
    const descriptions = {
        current_view: "Export data from the current view with applied filters",
        all_data: "Export all stock book data regardless of current view",
    };
    return descriptions[form.value.exportType] || "";
};

const onDatePresetChange = () => {
    const preset = form.value.datePreset;
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

    form.value.startDate = start.toISOString().split("T")[0];
    form.value.endDate = end.toISOString().split("T")[0];
};

const onSubmit = () => {
    if (!isValidForm.value) return;

    const exportOptions = {
        exportType: form.value.exportType,
        datePreset: form.value.datePreset,
        startDate: form.value.startDate,
        endDate: form.value.endDate,
        format: form.value.format,
        includeHeaders: form.value.includeHeaders,
        includeSummary: form.value.includeSummary,
    };

    emit("export", exportOptions);
};

// Initialize form on mount
onMounted(() => {
    // Set default date range to last 30 days
    form.value.datePreset = "last_30_days";
    onDatePresetChange();
});
</script>
