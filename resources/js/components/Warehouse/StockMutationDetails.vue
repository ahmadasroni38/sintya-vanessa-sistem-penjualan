<template>
    <div v-if="mutation" class="space-y-6">
        <!-- Header Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Transaction Number
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                    {{ mutation.transaction_number }}
                </p>
            </div>
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Transaction Date
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ formatDate(mutation.transaction_date) }}
                </p>
            </div>
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    From Location
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ mutation.from_location?.name || "-" }}
                </p>
            </div>
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    To Location
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ mutation.to_location?.name || "-" }}
                </p>
            </div>
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Reference Number
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ mutation.reference_number || "-" }}
                </p>
            </div>
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Status
                </label>
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1',
                        getStatusClass(mutation.status),
                    ]"
                >
                    {{ getStatusLabel(mutation.status) }}
                </span>
            </div>
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Created By
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ mutation.creator?.name || "-" }}
                </p>
            </div>
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Created At
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ formatDateTime(mutation.created_at) }}
                </p>
            </div>
        </div>

        <!-- Status History -->
        <div
            v-if="hasStatusHistory"
            class="border-t border-gray-200 dark:border-gray-700 pt-4"
        >
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                Status History
            </h4>
            <div class="space-y-3">
                <div
                    v-if="mutation.submitted_at"
                    class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg"
                >
                    <div class="flex items-center">
                        <ClockIcon
                            class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3"
                        />
                        <div>
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Submitted for Approval
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                By {{ mutation.submitter?.name || "Unknown" }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDateTime(mutation.submitted_at) }}
                    </span>
                </div>

                <div
                    v-if="mutation.approved_at"
                    class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg"
                >
                    <div class="flex items-center">
                        <CheckCircleIcon
                            class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3"
                        />
                        <div>
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Approved
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                By {{ mutation.approver?.name || "Unknown" }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDateTime(mutation.approved_at) }}
                    </span>
                </div>

                <div
                    v-if="mutation.completed_at"
                    class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg"
                >
                    <div class="flex items-center">
                        <CheckIcon
                            class="w-5 h-5 text-green-600 dark:text-green-400 mr-3"
                        />
                        <div>
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Completed
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                By {{ mutation.completer?.name || "Unknown" }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDateTime(mutation.completed_at) }}
                    </span>
                </div>

                <div
                    v-if="mutation.cancelled_at"
                    class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg"
                >
                    <div class="flex items-center">
                        <XCircleIcon
                            class="w-5 h-5 text-red-600 dark:text-red-400 mr-3"
                        />
                        <div>
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Cancelled
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Reason:
                                {{
                                    mutation.cancellation_reason ||
                                    "Not specified"
                                }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDateTime(mutation.cancelled_at) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div
            v-if="mutation.notes"
            class="border-t border-gray-200 dark:border-gray-700 pt-4"
        >
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                Notes
            </h4>
            <p
                class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg"
            >
                {{ mutation.notes }}
            </p>
        </div>

        <!-- Products Table -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                Products
            </h4>
            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                Product Code
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                Product Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                Quantity
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                Available Stock
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                Unit
                            </th>
                            <th
                                v-if="hasItemNotes"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                Notes
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                    >
                        <tr v-for="detail in mutation.details" :key="detail.id">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-mono"
                            >
                                {{ detail.product?.product_code }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ detail.product?.product_name }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(detail.quantity) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatNumber(detail.available_stock) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ detail.product?.unit?.code || "-" }}
                            </td>
                            <td
                                v-if="hasItemNotes"
                                class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                            >
                                {{ detail.notes || "-" }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Items
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ mutation.details?.length || 0 }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Quantity
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ formatNumber(totalQuantity) }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Status
                    </p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ getStatusLabel(mutation.status) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    ClockIcon,
    CheckCircleIcon,
    CheckIcon,
    XCircleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    mutation: {
        type: Object,
        required: true,
    },
    products: {
        type: Array,
        default: () => [],
    },
});

// Computed
const hasStatusHistory = computed(() => {
    return (
        props.mutation.submitted_at ||
        props.mutation.approved_at ||
        props.mutation.completed_at ||
        props.mutation.cancelled_at
    );
});

const hasItemNotes = computed(() => {
    return props.mutation.details?.some((detail) => detail.notes);
});

const totalQuantity = computed(() => {
    return (
        props.mutation.details?.reduce(
            (sum, detail) => sum + parseFloat(detail.quantity || 0),
            0
        ) || 0
    );
});

// Methods
const getStatusClass = (status) => {
    const classes = {
        draft: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
        pending:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        approved:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        completed:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        cancelled:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return classes[status] || classes.draft;
};

const getStatusLabel = (status) => {
    const labels = {
        draft: "Draft",
        pending: "Menunggu Persetujuan",
        approved: "Disetujui",
        completed: "Selesai",
        cancelled: "Dibatalkan",
    };
    return labels[status] || status;
};

const formatNumber = (value) => {
    return new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatDateTime = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>
