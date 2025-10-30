<template>
    <button
        :type="type"
        :class="buttonClasses"
        :disabled="disabled || loading"
        @click="$emit('click', $event)"
    >
        <svg
            v-if="loading"
            class="animate-spin -ml-1 mr-2 h-4 w-4"
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
        <slot />
    </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    variant: {
        type: String,
        default: "primary",
        validator: (value) =>
            [
                "primary",
                "secondary",
                "success",
                "danger",
                "warning",
                "info",
                "light",
                "dark",
            ].includes(value),
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["xs", "sm", "md", "lg", "xl"].includes(value),
    },
    outline: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: "button",
    },
});

const buttonClasses = computed(() => {
    const baseClasses =
        "inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none";

    const variantClasses = {
        primary: props.outline
            ? "text-blue-600 border-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-50"
            : "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700",
        secondary: props.outline
            ? "text-gray-600 border-gray-600 hover:bg-gray-600 hover:text-white focus:ring-gray-500 dark:text-gray-400 dark:border-gray-400 dark:hover:text-gray-50"
            : "bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500 dark:bg-gray-600 dark:hover:bg-gray-700",
        success: props.outline
            ? "text-green-600 border-green-600 hover:bg-green-600 hover:text-white focus:ring-green-500 dark:text-green-400 dark:border-green-400 dark:hover:text-green-50"
            : "bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700",
        danger: props.outline
            ? "text-red-600 border-red-600 hover:bg-red-600 hover:text-white focus:ring-red-500 dark:text-red-400 dark:border-red-400 dark:hover:text-red-50"
            : "bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-700",
        warning: props.outline
            ? "text-yellow-600 border-yellow-600 hover:bg-yellow-600 hover:text-white focus:ring-yellow-500 dark:text-yellow-400 dark:border-yellow-400 dark:hover:text-yellow-50"
            : "bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700",
        info: props.outline
            ? "text-blue-600 border-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-50"
            : "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700",
        light: props.outline
            ? "text-gray-800 border-gray-300 hover:bg-gray-100 focus:ring-gray-500 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700"
            : "bg-gray-100 text-gray-800 hover:bg-gray-200 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600",
        dark: props.outline
            ? "text-gray-900 border-gray-900 hover:bg-gray-900 hover:text-white focus:ring-gray-500 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-300"
            : "bg-gray-900 text-white hover:bg-gray-800 focus:ring-gray-500 dark:bg-gray-800 dark:hover:bg-gray-700",
    };

    const sizeClasses = {
        xs: "px-2.5 py-1.5 text-xs",
        sm: "px-3 py-2 text-sm",
        md: "px-4 py-3 text-sm",
        lg: "px-4 py-3 text-base",
        xl: "px-6 py-3.5 text-base",
    };

    return `${baseClasses} ${variantClasses[props.variant]} ${
        sizeClasses[props.size]
    }`;
});
</script>
