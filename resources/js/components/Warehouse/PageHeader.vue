<template>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ title }}
            </h1>
            <p v-if="description" class="text-gray-500 dark:text-gray-400 mt-1">
                {{ description }}
            </p>
        </div>
        <div v-if="$slots.actions" class="flex items-center gap-3">
            <slot name="actions"></slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>
