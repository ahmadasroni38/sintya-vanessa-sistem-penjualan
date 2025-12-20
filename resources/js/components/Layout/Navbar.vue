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
                <!-- Language Switcher -->
                <div class="relative hidden lg:block" ref="languageDropdownContainer">
                    <button
                        @click.stop="toggleLanguageDropdown"
                        type="button"
                        class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                        title="Select Language"
                    >
                        <GlobeAltIcon class="w-5 h-5" />
                    </button>
                    <!-- Language Dropdown -->
                    <Transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div
                            v-if="showLanguageDropdown"
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                            @click.stop
                        >
                            <div class="py-1">
                                <button
                                    @click="changeLanguage('id')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-2 first:rounded-t-xl"
                                    :class="{ 'bg-primary-50 text-primary-700 dark:bg-primary-900 dark:text-primary-300': locale === 'id' }"
                                >
                                    <span class="text-lg">🇮🇩</span>
                                    <span>Bahasa Indonesia</span>
                                </button>
                                <button
                                    @click="changeLanguage('en')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-2 last:rounded-b-xl"
                                    :class="{ 'bg-primary-50 text-primary-700 dark:bg-primary-900 dark:text-primary-300': locale === 'en' }"
                                >
                                    <span class="text-lg">🇺🇸</span>
                                    <span>English</span>
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>

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
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import { useThemeStore } from "../../stores/theme";
import { useI18n } from "vue-i18n";
import {
    MagnifyingGlassIcon,
    PlusIcon,
    MoonIcon,
    SunIcon,
    GlobeAltIcon,
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
const showLanguageDropdown = ref(false);
const languageDropdownContainer = ref(null);
const { locale } = useI18n();

const emit = defineEmits(["search"]);

// Watch search query and emit to parent
watch(searchQuery, (newValue) => {
    emit("search", newValue);
});

// Toggle language dropdown
const toggleLanguageDropdown = () => {
    showLanguageDropdown.value = !showLanguageDropdown.value;
};

// Change language function
const changeLanguage = (lang) => {
    locale.value = lang;
    showLanguageDropdown.value = false;
    // Store language preference in localStorage
    localStorage.setItem('language', lang);
};

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
    if (languageDropdownContainer.value && !languageDropdownContainer.value.contains(event.target)) {
        showLanguageDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);

    // Load saved language preference
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage && (savedLanguage === 'en' || savedLanguage === 'id')) {
        locale.value = savedLanguage;
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
