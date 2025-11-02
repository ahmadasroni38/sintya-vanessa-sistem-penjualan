<template>
    <Modal
        :is-open="show"
        :title="isEdit ? 'Edit Stock Opname' : 'New Stock Opname'"
        size="4xl"
        @close="$emit('close')"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Master Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Stock Opname Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </div>

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

            <!-- Details (Products) Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Products
                    </h3>
                    <button
                        type="button"
                        @click="addProduct"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Add Product
                    </button>
                </div>

                <!-- Products Table -->
                <div
                    class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg"
                >
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Product
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
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr v-if="formData.details.length === 0">
                                <td
                                    colspan="7"
                                    class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    No products added. Click "Add Product" to
                                    start.
                                </td>
                            </tr>
                            <tr
                                v-for="(detail, index) in formData.details"
                                :key="index"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800"
                            >
                                <!-- Product Select -->
                                <td class="px-4 py-3">
                                    <select
                                        v-model="detail.product_id"
                                        @change="onProductSelect(index)"
                                        class="block w-full rounded-md border px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 bg-white dark:border-gray-600 dark:bg-gray-700 dark:text-white cursor-default"
                                        required
                                    >
                                        <option value="">Choose Product</option>
                                        <option
                                            v-for="product in availableProducts(
                                                index
                                            )"
                                            :key="product.id"
                                            :value="product.id"
                                        >
                                            {{ product.product_code }} -
                                            {{ product.product_name }}
                                        </option>
                                    </select>
                                </td>

                                <!-- System Quantity -->
                                <td class="px-4 py-3">
                                    <input
                                        v-model="detail.system_quantity"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="block w-24 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        readonly
                                        disabled
                                    />
                                </td>

                                <!-- Physical Quantity -->
                                <td class="px-4 py-3">
                                    <input
                                        v-model="detail.physical_quantity"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        @input="
                                            calculateDetailDifference(index)
                                        "
                                        class="block w-24 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                        required
                                    />
                                </td>

                                <!-- Difference -->
                                <td class="px-4 py-3">
                                    <span
                                        :class="[
                                            'text-sm font-semibold',
                                            (detail.difference_quantity || 0) >
                                            0
                                                ? 'text-green-600 dark:text-green-400'
                                                : (detail.difference_quantity ||
                                                      0) < 0
                                                ? 'text-red-600 dark:text-red-400'
                                                : 'text-gray-900 dark:text-white',
                                        ]"
                                    >
                                        {{
                                            (detail.difference_quantity || 0) >
                                            0
                                                ? "+"
                                                : ""
                                        }}{{
                                            (
                                                parseFloat(
                                                    detail.difference_quantity ||
                                                        0
                                                ) || 0
                                            ).toFixed(2)
                                        }}
                                    </span>
                                </td>

                                <!-- Type Badge -->
                                <td class="px-4 py-3">
                                    <span
                                        v-if="detail.adjustment_type"
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            detail.adjustment_type ===
                                            'increase'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                        ]"
                                    >
                                        {{ detail.adjustment_type }}
                                    </span>
                                </td>

                                <!-- Notes -->
                                <td class="px-4 py-3">
                                    <input
                                        v-model="detail.notes"
                                        type="text"
                                        placeholder="Enter notes"
                                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                    />
                                </td>

                                <!-- Remove Button -->
                                <td class="px-4 py-3">
                                    <button
                                        type="button"
                                        @click="removeProduct(index)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        title="Remove product"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div
                    v-if="formData.details.length > 0"
                    class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                    <div class="flex justify-between items-center">
                        <span
                            class="text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Total Products:
                        </span>
                        <span
                            class="text-lg font-bold text-gray-900 dark:text-white"
                        >
                            {{ formData.details.length }}
                        </span>
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
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="saving || !isFormValid"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
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

const emit = defineEmits(["close", "submit", "get-system-quantity"]);

const formData = ref({
    opname_date: new Date().toISOString().split("T")[0],
    location_id: "",
    description: "",
    notes: "",
    details: [],
});

