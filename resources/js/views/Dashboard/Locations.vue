<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $t('locations.title') }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $t('locations.subtitle') }}
                </p>
            </div>
        </div>

        <!-- Stats Cards -->
        <LocationStats :stats="stats" />

        <!-- Filters -->
        <LocationFilters
            :show="showFilters"
            :filters="filters"
            :parent-options="parentOptions"
            @update:filters="handleFilterChange"
            @reset="handleResetFilters"
        />

        <!-- Locations Table -->
        <DataTable
            :title="$t('locations.tableTitle')"
            :data="locations"
            :columns="columns"
            :loading="loading"
            :server-side-pagination="true"
            :pagination="pagination"
            :show-refresh="true"
            :refresh-loading="refreshing"
            :show-add-button="false"
            :show-filters="false"
            @add="showAddModal = true"
            @edit="handleEdit"
            @delete="handleDelete"
            @refresh="handleRefresh"
            @page-change="handlePageChange"
            @sort="handleSort"
        >
            <!-- Custom Name Column with Hierarchy -->
            <template #column-name="{ item }">
                <div class="flex items-center">
                    <div
                        class="w-4 h-4 rounded-full mr-3 flex-shrink-0"
                        :style="{ backgroundColor: item.color }"
                    ></div>
                    <div
                        :style="{
                            paddingLeft: `${item.hierarchy_level * 20}px`,
                        }"
                    >
                        <p
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ item.code }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Address Column -->
            <template #column-address="{ item }">
                <div class="max-w-xs">
                    <p class="text-sm text-gray-900 dark:text-white truncate">
                        {{ item.full_address || $t('locations.noAddress') }}
                    </p>
                    <p
                        v-if="item.city || item.country"
                        class="text-xs text-gray-500 dark:text-gray-400"
                    >
                        {{
                            [item.city, item.country].filter(Boolean).join(", ")
                        }}
                    </p>
                </div>
            </template>

            <!-- Custom Parent Column -->
            <template #column-parent="{ item }">
                <div v-if="item.parent" class="flex items-center">
                    <div
                        class="w-3 h-3 rounded-full mr-2"
                        :style="{
                            backgroundColor: item.parent.color || '#10B981',
                        }"
                    ></div>
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ item.parent.name }}
                    </span>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('locations.rootLocation') }}
                </span>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            item.is_active ? 'bg-green-500' : 'bg-red-500',
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                        >{{ item.is_active ? $t('products.statusActive') : $t('products.statusInactive') }}</span
                    >
                </div>
            </template>

            <!-- Custom Children Count Column -->
            <template #column-children="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        item.children_count > 0
                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                    ]"
                >
                    {{ item.children_count }} {{ item.children_count !== 1 ? $t('locations.childrenPlural') : $t('locations.child') }}
                </span>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleEdit(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        :title="$t('locations.editLocation')"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button
                        @click="handleDelete(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        :title="$t('locations.deleteLocation')"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Add/Edit Location Modal -->
        <LocationFormModal
            :is-open="showAddModal || editingLocation"
            :editing-location="editingLocation"
            :parent-options="parentOptions"
            :saving="saving"
            @close="closeModal"
            @saved="handleFormSave"
        />

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :is-open="confirmationModal.isOpen"
            :title="confirmationModal.config.title"
            :message="confirmationModal.config.message"
            :description="confirmationModal.config.description"
            :confirm-text="confirmationModal.config.confirmText"
            :cancel-text="confirmationModal.config.cancelText"
            :loading="confirmationModal.loading || deleting"
            @confirm="confirmationModal.handleConfirm"
            @cancel="confirmationModal.handleCancel"
            @close="confirmationModal.handleClose"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useLocations } from "../../composables/useLocations";
import DataTable from "../../components/UI/DataTable.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import LocationStats from "../../components/Locations/LocationStats.vue";
import LocationFilters from "../../components/Locations/LocationFilters.vue";
import LocationFormModal from "../../components/Locations/LocationFormModal.vue";
import { useNotificationStore } from "../../stores/notification";
import { useConfirmationModalStore } from "../../stores/confirmationModal";

const { t } = useI18n();
const notificationStore = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Use Locations Composable
const {
    locations,
    loading,
    saving,
    deleting,
    pagination,
    filters,
    fetchLocations,
    createLocation,
    updateLocation,
    deleteLocation,
    toggleLocationStatus,
    fetchLocationStatistics,
    fetchParentOptions,
    applyFilters,
    resetFilters,
    changePage,
    changeSort,
} = useLocations();

// Local State
const refreshing = ref(false);
const showAddModal = ref(false);
const editingLocation = ref(null);
const showFilters = ref(false);
const statsLoading = ref(false);
const statistics = ref({
    total: 0,
    active: 0,
    root: 0,
    new_this_month: 0,
});
const parentOptions = ref([]);

// Computed
const stats = computed(() => ({
    total: statistics.value.total || 0,
    active: statistics.value.active || 0,
    root: statistics.value.root || 0,
    new_this_month: statistics.value.new_this_month || 0,
}));

// Table columns
const columns = computed(() => [
    {
        key: "name",
        label: t("locations.location"),
        sortable: true,
    },
    {
        key: "address",
        label: t("locations.address"),
        sortable: false,
    },
    {
        key: "parent",
        label: t("locations.parent"),
        sortable: false,
    },
    {
        key: "children",
        label: t("locations.children"),
        sortable: false,
    },
    {
        key: "status",
        label: t("products.status"),
        sortable: false,
    },
    {
        key: "created_at",
        label: t("locations.created"),
        type: "date",
        sortable: true,
    },
]);

