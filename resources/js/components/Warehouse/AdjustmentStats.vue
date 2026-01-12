<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
            title="Total This Month"
            :value="stats.total_this_month"
            icon="cube"
            color="blue"
        />
        <StatCard
            title="Pending Approval"
            :value="stats.pending"
            icon="clock"
            color="yellow"
        />
        <StatCard
            title="Increase"
            :value="stats.increase"
            icon="arrow-up"
            color="green"
        />
        <StatCard
            title="Decrease"
            :value="stats.decrease"
            icon="arrow-down"
            color="red"
        />
    </div>
</template>

<script setup>
import StatCard from './StatCard.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_this_month: 0,
            pending: 0,
            increase: 0,
            decrease: 0,
            posted: 0,
        }),
    },
});
</script>
