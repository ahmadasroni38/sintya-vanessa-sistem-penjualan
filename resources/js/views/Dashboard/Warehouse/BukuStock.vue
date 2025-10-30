<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Buku Stock
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Stock card reporting and inventory movement tracking
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="exportData"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Export
                </button>
                <button
                    @click="showFilterModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <FunnelIcon class="w-4 h-4" />
                    Filter
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Transactions
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ stats.totalTransactions }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <BookOpenIcon
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
                            Stock In
                        </p>
                        <p
                            class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1"
                        >
                            {{ stats.stockIn }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ArrowTrendingUpIcon
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
                            Stock Out
                        </p>
                        <p
                            class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1"
                        >
                            {{ stats.stockOut }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ArrowTrendingDownIcon
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
                            Current Balance
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ stats.currentBalance }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ScaleIcon
                            class="w-6 h-6 text-purple-600 dark:text-purple-400"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Summary -->
        <div
            v-if="hasActiveFilters"
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span
                        class="text-sm font-medium text-blue-800 dark:text-blue-200"
                        >Active Filters:</span
                    >
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-if="filters.product_id"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            Product: {{ getProductName(filters.product_id) }}
                        </span>
                        <span
                            v-if="filters.location_id"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            Location: {{ getLocationName(filters.location_id) }}
                        </span>
                        <span
                            v-if="filters.date_from"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            From: {{ formatDate(filters.date_from) }}
                        </span>
                        <span
                            v-if="filters.date_to"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            To: {{ formatDate(filters.date_to) }}
                        </span>
                        <span
                            v-if="filters.transaction_type"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                        >
                            Type: {{ filters.transaction_type }}
                        </span>
                    </div>
                </div>
                <button
                    @click="clearFilters"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Stock Cards Table -->
        <DataTable
            title="Stock Cards"
            :data="stockCards"
            :columns="columns"
            :loading="loading"
            @refresh="loadStockCards"
            :show-refresh="true"
            :refresh-loading="refreshing"
            :show-add-button="false"
            search-placeholder="Search stock cards..."
        >
            <template #column-transaction_type="{ value }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getTransactionTypeClass(value),
                    ]"
                >
                    {{ getTransactionTypeLabel(value) }}
                </span>
            </template>

            <template #column-quantity_in="{ value }">
                <span
                    v-if="value > 0"
                    class="text-green-600 dark:text-green-400 font-semibold"
                >
                    +{{ value }}
                </span>
                <span v-else class="text-gray-400">-</span>
            </template>

            <template #column-quantity_out="{ value }">
                <span
                    v-if="value > 0"
                    class="text-red-600 dark:text-red-400 font-semibold"
                >
                    -{{ value }}
                </span>
                <span v-else class="text-gray-400">-</span>
            </template>

            <template #column-balance="{ value }">
                <span class="font-semibold text-gray-900 dark:text-white">
                    {{ value }}
                </span>
            </template>

            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="viewDetails(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Filter Modal -->
        <Modal
            v-if="showFilterModal"
            title="Filter Stock Cards"
            @close="showFilterModal = false"
        >
            <form @submit.prevent="applyFilters" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormSelect
                        v-model="filterForm.product_id"
                        label="Product"
                        :options="productOptions"
                        clearable
                    />
                    <FormSelect
                        v-model="filterForm.location_id"
                        label="Location"
                        :options="locationOptions"
                        clearable
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormInput
                        v-model="filterForm.date_from"
                        label="Date From"
                        type="date"
                    />
                    <FormInput
                        v-model="filterForm.date_to"
                        label="Date To"
                        type="date"
                    />
                </div>

                <FormSelect
                    v-model="filterForm.transaction_type"
                    label="Transaction Type"
                    :options="transactionTypeOptions"
                    clearable
                />

                <div
                    class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="clearFilters"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        Clear
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Apply Filters
                    </button>
                </div>
            </form>
        </Modal>

        <!-- Details Modal -->
        <Modal
            v-if="showDetailsModal"
            title="Stock Card Details"
            @close="showDetailsModal = false"
        >
            <div v-if="selectedStockCard" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Transaction Date
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ formatDate(selectedStockCard.date) }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Product
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockCard.product_code }} -
                            {{ selectedStockCard.product_name }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Location
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockCard.location_name }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Transaction Type
                        </label>
                        <span
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1',
                                getTransactionTypeClass(
                                    selectedStockCard.transaction_type
                                ),
                            ]"
                        >
                            {{
                                getTransactionTypeLabel(
                                    selectedStockCard.transaction_type
                                )
                            }}
                        </span>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Reference
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockCard.reference }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Created By
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockCard.created_by }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Quantity In
                        </label>
                        <p
                            class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400"
                        >
                            {{ selectedStockCard.quantity_in || 0 }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Quantity Out
                        </label>
                        <p
                            class="mt-1 text-lg font-semibold text-red-600 dark:text-red-400"
                        >
                            {{ selectedStockCard.quantity_out || 0 }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Balance
                        </label>
                        <p
                            class="mt-1 text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            {{ selectedStockCard.balance }}
                        </p>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Notes
                    </label>
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ selectedStockCard.notes || "-" }}
                    </p>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useNotificationStore } from "../../../stores/notification";
