<template>
    <Modal
        :is-open="show"
        :title="isEdit ? 'Edit Stock Opname' : 'New Stock Opname'"
        size="full"
        @close="$emit('close')"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Master Information -->
            <div class="space-y-4 border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Stock Opname Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <FormInput
                        v-model="formData.opname_date"
                        label="Date"
                        type="date"
                        required
                    />
                    <FormSelect
                        v-model="formData.location_id"
                        label="Location"
                        :options="locationOptions"
                        required
                        @change="onLocationChange"
                    />
                    <FormInput
                        v-model="formData.opname_number"
                        label="Opname Number"
                        type="text"
                        readonly
                        disabled
                        class="bg-gray-100 dark:bg-gray-700"
                    />
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <FormTextarea
                        v-model="formData.description"
                        label="Description"
                        placeholder="Enter stock opname description"
                        rows="2"
                    />

                    <FormTextarea
                        v-model="formData.notes"
                        label="Notes"
                        placeholder="Enter additional notes (optional)"
                        rows="2"
                    />
                </div>
            </div>

            <!-- All Products Table -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        All Products - Fill Physical Quantity Only
                    </h3>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ filteredProducts.length }} products
                    </div>
                </div>

                <!-- Products Table -->
                <div
                    class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg"
                >
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12"
                                >
                                    #
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Product Code
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Product Name
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Category
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Unit
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    System Qty
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Physical Qty
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Difference
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Notes
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr v-if="filteredProducts.length === 0">
                                <td
                                    colspan="10"
                                    class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    No products available. Please select a
                                    location to see products.
                                </td>
                            </tr>
                            <tr
                                v-for="(product, index) in filteredProducts"
                                :key="product.id"
                                :class="[
                                    'hover:bg-gray-50 dark:hover:bg-gray-800',
                                    productDetail(product.id)?.difference_quantity !== 0
                                        ? 'bg-yellow-50 dark:bg-yellow-900/10'
                                        : '',
                                ]"
                            >
                                <!-- Row Number -->
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ index + 1 }}
                                </td>

                                <!-- Product Code -->
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ product.product_code }}
                                </td>

                                <!-- Product Name -->
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ product.product_name }}
                                </td>

                                <!-- Category -->
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ product.category?.name || '-' }}
                                </td>

                                <!-- Unit -->
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ product.unit?.name || '-' }}
                                </td>

                                <!-- System Quantity -->
                                <td class="px-4 py-3">
                                    <input
                                        v-model="productDetail(product.id).system_quantity"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="block w-28 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        readonly
                                        disabled
                                    />
                                </td>

                                <!-- Physical Quantity -->
                                <td class="px-4 py-3">
                                    <input
                                        v-model="productDetail(product.id).physical_quantity"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        @input="calculateDetailDifference(product.id)"
                                        class="block w-28 px-3 py-2 text-sm border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                        placeholder="0"
                                    />
                                </td>

                                <!-- Difference -->
                                <td class="px-4 py-3">
                                    <span
                                        :class="[
                                            'text-sm font-bold',
                                            (productDetail(product.id).difference_quantity || 0) >
                                            0
                                                ? 'text-green-600 dark:text-green-400'
                                                : (productDetail(product.id).difference_quantity ||
                                                      0) < 0
                                                ? 'text-red-600 dark:text-red-400'
                                                : 'text-gray-500 dark:text-gray-400',
                                        ]"
                                    >
                                        {{
                                            (productDetail(product.id).difference_quantity || 0) >
                                            0
                                                ? "+"
                                                : ""
                                        }}{{
                                            (
                                                parseFloat(
                                                    productDetail(product.id).difference_quantity ||
                                                        0
                                                ) || 0
                                            ).toFixed(2)
                                        }}
                                    </span>
                                </td>

                                <!-- Type Badge -->
                                <td class="px-4 py-3">
                                    <span
                                        v-if="productDetail(product.id).adjustment_type"
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            productDetail(product.id).adjustment_type ===
                                            'increase'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                        ]"
                                    >
                                        {{ productDetail(product.id).adjustment_type }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-gray-400 dark:text-gray-500"
                                    >
                                        -
                                    </span>
                                </td>

                                <!-- Notes -->
                                <td class="px-4 py-3">
                                    <input
                                        v-model="productDetail(product.id).notes"
                                        type="text"
                                        placeholder="Add notes..."
                                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div
                    v-if="filteredProducts.length > 0"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                    <div class="text-center">
                        <div
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ filteredProducts.length }}
                        </div>
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400 uppercase"
                        >
                            Total Products
                        </div>
                    </div>
                    <div class="text-center">
                        <div
                            :class="[
                                'text-2xl font-bold',
                                totalDifference > 0
                                    ? 'text-green-600 dark:text-green-400'
                                    : totalDifference < 0
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-gray-500 dark:text-gray-400',
                            ]"
                        >
                            {{
                                totalDifference > 0
                                    ? "+"
                                    : ""
                            }}{{ totalDifference.toFixed(2) }}
                        </div>
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400 uppercase"
                        >
                            Net Difference
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            +{{ totalIncrease.toFixed(2) }}
                        </div>
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400 uppercase"
                        >
                            Total Increases
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ totalDecrease.toFixed(2) }}
                        </div>
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400 uppercase"
                        >
                            Total Decreases
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div
                class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
            >
                <button
                    type="button"
                    @click="$emit('close')"
                    class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="saving || !isFormValid"
                    class="px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="saving">Saving...</span>
                    <span v-else>{{ isEdit ? "Update" : "Save" }} Opname</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import Modal from "../Overlays/Modal.vue";
