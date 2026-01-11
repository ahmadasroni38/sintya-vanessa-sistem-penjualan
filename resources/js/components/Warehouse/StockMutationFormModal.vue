<template>
    <Modal
        :is-open="isOpen"
        :title="editingItem ? 'Edit Mutasi Stok' : 'Mutasi Stok Baru'"
        size="6xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                    v-model="formData.transaction_date"
                    label="Tanggal Mutasi"
                    type="date"
                    required
                    :error="errors?.transaction_date"
                />
                <FormInput
                    v-model="formData.reference_number"
                    label="Nomor Referensi"
                    placeholder="Nomor referensi (opsional)"
                    :error="errors?.reference_number"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormSelect
                    v-model="formData.from_location_id"
                    label="Lokasi Asal"
                    :options="locationOptions"
                    required
                    :error="errors?.from_location_id"
                    @update:modelValue="onFromLocationChange"
                />
                <FormSelect
                    v-model="formData.to_location_id"
                    label="Lokasi Tujuan"
                    :options="locationOptions"
                    required
                    :error="errors?.to_location_id"
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
                <StockMutationItemsTable
                    v-model="formData.items"
                    :products="products"
                    :loading-products="loadingProducts"
                    :errors="errors"
                    @item-changed="onItemChanged"
                />
            </div>

            <!-- Actions -->
            <div
                class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
            >
                <button
                    type="button"
                    @click.stop.prevent="handleClose"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    :disabled="saving || !canSubmit"
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
import { ref, watch, computed } from "vue";
import Modal from "@/components/Overlays/Modal.vue";
import FormInput from "@/components/Forms/FormInput.vue";
import FormSelect from "@/components/Forms/FormSelect.vue";
import FormTextarea from "@/components/Forms/FormTextarea.vue";
import StockMutationItemsTable from "./StockMutationItemsTable.vue";

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
    products: {
        type: Array,
        default: () => [],
    },
    loadingProducts: {
        type: Boolean,
        default: false,
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

const emit = defineEmits(["close", "save", "from-location-changed"]);

const formData = ref({
    transaction_date: new Date().toISOString().split("T")[0],
    from_location_id: "",
    to_location_id: "",
    reference_number: "",
    notes: "",
    items: [],
});

// Check if form can be submitted
const canSubmit = computed(() => {
    const hasValidItems = formData.value.items.length > 0 &&
        formData.value.items.every((item) => {
            const quantity = parseFloat(item.quantity);
            const isValid = item.product_id && !isNaN(quantity) && quantity > 0;

            // Debug: Log invalid items
            if (!isValid) {
                console.log('Invalid item:', {
                    product_id: item.product_id,
                    quantity: item.quantity,
                    parsed_quantity: quantity,
                    isNaN: isNaN(quantity)
                });
            }

            return isValid;
        });

    const result = (
        formData.value.from_location_id &&
        formData.value.to_location_id &&
        formData.value.from_location_id !== formData.value.to_location_id &&
        hasValidItems
    );

    // Debug: Log validation state
    console.log('Form validation:', {
        from_location_id: formData.value.from_location_id,
        to_location_id: formData.value.to_location_id,
        locations_different: formData.value.from_location_id !== formData.value.to_location_id,
        items_count: formData.value.items.length,
        hasValidItems,
        canSubmit: result
    });

    return result;
});

const resetForm = () => {
    formData.value = {
        transaction_date: new Date().toISOString().split("T")[0],
        from_location_id: "",
        to_location_id: "",
        reference_number: "",
        notes: "",
        items: [],
    };
};

const handleClose = () => {
    if (!props.saving) {
        resetForm();
        emit("close");
    }
};

const handleSubmit = () => {
    if (!canSubmit.value || props.saving) return;
    emit("save", { ...formData.value });
};

// Watch for from_location change to reload products with stock
const onFromLocationChange = () => {
    // Clear items when location changes
    formData.value.items = [];
    // Notify parent to reload products with stock
    if (formData.value.from_location_id) {
        emit("from-location-changed", formData.value.from_location_id);
    }
};

const onItemChanged = () => {
    // Handle item changes if needed
};

// Helper function to format date for HTML input
const formatDateForInput = (dateString) => {
    if (!dateString) return new Date().toISOString().split("T")[0];

    try {
        // Handle different date formats from API
        const date = new Date(dateString);

        // Check if date is valid
        if (isNaN(date.getTime())) {
            console.warn("Invalid date format received:", dateString);
            return new Date().toISOString().split("T")[0];
        }

        // Return in YYYY-MM-DD format for HTML date input
        return date.toISOString().split("T")[0];
    } catch (error) {
        console.warn("Error parsing date:", dateString, error);
        return new Date().toISOString().split("T")[0];
    }
};

// Watch editing item
watch(
    () => props.editingItem,
    (newValue) => {
        if (newValue) {
            const items = (newValue.details || []).map((detail) => ({
                id: detail.id,
                product_id: detail.product_id,
                quantity: parseFloat(detail.quantity) || 0,
                available_stock: parseFloat(detail.available_stock) || 0,
                notes: detail.notes || "",
                product: detail.product || null,
            }));

            formData.value = {
                transaction_date: formatDateForInput(newValue.transaction_date),
                from_location_id: newValue.from_location_id || "",
                to_location_id: newValue.to_location_id || "",
                reference_number: newValue.reference_number || "",
                notes: newValue.notes || "",
                items: items,
            };

            // Load products with stock for from_location
            if (newValue.from_location_id) {
                emit("from-location-changed", newValue.from_location_id);
            }
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

// Watch modal open state
watch(
    () => props.isOpen,
    (newValue) => {
        if (!newValue && !props.editingItem) {
            resetForm();
        }
    }
);
</script>
