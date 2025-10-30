<template>
    <ReportLayout
        title="Neraca"
        description="Laporan posisi keuangan perusahaan pada tanggal tertentu"
        :show-single-date="true"
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
                        NERACA
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Per tanggal: {{ formatDate(period.end_date) }}
                    </p>
                </div>

                <!-- Balance Sheet Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <!-- Assets Section -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    AKTIVA
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <!-- Current Assets -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td
                                    colspan="2"
                                    class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Aktiva Lancar
                                </td>
                            </tr>
                            <template
                                v-for="account in getCurrentAssets()"
                                :key="'asset-' + account.code"
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
                                    Jumlah Aktiva Lancar
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(getCurrentAssetsTotal())
                                    }}
                                </td>
                            </tr>

                            <!-- Fixed Assets -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td
                                    colspan="2"
                                    class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Aktiva Tetap
                                </td>
                            </tr>
                            <template
                                v-for="account in getFixedAssets()"
                                :key="'fixed-' + account.code"
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
                                    Jumlah Aktiva Tetap
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getFixedAssetsTotal()) }}
                                </td>
                            </tr>

                            <!-- Other Assets -->
                            <template v-if="getOtherAssets().length > 0">
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td
                                        colspan="2"
                                        class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        Aktiva Lainnya
                                    </td>
                                </tr>
                                <template
                                    v-for="account in getOtherAssets()"
                                    :key="'other-' + account.code"
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
                                        Jumlah Aktiva Lainnya
                                    </td>
                                    <td
                                        class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                getOtherAssetsTotal()
                                            )
                                        }}
                                    </td>
                                </tr>
                            </template>

                            <!-- Total Assets -->
                            <tr class="bg-gray-200 dark:bg-gray-600 font-bold">
                                <td
                                    class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    JUMLAH AKTIVA
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getTotalAssets()) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Liabilities & Equity Section -->
                        <thead class="bg-gray-50 dark:bg-gray-700 mt-8">
                            <tr>
                                <th
                                    colspan="2"
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    KEWAJIBAN DAN EKUITAS
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <!-- Current Liabilities -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td
                                    colspan="2"
                                    class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Kewajiban Lancar
                                </td>
                            </tr>
                            <template
                                v-for="account in getCurrentLiabilities()"
                                :key="'liability-' + account.code"
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
                                    Jumlah Kewajiban Lancar
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            getCurrentLiabilitiesTotal()
                                        )
                                    }}
                                </td>
                            </tr>

                            <!-- Long-term Liabilities -->
                            <template
                                v-if="getLongTermLiabilities().length > 0"
                            >
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td
                                        colspan="2"
                                        class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        Kewajiban Jangka Panjang
                                    </td>
                                </tr>
                                <template
                                    v-for="account in getLongTermLiabilities()"
                                    :key="'longterm-' + account.code"
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
                                        Jumlah Kewajiban Jangka Panjang
                                    </td>
                                    <td
                                        class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                getLongTermLiabilitiesTotal()
                                            )
                                        }}
                                    </td>
                                </tr>
                            </template>

                            <!-- Total Liabilities -->
                            <tr
                                class="bg-gray-50 dark:bg-gray-700 font-semibold"
                            >
                                <td
                                    class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    JUMLAH KEWAJIBAN
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getTotalLiabilities()) }}
                                </td>
                            </tr>

                            <!-- Equity -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td
                                    colspan="2"
                                    class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    Ekuitas
                                </td>
                            </tr>
                            <template
                                v-for="account in data.equity"
                                :key="'equity-' + account.code"
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
                                    Jumlah Ekuitas
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(getTotalEquity()) }}
                                </td>
                            </tr>

                            <!-- Total Liabilities & Equity -->
                            <tr class="bg-gray-200 dark:bg-gray-600 font-bold">
                                <td
                                    class="px-6 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    JUMLAH KEWAJIBAN DAN EKUITAS
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            getTotalLiabilitiesAndEquity()
                                        )
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Balance Verification -->
                <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg
                                class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <span
                                class="text-sm font-medium text-blue-900 dark:text-blue-100"
                            >
                                Balance Verification:
                            </span>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-sm text-blue-900 dark:text-blue-100"
                            >
                                Assets: {{ formatCurrency(getTotalAssets()) }} =
                                Liabilities + Equity:
                                {{
                                    formatCurrency(
                                        getTotalLiabilitiesAndEquity()
                                    )
                                }}
                            </span>
                            <div
                                v-if="isBalanced()"
                                class="text-sm font-semibold text-green-600 dark:text-green-400"
                            >
                                ✓ BALANCED
                            </div>
                            <div
                                v-else
                                class="text-sm font-semibold text-red-600 dark:text-red-400"
                            >
                                ✗ NOT BALANCED (Difference:
                                {{ formatCurrency(getBalanceDifference()) }})
                            </div>
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
                        Select a date and generate report to view balance sheet.
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
        const response = await reportService.getNeraca(period.end_date);
        reportData.value = response;
        reportLayout.value?.setReportData(response);
    } catch (error) {
        notification.error("Failed to generate Neraca report");
        throw error;
    }
};

