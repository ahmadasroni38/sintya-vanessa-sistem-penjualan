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
                        Set New Password
                    </h2>
                    <p class="text-lg text-white/90 leading-relaxed">
                        Create a strong password for your account. Make sure
                        it's unique and hard to guess.
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

        <!-- Right Side - Reset Password Form -->
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
                        Reset Password
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Enter your new password below
                    </p>
                </div>

                <form @submit.prevent="handleResetPassword" class="space-y-5">
                    <!-- Password Field -->
                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            New Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
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
                        <!-- Password Strength Indicator -->
                        <div v-if="form.password" class="mt-2">
                            <div class="flex items-center space-x-2">
                                <div
                                    class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2"
                                >
                                    <div
                                        :class="[
                                            'h-2 rounded-full transition-all duration-300',
                                            passwordStrength.color,
                                        ]"
                                        :style="{
                                            width: passwordStrength.width,
                                        }"
                                    ></div>
                                </div>
                                <span
                                    :class="[
                                        'text-xs font-medium',
                                        passwordStrength.color.replace(
                                            'bg-',
                                            'text-'
                                        ),
                                    ]"
                                >
                                    {{ passwordStrength.text }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label
                            for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Confirm New Password
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            @input="validateConfirmPassword"
                            @blur="validateConfirmPassword"
                            :class="[
                                'block w-full px-4 py-3 rounded-lg border text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500 transition-colors sm:text-sm',
                                errors.password_confirmation
                                    ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10 focus:border-red-500 focus:ring-red-500'
                                    : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-gray-400 dark:hover:border-gray-500',
                            ]"
                            placeholder="••••••••"
                        />
                        <p
                            v-if="errors.password_confirmation"
                            class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.password_confirmation }}
                        </p>
                    </div>

                    <!-- Password Requirements -->
                    <div
                        class="rounded-lg bg-blue-50 dark:bg-blue-900/10 p-4 border border-blue-200 dark:border-blue-800"
                    >
                        <h4
                            class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2"
                        >
                            Password Requirements:
                        </h4>
                        <ul
                            class="text-xs text-blue-700 dark:text-blue-300 space-y-1"
                        >
                            <li
                                :class="{
                                    'line-through': passwordChecks.length,
                                }"
                            >
                                ✓ At least 8 characters
                            </li>
                            <li
                                :class="{
                                    'line-through': passwordChecks.uppercase,
                                }"
                            >
                                ✓ One uppercase letter
                            </li>
                            <li
                                :class="{
                                    'line-through': passwordChecks.lowercase,
                                }"
                            >
                                ✓ One lowercase letter
                            </li>
                            <li
                                :class="{
                                    'line-through': passwordChecks.number,
                                }"
                            >
                                ✓ One number
                            </li>
                            <li
                                :class="{
                                    'line-through': passwordChecks.special,
                                }"
                            >
                                ✓ One special character (@$!%*?&)
                            </li>
                        </ul>
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

                    <!-- Success Message -->
                    <div
                        v-if="successMessage"
                        class="rounded-lg bg-green-50 dark:bg-green-900/10 p-4 border border-green-200 dark:border-green-800"
                    >
                        <div class="flex">
                            <svg
                                class="h-5 w-5 text-green-400 mr-3 flex-shrink-0"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L9.53 12.22a.75.75 0 001.214.882l2.236-3.126z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <p
                                class="text-sm text-green-800 dark:text-green-200"
                            >
                                {{ successMessage }}
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full flex justify-center items-center px-4 py-3 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <svg
                                v-if="isLoading"
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
                            {{ isLoading ? "Resetting..." : "Reset Password" }}
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="mt-8 text-center">
                    <router-link
                        to="/login"
                        class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                    >
                        ← Back to Login
                    </router-link>
                </div>

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
import { reactive, ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useThemeStore } from "../../stores/theme";
import { useNotificationStore } from "../../stores/notification";
import { apiPost, apiGet } from "../../utils/api";

