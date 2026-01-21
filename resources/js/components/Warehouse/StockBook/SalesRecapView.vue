<template>
    <div class="space-y-6">
        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <FormSelect
                        v-model="datePreset"
                        label="Periode"
                        :options="datePresetOptions"
                        @change="onDatePresetChange"
                    />
                </div>
                <div>
                    <FormInput
                        v-model="startDate"
                        label="Tanggal Mulai"
                        type="date"
                        :max="today"
                        @change="onDateChange"
                    />
                </div>
                <div>
                    <FormInput
                        v-model="endDate"
                        label="Tanggal Akhir"
                        type="date"
                        :max="today"
                        :min="startDate"
                        @change="onDateChange"
                    />
                </div>
                <div>
                    <FormSelect
                        v-model="groupBy"
                        label="Kelompokkan"
                        :options="groupByOptions"
                        @change="loadData"
                    />
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Penjualan
                        </p>
                        <p
                            class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1"
                        >
                            Rp {{ formatNumber(recapSummary.totalSales) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                    >
                        <BanknotesIcon
                            class="w-6 h-6 text-green-600 dark:text-green-400"
                        />
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Transaksi
                        </p>
                        <p
                            class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1"
                        >
                            {{ formatNumber(recapSummary.totalTransactions) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <DocumentTextIcon
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Qty Terjual
                        </p>
                        <p
                            class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1"
                        >
                            {{ formatNumber(recapSummary.totalQuantity) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center"
                    >
                        <CubeIcon
                            class="w-6 h-6 text-purple-600 dark:text-purple-400"
                        />
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Rata-rata Per Transaksi
                        </p>
                        <p
                            class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1"
                        >
                            Rp {{ formatNumber(recapSummary.averagePerTransaction) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ChartBarIcon
                            class="w-6 h-6 text-orange-600 dark:text-orange-400"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
            <svg class="animate-spin h-8 w-8 text-blue-600" viewBox="0 0 24 24">
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                    fill="none"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            <span class="ml-2 text-gray-600 dark:text-gray-400"
                >Memuat rekap penjualan...</span
            >
        </div>

        <!-- Data Table -->
        <div
            v-else-if="salesRecap.length > 0"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Rekap Penjualan
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan penjualan berdasarkan {{ getGroupByLabel() }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                {{ getGroupByLabel() }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Jumlah Transaksi
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Qty Terjual
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Total Penjualan
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Rata-rata
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Kontribusi
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="(item, index) in salesRecap"
                            :key="item.group_key"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-900/20'
                            "
                        >
                            <td class="px-6 py-4">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ item.group_label }}
                                </p>
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(item.transaction_count) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-semibold text-purple-600 dark:text-purple-400"
                            >
                                {{ formatNumber(item.total_quantity) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-semibold text-green-600 dark:text-green-400"
                            >
                                Rp {{ formatNumber(item.total_sales) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                            >
                                Rp {{ formatNumber(item.average_per_transaction) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div
                                            class="bg-green-600 h-2 rounded-full"
                                            :style="{ width: item.contribution + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-gray-600 dark:text-gray-400 min-w-[50px]">
                                        {{ item.contribution.toFixed(1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-gray-900/50">
                        <tr>
                            <td
                                class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white"
                            >
                                Total
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-bold text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(recapSummary.totalTransactions) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-bold text-purple-600 dark:text-purple-400"
                            >
                                {{ formatNumber(recapSummary.totalQuantity) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-bold text-green-600 dark:text-green-400"
                            >
                                Rp {{ formatNumber(recapSummary.totalSales) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-bold text-gray-900 dark:text-white"
                            >
                                Rp {{ formatNumber(recapSummary.averagePerTransaction) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-bold text-gray-900 dark:text-white"
                            >
                                100%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12"
        >
            <div class="flex flex-col items-center text-center">
                <ChartBarIcon class="w-16 h-16 text-gray-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    Tidak ada data penjualan
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    Tidak ditemukan data penjualan untuk periode yang dipilih.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useNotificationStore } from "../../../stores/notification";
import { stockBookService } from "../../../services/stockBookService";
import FormInput from "../../Forms/FormInput.vue";
import FormSelect from "../../Forms/FormSelect.vue";
import {
    BanknotesIcon,
    DocumentTextIcon,
    CubeIcon,
    ChartBarIcon,
} from "@heroicons/vue/24/outline";

const notificationStore = useNotificationStore();

// Local state
const startDate = ref("");
const endDate = ref("");
const datePreset = ref("this_month");
const groupBy = ref("daily");
const loading = ref(false);
const salesRecap = ref([]);

// Options
const datePresetOptions = [
    { value: "", label: "Pilih Periode" },
    { value: "today", label: "Hari Ini" },
    { value: "yesterday", label: "Kemarin" },
    { value: "last_7_days", label: "7 Hari Terakhir" },
    { value: "last_30_days", label: "30 Hari Terakhir" },
    { value: "this_month", label: "Bulan Ini" },
    { value: "last_month", label: "Bulan Lalu" },
    { value: "this_year", label: "Tahun Ini" },
];

const groupByOptions = [
    { value: "daily", label: "Harian" },
    { value: "weekly", label: "Mingguan" },
    { value: "monthly", label: "Bulanan" },
    { value: "category", label: "Kategori Produk" },
    { value: "location", label: "Lokasi" },
];

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

const recapSummary = computed(() => {
    const totalSales = salesRecap.value.reduce(
        (sum, item) => sum + (item.total_sales || 0),
        0
    );
    const totalTransactions = salesRecap.value.reduce(
        (sum, item) => sum + (item.transaction_count || 0),
        0
    );
    const totalQuantity = salesRecap.value.reduce(
        (sum, item) => sum + (item.total_quantity || 0),
        0
    );
    const averagePerTransaction =
        totalTransactions > 0 ? totalSales / totalTransactions : 0;

    return {
        totalSales,
        totalTransactions,
        totalQuantity,
        averagePerTransaction,
    };
});

// Methods
const getGroupByLabel = () => {
    const labels = {
        daily: "Tanggal",
        weekly: "Minggu",
        monthly: "Bulan",
        category: "Kategori",
        location: "Lokasi",
    };
    return labels[groupBy.value] || "Periode";
};

const onDatePresetChange = () => {
    const preset = datePreset.value;
    const todayDate = new Date();
    let start = new Date();
    let end = new Date();

    switch (preset) {
        case "today":
            start = todayDate;
            end = todayDate;
            break;
        case "yesterday":
            start = new Date(todayDate);
            start.setDate(todayDate.getDate() - 1);
            end = start;
            break;
        case "last_7_days":
            start = new Date(todayDate);
            start.setDate(todayDate.getDate() - 7);
            end = todayDate;
            break;
        case "last_30_days":
            start = new Date(todayDate);
            start.setDate(todayDate.getDate() - 30);
            end = todayDate;
            break;
        case "this_month":
            start = new Date(todayDate.getFullYear(), todayDate.getMonth(), 1);
            end = todayDate;
            break;
        case "last_month":
            start = new Date(todayDate.getFullYear(), todayDate.getMonth() - 1, 1);
            end = new Date(todayDate.getFullYear(), todayDate.getMonth(), 0);
            break;
        case "this_year":
            start = new Date(todayDate.getFullYear(), 0, 1);
            end = todayDate;
            break;
        default:
            return;
    }

    startDate.value = start.toISOString().split("T")[0];
    endDate.value = end.toISOString().split("T")[0];
    loadData();
};

const onDateChange = () => {
    datePreset.value = "";
    loadData();
};

const loadData = async () => {
    if (!startDate.value || !endDate.value) return;

    loading.value = true;
    try {
        const response = await stockBookService.getSalesRecap({
            start_date: startDate.value,
            end_date: endDate.value,
            group_by: groupBy.value,
        });

        const data = response.data || response || [];
        const totalSales = data.reduce((sum, item) => sum + (item.total_sales || 0), 0);

        salesRecap.value = data.map((item) => ({
            ...item,
            average_per_transaction:
                item.transaction_count > 0
                    ? item.total_sales / item.transaction_count
                    : 0,
            contribution: totalSales > 0 ? ((item.total_sales || 0) / totalSales) * 100 : 0,
        }));
    } catch (error) {
        notificationStore.error("Gagal memuat rekap penjualan");
        console.error("Failed to load sales recap:", error);
        salesRecap.value = [];
    } finally {
        loading.value = false;
    }
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0";
    return new Intl.NumberFormat("id-ID").format(Math.round(value));
};

onMounted(() => {
    onDatePresetChange();
});
</script>
