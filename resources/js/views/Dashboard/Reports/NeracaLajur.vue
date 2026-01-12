<template>
    <ReportLayout
        title="Neraca Lajur"
        description="Worksheet untuk menyiapkan Neraca dan Laporan Laba Rugi"
        ref="reportLayout"
        @generate="handleGenerate"
        @export="handleExport"
    >
        <template #default="{ data, period }">
            <NeracaLajurContent
                :data="data"
                :period="period"
                :report-settings="reportSettings"
                :print-date="printDate"
                :print-user="printUser"
            />
        </template>
    </ReportLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import ReportLayout from "../../../components/Reports/ReportLayout.vue";
import NeracaLajurContent from "./NeracaLajurContent.vue";
import reportService from "../../../services/reportService";
import { useNotificationStore } from "@/stores/notification";
import { useAuthStore } from "@/stores/auth";
import { apiGet } from "@/utils/api";

const notification = useNotificationStore();
const reportLayout = ref(null);
const reportData = ref([]);

const printDate = computed(() => {
  const now = new Date();
  return `Makassar, ${now.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric"
  })}`;
});

const authStore = useAuthStore();

const printUser = computed(() => authStore.user?.name || authStore.user?.full_name || 'Nama Pengguna');

const reportSettings = ref({
  checker_name: 'Nama Pemeriksa',
  approver_name: 'Nama Penyetuju'
});

// Methods
const handleGenerateSedangDiclick = ref(false);
const handleGenerate = async (period) => {
    if(handleGenerateSedangDiclick.value) {
        return;
    }
    handleGenerateSedangDiclick.value = true
    try {
        const response = await reportService.getNeracaLajur(
            period.start_date,
            period.end_date
        );
        reportData.value = response.data || [];
        reportLayout.value?.setReportData(response.data);

        // Fetch report signature settings
        try {
          const settingsResponse = await apiGet('/settings');

          if (settingsResponse.success) {

            const settings = settingsResponse.data;
            reportSettings.value.checker_name = settings.report_checker_name || reportSettings.value.checker_name;
            reportSettings.value.approver_name = settings.report_approver_name || reportSettings.value.approver_name;
          }
        } catch (settingsErr) {
          console.warn('Failed to fetch settings:', settingsErr);
        }
    } catch (error) {
        notification.error("Failed to generate Neraca Lajur report");
        throw error;
    } finally {
        handleGenerateSedangDiclick.value = false
    }
};

const handleExport = async (params) => {
    try {
        await reportService.exportNeracaLajur(
            params.start_date,
            params.end_date,
            params.format
        );
        notification.success("Report exported successfully");
    } catch (error) {
        notification.error("Failed to export report");
        throw error;
    }
};
</script>
