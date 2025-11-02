<template>
    <Modal :is-open="isOpen" title="Sale Transaction Details" size="4xl" @close="$emit('close')">
        <div v-if="sale" class="space-y-6">
            <!-- Header Information -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction Number</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ sale.transaction_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(sale.transaction_date) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ sale.customer?.customer_name || "Walk-in Customer" }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ sale.location?.name || "-" }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ getPaymentMethodLabel(sale.payment_method) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <span :class="getStatusClass(sale.status)" class="inline-flex mt-1 px-2 py-1 text-xs font-semibold rounded-full">
                        {{ getStatusLabel(sale.status) }}
                    </span>
                </div>
            </div>

            <!-- Items Table -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Disc %</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tax %</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="detail in sale.details" :key="detail.id">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ detail.product?.product_code }} - {{ detail.product?.product_name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">{{ detail.quantity }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">{{ formatCurrency(detail.unit_price) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">{{ detail.discount_percent }}%</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">{{ detail.tax_percent }}%</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">{{ formatCurrency(detail.total_price) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(sale.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                            <span class="font-medium text-red-600">-{{ formatCurrency(sale.discount_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(sale.tax_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span class="text-gray-900 dark:text-white">Total:</span>
                            <span class="text-blue-600">{{ formatCurrency(sale.total_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Paid Amount:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(sale.paid_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Change:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(sale.change_amount) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes and Metadata -->
            <div v-if="sale.notes || sale.creator || sale.poster" class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                <div v-if="sale.notes">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ sale.notes }}</p>
                </div>
                <div v-if="sale.creator" class="text-sm text-gray-600 dark:text-gray-400">
                    Created by: {{ sale.creator.name }} on {{ formatDateTime(sale.created_at) }}
                </div>
                <div v-if="sale.poster && sale.status === 'posted'" class="text-sm text-gray-600 dark:text-gray-400">
                    Posted by: {{ sale.poster.name }} on {{ formatDateTime(sale.posted_at) }}
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
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

const { formatCurrency, formatDate, formatDateTime, getStatusClass, getStatusLabel, getPaymentMethodLabel } = useSales();
</script>
