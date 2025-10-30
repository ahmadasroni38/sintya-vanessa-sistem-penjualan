<template>
    <div class="space-y-4">
        <FormSelect
            v-model="selectedProductId"
            :label="label"
            :options="productOptions"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            @change="onProductChange"
        />

        <!-- Product Info Display -->
        <div
            v-if="selectedProduct"
            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Product Code
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedProduct.code }}
                    </p>
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Category
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedProduct.category }}
                    </p>
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Unit
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedProduct.unit }}
                    </p>
                </div>
                <div v-if="showPrice">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Price
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ formatCurrency(selectedProduct.price) }}
                    </p>
                </div>
                <div v-if="showStock">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Available Stock
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ availableStock }} {{ selectedProduct.unit }}
                    </p>
                </div>
                <div v-if="showDescription && selectedProduct.description">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Description
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedProduct.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import FormSelect from "../Forms/FormSelect.vue";
import { dummyDataService } from "../../../services/warehouseService";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    label: {
        type: String,
        default: "Product",
    },
    placeholder: {
        type: String,
        default: "Select a product",
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    showPrice: {
        type: Boolean,
        default: false,
    },
    showStock: {
        type: Boolean,
        default: false,
    },
    showDescription: {
        type: Boolean,
        default: false,
    },
    locationId: {
        type: [String, Number],
        default: null,
    },
    filterCategory: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

// State
const products = ref([]);
const stockLevels = ref({});

// Computed
const selectedProductId = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const selectedProduct = computed(() => {
    return products.value.find((p) => p.id === selectedProductId.value);
});

const productOptions = computed(() => {
    let filteredProducts = products.value;

    if (props.filterCategory) {
        filteredProducts = filteredProducts.filter(
            (p) => p.category === props.filterCategory
        );
    }

    return filteredProducts.map((product) => ({
        value: product.id,
        label: `${product.code} - ${product.name}`,
    }));
});

const availableStock = computed(() => {
    if (!props.locationId || !selectedProductId.value) return 0;
    return stockLevels.value[props.locationId]?.[selectedProductId.value] || 0;
});

// Methods
const loadProducts = async () => {
    try {
        products.value = dummyDataService.generateProducts();

        // Generate simulated stock levels
        if (props.showStock) {
            const locations = dummyDataService.generateLocations();
            locations.forEach((location) => {
                stockLevels.value[location.id] = {};
                products.value.forEach((product) => {
                    stockLevels.value[location.id][product.id] =
                        Math.floor(Math.random() * 1000) + 100;
                });
            });
        }
    } catch (error) {
        console.error("Failed to load products:", error);
    }
};

const onProductChange = () => {
    emit("change", selectedProduct.value);
};

const formatCurrency = (value) => {
    if (!value) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(value);
};

// Watchers
watch(
    () => props.locationId,
    () => {
        if (props.showStock) {
            loadProducts();
        }
    }
);

onMounted(() => {
    loadProducts();
});
</script>
