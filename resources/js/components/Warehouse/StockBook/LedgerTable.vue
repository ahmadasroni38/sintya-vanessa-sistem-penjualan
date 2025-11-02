<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <!-- Header with Summary -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Stock Ledger
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ product?.product_code }} -
                        {{ product?.product_name }} @ {{ location?.name }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Period
                        </p>
                        <p
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ formatDate(period?.start_date) }} -
                            {{ formatDate(period?.end_date) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div
            class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700"
        >
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Opening Balance
                </p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ formatNumber(openingBalance) }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total In</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">
                    +{{ formatNumber(summary?.total_in) }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Total Out
                </p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400">
                    -{{ formatNumber(summary?.total_out) }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Ending Balance
                </p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ formatNumber(summary?.ending_balance) }}
                </p>
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
                    >Loading ledger data...</span
                >
            </div>
        </div>

        <!-- Table -->
        <div
            v-else-if="transactions && transactions.length > 0"
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
                            Type
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
                            Notes
                        </th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <!-- Opening Balance Row -->
                    <tr class="bg-gray-50 dark:bg-gray-900/50 font-semibold">
                        <td
                            class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            {{ formatDate(period?.start_date) }}
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            Opening Balance
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            -
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-right text-gray-500 dark:text-gray-400"
                        >
                            -
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-right text-gray-500 dark:text-gray-400"
                        >
                            -
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"
                        >
                            {{ formatNumber(openingBalance) }}
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Balance carried forward
                        </td>
                    </tr>

                    <!-- Transaction Rows -->
                    <tr
                        v-for="(transaction, index) in transactions"
                        :key="transaction.id"
                        :class="
                            index % 2 === 0
                                ? 'bg-white dark:bg-gray-800'
                                : 'bg-gray-50 dark:bg-gray-900/20'
                        "
                    >
                        <td
                            class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            {{ formatDate(transaction.transaction_date) }}
                        </td>
                        <td class="px-6 py-3">
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getTransactionTypeClass(
                                        transaction.transaction_type
                                    ),
                                ]"
                            >
                                {{
                                    getTransactionTypeLabel(
                                        transaction.transaction_type
                                    )
                                }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-gray-900 dark:text-white font-mono"
                        >
                            {{ transaction.reference_number }}
                        </td>
                        <td class="px-6 py-3 text-sm text-right">
                            <span
                                v-if="transaction.quantity_in > 0"
                                class="text-green-600 dark:text-green-400 font-semibold"
                            >
                                +{{ formatNumber(transaction.quantity_in) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td class="px-6 py-3 text-sm text-right">
                            <span
                                v-if="transaction.quantity_out > 0"
                                class="text-red-600 dark:text-red-400 font-semibold"
                            >
                                -{{ formatNumber(transaction.quantity_out) }}
                            </span>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"
                        >
                            {{ formatNumber(transaction.balance) }}
                        </td>
                        <td
                            class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            {{ transaction.notes || "-" }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-else class="p-8 text-center">
            <div class="flex flex-col items-center">
                <DocumentTextIcon class="w-12 h-12 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No transactions found
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    No stock transactions found for the selected period and
                    filters.
                </p>
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="pagination && pagination.last_page > 1"
            class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"
        >
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing {{ pagination.from || 1 }} to
                    {{ pagination.to || 0 }} of {{ pagination.total }} results
                </div>
                <div class="flex gap-2">
                    <button
                        @click="
                            $emit('page-change', pagination.current_page - 1)
                        "
                        :disabled="pagination.current_page <= 1"
                        class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:border-gray-600 dark:hover:bg-gray-700"
                    >
                        Previous
                    </button>
                    <span
                        class="px-3 py-1 text-sm text-gray-700 dark:text-gray-300"
                    >
                        Page {{ pagination.current_page }} of
                        {{ pagination.last_page }}
                    </span>
                    <button
                        @click="
                            $emit('page-change', pagination.current_page + 1)
                        "
                        :disabled="
                            pagination.current_page >= pagination.last_page
                        "
                        class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:border-gray-600 dark:hover:bg-gray-700"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { DocumentTextIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    location: {
        type: Object,
        default: null,
    },
    period: {
        type: Object,
        default: null,
    },
    openingBalance: {
        type: Number,
        default: 0,
    },
    transactions: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: null,
    },
    pagination: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["page-change"]);

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
