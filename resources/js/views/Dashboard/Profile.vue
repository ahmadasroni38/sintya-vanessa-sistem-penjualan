<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    My Profile
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Manage your account settings and preferences
                </p>
            </div>

            <!-- Profile Overview Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden"
            >
                <div
                    class="h-32 bg-gradient-to-r from-primary-500 to-pink-500"
                ></div>
                <div class="px-6 pb-6">
                    <div class="flex items-end -mt-16 mb-4">
                        <div class="relative">
                            <img
                                :src="profileData.avatar_url || defaultAvatar"
                                :alt="profileData.name"
                                class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 object-cover shadow-lg"
                            />
                            <div
                                class="absolute bottom-2 right-2 h-6 w-6 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"
                                :class="{
                                    'bg-green-500':
                                        profileData.status === 'active',
                                    'bg-gray-400':
                                        profileData.status !== 'active',
                                }"
                            ></div>
                        </div>
                        <div class="ml-6 mb-2">
                            <h2
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ profileData.name }}
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ profileData.email }}
                            </p>
                            <div class="flex items-center mt-2 space-x-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                                            profileData.status === 'active',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200':
                                            profileData.status !== 'active',
                                    }"
                                >
                                    {{ profileData.status }}
                                </span>
                                <span
                                    v-if="profileData.active_role"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                >
                                    {{ profileData.active_role.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div
                    class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-t-xl"
                >
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                            ]"
                        >
                            <i :class="tab.icon" class="mr-2"></i>
                            {{ tab.name }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content -->
            <div
                class="bg-white dark:bg-gray-800 rounded-b-xl shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <!-- Personal Information Tab -->
                <div v-show="activeTab === 'personal'">
                    <form @submit.prevent="updateProfile">
                        <div class="p-6 space-y-6">
                            <div>
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                                >
                                    Personal Information
                                </h3>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400 mb-6"
                                >
                                    Update your personal details and profile
                                    picture
                                </p>
                            </div>

                            <!-- Avatar Upload -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3"
                                >
                                    Profile Photo
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="relative">
                                        <img
                                            :src="
                                                avatarPreview ||
                                                profileForm.avatar_url ||
                                                defaultAvatar
                                            "
                                            alt="Avatar preview"
                                            class="h-24 w-24 rounded-full object-cover ring-4 ring-gray-100 dark:ring-gray-700"
                                        />
                                        <div
                                            v-if="
                                                uploadProgress > 0 &&
                                                uploadProgress < 100
                                            "
                                            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full"
                                        >
                                            <span
                                                class="text-white text-sm font-medium"
                                            >
                                                {{ uploadProgress }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <input
                                            type="file"
                                            ref="avatarInput"
                                            @change="handleAvatarChange"
                                            accept="image/jpeg,image/jpg,image/png"
                                            class="hidden"
                                        />
                                        <div class="flex space-x-3">
                                            <button
                                                type="button"
                                                @click="
                                                    $refs.avatarInput.click()
                                                "
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                                            >
                                                <i
                                                    class="fas fa-upload mr-2"
                                                ></i>
                                                Upload New
                                            </button>
                                            <button
                                                v-if="avatarPreview"
                                                type="button"
                                                @click="cancelAvatarUpload"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                                            >
                                                <i
                                                    class="fas fa-times mr-2"
                                                ></i>
                                                Cancel
                                            </button>
                                        </div>
                                        <p
                                            class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            JPG, PNG up to 2MB. Image will be
                                            resized to 500x500px
                                        </p>
                                        <p
                                            v-if="avatarError"
                                            class="mt-2 text-xs text-primary-600 dark:text-primary-400"
                                        >
                                            {{ avatarError }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Grid -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Full Name -->
                                <div>
                                    <label
                                        for="name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Full Name
                                        <span class="text-primary-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-user text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            type="text"
                                            id="name"
                                            v-model="profileForm.name"
                                            required
                                            class="pl-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                            placeholder="Enter your full name"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.name"
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        {{ errors.name[0] }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label
                                        for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Email Address
                                        <span class="text-primary-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-envelope text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            type="email"
                                            id="email"
                                            v-model="profileForm.email"
                                            required
                                            class="pl-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                            placeholder="your.email@example.com"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.email"
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        {{ errors.email[0] }}
                                    </p>
                                </div>

                                <!-- Date of Birth -->
                                <div>
                                    <label
                                        for="date_of_birth"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Date of Birth
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-calendar text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            type="date"
                                            id="date_of_birth"
                                            v-model="profileForm.date_of_birth"
                                            :max="maxDate"
                                            class="pl-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.date_of_birth"
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        {{ errors.date_of_birth[0] }}
                                    </p>
                                </div>

                                <!-- Phone Number -->
                                <div>
                                    <label
                                        for="phone_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-phone text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            type="tel"
                                            id="phone_number"
                                            v-model="profileForm.phone_number"
                                            class="pl-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                            placeholder="+62 812 3456 7890"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.phone_number"
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        {{ errors.phone_number[0] }}
                                    </p>
                                </div>

                                <!-- Address -->
                                <div class="sm:col-span-2">
                                    <label
                                        for="address"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Address
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-map-marker-alt text-gray-400"
                                            ></i>
                                        </div>
                                        <textarea
                                            id="address"
                                            v-model="profileForm.address"
                                            rows="3"
                                            class="pl-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                            placeholder="Enter your complete address..."
                                        ></textarea>
                                    </div>
                                    <p
                                        v-if="errors.address"
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        {{ errors.address[0] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600 rounded-b-xl flex justify-between items-center"
                        >
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Last updated:
                                {{ formatDate(profileData.updated_at) }}
                            </p>
                            <div class="flex space-x-3">
                                <router-link
                                    to="/dashboard"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                                >
                                    Cancel
                                </router-link>
                                <button
                                    type="submit"
                                    :disabled="isUpdatingProfile"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    <svg
                                        v-if="isUpdatingProfile"
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{
                                        isUpdatingProfile
                                            ? "Saving..."
                                            : "Save Changes"
                                    }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Security Tab -->
                <div v-show="activeTab === 'security'">
                    <form @submit.prevent="changePassword">
                        <div class="p-6 space-y-6">
                            <div>
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                                >
                                    Change Password
                                </h3>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400 mb-6"
                                >
                                    Ensure your account is using a strong
                                    password to stay secure
                                </p>
                            </div>

                            <!-- Password Requirements -->
                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
                            >
                                <h4
                                    class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2"
                                >
                                    Password Requirements:
                                </h4>
                                <ul
                                    class="text-xs text-blue-700 dark:text-blue-400 space-y-1"
                                >
                                    <li class="flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        At least 8 characters long
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Contains uppercase and lowercase letters
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Contains at least one number
                                    </li>
                                </ul>
                            </div>

                            <div class="grid grid-cols-1 gap-6 max-w-xl">
                                <!-- Current Password -->
                                <div>
                                    <label
                                        for="current-password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Current Password
                                        <span class="text-primary-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-lock text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            :type="
                                                showCurrentPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            id="current-password"
                                            v-model="
                                                passwordForm.currentPassword
                                            "
                                            required
                                            class="pl-10 pr-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                            placeholder="Enter current password"
                                        />
                                        <button
                                            type="button"
                                            @click="
                                                showCurrentPassword =
                                                    !showCurrentPassword
                                            "
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                        >
                                            <i
                                                :class="
                                                    showCurrentPassword
                                                        ? 'fas fa-eye-slash'
                                                        : 'fas fa-eye'
                                                "
                                            ></i>
                                        </button>
                                    </div>
                                    <p
                                        v-if="passwordErrors.current_password"
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        {{ passwordErrors.current_password[0] }}
                                    </p>
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label
                                        for="new-password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        New Password
                                        <span class="text-primary-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-key text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            :type="
                                                showNewPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            id="new-password"
                                            v-model="passwordForm.newPassword"
                                            required
                                            class="pl-10 pr-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                            placeholder="Enter new password"
                                        />
                                        <button
                                            type="button"
                                            @click="
                                                showNewPassword =
                                                    !showNewPassword
                                            "
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                        >
                                            <i
                                                :class="
                                                    showNewPassword
                                                        ? 'fas fa-eye-slash'
                                                        : 'fas fa-eye'
                                                "
                                            ></i>
                                        </button>
                                    </div>
                                    <!-- Password Strength Indicator -->
                                    <div
                                        v-if="passwordForm.newPassword"
                                        class="mt-2"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <div
                                                class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden"
                                            >
                                                <div
                                                    :class="
                                                        passwordStrengthClass
                                                    "
                                                    :style="{
                                                        width: passwordStrength.percent,
                                                    }"
                                                    class="h-full transition-all duration-300"
                                                ></div>
                                            </div>
                                            <span
                                                :class="
                                                    passwordStrengthTextClass
                                                "
                                                class="text-xs font-medium"
                                            >
                                                {{ passwordStrength.label }}
                                            </span>
                                        </div>
                                    </div>
                                    <p
                                        v-if="passwordErrors.new_password"
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        {{ passwordErrors.new_password[0] }}
                                    </p>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label
                                        for="confirm-password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Confirm New Password
                                        <span class="text-primary-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="fas fa-check-circle text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            :type="
                                                showConfirmPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            id="confirm-password"
                                            v-model="
                                                passwordForm.confirmPassword
                                            "
                                            required
                                            class="pl-10 pr-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors"
                                            placeholder="Confirm new password"
                                        />
                                        <button
                                            type="button"
                                            @click="
                                                showConfirmPassword =
                                                    !showConfirmPassword
                                            "
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                        >
                                            <i
                                                :class="
                                                    showConfirmPassword
                                                        ? 'fas fa-eye-slash'
                                                        : 'fas fa-eye'
                                                "
                                            ></i>
                                        </button>
                                    </div>
                                    <p
                                        v-if="
                                            passwordForm.confirmPassword &&
                                            passwordForm.newPassword !==
                                                passwordForm.confirmPassword
                                        "
                                        class="mt-1 text-xs text-primary-600 dark:text-primary-400"
                                    >
                                        Passwords do not match
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600 rounded-b-xl flex justify-end"
                        >
                            <button
                                type="submit"
                                :disabled="
                                    isChangingPassword ||
                                    passwordForm.newPassword !==
                                        passwordForm.confirmPassword
                                "
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <svg
                                    v-if="isChangingPassword"
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                                <i v-else class="fas fa-shield-alt mr-2"></i>
                                {{
                                    isChangingPassword
                                        ? "Changing..."
                                        : "Change Password"
                                }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Info Tab -->
                <div v-show="activeTab === 'account'">
                    <div class="p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Account Information
                        </h3>
                        <p
                            class="text-sm text-gray-600 dark:text-gray-400 mb-6"
                        >
                            View your account details and status
                        </p>

                        <div class="space-y-4">
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Account ID
                                    </p>
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ profileData.id }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Email Verification
                                    </p>
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{
                                            profileData.email_verified_at
                                                ? `Verified on ${formatDate(
                                                      profileData.email_verified_at
                                                  )}`
                                                : "Not verified"
                                        }}
                                    </p>
                                </div>
                                <span
                                    v-if="profileData.email_verified_at"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                >
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Verified
                                </span>
                            </div>

                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Account Created
                                    </p>
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ formatDate(profileData.created_at) }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Last Updated
                                    </p>
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ formatDate(profileData.updated_at) }}
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="profileData.active_role"
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 flex justify-between items-center"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Active Role
                                    </p>
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ profileData.active_role.name }}
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                >
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useNotificationStore } from "../../stores/notification";
import { apiGet, apiPost } from "../../utils/api";

