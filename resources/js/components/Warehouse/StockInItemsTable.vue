<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                Daftar Produk
            </h3>
            <button
                type="button"
                @click.stop.prevent="addItem"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
            >
                <PlusIcon class="w-4 h-4" />
                Tambah Produk
            </button>
        </div>

        <!-- Items Table -->
        <div
            class="border border-gray-200 rounded-lg dark:border-gray-700 relative"
            style="overflow: visible"
        >
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Produk
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider w-32"
                        >
                            Quantity
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider w-40"
                        >
                            Harga Satuan
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider w-48"
                        >
                            Catatan
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider w-40"
                        >
                            Total
                        </th>
                        <th
                            class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700"
                >
                    <tr v-if="items.length === 0">
                        <td
                            colspan="6"
                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"
                        >
                            Belum ada produk. Klik "Tambah Produk" untuk
                            menambahkan.
                        </td>
                    </tr>
                    <tr
                        v-for="(item, index) in items"
                        :key="index"
                        class="hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <td class="px-4 py-3">
                            <EnhancedFormSelect
                                v-model="item.product_id"
                                :options="productOptions"
                                placeholder="Pilih produk"
                                :error="getError(index, 'product_id')"
                                :disabled="loadingProducts"
                                @update:modelValue="
                                    updateItem(index, 'product_id')
                                "
                            />
                            <p
                                v-if="loadingProducts"
                                class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                            >
                                Loading products...
                            </p>
                        </td>
                        <td class="px-4 py-3">
                            <input
                                v-model.number="item.quantity"
                                type="number"
                                step="0.01"
                                min="0.01"
                                placeholder="0"
                                @input="updateItem(index)"
                                :class="[
                                    'w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2',
                                    getError(index, 'quantity')
                                        ? 'border-red-300 focus:ring-red-500 dark:border-red-600'
                                        : 'border-gray-300 focus:ring-blue-500 dark:border-gray-600',
                                    'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                                ]"
                            />
                            <p
                                v-if="getError(index, 'quantity')"
                                class="mt-1 text-xs text-red-600 dark:text-red-400"
                            >
                                {{ getError(index, "quantity") }}
                            </p>
                        </td>
                        <td class="px-4 py-3">
                            <input
                                v-model.number="item.unit_price"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0"
                                @input="updateItem(index)"
                                :class="[
                                    'w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2',
                                    getError(index, 'unit_price')
                                        ? 'border-red-300 focus:ring-red-500 dark:border-red-600'
                                        : 'border-gray-300 focus:ring-blue-500 dark:border-gray-600',
                                    'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                                ]"
                            />
                            <p
                                v-if="getError(index, 'unit_price')"
                                class="mt-1 text-xs text-red-600 dark:text-red-400"
                            >
                                {{ getError(index, "unit_price") }}
                            </p>
                        </td>
                        <td class="px-4 py-3">
                            <input
                                v-model="item.notes"
                                type="text"
                                placeholder="Catatan opsional"
                                @input="updateItem(index)"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            />
                        </td>
                        <td
                            class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white"
                        >
                            {{
                                formatCurrency(item.quantity * item.unit_price)
                            }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                @click.stop.prevent="removeItem(index)"
                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-900/20"
                                title="Hapus"
                            >
                                <TrashIcon class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot
                    v-if="items.length > 0"
                    class="bg-gray-50 dark:bg-gray-800"
                >
                    <tr>
                        <td
                            colspan="4"
                            class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white"
                        >
                            Total Keseluruhan:
                        </td>
                        <td
                            class="px-4 py-3 text-right font-bold text-lg text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(grandTotal) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Validation Error Summary -->
        <div
            v-if="errorMessage"
            class="p-4 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/20 dark:border-red-800"
        >
            <p class="text-sm text-red-600 dark:text-red-400">
                {{ errorMessage }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { PlusIcon, TrashIcon } from "@heroicons/vue/24/outline";
import EnhancedFormSelect from "@/components/Forms/EnhancedFormSelect.vue";
import api from "@/utils/api";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["update:modelValue"]);

// Local items state
const items = ref([]);

// Product options dari backend
const productOptions = ref([]);
const loadingProducts = ref(false);

// Fetch products dari backend
const fetchProducts = async () => {
    loadingProducts.value = true;
    try {
        const response = await api.get("/products", {
            params: {
                is_active: true,
                per_page: 1000, // Get all active products
            },
        });

        // Transform data untuk options
        productOptions.value = response.data.data.map((product) => ({
            value: product.id,
            label: `${product.product_code} - ${product.product_name}`,
            extra: {
                code: product.product_code,
                name: product.product_name,
                unit: product.unit?.name || "",
                purchase_price: product.purchase_price || 0,
            },
        }));
    } catch (error) {
        console.error("Error fetching products:", error);
        productOptions.value = [];
    } finally {
        loadingProducts.value = false;
    }
};

// Load products saat component mounted
onMounted(() => {
    fetchProducts();
});

// Computed
const grandTotal = computed(() => {
    return items.value.reduce((sum, item) => {
        const itemTotal = (item.quantity || 0) * (item.unit_price || 0);
        return sum + itemTotal;
    }, 0);
});

const errorMessage = computed(() => {
    if (props.errors?.items) {
        if (typeof props.errors.items === "string") {
            return props.errors.items;
        }
    }
    return null;
});

// Methods
const updateParent = () => {
    emit("update:modelValue", items.value);
};

const addItem = () => {
    items.value.push({
        product_id: null,
        quantity: 0,
        unit_price: 0,
        notes: "",
    });
    updateParent();
};

// Initialize items from modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        console.log('StockInItemsTable - modelValue changed:', newValue);

        if (newValue && newValue.length > 0) {
            // Map data dari parent, pastikan semua field ada
            items.value = newValue.map((item) => ({
                product_id: item.product_id || null,
                quantity: parseFloat(item.quantity) || 0,
                unit_price: parseFloat(item.unit_price) || 0,
                notes: item.notes || "",
            }));
            console.log('StockInItemsTable - items loaded:', items.value);
        } else if (items.value.length === 0) {
            // Add one empty row by default untuk form baru
            console.log('StockInItemsTable - adding empty row');
            addItem();
        }
    },
    { immediate: true, deep: true }
);

