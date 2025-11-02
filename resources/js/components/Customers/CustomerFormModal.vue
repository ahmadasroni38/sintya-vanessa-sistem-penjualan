<template>
    <Modal
        :is-open="isOpen"
        :title="editingItem ? 'Edit Customer' : 'New Customer'"
        size="2xl"
        @close="handleClose"
        :close-on-backdrop="!saving"
    >
        <!-- Loading overlay for form initialization -->
        <div
            v-if="initializing"
            class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm z-10 flex items-center justify-center rounded-lg"
        >
            <div class="flex flex-col items-center gap-3">
                <div
                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                ></div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Loading customer data...
                </p>
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6 relative">
            <!-- Customer Code and Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                    v-model="formData.customer_code"
                    label="Customer Code"
                    placeholder="Auto-generated if empty"
                    :error="errors?.customer_code"
                    :disabled="initializing"
                />
                <FormInput
                    v-model="formData.customer_name"
                    label="Customer Name"
                    placeholder="Enter customer name"
                    required
                    :error="errors?.customer_name"
                    :disabled="initializing"
                />
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormInput
                    v-model="formData.phone"
                    label="Phone"
                    type="tel"
                    placeholder="Enter phone number"
                    :error="errors?.phone"
                    :disabled="initializing"
                />
                <FormInput
                    v-model="formData.email"
                    label="Email"
                    type="email"
                    placeholder="Enter email address"
                    :error="errors?.email"
                    :disabled="initializing"
                />
            </div>

            <!-- Address -->
            <FormTextarea
                v-model="formData.address"
                label="Address"
                placeholder="Enter full address"
                rows="3"
                :error="errors?.address"
                :disabled="initializing"
            />

            <!-- Status -->
            <FormSelect
                v-model="formData.status"
                label="Status"
                :options="statusOptions"
                required
                :error="errors?.status"
                :disabled="initializing"
            />

            <!-- Notes -->
            <FormTextarea
                v-model="formData.notes"
                label="Notes"
                placeholder="Additional notes (optional)"
                rows="3"
                :error="errors?.notes"
                :disabled="initializing"
            />

            <!-- Actions -->
            <div
                class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
            >
                <button
                    type="button"
                    @click="handleClose"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="saving"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="saving" class="flex items-center gap-2">
                        <svg
                            class="animate-spin h-4 w-4 text-white"
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
                        Saving...
                    </span>
                    <span v-else>{{ editingItem ? "Update" : "Save" }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, watch } from "vue";
import Modal from "@/components/Overlays/Modal.vue";
import FormInput from "@/components/Forms/FormInput.vue";
import FormSelect from "@/components/Forms/FormSelect.vue";
import FormTextarea from "@/components/Forms/FormTextarea.vue";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    editingItem: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["close", "saved"]);

// Form state
const saving = ref(false);
const initializing = ref(false);
const errors = ref({});
const formData = ref({
    customer_code: "",
    customer_name: "",
    address: "",
    phone: "",
    email: "",
    notes: "",
    status: "active",
});

// Status options
const statusOptions = [
    { value: "active", label: "Active" },
    { value: "inactive", label: "Inactive" },
];

// Reset form
const resetForm = () => {
    formData.value = {
        customer_code: "",
        customer_name: "",
        address: "",
        phone: "",
        email: "",
        notes: "",
        status: "active",
    };
    errors.value = {};
};

// Watch for editing item changes
watch(
    () => props.editingItem,
    (newValue) => {
        if (newValue) {
            initializing.value = true;
            // Simulate brief loading for better UX
            setTimeout(() => {
                formData.value = {
                    customer_code: newValue.customer_code || "",
                    customer_name: newValue.customer_name || "",
                    address: newValue.address || "",
                    phone: newValue.phone || "",
                    email: newValue.email || "",
                    notes: newValue.notes || "",
                    status: newValue.status || "active",
                };
                initializing.value = false;
            }, 300);
        } else {
            resetForm();
            initializing.value = false;
        }
    },
    { immediate: true }
);

// Handle form submission
const handleSubmit = async () => {
    errors.value = {};

    // Client-side validation
    if (!formData.value.customer_name?.trim()) {
        errors.value.customer_name = "Customer name is required";
        return;
    }

    if (formData.value.email && !isValidEmail(formData.value.email)) {
        errors.value.email = "Please enter a valid email address";
        return;
    }

    saving.value = true;
    try {
        emit("saved", formData.value);
    } finally {
        saving.value = false;
    }
};

// Handle close
const handleClose = () => {
    if (!saving.value) {
        resetForm();
        emit("close");
    }
};

// Email validation
const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};
</script>
