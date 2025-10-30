<template>
    <div
        class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
    >
        <button
            type="button"
            @click="$emit('cancel')"
            :disabled="disabled"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
        >
            Cancel
        </button>
        <button
            type="submit"
            :disabled="disabled || loading"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span v-if="loading">{{ loadingText }}</span>
            <span v-else>{{ submitText }}</span>
        </button>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    editingProduct: {
        type: Object,
        default: null,
    },
    loadingText: {
        type: String,
        default: "Saving...",
    },
    submitText: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["cancel"]);

const computedSubmitText = computed(() => {
    if (props.submitText) return props.submitText;
    return props.editingProduct ? "Update" : "Save";
});
</script>
