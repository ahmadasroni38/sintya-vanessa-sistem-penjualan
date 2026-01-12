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

                <!-- View Toggle -->
                <div class="mb-6 flex justify-end">
                    <div class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-700 p-1">
                        <button
                            @click="viewFormat = 'staffel'"
                            :class="[
                                'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                                viewFormat === 'staffel'
                                    ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow'
                                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                            ]"
                        >
                            Staffel
                        </button>
                        <button
                            @click="viewFormat = 'skontro'"
                            :class="[
                                'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                                viewFormat === 'skontro'
                                    ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow'
                                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                            ]"
                        >
                            Skontro
                        </button>
                    </div>
                </div>

                <!-- Balance Sheet Table -->
                <div class="overflow-x-auto">
                    <!-- Staffel View (Standard) -->
                    <table v-if="viewFormat === 'staffel'" class="min-w-full">
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
                                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <td
                                    class="px-12 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    Laba (Rugi) Bersih
                                </td>
                                <td
                                    class="px-6 py-3 text-sm text-right"
                                    :class="getNetIncomeClass()"
                                >
                                    {{ formatCurrency(data.net_income || 0) }}
                                </td>
                            </tr>
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

                    <!-- Skontro View (Debit/Credit) -->
                    <table v-else class="min-w-full">
                        <!-- Assets Section -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    AKTIVA
                                </th>
                                <th class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    DEBIT
                                </th>
                                <th class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    KREDIT
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Current Assets -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td colspan="3" class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    Aktiva Lancar
                                </td>
                            </tr>
                            <template v-for="account in getCurrentAssets()" :key="'asset-' + account.code">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ account.name }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroDebit(account) }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroCredit(account) }}
                                    </td>
                                </tr>
                            </template>
                            <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                    Jumlah Aktiva Lancar
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getCurrentAssetsDebit()) }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getCurrentAssetsCredit()) }}
                                </td>
                            </tr>

                            <!-- Fixed Assets -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td colspan="3" class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    Aktiva Tetap
                                </td>
                            </tr>
                            <template v-for="account in getFixedAssets()" :key="'fixed-' + account.code">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ account.name }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroDebit(account) }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroCredit(account) }}
                                    </td>
                                </tr>
                            </template>
                            <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                    Jumlah Aktiva Tetap
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getFixedAssetsDebit()) }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getFixedAssetsCredit()) }}
                                </td>
                            </tr>

                            <!-- Other Assets -->
                            <template v-if="getOtherAssets().length > 0">
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td colspan="3" class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        Aktiva Lainnya
                                    </td>
                                </tr>
                                <template v-for="account in getOtherAssets()" :key="'other-' + account.code">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                            {{ account.name }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                            {{ getSkontroDebit(account) }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                            {{ getSkontroCredit(account) }}
                                        </td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                    <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                        Jumlah Aktiva Lainnya
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ formatCurrency(getOtherAssetsDebit()) }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ formatCurrency(getOtherAssetsCredit()) }}
                                    </td>
                                </tr>
                            </template>

                            <!-- Total Assets -->
                            <tr class="bg-gray-200 dark:bg-gray-600 font-bold">
                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">
                                    JUMLAH AKTIVA
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalAssetsDebit()) }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalAssetsCredit()) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Liabilities & Equity Section -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    KEWAJIBAN DAN EKUITAS
                                </th>
                                <th class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    DEBIT
                                </th>
                                <th class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    KREDIT
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Current Liabilities -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td colspan="3" class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    Kewajiban Lancar
                                </td>
                            </tr>
                            <template v-for="account in getCurrentLiabilities()" :key="'liability-' + account.code">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ account.name }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroDebit(account) }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroCredit(account) }}
                                    </td>
                                </tr>
                            </template>
                            <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                    Jumlah Kewajiban Lancar
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getCurrentLiabilitiesDebit()) }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getCurrentLiabilitiesCredit()) }}
                                </td>
                            </tr>

                            <!-- Long-term Liabilities -->
                            <template v-if="getLongTermLiabilities().length > 0">
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <td colspan="3" class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        Kewajiban Jangka Panjang
                                    </td>
                                </tr>
                                <template v-for="account in getLongTermLiabilities()" :key="'longterm-' + account.code">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                            {{ account.name }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                            {{ getSkontroDebit(account) }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                            {{ getSkontroCredit(account) }}
                                        </td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                    <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                        Jumlah Kewajiban Jangka Panjang
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ formatCurrency(getLongTermLiabilitiesDebit()) }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ formatCurrency(getLongTermLiabilitiesCredit()) }}
                                    </td>
                                </tr>
                            </template>

                            <!-- Total Liabilities -->
                            <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">
                                    JUMLAH KEWAJIBAN
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalLiabilitiesDebit()) }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalLiabilitiesCredit()) }}
                                </td>
                            </tr>

                            <!-- Equity -->
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td colspan="3" class="px-6 py-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    Ekuitas
                                </td>
                            </tr>
                            <template v-for="account in data.equity" :key="'equity-' + account.code">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ account.name }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroDebit(account) }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ getSkontroCredit(account) }}
                                    </td>
                                </tr>
                            </template>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                    Laba (Rugi) Bersih
                                </td>
                                <td class="px-6 py-3 text-sm text-right" :class="getNetIncomeClass()">
                                    {{ getNetIncomeSkontroDebit() }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right" :class="getNetIncomeClass()">
                                    {{ getNetIncomeSkontroCredit() }}
                                </td>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                <td class="px-12 py-3 text-sm text-gray-900 dark:text-white">
                                    Jumlah Ekuitas
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalEquityDebit()) }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalEquityCredit()) }}
                                </td>
                            </tr>

                            <!-- Total Liabilities & Equity -->
                            <tr class="bg-gray-200 dark:bg-gray-600 font-bold">
                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">
                                    JUMLAH KEWAJIBAN DAN EKUITAS
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalLiabilitiesAndEquityDebit()) }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(getTotalLiabilitiesAndEquityCredit()) }}
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
                                v-if="viewFormat === 'staffel'"
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
                            <span
                                v-else
                                class="text-sm text-blue-900 dark:text-blue-100"
                            >
                                Assets (D: {{ formatCurrency(getTotalAssetsDebit()) }} / C: {{ formatCurrency(getTotalAssetsCredit()) }}) =
                                Liabilities + Equity (D: {{ formatCurrency(getTotalLiabilitiesAndEquityDebit()) }} / C: {{ formatCurrency(getTotalLiabilitiesAndEquityCredit()) }})
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

            <!-- Signature Section -->
            <div v-if="data" class="mt-16 bg-white dark:bg-gray-800 print:mt-32 page-break-inside-avoid">
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
            <br>
            <br>
        </template>
    </ReportLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import ReportLayout from "../../../components/Reports/ReportLayout.vue";
