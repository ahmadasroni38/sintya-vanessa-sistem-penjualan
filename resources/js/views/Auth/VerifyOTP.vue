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
                        Verify Your Identity
                    </h2>
                    <p class="text-lg text-white/90 leading-relaxed">
                        We've sent a 6-digit OTP to your email. Enter it below
                        to verify your identity and reset your password.
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

        <!-- Right Side - OTP Verification Form -->
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
                        Enter OTP
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        We've sent a 6-digit code to
                        <strong>{{ email }}</strong>
                    </p>
                </div>

                <form @submit.prevent="handleVerifyOTP" class="space-y-5">
                    <!-- OTP Input -->
                    <div>
                        <label
                            for="otp"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Verification Code
                        </label>
                        <div class="flex space-x-2">
                            <input
                                v-for="(digit, index) in otpDigits"
                                :key="index"
                                :ref="(el) => (otpInputs[index] = el)"
                                v-model="otpDigits[index]"
                                type="text"
                                maxlength="1"
                                @input="handleOtpInput(index, $event)"
                                @keydown="handleOtpKeydown(index, $event)"
                                @paste="handleOtpPaste"
                                class="w-12 h-12 text-center text-xl font-semibold rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500 focus:border-primary-600 dark:focus:border-primary-500 transition-colors"
                                :class="{
                                    'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10 focus:border-red-500 focus:ring-red-500':
                                        errors.otp,
                                }"
                            />
                        </div>
                        <p
                            v-if="errors.otp"
                            class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.otp }}
                        </p>
                        <p
                            class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                        >
                            OTP expires in {{ formatTime(remainingTime) }}
                        </p>
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
                            :disabled="isLoading || remainingTime <= 0"
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
                            {{
                                isLoading
                                    ? "Verifying..."
                                    : remainingTime <= 0
                                    ? "OTP Expired"
                                    : "Verify OTP"
                            }}
                        </button>
                    </div>
                </form>

                <!-- Resend OTP -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Didn't receive the code?
                        <button
                            @click="handleResendOTP"
                            :disabled="isResending || resendCooldown > 0"
                            class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{
                                isResending
                                    ? "Sending..."
                                    : resendCooldown > 0
                                    ? `Resend in ${resendCooldown}s`
                                    : "Resend OTP"
                            }}
                        </button>
                    </p>
                </div>

                <!-- Back to Forgot Password -->
                <div class="mt-4 text-center">
                    <router-link
                        to="/forgot-password"
                        class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                    >
                        ← Change Email
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
import { reactive, ref, onMounted, onUnmounted, nextTick } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useThemeStore } from "../../stores/theme";
import { useNotificationStore } from "../../stores/notification";
import { apiPost, apiGet } from "../../utils/api";

const router = useRouter();
const route = useRoute();
const themeStore = useThemeStore();
const notificationStore = useNotificationStore();

const isLoading = ref(false);
const isResending = ref(false);
const resendCooldown = ref(0);
const remainingTime = ref(15 * 60); // 15 minutes in seconds
const timer = ref(null);

const email = ref(route.query.email || "");

const otpDigits = reactive(Array(6).fill(""));
const otpInputs = ref([]);

const errors = reactive({
    otp: "",
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
onMounted(async () => {
    themeStore.loadTheme();
    await fetchSettings();

    if (!email.value) {
        router.push("/forgot-password");
        return;
    }

    startTimer();
    await nextTick();
    otpInputs.value[0]?.focus();
});

onUnmounted(() => {
    if (timer.value) {
        clearInterval(timer.value);
    }
});

const startTimer = () => {
    timer.value = setInterval(() => {
        remainingTime.value--;
        if (remainingTime.value <= 0) {
            clearInterval(timer.value);
            errors.otp = "OTP has expired. Please request a new one.";
        }
    }, 1000);
};

const clearErrors = () => {
    errors.otp = "";
    errors.general = "";
};

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, "0")}`;
};

const handleOtpInput = (index, event) => {
    const value = event.target.value.replace(/\D/g, "");
    otpDigits[index] = value;

    if (value && index < 5) {
        otpInputs.value[index + 1]?.focus();
    }

    clearErrors();
};

const handleOtpKeydown = (index, event) => {
    if (event.key === "Backspace" && !otpDigits[index] && index > 0) {
        otpInputs.value[index - 1]?.focus();
    }
};

const handleOtpPaste = (event) => {
    event.preventDefault();
    const paste = event.clipboardData
        .getData("text")
        .replace(/\D/g, "")
        .slice(0, 6);

    for (let i = 0; i < paste.length; i++) {
        if (otpInputs.value[i]) {
            otpDigits[i] = paste[i];
        }
    }

    if (paste.length === 6) {
        otpInputs.value[5]?.focus();
    }

    clearErrors();
};

const validateOtp = () => {
    const otp = otpDigits.join("");
    if (otp.length !== 6) {
        errors.otp = "Please enter the complete 6-digit OTP";
        return false;
    }
    if (!/^\d{6}$/.test(otp)) {
        errors.otp = "OTP must contain only numbers";
        return false;
    }
    return true;
};

const handleVerifyOTP = async () => {
    if (!validateOtp()) return;
    if (remainingTime.value <= 0) {
        errors.general = "OTP has expired. Please request a new one.";
        return;
    }

    clearErrors();
    isLoading.value = true;

    const otp = otpDigits.join("");

    try {
        const response = await apiPost("/auth/verify-otp", {
            email: email.value,
            otp: otp,
        });

        if (response.success) {
            // Show success notification
            notificationStore.success(
                "OTP Verified",
                "You can now reset your password."
            );

            // Redirect to reset password with token
            router.push({
                path: "/reset-password",
                query: {
                    email: email.value,
                    token: response.token,
                },
            });
        } else {
            // Show error notification
            notificationStore.error(
                "Verification Failed",
                response.message || "Invalid OTP. Please try again."
            );
            errors.general =
                response.message || "Invalid OTP. Please try again.";
        }
    } catch (error) {
        console.error("OTP verification error:", error);

        // Show error notification
        notificationStore.error(
            "Verification Failed",
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

const handleResendOTP = async () => {
    if (resendCooldown.value > 0) return;

    clearErrors();
    isResending.value = true;

    try {
        const response = await apiPost("/auth/forgot-password", {
            email: email.value,
        });

        if (response.success) {
            // Reset timer and cooldown
            remainingTime.value = 15 * 60;
            resendCooldown.value = 60; // 60 seconds cooldown

            // Start cooldown timer
            const cooldownTimer = setInterval(() => {
                resendCooldown.value--;
                if (resendCooldown.value <= 0) {
                    clearInterval(cooldownTimer);
                }
            }, 1000);

            // Show success notification
            notificationStore.success(
                "OTP Resent",
                "A new OTP has been sent to your email."
            );
        } else {
            // Show error notification
            notificationStore.error(
                "Resend Failed",
                response.message || "Failed to resend OTP."
            );
            errors.general = response.message || "Failed to resend OTP.";
        }
    } catch (error) {
        console.error("Resend OTP error:", error);

        // Show error notification
        notificationStore.error(
            "Resend Failed",
            error.response?.data?.message ||
                "An error occurred. Please try again."
        );
        errors.general =
            error.response?.data?.message ||
            "An error occurred. Please try again.";
    } finally {
        isResending.value = false;
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
