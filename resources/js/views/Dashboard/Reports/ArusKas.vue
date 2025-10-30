<template>
    <ReportLayout
        title="Arus Kas"
        description="Laporan aliran masuk dan keluarnya kas untuk periode tertentu"
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
                        LAPORAN ARUS KAS
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Periode: {{ formatDate(period.start_date) }} s/d
                        {{ formatDate(period.end_date) }}
                    </p>
                </div>

                <!-- Cash Flow Summary Cards -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
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
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm text-blue-600 dark:text-blue-400"
                                >
                                    Saldo Awal
                                </p>
                                <p
                                    class="text-lg font-bold text-blue-900 dark:text-blue-100"
                                >
                                    {{ formatCurrency(data.beginning_balance) }}
                                </p>
                            </div>
                        </div>
                    </div>

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
                                        d="M7 11l5-5m0 0l5 5m-5-5v12"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm text-green-600 dark:text-green-400"
                                >
                                    Penerimaan
                                </p>
                                <p
                                    class="text-lg font-bold text-green-900 dark:text-green-100"
                                >
                                    {{ formatCurrency(getTotalInflow()) }}
                                </p>
                            </div>
                        </div>
                    </div>

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
                                        d="M17 13l-5 5m0 0l-5-5m5 5V6"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    Pengeluaran
                                </p>
                                <p
                                    class="text-lg font-bold text-red-900 dark:text-red-100"
                                >
                                    {{ formatCurrency(getTotalOutflow()) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800"
                    >
                        <div class="flex items-center">
                            <div
                                class="p-2 bg-purple-100 dark:bg-purple-900/40 rounded-lg"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-600 dark:text-purple-400"
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
                                    class="text-sm text-purple-600 dark:text-purple-400"
                                >
                                    Saldo Akhir
                                </p>
                                <p
                                    class="text-lg font-bold text-purple-900 dark:text-purple-100"
                                >
                                    {{ formatCurrency(data.ending_balance) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cash Flow Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <!-- Operating Activities -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    ARUS KAS DARI AKTIVITAS OPERASI
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Laba Bersih
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right"
                                    :class="getNetIncomeClass()"
                                >
                                    {{ formatCurrency(data.net_income) }}
                                </td>
                            </tr>

                            <!-- Adjustments for non-cash items -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Penyusutan Aktiva Tetap
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-green-600 dark:text-green-400"
                                >
                                    {{ formatCurrency(getDepreciation()) }}
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Kenaikan/penurunan Piutang Usaha
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-red-600 dark:text-red-400"
                                >
                                    ({{
                                        formatCurrency(
                                            getAccountsReceivableChange()
                                        )
                                    }})
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Kenaikan/penurunan Persediaan
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-red-600 dark:text-red-400"
                                >
                                    ({{ formatCurrency(getInventoryChange()) }})
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Kenaikan/penurunan Utang Usaha
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-green-600 dark:text-green-400"
                                >
                                    {{
                                        formatCurrency(
                                            getAccountsPayableChange()
                                        )
                                    }}
                                </td>
                            </tr>

                            <tr
                                class="bg-gray-100 dark:bg-gray-700 font-semibold"
                            >
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Jumlah Arus Kas dari Aktivitas Operasi
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getOperatingCashFlow()) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Investing Activities -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    ARUS KAS DARI AKTIVITAS INVESTASI
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Pembelian Aktiva Tetap
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-red-600 dark:text-red-400"
                                >
                                    ({{
                                        formatCurrency(
                                            getFixedAssetPurchases()
                                        )
                                    }})
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Penjualan Aktiva Tetap
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-green-600 dark:text-green-400"
                                >
                                    {{ formatCurrency(getFixedAssetSales()) }}
                                </td>
                            </tr>

                            <tr
                                class="bg-gray-100 dark:bg-gray-700 font-semibold"
                            >
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Jumlah Arus Kas dari Aktivitas Investasi
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getInvestingCashFlow()) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Financing Activities -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    ARUS KAS DARI AKTIVITAS PENDANAAN
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Tambahan Modal
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-green-600 dark:text-green-400"
                                >
                                    {{ formatCurrency(getAdditionalCapital()) }}
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Prive
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-red-600 dark:text-red-400"
                                >
                                    ({{ formatCurrency(getWithdrawals()) }})
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Pinjaman
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-green-600 dark:text-green-400"
                                >
                                    {{ formatCurrency(getLoans()) }}
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Pelunasan Pinjaman
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-red-600 dark:text-red-400"
                                >
                                    ({{ formatCurrency(getLoanPayments()) }})
                                </td>
                            </tr>

                            <tr
                                class="bg-gray-100 dark:bg-gray-700 font-semibold"
                            >
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Jumlah Arus Kas dari Aktivitas Pendanaan
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getFinancingCashFlow()) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Net Change and Reconciliation -->
                        <tbody class="bg-white dark:bg-gray-800">
                            <tr class="bg-gray-200 dark:bg-gray-600 font-bold">
                                <td
                                    class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    KENAIKAN/PENURUNAN KAS BERSIH
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(data.net_change) }}
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Saldo Awal Kas
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(data.beginning_balance) }}
                                </td>
                            </tr>

                            <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    SALDO AKHIR KAS
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(data.ending_balance) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Cash Account Details -->
                <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Rincian Rekening Kas
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-100 dark:bg-gray-600">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Kode Rekening
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Nama Rekening
                                    </th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Saldo Awal
                                    </th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Pergerakan
                                    </th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Saldo Akhir
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="account in data.cash_accounts"
                                    :key="account.code"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td
                                        class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white"
                                    >
                                        {{ account.code }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ account.name }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                account.beginning_balance
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{ formatCurrency(account.movement) }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                account.ending_balance
                                            )
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                        Select a date range and generate report to view cash
                        flow statement.
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
        const response = await reportService.getArusKas(
            period.start_date,
            period.end_date
        );
        reportData.value = response;
        reportLayout.value?.setReportData(response);
    } catch (error) {
        notification.error("Failed to generate Arus Kas report");
        throw error;
    }
};

