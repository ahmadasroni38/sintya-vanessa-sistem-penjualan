import { ref, computed } from "vue";
import api from "../utils/api";
import { useNotificationStore } from "../stores/notification";

export function useLocations() {
    const notificationStore = useNotificationStore();

    // State
    const locations = ref([]);
    const location = ref(null);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const pagination = ref({
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1,
    });

    // Filters
    const filters = ref({
        search: "",
        is_active: "",
        parent_id: "",
        sort_by: "name",
        sort_order: "asc",
    });

    // Fetch locations with filters
    const fetchLocations = async (options = {}) => {
        console.log("=== fetchLocations called ===", new Date().toISOString());

        // Handle both old format (page number) and new format (options object)
        let page = 1;
        let perPage = pagination.value.per_page;

        if (typeof options === 'number') {
            page = options;
        } else {
            page = options.page || 1;
            perPage = options.per_page || pagination.value.per_page;
        }

        console.time("fetchLocations-" + page);

        // Prevent concurrent fetches
        if (loading.value) {
            console.log("fetchLocations blocked - already loading");
            return;
        }

        console.log("fetchLocations starting - setting loading to true");
        loading.value = true;
        try {
            const params = {
                page,
                per_page: perPage,
                ...filters.value,
                ...options, // Allow overriding filters with direct options
            };

            const response = await api.get("/locations", { params });
            locations.value = response.data;
            pagination.value = {
                current_page: response.current_page,
                per_page: perPage, // Force to requested per_page to maintain UI consistency
                total: response.total,
                last_page: response.last_page,
            };
            return response.data;
        } catch (error) {
            console.error("Error fetching locations:", error);
            notificationStore.error(
                "Failed to load locations",
                error.response?.data?.message ||
                    "An error occurred while loading locations"
            );
            throw error;
        } finally {
            console.log("fetchLocations completed - setting loading to false");
            console.timeEnd("fetchLocations-" + page);
            loading.value = false;
        }
    };

    // Fetch single location
    const fetchLocation = async (id) => {
        loading.value = true;
        try {
            const response = await api.get(`/locations/${id}`);
            location.value = response.data.data;
            return response.data;
        } catch (error) {
            console.error("Error fetching location:", error);
            notificationStore.error(
                "Failed to load location",
                error.response?.data?.message ||
                    "An error occurred while loading the location"
            );
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Create location
    const createLocation = async (data) => {
        saving.value = true;
        try {
            const response = await api.post("/locations", data);

            notificationStore.success("Location created successfully");

            return response.data;
        } catch (error) {
            console.error("Error creating location:", error);

            const message =
                error.response?.data?.message || "Failed to create location";
            notificationStore.error("Failed to create location", message);

            throw error;
        } finally {
            saving.value = false;
        }
    };

    // Update location
    const updateLocation = async (id, data) => {
        saving.value = true;
        try {
            const response = await api.put(`/locations/${id}`, data);

            notificationStore.success("Location updated successfully");

            return response.data;
        } catch (error) {
            console.error("Error updating location:", error);

            const message =
                error.response?.data?.message || "Failed to update location";
            notificationStore.error("Failed to update location", message);

            throw error;
        } finally {
            saving.value = false;
        }
    };

    // Delete location
    const deleteLocation = async (id) => {
        deleting.value = true;
        try {
            await api.delete(`/locations/${id}`);

            notificationStore.success("Location deleted successfully");
        } catch (error) {
            console.error("Error deleting location:", error);

            const message =
                error.response?.data?.message || "Failed to delete location";
            notificationStore.error("Failed to delete location", message);

            throw error;
        } finally {
            deleting.value = false;
        }
    };

    // Toggle location status
    const toggleLocationStatus = async (id) => {
        try {
            const response = await api.post(`/locations/${id}/toggle-status`);

            notificationStore.success("Location status updated successfully");

            return response.data;
        } catch (error) {
            console.error("Error toggling location status:", error);
            notificationStore.error("Failed to update location status");
            throw error;
        }
    };

    // Get active locations
    const fetchActiveLocations = async () => {
        try {
            const response = await api.get("/locations/active");
            return response.data.data;
        } catch (error) {
            console.error("Error fetching active locations:", error);
            notificationStore.error("Failed to load active locations");
            throw error;
        }
    };

    // Get location statistics
    const fetchLocationStatistics = async () => {
        try {
            const response = await api.get("/locations/statistics");
            return response.data.data || response.data; // Handle both response formats
        } catch (error) {
            console.error("Error fetching location statistics:", error);
            throw error;
        }
    };

    // Get parent location options
    const fetchParentOptions = async (excludeId = null) => {
        try {
            const url = excludeId
                ? `locations-parent-options/${excludeId}`
                : "locations-parent-options";
            const response = await api.get(url);
            return response.data.data || response.data; // Handle both response formats
        } catch (error) {
            console.error("Error fetching parent options:", error);
            notificationStore.error("Failed to load parent options");
            throw error;
        }
    };

    // Apply filters and refresh
    const applyFilters = (newFilters) => {
        console.log("applyFilters called with:", newFilters);
        filters.value = { ...filters.value, ...newFilters };
        return fetchLocations(1);
    };

    // Reset filters
    const resetFilters = () => {
        console.log("resetFilters called");
        filters.value = {
            search: "",
            is_active: "",
            parent_id: "",
            sort_by: "name",
            sort_order: "asc",
        };
        return fetchLocations(1);
    };

    // Change page
    const changePage = (page, itemsPerPage) => {
        console.log("changePage called with:", page, itemsPerPage);
        if (itemsPerPage?.value) {
            pagination.value.per_page = itemsPerPage?.value;
        }

        return fetchLocations(page);
    };

    // Change sorting
    const changeSort = (sortBy, sortOrder = "asc") => {
        console.log("changeSort called with:", sortBy, sortOrder);
        filters.value.sort_by = sortBy;
        filters.value.sort_order = sortOrder;
        return fetchLocations(1);
    };

    // Computed
    const hasLocations = computed(() => locations.value.length > 0);
    const isEmpty = computed(
        () => !loading.value && locations.value.length === 0
    );

    return {
        // State
        locations,
        location,
        loading,
        saving,
        deleting,
        pagination,
        filters,

        // Computed
        hasLocations,
        isEmpty,

        // Methods
        fetchLocations,
        fetchLocation,
        createLocation,
        updateLocation,
        deleteLocation,
        toggleLocationStatus,
        fetchActiveLocations,
        fetchLocationStatistics,
        fetchParentOptions,
        applyFilters,
        resetFilters,
        changePage,
        changeSort,
    };
}
