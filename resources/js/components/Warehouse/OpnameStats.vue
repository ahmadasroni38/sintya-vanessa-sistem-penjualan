<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <StatCard
            title="Total This Month"
            :value="stats.total_this_month"
            icon="cube"
            color="blue"
            :loading="loading"
        />
        <StatCard
            title="Draft"
            :value="stats.draft"
            icon="file-text"
            color="gray"
            :loading="loading"
        />
        <StatCard
            title="In Progress"
            :value="stats.in_progress"
            icon="clock"
            color="yellow"
            :loading="loading"
        />
        <StatCard
            title="Completed"
            :value="stats.completed"
            icon="check-circle"
            color="green"
            :loading="loading"
        />
        <StatCard
            title="Items Counted"
            :value="stats.items_counted"
            icon="package"
            color="purple"
            :loading="loading"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import StatCard from "./StatCard.vue";
import { stockOpnameService } from "../../services/warehouseService";

const stats = ref({
    total_this_month: 0,
    draft: 0,
    in_progress: 0,
    completed: 0,
    items_counted: 0,
});

const loading = ref(false);

const fetchStatistics = async () => {
    loading.value = true;
    try {
        const response = await stockOpnameService.getStatistics();
        stats.value = response;
    } catch (error) {
        console.error("Error fetching statistics:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchStatistics();
});

// Expose fetchStatistics so parent can refresh stats
defineExpose({
    refresh: fetchStatistics,
});
</script>
