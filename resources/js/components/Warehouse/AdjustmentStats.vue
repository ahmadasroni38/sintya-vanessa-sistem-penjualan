<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
            title="Total This Month"
            :value="stats.totalThisMonth"
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
import { computed } from 'vue';
import StatCard from './StatCard.vue';

const props = defineProps({
    adjustments: {
        type: Array,
        default: () => [],
    },
});

const stats = computed(() => {
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();

    const thisMonth = props.adjustments.filter((item) => {
        const itemDate = new Date(item.adjustment_date);
        return (
            itemDate.getMonth() === currentMonth &&
            itemDate.getFullYear() === currentYear
        );
    });

    const pending = props.adjustments.filter(
        (item) => item.status === 'draft'
    ).length;

    const increase = props.adjustments.filter(
        (item) => item.adjustment_type === 'increase'
    ).length;

    const decrease = props.adjustments.filter(
        (item) => item.adjustment_type === 'decrease'
    ).length;

    return {
        totalThisMonth: thisMonth.length,
        pending,
        increase,
        decrease,
    };
});
</script>