const authStore = useAuthStore();
const notificationStore = useNotificationStore();

// Tab management
const activeTab = ref("personal");
const tabs = [
    { id: "personal", name: "Personal Info", icon: "fas fa-user" },
    { id: "security", name: "Security", icon: "fas fa-shield-alt" },
    { id: "account", name: "Account", icon: "fas fa-info-circle" },
];

// State
const isUpdatingProfile = ref(false);
const isChangingPassword = ref(false);
const avatarInput = ref(null);
const avatarError = ref("");
const avatarPreview = ref("");
const uploadProgress = ref(0);
const errors = ref({});
const passwordErrors = ref({});
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const defaultAvatar =
    "https://ui-avatars.com/api/?name=User&size=500&background=ef4444&color=fff&bold=true";

const profileData = reactive({
    id: "",
    name: "",
    email: "",
    date_of_birth: "",
    phone_number: "",
    address: "",
    avatar_url: "",
    status: "",
    email_verified_at: "",
    created_at: "",
    updated_at: "",
    active_role: null,
});

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

// Computed
const maxDate = computed(() => {
    const today = new Date();
    return today.toISOString().split("T")[0];
});

const passwordStrength = computed(() => {
    const password = passwordForm.newPassword;
    if (!password) return { percent: "0%", label: "", score: 0 };

    let score = 0;
    if (password.length >= 8) score += 25;
    if (password.length >= 12) score += 25;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score += 25;
    if (/\d/.test(password)) score += 25;
    if (/[^a-zA-Z\d]/.test(password)) score += 10;

    score = Math.min(score, 100);

    let label = "";
    if (score <= 25) label = "Weak";
    else if (score <= 50) label = "Fair";
    else if (score <= 75) label = "Good";
    else label = "Strong";

    return { percent: `${score}%`, label, score };
});

