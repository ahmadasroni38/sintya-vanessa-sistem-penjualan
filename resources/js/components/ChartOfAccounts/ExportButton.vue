<template>
    <div class="export-buttons">
        <button
            @click="showExportModal = true"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200 flex items-center"
        >
            <svg
                class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 10v6m0 0l-3 3m0 0l3 3m-3-3v6m0 0h6"
                />
            </svg>
            Export
        </button>

        <!-- Export Modal -->
        <Modal
            :is-open="showExportModal"
            @close="showExportModal = false"
            size="md"
        >
            <template #title> Export Chart of Accounts </template>

            <form @submit.prevent="handleExport" class="space-y-4">
                <!-- Export Format -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Export Format
                    </label>
                    <select
                        v-model="exportFormat"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required
                    >
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="pdf">PDF (.pdf)</option>
                    </select>
                </div>

                <!-- Export Filters -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Filters (Optional)
                    </label>

                    <div class="space-y-3">
                        <!-- Account Type Filter -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1"
                            >
                                Account Type
                            </label>
                            <select
                                v-model="exportFilters.type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="">All Types</option>
                                <option value="asset">Asset</option>
                                <option value="liability">Liability</option>
                                <option value="equity">Equity</option>
                                <option value="revenue">Revenue</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1"
                            >
                                Status
                            </label>
                            <select
                                v-model="exportFilters.status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active Only</option>
                                <option value="inactive">Inactive Only</option>
                            </select>
                        </div>

                        <!-- Date Range Filter -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1"
                            >
                                Date Range
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"
                                    >
                                        Start Date
                                    </label>
                                    <input
                                        v-model="exportFilters.start_date"
                                        type="date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"
                                    >
                                        End Date
                                    </label>
                                    <input
                                        v-model="exportFilters.end_date"
                                        type="date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Options -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Export Options
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input
                                v-model="exportOptions.include_summary"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            />
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                Include Summary Sheet
                            </span>
                        </label>

                        <label class="flex items-center">
                            <input
                                v-model="exportOptions.include_inactive"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            />
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                Include Inactive Accounts
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4">
                    <Button
                        @click="showExportModal = false"
                        variant="secondary"
                        :disabled="exporting"
                    >
                        Cancel
                    </Button>

                    <Button
                        type="submit"
                        :loading="exporting"
                        :disabled="!exportFormat"
                    >
                        <span v-if="!exporting"
                            >Export {{ exportFormat.toUpperCase() }}</span
                        >
                        <span v-else>Exporting...</span>
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Export Status Modal -->
        <Modal
            :is-open="showStatusModal"
            @close="showStatusModal = false"
            size="lg"
        >
            <template #title> Export Status </template>

            <div class="space-y-4">
                <!-- Current Exports -->
                <div v-if="currentExports.length > 0">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Current Exports
                    </h3>

                    <div class="space-y-3">
                        <div
                            v-for="export in currentExports"
                            :key="export.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ export.filename }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatDate(export.created_at) }} •
                                    {{ formatFileSize(export.size) }}
                                </p>
                            </div>

                            <div class="flex space-x-2">
                                <Button
                                    v-if="export.status === 'completed'"
                                    @click="downloadExport(export)"
                                    variant="secondary"
                                    size="sm"
                                >
                                    Download
                                </Button>

                                <Button
                                    v-if="export.status === 'failed'"
                                    @click="retryExport(export)"
                                    variant="secondary"
                                    size="sm"
                                >
                                    Retry
                                </Button>

                                <div
                                    v-if="export.status === 'processing'"
                                    class="flex items-center"
                                >
                                    <div
                                        class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"
                                    ></div>
                                    <span class="ml-2 text-sm text-blue-600"
                                        >Processing...</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Exports -->
                <div v-else class="text-center py-8">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 0h6m-6 0h6m2 5H7a2 2 0 01-2-2V9a2 2 0 01-2 2h2a2 2 0 01 2 2v2a2 2 0 01 2-2h-2a2 2 0 01-2-2V7a2 2 0 01-2-2z"
                        />
                    </svg>
                    <h3
                        class="mt-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        No exports found
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Start an export to see the status here.
                    </p>
                </div>

                <!-- Refresh Button -->
                <div class="text-center mt-6">
                    <Button
                        @click="refreshExports"
                        :loading="refreshing"
                        variant="secondary"
                    >
                        <span v-if="!refreshing">Refresh</span>
                        <span v-else>Refreshing...</span>
                    </Button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useNotificationStore } from "@/stores/notification";
import chartOfAccountService from "@/services/chartOfAccountService";
import Button from "@/components/Base/Button.vue";
import Modal from "@/components/Overlays/Modal.vue";

const notification = useNotificationStore();

// Reactive data
const showExportModal = ref(false);
const showStatusModal = ref(false);
const exporting = ref(false);
const refreshing = ref(false);
const currentExports = ref([]);

// Export form data
const exportFormat = ref("excel");
const exportFilters = ref({
    type: "",
    status: "",
    start_date: "",
    end_date: "",
});

const exportOptions = ref({
    include_summary: true,
    include_inactive: false,
});

// Computed properties
const hasActiveFilters = computed(() => {
    return (
        exportFilters.value.type !== "" ||
        exportFilters.value.status !== "" ||
        exportFilters.value.start_date !== "" ||
        exportFilters.value.end_date !== ""
    );
});

// Methods
const handleExport = async () => {
    try {
        exporting.value = true;
        showExportModal.value = false;

        const options = {
            format: exportFormat.value,
            filters: exportFilters.value,
            options: exportOptions.value,
        };

        const response = await chartOfAccountService.exportAccounts(options);

        if (response.success) {
            notification.success(
                "Export job has been queued. You will be notified when it's ready."
            );
            await refreshExports();
        } else {
            throw new Error(response.message || "Export failed");
        }
    } catch (error) {
        notification.error(error.message || "Export failed");
    } finally {
        exporting.value = false;
    }
};

const downloadExport = async (exportItem) => {
    try {
        const response = await chartOfAccountService.getExportFileInfo(
            exportItem.filename
        );

        if (response.success) {
            // Create download link
            const link = document.createElement("a");
            link.href = response.data.url;
            link.download = exportItem.filename;
            link.target = "_blank";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            notification.success("Download started");
        } else {
            notification.error("Failed to download file");
        }
    } catch (error) {
        notification.error(error.message || "Download failed");
    }
};

const retryExport = async (exportItem) => {
    try {
        const response = await chartOfAccountService.retryExport(exportItem.id);

        if (response.success) {
            notification.success("Export job has been restarted");
            await refreshExports();
        } else {
            notification.error("Failed to restart export");
        }
    } catch (error) {
        notification.error(error.message || "Failed to restart export");
    }
};

const refreshExports = async () => {
    try {
        refreshing.value = true;

        const response = await chartOfAccountService.getExports();

        if (response.success) {
            currentExports.value = response.data;
        } else {
            notification.error("Failed to fetch exports");
        }
    } catch (error) {
        notification.error(error.message || "Failed to fetch exports");
    } finally {
        refreshing.value = false;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return "";

    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatFileSize = (bytes) => {
    if (!bytes) return "0 Bytes";

    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    const size = Math.round(bytes / Math.pow(1024, i), 2);

    return `${size} ${sizes[i]}`;
};

// Lifecycle
onMounted(() => {
    refreshExports();
});
</script>

<style scoped>
.export-buttons {
    @apply flex items-center space-x-4;
}
</style>