import FormInput from "../Forms/FormInput.vue";
import FormSelect from "../Forms/FormSelect.vue";
import FormTextarea from "../Forms/FormTextarea.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    opname: {
        type: Object,
        default: null,
    },
    products: {
        type: Array,
        default: () => [],
    },
    locations: {
        type: Array,
        default: () => [],
    },
    saving: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "submit", "get-system-quantity", "batch-get-system-quantities"]);

const formData = ref({
    opname_number: "",
    opname_date: new Date().toISOString().split("T")[0],
    location_id: "",
    description: "",
    notes: "",
    details: [], // Will hold all products
});

const productDetails = ref({}); // Map product_id -> detail object

const isEdit = computed(() => !!props.opname);

const locationOptions = computed(() =>
    props.locations.map((loc) => ({
        value: loc.id,
        label: loc.name,
    }))
);

// Filter products that have values or all if location is selected
const filteredProducts = computed(() => {
    return props.products;
});

// Get detail object for a product, create if doesn't exist
const productDetail = (productId) => {
    if (!productDetails.value[productId]) {
        productDetails.value[productId] = {
            product_id: productId,
            system_quantity: 0,
            physical_quantity: 0,
            difference_quantity: 0,
            adjustment_type: null,
            notes: "",
        };
    }
    return productDetails.value[productId];
};

// Calculate totals
const totalIncrease = computed(() => {
    return Object.values(productDetails.value)
        .filter(d => d.adjustment_type === 'increase')
        .reduce((sum, d) => sum + (parseFloat(d.difference_quantity) || 0), 0);
});

const totalDecrease = computed(() => {
    return Object.values(productDetails.value)
        .filter(d => d.adjustment_type === 'decrease')
        .reduce((sum, d) => sum + Math.abs(parseFloat(d.difference_quantity) || 0), 0);
});

const totalDifference = computed(() => {
    return totalIncrease.value - totalDecrease.value;
});

const isFormValid = computed(() => {
    return (
        formData.value.location_id &&
        formData.value.opname_date &&
        filteredProducts.value.length > 0
    );
});

// Initialize all products with details
const initializeProductDetails = () => {
    props.products.forEach((product) => {
        if (!productDetails.value[product.id]) {
            productDetails.value[product.id] = {
                product_id: product.id,
                system_quantity: 0,
                physical_quantity: 0,
                difference_quantity: 0,
                adjustment_type: null,
                notes: "",
            };
        }
    });
};

// Fetch system quantity for a single product
const fetchSystemQuantity = async (productId, locationId) => {
    if (!locationId) return;

    emit("get-system-quantity", {
        productId,
        locationId,
        callback: (quantity) => {
            const detail = productDetail(productId);
            detail.system_quantity = parseFloat(quantity) || 0;
            calculateDetailDifference(productId);
        },
    });
};

