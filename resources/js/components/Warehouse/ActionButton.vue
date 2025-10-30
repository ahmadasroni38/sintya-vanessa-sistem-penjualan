<template>
    <Tooltip :content="title" :position="tooltipPosition" :delay="tooltipDelay">
        <button
            @click.stop.prevent="$emit('click')"
            :class="[
                'min-w-[2rem] min-h-[2rem] p-1.5 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2',
                'hover:scale-105 active:scale-95',
                buttonClass,
                focusRingClass,
            ]"
            :aria-label="title"
            type="button"
        >
            <component
                :is="iconComponent"
                class="w-4 h-4 transition-transform duration-200"
            />
        </button>
    </Tooltip>
</template>

<script setup>
import { computed } from "vue";
import Tooltip from "@/components/Base/Tooltip.vue";
import {
    EyeIcon,
    PencilIcon,
    CheckIcon,
    CheckCircleIcon,
    CheckBadgeIcon,
    TrashIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    icon: {
        type: String,
        required: true,
        validator: (value) =>
            [
                "eye",
                "pencil",
                "check",
                "check-circle",
                "check-badge",
                "trash",
                "x-mark",
            ].includes(value),
    },
    color: {
        type: String,
        default: "gray",
        validator: (value) => ["blue", "green", "red", "gray"].includes(value),
    },
    title: {
        type: String,
        default: "",
    },
    tooltipPosition: {
        type: String,
        default: "top",
        validator: (value) =>
            ["top", "bottom", "left", "right"].includes(value),
    },
    tooltipDelay: {
        type: Number,
        default: 300,
    },
});

const emit = defineEmits(["click"]);

const iconComponent = computed(() => {
    const icons = {
        eye: EyeIcon,
        pencil: PencilIcon,
        check: CheckIcon,
        "check-circle": CheckCircleIcon,
        "check-badge": CheckBadgeIcon,
        trash: TrashIcon,
        "x-mark": XMarkIcon,
    };
    return icons[props.icon];
});

const buttonClass = computed(() => {
    const colors = {
        blue: "text-gray-400 hover:text-blue-500 hover:bg-blue-50/80 hover:shadow-blue-500/20 dark:hover:text-blue-400 dark:hover:bg-blue-900/30 transition-all duration-200",
        green: "text-gray-400 hover:text-green-500 hover:bg-green-50/80 hover:shadow-green-500/20 dark:hover:text-green-400 dark:hover:bg-green-900/30 transition-all duration-200",
        red: "text-gray-400 hover:text-red-500 hover:bg-red-50/80 hover:shadow-red-500/20 dark:hover:text-red-400 dark:hover:bg-red-900/30 transition-all duration-200",
        gray: "text-gray-400 hover:text-gray-500 hover:bg-gray-50/80 hover:shadow-gray-500/20 dark:hover:text-gray-300 dark:hover:bg-gray-700/50 transition-all duration-200",
    };
    return colors[props.color] || colors.gray;
});

const focusRingClass = computed(() => {
    const colors = {
        blue: "focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800",
        green: "focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800",
        red: "focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800",
        gray: "focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800",
    };
    return colors[props.color] || colors.gray;
});
</script>
