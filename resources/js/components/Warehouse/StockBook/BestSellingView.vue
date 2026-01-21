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
                        v-model="limit"
                        label="Jumlah Data"
                        :options="limitOptions"
                        @change="loadData"
                    />
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Penjualan
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ formatNumber(summary.totalSales) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ShoppingCartIcon
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
                            Total Qty Terjual
                        </p>
                        <p
                            class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1"
                        >
                            {{ formatNumber(summary.totalQuantity) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <CubeIcon
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
                            Produk Terlaris
                        </p>
                        <p
                            class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1 truncate"
                        >
                            {{ summary.topProduct || '-' }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center"
                    >
                        <TrophyIcon
                            class="w-6 h-6 text-purple-600 dark:text-purple-400"
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
                >Memuat data barang terlaris...</span
            >
        </div>

        <!-- Data Table -->
        <div
            v-else-if="bestSellingProducts.length > 0"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Daftar Barang Terlaris
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Produk dengan penjualan tertinggi berdasarkan periode yang dipilih
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
                                Rank
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Produk
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Qty Terjual
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Total Transaksi
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Total Penjualan
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
                            v-for="(product, index) in bestSellingProducts"
                            :key="product.product_id"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-900/20'
                            "
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span
                                        :class="[
                                            'inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold',
                                            index === 0
                                                ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                                                : index === 1
                                                ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                                : index === 2
                                                ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400'
                                                : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                        ]"
                                    >
                                        {{ index + 1 }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ product.product_name }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ product.product_code }}
                                    </p>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-semibold text-green-600 dark:text-green-400"
                            >
                                {{ formatNumber(product.total_quantity) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(product.transaction_count) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white"
                            >
                                Rp {{ formatNumber(product.total_sales) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full"
                                            :style="{ width: product.contribution + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-gray-600 dark:text-gray-400 min-w-[50px]">
                                        {{ product.contribution.toFixed(1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12"
        >
            <div class="flex flex-col items-center text-center">
                <ShoppingCartIcon class="w-16 h-16 text-gray-400 mb-4" />
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
    ShoppingCartIcon,
    CubeIcon,
    TrophyIcon,
} from "@heroicons/vue/24/outline";

const notificationStore = useNotificationStore();

// Local state
const startDate = ref("");
const endDate = ref("");
const datePreset = ref("this_month");
const limit = ref(10);
const loading = ref(false);
const bestSellingProducts = ref([]);

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

const limitOptions = [
    { value: 5, label: "Top 5" },
    { value: 10, label: "Top 10" },
    { value: 20, label: "Top 20" },
    { value: 50, label: "Top 50" },
];

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

const summary = computed(() => {
    const totalSales = bestSellingProducts.value.reduce(
        (sum, p) => sum + (p.total_sales || 0),
        0
    );
    const totalQuantity = bestSellingProducts.value.reduce(
        (sum, p) => sum + (p.total_quantity || 0),
        0
    );
    const topProduct = bestSellingProducts.value[0]?.product_name || null;

    return {
        totalSales,
        totalQuantity,
        topProduct,
    };
});

// Methods
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
        const response = await stockBookService.getBestSelling({
            start_date: startDate.value,
            end_date: endDate.value,
            limit: limit.value,
        });

        const data = response.data || response || [];
        const totalSales = data.reduce((sum, p) => sum + (p.total_sales || 0), 0);

        bestSellingProducts.value = data.map((product) => ({
            ...product,
            contribution: totalSales > 0 ? ((product.total_sales || 0) / totalSales) * 100 : 0,
        }));
    } catch (error) {
        notificationStore.error("Gagal memuat data barang terlaris");
        console.error("Failed to load best selling products:", error);
        bestSellingProducts.value = [];
    } finally {
        loading.value = false;
    }
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0";
    return new Intl.NumberFormat("id-ID").format(value);
};

onMounted(() => {
    onDatePresetChange();
});
</script>
