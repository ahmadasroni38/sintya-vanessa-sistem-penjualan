<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Date Range
                </label>
                <FormSelect
                    v-model="selectedPreset"
                    :options="presetOptions"
                    @change="onPresetChange"
                />
            </div>
            <div>
                <FormInput
                    v-model="startDate"
                    label="Start Date"
                    type="date"
                    :max="endDate || today"
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

        <!-- Quick Select Buttons -->
        <div class="flex flex-wrap gap-2">
            <button
                v-for="preset in quickPresets"
                :key="preset.value"
                @click="selectPreset(preset.value)"
                :class="[
                    'px-3 py-1.5 text-xs font-medium rounded-md transition-colors duration-200',
                    selectedPreset === preset.value
                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600',
                ]"
            >
                {{ preset.label }}
            </button>
        </div>

        <!-- Custom Range Input -->
        <div
            v-if="selectedPreset === 'custom'"
            class="border-t border-gray-200 dark:border-gray-700 pt-4"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                    v-model="customStartDate"
                    label="Custom Start Date"
                    type="date"
                    :max="customEndDate || today"
                    @change="onCustomDateChange"
                />
                <FormInput
                    v-model="customEndDate"
                    label="Custom End Date"
                    type="date"
                    :max="today"
                    :min="customStartDate"
                    @change="onCustomDateChange"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import FormInput from "../../Forms/FormInput.vue";
import FormSelect from "../../Forms/FormSelect.vue";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            preset: "",
            startDate: "",
            endDate: "",
        }),
    },
    showQuickPresets: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

// Local state
const selectedPreset = ref("");
const startDate = ref("");
const endDate = ref("");
const customStartDate = ref("");
const customEndDate = ref("");

// Options
const presetOptions = [
    { value: "", label: "Select Range" },
    { value: "today", label: "Today" },
    { value: "yesterday", label: "Yesterday" },
    { value: "last_7_days", label: "Last 7 Days" },
    { value: "last_30_days", label: "Last 30 Days" },
    { value: "this_month", label: "This Month" },
    { value: "last_month", label: "Last Month" },
    { value: "this_year", label: "This Year" },
    { value: "custom", label: "Custom Range" },
];

const quickPresets = [
    { value: "today", label: "Today" },
    { value: "yesterday", label: "Yesterday" },
    { value: "last_7_days", label: "Last 7 Days" },
    { value: "last_30_days", label: "Last 30 Days" },
    { value: "this_month", label: "This Month" },
    { value: "last_month", label: "Last Month" },
];

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

// Computed property for v-model
const dateRange = computed({
    get: () => ({
        preset: selectedPreset.value,
        startDate: startDate.value,
        endDate: endDate.value,
    }),
    set: (value) => {
        selectedPreset.value = value.preset || "";
        startDate.value = value.startDate || "";
        endDate.value = value.endDate || "";
    },
});

// Methods
const onPresetChange = () => {
    const preset = selectedPreset.value;
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
        case "custom":
            // Use custom dates
            start = customStartDate.value
                ? new Date(customStartDate.value)
                : new Date();
            end = customEndDate.value
                ? new Date(customEndDate.value)
                : new Date();
            break;
        default:
            return; // No preset selected
    }

    startDate.value = start.toISOString().split("T")[0];
    endDate.value = end.toISOString().split("T")[0];

    emitChange();
};

const onDateChange = () => {
    // When dates are manually changed, clear preset
    selectedPreset.value = "";
    emitChange();
};

const onCustomDateChange = () => {
    startDate.value = customStartDate.value;
    endDate.value = customEndDate.value;
    emitChange();
};

const selectPreset = (preset) => {
    selectedPreset.value = preset;
    onPresetChange();
};

const emitChange = () => {
    const value = {
        preset: selectedPreset.value,
        startDate: startDate.value,
        endDate: endDate.value,
    };

    emit("update:modelValue", value);
    emit("change", value);
};

// Watch for external changes
watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue) {
            selectedPreset.value = newValue.preset || "";
            startDate.value = newValue.startDate || "";
            endDate.value = newValue.endDate || "";

            if (newValue.preset === "custom") {
                customStartDate.value = newValue.startDate || "";
                customEndDate.value = newValue.endDate || "";
            }
        }
    },
    { deep: true }
);

// Initialize on mount
onMounted(() => {
    if (props.modelValue) {
        selectedPreset.value = props.modelValue.preset || "";
        startDate.value = props.modelValue.startDate || "";
        endDate.value = props.modelValue.endDate || "";

        if (props.modelValue.preset === "custom") {
            customStartDate.value = props.modelValue.startDate || "";
            customEndDate.value = props.modelValue.endDate || "";
        }
    }
});
</script>
