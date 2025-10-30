<template>
    <div class="flex items-center justify-end gap-2">
        <!-- View Detail Button - Available for all statuses -->
        <ActionButton
            icon="eye"
            color="blue"
            title="Lihat Detail Mutasi"
            tooltip-position="top"
            @click="$emit('view', item)"
        />

        <!-- Edit Button - Only for draft -->
        <ActionButton
            v-if="item.status === 'draft'"
            icon="pencil"
            color="blue"
            title="Edit Mutasi"
            tooltip-position="top"
            @click="$emit('edit', item)"
        />

        <!-- Submit Button - Only for draft -->
        <ActionButton
            v-if="item.status === 'draft'"
            icon="check"
            color="green"
            title="Ajukan untuk Persetujuan"
            tooltip-position="top"
            @click="$emit('submit', item)"
        />

        <!-- Approve Button - Only for pending (with permission check if needed) -->
        <ActionButton
            v-if="item.status === 'pending'"
            icon="check-circle"
            color="green"
            title="Setujui Mutasi"
            tooltip-position="top"
            @click="$emit('approve', item)"
        />

        <!-- Complete Button - Only for approved -->
        <ActionButton
            v-if="item.status === 'approved'"
            icon="check-badge"
            color="blue"
            title="Selesaikan Mutasi"
            tooltip-position="top"
            @click="$emit('complete', item)"
        />

        <!-- Delete Button - Only for draft -->
        <ActionButton
            v-if="item.status === 'draft'"
            icon="trash"
            color="red"
            title="Hapus Mutasi"
            tooltip-position="top"
            @click="$emit('delete', item)"
        />

        <!-- Cancel Button - For draft, pending, and approved -->
        <ActionButton
            v-if="['draft', 'pending', 'approved'].includes(item.status)"
            icon="x-mark"
            color="red"
            title="Batalkan Mutasi"
            tooltip-position="top"
            @click="$emit('cancel', item)"
        />
    </div>
</template>

<script setup>
import ActionButton from "./ActionButton.vue";

defineProps({
    item: {
        type: Object,
        required: true,
    },
});

defineEmits([
    "view",
    "edit",
    "submit",
    "approve",
    "complete",
    "delete",
    "cancel",
]);
</script>
