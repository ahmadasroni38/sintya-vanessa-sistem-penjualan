<template>
    <ReportLayout
        title="Laba Rugi"
        description="Laporan pendapatan dan beban perusahaan untuk periode tertentu"
        ref="reportLayout"
        @generate="handleGenerate"
        @export="handleExport"
    >
        <template #default="{ data, period }">
            <div v-if="data" class="p-6">
                <!-- Report Header -->
                <div class="mb-6 text-center">
                    <h2
                        class="text-xl font-bold text-gray-900 dark:text-white mb-2"
                    >
                        LAPORAN LABA RUGI
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Periode: {{ formatDate(period.start_date) }} s/d
                        {{ formatDate(period.end_date) }}
                    </p>
                </div>

                <!-- Income Statement Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <!-- Revenue Section -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    PENDAPATAN
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <!-- Operating Revenue -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td
                                    colspan="2"
                                    class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Pendapatan Usaha
                                </td>
                            </tr>
                            <template
                                v-for="account in getOperatingRevenue()"
                                :key="'revenue-' + account.code"
                            >
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td
                                        class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ account.name }}
                                    </td>
                                    <td
                                        class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{ formatCurrency(account.balance) }}
                                    </td>
                                </tr>
                            </template>
                            <tr
                                class="bg-gray-50 dark:bg-gray-700 font-semibold"
                            >
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Jumlah Pendapatan Usaha
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            getOperatingRevenueTotal()
                                        )
                                    }}
                                </td>
                            </tr>

                            <!-- Other Revenue -->
                            <template v-if="getOtherRevenue().length > 0">
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td
                                        colspan="2"
                                        class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        Pendapatan Lainnya
                                    </td>
                                </tr>
                                <template
                                    v-for="account in getOtherRevenue()"
                                    :key="'other-rev-' + account.code"
                                >
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                    >
                                        <td
                                            class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ account.name }}
                                        </td>
                                        <td
                                            class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatCurrency(account.balance)
                                            }}
                                        </td>
                                    </tr>
                                </template>
                                <tr
                                    class="bg-gray-50 dark:bg-gray-700 font-semibold"
                                >
                                    <td
                                        class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                    >
                                        Jumlah Pendapatan Lainnya
                                    </td>
                                    <td
                                        class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                getOtherRevenueTotal()
                                            )
                                        }}
                                    </td>
                                </tr>
                            </template>

                            <!-- Total Revenue -->
                            <tr class="bg-gray-200 dark:bg-gray-600 font-bold">
                                <td
                                    class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    JUMLAH PENDAPATAN
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getTotalRevenue()) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Expenses Section -->
                        <thead class="bg-gray-50 dark:bg-gray-700 mt-8">
                            <tr>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    BEBAN
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <!-- Cost of Goods Sold -->
                            <template v-if="getCostOfGoodsSold().length > 0">
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td
                                        colspan="2"
                                        class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        Harga Pokok Penjualan
                                    </td>
                                </tr>
                                <template
                                    v-for="account in getCostOfGoodsSold()"
                                    :key="'cogs-' + account.code"
                                >
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                    >
                                        <td
                                            class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ account.name }}
                                        </td>
                                        <td
                                            class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatCurrency(account.balance)
                                            }}
                                        </td>
                                    </tr>
                                </template>
                                <tr
                                    class="bg-gray-50 dark:bg-gray-700 font-semibold"
                                >
                                    <td
                                        class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                    >
                                        Jumlah Harga Pokok Penjualan
                                    </td>
                                    <td
                                        class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                getCostOfGoodsSoldTotal()
                                            )
                                        }}
                                    </td>
                                </tr>
                            </template>

                            <!-- Operating Expenses -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td
                                    colspan="2"
                                    class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Beban Usaha
                                </td>
                            </tr>
                            <template
                                v-for="account in getOperatingExpenses()"
                                :key="'expense-' + account.code"
                            >
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td
                                        class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ account.name }}
                                    </td>
                                    <td
                                        class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{ formatCurrency(account.balance) }}
                                    </td>
                                </tr>
                            </template>
                            <tr
                                class="bg-gray-50 dark:bg-gray-700 font-semibold"
                            >
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Jumlah Beban Usaha
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            getOperatingExpensesTotal()
                                        )
                                    }}
                                </td>
                            </tr>

                            <!-- Other Expenses -->
                            <template v-if="getOtherExpenses().length > 0">
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td
                                        colspan="2"
                                        class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        Beban Lainnya
                                    </td>
                                </tr>
                                <template
                                    v-for="account in getOtherExpenses()"
                                    :key="'other-exp-' + account.code"
                                >
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                    >
                                        <td
                                            class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ account.name }}
                                        </td>
                                        <td
                                            class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatCurrency(account.balance)
                                            }}
                                        </td>
                                    </tr>
                                </template>
                                <tr
                                    class="bg-gray-50 dark:bg-gray-700 font-semibold"
                                >
                                    <td
                                        class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                    >
                                        Jumlah Beban Lainnya
                                    </td>
                                    <td
                                        class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                getOtherExpensesTotal()
                                            )
                                        }}
                                    </td>
                                </tr>
                            </template>

                            <!-- Total Expenses -->
                            <tr class="bg-gray-200 dark:bg-gray-600 font-bold">
                                <td
                                    class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    JUMLAH BEBAN
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getTotalExpenses()) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Net Income Section -->
                        <tbody class="bg-white dark:bg-gray-800">
                            <tr class="bg-blue-100 dark:bg-blue-900/30">
                                <td
                                    class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    LABA (RUGI) BERSIH
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-right font-bold"
                                    :class="getNetIncomeClass()"
                                >
                                    {{ formatCurrency(getNetIncome()) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Revenue Card -->
                    <div
                        class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800"
                    >
                        <div class="flex items-center">
                            <div
                                class="p-2 bg-green-100 dark:bg-green-900/40 rounded-lg"
                            >
                                <svg
                                    class="w-6 h-6 text-green-600 dark:text-green-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm text-green-600 dark:text-green-400"
                                >
                                    Total Pendapatan
                                </p>
                                <p
                                    class="text-xl font-bold text-green-900 dark:text-green-100"
                                >
                                    {{ formatCurrency(getTotalRevenue()) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses Card -->
                    <div
                        class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800"
                    >
                        <div class="flex items-center">
                            <div
                                class="p-2 bg-red-100 dark:bg-red-900/40 rounded-lg"
                            >
                                <svg
                                    class="w-6 h-6 text-red-600 dark:text-red-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    Total Beban
                                </p>
                                <p
                                    class="text-xl font-bold text-red-900 dark:text-red-100"
                                >
                                    {{ formatCurrency(getTotalExpenses()) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Net Income Card -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800"
                    >
                        <div class="flex items-center">
                            <div
                                class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg"
                            >
                                <svg
                                    class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm text-blue-600 dark:text-blue-400"
                                >
                                    Laba/Rugi Bersih
                                </p>
                                <p
                                    class="text-xl font-bold"
                                    :class="getNetIncomeClass()"
                                >
                                    {{ formatCurrency(getNetIncome()) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profitability Indicators -->
                <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Indikator Profitabilitas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between">
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400"
                                >Gross Profit Margin:</span
                            >
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ getGrossProfitMargin() }}%
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400"
                                >Net Profit Margin:</span
                            >
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ getNetProfitMargin() }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex items-center justify-center py-12">
                <div class="text-center">
                    <svg
                        class="w-16 h-16 mx-auto text-gray-400 mb-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m9-4h-4"
                        />
                    </svg>
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                    >
                        No Data Available
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Select a date range and generate report to view income
                        statement.
                    </p>
                </div>
            </div>
        </template>
    </ReportLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import ReportLayout from "../../../components/Reports/ReportLayout.vue";
import reportService from "../../../services/reportService";
import { useNotificationStore } from "@/stores/notification";

const notification = useNotificationStore();
const reportLayout = ref(null);
const reportData = ref(null);

// Methods
const handleGenerate = async (period) => {
    try {
        const response = await reportService.getLabaRugi(
            period.start_date,
            period.end_date
        );
        reportData.value = response;
        reportLayout.value?.setReportData(response);
    } catch (error) {
        notification.error("Failed to generate Laba Rugi report");
        throw error;
    }
};

const handleExport = async (params) => {
    try {
        await reportService.exportLabaRugi(
            params.start_date,
            params.end_date,
            params.format
        );
        notification.success("Report exported successfully");
    } catch (error) {
        notification.error("Failed to export report");
        throw error;
    }
};

// Helper functions
const getOperatingRevenue = () => {
    if (!reportData.value?.revenues) return [];
    return reportData.value.revenues.filter(
        (account) =>
            account.name.toLowerCase().includes("penjualan") ||
            account.name.toLowerCase().includes("pendapatan") ||
            account.name.toLowerCase().includes("usaha") ||
            account.level <= 2
    );
};

const getOtherRevenue = () => {
    if (!reportData.value?.revenues) return [];
    return reportData.value.revenues.filter(
        (account) => !getOperatingRevenue().some((a) => a.code === account.code)
    );
};

const getCostOfGoodsSold = () => {
    if (!reportData.value?.expenses) return [];
    return reportData.value.expenses.filter(
        (account) =>
            account.name.toLowerCase().includes("pokok") ||
            account.name.toLowerCase().includes("hpp") ||
            account.name.toLowerCase().includes("persediaan") ||
            account.name.toLowerCase().includes("pembelian")
    );
};

const getOperatingExpenses = () => {
    if (!reportData.value?.expenses) return [];
    return reportData.value.expenses.filter(
        (account) =>
            !getCostOfGoodsSold().some((a) => a.code === account.code) &&
            (account.name.toLowerCase().includes("gaji") ||
                account.name.toLowerCase().includes("sewa") ||
                account.name.toLowerCase().includes("listrik") ||
                account.name.toLowerCase().includes("telepon") ||
                account.name.toLowerCase().includes("administrasi") ||
                account.name.toLowerCase().includes("pemasaran") ||
                account.name.toLowerCase().includes("operasional") ||
                account.level <= 2)
    );
};

const getOtherExpenses = () => {
    if (!reportData.value?.expenses) return [];
    return reportData.value.expenses.filter(
        (account) =>
            !getCostOfGoodsSold().some((a) => a.code === account.code) &&
            !getOperatingExpenses().some((a) => a.code === account.code)
    );
};

// Calculation functions
const getOperatingRevenueTotal = () => {
    return getOperatingRevenue().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getOtherRevenueTotal = () => {
    return getOtherRevenue().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getTotalRevenue = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.revenue || 0;
};

const getCostOfGoodsSoldTotal = () => {
    return getCostOfGoodsSold().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getOperatingExpensesTotal = () => {
    return getOperatingExpenses().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getOtherExpensesTotal = () => {
    return getOtherExpenses().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getTotalExpenses = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.expenses || 0;
};

const getNetIncome = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.net_income || 0;
};

const getNetIncomeClass = () => {
    return getNetIncome() >= 0
        ? "text-green-600 dark:text-green-400"
        : "text-red-600 dark:text-red-400";
};

const getGrossProfitMargin = () => {
    const grossProfit = getOperatingRevenueTotal() - getCostOfGoodsSoldTotal();
    const revenue = getOperatingRevenueTotal();
    if (revenue === 0) return "0.00";
    return ((grossProfit / revenue) * 100).toFixed(2);
};

const getNetProfitMargin = () => {
    const netIncome = getNetIncome();
    const revenue = getTotalRevenue();
    if (revenue === 0) return "0.00";
    return ((netIncome / revenue) * 100).toFixed(2);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>
