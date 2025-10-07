<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Settings
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage your application preferences and system settings.
                </p>
            </div>
        </div>

        <!-- Appearance Settings -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Appearance
                </h3>

                <div class="space-y-6">
                    <!-- Theme Toggle -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Dark Mode
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Toggle between light and dark theme
                            </p>
                        </div>
                        <button
                            @click="themeStore.toggleTheme"
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2',
                                themeStore.isDark
                                    ? 'bg-red-600'
                                    : 'bg-gray-200',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    themeStore.isDark
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            ></span>
                        </button>
                    </div>

                    <!-- Language Selection -->
                    <div>
                        <label
                            for="language"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Language
                        </label>
                        <FormSelect
                            v-model="settings.language"
                            :options="languageOptions"
                        />
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label
                            for="timezone"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Timezone
                        </label>
                        <FormSelect
                            v-model="settings.timezone"
                            :options="timezoneOptions"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Notifications
                </h3>

                <div class="space-y-6">
                    <!-- Email Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Email Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Receive email notifications for important
                                updates
                            </p>
                        </div>
                        <button
                            @click="
                                settings.emailNotifications =
                                    !settings.emailNotifications
                            "
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2',
                                settings.emailNotifications
                                    ? 'bg-red-600'
                                    : 'bg-gray-200',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    settings.emailNotifications
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            ></span>
                        </button>
                    </div>

                    <!-- Push Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Push Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Receive push notifications in your browser
                            </p>
                        </div>
                        <button
                            @click="
                                settings.pushNotifications =
                                    !settings.pushNotifications
                            "
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2',
                                settings.pushNotifications
                                    ? 'bg-red-600'
                                    : 'bg-gray-200',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    settings.pushNotifications
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            ></span>
                        </button>
                    </div>

                    <!-- SMS Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                SMS Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Receive SMS notifications for critical alerts
                            </p>
                        </div>
                        <button
                            @click="
                                settings.smsNotifications =
                                    !settings.smsNotifications
                            "
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2',
                                settings.smsNotifications
                                    ? 'bg-red-600'
                                    : 'bg-gray-200',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    settings.smsNotifications
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            ></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy & Security -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Privacy & Security
                </h3>

                <div class="space-y-6">
                    <!-- Two-Factor Authentication -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Two-Factor Authentication
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Add an extra layer of security to your account
                            </p>
                        </div>
                        <button
                            @click="
                                settings.twoFactorAuth = !settings.twoFactorAuth
                            "
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2',
                                settings.twoFactorAuth
                                    ? 'bg-red-600'
                                    : 'bg-gray-200',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    settings.twoFactorAuth
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            ></span>
                        </button>
                    </div>

                    <!-- Session Timeout -->
                    <div>
                        <label
                            for="session-timeout"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Session Timeout
                        </label>
                        <FormSelect
                            v-model="settings.sessionTimeout"
                            :options="sessionTimeoutOptions"
                        />
                    </div>

                    <!-- Activity Tracking -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Activity Tracking
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Track your activity for security monitoring
                            </p>
                        </div>
                        <button
                            @click="
                                settings.activityTracking =
                                    !settings.activityTracking
                            "
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2',
                                settings.activityTracking
                                    ? 'bg-red-600'
                                    : 'bg-gray-200',
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    settings.activityTracking
                                        ? 'translate-x-5'
                                        : 'translate-x-0',
                                ]"
                            ></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data & Storage -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Data & Storage
                </h3>

                <div class="space-y-6">
                    <!-- Data Export -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Export Data
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Download a copy of your data
                            </p>
                        </div>
                        <button
                            @click="exportData"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Export
                        </button>
                    </div>

                    <!-- Clear Cache -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Clear Cache
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Clear application cache and temporary data
                            </p>
                        </div>
                        <button
                            @click="clearCache"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                        >
                            Clear
                        </button>
                    </div>

                    <!-- Delete Account -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="text-sm font-medium text-red-700 dark:text-red-400"
                            >
                                Delete Account
                            </label>
                            <p class="text-sm text-red-500 dark:text-red-400">
                                Permanently delete your account and all data
                            </p>
                        </div>
                        <button
                            @click="showDeleteConfirmation = true"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Settings Button -->
        <div class="flex justify-end">
            <button
                @click="saveSettings"
                :disabled="isSaving"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg
                    v-if="isSaving"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                {{ isSaving ? "Saving..." : "Save Settings" }}
            </button>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteConfirmation"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click="showDeleteConfirmation = false"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800"
                @click.stop
            >
                <div class="mt-3 text-center">
                    <div
                        class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900"
                    >
                        <svg
                            class="h-6 w-6 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                            />
                        </svg>
                    </div>
                    <h3
                        class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-2"
                    >
                        Delete Account
                    </h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to delete your account? This
                            action cannot be undone and all your data will be
                            permanently removed.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button
                            @click="deleteAccount"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
                        >
                            Delete Account
                        </button>
                        <button
                            @click="showDeleteConfirmation = false"
                            class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useThemeStore } from "../../stores/theme";
