<template>
    <header
        class="sticky top-0 inset-x-0 z-[48] w-full bg-white/95 backdrop-blur-sm border-b border-gray-200 dark:bg-gray-800/95 dark:border-gray-700 shadow-sm"
    >
        <nav
            class="flex items-center justify-between w-full mx-auto px-6 py-4"
            aria-label="Global"
        >
            <!-- Page Title Section -->
            <div class="flex items-center gap-4">
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ pageTitle }}
                    </h1>
                    <p
                        v-if="pageDescription"
                        class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        {{ pageDescription }}
                    </p>
                </div>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="hidden lg:block relative">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                    >
                        <MagnifyingGlassIcon class="w-4 h-4 text-gray-400" />
                    </div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-400 transition-all duration-200 w-64"
                        placeholder="Search anything..."
                    />
                </div>

                <!-- Notifications -->
                <NotificationDropdown />

                <!-- Quick Actions -->
                <button
                    class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                    title="Quick Actions"
                >
                    <PlusIcon class="w-5 h-5" />
                </button>

                <!-- Dark Mode Toggle -->
                <button
                    @click="themeStore.toggleTheme"
                    type="button"
                    class="hidden lg:flex p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                    title="Toggle Theme"
                >
                    <MoonIcon
                        v-if="!themeStore.isDark"
                        class="w-5 h-5"
                    />
                    <SunIcon v-else class="w-5 h-5" />
                </button>

                <!-- User Profile Dropdown -->
                <SimpleUserDropdown />
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { useThemeStore } from "../../stores/theme";
import {
    MagnifyingGlassIcon,
    PlusIcon,
    MoonIcon,
    SunIcon,
} from "@heroicons/vue/24/outline";
import NotificationDropdown from "./NotificationDropdown.vue";
import SimpleUserDropdown from "./SimpleUserDropdown.vue";

const props = defineProps({
    pageTitle: {
        type: String,
        default: "Dashboard"
    },
    pageDescription: {
        type: String,
        default: ""
    }
});

const route = useRoute();
const themeStore = useThemeStore();
const searchQuery = ref("");

const emit = defineEmits(['search']);

// Watch search query and emit to parent
watch(searchQuery, (newValue) => {
    emit('search', newValue);
});
</script>