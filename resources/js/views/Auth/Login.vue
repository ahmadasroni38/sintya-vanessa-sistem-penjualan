<template>
    <div
        class="min-h-screen bg-white dark:bg-gray-900 flex transition-colors duration-300"
    >
        <!-- Left Side - Branding Section -->
        <div
            class="hidden lg:flex lg:w-1/2 bg-primary-600 dark:bg-primary-700 flex-col justify-between p-12 relative overflow-hidden"
        >
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute inset-0"
                    style="
                        background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
                    "
                ></div>
            </div>

            <!-- Logo & Brand Content -->
            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-8">
                    <div
                        class="w-12 h-12 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center"
                    >
                        <img
                            v-if="settings.logo_sistem"
                            :src="`/storage/logo/${settings.logo_sistem}`"
                            :alt="settings.nama_sistem"
                            class="w-full h-full object-contain p-1.5"
                        />
                        <svg
                            v-else
                            class="w-7 h-7 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            {{ settings.nama_sistem || "AdminPanel" }}
                        </h1>
                        <p
                            v-if="settings.nama_perusahaan"
                            class="text-sm text-white/80"
                        >
                            {{ settings.nama_perusahaan }}
                        </p>
                    </div>
                </div>

                <div class="max-w-md">
                    <h2 class="text-3xl font-bold text-white mb-4">
                        Manage Your Business with Confidence
                    </h2>
                    <p class="text-lg text-white/90 leading-relaxed">
                        {{
                            settings.deskripsi_sistem ||
                            "Powerful tools to streamline your operations and drive growth."
                        }}
                    </p>
                </div>
            </div>

            <!-- Footer Info -->
            <div
                v-if="
                    settings.email_perusahaan ||
                    settings.nomor_telepon ||
                    settings.alamat_lengkap
                "
                class="relative z-10 text-white/80 text-sm space-y-2"
            >
                <p v-if="settings.email_perusahaan" class="flex items-center">
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                    {{ settings.email_perusahaan }}
                </p>
                <p v-if="settings.nomor_telepon" class="flex items-center">
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                        />
                    </svg>
                    {{ settings.nomor_telepon }}
                </p>
                <p v-if="settings.alamat_lengkap" class="flex items-start">
                    <svg
                        class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                    <span class="flex-1">{{ settings.alamat_lengkap }}</span>
                </p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div
            class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12"
        >
            <div class="w-full max-w-md">
                <!-- Mobile Logo (visible on small screens) -->
                <div class="lg:hidden mb-8 text-center">
                    <div class="flex justify-center mb-4">
                        <div
                            class="w-16 h-16 bg-primary-600 dark:bg-primary-500 rounded-lg flex items-center justify-center"
                        >
                            <img
                                v-if="settings.logo_sistem"
                                :src="`/storage/logo/${settings.logo_sistem}`"
                                :alt="settings.nama_sistem"
                                class="w-full h-full object-contain p-2"
                            />
                            <svg
                                v-else
                                class="w-8 h-8 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                />
                            </svg>
                        </div>
                    </div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ settings.nama_sistem || "AdminPanel" }}
                    </h1>
                    <p
                        v-if="settings.nama_perusahaan"
                        class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                    >
                        {{ settings.nama_perusahaan }}
                    </p>
                </div>

                <!-- Header -->
                <div class="mb-8">
                    <h2
                        class="text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Sign in
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Enter your credentials to access your account
                    </p>
                </div>

                <form @submit.prevent="handleLogin" class="space-y-5">
                    <!-- Email Field -->
                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Email address
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            @input="validateEmail"
                            @blur="validateEmail"
                            :class="[
                                'block w-full px-4 py-3 rounded-lg border text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500 transition-colors sm:text-sm',
                                errors.email
                                    ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10 focus:border-red-500 focus:ring-red-500'
                                    : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-gray-400 dark:hover:border-gray-500',
                            ]"
                            placeholder="you@example.com"
                        />
                        <p
                            v-if="errors.email"
                            class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                @input="validatePassword"
                                @blur="validatePassword"
                                :class="[
                                    'block w-full px-4 py-3 pr-11 rounded-lg border text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500 transition-colors sm:text-sm',
                                    errors.password
                                        ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10 focus:border-red-500 focus:ring-red-500'
                                        : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-gray-400 dark:hover:border-gray-500',
                                ]"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="togglePassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            >
                                <svg
                                    v-if="!showPassword"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"
                                    />
                                </svg>
                            </button>
                        </div>
                        <p
                            v-if="errors.password"
                            class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember-me"
                                v-model="form.remember"
                                name="remember-me"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-500"
                            />
                            <label
                                for="remember-me"
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none"
                            >
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm">
                            <a
                                href="#"
                                @click.prevent="handleForgotPassword"
                                class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                            >
                                Forgot password?
                            </a>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div
                        v-if="errors.general"
                        class="rounded-lg bg-red-50 dark:bg-red-900/10 p-4 border border-red-200 dark:border-red-800"
                    >
                        <div class="flex">
                            <svg
                                class="h-5 w-5 text-red-400 mr-3 flex-shrink-0"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <p class="text-sm text-red-800 dark:text-red-200">
                                {{ errors.general }}
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            :disabled="authStore.isLoading"
                            class="w-full flex justify-center items-center px-4 py-3 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <svg
                                v-if="authStore.isLoading"
                                class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
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
                            {{
                                authStore.isLoading ? "Signing in..." : "Sign in"
                            }}
                        </button>
                    </div>
                </form>

                <!-- Footer Text -->
                <p
                    v-if="settings.footer_text"
                    class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400"
                >
                    {{ settings.footer_text }}
                </p>

            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import { useThemeStore } from "../../stores/theme";
