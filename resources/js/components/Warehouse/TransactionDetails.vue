<template>
    <div class="space-y-4">
        <!-- Transaction Header -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="field in headerFields" :key="field.key">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ field.label }}
                    </label>
                    <div class="mt-1">
                        <span
                            v-if="field.type === 'badge'"
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                getBadgeClass(
                                    transaction[field.key],
                                    field.badgeColors
                                ),
                            ]"
                        >
                            {{ transaction[field.key] }}
                        </span>
                        <span
                            v-else-if="field.type === 'date'"
                            class="text-sm text-gray-900 dark:text-white"
                        >
                            {{ formatDate(transaction[field.key]) }}
                        </span>
                        <span
                            v-else-if="field.type === 'currency'"
                            class="text-sm text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(transaction[field.key]) }}
                        </span>
                        <span
                            v-else
                            class="text-sm text-gray-900 dark:text-white"
                        >
                            {{ transaction[field.key] || "-" }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div
            v-if="transaction.notes"
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
        >
            <h4
                class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2"
            >
                Notes
            </h4>
            <p class="text-sm text-blue-700 dark:text-blue-300">
                {{ transaction.notes }}
            </p>
        </div>

        <!-- Details Table -->
        <div>
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                {{ detailsTitle }}
            </h4>
            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                v-for="column in detailColumns"
                                :key="column.key"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                {{ column.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                    >
                        <tr v-for="(detail, index) in details" :key="index">
                            <td
                                v-for="column in detailColumns"
                                :key="column.key"
                                class="px-6 py-4 whitespace-nowrap text-sm"
                            >
                                <span
                                    v-if="column.type === 'currency'"
                                    class="text-gray-900 dark:text-white font-medium"
                                >
                                    {{ formatCurrency(detail[column.key]) }}
                                </span>
                                <span
                                    v-else-if="column.type === 'badge'"
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getBadgeClass(
                                            detail[column.key],
                                            column.badgeColors
                                        ),
                                    ]"
                                >
                                    {{ detail[column.key] }}
                                </span>
                                <span
                                    v-else
                                    class="text-gray-900 dark:text-white"
                                >
                                    {{ detail[column.key] || "-" }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <!-- Footer Row -->
                    <tfoot
                        v-if="showFooter"
                        class="bg-gray-50 dark:bg-gray-700"
                    >
                        <tr>
                            <td
                                :colspan="footerColspan"
                                class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white text-right"
                            >
                                Total:
                            </td>
                            <td
                                v-for="footerColumn in footerColumns"
                                :key="footerColumn.key"
                                class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                <span v-if="footerColumn.type === 'currency'">
                                    {{
                                        formatCurrency(
                                            getFooterTotal(footerColumn.key)
                                        )
                                    }}
                                </span>
                                <span v-else>
                                    {{ getFooterTotal(footerColumn.key) }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div
            v-if="showActions"
            class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
        >
            <button
                v-for="action in actions"
                :key="action.key"
                @click="action.handler"
                :class="[
                    'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
                    action.class ||
                        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600',
                ]"
                :disabled="action.disabled"
            >
                <component :is="action.icon" class="w-4 h-4" />
                {{ action.label }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    transaction: {
        type: Object,
        required: true,
    },
    details: {
        type: Array,
        required: true,
    },
    headerFields: {
        type: Array,
        default: () => [
            { key: "code", label: "Transaction Code" },
            { key: "date", label: "Date", type: "date" },
            { key: "status", label: "Status", type: "badge" },
            { key: "created_by", label: "Created By" },
        ],
    },
    detailColumns: {
        type: Array,
        default: () => [
            { key: "product_code", label: "Product Code" },
            { key: "product_name", label: "Product Name" },
            { key: "quantity", label: "Quantity" },
            { key: "unit_price", label: "Unit Price", type: "currency" },
            { key: "total_price", label: "Total", type: "currency" },
        ],
    },
    detailsTitle: {
        type: String,
        default: "Details",
    },
    showFooter: {
        type: Boolean,
        default: true,
    },
    footerColumns: {
        type: Array,
        default: () => [
            { key: "quantity", type: "number" },
            { key: "total_price", type: "currency" },
        ],
    },
    actions: {
        type: Array,
        default: () => [],
    },
    showActions: {
        type: Boolean,
        default: true,
    },
});

// Computed
const footerColspan = computed(() => {
    return props.detailColumns.length - props.footerColumns.length;
});

// Methods
const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("id-ID");
};

const formatCurrency = (value) => {
    if (!value) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(value);
};

const getBadgeClass = (value, colors = {}) => {
    const defaultColors = {
        active: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        inactive:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        pending:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        approved:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        completed:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        rejected:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        draft: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        in_progress:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        increase:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        decrease:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };

    const colorMap = { ...defaultColors, ...colors };
    return (
        colorMap[value?.toLowerCase()] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const getFooterTotal = (key) => {
    if (key === "quantity") {
        return props.details.reduce(
            (sum, detail) => sum + (detail[key] || 0),
            0
        );
    } else if (key === "total_price") {
        return props.details.reduce(
            (sum, detail) => sum + (detail[key] || 0),
            0
        );
    }
    return 0;
};
</script>
