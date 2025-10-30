<template>
    <div v-if="isOpen" style="display: none">DEBUG: Modal should be open</div>
    <Modal
        :is-open="isOpen"
        :title="editingItem ? 'Edit Stock Masuk' : 'Stock Masuk Baru'"
        size="6xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                    v-model="formData.transaction_date"
                    label="Tanggal Transaksi"
                    type="date"
                    required
                    :error="errors?.transaction_date"
                />
                <FormSelect
                    v-model="formData.location_id"
                    label="Lokasi"
                    :options="locationOptions"
                    required
                    :error="errors?.location_id"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                    v-model="formData.supplier_name"
                    label="Nama Supplier"
                    placeholder="Masukkan nama supplier"
                    required
                    :error="errors?.supplier_name"
                />
                <FormInput
                    v-model="formData.reference_number"
                    label="Nomor Referensi"
                    placeholder="PO, Invoice, dll (opsional)"
                    :error="errors?.reference_number"
                />
            </div>

            <FormTextarea
                v-model="formData.notes"
                label="Catatan"
                placeholder="Catatan tambahan (opsional)"
                rows="2"
                :error="errors?.notes"
            />

            <!-- Items Table -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <StockInItemsTable v-model="formData.items" :errors="errors" />
            </div>

            <!-- Actions -->
            <div
                class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
            >
                <button
                    type="button"
                    @click.stop.prevent="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    :disabled="saving || formData.items.length === 0"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="saving">Menyimpan...</span>
                    <span v-else>{{ editingItem ? "Update" : "Simpan" }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, watch } from "vue";
import Modal from "@/components/Overlays/Modal.vue";
import FormInput from "@/components/Forms/FormInput.vue";
import FormSelect from "@/components/Forms/FormSelect.vue";
import FormTextarea from "@/components/Forms/FormTextarea.vue";
import StockInItemsTable from "./StockInItemsTable.vue";

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
    saving: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["close", "save"]);

const formData = ref({
    transaction_date: new Date().toISOString().split("T")[0],
    location_id: "",
    supplier_name: "",
    reference_number: "",
    notes: "",
    items: [],
});

const resetForm = () => {
    formData.value = {
        transaction_date: new Date().toISOString().split("T")[0],
        location_id: "",
        supplier_name: "",
        reference_number: "",
        notes: "",
        items: [],
    };
};

watch(
    () => props.editingItem,
    (newValue) => {
        if (newValue) {
            console.log('StockInFormModal - editingItem changed:', newValue);

            // Format items dari details
            const items = (newValue.details || []).map((detail) => ({
                id: detail.id, // Include ID for tracking
                product_id: detail.product_id,
                quantity: parseFloat(detail.quantity) || 0,
                unit_price: parseFloat(detail.unit_price) || 0,
                notes: detail.notes || "",
                // Include product info untuk display
                product: detail.product || null,
            }));

            formData.value = {
                transaction_date: newValue.transaction_date,
                location_id: newValue.location_id?.toString() || newValue.location_id,
                supplier_name: newValue.supplier_name || "",
                reference_number: newValue.reference_number || "",
                notes: newValue.notes || "",
                items: items,
            };

            console.log('StockInFormModal - formData updated:', formData.value);
        } else {
            resetForm();
        }
    },
    { immediate: true, deep: true }
);

// Watch isOpen untuk debug
watch(
    () => props.isOpen,
    (newValue) => {
        console.log('StockInFormModal - isOpen changed:', newValue, 'editingItem:', props.editingItem);
    }
);

const handleClose = () => {
    console.log('StockInFormModal - handleClose called');
    emit('close');
};

const handleSubmit = () => {
    // Validate that we have at least one item with valid data
    const validItems = formData.value.items.filter(
        (item) => item.product_id && item.quantity > 0 && item.unit_price >= 0
    );

    if (validItems.length === 0) {
        return;
    }

    emit("save", {
        ...formData.value,
        items: validItems,
    });
};

defineExpose({
    resetForm,
});
</script>