const isEdit = computed(() => !!props.opname);

const locationOptions = computed(() =>
    props.locations.map((loc) => ({
        value: loc.id,
        label: loc.name,
    }))
);

// Get available products (exclude already selected ones)
const availableProducts = (currentIndex) => {
    const selectedProductIds = formData.value.details
        .map((detail, idx) => (idx !== currentIndex ? detail.product_id : null))
        .filter((id) => id);

    return props.products.filter(
        (product) => !selectedProductIds.includes(product.id)
    );
};

const isFormValid = computed(() => {
    return (
        formData.value.location_id &&
        formData.value.opname_date &&
        formData.value.details.length > 0 &&
        formData.value.details.every((detail) => {
            const physQty = parseFloat(detail.physical_quantity || 0);
            const sysQty = parseFloat(detail.system_quantity || 0);
            return (
                detail.product_id &&
                !isNaN(physQty) &&
                physQty >= 0 &&
                !isNaN(sysQty) &&
                sysQty >= 0
            );
        })
    );
});

// Add new product row
const addProduct = () => {
    formData.value.details.push({
        product_id: "",
        system_quantity: 0,
        physical_quantity: 0,
        difference_quantity: 0,
        adjustment_type: null,
        notes: "",
    });
};

// Remove product row
const removeProduct = (index) => {
    formData.value.details.splice(index, 1);
};

// When product is selected, fetch system quantity
const onProductSelect = async (index) => {
    const detail = formData.value.details[index];

    if (detail.product_id && formData.value.location_id) {
        // Emit event to parent to fetch system quantity
        emit("get-system-quantity", {
            productId: detail.product_id,
            locationId: formData.value.location_id,
            callback: (quantity) => {
                detail.system_quantity = quantity;
                calculateDetailDifference(index);
            },
        });
    } else {
        detail.system_quantity = 0;
        calculateDetailDifference(index);
    }
};

// When location changes, refetch all system quantities
const onLocationChange = async () => {
    if (formData.value.location_id) {
        // Refetch system quantities for all products
        formData.value.details.forEach((detail, index) => {
            if (detail.product_id) {
                onProductSelect(index);
            }
        });
    }
};

// Calculate difference for a detail row
const calculateDetailDifference = (index) => {
    const detail = formData.value.details[index];
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

    console.log(`[OpnameFormModal] Calculated difference for index ${index}:`, {
        physicalQty,
        systemQty,
        difference: detail.difference_quantity,
        type: detail.adjustment_type,
    });
};

const handleSubmit = () => {
    if (!isFormValid.value) return;

    const submitData = {
        opname_date: formData.value.opname_date,
        location_id: formData.value.location_id,
        description: formData.value.description,
        notes: formData.value.notes,
        total_items: formData.value.details.length,
        details: formData.value.details.map((detail) => ({
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
                opname_date: newVal.opname_date
                    ? new Date(newVal.opname_date).toISOString().split("T")[0]
                    : new Date().toISOString().split("T")[0],
                location_id: newVal.location_id,
                description: newVal.description || "",
                notes: newVal.notes || "",
                details:
                    newVal.details && newVal.details.length > 0
                        ? newVal.details.map((detail) => {
                              const systemQty = parseFloat(
                                  detail.system_quantity || 0
                              );
                              const physicalQty = parseFloat(
                                  detail.physical_quantity || 0
                              );
                              const diffQty = physicalQty - systemQty;

                              console.log(
                                  `[OpnameFormModal] Processing detail for product ${detail.product_id}:`,
                                  {
                                      systemQty,
                                      physicalQty,
                                      diffQty,
                                      adjustment_type: detail.adjustment_type,
                                  }
                              );

                              return {
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
                          })
                        : [],
            };

            console.log(
                "[OpnameFormModal] Form data populated:",
                formData.value
            );
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
                opname_date: new Date().toISOString().split("T")[0],
                location_id: "",
                description: "",
                notes: "",
                details: [],
            };
        }
    }
);
</script>
