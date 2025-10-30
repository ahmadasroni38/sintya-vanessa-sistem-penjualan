<template>
    <Modal
        :is-open="isOpen && item !== null"
        title="Detail Stock Masuk"
        size="2xl"
        @close="$emit('close')"
    >
        <div v-if="item" class="space-y-6">
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <DetailItem label="Nomor Transaksi" :value="item.transaction_number" />
                <DetailItem label="Tanggal" :value="formatDate(item.transaction_date)" />
                <DetailItem label="Lokasi" :value="item.location?.name" />
                <DetailItem label="Supplier" :value="item.supplier_name" />
                <DetailItem label="Referensi" :value="item.reference_number || '-'" />
                <DetailItem label="Status">
                    <template #value>
                        <span :class="[
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            statusClass(item.status)
                        ]">
                            {{ statusLabel(item.status) }}
                        </span>
                    </template>
                </DetailItem>
                <DetailItem label="Dibuat Oleh" :value="item.creator?.name || '-'" />
                <DetailItem
                    v-if="item.posted_by"
                    label="Diposting Oleh"
                    :value="item.poster?.name"
                />
            </div>

            <!-- Items List -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    Daftar Produk
                </h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Kode Produk
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Nama Produk
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Quantity
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Harga Satuan
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                            <tr v-for="(detail, index) in item.details" :key="detail.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">
                                    {{ detail.product?.product_code }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-900 dark:text-white">
                                        {{ detail.product?.product_name }}
                                    </div>
                                    <div v-if="detail.notes" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ detail.notes }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                    {{ formatNumber(detail.quantity) }} {{ detail.product?.unit?.name }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                    {{ formatCurrency(detail.unit_price) }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                    {{ formatCurrency(detail.total_price) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                    Total Keseluruhan:
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-lg text-gray-900 dark:text-white">
                                    {{ formatCurrency(item.total_price) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div v-if="item.notes" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Catatan Transaksi
                </label>
                <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    {{ item.notes }}
                </p>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/components/Overlays/Modal.vue';
import DetailItem from './DetailItem.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    item: {
        type: Object,
        default: null,
    },
});

defineEmits(['close']);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const statusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
        posted: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };
    return classes[status] || classes.draft;
};

const statusLabel = (status) => {
    const labels = {
        draft: 'Draft',
        posted: 'Posted',
        cancelled: 'Cancelled',
    };
    return labels[status] || status;
};
</script>
