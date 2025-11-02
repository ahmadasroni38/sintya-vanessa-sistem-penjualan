<template>
    <Modal
        :is-open="isOpen"
        @close="handleCancel"
        size="sm"
        :close-on-backdrop="!loading"
    >
        <template #header>
            {{ title }}
        </template>

        <div class="space-y-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center ring-4 ring-red-100 dark:ring-red-900/30 animate-pulse"
                    >
                        <svg
                            class="h-6 w-6 text-red-600 dark:text-red-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                            />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-2"
                    >
                        {{ message }}
                    </h3>
                    <div
                        v-if="description"
                        class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 border-l-4 border-red-500"
                    >
                        <p
                            class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed"
                        >
                            {{ description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <div
                class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0"
            >
                <Button
                    @click="handleCancel"
                    variant="secondary"
                    :disabled="loading"
                    class="w-full sm:w-auto"
                >
                    {{ cancelText }}
                </Button>
                <Button
                    @click="handleConfirm"
                    variant="danger"
                    :loading="loading"
                    :disabled="loading"
                    class="w-full sm:w-auto"
                >
                    {{ confirmText }}
                </Button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref } from "vue";
import Modal from "./Modal.vue";
import Button from "../Base/Button.vue";
import { useNotificationStore } from "@/stores/notification";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Confirm Action",
    },
    message: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: "",
    },
    confirmText: {
        type: String,
        default: "Delete",
    },
    cancelText: {
        type: String,
        default: "Cancel",
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["confirm", "cancel", "close"]);

const notification = useNotificationStore();

const handleConfirm = () => {
    emit("confirm");
};

const handleCancel = () => {
    if (props.loading) return; // Prevent closing while loading
    emit("cancel");
};

// Note: We don't need to watch isOpen here as the parent component manages the state
</script>
