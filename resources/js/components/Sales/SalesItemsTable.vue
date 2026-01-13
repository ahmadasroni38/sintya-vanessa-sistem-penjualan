<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ t('sales.form.items.title') }}</h3>
            <button
                type="button"
                @click="addItem"
                :disabled="saving || !canAddItems"
                :title="!canAddItems ? t('sales.form.items.selectLocationFirst') : ''"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-blue-900/20 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ t('sales.form.items.addItem') }}
            </button>
        </div>

        <div v-if="items.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
            {{ t('sales.form.items.noItems') }}
        </div>

        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ t('sales.form.items.product') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">{{ t('sales.form.items.quantity') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">{{ t('sales.form.items.unitPrice') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-28">{{ t('sales.form.items.discountPercent') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-28">{{ t('sales.form.items.taxPercent') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-36">{{ t('sales.form.items.total') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-20">{{ t('sales.form.items.action') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template v-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-4 py-3">
                                <select
                                    v-model="item.product_id"
                                    @change="onProductChange(index)"
                                    :disabled="saving"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="{ 'border-red-500': errors?.[`items.${index}.product_id`] }"
                                >
                                    <option value="">{{ t('sales.form.items.selectProduct') }}</option>
                                    <option
                                        v-for="product in productOptions"
                                        :key="product.id"
                                        :value="product.id"
                                        :disabled="(product.stock_quantity || 0) === 0"
                                    >
                                        {{ product.product_code }} - {{ product.product_name }}
                                        (Stock: {{ product.stock_quantity || 0 }})
                                    </option>
                                </select>
                                <p v-if="errors?.[`items.${index}.product_id`]" class="mt-1 text-xs text-red-600">{{ errors[`items.${index}.product_id`] }}</p>
                                <div v-if="item.product" class="mt-1 text-xs flex items-center gap-1">
                                    <span
                                        :class="{
                                            'text-red-600 dark:text-red-400': (item._available_stock || item.product.stock_quantity || 0) === 0,
                                            'text-orange-600 dark:text-orange-400': (item._available_stock || item.product.stock_quantity || 0) > 0 && (item._available_stock || item.product.stock_quantity || 0) < 10,
                                            'text-green-600 dark:text-green-400': (item._available_stock || item.product.stock_quantity || 0) >= 10
                                        }"
                                    >
                                        <span class="font-medium">●</span> Stock: {{ item._available_stock || item.product.stock_quantity || 0 }}
                                    </span>
                                    <span v-if="(item._available_stock || item.product.stock_quantity || 0) === 0" class="text-red-600 dark:text-red-400">{{ t('sales.form.items.outOfStock') }}</span>
                                    <span v-else-if="(item._available_stock || item.product.stock_quantity || 0) < 10" class="text-orange-600 dark:text-orange-400">{{ t('sales.form.items.lowStock') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model.number="item.quantity"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    :disabled="saving"
                                    @input="calculateItemTotal(index)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="{ 'border-red-500': errors?.[`items.${index}.quantity`] || (item._validation_errors && item._validation_errors.length > 0) }"
                                />
                                <p v-if="errors?.[`items.${index}.quantity`]" class="mt-1 text-xs text-red-600">
                                    {{ errors[`items.${index}.quantity`] }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model.number="item.unit_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :disabled="saving"
                                    @input="calculateItemTotal(index)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="{ 'border-red-500': errors?.[`items.${index}.unit_price`] || (item._validation_errors && item._validation_errors.length > 0) }"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model.number="item.discount_percent"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    :disabled="saving"
                                    @input="calculateItemTotal(index)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="{ 'border-red-500': errors?.[`items.${index}.discount_percent`] || (item._validation_errors && item._validation_errors.length > 0) }"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model.number="item.tax_percent"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    :disabled="saving"
                                    @input="calculateItemTotal(index)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="{ 'border-red-500': errors?.[`items.${index}.tax_percent`] || (item._validation_errors && item._validation_errors.length > 0) }"
                                />
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(item._total || 0) }}
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    :disabled="saving"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="item._validation_errors && item._validation_errors.length > 0">
                            <td colspan="7" class="px-4 py-2 bg-red-50 dark:bg-red-900/20">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <ul class="text-xs text-red-600 dark:text-red-400 space-y-1">
                                        <li v-for="(error, errorIndex) in item._validation_errors" :key="errorIndex">
                                            {{ error }}
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="item._stock_warning && (!item._validation_errors || item._validation_errors.length === 0)">
                            <td colspan="7" class="px-4 py-2 bg-orange-50 dark:bg-orange-900/20">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="text-xs text-orange-600 dark:text-orange-400">
                                        {{ item._stock_warning }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { watch, computed, nextTick } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    productOptions: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    saving: {
        type: Boolean,
        default: false,
    },
    canAddItems: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:modelValue", "update:total", "update:hasValidationError"]);

const items = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};

const addItem = () => {
    const newItem = {
        product_id: "",
        quantity: 1,
        unit_price: 0,
        discount_percent: 0,
        tax_percent: 0,
        notes: "",
        _total: 0,
        _validation_errors: [],
        _stock_warning: null,
        _available_stock: 0,
        original_quantity: 0, // New items have no original quantity
        product: null, // Initialize product reference
    };

    const newIndex = items.value.length;
    items.value.push(newItem);

    // Use nextTick to ensure item is fully added before validation
    nextTick(() => {
        calculateItemTotal(newIndex);
    });
};

const removeItem = (index) => {
    const item = items.value[index];

    // Ask for confirmation if item has data
    const hasData = item.product_id || item.quantity > 1 || item.unit_price > 0;
    if (hasData) {
        const productName = item.product?.product_name || t('sales.form.items.confirmRemove').replace('?', '');
        const confirmed = confirm(`${t('sales.form.items.confirmRemove')} ${productName}?`);
        if (!confirmed) {
            return;
        }
    }

    items.value.splice(index, 1);
    calculateTotals();
};

const onProductChange = (index) => {
    const product = props.productOptions.find(
        (p) => p.id === items.value[index].product_id
    );
    if (product) {
        // Auto-fill unit price from product's selling price
        items.value[index].unit_price = product.selling_price || 0;
        items.value[index].product = product;

        // Calculate available stock (for new items, original_quantity is 0)
        const originalQuantity = items.value[index].original_quantity || 0;
        items.value[index]._available_stock = (product.stock_quantity || 0) + originalQuantity;
    }
    calculateItemTotal(index);
};

const calculateItemTotal = (index) => {
    const item = items.value[index];
    const quantity = parseFloat(item.quantity) || 0;
    const unitPrice = parseFloat(item.unit_price) || 0;
    const discountPercent = parseFloat(item.discount_percent) || 0;
    const taxPercent = parseFloat(item.tax_percent) || 0;

    // Reset all validation errors and warnings
    item._validation_errors = [];
    item._stock_warning = null;

    // Validate product selection
    if (!item.product_id || item.product_id === "") {
        item._validation_errors.push(t('sales.form.items.validationSelectProduct'));
    }

    // Validate quantity
    if (quantity <= 0) {
        item._validation_errors.push(t('sales.form.items.validationQuantityGreaterZero'));
    }

    // Validate unit price
    if (unitPrice <= 0) {
        item._validation_errors.push(t('sales.form.items.validationUnitPriceGreaterZero'));
    }

    // Validate discount percentage
    if (discountPercent < 0 || discountPercent > 100) {
        item._validation_errors.push(t('sales.form.items.validationDiscountRange'));
    }

    // Validate tax percentage
    if (taxPercent < 0 || taxPercent > 100) {
        item._validation_errors.push(t('sales.form.items.validationTaxRange'));
    }

    // Check stock availability (only if product is selected and quantity is valid)
    if (item.product_id) {
        const currentStock = item.product?.stock_quantity || 0;

        // For editing transactions, we need to add back the original quantity
        // because that quantity is still "in stock" until the transaction is updated
        const originalQuantity = item.original_quantity || 0;
        const availableStock = currentStock + originalQuantity;

        // Store calculated available stock for display
        item._available_stock = availableStock;

        // Validate stock availability
        if (availableStock === 0) {
            item._validation_errors.push(t('sales.form.items.validationOutOfStock'));
        } else if (quantity > availableStock) {
            item._validation_errors.push(`${t('sales.form.items.validationExceedsStock')} (${availableStock})`);
        }

        // Add low stock warning (not an error, just info)
        if (availableStock > 0 && availableStock < 10 && quantity <= availableStock) {
            item._stock_warning = t('sales.form.items.warningLowStock', { count: availableStock });
        } else {
            item._stock_warning = null;
        }
    }

    // Calculate subtotal
    const subtotal = quantity * unitPrice;

    // Calculate discount amount
    const discountAmount = subtotal * (discountPercent / 100);

    // Calculate subtotal after discount
    const subtotalAfterDiscount = subtotal - discountAmount;

    // Calculate tax amount
    const taxAmount = subtotalAfterDiscount * (taxPercent / 100);

    // Calculate total
    item._total = subtotalAfterDiscount + taxAmount;
    item._discount_amount = discountAmount;
    item._tax_amount = taxAmount;

    calculateTotals();
};

const calculateTotals = () => {
    let subtotal = 0;
    let discount = 0;
    let tax = 0;
    let hasValidationError = false;

    items.value.forEach((item) => {
        const quantity = parseFloat(item.quantity) || 0;
        const unitPrice = parseFloat(item.unit_price) || 0;
        subtotal += quantity * unitPrice;
        discount += parseFloat(item._discount_amount) || 0;
        tax += parseFloat(item._tax_amount) || 0;

        // Check if any item has validation errors
        if (item._validation_errors && item._validation_errors.length > 0) {
            hasValidationError = true;
        }
    });

    const total = subtotal - discount + tax;

    emit("update:total", {
        subtotal,
        discount,
        tax,
        total,
    });

    emit("update:hasValidationError", hasValidationError);
};

// Watch for changes in items to recalculate totals
watch(() => items.value.length, () => {
    calculateTotals();
}, { immediate: true });

// Validate all items (can be called from parent)
const validateAllItems = () => {
    items.value.forEach((item, index) => {
        calculateItemTotal(index);
    });
};

// Expose for parent component
defineExpose({
    validateAllItems,
});
</script>
