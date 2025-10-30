<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4"
    >
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ title }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                v-for="(item, index) in summaryItems"
                :key="index"
                class="text-center"
            >
                <div
                    :class="[
                        'p-3 rounded-lg mb-2',
                        item.bgColor || 'bg-gray-100 dark:bg-gray-700',
                    ]"
                >
                    <component
                        :is="item.icon"
                        :class="[
                            'w-6 h-6 mx-auto',
                            item.iconColor ||
                                'text-gray-600 dark:text-gray-400',
                        ]"
                    />
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                    {{ item.label }}
                </p>
                <p
                    :class="[
                        'text-lg font-bold',
                        item.valueColor || 'text-gray-900 dark:text-white',
                    ]"
                >
                    {{ formatCurrency(item.value) }}
                </p>
                <p
                    v-if="item.change !== undefined"
                    :class="[
                        'text-xs',
                        item.change >= 0
                            ? 'text-green-600 dark:text-green-400'
                            : 'text-red-600 dark:text-red-400',
                    ]"
                >
                    {{ item.change >= 0 ? "+" : ""
                    }}{{ formatCurrency(item.change) }} ({{
                        item.changePercent
                    }}%)
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    summaryItems: {
        type: Array,
        required: true,
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};
</script>
