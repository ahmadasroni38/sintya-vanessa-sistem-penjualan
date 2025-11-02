<template>
    <div class="space-y-6">
        <!-- Ledger Header Info -->
        <div v-if="stockBookStore.ledgerData" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Product</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ stockBookStore.ledgerData.product?.product_code }} - {{ stockBookStore.ledgerData.product?.product_name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Location</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ stockBookStore.ledgerData.location?.code }} - {{ stockBookStore.ledgerData.location?.name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Period</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ formatDate(stockBookStore.ledgerData.period?.start_date) }} - {{ formatDate(stockBookStore.ledgerData.period?.end_date) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div v-if="stockBookStore.ledgerData" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Opening Balance</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ formatNumber(stockBookStore.ledgerData.opening_balance) }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total In</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                    {{ formatNumber(stockBookStore.ledgerData.summary?.total_in) }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Out</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                    {{ formatNumber(stockBookStore.ledgerData.summary?.total_out) }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Ending Balance</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                    {{ formatNumber(stockBookStore.ledgerData.summary?.ending_balance) }}
                </p>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="stockBookStore.loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Loading ledger data...</p>
        </div>

        <!-- No Data State -->
        <div v-else-if="!stockBookStore.ledgerData" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <BookOpenIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No Ledger Data</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Please select Product, Location, and Date Range to view ledger.
            </p>
        </div>

        <!-- Ledger Table -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Transaction Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Reference
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                In
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Out
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Balance
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Notes
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Opening Balance Row -->
                        <tr class="bg-blue-50 dark:bg-blue-900/10">
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                Opening Balance
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                -
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                -
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-white">
                                {{ formatNumber(stockBookStore.ledgerData.opening_balance) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                -
                            </td>
                        </tr>

                        <!-- Transaction Rows -->
                        <tr v-for="transaction in stockBookStore.ledgerData.transactions" :key="transaction.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ formatDate(transaction.transaction_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="getTransactionTypeClass(transaction.transaction_type)">
                                    {{ formatTransactionType(transaction.transaction_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ transaction.reference_number || '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400 font-semibold">
                                {{ transaction.quantity_in > 0 ? formatNumber(transaction.quantity_in) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400 font-semibold">
                                {{ transaction.quantity_out > 0 ? formatNumber(transaction.quantity_out) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-white">
                                {{ formatNumber(transaction.balance) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ transaction.notes || '-' }}
                            </td>
                        </tr>

                        <!-- Ending Balance Row -->
                        <tr class="bg-blue-50 dark:bg-blue-900/10 font-semibold">
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                Ending Balance
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 dark:text-green-400">
                                {{ formatNumber(stockBookStore.ledgerData.summary?.total_in) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                                {{ formatNumber(stockBookStore.ledgerData.summary?.total_out) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                {{ formatNumber(stockBookStore.ledgerData.summary?.ending_balance) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                Net: {{ formatNumber(stockBookStore.ledgerData.summary?.net_change) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="stockBookStore.ledgerData.pagination && stockBookStore.ledgerData.pagination.total > 0" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing {{ stockBookStore.ledgerData.pagination.current_page }} of {{ stockBookStore.ledgerData.pagination.last_page }} pages
                        ({{ stockBookStore.ledgerData.pagination.total }} transactions)
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="previousPage"
                            :disabled="stockBookStore.ledgerData.pagination.current_page === 1"
                            class="px-3 py-1 text-sm border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Previous
                        </button>
                        <button
                            @click="nextPage"
                            :disabled="stockBookStore.ledgerData.pagination.current_page === stockBookStore.ledgerData.pagination.last_page"
                            class="px-3 py-1 text-sm border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useStockBookStore } from '../../../../stores/stockBookStore';
import { BookOpenIcon } from '@heroicons/vue/24/outline';

const stockBookStore = useStockBookStore();

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const formatNumber = (num) => {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat('id-ID').format(num);
};

const formatTransactionType = (type) => {
    const types = {
        'stock_in': 'Stock In',
        'mutation_in': 'Mutation In',
        'mutation_out': 'Mutation Out',
        'adjustment': 'Adjustment',
        'opname': 'Stock Opname',
    };
    return types[type] || type;
};

const getTransactionTypeClass = (type) => {
    const classes = {
        'stock_in': 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        'mutation_in': 'px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        'mutation_out': 'px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
        'adjustment': 'px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
        'opname': 'px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400',
    };
    return classes[type] || 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
};

const previousPage = () => {
    // Implement pagination logic
    console.log('Previous page');
};

const nextPage = () => {
    // Implement pagination logic
    console.log('Next page');
};
</script>
