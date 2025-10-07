<template>
    <div class="relative">
        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="inputClasses"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="$emit('blur', $event)"
            @focus="$emit('focus', $event)"
        />
        <slot name="icon" />
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
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

const inputClasses = computed(() => {
    const baseClasses =
        "block w-full rounded-md border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500 disabled:pointer-events-none disabled:opacity-50";

    const sizeClasses = {
        sm: "px-2.5 py-1.5 text-xs",
        md: "px-3 py-2 text-sm",
        lg: "px-4 py-3 text-base",
    };

    const variantClasses = {
        default: "border-gray-200 focus:border-red-500 focus:ring-red-500",
        error: "border-red-500 focus:border-red-500 focus:ring-red-500",
    };

    return `${baseClasses} ${sizeClasses[props.size]} ${
        variantClasses[props.variant]
    }`;
});
</script>
