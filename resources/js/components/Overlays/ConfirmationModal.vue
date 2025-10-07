<template>
    <Modal :is-open="isOpen" @close="handleCancel" size="sm">
        <template #header>
            {{ title }}
        </template>

        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center"
                    >
                        <svg
                            class="h-5 w-5 text-red-600 dark:text-red-400"
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
                <div class="ml-4">
                    <h3
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ message }}
                    </h3>
                    <div v-if="description" class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
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

const handleConfirm = async () => {
    try {
        emit("confirm");
    } catch (error) {
        console.error("Confirmation error:", error);
        notification.error("An error occurred during the operation");
    }
};

const handleCancel = () => {
    emit("cancel");
    emit("close");
};

// Note: We don't need to watch isOpen here as the parent component manages the state
</script>