import DataTable from "../../../components/UI/DataTable.vue";
import Modal from "../../../components/Overlays/Modal.vue";
import FormInput from "../../../components/Forms/FormInput.vue";
import FormSelect from "../../../components/Forms/FormSelect.vue";
import {
    ArrowDownTrayIcon,
    FunnelIcon,
    BookOpenIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ScaleIcon,
    EyeIcon,
} from "@heroicons/vue/24/outline";
import {
    stockCardService,
    dummyDataService,
} from "../../../services/warehouseService";

const notificationStore = useNotificationStore();

// State
const loading = ref(false);
const refreshing = ref(false);
const stockCards = ref([]);
const showFilterModal = ref(false);
const showDetailsModal = ref(false);
const selectedStockCard = ref(null);

// Dummy data
const products = ref([]);
const locations = ref([]);

// Filters
const filters = ref({
    product_id: "",
    location_id: "",
    date_from: "",
    date_to: "",
    transaction_type: "",
});

const filterForm = ref({ ...filters.value });

// Options
const locationOptions = computed(() =>
    locations.value.map((loc) => ({ value: loc.id, label: loc.name }))
);

const productOptions = computed(() =>
    products.value.map((product) => ({
        value: product.id,
        label: `${product.code} - ${product.name}`,
    }))
);

const transactionTypeOptions = [
    { value: "stock_in", label: "Stock In" },
    { value: "stock_out", label: "Stock Out" },
    { value: "mutation_in", label: "Mutation In" },
    { value: "mutation_out", label: "Mutation Out" },
    { value: "adjustment_in", label: "Adjustment In" },
    { value: "adjustment_out", label: "Adjustment Out" },
    { value: "opname_in", label: "Opname In" },
    { value: "opname_out", label: "Opname Out" },
];

// Table columns
const columns = [
    {
        key: "date",
        label: "Date",
        sortable: true,
        type: "date",
    },
    {
        key: "product_code",
        label: "Product Code",
        sortable: true,
    },
    {
        key: "product_name",
        label: "Product Name",
        sortable: true,
    },
    {
        key: "location_name",
        label: "Location",
        sortable: true,
    },
    {
        key: "transaction_type",
        label: "Transaction Type",
        sortable: true,
    },
    {
        key: "reference",
        label: "Reference",
        sortable: true,
    },
    {
        key: "quantity_in",
        label: "Qty In",
        sortable: true,
    },
    {
        key: "quantity_out",
        label: "Qty Out",
        sortable: true,
    },
    {
        key: "balance",
        label: "Balance",
        sortable: true,
    },
    {
        key: "created_by",
        label: "Created By",
        sortable: true,
    },
];

// Computed
const stats = computed(() => {
    const totalTransactions = stockCards.value.length;
    const stockIn = stockCards.value.reduce(
        (sum, item) => sum + (item.quantity_in || 0),
        0
    );
    const stockOut = stockCards.value.reduce(
        (sum, item) => sum + (item.quantity_out || 0),
        0
    );
    const currentBalance =
        stockCards.value.length > 0
            ? stockCards.value[stockCards.value.length - 1]?.balance || 0
            : 0;

    return {
        totalTransactions,
        stockIn,
        stockOut,
        currentBalance,
    };
});

const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some((value) => value !== "");
});

// Methods
const loadStockCards = async () => {
    loading.value = true;
    try {
        // Load dummy data
        products.value = dummyDataService.generateProducts();
        locations.value = dummyDataService.generateLocations();
        stockCards.value = dummyDataService.generateStockCards(
            products.value,
            locations.value
        );

        // Apply filters if any
        if (hasActiveFilters.value) {
            applyFiltersToData();
        }
    } catch (error) {
        notificationStore.error("Failed to load stock cards");
    } finally {
        loading.value = false;
    }
};

const refreshStockCards = async () => {
    refreshing.value = true;
    await loadStockCards();
    refreshing.value = false;
};

const viewDetails = (stockCard) => {
    selectedStockCard.value = stockCard;
    showDetailsModal.value = true;
};

const applyFilters = () => {
    filters.value = { ...filterForm.value };
    showFilterModal.value = false;
    applyFiltersToData();
};

const applyFiltersToData = () => {
    // In a real app, this would call the API with filters
    // For now, we'll just reload the data (it's already filtered in the computed)
    loadStockCards();
};

const clearFilters = () => {
    filters.value = {
        product_id: "",
        location_id: "",
        date_from: "",
        date_to: "",
        transaction_type: "",
    };
    filterForm.value = { ...filters.value };
    showFilterModal.value = false;
    loadStockCards();
};

const getProductName = (productId) => {
    const product = products.value.find((p) => p.id === productId);
    return product ? `${product.code} - ${product.name}` : "";
};

const getLocationName = (locationId) => {
    const location = locations.value.find((l) => l.id === locationId);
    return location ? location.name : "";
};

const getTransactionTypeClass = (type) => {
    const classes = {
        stock_in:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        stock_out:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        mutation_in:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        mutation_out:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        adjustment_in:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        adjustment_out:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        opname_in:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
        opname_out:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
    };
    return (
        classes[type] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const getTransactionTypeLabel = (type) => {
    const labels = {
        stock_in: "Stock In",
        stock_out: "Stock Out",
        mutation_in: "Mutation In",
        mutation_out: "Mutation Out",
        adjustment_in: "Adjustment In",
        adjustment_out: "Adjustment Out",
        opname_in: "Opname In",
        opname_out: "Opname Out",
    };
    return labels[type] || type;
};

const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("id-ID");
};

const exportData = () => {
    notificationStore.info("Export feature coming soon");
};

onMounted(() => {
    loadStockCards();
});
</script>
