import { useNotificationStore } from "@/stores/notification";

export function useToast() {
    const notificationStore = useNotificationStore();

    const showToast = (message, type = "info", options = {}) => {
        switch (type) {
            case "success":
                return notificationStore.success("", message, options);
            case "error":
                return notificationStore.error("", message, options);
            case "warning":
                return notificationStore.warning("", message, options);
            case "info":
            default:
                return notificationStore.info("", message, options);
        }
    };

    return {
        showToast,
    };
}
