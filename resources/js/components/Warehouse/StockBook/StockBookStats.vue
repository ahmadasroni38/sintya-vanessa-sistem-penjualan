<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Transactions -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Transactions
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ formatNumber(stats.total_transactions) }}
                    </p>
                    <div
                        v-if="showTrend && stats.transactions_trend"
                        class="flex items-center mt-2"
                    >
                        <span
                            :class="[
                                'inline-flex items-center text-xs font-medium',
                                stats.transactions_trend > 0
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-red-600 dark:text-red-400',
                            ]"
                        >
                            <ArrowTrendingUpIcon
                                v-if="stats.transactions_trend > 0"
                                class="w-4 h-4 mr-1"
                            />
                            <ArrowTrendingDownIcon
                                v-else
                                class="w-4 h-4 mr-1"
                            />
                            {{ Math.abs(stats.transactions_trend) }}%
                        </span>
                        <span
                            class="text-xs text-gray-500 dark:text-gray-400 ml-1"
                        >
                            vs last period
                        </span>
                    </div>
                </div>
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                >
                    <BookOpenIcon
                        class="w-6 h-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
            </div>
        </div>

        <!-- Stock In -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Stock In
                    </p>
                    <p
                        class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1"
                    >
                        {{ formatNumber(stats.total_in) }}
                    </p>
                    <div
                        v-if="showPercentage && stats.total_transactions"
                        class="mt-2"
                    >
                        <div class="flex items-center">
                            <div
                                class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2"
                            >
                                <div
                                    class="bg-green-600 dark:bg-green-400 h-2 rounded-full"
                                    :style="{ width: `${stockInPercentage}%` }"
                                ></div>
                            </div>
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ stockInPercentage }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div
                    class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                >
                    <ArrowTrendingUpIcon
                        class="w-6 h-6 text-green-600 dark:text-green-400"
                    />
                </div>
            </div>
        </div>

        <!-- Stock Out -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Stock Out
                    </p>
                    <p
                        class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1"
                    >
                        {{ formatNumber(stats.total_out) }}
                    </p>
                    <div
                        v-if="showPercentage && stats.total_transactions"
                        class="mt-2"
                    >
                        <div class="flex items-center">
                            <div
                                class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2"
                            >
                                <div
                                    class="bg-red-600 dark:bg-red-400 h-2 rounded-full"
                                    :style="{ width: `${stockOutPercentage}%` }"
                                ></div>
                            </div>
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ stockOutPercentage }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div
                    class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                >
                    <ArrowTrendingDownIcon
                        class="w-6 h-6 text-red-600 dark:text-red-400"
                    />
                </div>
            </div>
        </div>

        <!-- Net Change -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Net Change
                    </p>
                    <p
                        :class="[
                            'text-2xl font-bold mt-1',
                            netChange >= 0
                                ? 'text-green-600 dark:text-green-400'
                                : 'text-red-600 dark:text-red-400',
                        ]"
                    >
                        {{ netChange >= 0 ? "+" : ""
                        }}{{ formatNumber(netChange) }}
                    </p>
                    <div class="flex items-center mt-2">
                        <span
                            :class="[
                                'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                netChange >= 0
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                    : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                            ]"
                        >
                            {{ netChange >= 0 ? "Increase" : "Decrease" }}
                        </span>
                    </div>
                </div>
                <div
                    :class="[
                        'w-12 h-12 rounded-lg flex items-center justify-center',
                        netChange >= 0
                            ? 'bg-green-100 dark:bg-green-900/20'
                            : 'bg-red-100 dark:bg-red-900/20',
                    ]"
                >
                    <ScaleIcon
                        :class="[
                            'w-6 h-6',
                            netChange >= 0
                                ? 'text-green-600 dark:text-green-400'
                                : 'text-red-600 dark:text-red-400',
                        ]"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row (Optional) -->
    <div
        v-if="showAdditionalStats"
        class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6"
    >
        <!-- Products with Stock -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Products with Stock
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ formatNumber(stats.total_products_with_stock || 0) }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center"
                >
                    <CubeIcon
                        class="w-6 h-6 text-purple-600 dark:text-purple-400"
                    />
                </div>
            </div>
        </div>

        <!-- Locations with Stock -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Locations with Stock
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{
                            formatNumber(stats.total_locations_with_stock || 0)
                        }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center"
                >
                    <BuildingOfficeIcon
                        class="w-6 h-6 text-indigo-600 dark:text-indigo-400"
                    />
                </div>
            </div>
        </div>

        <!-- Average Daily Movement -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Avg Daily Movement
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ formatNumber(averageDailyMovement) }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center"
                >
                    <ChartBarIcon
                        class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    BookOpenIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ScaleIcon,
    CubeIcon,
    BuildingOfficeIcon,
    ChartBarIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    stats: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    showTrend: {
        type: Boolean,
        default: false,
    },
    showPercentage: {
        type: Boolean,
        default: false,
    },
    showAdditionalStats: {
        type: Boolean,
        default: false,
    },
});

// Computed properties
const netChange = computed(() => {
    return (props.stats.total_in || 0) - (props.stats.total_out || 0);
});

const stockInPercentage = computed(() => {
    if (!props.stats.total_transactions) return 0;
    return Math.round(
        ((props.stats.total_in || 0) / props.stats.total_transactions) * 100
    );
});

const stockOutPercentage = computed(() => {
    if (!props.stats.total_transactions) return 0;
    return Math.round(
        ((props.stats.total_out || 0) / props.stats.total_transactions) * 100
    );
});

const averageDailyMovement = computed(() => {
    if (!props.stats.total_transactions) return 0;

    // Calculate days in period if start_date and end_date are available
    if (props.stats.period?.start_date && props.stats.period?.end_date) {
        const start = new Date(props.stats.period.start_date);
        const end = new Date(props.stats.period.end_date);
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        return Math.round(props.stats.total_transactions / days);
    }

    // Default to last 30 days if no period specified
    return Math.round(props.stats.total_transactions / 30);
});

// Methods
const formatNumber = (value) => {
    if (value === null || value === undefined) return "0";
    return new Intl.NumberFormat("en-US").format(value);
};
</script>
