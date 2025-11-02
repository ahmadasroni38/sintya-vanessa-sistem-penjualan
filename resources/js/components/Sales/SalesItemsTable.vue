<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Sale Items</h3>
            <button
                type="button"
                @click="addItem"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-blue-900/20"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>
        </div>

        <div v-if="items.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
            No items added. Click "Add Item" to start.
        </div>

        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">Unit Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-28">Disc %</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-28">Tax %</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-36">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-20">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="(item, index) in items" :key="index">
                        <td class="px-4 py-3">
                            <select
                                v-model="item.product_id"
                                @change="onProductChange(index)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': errors?.[`items.${index}.product_id`] }"
                            >
                                <option value="">Select Product</option>
                                <option
                                    v-for="product in productOptions"
                                    :key="product.id"
                                    :value="product.id"
                                >
                                    {{ product.product_code }} - {{ product.product_name }}
                                </option>
                            </select>
                            <p v-if="errors?.[`items.${index}.product_id`]" class="mt-1 text-xs text-red-600">{{ errors[`items.${index}.product_id`] }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <input
                                v-model.number="item.quantity"
                                type="number"
                                step="0.01"
                                min="0.01"
                                @input="calculateItemTotal(index)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': errors?.[`items.${index}.quantity`] }"
                            />
                        </td>
                        <td class="px-4 py-3">
                            <input
                                v-model.number="item.unit_price"
                                type="number"
                                step="0.01"
                                min="0"
                                @input="calculateItemTotal(index)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': errors?.[`items.${index}.unit_price`] }"
                            />
                        </td>
                        <td class="px-4 py-3">
                            <input
                                v-model.number="item.discount_percent"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                @input="calculateItemTotal(index)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': errors?.[`items.${index}.discount_percent`] }"
                            />
                        </td>
                        <td class="px-4 py-3">
                            <input
                                v-model.number="item.tax_percent"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                @input="calculateItemTotal(index)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                :class="{ 'border-red-500': errors?.[`items.${index}.tax_percent`] }"
                            />
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                            {{ formatCurrency(item._total || 0) }}
                        </td>
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                @click="removeItem(index)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { watch, computed } from "vue";

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
});

const emit = defineEmits(["update:modelValue", "update:total"]);

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
    items.value.push({
        product_id: "",
        quantity: 1,
        unit_price: 0,
        discount_percent: 0,
        tax_percent: 0,
        notes: "",
        _total: 0,
    });
};

const removeItem = (index) => {
    items.value.splice(index, 1);
    calculateTotals();
};

const onProductChange = (index) => {
    const product = props.productOptions.find(
        (p) => p.id === items.value[index].product_id
    );
    if (product) {
        // You can set default unit price from product if available
        // items.value[index].unit_price = product.selling_price || 0;
        items.value[index].product = product;
    }
    calculateItemTotal(index);
};

const calculateItemTotal = (index) => {
    const item = items.value[index];
    const quantity = parseFloat(item.quantity) || 0;
    const unitPrice = parseFloat(item.unit_price) || 0;
    const discountPercent = parseFloat(item.discount_percent) || 0;
    const taxPercent = parseFloat(item.tax_percent) || 0;

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

    items.value.forEach((item) => {
        const quantity = parseFloat(item.quantity) || 0;
        const unitPrice = parseFloat(item.unit_price) || 0;
        subtotal += quantity * unitPrice;
        discount += parseFloat(item._discount_amount) || 0;
        tax += parseFloat(item._tax_amount) || 0;
    });

    const total = subtotal - discount + tax;

    emit("update:total", {
        subtotal,
        discount,
        tax,
        total,
    });
};

// Watch for changes in items to recalculate totals
watch(() => items.value.length, () => {
    calculateTotals();
}, { immediate: true });
</script>