const removeItem = (index) => {
    if (items.value.length === 1) {
        // Don't remove the last item, just reset it
        items.value[0] = {
            product_id: null,
            quantity: 0,
            unit_price: 0,
            notes: "",
        };
    } else {
        items.value.splice(index, 1);
    }
    updateParent();
};

const updateItem = (index, field = null) => {
    // Ensure numeric values are valid
    if (isNaN(items.value[index].quantity)) items.value[index].quantity = 0;
    if (isNaN(items.value[index].unit_price)) items.value[index].unit_price = 0;

    // Auto-fill unit_price when product is selected (if not already filled)
    if (field === "product_id" && items.value[index].product_id) {
        const selectedProduct = productOptions.value.find(
            (p) => p.value === items.value[index].product_id
        );

        if (selectedProduct && selectedProduct.extra) {
            // Auto-fill dengan purchase_price jika unit_price masih 0
            if (
                items.value[index].unit_price === 0 &&
                selectedProduct.extra.purchase_price
            ) {
                items.value[index].unit_price = parseFloat(
                    selectedProduct.extra.purchase_price
                );
            }
        }
    }

    updateParent();
};

const formatCurrency = (value) => {
    if (!value || isNaN(value)) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const getError = (index, field) => {
    const key = `items.${index}.${field}`;
    return props.errors?.[key];
};

const getProductLabel = (productId) => {
    if (!productId) return "";
    const product = productOptions.value.find((p) => p.value === productId);
    return product ? product.label : "";
};
</script>
