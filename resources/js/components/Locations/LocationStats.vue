<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('locations.totalLocations') }}
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.total }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                >
                    <MapPinIcon class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('locations.activeLocations') }}
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.active }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                >
                    <CheckCircleIcon
                        class="w-6 h-6 text-green-600 dark:text-green-400"
                    />
                </div>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('locations.rootLocations') }}
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.root }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                >
                    <BuildingOfficeIcon
                        class="w-6 h-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('locations.newThisMonth') }}
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                    >
                        {{ stats.new_this_month }}
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center"
                >
                    <PlusIcon
                        class="w-6 h-6 text-orange-600 dark:text-orange-400"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import {
    MapPinIcon,
    CheckCircleIcon,
    BuildingOfficeIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";

const { t } = useI18n();

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            active: 0,
            root: 0,
            new_this_month: 0,
        }),
    },
});
</script>