// Methods
const loadStatistics = async () => {
    console.log("loadStatistics called - statistics.loading:", statsLoading.value);

    // Prevent concurrent statistics loading
    if (statsLoading.value) {
        console.log("loadStatistics blocked - already loading");
        return;
    }

    try {
        const data = await fetchLocationStatistics();
        statistics.value = data;
    } catch (error) {
        console.error("Error loading statistics:", error);
    }
};

const loadParentOptions = async () => {
    try {
        const data = await fetchParentOptions();
        parentOptions.value = [
            { value: null, label: t("locations.noParent") },
            ...data.map((location) => ({
                value: location.id,
                label: location.full_path,
            })),
        ];
    } catch (error) {
        console.error("Error loading parent options:", error);
        notificationStore.error("Failed to load parent options");
    }
};

const handleFilterChange = (newFilters) => {
    console.log("handleFilterChange");
    applyFilters(newFilters);
    // Statistics will be updated after the filter is applied
};

const handleResetFilters = () => {
    console.log("handleResetFilters");
    resetFilters();
    // Statistics will be updated after the filters are reset
};

const handleRefresh = async () => {
    console.log("=== handleRefresh STARTED ===", new Date().toISOString());
    console.time("handleRefresh-total");

    refreshing.value = true;
    statsLoading.value = true;
    try {
        console.log("Fetching locations...");
        console.time("fetchLocations-refresh");
        await fetchLocations({
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
        });
        console.timeEnd("fetchLocations-refresh");

        console.log("Fetching statistics...");
        console.time("fetchLocationStatistics-refresh");
        await fetchLocationStatistics().then((data) => {
            statistics.value = data;
        });
        console.timeEnd("fetchLocationStatistics-refresh");

        notificationStore.success(t("locations.refreshSuccess"));
    } catch (error) {
        console.error("Error refreshing locations:", error);
        notificationStore.error(t("locations.refreshError"), error.message);
    } finally {
        refreshing.value = false;
        statsLoading.value = false;
    }

    console.timeEnd("handleRefresh-total");
    console.log("=== handleRefresh COMPLETED ===", new Date().toISOString());
};

const handlePageChange = (page, itemsPerPage) => {
    console.log("handlePageChange", page, itemsPerPage);
    // Update local pagination immediately for UI consistency
    if (itemsPerPage) {
        pagination.value.per_page = itemsPerPage;
    }
    fetchLocations({
        page,
        per_page: itemsPerPage,
    });
};

const handleSort = ({ column, direction }) => {
    fetchLocations({
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
        sort_by: column,
        sort_order: direction,
    });
};

const handleEdit = (location) => {
    editingLocation.value = location;
    showAddModal.value = true;
};

const handleDelete = (location) => {
    confirmationModal.showModal({
        title: t("locations.deleteLocationTitle"),
        message: `${t("locations.deleteLocationMessage")} "${location.name}"?`,
        description: t("locations.deleteLocationDescription"),
        confirmText: t("locations.deleteLocation"),
        cancelText: t("locations.cancelText"),
        onConfirm: async () => {
            if (deleting.value) return;
            deleting.value = true;
            try {
                await deleteLocation(location.id);
                // Refresh both locations list and statistics
                refreshing.value = true;
                await Promise.all([
                    fetchLocations(pagination.value.current_page),
                    loadStatistics()
                ]);
                refreshing.value = false;
            } catch (error) {
                console.error("Error deleting location:", error);
                // Error notification already handled in composable
            } finally {
                deleting.value = false;
            }
        },
        onSuccess: (result) => {
            console.log("Location deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete location:", error);
            notificationStore.error(error.message || t("locations.saveError"));
        },
    });
};

const handleFormSave = async ({ formData, isEditing, locationId }) => {
    try {
        if (isEditing) {
            await updateLocation(locationId, formData);
        } else {
            await createLocation(formData);
        }

        closeModal();
        // Refresh both locations list and statistics
        refreshing.value = true;
        await Promise.all([
            fetchLocations(pagination.value.current_page),
            loadStatistics()
        ]);
        refreshing.value = false;
    } catch (error) {
        console.error("Error saving location:", error);
        // Error handling is done in the modal component
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingLocation.value = null;
};

// Lifecycle
onMounted(async () => {
    console.log("=== Locations.vue onMounted STARTED ===", new Date().toISOString());
    console.time("onMounted-total");

    console.log("Loading parent options...");
    console.time("loadParentOptions");
    await loadParentOptions();
    console.timeEnd("loadParentOptions");

    statsLoading.value = true;
    try {
        console.log("Calling fetchLocations from onMounted...");
        console.time("fetchLocations-initial");
        await fetchLocations();
        console.timeEnd("fetchLocations-initial");

        console.log("Calling fetchLocationStatistics from onMounted...");
        console.time("fetchLocationStatistics-initial");
        await fetchLocationStatistics().then((data) => {
            statistics.value = data;
        });
        console.timeEnd("fetchLocationStatistics-initial");
    } finally {
        statsLoading.value = false;
    }

    console.timeEnd("onMounted-total");
    console.log("=== Locations.vue onMounted COMPLETED ===", new Date().toISOString());
});
</script>
