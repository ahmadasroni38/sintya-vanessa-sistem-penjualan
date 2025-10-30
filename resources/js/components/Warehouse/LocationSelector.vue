<template>
    <div class="space-y-4">
        <FormSelect
            v-model="selectedLocationId"
            :label="label"
            :options="locationOptions"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            @change="onLocationChange"
        />

        <!-- Location Info Display -->
        <div
            v-if="selectedLocation"
            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Location Code
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedLocation.code }}
                    </p>
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Type
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedLocation.type }}
                    </p>
                </div>
                <div v-if="showAddress && selectedLocation.address">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Address
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedLocation.address }}
                    </p>
                </div>
                <div v-if="showCapacity">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Capacity
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ selectedLocation.capacity.toLocaleString() }} units
                    </p>
                </div>
                <div v-if="showStatus">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Status
                    </label>
                    <span
                        :class="[
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1',
                            getStatusBadgeClass(selectedLocation.status),
                        ]"
                    >
                        {{ selectedLocation.status }}
                    </span>
                </div>
                <div v-if="showStock">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Current Stock
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ totalStock }} items
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
        default: "Location",
    },
    placeholder: {
        type: String,
        default: "Select a location",
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    showAddress: {
        type: Boolean,
        default: false,
    },
    showCapacity: {
        type: Boolean,
        default: false,
    },
    showStatus: {
        type: Boolean,
        default: false,
    },
    showStock: {
        type: Boolean,
        default: false,
    },
    filterType: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

// State
const locations = ref([]);
const stockLevels = ref({});

// Computed
const selectedLocationId = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const selectedLocation = computed(() => {
    return locations.value.find((l) => l.id === selectedLocationId.value);
});

const locationOptions = computed(() => {
    let filteredLocations = locations.value;

    if (props.filterType) {
        filteredLocations = filteredLocations.filter(
            (l) => l.type === props.filterType
        );
    }

    return filteredLocations.map((location) => ({
        value: location.id,
        label: `${location.code} - ${location.name}`,
    }));
});

const totalStock = computed(() => {
    if (!props.showStock || !selectedLocationId.value) return 0;

    const locationStock = stockLevels.value[selectedLocationId.value];
    if (!locationStock) return 0;

    return Object.values(locationStock).reduce(
        (sum, quantity) => sum + quantity,
        0
    );
});

// Methods
const loadLocations = async () => {
    try {
        locations.value = dummyDataService.generateLocations();

        // Generate simulated stock levels if needed
        if (props.showStock) {
            const products = dummyDataService.generateProducts();
            locations.value.forEach((location) => {
                stockLevels.value[location.id] = {};
                products.forEach((product) => {
                    stockLevels.value[location.id][product.id] =
                        Math.floor(Math.random() * 1000) + 100;
                });
            });
        }
    } catch (error) {
        console.error("Failed to load locations:", error);
    }
};

const onLocationChange = () => {
    emit("change", selectedLocation.value);
};

const getStatusBadgeClass = (status) => {
    const classes = {
        active: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        inactive:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return (
        classes[status] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

onMounted(() => {
    loadLocations();
});
</script>
