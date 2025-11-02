<template>
    <Modal
        :is-open="isOpen"
        title="Sale Transaction Details"
        size="4xl"
        @close="$emit('close')"
        role="dialog"
        aria-labelledby="sale-details-title"
        aria-describedby="sale-details-content"
    >
        <div
            class="space-y-6 max-h-[calc(100vh-12rem)] overflow-y-auto custom-scrollbar"
        >
            <!-- Loading State -->
            <div v-if="!sale" class="text-center py-12">
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
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        ></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No Sale Data
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    The sale information could not be loaded.
                </p>
            </div>

            <!-- Sale Information -->
            <div v-else class="space-y-6">
                <!-- Header with Transaction Number and Status -->
                <div
                    class="flex items-start justify-between pb-6 border-b border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center shadow-lg"
                            :class="getHeaderColorClass(sale.status)"
                        >
                            <svg
                                class="w-8 h-8 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            <h2
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ sale.transaction_number }}
                            </h2>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium"
                            >
                                {{ formatDate(sale.transaction_date) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span :class="getStatusClass(sale.status)">
                            {{ getStatusLabel(sale.status) }}
                        </span>
                    </div>
                </div>

                <!-- Transaction Details Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Transaction Information -->
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex items-center gap-2"
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
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            Transaction Information
                        </h3>

                        <div class="space-y-4">
                            <!-- Customer -->
                            <div
                                class="group p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
                                    >
                                        <svg
                                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
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
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                                        >
                                            Customer
                                        </p>
                                        <p
                                            class="text-base font-semibold text-gray-900 dark:text-white mt-1"
                                        >
                                            {{
                                                sale.customer?.customer_name ||
                                                "Walk-in Customer"
                                            }}
                                        </p>
                                        <p
                                            v-if="sale.customer?.customer_code"
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                                        >
                                            {{ sale.customer.customer_code }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div
                                class="group p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
                                    >
                                        <svg
                                            class="w-5 h-5 text-purple-600 dark:text-purple-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            ></path>
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                                        >
                                            Location
                                        </p>
                                        <p
                                            class="text-base font-semibold text-gray-900 dark:text-white mt-1"
                                        >
                                            {{ sale.location?.name || "-" }}
                                        </p>
                                        <p
                                            v-if="sale.location?.code"
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                                        >
                                            {{ sale.location.code }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex items-center gap-2"
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
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                ></path>
                            </svg>
                            Payment Information
                        </h3>

                        <div class="space-y-4">
                            <!-- Payment Method -->
                            <div
                                class="group p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
                                    >
                                        <svg
                                            class="w-5 h-5 text-green-600 dark:text-green-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                                        >
                                            Payment Method
                                        </p>
                                        <p
                                            class="text-base font-semibold text-gray-900 dark:text-white mt-1"
                                        >
                                            {{
                                                getPaymentMethodLabel(
                                                    sale.payment_method
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Summary -->
                            <div
                                class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg border border-blue-200 dark:border-blue-800"
                            >
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Paid Amount:</span
                                        >
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white"
                                            >{{
                                                formatCurrency(sale.paid_amount)
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        v-if="sale.change_amount > 0"
                                        class="flex justify-between text-sm"
                                    >
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Change:</span
                                        >
                                        <span
                                            class="font-semibold text-green-600 dark:text-green-400"
                                            >{{
                                                formatCurrency(
                                                    sale.change_amount
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="space-y-4">
                    <CollapsibleSection
                        :title="`Items (${sale.details?.length || 0})`"
                        icon="shopping-cart"
                        :default-open="true"
                    >
                        <div class="overflow-x-auto custom-scrollbar">
                            <table
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <thead
                                    class="bg-gray-100 dark:bg-gray-800/70"
                                >
                                    <tr>
                                        <th
                                            class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Product
                                        </th>
                                        <th
                                            class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Qty
                                        </th>
                                        <th
                                            class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Unit Price
                                        </th>
                                        <th
                                            class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Disc %
                                        </th>
                                        <th
                                            class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Tax %
                                        </th>
                                        <th
                                            class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-gray-900/50 divide-y divide-gray-200 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="detail in sale.details"
                                        :key="detail.id"
                                        class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200"
                                    >
                                        <td
                                            class="px-5 py-4 text-sm text-gray-900 dark:text-white"
                                        >
                                            <div>
                                                <p class="font-semibold">
                                                    {{
                                                        detail.product
                                                            ?.product_name
                                                    }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    {{
                                                        detail.product
                                                            ?.product_code
                                                    }}
                                                </p>
                                            </div>
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm text-right font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{ detail.quantity }}
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-300"
                                        >
                                            {{
                                                formatCurrency(detail.unit_price)
                                            }}
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-300"
                                        >
                                            {{ detail.discount_percent }}%
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-300"
                                        >
                                            {{ detail.tax_percent }}%
                                        </td>
                                        <td
                                            class="px-5 py-4 text-sm text-right font-bold text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatCurrency(
                                                    detail.total_price
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CollapsibleSection>
                </div>

                <!-- Financial Summary -->
                <div
                    class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <h3
                        class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2"
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
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                            ></path>
                        </svg>
                        Financial Summary
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-base">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Subtotal:</span
                            >
                            <span
                                class="font-semibold text-gray-900 dark:text-white"
                                >{{ formatCurrency(sale.subtotal) }}</span
                            >
                        </div>
                        <div class="flex justify-between text-base">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Discount:</span
                            >
                            <span class="font-semibold text-red-600"
                                >-{{
                                    formatCurrency(sale.discount_amount)
                                }}</span
                            >
                        </div>
                        <div class="flex justify-between text-base">
                            <span class="text-gray-600 dark:text-gray-400"
                                >Tax:</span
                            >
                            <span
                                class="font-semibold text-gray-900 dark:text-white"
                                >{{ formatCurrency(sale.tax_amount) }}</span
                            >
                        </div>
                        <div
                            class="flex justify-between text-xl font-bold border-t-2 border-gray-300 dark:border-gray-600 pt-3 mt-3"
                        >
                            <span class="text-gray-900 dark:text-white"
                                >Total Amount:</span
                            >
                            <span class="text-blue-600 dark:text-blue-400">{{
                                formatCurrency(sale.total_amount)
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div
                    v-if="sale.notes"
                    class="p-5 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <h3
                        class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3 flex items-center gap-2"
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
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                            ></path>
                        </svg>
                        Notes
                    </h3>
                    <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                        {{ sale.notes }}
                    </p>
                </div>

                <!-- Metadata Section -->
                <div
                    v-if="sale.creator || sale.poster"
                    class="space-y-3 text-sm text-gray-600 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <div v-if="sale.creator" class="flex items-center gap-2">
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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            ></path>
                        </svg>
                        <span
                            >Created by
                            <strong class="text-gray-900 dark:text-white">{{
                                sale.creator.name
                            }}</strong>
                            on {{ formatDateTime(sale.created_at) }}</span
                        >
                    </div>
                    <div
                        v-if="sale.poster && sale.status === 'posted'"
                        class="flex items-center gap-2"
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
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                        <span
                            >Posted by
                            <strong class="text-gray-900 dark:text-white">{{
                                sale.poster.name
                            }}</strong>
                            on {{ formatDateTime(sale.posted_at) }}</span
                        >
                    </div>
                </div>

                <!-- Actions -->
                <div
                    class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t-2 border-gray-200 dark:border-gray-700 sticky bottom-0 bg-white dark:bg-gray-900"
                >
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:border-gray-500 transition-all shadow-sm hover:shadow"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, h } from "vue";
import Modal from "@/components/Overlays/Modal.vue";
import { useSales } from "@/composables/useSales";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    sale: {
        type: Object,
        default: null,
    },
});

defineEmits(["close"]);

const {
    formatCurrency,
    formatDate,
    formatDateTime,
    getStatusClass,
    getStatusLabel,
    getPaymentMethodLabel,
} = useSales();

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
                "shopping-cart": h(
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
                            d: "M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z",
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
                    class: "border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all hover:shadow-md",
                },
                [
                    h(
                        "button",
                        {
                            type: "button",
                            class: "w-full px-5 py-4 flex items-center justify-between text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-all focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500",
                            onClick: toggle,
                            "aria-expanded": isOpen.value,
                        },
                        [
                            h("div", { class: "flex items-center gap-3" }, [
                                getIcon(),
                                h("span", { class: "text-base" }, props.title),
                            ]),
                            h(
                                "svg",
                                {
                                    class: `w-5 h-5 transition-transform duration-300 ${
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
                            class: `transition-all duration-300 ease-in-out ${
                                isOpen.value
                                    ? "max-h-[500px] opacity-100"
                                    : "max-h-0 opacity-0"
                            } overflow-auto custom-scrollbar`,
                        },
                        [
                            h(
                                "div",
                                {
                                    class: "px-5 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30",
                                },
                                [slots.default?.()]
                            ),
                        ]
                    ),
                ]
            );
    },
};

const getHeaderColorClass = (status) => {
    const classes = {
        draft: "bg-gradient-to-br from-yellow-500 to-yellow-600",
        posted: "bg-gradient-to-br from-green-500 to-green-600",
        cancelled: "bg-gradient-to-br from-red-500 to-red-600",
    };
    return classes[status] || classes.draft;
};
</script>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(75, 85, 99, 0.5);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(75, 85, 99, 0.7);
}

/* Smooth transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke,
        opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
