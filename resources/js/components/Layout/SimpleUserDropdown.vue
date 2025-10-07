<template>
    <div class="relative">
        <button
            @click="toggleDropdown"
            class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-xl transition-all duration-200 dark:hover:bg-gray-700 group"
        >
            <!-- User Avatar -->
            <div class="relative">
                <img
                    class="w-8 h-8 rounded-lg object-cover ring-2 ring-gray-200 dark:ring-gray-700 group-hover:ring-blue-500 transition-all duration-200"
                    :src="userAvatar"
                    :alt="userName"
                    @error="handleImageError"
                />
                <!-- Online Status Indicator -->
                <div
                    class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"
                    title="Online"
                ></div>
            </div>

            <!-- User Info (Desktop Only) -->
            <div class="hidden lg:block text-left">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ userName }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ userRole }}
                </p>
            </div>

            <!-- Dropdown Icon -->
            <ChevronDownIcon
                :class="[
                    'w-4 h-4 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-all duration-200',
                    isOpen ? 'rotate-180' : '',
                ]"
            />
        </button>

        <!-- Dropdown Menu -->
        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-show="isOpen"
                class="absolute right-0 mt-2 w-64 origin-top-right bg-white shadow-xl rounded-2xl border border-gray-200 p-2 dark:bg-gray-800 dark:border-gray-700 z-50"
                @click.away="closeDropdown"
            >
                <!-- User Header -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <img
                            class="w-12 h-12 rounded-xl object-cover"
                            :src="userAvatar"
                            :alt="userName"
                            @error="handleImageError"
                        />
                        <div class="flex-1">
                            <p
                                class="font-semibold text-gray-900 dark:text-white"
                            >
                                {{ userName }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ userEmail }}
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <div
                                    class="w-2 h-2 bg-green-500 rounded-full"
                                ></div>
                                <span
                                    class="text-xs text-green-600 dark:text-green-400"
                                    >Online</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="p-2 space-y-1">
                    <!-- Profile -->
                    <router-link
                        :to="{ name: 'Profile' }"
                        @click="closeDropdown"
                        class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 dark:text-gray-300 dark:hover:bg-gray-700 group"
                    >
                        <UserIcon
                            class="w-4 h-4 group-hover:text-blue-500 transition-colors"
                        />
                        <span>My Profile</span>
                        <kbd
                            class="ml-auto text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded"
                            >⌘P</kbd
                        >
                    </router-link>

                    <!-- Divider -->
                    <div
                        class="border-t border-gray-100 dark:border-gray-700 my-2"
                    ></div>

                    <!-- Logout -->
                    <button
                        @click="handleLogout"
                        :disabled="isLoggingOut"
                        class="w-full flex items-center gap-3 p-3 rounded-xl text-sm text-red-600 hover:bg-red-50 transition-colors duration-200 dark:text-red-400 dark:hover:bg-red-900/20 group disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <ArrowRightOnRectangleIcon
                            v-if="!isLoggingOut"
                            class="w-4 h-4 group-hover:text-red-700 dark:group-hover:text-red-300 transition-colors"
                        />
                        <div
                            v-else
                            class="w-4 h-4 animate-spin rounded-full border-2 border-red-600 border-t-transparent"
                        ></div>
                        <span>{{
                            isLoggingOut ? "Signing Out..." : "Sign Out"
                        }}</span>
                        <kbd
                            v-if="!isLoggingOut"
                            class="ml-auto text-xs text-red-400 bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded"
                            >⌘Q</kbd
                        >
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import {
    ChevronDownIcon,
    UserIcon,
    CogIcon,
    ArrowRightOnRectangleIcon,
    CreditCardIcon,
    QuestionMarkCircleIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const authStore = useAuthStore();
const isLoggingOut = ref(false);
const isOpen = ref(false);

const defaultAvatar =
    "https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80";

const userName = computed(() => authStore.user?.name || "User");
const userEmail = computed(() => authStore.user?.email || "user@example.com");
const userRole = computed(() => {
    const role = authStore.user?.role || authStore.user?.roles?.[0]?.name;
    return role ? role.charAt(0).toUpperCase() + role.slice(1) : "User";
});
const userAvatar = computed(
    () =>
        authStore.user?.avatar ||
        authStore.user?.profile_photo_url ||
        defaultAvatar
);

const handleImageError = (event) => {
    event.target.src = defaultAvatar;
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
    isOpen.value = false;
};

const handleLogout = async () => {
    if (isLoggingOut.value) return;

    isLoggingOut.value = true;

    try {
        await authStore.logout();
        closeDropdown();
        router.push({ name: "Login" });
    } catch (error) {
        console.error("Logout error:", error);
        closeDropdown();
        router.push({ name: "Login" });
    } finally {
        isLoggingOut.value = false;
    }
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const dropdown = event.target.closest(".relative");
    if (!dropdown) {
        closeDropdown();
    }
};

// Keyboard shortcuts
const handleKeydown = (e) => {
    if (e.metaKey || e.ctrlKey) {
        switch (e.key) {
            case "p":
                e.preventDefault();
                router.push({ name: "Profile" });
                closeDropdown();
                break;
            case ",":
                e.preventDefault();
                router.push({ name: "Settings" });
                closeDropdown();
                break;
            case "q":
                e.preventDefault();
                handleLogout();
                break;
        }
    }

    // Close on Escape
    if (e.key === "Escape") {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
    document.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
    document.removeEventListener("keydown", handleKeydown);
});
</script>
