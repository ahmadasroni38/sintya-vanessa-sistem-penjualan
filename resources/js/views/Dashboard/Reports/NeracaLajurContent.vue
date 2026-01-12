<template>
    <div v-if="data && data.length > 0" class="p-6">
        <!-- Report Header -->
        <div class="mb-6 text-center">
            <h2
                class="text-xl font-bold text-gray-900 dark:text-white mb-2"
            >
                NERACA LAJUR
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Periode: {{ formatDate(period.start_date) }} s/d
                {{ formatDate(period.end_date) }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Dicetak pada {{ printDate }} oleh {{ printUser }}
            </p>
        </div>

        <!-- Worksheet Table -->
        <div class="overflow-x-auto">
            <table
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
            >
                <!-- Table Header -->
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Akun
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Saldo Awal
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Penyesuaian Debit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Penyesuaian Kredit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Saldo Disesuaikan Debit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Saldo Disesuaikan Kredit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Neraca Debit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Neraca Kredit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Laba Rugi Debit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        >
                            Laba Rugi Kredit
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <!-- Asset Accounts -->
                    <template
                        v-for="account in getAccountsByType('asset')"
                        :key="account.id"
                    >
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <td
                                class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ account.account_code }} -
                                {{ account.account_name }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(account.opening_balance || 0) }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_debit > 0 ? formatCurrency(account.adjustment_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_credit > 0 ? formatCurrency(account.adjustment_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_debit > 0 ? formatCurrency(account.adjusted_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_credit > 0 ? formatCurrency(account.adjusted_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.neraca_debit > 0 ? formatCurrency(account.neraca_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.neraca_credit > 0 ? formatCurrency(account.neraca_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                        </tr>
                    </template>

                    <!-- Liability Accounts -->
                    <template
                        v-for="account in getAccountsByType(
                            'liability'
                        )"
                        :key="account.id"
                    >
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <td
                                class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ account.account_code }} -
                                {{ account.account_name }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(account.opening_balance || 0) }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_debit > 0 ? formatCurrency(account.adjustment_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_credit > 0 ? formatCurrency(account.adjustment_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_debit > 0 ? formatCurrency(account.adjusted_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_credit > 0 ? formatCurrency(account.adjusted_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.neraca_debit > 0 ? formatCurrency(account.neraca_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.neraca_credit > 0 ? formatCurrency(account.neraca_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                        </tr>
                    </template>

                    <!-- Equity Accounts -->
                    <template
                        v-for="account in getAccountsByType('equity')"
                        :key="account.id"
                    >
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <td
                                class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ account.account_code }} -
                                {{ account.account_name }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(account.opening_balance || 0) }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_debit > 0 ? formatCurrency(account.adjustment_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_credit > 0 ? formatCurrency(account.adjustment_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_debit > 0 ? formatCurrency(account.adjusted_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_credit > 0 ? formatCurrency(account.adjusted_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.neraca_debit > 0 ? formatCurrency(account.neraca_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.neraca_credit > 0 ? formatCurrency(account.neraca_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                        </tr>
                    </template>

                    <!-- Revenue Accounts -->
                    <template
                        v-for="account in getAccountsByType('revenue')"
                        :key="account.id"
                    >
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <td
                                class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ account.account_code }} -
                                {{ account.account_name }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(account.opening_balance || 0) }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_debit > 0 ? formatCurrency(account.adjustment_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_credit > 0 ? formatCurrency(account.adjustment_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_debit > 0 ? formatCurrency(account.adjusted_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_credit > 0 ? formatCurrency(account.adjusted_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.laba_rugi_debit > 0 ? formatCurrency(account.laba_rugi_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.laba_rugi_credit > 0 ? formatCurrency(account.laba_rugi_credit) : '-' }}
                            </td>
                        </tr>
                    </template>

                    <!-- Expense Accounts -->
                    <template
                        v-for="account in getAccountsByType('expense')"
                        :key="account.id"
                    >
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <td
                                class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ account.account_code }} -
                                {{ account.account_name }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(account.opening_balance || 0) }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_debit > 0 ? formatCurrency(account.adjustment_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjustment_credit > 0 ? formatCurrency(account.adjustment_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_debit > 0 ? formatCurrency(account.adjusted_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.adjusted_credit > 0 ? formatCurrency(account.adjusted_credit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                -
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.laba_rugi_debit > 0 ? formatCurrency(account.laba_rugi_debit) : '-' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ account.laba_rugi_credit > 0 ? formatCurrency(account.laba_rugi_credit) : '-' }}
                            </td>
                        </tr>
                    </template>

                    <!-- Totals Row -->
                    <tr
                        class="bg-gray-100 dark:bg-gray-700 font-semibold"
                    >
                        <td
                            class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            TOTAL
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(getTotalOpeningBalance()) }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(getTotalAdjustmentDebit()) }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(getTotalAdjustmentCredit()) }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(getTotalAdjustedDebit())
                            }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(getTotalAdjustedCredit())
                            }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(getTotalNeracaDebit()) }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(getTotalNeracaCredit()) }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(getTotalLabaRugiDebit())
                            }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(getTotalLabaRugiCredit())
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Neraca Summary -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                >
                    Summary Neraca
                </h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400"
                            >Total Debit:</span
                        >
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(getTotalNeracaDebit()) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400"
                            >Total Kredit:</span
                        >
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(getTotalNeracaCredit()) }}
                        </span>
                    </div>
                    <div
                        class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-600"
                    >
                        <span
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                            >Balance:</span
                        >
                        <span
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(
                                    getTotalNeracaDebit() -
                                        getTotalNeracaCredit()
                                )
                            }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Laba Rugi Summary -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                >
                    Summary Laba Rugi
                </h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400"
                            >Total Debit:</span
                        >
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(getTotalLabaRugiDebit())
                            }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400"
                            >Total Kredit:</span
                        >
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(getTotalLabaRugiCredit())
                            }}
                        </span>
                    </div>
                    <div
                        class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-600"
                    >
                        <span
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            {{
                                getTotalLabaRugiCredit() >
                                getTotalLabaRugiDebit()
                                    ? "Laba Bersih"
                                    : "Rugi Bersih"
                            }}:
                        </span>
                        <span
                            class="text-sm font-semibold"
                            :class="
                                getTotalLabaRugiCredit() >
                                getTotalLabaRugiDebit()
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-red-600 dark:text-red-400'
                            "
                        >
                            {{
                                formatCurrency(
                                    Math.abs(
                                        getTotalLabaRugiCredit() -
                                            getTotalLabaRugiDebit()
                                    )
                                )
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    <!-- Signature Section -->
    <div class="mt-16 bg-white dark:bg-gray-800 print:mt-32 page-break-inside-avoid">
        <div class="text-right mb-8 print:mb-16 pr-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 print:text-base">Denpasar, {{ printDate.split(', ')[1] }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 print:gap-24 items-end print:p-8">
            <div class="text-center">
                <p class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-16 print:mb-24">Dibuat oleh,</p>
                <div class="relative mx-auto w-52 md:w-64 print:w-80 h-28 print:h-36 border-t-0 border-gray-900 flex flex-col justify-end items-center pt-10 print:pt-16">
                    <p class="font-bold text-sm md:text-base print:text-xl whitespace-nowrap">{{ printUser }}</p>
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 italic mt-1 print:mt-2">Akuntan</p>
                </div>
            </div>
            <div class="text-center">
                <p class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-16 print:mb-24">Diperiksa oleh,</p>
                <div class="relative mx-auto w-52 md:w-64 print:w-80 h-28 print:h-36 border-t-0 border-gray-900 flex flex-col justify-end items-center pt-10 print:pt-16">
                    <p class="font-bold text-sm md:text-base print:text-xl whitespace-nowrap">{{ reportSettings.checker_name }}</p>
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 italic mt-1 print:mt-2">Kepala Bagian Keuangan</p>
                </div>
            </div>
            <div class="text-center">
                <p class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-16 print:mb-24">Disetujui oleh,</p>
                <div class="relative mx-auto w-52 md:w-64 print:w-80 h-28 print:h-36 border-t-0 border-gray-900 flex flex-col justify-end items-center pt-10 print:pt-16">
                    <p class="font-bold text-sm md:text-base print:text-xl whitespace-nowrap">{{ reportSettings.approver_name }}</p>
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 italic mt-1 print:mt-2">Direktur Utama</p>
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
                Select a date range and generate the report to view the
                worksheet.
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    },
    period: {
        type: Object,
        default: () => ({})
    },
    reportSettings: {
        type: Object,
        default: () => ({
            checker_name: 'Nama Pemeriksa',
            approver_name: 'Nama Penyetuju'
        })
    },
    printDate: {
        type: String,
        default: ''
    },
    printUser: {
        type: String,
        default: ''
    }
});

