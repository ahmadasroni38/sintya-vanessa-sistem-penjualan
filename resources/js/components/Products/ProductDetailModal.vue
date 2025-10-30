<template>
    <Modal :show="show" @close="$emit('close')" size="2xl">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <CubeIcon
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Product Details
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ product?.product_code }}
                        </p>
                    </div>
                </div>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                    <XMarkIcon class="w-6 h-6" />
                </button>
            </div>
        </template>

        <template #default>
            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>

            <div v-else-if="product" class="space-y-6">
                <!-- Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                            ]"
                        >
                            <component
                                :is="tab.icon"
                                class="w-5 h-5 inline-block mr-2"
                            />
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="py-4">
                    <!-- General Info Tab -->
                    <div v-if="activeTab === 'general'" class="space-y-6">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6"
                        >
                            <div class="space-y-4">
                                <h4
                                    class="font-semibold text-gray-900 dark:text-white"
                                >
                                    Basic Information
                                </h4>
                                <dl class="space-y-3">
                                    <div>
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Product Name
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ product.product_name }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Product Type
                                        </dt>
                                        <dd class="mt-1">
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    getProductTypeBadgeClass(
                                                        product.product_type
                                                    ),
                                                ]"
                                            >
                                                {{ product.product_type_label }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div v-if="product.category">
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Category
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ product.category.name }}
                                        </dd>
                                    </div>
                                    <div v-if="product.unit">
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Unit
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ product.unit.name }}
                                            <span class="text-gray-500">
                                                ({{ product.unit.symbol }})
                                            </span>
                                        </dd>
                                    </div>
                                    <div v-if="product.description">
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Description
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ product.description }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="space-y-4">
                                <h4
                                    class="font-semibold text-gray-900 dark:text-white"
                                >
                                    Pricing & Stock
                                </h4>
                                <dl class="space-y-3">
                                    <div>
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Purchase Price
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{ formatCurrency(product.purchase_price) }}
                                        </dd>
                                    </div>
                                    <div v-if="product.selling_price">
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Selling Price
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{ formatCurrency(product.selling_price) }}
                                        </dd>
                                    </div>
                                    <div v-if="product.profit_margin">
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Profit Margin
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm font-semibold text-green-600 dark:text-green-400"
                                        >
                                            {{ formatCurrency(product.profit_margin) }}
                                            <span class="text-xs">
                                                ({{
                                                    product.profit_margin_percentage.toFixed(
                                                        2
                                                    )
                                                }}%)
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Stock Thresholds
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-white"
                                        >
                                            Min: {{ product.minimum_stock }} | Max:
                                            {{ product.maximum_stock }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            Status
                                        </dt>
                                        <dd class="mt-1">
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    product.is_active
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                        : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                                ]"
                                            >
                                                {{
                                                    product.is_active
                                                        ? "Active"
                                                        : "Inactive"
                                                }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div
                            class="pt-4 border-t border-gray-200 dark:border-gray-700"
                        >
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Created At
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ formatDate(product.created_at) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Last Updated
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ formatDate(product.updated_at) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Stock Info Tab -->
                    <div v-if="activeTab === 'stock'" class="space-y-4">
                        <div
                            v-if="loadingStock"
                            class="flex items-center justify-center py-8"
                        >
                            <div
                                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                            ></div>
                        </div>

                        <div v-else-if="stockData" class="space-y-4">
                            <div
                                v-for="location in stockData.stock_data"
                                :key="location.location_id"
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <div>
                                        <h5
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ location.location_name }}
                                        </h5>
                                        <p
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            Code: {{ location.location_code }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="text-2xl font-bold text-gray-900 dark:text-white"
                                        >
                                            {{ location.stock }}
                                        </div>
                                        <span
                                            v-if="location.is_below_minimum"
                                            class="text-xs text-red-600 dark:text-red-400"
                                        >
                                            Below Minimum
                                        </span>
                                        <span
                                            v-else-if="location.is_above_maximum"
                                            class="text-xs text-yellow-600 dark:text-yellow-400"
                                        >
                                            Above Maximum
                                        </span>
                                        <span
                                            v-else
                                            class="text-xs text-green-600 dark:text-green-400"
                                        >
                                            Normal
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-sm font-medium text-blue-900 dark:text-blue-300"
                                    >
                                        Total Stock
                                    </span>
                                    <span
                                        class="text-lg font-bold text-blue-900 dark:text-blue-300"
                                    >
                                        {{ stockData.total_stock }}
                                        {{ product.unit?.symbol }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div v-if="activeTab === 'history'" class="space-y-4">
                        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                            Stock movement history will be displayed here
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex items-center justify-between">
                <button
                    @click="$emit('edit', product)"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Edit Product
                </button>
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Close
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import Modal from "../Overlays/Modal.vue";
import {
    CubeIcon,
    XMarkIcon,
    InformationCircleIcon,
    ChartBarIcon,
    ClockIcon,
} from "@heroicons/vue/24/outline";
import api from "../../utils/api";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    productId: {
        type: [Number, String],
        default: null,
    },
});

const emit = defineEmits(["close", "edit"]);

const loading = ref(false);
const loadingStock = ref(false);
const product = ref(null);
const stockData = ref(null);
const activeTab = ref("general");

const tabs = [
    { id: "general", label: "General", icon: InformationCircleIcon },
    { id: "stock", label: "Stock Info", icon: ChartBarIcon },
    { id: "history", label: "History", icon: ClockIcon },
];

watch(
    () => props.show,
    async (newValue) => {
        if (newValue && props.productId) {
            await loadProduct();
        }
    }
);

watch(activeTab, async (newTab) => {
    if (newTab === "stock" && !stockData.value) {
        await loadStockData();
    }
});

const loadProduct = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/products/${props.productId}`);
        product.value = response.data.data;
    } catch (error) {
        console.error("Error loading product:", error);
    } finally {
        loading.value = false;
    }
};

const loadStockData = async () => {
    loadingStock.value = true;
    try {
        const response = await api.get(`/products/${props.productId}/stock`);
        stockData.value = response.data.data;
    } catch (error) {
        console.error("Error loading stock data:", error);
    } finally {
        loadingStock.value = false;
    }
};

const getProductTypeBadgeClass = (type) => {
    const classes = {
        finished_goods:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        raw_material:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        consumable:
            "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
    };
    return classes[type] || "bg-gray-100 text-gray-800";
};

const formatCurrency = (value) => {
    if (!value && value !== 0) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).format(date);
};
</script>
