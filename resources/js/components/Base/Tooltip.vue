<template>
    <div class="relative inline-block">
        <div
            ref="trigger"
            @mouseenter="showTooltip"
            @mouseleave="hideTooltip"
            @focus="showTooltip"
            @blur="hideTooltip"
        >
            <slot />
        </div>

        <!-- Tooltip -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isVisible"
                ref="tooltip"
                :class="[
                    'absolute z-50 px-3 py-2 text-sm font-medium text-white rounded-lg shadow-xl border',
                    'whitespace-nowrap pointer-events-none backdrop-blur-sm',
                    'animate-in fade-in-0 zoom-in-95 duration-200',
                    positionClasses,
                    darkMode
                        ? 'bg-gray-800/95 border-gray-600/50 shadow-gray-900/50'
                        : 'bg-gray-900/95 border-gray-700/50 shadow-gray-900/50',
                ]"
                role="tooltip"
            >
                <div class="flex items-center gap-2">
                    <span class="relative">
                        {{ content }}
                        <!-- Subtle glow effect -->
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-purple-400/20 rounded-lg blur-sm -z-10"
                        ></div>
                    </span>
                </div>

                <!-- Arrow with enhanced styling -->
                <div
                    :class="[
                        'absolute w-2 h-2 transform rotate-45 border backdrop-blur-sm',
                        darkMode
                            ? 'bg-gray-800/95 border-gray-600/50'
                            : 'bg-gray-900/95 border-gray-700/50',
                        arrowClasses,
                    ]"
                ></div>

                <!-- Subtle border glow -->
                <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-500/10 to-purple-500/10 pointer-events-none"
                ></div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
    position: {
        type: String,
        default: "top",
        validator: (value) =>
            ["top", "bottom", "left", "right"].includes(value),
    },
    delay: {
        type: Number,
        default: 300, // ms
    },
});

const emit = defineEmits(["show", "hide"]);

const trigger = ref(null);
const tooltip = ref(null);
const isVisible = ref(false);
const showTimeout = ref(null);
const hideTimeout = ref(null);

const darkMode = computed(() => {
    if (typeof window !== "undefined") {
        return document.documentElement.classList.contains("dark");
    }
    return false;
});

const positionClasses = computed(() => {
    const positions = {
        top: "bottom-full left-1/2 transform -translate-x-1/2 mb-2",
        bottom: "top-full left-1/2 transform -translate-x-1/2 mt-2",
        left: "right-full top-1/2 transform -translate-y-1/2 mr-2",
        right: "left-full top-1/2 transform -translate-y-1/2 ml-2",
    };
    return positions[props.position] || positions.top;
});

const arrowClasses = computed(() => {
    const arrows = {
        top: "top-full left-1/2 transform -translate-x-1/2 -mt-1 border-t border-l",
        bottom: "bottom-full left-1/2 transform -translate-x-1/2 -mb-1 border-b border-r",
        left: "left-full top-1/2 transform -translate-y-1/2 -ml-1 border-l border-t",
        right: "right-full top-1/2 transform -translate-y-1/2 -mr-1 border-r border-b",
    };
    return arrows[props.position] || arrows.top;
});

const showTooltip = () => {
    if (hideTimeout.value) {
        clearTimeout(hideTimeout.value);
        hideTimeout.value = null;
    }

    showTimeout.value = setTimeout(() => {
        isVisible.value = true;
        emit("show");
    }, props.delay);
};

const hideTooltip = () => {
    if (showTimeout.value) {
        clearTimeout(showTimeout.value);
        showTimeout.value = null;
    }

    hideTimeout.value = setTimeout(() => {
        isVisible.value = false;
        emit("hide");
    }, 150);
};

onUnmounted(() => {
    if (showTimeout.value) clearTimeout(showTimeout.value);
    if (hideTimeout.value) clearTimeout(hideTimeout.value);
});
</script>