const handleExport = async (params) => {
    try {
        await reportService.exportNeraca(params.end_date, params.format);
        notification.success("Report exported successfully");
    } catch (error) {
        notification.error("Failed to export report");
        throw error;
    }
};

// Helper functions
const getCurrentAssets = () => {
    if (!reportData.value?.assets) return [];
    return reportData.value.assets.filter(
        (account) =>
            account.name.toLowerCase().includes("kas") ||
            account.name.toLowerCase().includes("bank") ||
            account.name.toLowerCase().includes("piutang") ||
            account.name.toLowerCase().includes("persediaan") ||
            account.level <= 2
    );
};

const getFixedAssets = () => {
    if (!reportData.value?.assets) return [];
    return reportData.value.assets.filter(
        (account) =>
            account.name.toLowerCase().includes("tanah") ||
            account.name.toLowerCase().includes("gedung") ||
            account.name.toLowerCase().includes("kendaraan") ||
            account.name.toLowerCase().includes("peralatan") ||
            account.name.toLowerCase().includes("akumulasi") ||
            account.level >= 3
    );
};

const getOtherAssets = () => {
    if (!reportData.value?.assets) return [];
    return reportData.value.assets.filter(
        (account) =>
            !getCurrentAssets().some((a) => a.code === account.code) &&
            !getFixedAssets().some((a) => a.code === account.code)
    );
};

const getCurrentLiabilities = () => {
    if (!reportData.value?.liabilities) return [];
    return reportData.value.liabilities.filter(
        (account) =>
            account.name.toLowerCase().includes("utang") &&
            (account.name.toLowerCase().includes("dagang") ||
                account.name.toLowerCase().includes("usaha") ||
                account.name.toLowerCase().includes("bank") ||
                account.name.toLowerCase().includes("gaji") ||
                account.level <= 2)
    );
};

const getLongTermLiabilities = () => {
    if (!reportData.value?.liabilities) return [];
    return reportData.value.liabilities.filter(
        (account) =>
            account.name.toLowerCase().includes("utang") &&
            (account.name.toLowerCase().includes("jangka panjang") ||
                account.name.toLowerCase().includes("investasi") ||
                account.level >= 3)
    );
};

// Calculation functions
const getCurrentAssetsTotal = () => {
    return getCurrentAssets().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getFixedAssetsTotal = () => {
    return getFixedAssets().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getOtherAssetsTotal = () => {
    return getOtherAssets().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getTotalAssets = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.assets || 0;
};

const getCurrentLiabilitiesTotal = () => {
    return getCurrentLiabilities().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getLongTermLiabilitiesTotal = () => {
    return getLongTermLiabilities().reduce(
        (total, account) => total + (account.balance || 0),
        0
    );
};

const getTotalLiabilities = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.liabilities || 0;
};

const getTotalEquity = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.equity || 0;
};

const getTotalLiabilitiesAndEquity = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.liabilities_equity || 0;
};

const isBalanced = () => {
    return getTotalAssets() === getTotalLiabilitiesAndEquity();
};

const getBalanceDifference = () => {
    return Math.abs(getTotalAssets() - getTotalLiabilitiesAndEquity());
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
