import { defineStore } from "pinia";
import { ref } from "vue";

export const useNotificationStore = defineStore("notification", () => {
    const notifications = ref([]);

    const addNotification = (
        type,
        title,
        message = "",
        duration = 0,
        closable = true
    ) => {
        const id = Date.now() + Math.random();
        const notification = {
            id,
            type,
            title,
            message,
            duration,
            closable,
        };

        notifications.value.push(notification);

        // Auto remove after duration if specified
        if (duration > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, duration);
        }

        return id;
    };

    const removeNotification = (id) => {
        const index = notifications.value.findIndex((n) => n.id === id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    };

    const success = (title, message = "", options = {}) => {
        return addNotification(
            "success",
            title || "Success",
            message,
            options.duration || 3000,
            options.closable !== false
        );
    };

    const error = (title, message = "", options = {}) => {
        return addNotification(
            "error",
            title || "Error",
            message,
            options.duration || 5000,
            options.closable !== false
        );
    };

    const warning = (title, message = "", options = {}) => {
        return addNotification(
            "warning",
            title || "Warning",
            message,
            options.duration || 4000,
            options.closable !== false
        );
    };

    const info = (title, message = "", options = {}) => {
        return addNotification(
            "info",
            title || "Info",
            message,
            options.duration || 3000,
            options.closable !== false
        );
    };

    const clearAll = () => {
        notifications.value = [];
    };

    return {
        notifications,
        addNotification,
        removeNotification,
        success,
        error,
        warning,
        info,
        clearAll,
    };
});
