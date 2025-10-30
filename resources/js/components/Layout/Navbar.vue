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
                <!-- Dark Mode Toggle -->
                <button
                    @click="themeStore.toggleTheme"
                    type="button"
                    class="hidden lg:flex p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                    title="Toggle Theme"
                >
                    <MoonIcon v-if="!themeStore.isDark" class="w-5 h-5" />
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
        default: "Dashboard",
    },
    pageDescription: {
        type: String,
        default: "",
    },
});

const route = useRoute();
const themeStore = useThemeStore();
const searchQuery = ref("");

const emit = defineEmits(["search"]);

// Watch search query and emit to parent
watch(searchQuery, (newValue) => {
    emit("search", newValue);
});
</script>
