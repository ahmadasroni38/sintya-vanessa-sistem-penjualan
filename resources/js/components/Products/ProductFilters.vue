<template>
    <div
        v-if="show"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
    >
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <FormInput
                v-model="localFilters.search"
                label="Search"
                placeholder="Search by code or name..."
                @input="handleFilterChange"
            />
            <FormSelect
                v-model="localFilters.product_type"
                label="Product Type"
                :options="[
                    { value: '', label: 'All Types' },
                    ...productTypeOptions,
                ]"
                @change="handleFilterChange"
            />
            <FormSelect
                v-model="localFilters.is_active"
                label="Status"
                :options="[
                    { value: '', label: 'All Status' },
                    { value: '1', label: 'Active' },
                    { value: '0', label: 'Inactive' },
                ]"
                @change="handleFilterChange"
            />
            <div class="flex items-end gap-2">
                <button
                    @click="handleResetFilters"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Reset
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import FormInput from "../Forms/FormInput.vue";
import FormSelect from "../Forms/FormSelect.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["update:filters", "reset"]);

const localFilters = ref({
    search: "",
    product_type: "",
    is_active: "",
    ...props.filters,
});

const productTypeOptions = [
    { value: "finished_goods", label: "Finished Goods" },
    { value: "raw_material", label: "Raw Material" },
    { value: "consumable", label: "Consumable" },
];

// Watch for external filter changes
watch(
    () => props.filters,
    (newFilters) => {
        localFilters.value = { ...localFilters.value, ...newFilters };
    },
    { deep: true }
);

const handleFilterChange = () => {
    emit("update:filters", localFilters.value);
};

const handleResetFilters = () => {
    localFilters.value = {
        search: "",
        product_type: "",
        is_active: "",
    };
    emit("reset");
};
</script>
