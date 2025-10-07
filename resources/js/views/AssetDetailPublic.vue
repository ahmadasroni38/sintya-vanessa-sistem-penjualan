<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                <div class="flex items-center">
                    <ExclamationTriangleIcon class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" />
                    <div>
                        <h3 class="text-lg font-medium text-red-800 dark:text-red-200">Error</h3>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ error }}</p>
                    </div>
                </div>
            </div>

            <!-- Asset Information -->
            <div v-else-if="asset" class="space-y-6">
                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <!-- Asset Image -->
                            <div v-if="asset.image_url" class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                                <img :src="asset.image_url" :alt="asset.name" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                <ArchiveBoxIcon class="w-10 h-10 text-gray-400" />
                            </div>

                            <!-- Asset Info -->
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ asset.name }}</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Code: {{ asset.code }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getConditionBadgeClass(asset.condition)
                                    ]">
                                        {{ asset.condition }}
                                    </span>
                                    <span :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getStatusBadgeClass(asset.status)
                                    ]">
                                        {{ asset.status.replace('_', ' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Auth Status Badge -->
                        <div v-if="asset.authenticated" class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <CheckCircleIcon class="w-5 h-5" />
                            <span>Authenticated</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions (Authenticated Only) -->
                <div v-if="asset.shortcuts_enabled" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button
                            @click="openWorkOrderModal"
                            class="flex items-center justify-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                        >
                            <WrenchScrewdriverIcon class="w-5 h-5 text-red-600 dark:text-red-400" />
                            <span class="font-medium text-red-700 dark:text-red-300">Create Work Order</span>
                        </button>
                        <button
                            @click="openRepairRequestModal"
                            class="flex items-center justify-center gap-3 px-4 py-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors"
                        >
                            <ExclamationTriangleIcon class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                            <span class="font-medium text-orange-700 dark:text-orange-300">Create Repair Request</span>
                        </button>
                    </div>
                </div>

                <!-- Asset Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Asset Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="space-y-3">
                            <div v-if="asset.description">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ asset.description }}</p>
                            </div>

                            <div v-if="asset.serial_number">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Serial Number</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ asset.serial_number }}</p>
                            </div>

                            <div v-if="asset.model_number">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Model Number</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ asset.model_number }}</p>
                            </div>

                            <div v-if="asset.manufacturer">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Manufacturer</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ asset.manufacturer }}</p>
                            </div>
                        </div>

                        <!-- Category & Location -->
                        <div class="space-y-3">
                            <div v-if="asset.category">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ asset.category.name }}</p>
                            </div>

                            <div v-if="asset.location">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ asset.location.name }}</p>
                                <p v-if="asset.location.full_address" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ asset.location.full_address }}
                                </p>
                            </div>

                            <div v-if="asset.current_value">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Value</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">${{ asset.current_value.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info (Authenticated Only) -->
                    <div v-if="asset.authenticated" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div v-if="asset.purchase_date">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Date</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ formatDate(asset.purchase_date) }}</p>
                            </div>

                            <div v-if="asset.warranty_expiry">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Warranty</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    {{ formatDate(asset.warranty_expiry) }}
                                    <span :class="asset.is_warranty_valid ? 'text-green-600' : 'text-red-600'">
                                        ({{ asset.is_warranty_valid ? 'Valid' : 'Expired' }})
                                    </span>
                                </p>
                            </div>

                            <div v-if="asset.quantity">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ asset.quantity }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Prompt (Not Authenticated) -->
                <div v-if="!asset.authenticated" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
                    <div class="flex items-start gap-3">
                        <InformationCircleIcon class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                        <div>
                            <h3 class="text-lg font-medium text-red-900 dark:text-red-100">Want to see more details?</h3>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                Login to view complete asset information and access quick actions like creating work orders and repair requests.
                            </p>
                            <button
                                @click="goToLogin"
                                class="mt-3 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                Login Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useNotificationStore } from '@/stores/notification';
import { apiGet } from '@/utils/api';
import {
    ArchiveBoxIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    WrenchScrewdriverIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const notification = useNotificationStore();

const loading = ref(true);
const error = ref(null);
const asset = ref(null);

const loadAssetInfo = async () => {
    try {
        loading.value = true;
        error.value = null;

        const identifier = route.params.identifier;
        const response = await apiGet(`assets/${identifier}/public`);

        if (response.success) {
            asset.value = response.data;
        } else {
            error.value = response.message || 'Failed to load asset information';
        }
    } catch (err) {
        console.error('Load asset error:', err);
        error.value = 'Asset not found or unavailable';
    } finally {
        loading.value = false;
    }
};

const getConditionBadgeClass = (condition) => {
    const classes = {
        excellent: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        good: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        fair: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        poor: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
        damaged: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };
    return classes[condition] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
};

const getStatusBadgeClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
        maintenance: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
        retired: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        lost: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const goToLogin = () => {
    router.push({
        name: 'login',
        query: { redirect: route.fullPath }
    });
};

const openWorkOrderModal = () => {
    // Redirect to work orders page with prefilled data
    router.push({
        name: 'work-orders',
        query: {
            action: 'create',
            asset_id: asset.value.id,
            asset_name: asset.value.name,
            asset_code: asset.value.code,
            location_id: asset.value.location?.id
        }
    });
};

const openRepairRequestModal = () => {
    // Redirect to repair requests page with prefilled data
    router.push({
        name: 'repair-requests',
        query: {
            action: 'create',
            asset_id: asset.value.id,
            asset_name: asset.value.name,
            asset_code: asset.value.code,
            location_id: asset.value.location?.id
        }
    });
};

onMounted(() => {
    loadAssetInfo();
});
</script>