const handleExport = async (params) => {
    try {
        await reportService.exportArusKas(
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

// Helper functions (these would typically be calculated from actual account data)
const getDepreciation = () => {
    return 0; // Placeholder - would calculate from depreciation accounts
};

const getAccountsReceivableChange = () => {
    return 0; // Placeholder - would calculate from AR accounts
};

const getInventoryChange = () => {
    return 0; // Placeholder - would calculate from inventory accounts
};

const getAccountsPayableChange = () => {
    return 0; // Placeholder - would calculate from AP accounts
};

const getFixedAssetPurchases = () => {
    return 0; // Placeholder - would calculate from fixed asset purchases
};

const getFixedAssetSales = () => {
    return 0; // Placeholder - would calculate from fixed asset sales
};

const getAdditionalCapital = () => {
    return 0; // Placeholder - would calculate from capital contributions
};

const getWithdrawals = () => {
    if (!reportData.value) return 0;
    return reportData.value.prive || 0;
};

const getLoans = () => {
    return 0; // Placeholder - would calculate from loan receipts
};

const getLoanPayments = () => {
    return 0; // Placeholder - would calculate from loan payments
};

// Calculation functions
const getOperatingCashFlow = () => {
    if (!reportData.value) return 0;
    return (
        reportData.value.net_income +
        getDepreciation() -
        getAccountsReceivableChange() -
        getInventoryChange() +
        getAccountsPayableChange()
    );
};

const getInvestingCashFlow = () => {
    return getFixedAssetSales() - getFixedAssetPurchases();
};

const getFinancingCashFlow = () => {
    return (
        getAdditionalCapital() -
        getWithdrawals() +
        getLoans() -
        getLoanPayments()
    );
};

const getTotalInflow = () => {
    if (!reportData.value) return 0;
    return (
        Math.max(0, reportData.value.net_change) +
        reportData.value.beginning_balance
    );
};

const getTotalOutflow = () => {
    if (!reportData.value) return 0;
    return Math.max(0, -reportData.value.net_change);
};

const getNetIncomeClass = () => {
    if (!reportData.value) return "text-gray-600 dark:text-gray-400";
    return reportData.value.net_income >= 0
        ? "text-green-600 dark:text-green-400"
        : "text-red-600 dark:text-red-400";
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
