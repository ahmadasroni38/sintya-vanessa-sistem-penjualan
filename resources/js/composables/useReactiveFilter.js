import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { debounce } from "lodash-es";
import chartOfAccountService from "@/services/chartOfAccountService";

/**
 * Reactive Filter Composable for Chart of Accounts
 *
 * Provides reactive filtering functionality with debouncing and automatic API calls.
 * Integrates with the chartOfAccountService for data fetching.
 */
export function useReactiveFilter(initialFilters = {}, autoFetch = true) {
    // Reactive filters object
    const filters = ref({
        search: "",
        type: "",
        status: "",
        page: 1,
        per_page: 15,
        ...initialFilters,
    });

    // Loading state
    const loading = ref(false);

    // Data state
    const data = ref(null);
    const error = ref(null);

    // Debounced fetch function
    const debouncedFetch = debounce(async (fetchParams = {}) => {
        await fetchData(fetchParams);
    }, 300);

    /**
     * Fetch data based on current filters
     *
     * @param {Object} additionalParams - Additional parameters for API call
     */
    const fetchData = async (additionalParams = {}) => {
        try {
            loading.value = true;
            error.value = null;

            const params = {
                ...filters.value,
                ...additionalParams,
            };

            // Convert status to is_active format for API
            if (params.status !== "") {
                params.is_active = params.status === "active" ? "1" : "0";
                delete params.status;
            }

            const response = await chartOfAccountService.getAccounts(params);
            data.value = response;

            return response;
        } catch (err) {
            error.value = err.message || "Failed to fetch data";
            console.error("Filter fetch error:", err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Apply filters manually (triggers immediate fetch)
     */
    const applyFilters = async () => {
        filters.value.page = 1; // Reset to first page when applying filters
        await fetchData();
    };

    /**
     * Clear all filters and reset to initial state
     */
    const clearFilters = () => {
        filters.value = {
            search: "",
            type: "",
            status: "",
            page: 1,
            per_page: 15,
            ...initialFilters,
        };

        // Trigger fetch after clearing
        if (autoFetch) {
            fetchData();
        }
    };

    /**
     * Update a specific filter value
     *
     * @param {string} filterName - Name of the filter to update
     * @param {any} value - New value for the filter
     */
    const updateFilter = (filterName, value) => {
        filters.value[filterName] = value;

        // Reset page when filters change (except pagination)
        if (filterName !== "page" && filterName !== "per_page") {
            filters.value.page = 1;
        }
    };

    /**
     * Update multiple filters at once
     *
     * @param {Object} newFilters - Object containing filter updates
     */
    const updateFilters = (newFilters) => {
        Object.assign(filters.value, newFilters);

        // Reset page if not pagination update
        if (!newFilters.page && !newFilters.per_page) {
            filters.value.page = 1;
        }
    };

    /**
     * Go to specific page
     *
     * @param {number} page - Page number to go to
     */
    const goToPage = async (page) => {
        if (page < 1) return;

        filters.value.page = page;
        await fetchData();
    };

    /**
     * Change items per page
     *
     * @param {number} perPage - Number of items per page
     */
    const changePerPage = async (perPage) => {
        filters.value.per_page = perPage;
        filters.value.page = 1; // Reset to first page
        await fetchData();
    };

    /**
     * Search with immediate effect (no debounce)
     *
     * @param {string} searchTerm - Search term
     */
    const search = async (searchTerm) => {
        filters.value.search = searchTerm;
        filters.value.page = 1;
        await fetchData();
    };

    /**
     * Filter by account type
     *
     * @param {string} accountType - Account type to filter by
     */
    const filterByType = async (accountType) => {
        filters.value.type = accountType;
        filters.value.page = 1;
        await fetchData();
    };

    /**
     * Filter by status
     *
     * @param {string} status - Status to filter by ('active', 'inactive', or '')
     */
    const filterByStatus = async (status) => {
        filters.value.status = status;
        filters.value.page = 1;
        await fetchData();
    };

    /**
     * Get current filters as plain object
     *
     * @returns {Object} Current filters
     */
    const getFilters = () => {
        return { ...filters.value };
    };

    /**
     * Reset filters to specific values
     *
     * @param {Object} resetValues - Values to reset to
     */
    const resetFilters = (resetValues = {}) => {
        filters.value = {
            search: "",
            type: "",
            status: "",
            page: 1,
            per_page: 15,
            ...resetValues,
        };

        if (autoFetch) {
            fetchData();
        }
    };

    // Computed properties
    const hasActiveFilters = computed(() => {
        return (
            filters.value.search !== "" ||
            filters.value.type !== "" ||
            filters.value.status !== ""
        );
    });

    const hasSearchFilter = computed(() => {
        return filters.value.search !== "";
    });

    const hasTypeFilter = computed(() => {
        return filters.value.type !== "";
    });

    const hasStatusFilter = computed(() => {
        return filters.value.status !== "";
    });

    const currentPage = computed(() => {
        return filters.value.page;
    });

    const currentPerPage = computed(() => {
        return filters.value.per_page;
    });

    const currentSearch = computed(() => {
        return filters.value.search;
    });

    const currentType = computed(() => {
        return filters.value.type;
    });

    const currentStatus = computed(() => {
        return filters.value.status;
    });

    const paginationData = computed(() => {
        return data.value?.data || null;
    });

    const totalItems = computed(() => {
        return data.value?.data?.total || 0;
    });

    const totalPages = computed(() => {
        return data.value?.data?.last_page || 1;
    });

    const fromItem = computed(() => {
        return data.value?.data?.from || 0;
    });

    const toItem = computed(() => {
        return data.value?.data?.to || 0;
    });

    // Watch for filter changes and trigger debounced fetch
    if (autoFetch) {
        watch(
            () => ({
                search: filters.value.search,
                type: filters.value.type,
                status: filters.value.status,
                per_page: filters.value.per_page,
            }),
            (newFilters, oldFilters) => {
                // Only debounce for search, type, and status changes
                // Page changes are handled directly by goToPage method
                debouncedFetch();
            },
            { deep: true }
        );
    }

    // Initial data fetch
    onMounted(() => {
        if (autoFetch) {
            fetchData();
        }
    });

    // Cleanup debounce on unmount
    onUnmounted(() => {
        debouncedFetch.cancel();
    });

    // Return reactive object with all necessary data and methods
    return {
        // Filter state
        filters,
        loading,
        data,
        error,

        // Computed properties
        hasActiveFilters,
        hasSearchFilter,
        hasTypeFilter,
        hasStatusFilter,
        currentPage,
        currentPerPage,
        currentSearch,
        currentType,
        currentStatus,
        paginationData,
        totalItems,
        totalPages,
        fromItem,
        toItem,

        // Methods
        fetchData,
        applyFilters,
        clearFilters,
        updateFilter,
        updateFilters,
        goToPage,
        changePerPage,
        search,
        filterByType,
        filterByStatus,
        getFilters,
        resetFilters,

        // Debounced function (for external use if needed)
        debouncedFetch,
    };
}

/**
 * Create a simple filter instance without auto-fetch
 * Useful for forms where you want to control when to fetch data
 */
export function useSimpleFilter(initialFilters = {}) {
    return useReactiveFilter(initialFilters, false);
}

/**
 * Create a filter instance with custom debounce time
 *
 * @param {number} debounceTime - Debounce time in milliseconds
 * @param {Object} initialFilters - Initial filter values
 */
export function useCustomDebounceFilter(
    debounceTime = 300,
    initialFilters = {}
) {
    const filters = ref({
        search: "",
        type: "",
        status: "",
        page: 1,
        per_page: 15,
        ...initialFilters,
    });

    const loading = ref(false);
    const data = ref(null);
    const error = ref(null);

    const customDebouncedFetch = debounce(async (fetchParams = {}) => {
        try {
            loading.value = true;
            error.value = null;

            const params = {
                ...filters.value,
                ...fetchParams,
            };

            if (params.status !== "") {
                params.is_active = params.status === "active" ? "1" : "0";
                delete params.status;
            }

            const response = await chartOfAccountService.getAccounts(params);
            data.value = response;

            return response;
        } catch (err) {
            error.value = err.message || "Failed to fetch data";
            console.error("Filter fetch error:", err);
            throw err;
        } finally {
            loading.value = false;
        }
    }, debounceTime);

    const hasActiveFilters = computed(() => {
        return (
            filters.value.search !== "" ||
            filters.value.type !== "" ||
            filters.value.status !== ""
        );
    });

    const applyFilters = async () => {
        filters.value.page = 1;
        await customDebouncedFetch();
    };

    const clearFilters = () => {
        filters.value = {
            search: "",
            type: "",
            status: "",
            page: 1,
            per_page: 15,
            ...initialFilters,
        };
        customDebouncedFetch();
    };

    onUnmounted(() => {
        customDebouncedFetch.cancel();
    });

    return {
        filters,
        loading,
        data,
        error,
        hasActiveFilters,
        applyFilters,
        clearFilters,
        customDebouncedFetch,
    };
}
