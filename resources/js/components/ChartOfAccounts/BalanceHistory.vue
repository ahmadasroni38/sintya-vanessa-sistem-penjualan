<template>
    <div class="balance-history">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Balance History
            </h3>

            <div class="flex items-center space-x-4">
                <!-- Period Selector -->
                <div class="flex items-center space-x-3">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Period
                    </label>
                    <select
                        v-model="selectedPeriod"
                        @change="handlePeriodChange"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="1m">Last Month</option>
                        <option value="3m">Last 3 Months</option>
                        <option value="6m">Last 6 Months</option>
                        <option value="12m">Last Year</option>
                        <option value="ytd">Year to Date</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div
                    v-if="selectedPeriod === 'custom'"
                    class="flex items-center space-x-3"
                >
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            Start Date
                        </label>
                        <input
                            v-model="customStartDate"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            End Date
                        </label>
                        <input
                            v-model="customEndDate"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <Button
                        @click="applyPeriod"
                        :disabled="loading || !hasValidDateRange"
                        variant="secondary"
                        size="sm"
                    >
                        Apply
                    </Button>

                    <Button
                        @click="refreshBalanceHistory"
                        :loading="refreshing"
                        variant="secondary"
                        size="sm"
                    >
                        <span v-if="!refreshing">Refresh</span>
                        <span v-else>Refreshing...</span>
                    </Button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div
                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
            ></div>
            <span class="ml-2 text-blue-600">Loading balance history...</span>
        </div>

        <!-- Balance History Chart -->
        <div v-else-if="balanceHistory.length > 0" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Balance Trend
                </h3>

                <div class="h-64">
                    <canvas ref="balanceChart"></canvas>
                </div>
            </div>

            <!-- Balance Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Balance Summary
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Balance -->
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6">
                        <div class="text-center">
                            <div
                                class="text-3xl font-bold text-blue-600 dark:text-blue-300"
                            >
                                {{ formatCurrency(currentBalance) }}
                            </div>
                            <div
                                class="text-sm text-blue-600 dark:text-blue-300 mt-2"
                            >
                                Current Balance
                            </div>
                        </div>
                    </div>

                    <!-- Period Statistics -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h4
                            class="text-md font-medium text-gray-900 dark:text-white mb-4"
                        >
                            Period Statistics
                        </h4>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Opening Balance:</span
                                >
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            periodStats.opening_balance
                                        )
                                    }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Total Debits:</span
                                >
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(periodStats.total_debits)
                                    }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Total Credits:</span
                                >
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            periodStats.total_credits
                                        )
                                    }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Net Change:</span
                                >
                                <span
                                    class="text-sm font-medium"
                                    :class="
                                        getNetChangeClass(
                                            periodStats.net_change
                                        )
                                    "
                                >
                                    {{ formatCurrency(periodStats.net_change) }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Period:</span
                                >
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ periodStats.period_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance History Table -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
            >
                <div class="px-6 py-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Balance History
                    </h3>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Period
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Opening Balance
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Total Debits
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Total Credits
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Closing Balance
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Net Change
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="(history, index) in balanceHistory"
                                    :key="history.id"
                                    :class="
                                        index % 2 === 0
                                            ? 'bg-gray-50 dark:bg-gray-700'
                                            : 'bg-white dark:bg-gray-800'
                                    "
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ history.period_label }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                history.opening_balance
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(history.debit_total)
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(history.credit_total)
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                        :class="
                                            getBalanceClass(
                                                history.closing_balance
                                            )
                                        "
                                    >
                                        {{
                                            formatCurrency(
                                                history.closing_balance
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                        :class="
                                            getNetChangeClass(
                                                history.net_change
                                            )
                                        "
                                    >
                                        {{ formatCurrency(history.net_change) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
            <svg
                class="mx-auto h-12 w-12 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 0h6m-6 0h6m-6 0h6m2 5H7a2 2 0 01-2-2V9a2 2 0 01-2-2h2a2 2 0 01-2-2z"
                />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                No balance history found
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                No balance history has been recorded for this account yet.
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { useNotificationStore } from "@/stores/notification";
import chartOfAccountService from "@/services/chartOfAccountService";
import Button from "@/components/Base/Button.vue";

const props = defineProps({
    accountId: {
        type: Number,
        required: true,
    },
});

const notification = useNotificationStore();

// Reactive data
const balanceHistory = ref([]);
const loading = ref(false);
const refreshing = ref(false);
const selectedPeriod = ref("6m");
const customStartDate = ref("");
const customEndDate = ref("");
const balanceChart = ref(null);

// Computed properties
const currentBalance = computed(() => {
    return balanceHistory.value.length > 0
        ? balanceHistory.value[0].closing_balance
        : 0;
});

const periodStats = computed(() => {
    if (balanceHistory.value.length === 0) {
        return {
            opening_balance: 0,
            total_debits: 0,
            total_credits: 0,
            net_change: 0,
            period_label: "No Data",
        };
    }

    const latestHistory = balanceHistory.value[0];
    return {
        opening_balance: latestHistory?.opening_balance || 0,
        total_debits: latestHistory?.debit_total || 0,
        total_credits: latestHistory?.credit_total || 0,
        net_change:
            (latestHistory?.credit_total || 0) -
            (latestHistory?.debit_total || 0) -
            (latestHistory?.opening_balance || 0),
        period_label: getPeriodLabel(),
    };
});

const hasValidDateRange = computed(() => {
    if (selectedPeriod.value !== "custom") {
        return true;
    }

    return (
        customStartDate.value &&
        customEndDate.value &&
        new Date(customEndDate.value) >= new Date(customStartDate.value)
    );
});

// Methods
const fetchBalanceHistory = async () => {
    try {
        loading.value = true;

        let startDate, endDate;

        switch (selectedPeriod.value) {
            case "1m":
                startDate = new Date();
                startDate.setMonth(startDate.getMonth() - 1);
                endDate = new Date();
                break;
            case "3m":
                startDate = new Date();
                startDate.setMonth(startDate.getMonth() - 3);
                endDate = new Date();
                break;
            case "6m":
                startDate = new Date();
                startDate.setMonth(startDate.getMonth() - 6);
                endDate = new Date();
                break;
            case "12m":
                startDate = new Date();
                startDate.setMonth(startDate.getMonth() - 12);
                endDate = new Date();
                break;
            case "ytd":
                startDate = new Date(new Date().getFullYear(), 0, 1);
                endDate = new Date();
                break;
            case "custom":
                startDate = customStartDate.value;
                endDate = customEndDate.value;
                break;
            default:
                startDate = new Date();
                startDate.setMonth(startDate.getMonth() - 6);
                endDate = new Date();
                break;
        }

        const response = await chartOfAccountService.getBalanceHistory(
            props.accountId,
            {
                period_start: startDate.toISOString().split("T")[0],
                period_end: endDate.toISOString().split("T")[0],
            }
        );

        if (response.success) {
            balanceHistory.value = response.data;
        } else {
            throw new Error(
                response.message || "Failed to fetch balance history"
            );
        }
    } catch (error) {
        notification.error(error.message || "Failed to fetch balance history");
    } finally {
        loading.value = false;
    }
};

const handlePeriodChange = () => {
    if (selectedPeriod.value !== "custom") {
        customStartDate.value = "";
        customEndDate.value = "";
    }
};

const applyPeriod = () => {
    if (hasValidDateRange.value) {
        fetchBalanceHistory();
    } else {
        notification.warning("Please select a valid date range");
    }
};

const refreshBalanceHistory = async () => {
    try {
        refreshing.value = true;
        await fetchBalanceHistory();
    } catch (error) {
        notification.error(
            error.message || "Failed to refresh balance history"
        );
    } finally {
        refreshing.value = false;
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(amount);
};

const getPeriodLabel = () => {
    const labels = {
        "1m": "Last Month",
        "3m": "Last 3 Months",
        "6m": "Last 6 Months",
        "12m": "Last Year",
        ytd: "Year to Date",
        custom: "Custom Range",
    };

    return labels[selectedPeriod.value] || "Unknown";
};

const getBalanceClass = (balance) => {
    if (balance > 0) {
        return "text-green-600 dark:text-green-400";
    } else if (balance < 0) {
        return "text-red-600 dark:text-red-400";
    } else {
        return "text-gray-900 dark:text-white";
    }
};

const getNetChangeClass = (change) => {
    if (change > 0) {
        return "text-green-600 dark:text-green-400";
    } else if (change < 0) {
        return "text-red-600 dark:text-red-400";
    } else {
        return "text-gray-900 dark:text-white";
    }
};

// Chart initialization
const initBalanceChart = () => {
    nextTick(() => {
        if (balanceChart.value && balanceHistory.value.length > 0) {
            const ctx = balanceChart.value.getContext("2d");

            // Destroy existing chart
            if (window.balanceChartInstance) {
                window.balanceChartInstance.destroy();
            }

            // Create new chart
            window.balanceChartInstance = new Chart(ctx, {
                type: "line",
                data: {
                    labels: balanceHistory.value.map((h) => h.period_label),
                    datasets: [
                        {
                            label: "Balance",
                            data: balanceHistory.value.map(
                                (h) => h.closing_balance
                            ),
                            borderColor: "rgb(59, 130, 246)",
                            backgroundColor: "rgba(59, 130, 246, 0.1)",
                            tension: 0.1,
                            fill: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: "top",
                        },
                        tooltip: {
                            mode: "index",
                            intersect: false,
                            callbacks: {
                                label: function (context) {
                                    const label = context.dataset.label || "";
                                    const value = context.parsed.y;
                                    return `${label}: ${formatCurrency(value)}`;
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return formatCurrency(value);
                                },
                            },
                        },
                    },
                },
            });
        }
    });
};

// Lifecycle
onMounted(() => {
    fetchBalanceHistory();

    // Load Chart.js
    const script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/chart.js";
    script.async = true;
    document.head.appendChild(script);

    script.onload = () => {
        initBalanceChart();
    };
});
</script>

<style scoped>
.balance-history {
    @apply max-w-6xl mx-auto;
}

.balance-chart {
    @apply h-64 w-full;
}
</style>
