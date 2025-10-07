import { defineStore } from "pinia";
import { useNotificationStore } from "./notification";

// Default configuration constants for better maintainability
const DEFAULT_CONFIG = {
    title: "Confirm Action",
    message: "Are you sure you want to proceed?",
    description: "",
    confirmText: "Confirm",
    cancelText: "Cancel",
};

export const useConfirmationModalStore = defineStore("confirmationModal", {
    state: () => ({
        isOpen: false,
        loading: false,
        config: { ...DEFAULT_CONFIG },
        onConfirmCallback: null,
        onCancelCallback: null,
        onSuccessCallback: null,
        onErrorCallback: null,
    }),

    actions: {
        /**
         * Show the confirmation modal with given options
         * @param {Object} options - Configuration options
         * @param {string} options.title - Modal title
         * @param {string} options.message - Main confirmation message
         * @param {string} options.description - Additional description
         * @param {string} options.confirmText - Text for confirm button
         * @param {string} options.cancelText - Text for cancel button
         * @param {Function} options.onConfirm - Callback when user confirms
         * @param {Function} options.onCancel - Callback when user cancels
         * @param {Function} options.onSuccess - Callback when action succeeds
         * @param {Function} options.onError - Callback when action fails
         */
        showModal(options = {}) {
            // Input validation
            if (typeof options !== "object" || options === null) {
                console.error("showModal: options must be an object");
                return;
            }

            // Validate callback functions
            const validateCallback = (callback, name) => {
                if (callback !== undefined && typeof callback !== "function") {
                    console.warn(
                        `showModal: ${name} must be a function or undefined`
                    );
                    return null;
                }
                return callback || null;
            };

            // Update config with defaults
            this.config = {
                ...DEFAULT_CONFIG,
                ...options,
            };

            // Set callbacks
            this.onConfirmCallback = validateCallback(
                options.onConfirm,
                "onConfirm"
            );
            this.onCancelCallback = validateCallback(
                options.onCancel,
                "onCancel"
            );
            this.onSuccessCallback = validateCallback(
                options.onSuccess,
                "onSuccess"
            );
            this.onErrorCallback = validateCallback(options.onError, "onError");

            // Reset loading state
            this.loading = false;

            // Show modal
            this.isOpen = true;
        },

        /**
         * Hide the confirmation modal
         */
        hideModal() {
            this.isOpen = false;
            this.loading = false;

            // Clear callbacks to prevent memory leaks
            this.onConfirmCallback = null;
            this.onCancelCallback = null;
            this.onSuccessCallback = null;
            this.onErrorCallback = null;
        },

        /**
         * Handle confirm action
         */
        async handleConfirm() {
            if (!this.onConfirmCallback) {
                this.hideModal();
                return;
            }

            this.loading = true;

            try {
                const result = await this.onConfirmCallback();

                // Call success callback if provided
                if (this.onSuccessCallback) {
                    this.onSuccessCallback(result);
                }

                this.hideModal();
            } catch (error) {
                // Call error callback if provided, otherwise show default error
                if (this.onErrorCallback) {
                    this.onErrorCallback(error);
                } else {
                    const notification = useNotificationStore();
                    notification.error(
                        error.response?.data?.message ||
                            error.message ||
                            "An error occurred during the operation"
                    );
                }

                this.loading = false;
            }
        },

        /**
         * Handle cancel action
         */
        handleCancel() {
            if (this.onCancelCallback) {
                this.onCancelCallback();
            }
            this.hideModal();
        },

        /**
         * Handle modal close
         */
        handleClose() {
            this.hideModal();
        },
    },
});
