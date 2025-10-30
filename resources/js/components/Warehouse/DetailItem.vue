<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
        </label>
        <slot name="value">
            <p :class="[
                'mt-1 text-sm text-gray-900 dark:text-white',
                bold ? 'font-semibold' : ''
            ]">
                {{ value }}
            </p>
        </slot>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        default: '-',
    },
    bold: {
        type: Boolean,
        default: false,
    },
});
</script>