const router = useRouter();
const route = useRoute();
const themeStore = useThemeStore();
const notificationStore = useNotificationStore();

const isLoading = ref(false);
const showPassword = ref(false);

const email = ref(route.query.email || "");
const token = ref(route.query.token || "");

const form = reactive({
    password: "",
    password_confirmation: "",
});

const errors = reactive({
    password: "",
    password_confirmation: "",
    general: "",
});

const successMessage = ref("");

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

// Password strength checks
const passwordChecks = computed(() => ({
    length: form.password.length >= 8,
    uppercase: /[A-Z]/.test(form.password),
    lowercase: /[a-z]/.test(form.password),
    number: /\d/.test(form.password),
    special: /[@$!%*?&]/.test(form.password),
}));

// Password strength indicator
const passwordStrength = computed(() => {
    const checks = Object.values(passwordChecks.value);
    const passed = checks.filter(Boolean).length;

    if (passed === 0) return { text: "", width: "0%", color: "bg-gray-300" };
    if (passed <= 2) return { text: "Weak", width: "33%", color: "bg-red-500" };
    if (passed <= 4)
        return { text: "Medium", width: "66%", color: "bg-yellow-500" };
    return { text: "Strong", width: "100%", color: "bg-green-500" };
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
onMounted(async () => {
    themeStore.loadTheme();
    await fetchSettings();

    if (!email.value || !token.value) {
        router.push("/forgot-password");
        return;
    }
});

const clearErrors = () => {
    errors.password = "";
    errors.password_confirmation = "";
    errors.general = "";
};

// Real-time password validation
const validatePassword = () => {
    if (!form.password) {
        errors.password = "Password is required";
    } else if (form.password.length < 8) {
        errors.password = "Password must be at least 8 characters";
    } else if (
        !/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(
            form.password
        )
    ) {
        errors.password =
            "Password must contain uppercase, lowercase, number, and special character";
    } else {
        errors.password = "";
    }
};

// Real-time confirm password validation
const validateConfirmPassword = () => {
    if (!form.password_confirmation) {
        errors.password_confirmation = "Please confirm your password";
    } else if (form.password_confirmation !== form.password) {
        errors.password_confirmation = "Passwords do not match";
    } else {
        errors.password_confirmation = "";
    }
};

const validateForm = () => {
    clearErrors();
    let isValid = true;

    if (!form.password) {
        errors.password = "Password is required";
        isValid = false;
    } else if (form.password.length < 8) {
        errors.password = "Password must be at least 8 characters";
        isValid = false;
    } else if (
        !/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(
            form.password
        )
    ) {
        errors.password =
            "Password must contain uppercase, lowercase, number, and special character";
        isValid = false;
    }

    if (!form.password_confirmation) {
        errors.password_confirmation = "Please confirm your password";
        isValid = false;
    } else if (form.password_confirmation !== form.password) {
        errors.password_confirmation = "Passwords do not match";
        isValid = false;
    }

    return isValid;
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const handleResetPassword = async () => {
    if (!validateForm()) return;

    clearErrors();
    isLoading.value = true;

    try {
        const response = await apiPost("/auth/reset-password", {
            email: email.value,
            token: token.value,
            password: form.password,
            password_confirmation: form.password_confirmation,
        });

        if (response.success) {
            successMessage.value = response.message;

            // Show success notification
            notificationStore.success(
                "Password Reset",
                "Your password has been successfully reset."
            );

            // Redirect to login after a delay
            setTimeout(() => {
                router.push("/login");
            }, 2000);
        } else {
            // Show error notification
            notificationStore.error(
                "Reset Failed",
                response.message || "Failed to reset password."
            );
            errors.general = response.message || "Failed to reset password.";
        }
    } catch (error) {
        console.error("Reset password error:", error);

        // Show error notification
        notificationStore.error(
            "Reset Failed",
            error.response?.data?.message ||
                "An error occurred. Please try again."
        );
        errors.general =
            error.response?.data?.message ||
            "An error occurred. Please try again.";
    } finally {
        isLoading.value = false;
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