// Helper functions
const getAccountsByType = (type) => {
    return props.data.filter((account) => account.account_type === type);
};

const getTotalOpeningBalance = () => {
    return props.data.reduce(
        (total, account) => total + Math.abs(account.opening_balance || 0),
        0
    );
};

const getTotalAdjustmentDebit = () => {
    return props.data.reduce(
        (total, account) => total + (account.adjustment_debit || 0),
        0
    );
};

const getTotalAdjustmentCredit = () => {
    return props.data.reduce(
        (total, account) => total + (account.adjustment_credit || 0),
        0
    );
};

const getTotalAdjustedDebit = () => {
    return props.data.reduce(
        (total, account) => total + (account.adjusted_debit || 0),
        0
    );
};

const getTotalAdjustedCredit = () => {
    return props.data.reduce(
        (total, account) => total + (account.adjusted_credit || 0),
        0
    );
};

const getTotalNeracaDebit = () => {
    return props.data.reduce(
        (total, account) => total + (account.neraca_debit || 0),
        0
    );
};

const getTotalNeracaCredit = () => {
    return props.data.reduce(
        (total, account) => total + (account.neraca_credit || 0),
        0
    );
};

const getTotalLabaRugiDebit = () => {
    return props.data.reduce(
        (total, account) => total + (account.laba_rugi_debit || 0),
        0
    );
};

const getTotalLabaRugiCredit = () => {
    return props.data.reduce(
        (total, account) => total + (account.laba_rugi_credit || 0),
        0
    );
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
