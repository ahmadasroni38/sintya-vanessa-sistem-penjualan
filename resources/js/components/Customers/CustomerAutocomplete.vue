<template>
    <div class="relative">
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative">
            <input
                v-model="searchQuery"
                type="text"
                :placeholder="placeholder"
                :disabled="disabled"
                @input="handleSearch"
                @focus="showDropdown = true"
                @blur="handleBlur"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                :class="{ 'border-red-500': error }"
            />

            <div
                v-if="loading"
                class="absolute right-3 top-1/2 transform -translate-y-1/2"
            >
                <svg
                    class="animate-spin h-5 w-5 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
            </div>

            <!-- Selected customer display -->
            <div
                v-if="selectedCustomer && !showDropdown"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center gap-2"
            >
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ selectedCustomer.customer_code }}
                </span>
                <button
                    type="button"
                    @click="clearSelection"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                >
                    <XMarkIcon class="w-4 h-4" />
                </button>
            </div>

            <!-- Dropdown -->
            <div
                v-if="
                    showDropdown &&
                    (filteredCustomers.length > 0 || allowCreate)
                "
                class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-auto"
            >
                <!-- Create new customer option -->
                <div
                    v-if="allowCreate && searchQuery"
                    @mousedown.prevent="handleCreateNew"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 border-b border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400 font-medium"
                >
                    <PlusIcon class="w-4 h-4 inline mr-2" />
                    Create "{{ searchQuery }}"
                </div>

                <!-- Loading skeleton -->
                <div v-if="loading" class="px-4 py-3 space-y-3">
                    <div v-for="n in 3" :key="n" class="animate-pulse">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div
                                    class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-1"
                                ></div>
                                <div
                                    class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"
                                ></div>
                            </div>
                            <div
                                class="w-5 h-5 bg-gray-200 dark:bg-gray-700 rounded"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Customer list -->
                <div
                    v-else-if="filteredCustomers.length > 0"
                    v-for="customer in filteredCustomers"
                    :key="customer.value"
                    @mousedown.prevent="selectCustomer(customer)"
                    class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <div
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ customer.customer_name }}
                            </div>
                            <div
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ customer.customer_code }}
                                <span v-if="customer.phone">
                                    • {{ customer.phone }}</span
                                >
                            </div>
                        </div>
                        <CheckIcon
                            v-if="modelValue === customer.value"
                            class="w-5 h-5 text-blue-600"
                        />
                    </div>
                </div>

                <!-- No results -->
                <div
                    v-else-if="
                        !loading &&
                        filteredCustomers.length === 0 &&
                        !allowCreate
                    "
                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center"
                >
                    No customers found
                </div>
            </div>
        </div>

        <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from "vue";
import { useCustomers } from "@/composables/useCustomers";
import { XMarkIcon, CheckIcon, PlusIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null,
    },
    label: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "Search customers...",
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: "",
    },
    allowCreate: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "create-new"]);

const { fetchActiveCustomers } = useCustomers();

const searchQuery = ref("");
const showDropdown = ref(false);
const loading = ref(false);
const customers = ref([]);
const selectedCustomer = ref(null);

// Filtered customers based on search
const filteredCustomers = computed(() => {
    if (!searchQuery.value) {
        return customers.value;
    }
    const query = searchQuery.value.toLowerCase();
    return customers.value.filter(
        (customer) =>
            customer.customer_name.toLowerCase().includes(query) ||
            customer.customer_code.toLowerCase().includes(query) ||
            (customer.phone && customer.phone.includes(query)) ||
            (customer.email && customer.email.toLowerCase().includes(query))
    );
});

// Load customers
const loadCustomers = async (search = "") => {
    loading.value = true;
    try {
        const data = await fetchActiveCustomers(search);
        customers.value = data;
    } catch (error) {
        console.error("Error loading customers:", error);
    } finally {
        loading.value = false;
    }
};

// Handle search input
let searchTimeout = null;
const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadCustomers(searchQuery.value);
    }, 300);
};

// Select customer
const selectCustomer = (customer) => {
    selectedCustomer.value = customer;
    searchQuery.value = customer.customer_name;
    showDropdown.value = false;
    emit("update:modelValue", customer.value);
};

// Clear selection
const clearSelection = () => {
    selectedCustomer.value = null;
    searchQuery.value = "";
    emit("update:modelValue", null);
    showDropdown.value = true;
};

// Handle blur
const handleBlur = () => {
    setTimeout(() => {
        showDropdown.value = false;
        // Restore selected customer name if exists
        if (selectedCustomer.value) {
            searchQuery.value = selectedCustomer.value.customer_name;
        } else {
            searchQuery.value = "";
        }
    }, 200);
};

// Handle create new
const handleCreateNew = () => {
    emit("create-new", searchQuery.value);
    showDropdown.value = false;
};

// Watch for model value changes
watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue) {
            const customer = customers.value.find((c) => c.value === newValue);
            if (customer) {
                selectedCustomer.value = customer;
                searchQuery.value = customer.customer_name;
            }
        } else {
            selectedCustomer.value = null;
            searchQuery.value = "";
        }
    }
);

// Load customers on mount
onMounted(() => {
    loadCustomers();
});
</script>
