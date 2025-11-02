<template>
    <Modal
        :is-open="isOpen"
        :title="editingItem ? 'Edit Sale Transaction' : 'New Sale Transaction'"
        size="6xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <FormInput
                    v-model="formData.transaction_date"
                    label="Transaction Date"
                    type="date"
                    required
                    :error="errors?.transaction_date"
                />
                <FormSelect
                    v-model="formData.location_id"
                    label="Location"
                    :options="locationOptions"
                    required
                    :error="errors?.location_id"
                />
                <FormSelect
                    v-model="formData.customer_id"
                    label="Customer"
                    :options="customerOptions"
                    :error="errors?.customer_id"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <FormSelect
                    v-model="formData.payment_method"
                    label="Payment Method"
                    :options="paymentMethodOptions"
                    required
                    :error="errors?.payment_method"
                />
                <FormInput
                    v-model="formData.paid_amount"
                    label="Paid Amount"
                    type="number"
                    step="0.01"
                    min="0"
                    :error="errors?.paid_amount"
                    @input="calculateChange"
                />
                <FormInput
                    v-model="formData.change_amount"
                    label="Change Amount"
                    type="number"
                    step="0.01"
                    min="0"
                    readonly
                    :error="errors?.change_amount"
                />
            </div>

            <FormTextarea
                v-model="formData.notes"
                label="Notes"
                placeholder="Additional notes (optional)"
                rows="2"
                :error="errors?.notes"
            />

            <!-- Items Table -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <SalesItemsTable v-model="formData.items" :errors="errors" :productOptions="productOptions" @update:total="handleTotalUpdate" />
            </div>

            <!-- Summary Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(totals.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                            <span class="font-medium text-red-600">-{{ formatCurrency(totals.discount) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(totals.tax) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span class="text-gray-900 dark:text-white">Total:</span>
                            <span class="text-blue-600">{{ formatCurrency(totals.total) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    @click="handleClose"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="saving || formData.items.length === 0"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="saving">Saving...</span>
                    <span v-else>{{ editingItem ? "Update" : "Save" }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import Modal from "@/components/Overlays/Modal.vue";
import FormInput from "@/components/Forms/FormInput.vue";
import FormSelect from "@/components/Forms/FormSelect.vue";
import FormTextarea from "@/components/Forms/FormTextarea.vue";
import SalesItemsTable from "./SalesItemsTable.vue";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    editingItem: {
        type: Object,
        default: null,
    },
    locationOptions: {
        type: Array,
        default: () => [],
    },
    customerOptions: {
        type: Array,
        default: () => [],
    },
    productOptions: {
        type: Array,
        default: () => [],
    },
    saving: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["close", "saved"]);

const paymentMethodOptions = [
    { value: "cash", label: "Cash" },
    { value: "transfer", label: "Bank Transfer" },
    { value: "credit", label: "Credit" },
];

const formData = ref({
    transaction_date: new Date().toISOString().split("T")[0],
    location_id: "",
    customer_id: "",
    payment_method: "cash",
    paid_amount: 0,
    change_amount: 0,
    notes: "",
    items: [],
});

const errors = ref({});

const totals = ref({
    subtotal: 0,
    discount: 0,
    tax: 0,
    total: 0,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};

const handleTotalUpdate = (newTotals) => {
    totals.value = newTotals;
    calculateChange();
};

const calculateChange = () => {
    const paid = parseFloat(formData.value.paid_amount) || 0;
    const total = parseFloat(totals.value.total) || 0;
    formData.value.change_amount = Math.max(0, paid - total);
};

const resetForm = () => {
    formData.value = {
        transaction_date: new Date().toISOString().split("T")[0],
        location_id: "",
        customer_id: "",
        payment_method: "cash",
        paid_amount: 0,
        change_amount: 0,
        notes: "",
        items: [],
    };
    totals.value = {
        subtotal: 0,
        discount: 0,
        tax: 0,
        total: 0,
    };
};

watch(
    () => props.editingItem,
    (newValue) => {
        if (newValue) {
            const items = (newValue.details || []).map((detail) => ({
                id: detail.id,
                product_id: detail.product_id,
                quantity: parseFloat(detail.quantity) || 0,
                unit_price: parseFloat(detail.unit_price) || 0,
                discount_percent: parseFloat(detail.discount_percent) || 0,
                tax_percent: parseFloat(detail.tax_percent) || 0,
                notes: detail.notes || "",
                product: detail.product || null,
            }));

            formData.value = {
                transaction_date: newValue.transaction_date?.split(" ")[0] || new Date().toISOString().split("T")[0],
                location_id: newValue.location_id || "",
                customer_id: newValue.customer_id || "",
                payment_method: newValue.payment_method || "cash",
                paid_amount: parseFloat(newValue.paid_amount) || 0,
                change_amount: parseFloat(newValue.change_amount) || 0,
                notes: newValue.notes || "",
                items: items,
            };
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

watch(
    () => props.isOpen,
    (newValue) => {
        if (!newValue && !props.editingItem) {
            resetForm();
        }
    }
);

const handleClose = () => {
    emit("close");
};

const handleSubmit = () => {
    const isEditing = !!props.editingItem;
    const saleId = props.editingItem?.id;

    emit("saved", {
        formData: formData.value,
        isEditing,
        saleId,
    });
};
</script>
