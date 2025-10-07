<template>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors duration-300"
    >
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-red-100 dark:bg-red-900/20 opacity-50 blur-3xl"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-red-100 dark:bg-red-900/20 opacity-50 blur-3xl"
            ></div>
        </div>

        <!-- Header -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="flex justify-center">
                <div class="flex items-center space-x-2">
                    <div
                        class="w-10 h-10 rounded-lg bg-red-600 dark:bg-red-500 flex items-center justify-center shadow-lg transform transition-transform hover:scale-105"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        <span class="text-red-600 dark:text-red-400"
                            >Admin</span
                        >Panel
                    </h1>
                </div>
            </div>
            <h2
                class="mt-6 text-center text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
            >
                Sign in to your account
            </h2>
            <p
                class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400"
            >
                Welcome back! Please enter your details.
            </p>
        </div>

        <!-- Login Form -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg py-8 px-4 shadow-xl sm:rounded-xl sm:px-10 border border-gray-200/50 dark:border-gray-700/50 transition-all duration-300 hover:shadow-2xl"
            >
                <form @submit.prevent="handleLogin" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium leading-6 text-gray-900 dark:text-white transition-colors duration-200"
                        >
                            Email address
                        </label>
                        <div class="mt-2 relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
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
                                    'block w-full pl-10 rounded-lg border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset transition-all duration-200 sm:text-sm sm:leading-6',
                                    errors.email
                                        ? 'ring-red-300 focus:ring-red-500 dark:ring-red-600 dark:focus:ring-red-500 bg-red-50 dark:bg-red-900/10'
                                        : 'ring-gray-300 focus:ring-blue-600 dark:bg-gray-700 dark:ring-gray-600 dark:text-white dark:focus:ring-blue-500 hover:ring-gray-400',
                                ]"
                                placeholder="Enter your email"
                                aria-describedby="email-error"
                            />
                            <div
                                v-if="errors.email"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg
                                    class="h-5 w-5 text-red-500"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                        <p
                            v-if="errors.email"
                            id="email-error"
                            class="mt-2 text-sm text-red-600 dark:text-red-400 animate-fade-in"
                        >
                            {{ errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium leading-6 text-gray-900 dark:text-white transition-colors duration-200"
                        >
                            Password
                        </label>
                        <div class="mt-2 relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                            </div>
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
                                    'block w-full pl-10 pr-12 rounded-lg border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset transition-all duration-200 sm:text-sm sm:leading-6',
                                    errors.password
                                        ? 'ring-red-300 focus:ring-red-500 dark:ring-red-600 dark:focus:ring-red-500 bg-red-50 dark:bg-red-900/10'
                                        : 'ring-gray-300 focus:ring-blue-600 dark:bg-gray-700 dark:ring-gray-600 dark:text-white dark:focus:ring-blue-500 hover:ring-gray-400',
                                ]"
                                placeholder="Enter your password"
                                aria-describedby="password-error"
                            />
                            <button
                                type="button"
                                @click="togglePassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                                aria-label="Toggle password visibility"
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
                            <div
                                v-if="errors.password"
                                class="absolute inset-y-0 right-10 pr-3 flex items-center"
                            >
                                <svg
                                    class="h-5 w-5 text-red-500"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                        <p
                            v-if="errors.password"
                            id="password-error"
                            class="mt-2 text-sm text-red-600 dark:text-red-400 animate-fade-in"
                        >
                            {{ errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember-me"
                                v-model="form.remember"
                                name="remember-me"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-600 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-red-500 transition-colors duration-200"
                            />
                            <label
                                for="remember-me"
                                class="ml-2 block text-sm text-gray-900 dark:text-white cursor-pointer select-none transition-colors duration-200"
                            >
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm">
                            <a
                                href="#"
                                @click.prevent="handleForgotPassword"
                                class="font-medium text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200"
                            >
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div
                        v-if="errors.general"
                        class="rounded-md bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-800/30 animate-fade-in"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-red-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p
                                    class="text-sm font-medium text-red-800 dark:text-red-200"
                                >
                                    {{ errors.general }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            :disabled="authStore.isLoading"
                            class="group relative flex w-full justify-center rounded-lg bg-gradient-to-r from-red-600 to-red-600 px-4 py-3 text-sm font-semibold leading-6 text-white shadow-lg hover:from-red-500 hover:to-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:opacity-70 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]"
                        >
                            <span
                                class="absolute inset-0 rounded-lg bg-gradient-to-r from-red-600 to-red-600 opacity-0 transition-opacity duration-300 group-hover:opacity-10"
                            ></span>
                            <svg
                                v-if="authStore.isLoading"
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                            {{
                                authStore.isLoading
                                    ? "Signing in..."
                                    : "Sign in"
                            }}
                        </button>
                    </div>
                </form>

                <!-- Demo Credentials -->
                <div
                    class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6"
                >
                    <div class="text-center">
                        <p
                            class="text-sm text-gray-600 dark:text-gray-400 mb-3 font-medium"
                        >
                            Demo credentials for testing:
                        </p>
                        <div class="space-y-2 text-xs">
                            <div
                                class="bg-gray-100 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600 backdrop-blur-sm"
                            >
                                <div
                                    class="flex justify-between items-center mb-1"
                                >
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300"
                                        >Email:</span
                                    >
                                    <span
                                        class="text-gray-600 dark:text-gray-400 font-mono"
                                        >admin@example.com</span
                                    >
                                </div>
                                <div class="flex justify-between items-center">
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300"
                                        >Password:</span
                                    >
                                    <span
                                        class="text-gray-600 dark:text-gray-400 font-mono"
                                        >password123</span
                                    >
                                </div>
                            </div>
                        </div>
                        <button
                            @click="fillDemoCredentials"
                            type="button"
                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 dark:text-red-300 dark:bg-red-900/30 dark:hover:bg-red-800/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                />
                            </svg>
                            Fill demo credentials
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Theme Toggle (Bottom Right) -->
        <div class="fixed bottom-4 right-4 z-20">
            <button
                @click="themeStore.toggleTheme"
                type="button"
                class="group p-3 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-xl border border-gray-200/50 dark:border-gray-700/50 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                aria-label="Toggle theme"
            >
                <svg
                    v-if="!themeStore.isDark"
                    class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                    />
                </svg>
                <svg
                    v-else
                    class="w-5 h-5 group-hover:-rotate-12 transition-transform duration-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import { useThemeStore } from "../../stores/theme";
import { useNotificationStore } from "../../stores/notification";

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

// Initialize theme on component mount
onMounted(() => {
    themeStore.loadTheme();
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
    form.email = "admin@example.com";
    form.password = "password123";
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
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Custom focus styles for better accessibility */
input:focus,
button:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Smooth transitions for all interactive elements */
input,
button,
a {
    transition: all 0.2s ease;
}

/* Mobile responsiveness */
@media (max-width: 640px) {
    .min-h-screen {
        padding: 1rem;
    }

    .sm\\:rounded-xl {
        border-radius: 0.5rem;
    }
}
</style>
