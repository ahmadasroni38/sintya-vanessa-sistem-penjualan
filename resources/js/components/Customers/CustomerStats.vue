<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Customers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        Total Customers
                    </p>
                    <div v-if="loading" class="mt-2">
                        <div class="animate-pulse">
                            <div
                                class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16"
                            ></div>
                        </div>
                    </div>
                    <p
                        v-else
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-2"
                    >
                        {{ stats?.total_customers || 0 }}
                    </p>
                </div>
                <div v-if="loading" class="animate-pulse">
                    <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-lg">
                        <div
                            class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded"
                        ></div>
                    </div>
                </div>
                <div v-else class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                    <UsersIcon
                        class="w-6 h-6 text-blue-600 dark:text-blue-300"
                    />
                </div>
            </div>
        </div>

        <!-- Active Customers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        Active Customers
                    </p>
                    <div v-if="loading" class="mt-2">
                        <div class="animate-pulse">
                            <div
                                class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-12"
                            ></div>
                        </div>
                    </div>
                    <p
                        v-else
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-2"
                    >
                        {{ stats?.active_customers || 0 }}
                    </p>
                </div>
                <div v-if="loading" class="animate-pulse">
                    <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-lg">
                        <div
                            class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded"
                        ></div>
                    </div>
                </div>
                <div
                    v-else
                    class="bg-green-100 dark:bg-green-900 p-3 rounded-lg"
                >
                    <CheckCircleIcon
                        class="w-6 h-6 text-green-600 dark:text-green-300"
                    />
                </div>
            </div>
        </div>

        <!-- Inactive Customers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        Inactive Customers
                    </p>
                    <div v-if="loading" class="mt-2">
                        <div class="animate-pulse">
                            <div
                                class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-14"
                            ></div>
                        </div>
                    </div>
                    <p
                        v-else
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-2"
                    >
                        {{ stats?.inactive_customers || 0 }}
                    </p>
                </div>
                <div v-if="loading" class="animate-pulse">
                    <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-lg">
                        <div
                            class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded"
                        ></div>
                    </div>
                </div>
                <div v-else class="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg">
                    <XCircleIcon
                        class="w-6 h-6 text-gray-600 dark:text-gray-300"
                    />
                </div>
            </div>
        </div>

        <!-- New This Month -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        New This Month
                    </p>
                    <div v-if="loading" class="mt-2">
                        <div class="animate-pulse">
                            <div
                                class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-10"
                            ></div>
                        </div>
                    </div>
                    <p
                        v-else
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-2"
                    >
                        {{ stats?.new_customers_this_month || 0 }}
                    </p>
                </div>
                <div v-if="loading" class="animate-pulse">
                    <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-lg">
                        <div
                            class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded"
                        ></div>
                    </div>
                </div>
                <div
                    v-else
                    class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg"
                >
                    <SparklesIcon
                        class="w-6 h-6 text-purple-600 dark:text-purple-300"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    UsersIcon,
    CheckCircleIcon,
    XCircleIcon,
    SparklesIcon,
} from "@heroicons/vue/24/outline";

defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_customers: 0,
            active_customers: 0,
            inactive_customers: 0,
            new_customers_this_month: 0,
        }),
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>
