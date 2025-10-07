<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    My Profile
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage your account settings and profile information.
                </p>
            </div>
        </div>

        <!-- Profile Information -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <form @submit.prevent="updateProfile" enctype="multipart/form-data">
                <div class="p-6">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Avatar -->
                        <div class="sm:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Profile Photo
                            </label>
                            <div class="mt-1 flex items-center space-x-5">
                                <div class="flex-shrink-0">
                                    <img
                                        class="h-20 w-20 rounded-full object-cover"
                                        :src="
                                            profileForm.avatar_url ||
                                            defaultAvatar
                                        "
                                        :alt="profileForm.name"
                                    />
                                </div>
                                <div class="flex-1">
                                    <input
                                        type="file"
                                        ref="avatarInput"
                                        @change="handleAvatarChange"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                    <button
                                        type="button"
                                        @click="$refs.avatarInput.click()"
                                        class="bg-white dark:bg-gray-700 py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        Change Photo
                                    </button>
                                    <p
                                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        JPG, PNG up to 2MB
                                    </p>
                                    <p
                                        v-if="avatarError"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ avatarError }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                v-model="profileForm.name"
                                required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your full name"
                            />
                            <p
                                v-if="errors.name"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ errors.name[0] }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Email Address
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                v-model="profileForm.email"
                                required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your email"
                            />
                            <p
                                v-if="errors.email"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ errors.email[0] }}
                            </p>
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label
                                for="date_of_birth"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Date of Birth
                            </label>
                            <input
                                type="date"
                                id="date_of_birth"
                                v-model="profileForm.date_of_birth"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            />
                            <p
                                v-if="errors.date_of_birth"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ errors.date_of_birth[0] }}
                            </p>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label
                                for="phone_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Phone Number
                            </label>
                            <input
                                type="tel"
                                id="phone_number"
                                v-model="profileForm.phone_number"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your phone number"
                            />
                            <p
                                v-if="errors.phone_number"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ errors.phone_number[0] }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <label
                                for="address"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Address
                            </label>
                            <textarea
                                id="address"
                                v-model="profileForm.address"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your address..."
                            ></textarea>
                            <p
                                v-if="errors.address"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ errors.address[0] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right border-t border-gray-200 dark:border-gray-600 space-x-3"
                >
                    <router-link
                        to="/dashboard"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </router-link>
                    <button
                        type="submit"
                        :disabled="isUpdatingProfile"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            v-if="isUpdatingProfile"
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
                        {{ isUpdatingProfile ? "Saving..." : "Save Changes" }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Change -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <form @submit.prevent="changePassword">
                <div class="p-6">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Change Password
                    </h3>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Current Password -->
                        <div>
                            <label
                                for="current-password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Current Password
                            </label>
                            <input
                                type="password"
                                id="current-password"
                                v-model="passwordForm.currentPassword"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter current password"
                            />
                        </div>

                        <!-- New Password -->
                        <div>
                            <label
                                for="new-password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                New Password
                            </label>
                            <input
                                type="password"
                                id="new-password"
                                v-model="passwordForm.newPassword"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter new password"
                            />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label
                                for="confirm-password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Confirm New Password
                            </label>
                            <input
                                type="password"
                                id="confirm-password"
                                v-model="passwordForm.confirmPassword"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Confirm new password"
                            />
                        </div>
                    </div>
                </div>

                <div
                    class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right border-t border-gray-200 dark:border-gray-600"
                >
                    <button
                        type="submit"
                        :disabled="isChangingPassword"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            v-if="isChangingPassword"
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
                            isChangingPassword
                                ? "Changing..."
                                : "Change Password"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useNotificationStore } from "../../stores/notification";
import { apiGet, apiPut } from "../../utils/api";

const authStore = useAuthStore();
const notificationStore = useNotificationStore();

const isUpdatingProfile = ref(false);
const isChangingPassword = ref(false);
const avatarInput = ref(null);
const avatarError = ref("");
const errors = ref({});

const defaultAvatar =
    "https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80";

const profileForm = reactive({
    name: "",
    email: "",
    date_of_birth: "",
    phone_number: "",
    address: "",
    avatar_url: "",
});

const passwordForm = reactive({
    currentPassword: "",
    newPassword: "",
    confirmPassword: "",
});

// Load profile data on mount
onMounted(async () => {
    try {
        const response = await apiGet("/profile");
        if (response.success) {
            const data = response.data;
            profileForm.name = data.name || "";
            profileForm.email = data.email || "";
            profileForm.date_of_birth = data.date_of_birth
                ? data.date_of_birth.split("T")[0]
                : "";
            profileForm.phone_number = data.phone_number || "";
            profileForm.address = data.address || "";
            profileForm.avatar_url = data.avatar_url || "";
        }
    } catch (error) {
        console.error("Failed to load profile:", error);
        notificationStore.error("Failed to load profile data");
    }
});

const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    if (!["image/jpeg", "image/jpg", "image/png"].includes(file.type)) {
        avatarError.value = "Please select a valid image file (JPG, PNG)";
        return;
    }

    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        avatarError.value = "File size must be less than 2MB";
        return;
    }

    avatarError.value = "";
    // File will be handled in form submission
};

const updateProfile = async () => {
    isUpdatingProfile.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        formData.append("name", profileForm.name);
        formData.append("email", profileForm.email);
        formData.append("date_of_birth", profileForm.date_of_birth);
        formData.append("phone_number", profileForm.phone_number);
        formData.append("address", profileForm.address);

        if (avatarInput.value?.files[0]) {
            formData.append("avatar", avatarInput.value.files[0]);
        }

        const response = await apiPut("/profile", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.success) {
            // Update auth store with new profile data
            authStore.user = {
                ...authStore.user,
                ...response.data,
            };

            // Update local storage
            localStorage.setItem("user", JSON.stringify(authStore.user));

            // Update form with new data (including avatar URL)
            profileForm.avatar_url = response.data.avatar_url || profileForm.avatar_url;

            notificationStore.success("Profile updated successfully");
        }
    } catch (error) {
        console.error("Failed to update profile:", error);
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
        notificationStore.error("Failed to update profile");
    } finally {
        isUpdatingProfile.value = false;
    }
};

const changePassword = async () => {
    if (passwordForm.newPassword !== passwordForm.confirmPassword) {
        notificationStore.error("Passwords do not match");
        return;
    }

    if (passwordForm.newPassword.length < 8) {
        notificationStore.error("Password must be at least 8 characters and contain uppercase, lowercase, and numbers");
        return;
    }

    isChangingPassword.value = true;

    try {
        const response = await fetch("/api/profile/password", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.token}`,
                "Accept": "application/json",
            },
            body: JSON.stringify({
                current_password: passwordForm.currentPassword,
                new_password: passwordForm.newPassword,
                new_password_confirmation: passwordForm.confirmPassword,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                // Display specific validation errors
                const errorMessages = Object.values(data.errors).flat();
                notificationStore.error(errorMessages.join(', '));
            } else {
                notificationStore.error(data.message || "Failed to change password");
            }
            return;
        }

        // Clear form
        passwordForm.currentPassword = "";
        passwordForm.newPassword = "";
        passwordForm.confirmPassword = "";

        notificationStore.success("Password changed successfully");
    } catch (error) {
        console.error("Failed to change password:", error);
        notificationStore.error("Failed to change password. Please try again.");
    } finally {
        isChangingPassword.value = false;
    }
};
</script>
