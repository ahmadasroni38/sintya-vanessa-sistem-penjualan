<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Transactions -->
        <div
            class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg"
                    >
                        <svg
                            class="w-7 h-7 text-blue-600 dark:text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <p
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                        >
                            Total Transactions
                        </p>
                        <p
                            class="text-3xl font-bold text-gray-900 dark:text-white mt-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                        >
                            {{ formatNumber(stats.total_transactions) }}
                        </p>
                    </div>
                </div>
                <div
                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-300"
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
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                        ></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Draft -->
        <div
            class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg"
                    >
                        <svg
                            class="w-7 h-7 text-yellow-600 dark:text-yellow-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <p
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                        >
                            Draft
                        </p>
                        <p
                            class="text-3xl font-bold text-gray-900 dark:text-white mt-1 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors"
                        >
                            {{ formatNumber(stats.draft_count) }}
                        </p>
                    </div>
                </div>
                <div
                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                >
                    <svg
                        class="w-5 h-5 text-yellow-600 dark:text-yellow-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                        ></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Posted -->
        <div
            class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg"
                    >
                        <svg
                            class="w-7 h-7 text-green-600 dark:text-green-400"
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
                    </div>
                    <div>
                        <p
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                        >
                            Posted
                        </p>
                        <p
                            class="text-3xl font-bold text-gray-900 dark:text-white mt-1 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors"
                        >
                            {{ formatNumber(stats.posted_count) }}
                        </p>
                    </div>
                </div>
                <div
                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                >
                    <svg
                        class="w-5 h-5 text-green-600 dark:text-green-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        ></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div
            class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl shadow-sm border-2 border-purple-200 dark:border-purple-800 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg"
                    >
                        <svg
                            class="w-7 h-7 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <p
                            class="text-xs font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wide"
                        >
                            Total Revenue
                        </p>
                        <p
                            class="text-2xl font-bold text-purple-900 dark:text-purple-100 mt-1"
                        >
                            {{ formatCurrency(stats.total_revenue) }}
                        </p>
                    </div>
                </div>
                <div
                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                >
                    <svg
                        class="w-5 h-5 text-purple-600 dark:text-purple-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                        ></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_transactions: 0,
            draft_count: 0,
            posted_count: 0,
            total_revenue: 0,
        }),
    },
});

const formatNumber = (value) => {
    return new Intl.NumberFormat("id-ID").format(value || 0);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};
</script>

<style scoped>
/* Smooth transitions for all elements */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