import { useNotificationStore } from "../../stores/notification";
import { apiGet } from "../../utils/api";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const themeStore = useThemeStore();
const notificationStore = useNotificationStore();

const showPassword = ref(false);

const form = reactive({
    email: "",
    password: "",
    remember: false,
});

const errors = reactive({
    email: "",
    password: "",
    general: "",
});

const settings = reactive({
    logo_sistem: "",
    nama_sistem: "AdminPanel",
    deskripsi_sistem: "",
    nama_perusahaan: "",
    alamat_lengkap: "",
    email_perusahaan: "",
    nomor_telepon: "",
    footer_text: "",
});

// Fetch system settings
const fetchSettings = async () => {
    try {
        const response = await apiGet("/settings");
        if (response.success) {
            Object.assign(settings, response.data);
        }
    } catch (error) {
        console.error("Error fetching settings:", error);
        // Keep default values if fetch fails
    }
};

// Initialize theme and fetch settings on component mount
onMounted(() => {
    themeStore.loadTheme();
    fetchSettings();
    fillDemoCredentials();
});

const clearErrors = () => {
    errors.email = "";
    errors.password = "";
    errors.general = "";
};

// Real-time email validation
const validateEmail = () => {
    if (!form.email) {
        errors.email = "Email is required";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = "Please enter a valid email address";
    } else {
        errors.email = "";
    }
};

// Real-time password validation
const validatePassword = () => {
    if (!form.password) {
        errors.password = "Password is required";
    } else if (form.password.length < 6) {
        errors.password = "Password must be at least 6 characters";
    } else {
        errors.password = "";
    }
};

const validateForm = () => {
    clearErrors();
    let isValid = true;

    if (!form.email) {
        errors.email = "Email is required";
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = "Please enter a valid email address";
        isValid = false;
    }

    if (!form.password) {
        errors.password = "Password is required";
        isValid = false;
    } else if (form.password.length < 6) {
        errors.password = "Password must be at least 6 characters";
        isValid = false;
    }

    return isValid;
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const handleLogin = async () => {
    if (!validateForm()) return;

    clearErrors();

    const result = await authStore.login({
        email: form.email,
        password: form.password,
        remember: form.remember,
    });

    if (result.success) {
        // Show success notification
        notificationStore.success(
            "Login Successful",
            `Welcome back, ${result.user.name}!`
        );

        // Redirect with a small delay for better UX
        const redirectTo = route.query.redirect || "/";
        setTimeout(() => {
            router.push(redirectTo);
        }, 800);
    } else {
        // Show error notification
        notificationStore.error(
            "Login Failed",
            result.error || "Invalid email or password. Please try again."
        );
        errors.general = result.error || "Login failed. Please try again.";
    }
};

const handleForgotPassword = () => {
    notificationStore.info(
        "Password Reset",
        "Password reset functionality will be implemented soon."
    );
};

const fillDemoCredentials = () => {
    form.email = "admin@sintiya.com";
    form.password = "admin123";
    clearErrors();

    // Add a subtle animation to indicate the form has been filled
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    if (emailInput && passwordInput) {
        emailInput.classList.add("ring-2", "ring-green-500");
        passwordInput.classList.add("ring-2", "ring-green-500");

        setTimeout(() => {
            emailInput.classList.remove("ring-2", "ring-green-500");
            passwordInput.classList.remove("ring-2", "ring-green-500");
        }, 1000);
    }
};
</script>

<style scoped>
/* Mobile responsiveness */
@media (max-width: 1024px) {
    .min-h-screen {
        padding: 1rem;
    }
}
</style>