const passwordStrengthClass = computed(() => {
    const score = passwordStrength.value.score;
    if (score <= 25) return "bg-primary-500";
    if (score <= 50) return "bg-yellow-500";
    if (score <= 75) return "bg-blue-500";
    return "bg-green-500";
});

const passwordStrengthTextClass = computed(() => {
    const score = passwordStrength.value.score;
    if (score <= 25) return "text-primary-600 dark:text-primary-400";
    if (score <= 50) return "text-yellow-600 dark:text-yellow-400";
    if (score <= 75) return "text-blue-600 dark:text-blue-400";
    return "text-green-600 dark:text-green-400";
});

// Methods
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    };
    return new Date(dateString).toLocaleDateString("en-US", options);
};

const loadProfile = async () => {
    try {
        const response = await apiGet("/profile");
        if (response.success) {
            const data = response.data;

            // Update profileData
            Object.assign(profileData, data);

            // Update form
            profileForm.name = data.name || "";
            profileForm.email = data.email || "";
            profileForm.date_of_birth = data.date_of_birth || "";
            profileForm.phone_number = data.phone_number || "";
            profileForm.address = data.address || "";
            profileForm.avatar_url = data.avatar_url || "";
        }
    } catch (error) {
        console.error("Failed to load profile:", error);
        notificationStore.error("Failed to load profile data");
    }
};

