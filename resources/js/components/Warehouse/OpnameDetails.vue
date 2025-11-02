<template>
    <Modal
        :is-open="show"
        title="Stock Opname Details"
        size="4xl"
        @close="$emit('close')"
    >
        <div v-if="opname" class="space-y-6">
            <!-- Master Information -->
            <div class="space-y-4">
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2"
                >
                    General Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <DetailItem
                        label="Opname Number"
                        :value="opname.opname_number"
                    />
                    <DetailItem
                        label="Date"
                        :value="formatDate(opname.opname_date)"
                    />
                    <DetailItem
                        label="Location"
                        :value="opname.location?.name || '-'"
                    />
                    <DetailItem label="Total Items">
                        <template #value>
                            <span
                                class="font-semibold text-blue-600 dark:text-blue-400"
                            >
                                {{ opname.total_items || 0 }} product(s)
                            </span>
                        </template>
                    </DetailItem>
                    <DetailItem label="Status">
                        <template #value>
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getStatusBadgeClass(opname.status),
                                ]"
                            >
                                {{ opname.status }}
                            </span>
                        </template>
                    </DetailItem>
                    <DetailItem
                        label="Created By"
                        :value="opname.creator?.name || '-'"
                    />
                    <DetailItem
                        label="Completed By"
                        :value="opname.completer?.name || '-'"
                    />
                    <DetailItem
                        label="Completed At"
                        :value="
                            opname.completed_at
                                ? formatDate(opname.completed_at)
                                : '-'
                        "
                    />
                </div>

                <div>
                    <DetailItem
                        label="Description"
                        :value="opname.description || '-'"
                    />
                </div>

                <div>
                    <DetailItem label="Notes" :value="opname.notes || '-'" />
                </div>
            </div>

            <!-- Details (Products) Table -->
            <div class="space-y-4">
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2"
                >
                    Product Details
                </h3>

                <div
                    class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg"
                >
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    #
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Product Code
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Product Name
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    System Qty
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Physical Qty
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Difference
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Notes
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr
                                v-if="
                                    !opname.details ||
                                    opname.details.length === 0
                                "
                            >
                                <td
                                    colspan="8"
                                    class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    No product details available
                                </td>
                            </tr>
                            <tr
                                v-for="(detail, index) in opname.details"
                                :key="detail.id || index"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800"
                            >
                                <!-- Row Number -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    {{ index + 1 }}
                                </td>

                                <!-- Product Code -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    {{ detail.product?.product_code || "-" }}
                                </td>

                                <!-- Product Name -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 dark:text-white"
                                >
                                    {{ detail.product?.product_name || "-" }}
                                </td>

                                <!-- System Quantity -->
                                <td
                                    class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatNumber(detail.system_quantity) }}
                                </td>

                                <!-- Physical Quantity -->
                                <td
                                    class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"
                                >
                                    {{ formatNumber(detail.physical_quantity) }}
                                </td>

                                <!-- Difference -->
                                <td class="px-4 py-3 text-sm text-right">
                                    <span
                                        :class="[
                                            'font-semibold',
                                            detail.difference_quantity > 0
                                                ? 'text-green-600 dark:text-green-400'
                                                : detail.difference_quantity < 0
                                                ? 'text-red-600 dark:text-red-400'
                                                : 'text-gray-900 dark:text-white',
                                        ]"
                                    >
                                        {{
                                            detail.difference_quantity > 0
                                                ? "+"
                                                : ""
                                        }}{{
                                            formatNumber(
                                                detail.difference_quantity
                                            )
                                        }}
                                    </span>
                                </td>

                                <!-- Type Badge -->
                                <td class="px-4 py-3 text-center">
                                    <span
                                        v-if="detail.adjustment_type"
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            detail.adjustment_type ===
                                            'increase'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                        ]"
                                    >
                                        {{ detail.adjustment_type }}
                                    </span>
                                    <span v-else class="text-xs text-gray-400"
                                        >-</span
                                    >
                                </td>

                                <!-- Notes -->
                                <td
                                    class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400"
                                >
                                    {{ detail.notes || "-" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Statistics -->
                <div
                    v-if="opname.details && opname.details.length > 0"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                >
                    <div
                        class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg"
                    >
                        <div
                            class="text-sm text-green-600 dark:text-green-400 font-medium"
                        >
                            Total Increases
                        </div>
                        <div
                            class="text-2xl font-bold text-green-700 dark:text-green-300"
                        >
                            {{ getTotalIncreases() }}
                        </div>
                    </div>

                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <div
                            class="text-sm text-red-600 dark:text-red-400 font-medium"
                        >
                            Total Decreases
                        </div>
                        <div
                            class="text-2xl font-bold text-red-700 dark:text-red-300"
                        >
                            {{ getTotalDecreases() }}
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div
                            class="text-sm text-blue-600 dark:text-blue-400 font-medium"
                        >
                            Total Products
                        </div>
                        <div
                            class="text-2xl font-bold text-blue-700 dark:text-blue-300"
                        >
                            {{ opname.details.length }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { watch } from "vue";
import Modal from "../Overlays/Modal.vue";
import DetailItem from "./DetailItem.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    opname: {
        type: Object,
        default: null,
    },
});

defineEmits(["close"]);

// Debug: watch when props change
watch(
    () => props.show,
    (newVal) => {
        console.log("[AdjustmentDetails] Modal show changed:", newVal);
    }
);

watch(
    () => props.opname,
    (newVal) => {
        console.log("[OpnameDetails] Opname data:", newVal);
    },
    { deep: true }
);

const formatDate = (value) => {
    if (!value) return "-";
    return new Date(value).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0.00";
    return parseFloat(value).toFixed(2);
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        in_progress:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        completed:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        cancelled:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return (
        classes[status] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const getTotalIncreases = () => {
    if (!props.opname?.details) return 0;
    return props.opname.details.filter((d) => d.adjustment_type === "increase")
        .length;
};

const getTotalDecreases = () => {
    if (!props.opname?.details) return 0;
    return props.opname.details.filter((d) => d.adjustment_type === "decrease")
        .length;
};
</script>
