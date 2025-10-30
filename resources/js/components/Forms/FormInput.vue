<template>
    <div class="space-y-2">
        <label
            v-if="label"
            :for="inputId"
            class="block text-sm font-medium text-gray-700 dark:text-gray-200"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <BaseInput
                :id="inputId"
                v-model="inputValue"
                :type="type"
                :placeholder="placeholder"
                :disabled="disabled"
                :size="size"
                :variant="error ? 'error' : 'default'"
                @blur="handleBlur"
                @focus="handleFocus"
            />
            <div
                v-if="$slots.icon"
                class="absolute inset-y-0 right-0 flex items-center pr-3"
            >
                <slot name="icon" />
            </div>
        </div>
        <p v-if="error" class="text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
        <p
            v-if="hint && !error"
            class="text-sm text-gray-500 dark:text-gray-400"
        >
            {{ hint }}
        </p>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import BaseInput from "../Base/Input.vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "text",
    },
    placeholder: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "md",
    },
    error: {
        type: String,
        default: "",
    },
    hint: {
        type: String,
        default: "",
    },
    id: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "blur", "focus"]);

const inputValue = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const inputId = computed(
    () => props.id || `input-${Math.random().toString(36).substr(2, 9)}`
);

const handleBlur = (event) => {
    emit("blur", event);
};

const handleFocus = (event) => {
    emit("focus", event);
};
</script>
