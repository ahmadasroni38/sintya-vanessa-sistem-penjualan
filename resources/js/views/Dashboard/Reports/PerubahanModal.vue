<template>
    <ReportLayout
        title="Perubahan Modal"
        description="Laporan perubahan ekuitas pemilik untuk periode tertentu"
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
                        LAPORAN PERUBAHAN MODAL
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Periode: {{ formatDate(period.start_date) }} s/d
                        {{ formatDate(period.end_date) }}
                    </p>
                </div>

                <!-- Statement of Changes in Equity Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    KETERANGAN
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                >
                                    JUMLAH (Rp)
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <!-- Beginning Balance -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Saldo Awal Modal
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(data.beginning_balance) }}
                                </td>
                            </tr>

                            <!-- Net Income -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                >
                                    Laba Bersih Periode Ini
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-right"
                                    :class="getNetIncomeClass()"
                                >
                                    {{ formatCurrency(data.net_income) }}
                                </td>
                            </tr>

                            <!-- Additional Investments (if any) -->
                            <tr
                                v-if="hasAdditionalInvestments()"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <td
                                    class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                >
                                    Tambahan Investasi Pemilik
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-right text-green-600 dark:text-green-400"
                                >
                                    {{
                                        formatCurrency(
                                            getAdditionalInvestments()
                                        )
                                    }}
                                </td>
                            </tr>

                            <!-- Withdrawals/Drawings -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                >
                                    Prive (Penarikan oleh Pemilik)
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-right text-red-600 dark:text-red-400"
                                >
                                    ({{ formatCurrency(data.prive) }})
                                </td>
                            </tr>

                            <!-- Other Changes (if any) -->
                            <tr
                                v-if="hasOtherChanges()"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <td
                                    class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                >
                                    Perubahan Lainnya
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-right text-blue-600 dark:text-blue-400"
                                >
                                    {{ formatCurrency(getOtherChanges()) }}
                                </td>
                            </tr>

                            <!-- Separator Line -->
                            <tr>
                                <td colspan="2" class="px-6 py-2">
                                    <div
                                        class="border-t-2 border-gray-300 dark:border-gray-600"
                                    ></div>
                                </td>
                            </tr>

                            <!-- Ending Balance -->
                            <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                <td
                                    class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                >
                                    Saldo Akhir Modal
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(data.ending_balance) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Beginning Balance Card -->
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
                                    class="text-xl font-bold text-blue-900 dark:text-blue-100"
                                >
                                    {{ formatCurrency(data.beginning_balance) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ending Balance Card -->
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
                                    Saldo Akhir
                                </p>
                                <p
                                    class="text-xl font-bold text-green-900 dark:text-green-100"
                                >
                                    {{ formatCurrency(data.ending_balance) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Changes Breakdown -->
                <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Rincian Perubahan
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400"
                                >Laba Bersih:</span
                            >
                            <span
                                class="text-sm font-medium"
                                :class="getNetIncomeClass()"
                            >
                                {{ formatCurrency(data.net_income) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400"
                                >Prive:</span
                            >
                            <span
                                class="text-sm font-medium text-red-600 dark:text-red-400"
                            >
                                ({{ formatCurrency(data.prive) }})
                            </span>
                        </div>
                        <div
                            v-if="hasAdditionalInvestments()"
                            class="flex justify-between items-center"
                        >
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400"
                                >Tambahan Investasi:</span
                            >
                            <span
                                class="text-sm font-medium text-green-600 dark:text-green-400"
                            >
                                {{ formatCurrency(getAdditionalInvestments()) }}
                            </span>
                        </div>
                        <div
                            v-if="hasOtherChanges()"
                            class="flex justify-between items-center"
                        >
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400"
                                >Perubahan Lainnya:</span
                            >
                            <span
                                class="text-sm font-medium text-blue-600 dark:text-blue-400"
                            >
                                {{ formatCurrency(getOtherChanges()) }}
                            </span>
                        </div>
                        <div
                            class="flex justify-between items-center pt-3 border-t border-gray-300 dark:border-gray-600"
                        >
                            <span
                                class="text-sm font-semibold text-gray-900 dark:text-white"
                                >Total Perubahan:</span
                            >
                            <span
                                class="text-sm font-bold text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(getTotalChanges()) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Equity Growth Chart -->
                <div
                    class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Pertumbuhan Modal
                    </h3>
                    <div class="relative">
                        <!-- Simple Bar Chart Representation -->
                        <div class="flex items-end justify-between h-32">
                            <div class="flex-1 flex flex-col items-center">
                                <div
                                    class="w-16 bg-blue-500 rounded-t"
                                    :style="{
                                        height:
                                            getBarHeight(
                                                data.beginning_balance
                                            ) + 'px',
                                    }"
                                ></div>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400 mt-2"
                                    >Awal</span
                                >
                                <span
                                    class="text-xs font-medium text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(data.beginning_balance) }}
                                </span>
                            </div>
                            <div class="flex-1 flex flex-col items-center">
                                <div
                                    class="w-16 bg-green-500 rounded-t"
                                    :style="{
                                        height:
                                            getBarHeight(data.ending_balance) +
                                            'px',
                                    }"
                                ></div>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400 mt-2"
                                    >Akhir</span
                                >
                                <span
                                    class="text-xs font-medium text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(data.ending_balance) }}
                                </span>
                            </div>
                        </div>
                        <!-- Growth Percentage -->
                        <div class="mt-4 text-center">
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400"
                                >Pertumbuhan:</span
                            >
                            <span
                                class="text-lg font-bold"
                                :class="getGrowthClass()"
                            >
                                {{ getGrowthPercentage() }}%
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
                        Select a date range and generate report to view
                        statement of changes in equity.
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
        const response = await reportService.getPerubahanModal(
            period.start_date,
            period.end_date
        );
        reportData.value = response;
        reportLayout.value?.setReportData(response);
    } catch (error) {
        notification.error("Failed to generate Perubahan Modal report");
        throw error;
    }
};

const handleExport = async (params) => {
    try {
        await reportService.exportPerubahanModal(
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
const hasAdditionalInvestments = () => {
    // This would be calculated from additional equity accounts
    return false; // Placeholder
};

const hasOtherChanges = () => {
    // This would be calculated from other equity changes
    return false; // Placeholder
};

const getAdditionalInvestments = () => {
    // This would be calculated from additional investment accounts
    return 0; // Placeholder
};

const getOtherChanges = () => {
    // This would be calculated from other equity change accounts
    return 0; // Placeholder
};

const getTotalChanges = () => {
    if (!reportData.value) return 0;
    return (
        reportData.value.net_income -
        reportData.value.prive +
        getAdditionalInvestments() +
        getOtherChanges()
    );
};

const getNetIncomeClass = () => {
    if (!reportData.value) return "text-gray-600 dark:text-gray-400";
    return reportData.value.net_income >= 0
        ? "text-green-600 dark:text-green-400"
        : "text-red-600 dark:text-red-400";
};

const getGrowthPercentage = () => {
    if (!reportData.value) return "0.00";
    const beginning = reportData.value.beginning_balance || 0;
    const ending = reportData.value.ending_balance || 0;
    if (beginning === 0) return "0.00";
    return (((ending - beginning) / Math.abs(beginning)) * 100).toFixed(2);
};

const getGrowthClass = () => {
    const growth = parseFloat(getGrowthPercentage());
    if (growth > 0) return "text-green-600 dark:text-green-400";
    if (growth < 0) return "text-red-600 dark:text-red-400";
    return "text-gray-600 dark:text-gray-400";
};

const getBarHeight = (value) => {
    if (!reportData.value) return 0;
    const maxValue = Math.max(
        reportData.value.beginning_balance || 0,
        reportData.value.ending_balance || 0
    );
    if (maxValue === 0) return 0;
    return Math.max(20, (value / maxValue) * 100);
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
