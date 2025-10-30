<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput
                v-model="form.product_code"
                label="Product Code"
                placeholder="e.g., PRD00001"
                required
                :disabled="disabled || !!editingProduct"
                :error="getFieldError('product_code')"
                @blur="validateField('product_code')"
            >
                <template #icon>
                    <button
                        v-if="!editingProduct && !disabled"
                        type="button"
                        @click="$emit('generate-code')"
                        :disabled="generatingCode"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-blue-600 disabled:opacity-50"
                        title="Generate product code"
                    >
                        <svg
                            v-if="!generatingCode"
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-4 h-4 animate-spin"
                            fill="none"
                            stroke="currentColor"
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
                    </button>
                </template>
            </FormInput>
            <FormInput
                v-model="form.product_name"
                label="Product Name"
                placeholder="Enter product name"
                required
                :disabled="disabled"
                :error="getFieldError('product_name')"
                @blur="validateField('product_name')"
            />
        </div>

        <FormTextarea
            v-model="form.description"
            label="Description"
            placeholder="Enter product description"
            rows="3"
            :disabled="disabled"
            :error="getFieldError('description')"
            @blur="validateField('description')"
        />
    </div>
</template>

<script setup>
import FormInput from "../Forms/FormInput.vue";
import FormTextarea from "../Forms/FormTextarea.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    editingProduct: {
        type: Object,
        default: null,
    },
    generatingCode: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["generate-code"]);

const getFieldError = (field) => {
    return props.errors[field] ? props.errors[field][0] : "";
};

const validateField = (field) => {
    emit("validate-field", field);
};
</script>
