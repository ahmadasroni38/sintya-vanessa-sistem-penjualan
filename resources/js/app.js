import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import { useThemeStore } from "./stores/theme";
import { useAuthStore } from "./stores/auth";
import i18n from "./i18n";

// Import Preline
import "preline/preline.js";

// Global error handler
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
});

window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
});

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(i18n);

// Global error handler for Vue
app.config.errorHandler = (err, instance, info) => {
    console.error('Vue error:', err);
    console.error('Error info:', info);
    console.error('Component instance:', instance);
};

// Initialize stores
const themeStore = useThemeStore();
const authStore = useAuthStore();

// Load theme and user data on app start
try {
    themeStore.loadTheme();
    authStore.loadUserFromStorage();
} catch (error) {
    console.error('Error initializing stores:', error);
}

// Initialize Preline after router navigation
router.afterEach(() => {
    setTimeout(() => {
        if (window.HSStaticMethods) {
            window.HSStaticMethods.autoInit();
        }
    }, 100);
});

// Mount app with error handling
try {
    app.mount("#app");
    console.log('Vue app mounted successfully');
} catch (error) {
    console.error('Error mounting Vue app:', error);
}
