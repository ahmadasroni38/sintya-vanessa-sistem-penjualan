<template>
    <div class="relative">
        <textarea
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :rows="rows"
            :class="textareaClasses"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="$emit('blur', $event)"
            @focus="$emit('focus', $event)"
        ></textarea>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    rows: {
        type: Number,
        default: 3,
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg"].includes(value),
    },
    variant: {
        type: String,
        default: "default",
        validator: (value) => ["default", "error"].includes(value),
    },
});

const emit = defineEmits(["update:modelValue", "blur", "focus"]);

const textareaClasses = computed(() => {
    const baseClasses =
        "block w-full rounded-md border-gray-200 resize-none focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white";

    const sizeClasses = {
        sm: "px-2.5 py-1.5 text-xs",
        md: "px-3 py-2 text-sm",
        lg: "px-4 py-3 text-base",
    };

    const variantClasses = {
        default:
            "border-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:focus:border-blue-500",
        error: "border-red-500 focus:border-red-500 focus:ring-red-500 dark:border-red-500",
    };

    return `${baseClasses} ${sizeClasses[props.size]} ${
        variantClasses[props.variant]
    }`;
});
</script>
