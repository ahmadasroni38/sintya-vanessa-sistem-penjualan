<template>
    <Menu as="div" class="relative inline-block text-left">
        <MenuButton class="relative p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700" title="Notifications">
            <BellIcon class="w-5 h-5" />
            <!-- Notification Badge -->
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium animate-pulse"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
            <!-- Pulse animation for new notifications -->
            <span
                v-if="hasNewNotifications"
                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full animate-ping"
            ></span>
        </MenuButton>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems class="absolute right-0 mt-2 w-80 origin-top-right bg-white shadow-xl rounded-2xl border border-gray-200 p-2 dark:bg-gray-800 dark:border-gray-700 focus:outline-none z-50">
                <!-- Header -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ unreadCount }} unread{{ unreadCount !== 1 ? '' : '' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Mark all as read -->
                            <button
                                v-if="unreadCount > 0"
                                @click="markAllAsRead"
                                class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 px-2 py-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            >
                                Mark all read
                            </button>
                            <!-- Settings -->
                            <button
                                class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                title="Notification Settings"
                            >
                                <CogIcon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="max-h-80 overflow-y-auto">
                    <!-- Loading State -->
                    <div v-if="isLoading" class="p-4 text-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-500 border-t-transparent mx-auto"></div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Loading notifications...</p>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="notifications.length === 0" class="p-8 text-center">
                        <BellIcon class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No notifications</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">You're all caught up! Check back later.</p>
                    </div>

                    <!-- Notifications -->
                    <div v-else class="py-2">
                        <div
                            v-for="notification in displayedNotifications"
                            :key="notification.id"
                            @click="markAsRead(notification)"
                            class="group p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer rounded-lg mx-2 mb-1"
                        >
                            <div class="flex items-start gap-3">
                                <!-- Status Indicator -->
                                <div class="flex-shrink-0 mt-1">
                                    <div
                                        :class="[
                                            'w-2 h-2 rounded-full',
                                            notification.read_at ? 'bg-gray-300 dark:bg-gray-600' : 'bg-red-500 animate-pulse'
                                        ]"
                                    ></div>
                                </div>

                                <!-- Notification Icon -->
                                <div
                                    :class="[
                                        'flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center',
                                        getNotificationIconBg(notification.type)
                                    ]"
                                >
                                    <component
                                        :is="getNotificationIcon(notification.type)"
                                        :class="[
                                            'w-4 h-4',
                                            getNotificationIconColor(notification.type)
                                        ]"
                                    />
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p
                                                :class="[
                                                    'text-sm font-medium group-hover:text-gray-900 dark:group-hover:text-white transition-colors',
                                                    notification.read_at
                                                        ? 'text-gray-700 dark:text-gray-300'
                                                        : 'text-gray-900 dark:text-white'
                                                ]"
                                            >
                                                {{ notification.data?.title || notification.title }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                                {{ notification.data?.message || notification.message }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ formatDate(notification.created_at) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Actions (if any) -->
                                    <div v-if="notification.data?.action" class="mt-2">
                                        <button
                                            @click.stop="handleNotificationAction(notification)"
                                            class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium"
                                        >
                                            {{ notification.data.action.text }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-2 border-t border-gray-100 dark:border-gray-700">
                    <button
                        @click="viewAllNotifications"
                        class="w-full text-center text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors font-medium"
                    >
                        View all notifications
                    </button>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import {
    BellIcon,
    CogIcon,
    InformationCircleIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
    UserIcon,
    DocumentIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const authStore = useAuthStore();

const notifications = ref([]);
const isLoading = ref(false);
const hasNewNotifications = ref(false);

const unreadCount = computed(() =>
    notifications.value.filter(n => !n.read_at).length
);

const displayedNotifications = computed(() =>
    notifications.value.slice(0, 10)
);

const fetchNotifications = async () => {
    if (!authStore.token) return;

    isLoading.value = true;
    try {
        const response = await fetch('/api/notifications', {
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            const data = await response.json();
            const newNotifications = data.data || [];

            // Check for new notifications
            if (notifications.value.length > 0 && newNotifications.length > notifications.value.length) {
                hasNewNotifications.value = true;
                setTimeout(() => {
                    hasNewNotifications.value = false;
                }, 3000);
            }

            notifications.value = newNotifications;
        }
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    } finally {
        isLoading.value = false;
    }
};

const markAsRead = async (notification) => {
    if (notification.read_at) return;

    try {
        const response = await fetch(`/api/notifications/${notification.id}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            notification.read_at = new Date().toISOString();
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        const response = await fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            notifications.value.forEach(notification => {
                if (!notification.read_at) {
                    notification.read_at = new Date().toISOString();
                }
            });
        }
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
    }
};

const handleNotificationAction = (notification) => {
    if (notification.data?.action?.url) {
        if (notification.data.action.external) {
            window.open(notification.data.action.url, '_blank');
        } else {
            router.push(notification.data.action.url);
        }
    }
    markAsRead(notification);
};

const viewAllNotifications = () => {
    router.push({ name: 'Notifications' });
};

const getNotificationIcon = (type) => {
    const icons = {
        info: InformationCircleIcon,
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        error: XCircleIcon,
        user: UserIcon,
        system: CogIcon,
        document: DocumentIcon,
    };
    return icons[type] || InformationCircleIcon;
};

const getNotificationIconBg = (type) => {
    const backgrounds = {
        info: 'bg-red-100 dark:bg-red-900/30',
        success: 'bg-green-100 dark:bg-green-900/30',
        warning: 'bg-yellow-100 dark:bg-yellow-900/30',
        error: 'bg-red-100 dark:bg-red-900/30',
        user: 'bg-red-100 dark:bg-red-900/30',
        system: 'bg-gray-100 dark:bg-gray-700',
        document: 'bg-red-100 dark:bg-red-900/30',
    };
    return backgrounds[type] || 'bg-gray-100 dark:bg-gray-700';
};

const getNotificationIconColor = (type) => {
    const colors = {
        info: 'text-red-600 dark:text-red-400',
        success: 'text-green-600 dark:text-green-400',
        warning: 'text-yellow-600 dark:text-yellow-400',
        error: 'text-red-600 dark:text-red-400',
        user: 'text-red-600 dark:text-red-400',
        system: 'text-gray-600 dark:text-gray-400',
        document: 'text-red-600 dark:text-red-400',
    };
    return colors[type] || 'text-gray-600 dark:text-gray-400';
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));

    if (diffInMinutes < 1) {
        return 'Just now';
    } else if (diffInMinutes < 60) {
        return `${diffInMinutes}m ago`;
    } else if (diffInMinutes < 1440) {
        const hours = Math.floor(diffInMinutes / 60);
        return `${hours}h ago`;
    } else {
        const days = Math.floor(diffInMinutes / 1440);
        if (days === 1) return 'Yesterday';
        return `${days}d ago`;
    }
};

// Auto-refresh notifications
let refreshInterval;

onMounted(() => {
    fetchNotifications();
    // Refresh every 30 seconds
    refreshInterval = setInterval(fetchNotifications, 30000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>