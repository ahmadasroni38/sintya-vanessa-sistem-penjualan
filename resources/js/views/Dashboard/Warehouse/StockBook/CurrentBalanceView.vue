<template>
    <div class="space-y-6">
        <!-- Summary Cards -->
        <div v-if="stockBookStore.currentBalances.length > 0" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ summary.total_products }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-green-200 dark:border-green-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">In Stock</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                    {{ summary.in_stock }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-yellow-200 dark:border-yellow-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                    {{ summary.low_stock }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-red-200 dark:border-red-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Out of Stock</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                    {{ summary.out_of_stock }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-blue-200 dark:border-blue-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Overstock</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                    {{ summary.overstock }}
                </p>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input
                    v-model="searchQuery"
                    @input="handleSearch"
                    type="text"
                    placeholder="Search by product code or name..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                />
            </div>
            <div class="sm:w-48">
                <select
                    v-model="statusFilter"
                    @change="handleStatusFilterChange"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="all">All Status</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                    <option value="overstock">Overstock</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="stockBookStore.loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Loading stock balances...</p>
        </div>

        <!-- No Data State -->
        <div v-else-if="stockBookStore.currentBalances.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <ClipboardDocumentListIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No Stock Data</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Please select a Location to view current stock balances.
            </p>
        </div>

        <!-- Stock Balance Table -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Product Code
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Product Name
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Current Balance
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Min Stock
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Max Stock
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Last Transaction
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="item in filteredBalances" :key="item.product_id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ item.product_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ item.product_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold" :class="getBalanceClass(item.current_balance)">
                                {{ formatNumber(item.current_balance) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                {{ formatNumber(item.minimum_stock) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                {{ formatNumber(item.maximum_stock) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span :class="getStatusBadgeClass(item.status)">
                                    {{ formatStatus(item.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div v-if="item.last_transaction_date">
                                    {{ formatDate(item.last_transaction_date) }}
                                    <span class="text-xs text-gray-400">
                                        ({{ formatTransactionType(item.last_transaction_type) }})
                                    </span>
                                </div>
                                <span v-else class="text-gray-400">No transaction</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty Filtered Results -->
            <div v-if="filteredBalances.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                No products found matching your filters.
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useStockBookStore } from '../../../../stores/stockBookStore';
import { ClipboardDocumentListIcon } from '@heroicons/vue/24/outline';

const stockBookStore = useStockBookStore();
const searchQuery = ref('');
const statusFilter = ref('all');

const summary = computed(() => {
    if (!stockBookStore.currentBalances || stockBookStore.currentBalances.length === 0) {
        return {
            total_products: 0,
            in_stock: 0,
            low_stock: 0,
            out_of_stock: 0,
            overstock: 0,
        };
    }

    return {
        total_products: stockBookStore.currentBalances.length,
        in_stock: stockBookStore.currentBalances.filter(item => item.status === 'in_stock').length,
        low_stock: stockBookStore.currentBalances.filter(item => item.status === 'low_stock').length,
        out_of_stock: stockBookStore.currentBalances.filter(item => item.status === 'out_of_stock').length,
        overstock: stockBookStore.currentBalances.filter(item => item.status === 'overstock').length,
    };
});

const filteredBalances = computed(() => {
    let balances = stockBookStore.currentBalances || [];

    // Apply search filter
    if (searchQuery.value) {
        const search = searchQuery.value.toLowerCase();
        balances = balances.filter(item =>
            item.product_code.toLowerCase().includes(search) ||
            item.product_name.toLowerCase().includes(search)
        );
    }

    // Apply status filter
    if (statusFilter.value !== 'all') {
        balances = balances.filter(item => item.status === statusFilter.value);
    }

    return balances;
});

const formatNumber = (num) => {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat('id-ID').format(num);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const formatStatus = (status) => {
    const statuses = {
        'in_stock': 'In Stock',
        'low_stock': 'Low Stock',
        'out_of_stock': 'Out of Stock',
        'overstock': 'Overstock',
    };
    return statuses[status] || status;
};

const formatTransactionType = (type) => {
    const types = {
        'stock_in': 'Stock In',
        'mutation_in': 'Mutation In',
        'mutation_out': 'Mutation Out',
        'adjustment': 'Adjustment',
        'opname': 'Opname',
    };
    return types[type] || type;
};

const getBalanceClass = (balance) => {
    if (balance === 0) return 'text-red-600 dark:text-red-400';
    return 'text-gray-900 dark:text-white';
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'in_stock': 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        'low_stock': 'px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        'out_of_stock': 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        'overstock': 'px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    };
    return classes[status] || 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
};

const handleSearch = () => {
    // Search is reactive through computed property
};

const handleStatusFilterChange = () => {
    // Filter is reactive through computed property
};
</script>
