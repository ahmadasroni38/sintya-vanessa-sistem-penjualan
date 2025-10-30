<template>
    <Modal
        :is-open="isOpen"
        :title="editingProduct ? 'Edit Product' : 'Add New Product'"
        @close="handleClose"
    >
        <form @submit.prevent="handleSave" class="space-y-6">
            <!-- Basic Information Section -->
            <ProductFormBasicInfo
                :form="form"
                :errors="errors"
                :disabled="saving"
                :editing-product="editingProduct"
                :generating-code="generatingCode"
                @generate-code="generateProductCode"
                @validate-field="validateField"
            />

            <!-- Product Details Section -->
            <ProductFormDetails
                :form="form"
                :errors="errors"
                :disabled="saving"
                :unit-options="unitOptions"
                :category-options="categoryOptions"
                @validate-field="validateField"
                @product-type-added="handleProductTypeAdded"
                @unit-added="handleUnitAdded"
            />

            <!-- Form Actions -->
            <ProductFormActions
                :loading="saving"
                :disabled="!isFormValid"
                :editing-product="editingProduct"
                @cancel="handleClose"
            />
        </form>
    </Modal>
</template>

<script setup>
import { ref, watch } from "vue";
import Modal from "../Overlays/Modal.vue";
import ProductFormBasicInfo from "./ProductFormBasicInfo.vue";
import ProductFormDetails from "./ProductFormDetails.vue";
import ProductFormActions from "./ProductFormActions.vue";
import { useProductFormValidation } from "../../composables/useProductFormValidation";
import { useNotificationStore } from "../../stores/notification";
import { useProducts } from "../../composables/useProducts";
import api from "../../utils/api";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    editingProduct: {
        type: Object,
        default: null,
    },
    unitOptions: {
        type: Array,
        default: () => [],
    },
    categoryOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits([
    "close",
    "saved",
    "unit-added",
    "product-type-added",
]);

// Use composables
const notificationStore = useNotificationStore();
const { generateProductCode: generateCode } = useProducts();
const {
    form,
    errors,
    isFormValid,
    validateField,
    validateForm,
    resetForm,
    setFormData,
    setServerErrors,
    prepareFormData,
} = useProductFormValidation();

// Local state
const saving = ref(false);
const generatingCode = ref(false);
const productTypeOptions = ref([
    { value: "finished_goods", label: "Finished Goods" },
    { value: "raw_material", label: "Raw Material" },
    { value: "consumable", label: "Consumable" },
]);

// Watch for modal opening and editing product changes
watch(
    () => props.isOpen,
    (newValue) => {
        if (newValue) {
            if (props.editingProduct) {
                populateForm(props.editingProduct);
            } else {
                resetForm();
            }
        }
    }
);

watch(
    () => props.editingProduct,
    (newProduct) => {
        if (newProduct && props.isOpen) {
            populateForm(newProduct);
        }
    }
);

const populateForm = (product) => {
    setFormData({
        product_code: product.product_code,
        product_name: product.product_name,
        description: product.description || "",
        product_type: product.product_type,
        category_id: product.category_id,
        unit_id: product.unit_id,
        purchase_price: product.purchase_price || 0,
        selling_price: product.selling_price || 0,
        minimum_stock: product.minimum_stock || 0,
        maximum_stock: product.maximum_stock || 0,
        is_active: product.is_active,
    });
};

const handleSave = async () => {
    if (!validateForm()) {
        notificationStore.error("Please fill all required fields correctly");
        return;
    }

    try {
        saving.value = true;
        errors.value = {};

        const formData = prepareFormData();

        emit("saved", {
            formData,
            isEditing: !!props.editingProduct,
            productId: props.editingProduct?.id,
        });
    } catch (error) {
        console.error("Error saving product:", error);

        if (error.response?.data?.errors) {
            setServerErrors(error.response.data.errors);
        }
    } finally {
        saving.value = false;
    }
};

const handleClose = () => {
    emit("close");
};

const generateProductCode = async () => {
    generatingCode.value = true;
    try {
        const code = await generateCode(form.product_type);
        form.product_code = code;
        notificationStore.success("Product code generated successfully");
    } catch (error) {
        console.error("Error generating product code:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to generate product code"
        );
    } finally {
        generatingCode.value = false;
    }
};

const handleUnitAdded = (newUnit) => {
    // Emit to parent to refresh unit options
    emit("unit-added", newUnit);
};

const handleProductTypeAdded = (newProductType) => {
    // Emit to parent to refresh product type options
    emit("product-type-added", newProductType);
};
</script>
