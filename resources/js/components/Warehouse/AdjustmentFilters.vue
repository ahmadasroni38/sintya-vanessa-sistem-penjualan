<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Filters
            </h3>
            <button
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
            >
                Clear all
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <FormSelect
                v-model="localFilters.status"
                label="Status"
                :options="statusOptions"
                @change="emitFilters"
            />

            <!-- Adjustment Type Filter -->
            <FormSelect
                v-model="localFilters.adjustment_type"
                label="Type"
                :options="typeOptions"
                @change="emitFilters"
            />

            <!-- Location Filter -->
            <FormSelect
                v-model="localFilters.location_id"
                label="Location"
                :options="locationOptions"
                @change="emitFilters"
            />

            <!-- Product Filter -->
            <FormSelect
                v-model="localFilters.product_id"
                label="Product"
                :options="productOptions"
                @change="emitFilters"
            />

            <!-- Date Range -->
            <FormInput
                v-model="localFilters.start_date"
                label="Start Date"
                type="date"
                @input="emitFilters"
            />

            <FormInput
                v-model="localFilters.end_date"
                label="End Date"
                type="date"
                @input="emitFilters"
            />

            <!-- Search -->
            <div class="md:col-span-2">
                <FormInput
                    v-model="localFilters.search"
                    label="Search"
                    placeholder="Search by adjustment number or reason..."
                    @input="debouncedEmit"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FormSelect from '../Forms/FormSelect.vue';
import FormInput from '../Forms/FormInput.vue';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({}),
    },
    locations: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:filters']);

const localFilters = ref({
    status: '',
    adjustment_type: '',
    location_id: '',
    product_id: '',
    start_date: '',
    end_date: '',
    search: '',
    ...props.filters,
});

const statusOptions = [
    { value: '', label: 'All Status' },
    { value: 'draft', label: 'Draft' },
    { value: 'posted', label: 'Posted' },
    { value: 'cancelled', label: 'Cancelled' },
];

const typeOptions = [
    { value: '', label: 'All Types' },
    { value: 'increase', label: 'Increase' },
    { value: 'decrease', label: 'Decrease' },
];

const locationOptions = computed(() => [
    { value: '', label: 'All Locations' },
    ...props.locations.map((loc) => ({
        value: loc.id,
        label: loc.name,
    })),
]);

const productOptions = computed(() => [
    { value: '', label: 'All Products' },
    ...props.products.map((product) => ({
        value: product.id,
        label: `${product.product_code} - ${product.product_name}`,
    })),
]);

const hasActiveFilters = computed(() => {
    return Object.values(localFilters.value).some((value) => value !== '');
});

let debounceTimer = null;

const emitFilters = () => {
    emit('update:filters', { ...localFilters.value });
};

const debouncedEmit = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        emitFilters();
    }, 500);
};

const clearFilters = () => {
    localFilters.value = {
        status: '',
        adjustment_type: '',
        location_id: '',
        product_id: '',
        start_date: '',
        end_date: '',
        search: '',
    };
    emitFilters();
};

watch(
    () => props.filters,
    (newFilters) => {
        localFilters.value = { ...localFilters.value, ...newFilters };
    },
    { deep: true }
);
</script>
