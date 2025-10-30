<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Products
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.total }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                >
                    <TagIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Active Products
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.active }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                >
                    <CheckCircleIcon
                        class="w-6 h-6 text-green-600 dark:text-green-400"
                    />
                </div>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Low Stock
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.low_stock || 0 }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center"
                >
                    <ExclamationTriangleIcon
                        class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                    />
                </div>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Product Types
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        3
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center"
                >
                    <FolderIcon
                        class="w-6 h-6 text-purple-600 dark:text-purple-400"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    TagIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    FolderIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            active: 0,
            low_stock: 0,
        }),
    },
});
</script>
