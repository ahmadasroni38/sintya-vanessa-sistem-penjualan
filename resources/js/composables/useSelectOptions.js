import { ref, computed } from "vue";
import api from "../utils/api";
import { useNotificationStore } from "../stores/notification";

/**
 * Composable for managing select options from backend
 *
 * Provides centralized state management for all select dropdowns
 * with caching, auto-refresh, and error handling.
 *
 * @example
 * const { unitOptions, loadUnits, addUnit } = useSelectOptions();
 * await loadUnits(); // Load units from API
 * const newUnit = await addUnit({ name: "Dozen" }); // Add new unit
 */

// Shared state across all component instances (singleton pattern)
const units = ref([]);
const categories = ref([]);
const locations = ref([]);
const productTypes = ref([
    { value: "finished_goods", label: "Finished Goods" },
    { value: "raw_material", label: "Raw Material" },
    { value: "consumable", label: "Consumable" },
]);

// Loading states
const loadingUnits = ref(false);
const loadingCategories = ref(false);
const loadingLocations = ref(false);

// Cache timestamps
const unitsCacheTime = ref(null);
const categoriesCacheTime = ref(null);
const locationsCacheTime = ref(null);

// Cache duration in milliseconds (5 minutes)
const CACHE_DURATION = 5 * 60 * 1000;

