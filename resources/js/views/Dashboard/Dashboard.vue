<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Dashboard
                        </h1>
                        <p
                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Welcome back, {{ authStore.user?.name || "Admin" }}!
                            Here's what's happening today.
                        </p>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ currentDateTime }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Users -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-14a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    Total Assets
                                </dt>
                                <dd
                                    class="text-lg font-medium text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{ stats.totalAssets.toLocaleString() }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            +12%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    Work Orders
                                </dt>
                                <dd
                                    class="text-lg font-medium text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{ stats.totalWorkOrders.toLocaleString() }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            +8%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    Locations
                                </dt>
                                <dd
                                    class="text-lg font-medium text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{ stats.totalLocations.toLocaleString() }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"
                                />
                            </svg>
                            -3%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Growth Rate -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    Total Users
                                </dt>
                                <dd
                                    class="text-lg font-medium text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{ stats.totalUsers.toLocaleString() }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            +5%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Recent Activity
                    </h3>
                    <div class="space-y-4">
                        <div
                            v-for="activity in recentActivities"
                            :key="activity.id"
                            class="flex items-start space-x-3"
                        >
                            <div class="flex-shrink-0">
                                <div
                                    :class="[
                                        'w-8 h-8 rounded-full flex items-center justify-center text-white text-sm',
                                        activity.type === 'user'
                                            ? 'bg-red-500'
                                            : activity.type === 'order'
                                            ? 'bg-red-500'
                                            : activity.type === 'system'
                                            ? 'bg-gray-500'
                                            : 'bg-red-500',
                                    ]"
                                >
                                    {{
                                        activity.type === "user"
                                            ? "U"
                                            : activity.type === "order"
                                            ? "O"
                                            : "S"
                                    }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm text-gray-900 dark:text-white"
                                >
                                    {{ activity.description }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ activity.time }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Quick Actions
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <button
                            v-for="action in quickActions"
                            :key="action.name"
                            @click="handleQuickAction(action.action)"
                            class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
                        >
                            <div
                                :class="[
                                    'w-8 h-8 rounded-md flex items-center justify-center mb-2',
                                    action.color,
                                ]"
                            >
                                <svg
                                    class="w-5 h-5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        :d="action.icon"
                                    />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white text-center"
                            >
                                {{ action.name }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Recent Users
                    </h3>
                    <button
                        class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                    >
                        View all
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Joined
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr v-for="user in recentUsers" :key="user.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img
                                                class="h-10 w-10 rounded-full"
                                                :src="user.avatar"
                                                :alt="user.name"
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ user.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            user.status === 'active'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                : user.status === 'inactive'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        ]"
                                    >
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ user.joined }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import { apiGet } from "@/utils/api";
import { useNotificationStore } from "@/stores/notification";

const authStore = useAuthStore();
const notification = useNotificationStore();

// Loading states
const loading = ref(true);
const statisticsLoading = ref(true);

// Current date and time
const currentTime = ref(new Date());
let timeInterval = null;

const currentDateTime = computed(() => {
    return currentTime.value.toLocaleString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

// Real-time data from API
const statistics = ref({});
const chartData = ref({});
const recentActivity = ref({});

// Computed stats for easy access
const stats = computed(() => ({
    totalUsers: statistics.value.users?.total || 0,
    totalAssets: statistics.value.assets?.total || 0,
    totalWorkOrders: statistics.value.work_orders?.total || 0,
    totalLocations: statistics.value.locations?.total || 0,
}));

const recentActivities = computed(() => {
    const activities = [];

    // Work Orders
    if (recentActivity.value.recent_work_orders) {
        recentActivity.value.recent_work_orders.forEach(wo => {
            activities.push({
                id: `wo-${wo.id}`,
                type: 'work_order',
                description: `Work Order: ${wo.title}`,
                status: wo.status,
                time: formatTimeAgo(wo.created_at),
            });
        });
    }

    // Repair Requests
    if (recentActivity.value.recent_repair_requests) {
        recentActivity.value.recent_repair_requests.forEach(rr => {
            activities.push({
                id: `rr-${rr.id}`,
                type: 'repair_request',
                description: `Repair Request: ${rr.issue}`,
                status: rr.status,
                time: formatTimeAgo(rr.created_at),
            });
        });
    }

    // Recent Assets
    if (recentActivity.value.recent_assets) {
        recentActivity.value.recent_assets.forEach(asset => {
            activities.push({
                id: `asset-${asset.id}`,
                type: 'asset',
                description: `New Asset: ${asset.name} (${asset.code})`,
                status: asset.status,
                time: formatTimeAgo(asset.created_at),
            });
        });
    }

    // Sort by time (most recent first) and limit to 5
    return activities.slice(0, 5);
});

const quickActions = ref([
    {
        name: "Add Asset",
        action: "add-asset",
        route: "/assets",
        icon: "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4",
        color: "bg-red-500",
    },
    {
        name: "Create Work Order",
        action: "create-work-order",
        route: "/work-orders",
        icon: "M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
        color: "bg-red-500",
    },
    {
        name: "Manage Users",
        action: "manage-users",
        route: "/users",
        icon: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-14a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z",
        color: "bg-red-500",
    },
    {
        name: "View Reports",
        action: "view-reports",
        route: "/assets",
        icon: "M13 7h8m0 0v8m0-8l-8 8-4-4-6 6",
        color: "bg-yellow-500",
    },
]);

const recentUsers = ref([]);

// Load dashboard data from API
const loadDashboardData = async () => {
    loading.value = true;
    statisticsLoading.value = true;

    try {
        // Load statistics
        const statsResponse = await apiGet('/dashboard/statistics');
        if (statsResponse.success) {
            statistics.value = statsResponse.data;
        }

        // Load chart data
        const chartsResponse = await apiGet('/dashboard/chart-data');
        if (chartsResponse.success) {
            chartData.value = chartsResponse.data;
        }

        // Load recent activity
        const activityResponse = await apiGet('/dashboard/recent-activity');
        if (activityResponse.success) {
            recentActivity.value = activityResponse.data;
        }

    } catch (error) {
        console.error('Failed to load dashboard data:', error);
        notification.error('Failed to load dashboard data');
    } finally {
        loading.value = false;
        statisticsLoading.value = false;
    }
};

// Format time ago helper
const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    return `${Math.floor(diffInSeconds / 86400)} days ago`;
};

const handleQuickAction = (action) => {
    // Handle quick actions - redirect to relevant pages
    const actionItem = quickActions.value.find(a => a.action === action);
    if (actionItem && actionItem.route) {
        window.location.href = actionItem.route;
    }
};

onMounted(() => {
    // Load dashboard data
    loadDashboardData();

    // Update time every minute
    timeInterval = setInterval(() => {
        currentTime.value = new Date();
    }, 60000);

    // Refresh dashboard data every 5 minutes
    const refreshInterval = setInterval(() => {
        loadDashboardData();
    }, 300000); // 5 minutes

    // Cleanup on unmount
    onUnmounted(() => {
        if (timeInterval) clearInterval(timeInterval);
        if (refreshInterval) clearInterval(refreshInterval);
    });
});

onUnmounted(() => {
    if (timeInterval) {
        clearInterval(timeInterval);
    }
});
</script>
