<template>
    <div class="flex items-center justify-end gap-2">
        <ActionButton
            icon="eye"
            color="blue"
            title="Lihat Detail"
            @click="$emit('view', item)"
        />

        <ActionButton
            v-if="item.status === 'draft'"
            icon="pencil"
            color="blue"
            title="Edit"
            @click="$emit('edit', item)"
        />

        <ActionButton
            v-if="item.status === 'draft'"
            icon="check"
            color="green"
            title="Post"
            @click="$emit('post', item)"
        />

        <ActionButton
            v-if="item.status === 'draft'"
            icon="trash"
            color="red"
            title="Delete"
            @click="$emit('delete', item)"
        />

        <ActionButton
            v-if="item.status === 'posted'"
            icon="x-mark"
            color="red"
            title="Cancel"
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

defineEmits(["view", "edit", "post", "delete", "cancel"]);
</script>
