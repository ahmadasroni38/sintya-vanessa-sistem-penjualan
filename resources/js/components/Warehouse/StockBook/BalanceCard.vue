<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200 cursor-pointer"
        @click="$emit('click', balance)"
    >
        <div class="flex items-center justify-between mb-4">
            <div class="flex-1">
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-white truncate"
                >
                    {{ balance.product_code }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                    {{ balance.product_name }}
                </p>
            </div>
            <div class="ml-4">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        stockStatusClass,
                    ]"
                >
                    {{ stockStatusLabel }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Current Balance
                </p>
                <p
                    :class="[
                        'text-xl font-bold',
                        balance.current_balance === 0
                            ? 'text-red-600 dark:text-red-400'
                            : 'text-gray-900 dark:text-white',
                    ]"
                >
                    {{ formatNumber(balance.current_balance) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Min/Max Stock
                </p>
                <p class="text-sm text-gray-900 dark:text-white">
                    {{ formatNumber(balance.minimum_stock) }} /
                    {{ formatNumber(balance.maximum_stock) }}
                </p>
            </div>
        </div>

        <!-- Stock Level Indicator -->
        <div class="mb-4">
            <div class="flex justify-between items-center mb-1">
                <span class="text-xs text-gray-500 dark:text-gray-400"
                    >Stock Level</span
                >
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ stockPercentage }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div
                    :class="[
                        'h-2 rounded-full transition-all duration-300',
                        stockLevelBarClass,
                    ]"
                    :style="{ width: `${Math.min(stockPercentage, 100)}%` }"
                ></div>
            </div>
        </div>

        <!-- Last Transaction Info -->
        <div
            v-if="balance.last_transaction_date"
            class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400"
        >
            <div class="flex items-center">
                <ClockIcon class="w-3 h-3 mr-1" />
                Last: {{ formatDate(balance.last_transaction_date) }}
            </div>
            <div class="flex items-center">
                <span
                    :class="[
                        'inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium',
                        getTransactionTypeClass(balance.last_transaction_type),
                    ]"
                >
                    {{ getTransactionTypeLabel(balance.last_transaction_type) }}
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div
            v-if="showActions"
            class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"
        >
            <button
                @click.stop="$emit('view-ledger', balance)"
                class="flex-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30"
            >
                View Ledger
            </button>
            <button
                v-if="balance.is_below_minimum"
                @click.stop="$emit('reorder', balance)"
                class="flex-1 px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 border border-orange-200 rounded hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-orange-900/20 dark:border-orange-800 dark:text-orange-400 dark:hover:bg-orange-900/30"
            >
                Reorder
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { ClockIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    balance: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["click", "view-ledger", "reorder"]);

// Computed properties
const stockStatusClass = computed(() => {
    const status = props.balance.status;
    const classes = {
        in_stock:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        low_stock:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        out_of_stock:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        overstock:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
    };
    return (
        classes[status] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
});

const stockStatusLabel = computed(() => {
    const status = props.balance.status;
    const labels = {
        in_stock: "In Stock",
        low_stock: "Low Stock",
        out_of_stock: "Out of Stock",
        overstock: "Overstock",
    };
    return labels[status] || "Unknown";
});

const stockPercentage = computed(() => {
    const { current_balance, maximum_stock } = props.balance;
    if (!maximum_stock || maximum_stock === 0) return 0;
    return Math.round((current_balance / maximum_stock) * 100);
});

const stockLevelBarClass = computed(() => {
    const percentage = stockPercentage.value;
    if (percentage === 0) return "bg-red-600 dark:bg-red-400";
    if (percentage < 25) return "bg-orange-600 dark:bg-orange-400";
    if (percentage < 50) return "bg-yellow-600 dark:bg-yellow-400";
    if (percentage > 100) return "bg-blue-600 dark:bg-blue-400";
    return "bg-green-600 dark:bg-green-400";
});

// Methods
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
        adjustment_in: "Adj In",
        adjustment_out: "Adj Out",
        opname_in: "Opname In",
        opname_out: "Opname Out",
    };
    return labels[type] || type;
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0";
    return new Intl.NumberFormat("en-US").format(value);
};

const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};
</script>
