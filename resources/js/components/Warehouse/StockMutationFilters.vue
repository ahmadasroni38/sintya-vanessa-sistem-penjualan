<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
    >
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Filter
            </h3>
            <button
                v-if="hasActiveFilters"
                @click="resetFilters"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                Reset Filter
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <FormInput
                    v-model="filters.search"
                    label="Cari"
                    placeholder="Nomor transaksi, referensi, atau produk"
                    @update:modelValue="updateFilter('search', $event)"
                />
            </div>

            <!-- Status Filter -->
            <div>
                <FormSelect
                    v-model="filters.status"
                    label="Status"
                    :options="statusOptions"
                    @update:modelValue="updateFilter('status', $event)"
                />
            </div>

            <!-- From Location Filter -->
            <div>
                <FormSelect
                    v-model="filters.from_location_id"
                    label="Lokasi Asal"
                    :options="locationOptions"
                    @update:modelValue="
                        updateFilter('from_location_id', $event)
                    "
                />
            </div>

            <!-- To Location Filter -->
            <div>
                <FormSelect
                    v-model="filters.to_location_id"
                    label="Lokasi Tujuan"
                    :options="locationOptions"
                    @update:modelValue="updateFilter('to_location_id', $event)"
                />
            </div>

            <!-- Date Range -->
            <div>
                <FormInput
                    v-model="filters.start_date"
                    label="Tanggal Mulai"
                    type="date"
                    @update:modelValue="updateFilter('start_date', $event)"
                />
            </div>

            <div>
                <FormInput
                    v-model="filters.end_date"
                    label="Tanggal Selesai"
                    type="date"
                    @update:modelValue="updateFilter('end_date', $event)"
                />
            </div>

            <!-- Product Filter -->
            <div>
                <EnhancedFormSelect
                    v-model="filters.product_id"
                    label="Produk"
                    :options="productOptions"
                    :loading="loadingProducts"
                    @update:modelValue="updateFilter('product_id', $event)"
                />
            </div>

            <!-- Per Page -->
            <div>
                <FormSelect
                    v-model="filters.per_page"
                    label="Per Halaman"
                    :options="perPageOptions"
                    @update:modelValue="updateFilter('per_page', $event)"
                />
            </div>
        </div>

        <!-- Active Filters Display -->
        <div
            v-if="hasActiveFilters"
            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400"
                    >Filter aktif:</span
                >
                <span
                    v-for="(value, key) in activeFilters"
                    :key="key"
                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded"
                >
                    {{ getFilterLabel(key) }}: {{ getFilterValue(key, value) }}
                    <button
                        @click="removeFilter(key)"
                        class="hover:text-blue-600 dark:hover:text-blue-300"
                    >
                        <XMarkIcon class="w-3 h-3" />
                    </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import FormInput from "@/components/Forms/FormInput.vue";
import FormSelect from "@/components/Forms/FormSelect.vue";
import EnhancedFormSelect from "@/components/Forms/EnhancedFormSelect.vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    locations: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
    loadingProducts: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["filter-change"]);

// Filters state
const filters = ref({
    search: "",
    status: "",
    from_location_id: "",
    to_location_id: "",
    product_id: "",
    start_date: "",
    end_date: "",
    per_page: 15,
});

// Status options
const statusOptions = [
    { value: "", label: "Semua Status" },
    { value: "draft", label: "Draft" },
    { value: "pending", label: "Menunggu Persetujuan" },
    { value: "approved", label: "Disetujui" },
    { value: "completed", label: "Selesai" },
    { value: "cancelled", label: "Dibatalkan" },
];

// Per page options
const perPageOptions = [
    { value: 10, label: "10" },
    { value: 15, label: "15" },
    { value: 25, label: "25" },
    { value: 50, label: "50" },
    { value: 100, label: "100" },
];

// Location options
const locationOptions = computed(() => [
    { value: "", label: "Semua Lokasi" },
    ...props.locations.map((loc) => ({
        value: loc.id,
        label: `${loc.code} - ${loc.name}`,
    })),
]);

// Product options
const productOptions = computed(() => [
    { value: "", label: "Semua Produk" },
    ...props.products.map((product) => ({
        value: product.id,
        label: `${product.product_code} - ${product.product_name}`,
    })),
]);

// Check if there are active filters
const hasActiveFilters = computed(() => {
    return Object.keys(activeFilters.value).length > 0;
});

// Get active filters (non-empty values)
const activeFilters = computed(() => {
    const active = {};
    Object.keys(filters.value).forEach((key) => {
        const value = filters.value[key];
        if (
            value !== "" &&
            value !== null &&
            value !== undefined &&
            key !== "per_page"
        ) {
            active[key] = value;
        }
    });
    return active;
});

// Update filter
const updateFilter = (key, value) => {
    filters.value[key] = value;
    emit("filter-change", { ...filters.value });
};

// Remove specific filter
const removeFilter = (key) => {
    filters.value[key] = "";
    emit("filter-change", { ...filters.value });
};

// Reset all filters
const resetFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        if (key !== "per_page") {
            filters.value[key] = "";
        }
    });
    emit("filter-change", { ...filters.value });
};

// Get filter label
const getFilterLabel = (key) => {
    const labels = {
        search: "Cari",
        status: "Status",
        from_location_id: "Lokasi Asal",
        to_location_id: "Lokasi Tujuan",
        product_id: "Produk",
        start_date: "Tanggal Mulai",
        end_date: "Tanggal Selesai",
    };
    return labels[key] || key;
};

// Get filter display value
const getFilterValue = (key, value) => {
    switch (key) {
        case "status":
            const status = statusOptions.find((s) => s.value === value);
            return status ? status.label : value;
        case "from_location_id":
        case "to_location_id":
            const location = props.locations.find((l) => l.id === value);
            return location ? `${location.code} - ${location.name}` : value;
        case "product_id":
            const product = props.products.find((p) => p.id === value);
            return product
                ? `${product.product_code} - ${product.product_name}`
                : value;
        case "start_date":
        case "end_date":
            return new Date(value).toLocaleDateString("id-ID");
        default:
            return value;
    }
};

// Watch for external filter changes
watch(
    () => props.filters,
    (newFilters) => {
        if (newFilters) {
            filters.value = { ...filters.value, ...newFilters };
        }
    },
    { immediate: true, deep: true }
);
</script>
