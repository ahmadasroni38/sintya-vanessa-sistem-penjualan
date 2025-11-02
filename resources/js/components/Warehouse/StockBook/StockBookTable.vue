<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Stock Cards
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ stockCards.length }} records
                    </span>
                    <button
                        @click="$emit('refresh')"
                        :disabled="loading || refreshing"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="Refresh"
                    >
                        <ArrowPathIcon v-if="!refreshing" class="w-5 h-5" />
                        <svg
                            v-else
                            class="animate-spin h-5 w-5 text-blue-600"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                                fill="none"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="p-8">
            <div class="flex items-center justify-center">
                <svg
                    class="animate-spin h-8 w-8 text-blue-600"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                        fill="none"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                <span class="ml-2 text-gray-600 dark:text-gray-400"
                    >Loading stock cards...</span
                >
            </div>
        </div>

        <!-- Table -->
        <div
            v-else-if="stockCards && stockCards.length > 0"
            class="overflow-x-auto"
        >
            <table
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
            >
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Date
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Product Code
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Product Name
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Location
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Transaction Type
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Reference
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Qty In
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Qty Out
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Balance
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Created By
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <tr
                        v-for="(stockCard, index) in stockCards"
                        :key="stockCard.id"
                        :class="
                            index % 2 === 0
                                ? 'bg-white dark:bg-gray-800'
                                : 'bg-gray-50 dark:bg-gray-900/20'
                        "
                    >
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                        >
                            {{ formatDate(stockCard.transaction_date) }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                        >
                            {{ stockCard.product_code }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                        >
                            {{ stockCard.product_name }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                        >
                            {{ stockCard.location_name }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getTransactionTypeClass(
                                        stockCard.transaction_type
                                    ),
                                ]"
                            >
                                {{
                                    getTransactionTypeLabel(
                                        stockCard.transaction_type
                                    )
                                }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono"
                        >
                            {{ stockCard.reference_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            <span
                                v-if="stockCard.quantity_in > 0"
                                class="text-green-600 dark:text-green-400 font-semibold"
                            >
                                +{{ formatNumber(stockCard.quantity_in) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            <span
                                v-if="stockCard.quantity_out > 0"
                                class="text-red-600 dark:text-red-400 font-semibold"
                            >
                                -{{ formatNumber(stockCard.quantity_out) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white"
                        >
                            {{ formatNumber(stockCard.balance) }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                        >
                            {{ stockCard.created_by }}
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    @click="$emit('view-details', stockCard)"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                                    title="View Details"
                                >
                                    <EyeIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-else class="p-8 text-center">
            <div class="flex flex-col items-center">
                <DocumentTextIcon class="w-16 h-16 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No stock cards found
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    No stock transactions found for the selected filters.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    DocumentTextIcon,
    EyeIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    stockCards: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    refreshing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["refresh", "view-details"]);

// Methods
const getTransactionTypeClass = (type) => {
    const classes = {
        stock_in:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        stock_out:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        mutation_in:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        mutation_out:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        adjustment_in:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        adjustment_out:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        opname_in:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
        opname_out:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
    };
    return (
        classes[type] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const getTransactionTypeLabel = (type) => {
    const labels = {
        stock_in: "Stock In",
        stock_out: "Stock Out",
        mutation_in: "Mutation In",
        mutation_out: "Mutation Out",
        adjustment_in: "Adjustment In",
        adjustment_out: "Adjustment Out",
        opname_in: "Opname In",
        opname_out: "Opname Out",
    };
    return labels[type] || type;
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0";
    return new Intl.NumberFormat("en-US").format(value);
};

const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>
