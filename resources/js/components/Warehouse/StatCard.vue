<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ title }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ value }}
                </p>
            </div>
            <div :class="[
                'w-12 h-12 rounded-lg flex items-center justify-center',
                iconBackgroundClass
            ]">
                <component :is="iconComponent" :class="['w-6 h-6', iconColorClass]" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import {
    ArchiveBoxIcon,
    ClockIcon,
    CheckCircleIcon,
    CubeIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    AdjustmentsHorizontalIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: String,
        default: 'cube',
        validator: (value) => ['archive', 'clock', 'check', 'cube', 'arrow-up', 'arrow-down', 'adjustments'].includes(value),
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'yellow', 'green', 'purple', 'red'].includes(value),
    },
});

const iconComponent = computed(() => {
    const icons = {
        archive: ArchiveBoxIcon,
        clock: ClockIcon,
        check: CheckCircleIcon,
        cube: CubeIcon,
        'arrow-up': ArrowTrendingUpIcon,
        'arrow-down': ArrowTrendingDownIcon,
        adjustments: AdjustmentsHorizontalIcon,
    };
    return icons[props.icon] || CubeIcon;
});

const iconBackgroundClass = computed(() => {
    const colors = {
        blue: 'bg-blue-100 dark:bg-blue-900/20',
        yellow: 'bg-yellow-100 dark:bg-yellow-900/20',
        green: 'bg-green-100 dark:bg-green-900/20',
        purple: 'bg-purple-100 dark:bg-purple-900/20',
        red: 'bg-red-100 dark:bg-red-900/20',
    };
    return colors[props.color] || colors.blue;
});

const iconColorClass = computed(() => {
    const colors = {
        blue: 'text-blue-600 dark:text-blue-400',
        yellow: 'text-yellow-600 dark:text-yellow-400',
        green: 'text-green-600 dark:text-green-400',
        purple: 'text-purple-600 dark:text-purple-400',
        red: 'text-red-600 dark:text-red-400',
    };
    return colors[props.color] || colors.blue;
});
</script>