// When location changes, fetch system quantities for all products using batch method
const onLocationChange = async () => {
    if (formData.value.location_id) {
        const locationId = formData.value.location_id;

        // Initialize all products first
        initializeProductDetails();

        // Get all product IDs
        const productIds = props.products.map((p) => p.id);

        // Use batch method to fetch all system quantities at once
        emit("batch-get-system-quantities", {
            productIds,
            locationId,
            callback: (quantities) => {
                // quantities should be an object mapping product_id -> quantity
                if (quantities && typeof quantities === "object") {
                    Object.entries(quantities).forEach(([productId, quantity]) => {
                        const detail = productDetail(parseInt(productId));
                        detail.system_quantity = parseFloat(quantity) || 0;
                        calculateDetailDifference(parseInt(productId));
                    });
                }
            },
        });
    }
};

// Calculate difference for a product
const calculateDetailDifference = (productId) => {
    const detail = productDetail(productId);
    if (!detail) return;

    const physicalQty = parseFloat(detail.physical_quantity || 0);
    const systemQty = parseFloat(detail.system_quantity || 0);

    detail.difference_quantity = physicalQty - systemQty;

    if (detail.difference_quantity > 0) {
        detail.adjustment_type = "increase";
    } else if (detail.difference_quantity < 0) {
        detail.adjustment_type = "decrease";
    } else {
        detail.adjustment_type = null;
    }
};

const handleSubmit = () => {
    if (!isFormValid.value) return;

    // Filter only products with physical quantities or notes
    const detailsWithValues = Object.values(productDetails.value).filter(
        (detail) => {
            const physQty = parseFloat(detail.physical_quantity || 0);
            const sysQty = parseFloat(detail.system_quantity || 0);
            return physQty > 0 || detail.notes;
        }
    );

    const submitData = {
        opname_date: formData.value.opname_date,
        location_id: formData.value.location_id,
        description: formData.value.description,
        notes: formData.value.notes,
        total_items: detailsWithValues.length,
        details: detailsWithValues.map((detail) => ({
            product_id: detail.product_id,
            system_quantity: parseFloat(detail.system_quantity),
            physical_quantity: parseFloat(detail.physical_quantity),
            notes: detail.notes || null,
        })),
    };

    emit("submit", submitData);
};

// Watch for opname changes (edit mode)
watch(
    () => props.opname,
    (newVal) => {
        if (newVal) {
            console.log("[OpnameFormModal] Edit mode - opname data:", newVal);

            formData.value = {
                opname_number: newVal.opname_number || "",
                opname_date: newVal.opname_date
                    ? new Date(newVal.opname_date).toISOString().split("T")[0]
                    : new Date().toISOString().split("T")[0],
                location_id: newVal.location_id,
                description: newVal.description || "",
                notes: newVal.notes || "",
            };

            // Reset and populate product details
            productDetails.value = {};

            if (newVal.details && newVal.details.length > 0) {
                newVal.details.forEach((detail) => {
                    const systemQty = parseFloat(detail.system_quantity || 0);
                    const physicalQty = parseFloat(detail.physical_quantity || 0);
                    const diffQty = physicalQty - systemQty;

                    productDetails.value[detail.product_id] = {
                        product_id: detail.product_id,
                        system_quantity: systemQty,
                        physical_quantity: physicalQty,
                        difference_quantity: diffQty,
                        adjustment_type:
                            detail.adjustment_type ||
                            (diffQty > 0
                                ? "increase"
                                : diffQty < 0
                                ? "decrease"
                                : null),
                        notes: detail.notes || "",
                    };
                });
            }

            console.log(
                "[OpnameFormModal] Form data populated:",
                formData.value
            );
        }
    },
    { immediate: true }
);

// Watch for products changes
watch(
    () => props.products,
    () => {
        if (formData.value.location_id) {
            onLocationChange();
        }
    },
    { immediate: true }
);

// Reset form when modal closes
watch(
    () => props.show,
    (newVal) => {
        if (!newVal && !props.opname) {
            formData.value = {
                opname_number: "",
                opname_date: new Date().toISOString().split("T")[0],
                location_id: "",
                description: "",
                notes: "",
            };
            productDetails.value = {};
        }
    }
);
</script>
