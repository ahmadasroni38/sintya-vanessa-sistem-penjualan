<template>
    <div class="tree-node">
        <div
            class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors group"
            :style="{ marginLeft: `${(node.level - 1) * 24}px` }"
        >
            <div class="flex items-center flex-1 min-w-0">
                <!-- Expand/Collapse Icon -->
                <button
                    v-if="node.children && node.children.length > 0"
                    @click="toggleExpand"
                    class="flex-shrink-0 mr-2 p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                >
                    <svg
                        class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform"
                        :class="{ 'rotate-90': isExpanded }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div v-else class="w-5 mr-2"></div>

                <!-- Account Icon -->
                <div :class="[
                    'flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mr-3',
                    getAccountTypeColor(node.account_type)
                ]">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                <!-- Account Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-mono font-semibold text-gray-900 dark:text-white">
                            {{ node.account_code }}
                        </span>
                        <span :class="[
                            'inline-flex px-2 py-0.5 text-xs font-semibold rounded-full',
                            getAccountTypeBadge(node.account_type)
                        ]">
                            {{ formatAccountType(node.account_type) }}
                        </span>
                        <span v-if="!node.is_active" class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                            Inactive
                        </span>
                    </div>
                    <p class="text-sm text-gray-900 dark:text-white font-medium mt-0.5">
                        {{ node.account_name }}
                    </p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Level {{ node.level }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Balance: {{ formatCurrency(node.current_balance || 0) }}
                        </span>
                        <span :class="[
                            'text-xs px-2 py-0.5 rounded-full',
                            node.normal_balance === 'debit'
                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'
                                : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                        ]">
                            {{ node.normal_balance.toUpperCase() }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity ml-4">
                    <button
                        @click="$emit('view', node)"
                        class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20"
                        title="View Details"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <button
                        @click="$emit('move', node)"
                        class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-50 rounded-lg transition-colors dark:hover:text-cyan-400 dark:hover:bg-cyan-900/20"
                        title="Move Account"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </button>
                    <button
                        @click="$emit('edit', node)"
                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors dark:hover:text-primary-400 dark:hover:bg-primary-900/20"
                        title="Edit"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button
                        @click="$emit('delete', node)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Children Nodes -->
        <transition
            enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-150 ease-in"
            enter-from-class="opacity-0 transform -translate-y-2"
            leave-to-class="opacity-0 transform -translate-y-2"
        >
            <div v-if="isExpanded && node.children && node.children.length > 0" class="mt-2 space-y-2">
                <TreeNode
                    v-for="child in node.children"
                    :key="child.id"
                    :node="child"
                    @view="$emit('view', $event)"
                    @edit="$emit('edit', $event)"
                    @delete="$emit('delete', $event)"
                    @move="$emit('move', $event)"
                />
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    node: {
        type: Object,
        required: true
    }
});

defineEmits(['view', 'edit', 'delete', 'move']);

const isExpanded = ref(true);

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

const getAccountTypeColor = (type) => {
    const colors = {
        asset: 'bg-gradient-to-br from-blue-500 to-blue-600',
        liability: 'bg-gradient-to-br from-red-500 to-red-600',
        equity: 'bg-gradient-to-br from-purple-500 to-purple-600',
        revenue: 'bg-gradient-to-br from-green-500 to-green-600',
        expense: 'bg-gradient-to-br from-orange-500 to-orange-600'
    };
    return colors[type] || 'bg-gradient-to-br from-gray-500 to-gray-600';
};

const getAccountTypeBadge = (type) => {
    const badges = {
        asset: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        liability: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        equity: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        revenue: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        expense: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
    };
    return badges[type] || 'bg-gray-100 text-gray-800';
};

const formatAccountType = (type) => {
    return type.charAt(0).toUpperCase() + type.slice(1);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};
</script>

<style scoped>
.tree-node {
    position: relative;
}
</style>
