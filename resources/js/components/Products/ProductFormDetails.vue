<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <EnhancedFormSelect
                v-model="form.product_type"
                label="Product Type"
                :options="productTypeOptions"
                required
                :disabled="disabled"
                :error="getFieldError('product_type')"
                :allow-add="true"
                add-endpoint="/product-types"
                add-field="name"
                @update:modelValue="validateField('product_type')"
                @item-added="handleProductTypeAdded"
            />
            <EnhancedFormSelect
                v-model="form.unit_id"
                label="Unit"
                :options="unitOptions"
                required
                :disabled="disabled"
                :error="getFieldError('unit_id')"
                :allow-add="true"
                add-endpoint="/units"
                add-field="name"
                @update:modelValue="validateField('unit_id')"
                @item-added="handleUnitAdded"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormSelect
                v-model="form.category_id"
                label="Category"
                :options="categoryOptions"
                :disabled="disabled"
                :error="getFieldError('category_id')"
                placeholder="Select a category (optional)"
                @update:modelValue="validateField('category_id')"
            />
            <FormSelect
                v-model="form.is_active"
                label="Status"
                :options="statusOptions"
                required
                :disabled="disabled"
                :error="getFieldError('is_active')"
                @update:modelValue="validateField('is_active')"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput
                v-model="form.purchase_price"
                label="Purchase Price"
                type="number"
                step="0.01"
                placeholder="0.00"
                required
                :disabled="disabled"
                :error="getFieldError('purchase_price')"
                @blur="validateField('purchase_price')"
            />
            <FormInput
                v-model="form.selling_price"
                label="Selling Price"
                type="number"
                step="0.01"
                placeholder="0.00"
                :disabled="disabled"
                :error="getFieldError('selling_price')"
                @blur="validateField('selling_price')"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput
                v-model="form.minimum_stock"
                label="Minimum Stock"
                type="number"
                placeholder="0"
                required
                :disabled="disabled"
                :error="getFieldError('minimum_stock')"
                @blur="validateField('minimum_stock')"
            />
            <FormInput
                v-model="form.maximum_stock"
                label="Maximum Stock"
                type="number"
                placeholder="0"
                required
                :disabled="disabled"
                :error="getFieldError('maximum_stock')"
                @blur="validateField('maximum_stock')"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import FormInput from "../Forms/FormInput.vue";
import FormSelect from "../Forms/FormSelect.vue";
import EnhancedFormSelect from "../Forms/EnhancedFormSelect.vue";
import { useProductFormValidation } from "../../composables/useProductFormValidation";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    disabled: {
        type: Boolean,
        default: false,
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
    "validate-field",
    "product-type-added",
    "unit-added",
]);

// Use the composable to get options
const { getProductTypeOptions, getStatusOptions } = useProductFormValidation();

const productTypeOptions = computed(() => getProductTypeOptions());
const statusOptions = computed(() => getStatusOptions());

const getFieldError = (field) => {
    return props.errors[field] ? props.errors[field][0] : "";
};

const validateField = (field) => {
    emit("validate-field", field);
};

const handleProductTypeAdded = (newItem) => {
    emit("product-type-added", newItem);
};

const handleUnitAdded = (newItem) => {
    emit("unit-added", newItem);
};
</script>
