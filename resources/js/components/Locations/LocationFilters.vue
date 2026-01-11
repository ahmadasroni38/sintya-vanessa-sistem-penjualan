<template>
    <div
        v-if="show"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
    >
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <FormInput
                v-model="localFilters.search"
                :label="$t('locations.search')"
                :placeholder="$t('locations.searchPlaceholder')"
                @input="handleFilterChange"
            />
            <FormSelect
                v-model="localFilters.is_active"
                :label="$t('locations.status')"
                :options="statusOptions"
                @change="handleFilterChange"
            />
            <FormSelect
                v-model="localFilters.parent_id"
                :label="$t('locations.parentLocation')"
                :options="parentOptions"
                @change="handleFilterChange"
            />
            <div class="flex items-end gap-2">
                <button
                    @click="handleResetFilters"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    {{ $t('locations.resetFilters') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import FormInput from "../Forms/FormInput.vue";
import FormSelect from "../Forms/FormSelect.vue";

const { t } = useI18n();

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    parentOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["update:filters", "reset"]);

const localFilters = ref({
    search: "",
    is_active: "",
    parent_id: "",
    ...props.filters,
});

const statusOptions = computed(() => [
    { value: "", label: t("locations.allStatus") },
    { value: "1", label: t("locations.statusActive") },
    { value: "0", label: t("locations.statusInactive") },
]);

const parentOptions = computed(() => [
    { value: "", label: t("locations.allParents") },
    ...props.parentOptions.map(parent => ({
        value: parent.id,
        label: parent.full_path || parent.name,
    })),
]);

// Watch for external filter changes
watch(
    () => props.filters,
    (newFilters) => {
        // Only update if filters actually changed to prevent unnecessary updates
        const hasChanged = JSON.stringify(localFilters.value) !== JSON.stringify(newFilters);
        if (hasChanged) {
            console.log("LocationFilters: External filters changed, updating localFilters");
            localFilters.value = { ...localFilters.value, ...newFilters };
        }
    },
    { deep: true }
);

const handleFilterChange = () => {
    console.log("LocationFilters: handleFilterChange called with:", localFilters.value);
    emit("update:filters", localFilters.value);
};

const handleResetFilters = () => {
    localFilters.value = {
        search: "",
        is_active: "",
        parent_id: "",
    };
    emit("reset");
};
</script>
