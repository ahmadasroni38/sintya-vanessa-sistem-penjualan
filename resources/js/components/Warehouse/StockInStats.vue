<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
            title="Total Transaksi"
            :value="stats.total_transactions"
            icon="archive"
            color="blue"
        />
        <StatCard
            title="Draft"
            :value="stats.draft_count"
            icon="clock"
            color="yellow"
        />
        <StatCard
            title="Posted"
            :value="stats.posted_count"
            icon="check"
            color="green"
        />
        <StatCard
            title="Total Nilai"
            :value="formatCurrency(stats.total_value)"
            icon="cube"
            color="purple"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import StatCard from './StatCard.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_transactions: 0,
            draft_count: 0,
            posted_count: 0,
            cancelled_count: 0,
            total_quantity: 0,
            total_value: 0,
        }),
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};
</script>