import reportService from "../../../services/reportService";
import { useNotificationStore } from "@/stores/notification";
import { useAuthStore } from "@/stores/auth";
import { apiGet } from "@/utils/api";

const notification = useNotificationStore();
const reportLayout = ref(null);
const reportData = ref(null);
const viewFormat = ref('staffel'); // 'staffel' or 'skontro'

const printDate = computed(() => {
  const now = new Date();
  return `Makassar, ${now.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric"
  })}`;
});

const authStore = useAuthStore();

const printUser = computed(() => authStore.user?.name || authStore.user?.full_name || 'Nama Pengguna');

const reportSettings = ref({
  checker_name: 'Nama Pemeriksa',
  approver_name: 'Nama Penyetuju'
});

// Methods
const handleGenerateSedangClick = ref(false);
const handleGenerate = async (period) => {
    try {
        if(handleGenerateSedangClick.value) {
            return
        }
        handleGenerateSedangClick.value = true
        const response = await reportService.getNeraca(period.end_date);

        // Extract data from response
        if (response.success && response.data) {
            reportData.value = response.data;
            reportLayout.value?.setReportData(response.data);
        } else {
            reportData.value = response;
            reportLayout.value?.setReportData(response);
        }

        // Fetch report signature settings
        try {
          const settingsResponse = await apiGet('/settings');

          if (settingsResponse.success) {

            const settings = settingsResponse.data;
            reportSettings.value.checker_name = settings.report_checker_name || reportSettings.value.checker_name;
            reportSettings.value.approver_name = settings.report_approver_name || reportSettings.value.approver_name;
          }
        } catch (settingsErr) {
          console.warn('Failed to fetch settings:', settingsErr);
        }
    } catch (error) {
        notification.error("Failed to generate Neraca report");
        throw error;
    } finally {
        handleGenerateSedangClick.value = false
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
    // Total equity includes equity accounts + net income
    return (reportData.value.totals.equity || 0) + (reportData.value.net_income || 0);
};

const getTotalLiabilitiesAndEquity = () => {
    if (!reportData.value?.totals) return 0;
    return reportData.value.totals.liabilities_equity || 0;
};

const getNetIncomeClass = () => {
    const netIncome = reportData.value?.net_income || 0;
    return netIncome >= 0
        ? 'text-green-600 dark:text-green-400'
        : 'text-red-600 dark:text-red-400';
};

const getNetIncomeSkontroDebit = () => {
    const netIncome = reportData.value?.net_income || 0;
    // Net income is credit normal (revenue - expense)
    // If positive (profit), goes to credit. If negative (loss), goes to debit
    return netIncome < 0 ? formatCurrency(Math.abs(netIncome)) : '-';
};

const getNetIncomeSkontroCredit = () => {
    const netIncome = reportData.value?.net_income || 0;
    // Net income is credit normal (revenue - expense)
    // If positive (profit), goes to credit. If negative (loss), goes to debit
    return netIncome >= 0 ? formatCurrency(netIncome) : '-';
};

const isBalanced = () => {
    if (viewFormat.value === 'staffel') {
        return getTotalAssets() === getTotalLiabilitiesAndEquity();
    } else {
        // For Skontro format, check if total debit equals total credit
        return getTotalAssetsDebit() + getTotalLiabilitiesAndEquityDebit() ===
               getTotalAssetsCredit() + getTotalLiabilitiesAndEquityCredit();
    }
};

const getBalanceDifference = () => {
    if (viewFormat.value === 'staffel') {
        return Math.abs(getTotalAssets() - getTotalLiabilitiesAndEquity());
    } else {
        // For Skontro format, calculate difference between total debit and total credit
        const totalDebit = getTotalAssetsDebit() + getTotalLiabilitiesAndEquityDebit();
        const totalCredit = getTotalAssetsCredit() + getTotalLiabilitiesAndEquityCredit();
        return Math.abs(totalDebit - totalCredit);
    }
};

// Skontro view helper functions - properly handle debit/credit based on balance and normal_balance
const getSkontroDebit = (account) => {
    const balance = account.balance || 0;
    // If balance follows normal balance (positive for debit, positive for credit)
    // OR if balance opposes normal balance (negative for debit goes to credit, negative for credit goes to debit)
    if (account.normal_balance === 'debit') {
        // Debit normal: positive balance in debit, negative balance in credit
        return balance >= 0 ? formatCurrency(balance) : '-';
    } else {
        // Credit normal: positive balance in credit, negative balance in debit
        return balance < 0 ? formatCurrency(Math.abs(balance)) : '-';
    }
};

const getSkontroCredit = (account) => {
    const balance = account.balance || 0;
    if (account.normal_balance === 'debit') {
        // Debit normal: positive balance in debit, negative balance in credit
        return balance < 0 ? formatCurrency(Math.abs(balance)) : '-';
    } else {
        // Credit normal: positive balance in credit, negative balance in debit
        return balance >= 0 ? formatCurrency(balance) : '-';
    }
};

const getSkontroDebitValue = (account) => {
    const balance = account.balance || 0;
    if (account.normal_balance === 'debit') {
        return balance >= 0 ? balance : 0;
    } else {
        return balance < 0 ? Math.abs(balance) : 0;
    }
};

const getSkontroCreditValue = (account) => {
    const balance = account.balance || 0;
    if (account.normal_balance === 'debit') {
        return balance < 0 ? Math.abs(balance) : 0;
    } else {
        return balance >= 0 ? balance : 0;
    }
};

const getCurrentAssetsDebit = () => {
    return getCurrentAssets().reduce((total, account) => {
        return total + getSkontroDebitValue(account);
    }, 0);
};

const getCurrentAssetsCredit = () => {
    return getCurrentAssets().reduce((total, account) => {
        return total + getSkontroCreditValue(account);
    }, 0);
};

const getFixedAssetsDebit = () => {
    return getFixedAssets().reduce((total, account) => {
        return total + getSkontroDebitValue(account);
    }, 0);
};

const getFixedAssetsCredit = () => {
    return getFixedAssets().reduce((total, account) => {
        return total + getSkontroCreditValue(account);
    }, 0);
};

const getOtherAssetsDebit = () => {
    return getOtherAssets().reduce((total, account) => {
        return total + getSkontroDebitValue(account);
    }, 0);
};

const getOtherAssetsCredit = () => {
    return getOtherAssets().reduce((total, account) => {
        return total + getSkontroCreditValue(account);
    }, 0);
};

const getTotalAssetsDebit = () => {
    return getCurrentAssetsDebit() + getFixedAssetsDebit() + getOtherAssetsDebit();
};

const getTotalAssetsCredit = () => {
    return getCurrentAssetsCredit() + getFixedAssetsCredit() + getOtherAssetsCredit();
};

const getCurrentLiabilitiesDebit = () => {
    return getCurrentLiabilities().reduce((total, account) => {
        return total + getSkontroDebitValue(account);
    }, 0);
};

const getCurrentLiabilitiesCredit = () => {
    return getCurrentLiabilities().reduce((total, account) => {
        return total + getSkontroCreditValue(account);
    }, 0);
};

const getLongTermLiabilitiesDebit = () => {
    return getLongTermLiabilities().reduce((total, account) => {
        return total + getSkontroDebitValue(account);
    }, 0);
};

const getLongTermLiabilitiesCredit = () => {
    return getLongTermLiabilities().reduce((total, account) => {
        return total + getSkontroCreditValue(account);
    }, 0);
};

const getTotalLiabilitiesDebit = () => {
    return getCurrentLiabilitiesDebit() + getLongTermLiabilitiesDebit();
};

const getTotalLiabilitiesCredit = () => {
    return getCurrentLiabilitiesCredit() + getLongTermLiabilitiesCredit();
};

const getTotalEquityDebit = () => {
    if (!reportData.value?.equity) return 0;
    const equityDebit = reportData.value.equity.reduce((total, account) => {
        return total + getSkontroDebitValue(account);
    }, 0);
    // Add net income to debit if it's negative (loss)
    const netIncome = reportData.value?.net_income || 0;
    return equityDebit + (netIncome < 0 ? Math.abs(netIncome) : 0);
};

const getTotalEquityCredit = () => {
    if (!reportData.value?.equity) return 0;
    const equityCredit = reportData.value.equity.reduce((total, account) => {
        return total + getSkontroCreditValue(account);
    }, 0);
    // Add net income to credit if it's positive (profit)
    const netIncome = reportData.value?.net_income || 0;
    return equityCredit + (netIncome >= 0 ? netIncome : 0);
};

const getTotalLiabilitiesAndEquityDebit = () => {
    return getTotalLiabilitiesDebit() + getTotalEquityDebit();
};

const getTotalLiabilitiesAndEquityCredit = () => {
    return getTotalLiabilitiesCredit() + getTotalEquityCredit();
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
