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
                            Total Produk Kurang Laris
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ formatNumber(slowMovingProducts.length) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ExclamationTriangleIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
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
                            class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1"
                        >
                            {{ formatNumber(summary.totalQuantity) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center"
                    >
                        <CubeIcon
                            class="w-6 h-6 text-orange-600 dark:text-orange-400"
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
                            Produk Tidak Terjual
                        </p>
                        <p
                            class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1"
                        >
                            {{ formatNumber(summary.zeroSalesCount) }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center"
                    >
                        <ArchiveBoxXMarkIcon
                            class="w-6 h-6 text-gray-600 dark:text-gray-400"
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
                >Memuat data barang kurang laris...</span
            >
        </div>

        <!-- Data Table -->
        <div
            v-else-if="slowMovingProducts.length > 0"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Daftar Barang Kurang Laris
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Produk dengan penjualan terendah atau tidak terjual sama sekali
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
                                No
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Produk
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Stok Saat Ini
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
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="(product, index) in slowMovingProducts"
                            :key="product.product_id"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-900/20'
                            "
                        >
                            <td
                                class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ index + 1 }}
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
                                class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(product.current_stock) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span
                                    :class="[
                                        'font-semibold',
                                        product.total_quantity === 0
                                            ? 'text-red-600 dark:text-red-400'
                                            : 'text-orange-600 dark:text-orange-400',
                                    ]"
                                >
                                    {{ formatNumber(product.total_quantity) }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(product.transaction_count) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white"
                            >
                                Rp {{ formatNumber(product.total_sales) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        product.total_quantity === 0
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    ]"
                                >
                                    {{
                                        product.total_quantity === 0
                                            ? "Tidak Terjual"
                                            : "Kurang Laris"
                                    }}
                                </span>
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
                <CheckCircleIcon class="w-16 h-16 text-green-400 mb-4" />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    Semua produk terjual dengan baik
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">
                    Tidak ditemukan produk dengan penjualan rendah pada periode yang dipilih.
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
    ExclamationTriangleIcon,
    CubeIcon,
    ArchiveBoxXMarkIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";

const notificationStore = useNotificationStore();

// Local state
const startDate = ref("");
const endDate = ref("");
const datePreset = ref("this_month");
const limit = ref(10);
const loading = ref(false);
const slowMovingProducts = ref([]);

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
    { value: 5, label: "Bottom 5" },
    { value: 10, label: "Bottom 10" },
    { value: 20, label: "Bottom 20" },
    { value: 50, label: "Bottom 50" },
];

const today = computed(() => {
    return new Date().toISOString().split("T")[0];
});

const summary = computed(() => {
    const totalQuantity = slowMovingProducts.value.reduce(
        (sum, p) => sum + (p.total_quantity || 0),
        0
    );
    const zeroSalesCount = slowMovingProducts.value.filter(
        (p) => p.total_quantity === 0
    ).length;

    return {
        totalQuantity,
        zeroSalesCount,
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
        const response = await stockBookService.getSlowMoving({
            start_date: startDate.value,
            end_date: endDate.value,
            limit: limit.value,
        });

        slowMovingProducts.value = response.data || response || [];
    } catch (error) {
        notificationStore.error("Gagal memuat data barang kurang laris");
        console.error("Failed to load slow moving products:", error);
        slowMovingProducts.value = [];
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
