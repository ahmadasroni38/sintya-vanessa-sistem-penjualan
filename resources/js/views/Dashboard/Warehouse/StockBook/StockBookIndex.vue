<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Buku Stock (Stock Book)
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Stock card reporting and real-time inventory tracking
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="handleExport"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Export
                </button>
                <button
                    @click="showFilterPanel = !showFilterPanel"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <FunnelIcon class="w-4 h-4" />
                    {{ showFilterPanel ? 'Hide Filters' : 'Show Filters' }}
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <StockBookStats />

        <!-- View Mode Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button
                        v-for="tab in viewTabs"
                        :key="tab.mode"
                        @click="stockBookStore.setViewMode(tab.mode)"
                        :class="[
                            stockBookStore.viewMode === tab.mode
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                        ]"
                    >
                        <component :is="tab.icon" class="w-5 h-5 inline-block mr-2" />
                        {{ tab.label }}
                    </button>
                </nav>
            </div>

            <!-- Filter Panel -->
            <div v-if="showFilterPanel" class="p-6 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                <StockBookFilters />
            </div>

            <!-- View Content -->
            <div class="p-6">
                <!-- Ledger View -->
                <LedgerView v-if="stockBookStore.isLedgerView" />

                <!-- Current Balance View -->
                <CurrentBalanceView v-else-if="stockBookStore.isCurrentBalanceView" />

                <!-- Movement Summary View -->
                <MovementSummaryView v-else-if="stockBookStore.isMovementSummaryView" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useStockBookStore } from '../../../../stores/stockBookStore';
import { ArrowDownTrayIcon, FunnelIcon } from '@heroicons/vue/24/outline';
import { BookOpenIcon, ClipboardDocumentListIcon, ChartBarIcon } from '@heroicons/vue/24/outline';
import StockBookStats from './components/StockBookStats.vue';
import StockBookFilters from './components/StockBookFilters.vue';
import LedgerView from './LedgerView.vue';
import CurrentBalanceView from './CurrentBalanceView.vue';
import MovementSummaryView from './MovementSummaryView.vue';

const stockBookStore = useStockBookStore();
const showFilterPanel = ref(true);

const viewTabs = [
    {
        mode: 'ledger',
        label: 'Ledger View',
        icon: BookOpenIcon,
    },
    {
        mode: 'current_balance',
        label: 'Current Balance',
        icon: ClipboardDocumentListIcon,
    },
    {
        mode: 'movement_summary',
        label: 'Movement Summary',
        icon: ChartBarIcon,
    },
];

const handleExport = () => {
    stockBookStore.exportData('xlsx');
};

onMounted(async () => {
    // Load initial data
    await stockBookStore.fetchStatistics();
    await stockBookStore.fetchProductsWithStock();
    await stockBookStore.fetchLocationsWithStock();

    // Set default date range to this month
    stockBookStore.applyQuickFilter('this_month');
});
</script>
