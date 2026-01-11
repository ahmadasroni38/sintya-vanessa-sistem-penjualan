<template>
    <div class="space-y-4">
        <!-- Add Item Button -->
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Produk
            </h3>
            <button
                type="button"
                @click="addItem"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
            >
                <PlusIcon class="w-4 h-4" />
                Tambah Produk
            </button>
        </div>

        <!-- Empty State -->
        <div
            v-if="items.length === 0"
            class="text-center py-8 text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
        >
            <div class="flex flex-col items-center">
                <PlusIcon class="w-12 h-12 text-gray-400 mb-3" />
                <p class="text-lg font-medium">Belum ada produk ditambahkan</p>
                <p class="text-sm mt-1">Klik "Tambah Produk" untuk memulai</p>
            </div>
        </div>

        <!-- Items List -->
        <div v-else class="space-y-3">
            <div
                v-for="(item, index) in items"
                :key="index"
                class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600"
            >
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    <!-- Product Select -->
                    <div class="md:col-span-5">
                        <EnhancedFormSelect
                            v-model="item.product_id"
                            label="Produk"
                            :options="productOptions"
                            :loading="loadingProducts"
                            :error="errors?.[`items.${index}.product_id`]"
                            required
                            @update:modelValue="onProductChange(index)"
                        />
                    </div>

                    <!-- Quantity Input -->
                    <div class="md:col-span-2">
                        <FormInput
                            v-model.number="item.quantity"
                            label="Jumlah"
                            type="number"
                            min="0.01"
                            step="0.01"
                            :error="errors?.[`items.${index}.quantity`]"
                            required
                        />
                    </div>

                    <!-- Available Stock Display -->
                    <div class="md:col-span-3">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Stok Tersedia
                        </label>
                        <div
                            class="text-sm text-gray-900 dark:text-white font-medium"
                        >
                            {{
                                formatNumber(getAvailableStock(item.product_id))
                            }}
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ getProductUnit(item.product_id) }}
                            </span>
                        </div>
                        <div
                            v-if="
                                item.quantity >
                                getAvailableStock(item.product_id)
                            "
                            class="text-xs text-red-600 dark:text-red-400 mt-1"
                        >
                            ⚠️ Stok tidak mencukupi
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="md:col-span-2 flex items-end justify-end">
                        <button
                            type="button"
                            @click="removeItem(index)"
                            class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:bg-red-900/20"
                            title="Hapus Item"
                        >
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Item Notes -->
                <div v-if="item.product_id" class="mt-3">
                    <FormTextarea
                        v-model="item.notes"
                        label="Catatan (opsional)"
                        placeholder="Catatan untuk item ini"
                        rows="2"
                        :error="errors?.[`items.${index}.notes`]"
                    />
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div
            v-if="items.length > 0"
            class="border-t border-gray-200 dark:border-gray-700 pt-4"
        >
            <div class="flex justify-between items-center">
                <span
                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Total Item:
                </span>
                <span
                    class="text-lg font-semibold text-gray-900 dark:text-white"
                >
                    {{ items.length }} produk
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from "vue";
import EnhancedFormSelect from "@/components/Forms/EnhancedFormSelect.vue";
import FormInput from "@/components/Forms/FormInput.vue";
import FormTextarea from "@/components/Forms/FormTextarea.vue";
import { PlusIcon, XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    modelValue: {
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
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["update:modelValue", "item-changed"]);

// Local items array
const items = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

// Product options for select
const productOptions = computed(() => {
    return props.products.map((product) => ({
        value: product.id,
        label: `${product.product_code} - ${product.product_name}`,
        extra: {
            available_stock: product.available_stock || 0,
            unit: product.unit?.code || "",
        },
    }));
});

// Add new item
const addItem = () => {
    const newItem = {
        product_id: "",
        quantity: 1,
        notes: "",
        available_stock: 0,
    };
    items.value = [...items.value, newItem];
};

// Remove item
const removeItem = (index) => {
    items.value = items.value.filter((_, i) => i !== index);
};

// Handle product change
const onProductChange = (index) => {
    const item = items.value[index];
    const product = props.products.find((p) => p.id === item.product_id);

    if (product) {
        item.available_stock = product.available_stock || 0;
        // Reset quantity if it's more than available stock
        if (item.quantity > item.available_stock) {
            item.quantity = Math.min(item.quantity, item.available_stock);
        }
    } else {
        item.available_stock = 0;
    }

    emit("item-changed", { index, item });
};

// Get available stock for a product
const getAvailableStock = (productId) => {
    const product = props.products.find((p) => p.id === productId);
    return product ? product.available_stock || 0 : 0;
};

// Get product unit
const getProductUnit = (productId) => {
    const product = props.products.find((p) => p.id === productId);
    return product?.unit?.code || "";
};

// Format number
const formatNumber = (value) => {
    return new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

// Watch for changes and emit
watch(
    items,
    (newItems) => {
        emit("update:modelValue", newItems);
        emit("item-changed", newItems);
    },
    { deep: true }
);
</script>
