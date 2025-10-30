<template>
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-if="show"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 space-y-6"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <AdjustmentsHorizontalIcon
                        class="w-5 h-5 text-blue-600 dark:text-blue-400"
                    />
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Advanced Filters
                    </h3>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="resetAllFilters"
                        class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                    >
                        Reset All
                    </button>
                    <button
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Quick Search -->
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Quick Search
                </label>
                <div class="relative">
                    <MagnifyingGlassIcon
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                    />
                    <input
                        v-model="localFilters.search"
                        type="text"
                        placeholder="Search by code, name, or description..."
                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        @input="debouncedSearch"
                    />
                    <button
                        v-if="localFilters.search"
                        @click="localFilters.search = ''"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2"
                    >
                        <XMarkIcon
                            class="w-4 h-4 text-gray-400 hover:text-gray-600"
                        />
                    </button>
                </div>
            </div>

            <!-- Main Filters Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Product Type -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Product Type
                    </label>
                    <select
                        v-model="localFilters.product_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        @change="emitFilters"
                    >
                        <option value="">All Types</option>
                        <option value="finished_goods">Finished Goods</option>
                        <option value="raw_material">Raw Material</option>
                        <option value="consumable">Consumable</option>
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Category
                    </label>
                    <select
                        v-model="localFilters.category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        @change="emitFilters"
                    >
                        <option value="">All Categories</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Status
                    </label>
                    <select
                        v-model="localFilters.is_active"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        @change="emitFilters"
                    >
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Price Range -->
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Price Range
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <input
                            v-model.number="localFilters.min_price"
                            type="number"
                            placeholder="Min Price"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            @input="emitFilters"
                        />
                    </div>
                    <div>
                        <input
                            v-model.number="localFilters.max_price"
                            type="number"
                            placeholder="Max Price"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            @input="emitFilters"
                        />
                    </div>
                </div>
            </div>

            <!-- Stock Range -->
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Stock Level
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <input
                            v-model.number="localFilters.min_stock"
                            type="number"
                            placeholder="Min Stock"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            @input="emitFilters"
                        />
                    </div>
                    <div>
                        <input
                            v-model.number="localFilters.max_stock"
                            type="number"
                            placeholder="Max Stock"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            @input="emitFilters"
                        />
                    </div>
                </div>
            </div>

            <!-- Date Range -->
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >
                    Created Date Range
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <input
                            v-model="localFilters.created_from"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            @change="emitFilters"
                        />
                    </div>
                    <div>
                        <input
                            v-model="localFilters.created_to"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            @change="emitFilters"
                        />
                    </div>
                </div>
            </div>

            <!-- Advanced Options -->
            <div class="space-y-3">
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Additional Options
                </label>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            v-model="localFilters.low_stock"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            @change="emitFilters"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Low Stock Only
                        </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            v-model="localFilters.has_selling_price"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            @change="emitFilters"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Has Selling Price
                        </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            v-model="localFilters.has_category"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            @change="emitFilters"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Has Category
                        </span>
                    </label>
                </div>
            </div>

            <!-- Active Filters Summary -->
            <div
                v-if="activeFiltersCount > 0"
                class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ activeFiltersCount }} filter(s) active
                </div>
                <button
                    @click="resetAllFilters"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    Clear all filters
                </button>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import {
    MagnifyingGlassIcon,
    XMarkIcon,
    AdjustmentsHorizontalIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["update:filters", "close", "apply"]);

// Local filters state
const localFilters = ref({
    search: "",
    product_type: "",
    category_id: "",
    is_active: "",
    min_price: null,
    max_price: null,
    min_stock: null,
    max_stock: null,
    created_from: "",
    created_to: "",
    low_stock: false,
    has_selling_price: false,
    has_category: false,
    ...props.filters,
});

// Watch for external filter changes
watch(
    () => props.filters,
    (newFilters) => {
        localFilters.value = { ...localFilters.value, ...newFilters };
    },
    { deep: true }
);

// Debounced search
let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        emitFilters();
    }, 500);
};

// Emit filters
const emitFilters = () => {
    const filters = {};

    // Only include non-empty filters
    Object.keys(localFilters.value).forEach((key) => {
        const value = localFilters.value[key];
        if (value !== "" && value !== null && value !== false) {
            filters[key] = value;
        }
    });

    emit("update:filters", filters);
    emit("apply", filters);
};

// Reset all filters
const resetAllFilters = () => {
    localFilters.value = {
        search: "",
        product_type: "",
        category_id: "",
        is_active: "",
        min_price: null,
        max_price: null,
        min_stock: null,
        max_stock: null,
        created_from: "",
        created_to: "",
        low_stock: false,
        has_selling_price: false,
        has_category: false,
    };
    emitFilters();
};

// Count active filters
const activeFiltersCount = computed(() => {
    let count = 0;
    Object.values(localFilters.value).forEach((value) => {
        if (value !== "" && value !== null && value !== false) {
            count++;
        }
    });
    return count;
});
</script>