const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    avatarError.value = "";

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

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const cancelAvatarUpload = () => {
    avatarPreview.value = "";
    avatarError.value = "";
    if (avatarInput.value) {
        avatarInput.value.value = "";
    }
};

const updateProfile = async () => {
    isUpdatingProfile.value = true;
    errors.value = {};
    uploadProgress.value = 0;

    try {
        const formData = new FormData();
        formData.append("name", profileForm.name);
        formData.append("email", profileForm.email);
        formData.append("date_of_birth", profileForm.date_of_birth || "");
        formData.append("phone_number", profileForm.phone_number || "");
        formData.append("address", profileForm.address || "");

        if (avatarInput.value?.files[0]) {
            formData.append("avatar", avatarInput.value.files[0]);
        }

        const response = await apiPost("/profile", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
            onUploadProgress: (progressEvent) => {
                uploadProgress.value = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total
                );
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

            // Update profile data
            await loadProfile();

            // Reset avatar upload
            avatarPreview.value = "";
            if (avatarInput.value) {
                avatarInput.value.value = "";
            }

            notificationStore.success("Profile updated successfully");
        }
    } catch (error) {
        console.error("Failed to update profile:", error);
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
        notificationStore.error(
            error.response?.data?.message || "Failed to update profile"
        );
    } finally {
        isUpdatingProfile.value = false;
        uploadProgress.value = 0;
    }
};

const changePassword = async () => {
    if (passwordForm.newPassword !== passwordForm.confirmPassword) {
        notificationStore.error("Passwords do not match");
        return;
    }

    if (passwordForm.newPassword.length < 8) {
        notificationStore.error("Password must be at least 8 characters");
        return;
    }

    isChangingPassword.value = true;
    passwordErrors.value = {};

    try {
        const response = await apiPost("/profile/password", {
            current_password: passwordForm.currentPassword,
            new_password: passwordForm.newPassword,
            new_password_confirmation: passwordForm.confirmPassword,
        });

        if (response.success) {
            // Clear form
            passwordForm.currentPassword = "";
            passwordForm.newPassword = "";
            passwordForm.confirmPassword = "";

            notificationStore.success("Password changed successfully");
        }
    } catch (error) {
        console.error("Failed to change password:", error);
        if (error.response?.data?.errors) {
            passwordErrors.value = error.response.data.errors;
        }
        notificationStore.error(
            error.response?.data?.message || "Failed to change password"
        );
    } finally {
        isChangingPassword.value = false;
    }
};

// Load profile on mount
onMounted(() => {
    loadProfile();
});
</script>

<style scoped>
/* Custom animations and transitions */
.transition-colors {
    transition-property: color, background-color, border-color,
        text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Smooth scrolling for tabs */
@media (max-width: 640px) {
    nav {
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    nav::-webkit-scrollbar {
        display: none;
    }
}
</style>
