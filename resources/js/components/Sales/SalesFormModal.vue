<template>
    <Modal
        :is-open="isOpen"
        :title="editingItem ? t('sales.form.editTitle') : t('sales.form.newTitle')"
        size="6xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <FormInput
                    v-model="formData.transaction_date"
                    :label="t('sales.form.transactionDate')"
                    type="date"
                    required
                    :disabled="saving"
                    :error="errors?.transaction_date"
                />
                <FormSelect
                    v-model="formData.location_id"
                    :label="t('sales.form.location')"
                    :options="locationOptions"
                    required
                    :disabled="saving || formData.items.length > 0"
                    :error="errors?.location_id"
                    :readonly="formData.items.length > 0"
                />
                <FormSelect
                    v-model="formData.customer_id"
                    :label="t('sales.form.customer')"
                    :options="customerOptions"
                    :disabled="saving"
                    :error="errors?.customer_id"
                />
            </div>

            <FormTextarea
                v-model="formData.notes"
                :label="t('sales.form.notes')"
                :placeholder="t('sales.form.notesPlaceholder')"
                rows="2"
                :disabled="saving"
                :error="errors?.notes"
            />

            <!-- Items Table -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <SalesItemsTable
                    ref="salesItemsTableRef"
                    v-model="formData.items"
                    :errors="errors"
                    :productOptions="productOptions"
                    :saving="saving"
                    :canAddItems="!!formData.location_id"
                    @update:total="handleTotalUpdate"
                    @update:hasValidationError="hasValidationError = $event"
                />
            </div>

            <!-- Summary Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ t('sales.form.subtotal') }}:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(totals.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ t('sales.form.discount') }}:</span>
                            <span class="font-medium text-red-600">-{{ formatCurrency(totals.discount) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ t('sales.form.tax') }}:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(totals.tax) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span class="text-gray-900 dark:text-white">{{ t('sales.form.total') }}:</span>
                            <span class="text-blue-600">{{ formatCurrency(totals.total) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormSelect
                        v-model="formData.payment_method"
                        :label="t('sales.form.paymentMethod')"
                        :options="paymentMethodOptions"
                        required
                        :disabled="saving"
                        :error="errors?.payment_method"
                    />
                    <FormInput
                        v-model="formData.paid_amount"
                        :label="t('sales.form.paidAmount')"
                        type="number"
                        step="0.01"
                        min="0"
                        :disabled="saving"
                        :error="errors?.paid_amount"
                        @input="calculateChange"
                    />
                    <FormInput
                        v-model="formData.change_amount"
                        :label="t('sales.form.changeAmount')"
                        type="number"
                        step="0.01"
                        min="0"
                        readonly
                        :disabled="saving"
                        :error="errors?.change_amount"
                    />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    @click="handleClose"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    {{ t('sales.form.cancel') }}
                </button>
                <button
                    type="submit"
                    :disabled="saving || formData.items.length === 0 || hasValidationError"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="saving">{{ t('sales.form.saving') }}</span>
                    <span v-else>{{ editingItem ? t('sales.form.update') : t('sales.form.save') }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from "vue";
import { useI18n } from "vue-i18n";
import Modal from "@/components/Overlays/Modal.vue";
import FormInput from "@/components/Forms/FormInput.vue";
import FormSelect from "@/components/Forms/FormSelect.vue";
import FormTextarea from "@/components/Forms/FormTextarea.vue";
import SalesItemsTable from "./SalesItemsTable.vue";

const { t } = useI18n();

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

const emit = defineEmits(["close", "saved", "locationChanged"]);

const paymentMethodOptions = computed(() => [
    { value: "cash", label: t('sales.form.paymentMethodCash') },
    { value: "transfer", label: t('sales.form.paymentMethodTransfer') },
    { value: "credit", label: t('sales.form.paymentMethodCredit') },
]);

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

const totals = ref({
    subtotal: 0,
    discount: 0,
    tax: 0,
    total: 0,
});

const hasValidationError = ref(false);

const salesItemsTableRef = ref(null);

const isFormDirty = computed(() => {
    // Check if any field has been modified from initial state
    if (!props.editingItem) {
        // For new transaction, check if any meaningful data was entered
        return formData.value.location_id !== "" ||
               formData.value.customer_id !== "" ||
               formData.value.notes !== "" ||
               formData.value.items.length > 0;
    }
    return false; // Don't show warning when editing
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
    const oldTotal = totals.value.total;
    totals.value = newTotals;

    // Auto-fill paid amount with new total only if:
    // 1. It's a new transaction (not editing)
    // 2. AND paid amount equals old total (user hasn't manually changed it)
    if (!props.editingItem && formData.value.paid_amount === oldTotal) {
        formData.value.paid_amount = newTotals.total;
    }

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
    async (newValue) => {
        if (newValue) {
            const items = (newValue.details || []).map((detail) => {
                const quantity = parseFloat(detail.quantity) || 0;
                const unitPrice = parseFloat(detail.unit_price) || 0;
                const discountPercent = parseFloat(detail.discount_percent) || 0;
                const taxPercent = parseFloat(detail.tax_percent) || 0;

                // Pre-calculate values (same logic as calculateItemTotal)
                const subtotal = quantity * unitPrice;
                const discountAmount = subtotal * (discountPercent / 100);
                const subtotalAfterDiscount = subtotal - discountAmount;
                const taxAmount = subtotalAfterDiscount * (taxPercent / 100);
                const total = subtotalAfterDiscount + taxAmount;

                return {
                    id: detail.id,
                    product_id: detail.product_id,
                    quantity,
                    unit_price: unitPrice,
                    discount_percent: discountPercent,
                    tax_percent: taxPercent,
                    notes: detail.notes || "",
                    product: detail.product || null,
                    original_quantity: quantity, // Track original quantity for stock checking
                    // Initialize validation properties
                    _validation_errors: [],
                    _stock_warning: null,
                    _available_stock: (detail.product?.stock_quantity || 0) + quantity,
                    _total: total,
                    _discount_amount: discountAmount,
                    _tax_amount: taxAmount,
                };
            });

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

            // Trigger validation for loaded items after DOM updates
            await nextTick();
            if (salesItemsTableRef.value) {
                salesItemsTableRef.value.validateAllItems();
            }
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

watch(
    () => props.isOpen,
    async (newValue) => {
        if (!newValue && !props.editingItem) {
            resetForm();
        } else if (newValue) {
            // Smart defaults for new transactions
            if (!props.editingItem) {
                // Auto-select location if only one option
                if (props.locationOptions.length === 1) {
                    formData.value.location_id = props.locationOptions[0].value;
                    // Emit location change to load products for this location
                    emit("locationChanged", props.locationOptions[0].value);
                }
            }

            // Auto-focus to first input when modal opens
            await nextTick();
            const firstInput = document.querySelector('input[type="date"]');
            if (firstInput) {
                firstInput.focus();
            }
        }
    }
);

// Watch for location changes to reload products with location-specific stock
watch(
    () => formData.value.location_id,
    (newLocationId, oldLocationId) => {
        // Only react if location actually changed and it's not the initial load
        if (oldLocationId !== undefined && newLocationId !== oldLocationId) {
            // Clear all items when location changes
            if (formData.value.items.length > 0) {
                const confirmed = confirm(
                    t('sales.form.locationChangeWarning')
                );
                if (!confirmed) {
                    // Revert to old location
                    formData.value.location_id = oldLocationId;
                    return;
                }
                formData.value.items = [];
            }

            // Emit event to parent to reload products for new location
            if (newLocationId) {
                emit("locationChanged", newLocationId);
            }
        }
    }
);

const handleClose = () => {
    // Warn user about unsaved changes
    if (isFormDirty.value) {
        const confirmed = confirm(t('sales.form.unsavedChangesWarning'));
        if (!confirmed) {
            return;
        }
    }
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

// Keyboard shortcuts
const handleKeyDown = (event) => {
    // Only handle shortcuts when modal is open
    if (!props.isOpen) return;

    // ESC key to close modal
    if (event.key === "Escape") {
        event.preventDefault();
        handleClose();
    }
};

onMounted(() => {
    document.addEventListener("keydown", handleKeyDown);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeyDown);
});
</script>
