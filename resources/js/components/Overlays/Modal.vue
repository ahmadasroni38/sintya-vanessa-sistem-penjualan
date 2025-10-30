<template>
    <Teleport to="body">
        <div
            v-if="isOpen"
            class="fixed inset-0 z-[9999] overflow-y-auto"
            @keydown.esc="close"
            @keydown.tab="handleTabKey"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="title ? 'modal-title' : undefined"
            :aria-describedby="title ? undefined : 'modal-description'"
        >
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                @click="closeOnBackdrop ? close() : null"
            ></div>

            <!-- Modal -->
            <div
                class="flex min-h-full items-center justify-center p-4 text-center"
            >
                <div
                    class="relative w-full transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all"
                    :class="sizeClasses"
                >
                    <!-- Header -->
                    <div
                        v-if="title || $slots.header"
                        class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-4 py-3"
                    >
                        <h3
                            id="modal-title"
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            <slot name="header">{{ title }}</slot>
                        </h3>
                        <button
                            type="button"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-transparent bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            @click="close"
                        >
                            <span class="sr-only">Close</span>
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-4 py-5 relative" style="overflow: visible">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div
                        v-if="$slots.footer"
                        class="flex items-center justify-end gap-x-2 border-t border-gray-200 dark:border-gray-700 px-4 py-3"
                    >
                        <slot name="footer" :close="close" />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) =>
            ["sm", "md", "lg", "xl", "2xl", "4xl", "6xl"].includes(value),
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["close"]);

const sizeClasses = computed(() => {
    const sizes = {
        sm: "max-w-sm w-full mx-4",
        md: "max-w-md w-full mx-4",
        lg: "max-w-lg w-full mx-4",
        xl: "max-w-xl w-full mx-4",
        "2xl": "max-w-2xl w-full mx-4",
        "4xl": "max-w-4xl w-full mx-4",
        "6xl": "max-w-6xl w-full mx-4",
    };
    return sizes[props.size];
});

const close = () => {
    emit("close");
};

const handleTabKey = (event) => {
    // Focus trap: keep focus within modal
    const modal = event.currentTarget;
    const focusableElements = modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    if (event.shiftKey) {
        if (document.activeElement === firstElement) {
            lastElement.focus();
            event.preventDefault();
        }
    } else {
        if (document.activeElement === lastElement) {
            firstElement.focus();
            event.preventDefault();
        }
    }
};
</script>
