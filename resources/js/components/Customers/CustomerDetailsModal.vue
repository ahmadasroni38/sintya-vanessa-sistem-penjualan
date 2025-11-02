<template>
    <Modal
        :is-open="isOpen"
        title="Customer Details"
        size="2xl"
        @close="$emit('close')"
        role="dialog"
        aria-labelledby="customer-details-title"
        aria-describedby="customer-details-content"
    >
        <div class="space-y-6">
            <!-- Loading State -->
            <div v-if="loading" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="animate-pulse">
                            <div
                                class="h-6 bg-gray-200 dark:bg-gray-700 rounded-lg w-48 mb-4"
                            ></div>
                        </div>
                        <div class="space-y-3">
                            <div class="animate-pulse">
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24 mb-2"
                                ></div>
                                <div
                                    class="h-5 bg-gray-200 dark:bg-gray-700 rounded-lg w-32"
                                ></div>
                            </div>
                            <div class="animate-pulse">
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28 mb-2"
                                ></div>
                                <div
                                    class="h-5 bg-gray-200 dark:bg-gray-700 rounded-lg w-40"
                                ></div>
                            </div>
                            <div class="animate-pulse">
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16 mb-2"
                                ></div>
                                <div
                                    class="h-6 bg-gray-200 dark:bg-gray-700 rounded-lg w-20"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="animate-pulse">
                            <div
                                class="h-6 bg-gray-200 dark:bg-gray-700 rounded-lg w-40 mb-4"
                            ></div>
                        </div>
                        <div class="space-y-3">
                            <div class="animate-pulse">
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16 mb-2"
                                ></div>
                                <div
                                    class="h-5 bg-gray-200 dark:bg-gray-700 rounded-lg w-36"
                                ></div>
                            </div>
                            <div class="animate-pulse">
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mb-2"
                                ></div>
                                <div
                                    class="h-5 bg-gray-200 dark:bg-gray-700 rounded-lg w-44"
                                ></div>
                            </div>
                            <div class="animate-pulse">
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20 mb-2"
                                ></div>
                                <div
                                    class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg w-full"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="!customer" class="text-center py-12">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full mb-4"
                >
                    <svg
                        class="w-8 h-8 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                        ></path>
                    </svg>
                </div>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    No Customer Data
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    The customer information could not be loaded.
                </p>
            </div>

            <!-- Customer Information -->
            <div v-else class="space-y-6">
                <!-- Customer Header with Status -->
                <div
                    class="flex items-start justify-between pb-4 border-b border-gray-200 dark:border-gray-700"
                >
                    <div>
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ customer.customer_name }}
                        </h2>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                        >
                            Code: {{ customer.customer_code }}
                        </p>
                    </div>
                    <div class="flex items-center">
                        <span :class="getStatusClass(customer.status)">
                            {{
                                customer.status.charAt(0).toUpperCase() +
                                customer.status.slice(1)
                            }}
                        </span>
                    </div>
                </div>

                <!-- Customer Details Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider"
                        >
                            Basic Information
                        </h3>

                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-5 h-5 text-gray-400 mt-0.5"
                                >
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Customer Code
                                    </p>
                                    <p
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ customer.customer_code }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-5 h-5 text-gray-400 mt-0.5"
                                >
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Customer Name
                                    </p>
                                    <p
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ customer.customer_name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider"
                        >
                            Contact Information
                        </h3>

                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-5 h-5 text-gray-400 mt-0.5"
                                >
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Phone
                                    </p>
                                    <p
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ customer.phone || "Not provided" }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-5 h-5 text-gray-400 mt-0.5"
                                >
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Email
                                    </p>
                                    <p
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ customer.email || "Not provided" }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address and Notes -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider"
                        >
                            Address
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ customer.address || "No address provided" }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3
                            class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider"
                        >
                            Notes
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ customer.notes || "No notes available" }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div v-if="customer.stats" class="space-y-4">
                    <CollapsibleSection
                        title="Customer Statistics"
                        icon="chart-bar"
                        :default-open="true"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div
                                class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800 transition-all hover:shadow-md"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 p-2 bg-blue-500 rounded-md"
                                    >
                                        <svg
                                            class="w-5 h-5 text-white"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p
                                            class="text-sm font-medium text-blue-600 dark:text-blue-400"
                                        >
                                            Total Sales
                                        </p>
                                        <p
                                            class="text-2xl font-bold text-blue-900 dark:text-blue-100"
                                        >
                                            {{
                                                customer.stats.total_sales || 0
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-800 transition-all hover:shadow-md"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 p-2 bg-green-500 rounded-md"
                                    >
                                        <svg
                                            class="w-5 h-5 text-white"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p
                                            class="text-sm font-medium text-green-600 dark:text-green-400"
                                        >
                                            Total Revenue
                                        </p>
                                        <p
                                            class="text-2xl font-bold text-green-900 dark:text-green-100"
                                        >
                                            {{
                                                formatCurrency(
                                                    customer.stats
                                                        .total_revenue || 0
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800 transition-all hover:shadow-md"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 p-2 bg-purple-500 rounded-md"
                                    >
                                        <svg
                                            class="w-5 h-5 text-white"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p
                                            class="text-sm font-medium text-purple-600 dark:text-purple-400"
                                        >
                                            Last Purchase
                                        </p>
                                        <p
                                            class="text-lg font-bold text-purple-900 dark:text-purple-100"
                                        >
                                            {{
                                                formatDate(
                                                    customer.stats
                                                        .last_purchase_date
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CollapsibleSection>
                </div>

                <!-- Recent Sales Section -->
                <div
                    v-if="customer.sales && customer.sales.length > 0"
                    class="space-y-4"
                >
                    <CollapsibleSection
                        :title="`Recent Sales (${customer.sales.length})`"
                        icon="receipt"
                        :default-open="false"
                    >
                        <div class="overflow-x-auto -mx-4 sm:mx-0 mt-4">
                            <div class="inline-block min-w-full align-middle">
                                <table
                                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                                    role="table"
                                >
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                            >
                                                Transaction #
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                            >
                                                Date
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                            >
                                                Amount
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                            >
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                                    >
                                        <tr
                                            v-for="sale in customer.sales"
                                            :key="sale.id"
                                            class="hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                                            @click="$emit('view-sale', sale)"
                                        >
                                            <td
                                                class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ sale.transaction_number }}
                                            </td>
                                            <td
                                                class="px-4 py-3 text-sm text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    formatDate(
                                                        sale.transaction_date
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    formatCurrency(
                                                        sale.total_amount
                                                    )
                                                }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span
                                                    :class="
                                                        getSaleStatusClass(
                                                            sale.status
                                                        )
                                                    "
                                                >
                                                    {{
                                                        sale.status.toUpperCase()
                                                    }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </CollapsibleSection>
                </div>

                <!-- Actions -->
                <div
                    class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="w-full sm:w-auto order-2 sm:order-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 transition-colors"
                    >
                        Close
                    </button>
                    <div
                        class="flex flex-col sm:flex-row gap-3 order-1 sm:order-2 w-full sm:w-auto"
                    >
                        <button
                            type="button"
                            @click="$emit('edit', customer)"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                ></path>
                            </svg>
                            Edit
                        </button>
                        <button
                            type="button"
                            @click="$emit('delete', customer)"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:border-red-600 dark:text-red-400 dark:hover:bg-gray-600 transition-colors"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                ></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { onMounted, onUnmounted, ref, h } from "vue";
import Modal from "@/components/Overlays/Modal.vue";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    customer: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "edit", "delete", "view-sale"]);

// Collapsible Section Component
const CollapsibleSection = {
    name: "CollapsibleSection",
    props: {
        title: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            default: "default",
        },
        defaultOpen: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, { slots }) {
        const isOpen = ref(props.defaultOpen);

        const toggle = () => {
            isOpen.value = !isOpen.value;
        };

        const getIcon = () => {
            const icons = {
                "chart-bar": h(
                    "svg",
                    {
                        class: "w-5 h-5",
                        fill: "none",
                        stroke: "currentColor",
                        viewBox: "0 0 24 24",
                    },
                    [
                        h("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z",
                        }),
                    ]
                ),
                receipt: h(
                    "svg",
                    {
                        class: "w-5 h-5",
                        fill: "none",
                        stroke: "currentColor",
                        viewBox: "0 0 24 24",
                    },
                    [
                        h("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
                        }),
                    ]
                ),
                default: h(
                    "svg",
                    {
                        class: "w-5 h-5",
                        fill: "none",
                        stroke: "currentColor",
                        viewBox: "0 0 24 24",
                    },
                    [
                        h("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M19 9l-7 7-7-7",
                        }),
                    ]
                ),
            };
            return icons[props.icon] || icons.default;
        };

        return () =>
            h(
                "div",
                {
                    class: "border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all",
                },
                [
                    h(
                        "button",
                        {
                            type: "button",
                            class: "w-full px-4 py-3 flex items-center justify-between text-sm font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500",
                            onClick: toggle,
                            "aria-expanded": isOpen.value,
                        },
                        [
                            h("div", { class: "flex items-center gap-2" }, [
                                getIcon(),
                                h("span", props.title),
                            ]),
                            h(
                                "svg",
                                {
                                    class: `w-4 h-4 transition-transform duration-200 ${
                                        isOpen.value
                                            ? "transform rotate-180"
                                            : ""
                                    }`,
                                    fill: "none",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24",
                                },
                                [
                                    h("path", {
                                        "stroke-linecap": "round",
                                        "stroke-linejoin": "round",
                                        "stroke-width": "2",
                                        d: "M19 9l-7 7-7-7",
                                    }),
                                ]
                            ),
                        ]
                    ),
                    h(
                        "div",
                        {
                            class: `transition-all duration-200 ${
                                isOpen.value
                                    ? "max-h-96 opacity-100"
                                    : "max-h-0 opacity-0"
                            } overflow-hidden`,
                        },
                        [
                            h(
                                "div",
                                {
                                    class: "px-4 py-3 border-t border-gray-200 dark:border-gray-700",
                                },
                                [slots.default?.()]
                            ),
                        ]
                    ),
                ]
            );
    },
};

// Status options
const statusOptions = [
    { value: "active", label: "Active" },
    { value: "inactive", label: "Inactive" },
];

// Keyboard navigation
const handleKeydown = (event) => {
    if (!props.isOpen) return;

    if (event.key === "Escape") {
        emit("close");
    }
};

onMounted(() => {
    document.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeydown);
});

// Utility functions
const getStatusClass = (status) => {
    const classes = {
        active: "inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        inactive:
            "inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200",
    };
    return classes[status] || classes.inactive;
};

const getSaleStatusClass = (status) => {
    const classes = {
        draft: "inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        posted: "inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        cancelled:
            "inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    };
    return classes[status] || classes.draft;
};

const formatCurrency = (value) => {
    if (!value && value !== 0) return "-";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>
