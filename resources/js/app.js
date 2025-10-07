import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import { useThemeStore } from "./stores/theme";
import { useAuthStore } from "./stores/auth";

// Import Preline
import "preline/preline.js";

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// Initialize stores
const themeStore = useThemeStore();
const authStore = useAuthStore();

// Load theme and user data on app start
themeStore.loadTheme();
authStore.loadUserFromStorage();

// Initialize Preline after router navigation
router.afterEach(() => {
    setTimeout(() => {
        if (window.HSStaticMethods) {
            window.HSStaticMethods.autoInit();
        }
    }, 100);
});

app.mount("#app");