import FormSelect from "../../components/Forms/FormSelect.vue";

const themeStore = useThemeStore();

const isSaving = ref(false);
const showDeleteConfirmation = ref(false);

const settings = reactive({
    language: "en",
    timezone: "UTC",
    emailNotifications: true,
    pushNotifications: true,
    smsNotifications: false,
    twoFactorAuth: false,
    sessionTimeout: "60",
    activityTracking: true,
});

const languageOptions = [
    { value: "en", label: "English" },
    { value: "id", label: "Bahasa Indonesia" },
    { value: "es", label: "Español" },
    { value: "fr", label: "Français" },
    { value: "de", label: "Deutsch" },
];

const timezoneOptions = [
    { value: "UTC", label: "UTC" },
    { value: "America/New_York", label: "Eastern Time (ET)" },
    { value: "America/Chicago", label: "Central Time (CT)" },
    { value: "America/Denver", label: "Mountain Time (MT)" },
    { value: "America/Los_Angeles", label: "Pacific Time (PT)" },
    { value: "Asia/Jakarta", label: "Western Indonesia Time (WIB)" },
    { value: "Asia/Tokyo", label: "Japan Standard Time (JST)" },
    { value: "Europe/London", label: "Greenwich Mean Time (GMT)" },
];

const sessionTimeoutOptions = [
    { value: "15", label: "15 minutes" },
    { value: "30", label: "30 minutes" },
    { value: "60", label: "1 hour" },
    { value: "240", label: "4 hours" },
    { value: "480", label: "8 hours" },
    { value: "never", label: "Never" },
];

const saveSettings = async () => {
    isSaving.value = true;

    try {
        // Simulate API call
        await new Promise((resolve) => setTimeout(resolve, 1500));

        // Save settings to localStorage
        localStorage.setItem("appSettings", JSON.stringify(settings));

        console.log("Settings saved successfully");
    } catch (error) {
        console.error("Failed to save settings:", error);
    } finally {
        isSaving.value = false;
    }
};

const exportData = async () => {
    try {
        // Simulate data export
        const data = {
            profile: JSON.parse(localStorage.getItem("user") || "{}"),
            settings: settings,
            exportDate: new Date().toISOString(),
        };

        const blob = new Blob([JSON.stringify(data, null, 2)], {
            type: "application/json",
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `user-data-export-${
            new Date().toISOString().split("T")[0]
        }.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        console.log("Data exported successfully");
    } catch (error) {
        console.error("Failed to export data:", error);
    }
};

const clearCache = async () => {
    try {
        // Clear various cache items
        if ("caches" in window) {
            const cacheNames = await caches.keys();
            await Promise.all(cacheNames.map((name) => caches.delete(name)));
        }

        // Clear some localStorage items (but keep user data)
        const itemsToKeep = ["user", "token", "theme", "appSettings"];
        const allKeys = Object.keys(localStorage);
        allKeys.forEach((key) => {
            if (!itemsToKeep.includes(key)) {
                localStorage.removeItem(key);
            }
        });

        console.log("Cache cleared successfully");
    } catch (error) {
        console.error("Failed to clear cache:", error);
    }
};

const deleteAccount = async () => {
    try {
        // Simulate account deletion API call
        await new Promise((resolve) => setTimeout(resolve, 2000));

        // Clear all local data
        localStorage.clear();
        sessionStorage.clear();

        // Redirect to login or show confirmation
        console.log("Account deleted successfully");
        showDeleteConfirmation.value = false;

        // In a real app, you would redirect to a goodbye page or login
        // window.location.href = '/goodbye'
    } catch (error) {
        console.error("Failed to delete account:", error);
    }
};

// Load settings from localStorage on component mount
const loadSettings = () => {
    const savedSettings = localStorage.getItem("appSettings");
    if (savedSettings) {
        try {
            Object.assign(settings, JSON.parse(savedSettings));
        } catch (error) {
            console.error("Failed to load settings:", error);
        }
    }
};

// Load settings when component mounts
loadSettings();
</script>
