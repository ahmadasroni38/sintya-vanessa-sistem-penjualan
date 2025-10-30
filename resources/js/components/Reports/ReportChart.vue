<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4"
    >
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ title }}
        </h3>
        <div class="relative h-64">
            <!-- Simple Bar Chart -->
            <div class="flex items-end justify-between h-full px-2">
                <div
                    v-for="(item, index) in chartData"
                    :key="index"
                    class="flex flex-col items-center flex-1"
                >
                    <div
                        class="w-full rounded-t transition-all duration-300 hover:opacity-80"
                        :class="item.color"
                        :style="{ height: getBarHeight(item.value) + 'px' }"
                        :title="`${item.label}: ${formatCurrency(item.value)}`"
                    ></div>
                    <span
                        class="text-xs text-gray-600 dark:text-gray-400 mt-2 text-center"
                        :title="`${item.label}: ${formatCurrency(item.value)}`"
                    >
                        {{ item.label }}
                    </span>
                </div>
            </div>

            <!-- Y-axis labels -->
            <div
                class="absolute left-0 top-0 bottom-8 w-8 flex flex-col justify-between text-xs text-gray-500 dark:text-gray-400"
            >
                <span>{{ formatCurrency(maxValue) }}</span>
                <span>{{ formatCurrency(maxValue * 0.75) }}</span>
                <span>{{ formatCurrency(maxValue * 0.5) }}</span>
                <span>{{ formatCurrency(maxValue * 0.25) }}</span>
                <span>0</span>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-4 flex flex-wrap justify-center gap-4">
            <div
                v-for="(item, index) in chartData"
                :key="index"
                class="flex items-center gap-2"
            >
                <div class="w-4 h-4 rounded" :class="item.color"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ item.label }}: {{ formatCurrency(item.value) }}
                </span>
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
    chartData: {
        type: Array,
        required: true,
    },
});

const maxValue = computed(() => {
    return Math.max(...props.chartData.map((item) => item.value));
});

const getBarHeight = (value) => {
    if (maxValue.value === 0) return 0;
    return Math.max(20, (value / maxValue.value) * 200);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};
</script>
