<template>
    <div class="audit-history">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Audit History
            </h3>

            <div class="flex items-center space-x-4">
                <!-- Filters -->
                <div class="flex items-center space-x-3">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Event Type
                    </label>
                    <select
                        v-model="filters.event_type"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Events</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="restored">Restored</option>
                    </select>
                </div>

                <div class="flex items-center space-x-3">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
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
                                v-model="filters.start_date"
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
                                v-model="filters.end_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>
                    </div>
                </div>

                <!-- Search -->
                <div class="flex items-center space-x-3">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Search
                    </label>
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by user, field, or value..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <Button
                        @click="applyFilters"
                        variant="secondary"
                        :disabled="loading"
                    >
                        Apply Filters
                    </Button>

                    <Button
                        @click="clearFilters"
                        variant="secondary"
                        :disabled="loading"
                    >
                        Clear
                    </Button>
                </div>
            </div>

            <!-- Refresh -->
            <Button
                @click="refreshAudits"
                :loading="refreshing"
                variant="secondary"
                size="sm"
            >
                <span v-if="!refreshing">Refresh</span>
                <span v-else>Refreshing...</span>
            </Button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div
                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
            ></div>
            <span class="ml-2 text-blue-600">Loading audit history...</span>
        </div>

        <!-- Audit List -->
        <div v-else-if="audits.length > 0" class="space-y-4">
            <div
                v-for="audit in audits"
                :key="audit.id"
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border border-gray-200 dark:border-gray-700"
            >
                <!-- Audit Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-2">
                        <span
                            :class="getEventTypeClass(audit.event_type)"
                            class="px-2 py-1 text-xs font-medium rounded-full"
                        >
                            {{ getEventTypeLabel(audit.event_type) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ formatDate(audit.created_at) }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        by {{ audit.user_name || "System" }}
                    </div>
                </div>

                <!-- Audit Details -->
                <div class="space-y-3">
                    <!-- Account Info -->
                    <div
                        class="border-b border-gray-200 dark:border-gray-700 pb-3"
                    >
                        <h4
                            class="text-sm font-medium text-gray-900 dark:text-white mb-2"
                        >
                            Account Information
                        </h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Account Code:</span
                                >
                                <span
                                    class="text-gray-900 dark:text-white font-medium"
                                    >{{
                                        audit.new_values?.account_code ||
                                        audit.old_values?.account_code ||
                                        "N/A"
                                    }}</span
                                >
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Account Name:</span
                                >
                                <span
                                    class="text-gray-900 dark:text-white font-medium"
                                    >{{
                                        audit.new_values?.account_name ||
                                        audit.old_values?.account_name ||
                                        "N/A"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Changes -->
                    <div
                        v-if="audit.has_changes"
                        class="border-b border-gray-200 dark:border-gray-700 pb-3"
                    >
                        <h4
                            class="text-sm font-medium text-gray-900 dark:text-white mb-2"
                        >
                            Changes Made
                        </h4>
                        <div class="space-y-2">
                            <div
                                v-for="(change, index) in getChangesList(audit)"
                                :key="index"
                                class="flex items-start space-x-2 text-sm"
                            >
                                <span
                                    class="text-gray-500 dark:text-gray-400 min-w-20"
                                    >{{ change.field }}:</span
                                >
                                <div class="flex-1">
                                    <span
                                        v-if="change.old_value !== null"
                                        class="text-red-600 dark:text-red-400 line-through"
                                    >
                                        {{ change.old_value }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-gray-400 dark:text-gray-500 italic"
                                    >
                                        (empty)
                                    </span>
                                    <span
                                        v-if="change.new_value !== null"
                                        class="text-green-600 dark:text-green-400 font-medium"
                                    >
                                        {{ change.new_value }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-gray-400 dark:text-gray-500 italic"
                                    >
                                        (empty)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div
                        v-if="hasMetadata(audit)"
                        class="border-b border-gray-200 dark:border-gray-700 pb-3"
                    >
                        <h4
                            class="text-sm font-medium text-gray-900 dark:text-white mb-2"
                        >
                            Additional Information
                        </h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div v-if="audit.ip_address">
                                <span class="text-gray-500 dark:text-gray-400"
                                    >IP Address:</span
                                >
                                <span
                                    class="text-gray-900 dark:text-white font-medium"
                                    >{{ audit.ip_address }}</span
                                >
                            </div>
                            <div v-if="audit.user_agent">
                                <span class="text-gray-500 dark:text-gray-400"
                                    >User Agent:</span
                                >
                                <span
                                    class="text-gray-900 dark:text-white font-mono text-xs"
                                    >{{ audit.user_agent }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
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
                    d="M9 12h6m-6 0h6m-6 0h6m2 5H7a2 2 0 01-2-2V9a2 2 0 01-2-2h2a2 2 0 01 2-2z"
                />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                No audit history found
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                No audit events have been recorded for this account yet.
            </p>
        </div>

        <!-- Pagination -->
        <div
            v-if="pagination && pagination.last_page > 1"
            class="flex justify-center items-center space-x-2 mt-6"
        >
            <Button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                variant="secondary"
                size="sm"
            >
                Previous
            </Button>

            <span class="text-sm text-gray-700 dark:text-gray-300">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>

            <Button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                variant="secondary"
                size="sm"
            >
                Next
            </Button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useNotificationStore } from "@/stores/notification";
import chartOfAccountService from "@/services/chartOfAccountService";
import Button from "@/components/Base/Button.vue";

const props = defineProps({
    accountId: {
        type: Number,
        required: true,
    },
});

const notification = useNotificationStore();

// Reactive data
const audits = ref([]);
const pagination = ref(null);
const loading = ref(false);
const refreshing = ref(false);

// Filters
const filters = ref({
    event_type: "",
    start_date: "",
    end_date: "",
    search: "",
});

// Computed properties
const hasActiveFilters = computed(() => {
    return (
        filters.value.event_type !== "" ||
        filters.value.start_date !== "" ||
        filters.value.end_date !== "" ||
        filters.value.search !== ""
    );
});

// Methods
const fetchAudits = async () => {
    try {
        loading.value = true;

        const response = await chartOfAccountService.getAccountAudits(
            props.accountId,
            {
                per_page: 15,
                ...filters.value,
            }
        );

        if (response.success) {
            audits.value = response.data.data;
            pagination.value = response.data.meta?.pagination;
        } else {
            throw new Error(
                response.message || "Failed to fetch audit history"
            );
        }
    } catch (error) {
        notification.error(error.message || "Failed to fetch audit history");
    } finally {
        loading.value = false;
    }
};

const applyFilters = () => {
    fetchAudits();
};

const clearFilters = () => {
    filters.value = {
        event_type: "",
        start_date: "",
        end_date: "",
        search: "",
    };
    fetchAudits();
};

const refreshAudits = async () => {
    try {
        refreshing.value = true;
        await fetchAudits();
    } catch (error) {
        notification.error(error.message || "Failed to refresh audit history");
    } finally {
        refreshing.value = false;
    }
};

const goToPage = async (page) => {
    if (page < 1 || page > (pagination.value?.last_page || 1)) {
        return;
    }

    // Update filters to include page
    const currentFilters = { ...filters.value };
    // This would typically be handled by the API service
    await fetchAudits();
};

// Helper methods
const getEventTypeClass = (eventType) => {
    const classes = {
        created: "bg-green-100 text-green-800",
        updated: "bg-blue-100 text-blue-800",
        deleted: "bg-red-100 text-red-800",
        restored: "bg-yellow-100 text-yellow-800",
    };

    return classes[eventType] || "bg-gray-100 text-gray-800";
};

const getEventTypeLabel = (eventType) => {
    const labels = {
        created: "Created",
        updated: "Updated",
        deleted: "Deleted",
        restored: "Restored",
    };

    return labels[eventType] || eventType;
};

const formatDate = (dateString) => {
    if (!dateString) return "";

    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getChangesList = (audit) => {
    if (!audit.has_changes) return [];

    const changes = [];
    const allKeys = new Set([
        ...Object.keys(audit.old_values || {}),
        ...Object.keys(audit.new_values || {}),
    ]);

    allKeys.forEach((key) => {
        const oldValue = audit.old_values?.[key];
        const newValue = audit.new_values?.[key];

        if (oldValue !== newValue) {
            changes.push({
                field: key.replace(/_/g, " ").replace(/\b\w/g, " "),
                old_value: oldValue,
                new_value: newValue,
            });
        }
    });

    return changes;
};

const hasMetadata = (audit) => {
    return audit.ip_address || audit.user_agent;
};

// Lifecycle
onMounted(() => {
    fetchAudits();
});
</script>

<style scoped>
.audit-history {
    @apply max-w-4xl mx-auto;
}

.audit-item {
    @apply transition-all duration-200 ease-in-out;
}

.audit-item:hover {
    @apply transform scale-105 shadow-lg;
}
</style>
