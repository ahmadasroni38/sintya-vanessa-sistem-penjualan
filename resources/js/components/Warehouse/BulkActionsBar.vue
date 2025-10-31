<template>
    <div
        v-if="selectedCount > 0"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50"
    >
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center gap-6"
        >
            <div class="flex items-center gap-2">
                <span
                    class="text-sm font-medium text-gray-900 dark:text-white"
                >
                    {{ selectedCount }} item{{ selectedCount > 1 ? 's' : '' }} selected
                </span>
                <button
                    @click="$emit('clear-selection')"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                >
                    Clear
                </button>
            </div>

            <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

            <div class="flex items-center gap-2">
                <button
                    v-if="canApprove"
                    @click="$emit('bulk-approve')"
                    :disabled="processing"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <CheckCircleIcon class="w-4 h-4" />
                    Approve Selected
                </button>

                <button
                    v-if="canDelete"
                    @click="$emit('bulk-delete')"
                    :disabled="processing"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <TrashIcon class="w-4 h-4" />
                    Delete Selected
                </button>

                <button
                    @click="$emit('bulk-export')"
                    :disabled="processing"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Export Selected
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import {
    CheckCircleIcon,
    TrashIcon,
    ArrowDownTrayIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    selectedItems: {
        type: Array,
        default: () => [],
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits([
    'clear-selection',
    'bulk-approve',
    'bulk-delete',
    'bulk-export',
]);

const selectedCount = computed(() => props.selectedItems.length);

const canApprove = computed(() => {
    return props.selectedItems.every((item) => item.status === 'draft');
});

const canDelete = computed(() => {
    return props.selectedItems.every((item) => item.status === 'draft');
});
</script>
