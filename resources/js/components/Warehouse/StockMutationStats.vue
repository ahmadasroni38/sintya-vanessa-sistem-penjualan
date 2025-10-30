<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total This Month -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Bulan Ini
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.total_this_month || 0 }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                >
                    <ArrowsRightLeftIcon
                        class="w-6 h-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
            </div>
        </div>

        <!-- Draft Count -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Draft
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.draft_count || 0 }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-gray-100 dark:bg-gray-900/20 rounded-lg flex items-center justify-center"
                >
                    <DocumentTextIcon
                        class="w-6 h-6 text-gray-600 dark:text-gray-400"
                    />
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Menunggu Persetujuan
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.pending_count || 0 }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center"
                >
                    <ClockIcon
                        class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                    />
                </div>
            </div>
        </div>

        <!-- In Transit -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Disetujui
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.approved_count || 0 }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center"
                >
                    <TruckIcon
                        class="w-6 h-6 text-purple-600 dark:text-purple-400"
                    />
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Selesai
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.completed_count || 0 }}
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

        <!-- Cancelled -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Dibatalkan
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.cancelled_count || 0 }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                >
                    <XCircleIcon
                        class="w-6 h-6 text-red-600 dark:text-red-400"
                    />
                </div>
            </div>
        </div>

        <!-- Total Items -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Item Dipindah
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ formatNumber(stats.total_items || 0) }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center"
                >
                    <CubeIcon
                        class="w-6 h-6 text-indigo-600 dark:text-indigo-400"
                    />
                </div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Transaksi
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.total_transactions || 0 }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-teal-100 dark:bg-teal-900/20 rounded-lg flex items-center justify-center"
                >
                    <ChartBarIcon
                        class="w-6 h-6 text-teal-600 dark:text-teal-400"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    ArrowsRightLeftIcon,
    DocumentTextIcon,
    ClockIcon,
    TruckIcon,
    CheckCircleIcon,
    XCircleIcon,
    CubeIcon,
    ChartBarIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    statistics: {
        type: Object,
        default: () => ({}),
    },
});

// Computed stats with default values
const stats = computed(() => ({
    total_this_month: props.statistics.total_this_month || 0,
    draft_count: props.statistics.draft_count || 0,
    pending_count: props.statistics.pending_count || 0,
    approved_count: props.statistics.approved_count || 0,
    completed_count: props.statistics.completed_count || 0,
    cancelled_count: props.statistics.cancelled_count || 0,
    total_items: props.statistics.total_items || 0,
    total_transactions: props.statistics.total_transactions || 0,
}));

// Format number with Indonesian locale
const formatNumber = (value) => {
    return new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value || 0);
};
</script>
