import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useSettingStore = defineStore("setting", () => {
    const settings = ref({});
    const loading = ref(false);

    const fetchSettings = async () => {
        loading.value = true;
        try {
            const response = await fetch("/api/settings", {
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem("token")}`,
                    "Accept": "application/json",
                },
            });

            const data = await response.json();

            if (data.success) {
                settings.value = data.data;
            }
        } catch (error) {
            console.error("Failed to fetch settings:", error);
        } finally {
            loading.value = false;
        }
    };

    // Getters for report settings
    const reportCheckerName = computed(() => settings.value.report_checker_name || "");
    const reportApproverName = computed(() => settings.value.report_approver_name || "");

    return {
        settings,
        loading,
        fetchSettings,
        reportCheckerName,
        reportApproverName,
    };
});
