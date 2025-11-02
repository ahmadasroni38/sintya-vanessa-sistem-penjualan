<template>
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 transform -translate-y-4"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform -translate-y-4"
    >
        <div
            v-if="show"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center"
                    >
                        <svg
                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                            ></path>
                        </svg>
                    </div>
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-white"
                    >
                        Filter Sales
                    </h3>
                </div>
                <span
                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                >
                    {{ activeFiltersCount }} Active Filters
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="relative group">
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                    >
                        <svg
                            class="w-4 h-4 inline mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            ></path>
                        </svg>
                        Search
                    </label>
                    <input
                        v-model="localFilters.search"
                        type="text"
                        placeholder="Transaction number..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200 group-hover:border-blue-400"
                        @input="handleFilterChange"
                    />
                    <div
                        v-if="localFilters.search"
                        class="absolute right-3 top-10 cursor-pointer"
                        @click="clearSearch"
                    >
                        <svg
                            class="w-5 h-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </div>
                </div>

                <!-- Status -->
                <div class="group">
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                    >
                        <svg
                            class="w-4 h-4 inline mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                        Status
                    </label>
                    <select
                        v-model="localFilters.status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200 group-hover:border-blue-400"
                        @change="handleFilterChange"
                    >
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="posted">Posted</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Location -->
                <div class="group">
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                    >
                        <svg
                            class="w-4 h-4 inline mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                            ></path>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            ></path>
                        </svg>
                        Location
                    </label>
                    <select
                        v-model="localFilters.location_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200 group-hover:border-blue-400"
                        @change="handleFilterChange"
                    >
                        <option value="">All Locations</option>
                        <option
                            v-for="location in locationOptions"
                            :key="location.id"
                            :value="location.id"
                        >
                            {{ location.code }} - {{ location.name }}
                        </option>
                    </select>
                </div>

                <!-- Customer -->
                <div class="group">
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                    >
                        <svg
                            class="w-4 h-4 inline mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            ></path>
                        </svg>
                        Customer
                    </label>
                    <select
                        v-model="localFilters.customer_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200 group-hover:border-blue-400"
                        @change="handleFilterChange"
                    >
                        <option value="">All Customers</option>
                        <option
                            v-for="customer in customerOptions"
                            :key="customer.id"
                            :value="customer.id"
                        >
                            {{ customer.customer_code }} -
                            {{ customer.customer_name }}
                        </option>
                    </select>
                </div>

                <!-- Start Date -->
                <div class="group">
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                    >
                        <svg
                            class="w-4 h-4 inline mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            ></path>
                        </svg>
                        Start Date
                    </label>
                    <input
                        v-model="localFilters.start_date"
                        type="date"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200 group-hover:border-blue-400"
                        @change="handleFilterChange"
                    />
                </div>

                <!-- End Date -->
                <div class="group">
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                    >
                        <svg
                            class="w-4 h-4 inline mr-1.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            ></path>
                        </svg>
                        End Date
                    </label>
                    <input
                        v-model="localFilters.end_date"
                        type="date"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200 group-hover:border-blue-400"
                        @change="handleFilterChange"
                    />
                </div>

                <!-- Reset Button -->
                <div class="flex items-end col-span-full md:col-span-2">
                    <button
                        @click="handleReset"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gradient-to-r from-gray-100 to-gray-200 border-2 border-gray-300 rounded-lg hover:from-gray-200 hover:to-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            ></path>
                        </svg>
                        Reset All Filters
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, watch, computed } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    filters: {
        type: Object,
        required: true,
    },
    locationOptions: {
        type: Array,
        default: () => [],
    },
    customerOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["update:filters", "reset"]);

const localFilters = ref({ ...props.filters });

watch(
    () => props.filters,
    (newFilters) => {
        localFilters.value = { ...newFilters };
    },
    { deep: true }
);

const activeFiltersCount = computed(() => {
    let count = 0;
    if (localFilters.value.search) count++;
    if (localFilters.value.status) count++;
    if (localFilters.value.location_id) count++;
    if (localFilters.value.customer_id) count++;
    if (localFilters.value.start_date) count++;
    if (localFilters.value.end_date) count++;
    return count;
});

const handleFilterChange = () => {
    emit("update:filters", localFilters.value);
};

const handleReset = () => {
    emit("reset");
};

const clearSearch = () => {
    localFilters.value.search = "";
    handleFilterChange();
};
</script>

<style scoped>
/* Custom transitions for smooth animations */
select option {
    padding: 0.5rem;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(0.5);
}

.dark input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8);
}
</style>