export function useSelectOptions() {
    const notificationStore = useNotificationStore();

    /**
     * Check if cache is still valid
     */
    const isCacheValid = (cacheTime) => {
        if (!cacheTime) return false;
        return Date.now() - cacheTime < CACHE_DURATION;
    };

    /**
     * Load units from API
     * @param {boolean} forceRefresh - Force refresh even if cache is valid
     * @returns {Promise<Array>} Array of unit options
     */
    const loadUnits = async (forceRefresh = false) => {
        // Return cached data if valid and not forcing refresh
        if (
            !forceRefresh &&
            isCacheValid(unitsCacheTime.value) &&
            units.value.length > 0
        ) {
            return units.value;
        }

        loadingUnits.value = true;
        try {
            const response = await api.get("/units/active");
            units.value = response.data.map((unit) => ({
                value: unit.id,
                label: unit.symbol
                    ? `${unit.name} (${unit.symbol})`
                    : unit.name,
                name: unit.name,
                code: unit.code,
                symbol: unit.symbol,
            }));
            unitsCacheTime.value = Date.now();
            return units.value;
        } catch (error) {
            console.error("Error loading units:", error);
            notificationStore.addNotification({
                type: "error",
                message:
                    error.response?.data?.message ||
                    "Failed to load units. Please try again.",
            });
            throw error;
        } finally {
            loadingUnits.value = false;
        }
    };

    /**
     * Add new unit via API
     * @param {Object} data - Unit data (name, code, symbol, etc.)
     * @returns {Promise<Object>} Created unit data
     */
    const addUnit = async (data) => {
        try {
            const response = await api.post("/units", data);
            const newUnit = response.data.data;

            // Add to local state
            const unitOption = {
                value: newUnit.id || newUnit.value,
                label:
                    newUnit.label ||
                    (newUnit.symbol
                        ? `${newUnit.name} (${newUnit.symbol})`
                        : newUnit.name),
                name: newUnit.name,
                code: newUnit.code,
                symbol: newUnit.symbol,
            };
            units.value.push(unitOption);

            // Refresh from backend to ensure consistency
            await loadUnits(true);

            notificationStore.addNotification({
                type: "success",
                message: `Unit "${newUnit.name}" has been added successfully.`,
            });

            return newUnit;
        } catch (error) {
            console.error("Error adding unit:", error);
            notificationStore.addNotification({
                type: "error",
                message:
                    error.response?.data?.message ||
                    "Failed to add unit. Please try again.",
            });
            throw error;
        }
    };

    /**
     * Load product categories from API
     * @param {boolean} forceRefresh - Force refresh even if cache is valid
     * @returns {Promise<Array>} Array of category options
     */
    const loadCategories = async (forceRefresh = false) => {
        // Return cached data if valid and not forcing refresh
        if (
            !forceRefresh &&
            isCacheValid(categoriesCacheTime.value) &&
            categories.value.length > 0
        ) {
            return categories.value;
        }

        loadingCategories.value = true;
        try {
            const response = await api.get("/product-categories/active");
            categories.value = response.data.map((category) => ({
                value: category.id,
                label: category.name,
                name: category.name,
                code: category.code,
                parent_id: category.parent_id,
            }));
            categoriesCacheTime.value = Date.now();
            return categories.value;
        } catch (error) {
            console.error("Error loading categories:", error);
            notificationStore.addNotification({
                type: "error",
                message:
                    error.response?.data?.message ||
                    "Failed to load categories. Please try again.",
            });
            throw error;
        } finally {
            loadingCategories.value = false;
        }
    };

    /**
     * Add new category via API
     * @param {Object} data - Category data (name, code, parent_id, etc.)
     * @returns {Promise<Object>} Created category data
     */
    const addCategory = async (data) => {
        try {
            const response = await api.post("/product-categories", data);
            const newCategory = response.data.data;

            // Add to local state
            const categoryOption = {
                value: newCategory.id || newCategory.value,
                label: newCategory.label || newCategory.name,
                name: newCategory.name,
                code: newCategory.code,
                parent_id: newCategory.parent_id,
            };
            categories.value.push(categoryOption);

            // Refresh from backend to ensure consistency
            await loadCategories(true);

            notificationStore.addNotification({
                type: "success",
                message: `Category "${newCategory.name}" has been added successfully.`,
            });

            return newCategory;
        } catch (error) {
            console.error("Error adding category:", error);
            notificationStore.addNotification({
                type: "error",
                message:
                    error.response?.data?.message ||
                    "Failed to add category. Please try again.",
            });
            throw error;
        }
    };

    /**
     * Load locations from API
     * @param {boolean} forceRefresh - Force refresh even if cache is valid
     * @returns {Promise<Array>} Array of location options
     */
    const loadLocations = async (forceRefresh = false) => {
        // Return cached data if valid and not forcing refresh
        if (
            !forceRefresh &&
            isCacheValid(locationsCacheTime.value) &&
            locations.value.length > 0
        ) {
            return locations.value;
        }

        loadingLocations.value = true;
        try {
            const response = await api.get("/locations");
            locations.value = response.data.map((location) => ({
                value: location.id,
                label: location.code
                    ? `${location.code} - ${location.name}`
                    : location.name,
                name: location.name,
                code: location.code,
                type: location.type,
                address: location.address,
            }));
            locationsCacheTime.value = Date.now();
            return locations.value;
        } catch (error) {
            console.error("Error loading locations:", error);
            notificationStore.addNotification({
                type: "error",
                message:
                    error.response?.data?.message ||
                    "Failed to load locations. Please try again.",
            });
            throw error;
        } finally {
            loadingLocations.value = false;
        }
    };

    /**
     * Add new location via API
     * @param {Object} data - Location data (name, code, type, etc.)
     * @returns {Promise<Object>} Created location data
     */
    const addLocation = async (data) => {
        try {
            const response = await api.post("/locations", data);
            const newLocation = response.data.data;

            // Add to local state
            const locationOption = {
                value: newLocation.id || newLocation.value,
                label: newLocation.code
                    ? `${newLocation.code} - ${newLocation.name}`
                    : newLocation.name,
                name: newLocation.name,
                code: newLocation.code,
                type: newLocation.type,
                address: newLocation.address,
            };
            locations.value.push(locationOption);

            // Refresh from backend to ensure consistency
            await loadLocations(true);

            notificationStore.addNotification({
                type: "success",
                message: `Location "${newLocation.name}" has been added successfully.`,
            });

            return newLocation;
        } catch (error) {
            console.error("Error adding location:", error);
            notificationStore.addNotification({
                type: "error",
                message:
                    error.response?.data?.message ||
                    "Failed to add location. Please try again.",
            });
            throw error;
        }
    };

    /**
     * Load all select options at once (parallel loading)
     * @returns {Promise<void>}
     */
    const loadAllOptions = async () => {
        try {
            await Promise.all([loadUnits(), loadCategories(), loadLocations()]);
        } catch (error) {
            console.error("Error loading select options:", error);
        }
    };

    /**
     * Clear all caches and reload from backend
     * @returns {Promise<void>}
     */
    const refreshAllOptions = async () => {
        unitsCacheTime.value = null;
        categoriesCacheTime.value = null;
        locationsCacheTime.value = null;
        await loadAllOptions();
    };

    /**
     * Clear specific cache
     */
    const clearCache = (type) => {
        switch (type) {
            case "units":
                unitsCacheTime.value = null;
                units.value = [];
                break;
            case "categories":
                categoriesCacheTime.value = null;
                categories.value = [];
                break;
            case "locations":
                locationsCacheTime.value = null;
                locations.value = [];
                break;
            case "all":
                unitsCacheTime.value = null;
                categoriesCacheTime.value = null;
                locationsCacheTime.value = null;
                units.value = [];
                categories.value = [];
                locations.value = [];
                break;
        }
    };

    /**
     * Get status options (for is_active fields)
     */
    const statusOptions = computed(() => [
        { value: true, label: "Active" },
        { value: false, label: "Inactive" },
    ]);

    /**
     * Get product type options (enum)
     */
    const productTypeOptions = computed(() => productTypes.value);

    /**
     * Get unit options (readonly computed)
     */
    const unitOptions = computed(() => units.value);

    /**
     * Get category options (readonly computed)
     */
    const categoryOptions = computed(() => categories.value);

    /**
     * Get location options (readonly computed)
     */
    const locationOptions = computed(() => locations.value);

    return {
        // Options (reactive)
        unitOptions,
        categoryOptions,
        locationOptions,
        productTypeOptions,
        statusOptions,

        // Loading states
        loadingUnits,
        loadingCategories,
        loadingLocations,

        // Load methods
        loadUnits,
        loadCategories,
        loadLocations,
        loadAllOptions,

        // Add methods
        addUnit,
        addCategory,
        addLocation,

        // Utility methods
        refreshAllOptions,
        clearCache,

        // Cache info (for debugging)
        isCacheValid: computed(() => ({
            units: isCacheValid(unitsCacheTime.value),
            categories: isCacheValid(categoriesCacheTime.value),
            locations: isCacheValid(locationsCacheTime.value),
        })),
    };
}
