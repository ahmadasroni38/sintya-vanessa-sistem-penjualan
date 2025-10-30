import { ref } from "vue";
import api from "@/utils/api";

export function useStockMutation() {
    const stockMutations = ref([]);
    const stockMutation = ref(null);
    const loading = ref(false);
    const error = ref(null);

    // Cache for frequently viewed mutations
    const mutationCache = ref(new Map());
    const CACHE_TTL = 5 * 60 * 1000; // 5 minutes

    /**
     * Get cached mutation if still valid
     */
    const getCachedMutation = (id) => {
        const cached = mutationCache.value.get(id);
        if (cached && Date.now() - cached.timestamp < CACHE_TTL) {
            return cached.data;
        }
        // Remove expired cache entry
        if (cached) {
            mutationCache.value.delete(id);
        }
        return null;
    };

    /**
     * Cache mutation data
     */
    const setCachedMutation = (id, data) => {
        mutationCache.value.set(id, {
            data: data,
            timestamp: Date.now(),
        });

        // Limit cache size to prevent memory leaks (keep only 20 most recent)
        if (mutationCache.value.size > 20) {
            const firstKey = mutationCache.value.keys().next().value;
            mutationCache.value.delete(firstKey);
        }
    };

    /**
     * Clear cache for specific mutation (useful when data is updated)
     */
    const clearMutationCache = (id) => {
        mutationCache.value.delete(id);
    };
    const pagination = ref({
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1,
    });
    const statistics = ref({
        total_this_month: 0,
        total_transactions: 0,
        draft_count: 0,
        pending_count: 0,
        approved_count: 0,
        completed_count: 0,
        cancelled_count: 0,
        total_items: 0,
    });
    const options = ref({
        products: [],
        locations: [],
    });

    /**
     * Fetch stock mutation list with filters and pagination
     */
    const fetchStockMutations = async (params = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get("/stock-mutations", { params });

            // Handle response data properly
            const responseData = response;

            // Check if data is paginated
            if (responseData.data && Array.isArray(responseData.data)) {
                stockMutations.value = responseData.data;
                pagination.value = {
                    current_page: responseData.current_page || 1,
                    per_page: responseData.per_page || 15,
                    total: responseData.total || 0,
                    last_page: responseData.last_page || 1,
                    from: responseData.from || 0,
                    to: responseData.to || 0,
                };
            } else if (Array.isArray(responseData)) {
                // Direct array response
                stockMutations.value = responseData;
                pagination.value = {
                    current_page: 1,
                    per_page: responseData.length,
                    total: responseData.length,
                    last_page: 1,
                    from: 1,
                    to: responseData.length,
                };
            } else {
                // Fallback
                stockMutations.value = [];
                pagination.value = {
                    current_page: 1,
                    per_page: 15,
                    total: 0,
                    last_page: 1,
                    from: 0,
                    to: 0,
                };
            }

            console.log("Stock mutations loaded:", stockMutations.value);
            return responseData;
        } catch (err) {
            console.error("Error fetching stock mutations:", err);
            error.value =
                err.response?.data?.message ||
                "Failed to fetch stock mutation data";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch single stock mutation by ID
     */
    const fetchStockMutation = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            // Check cache first
            const cachedData = getCachedMutation(id);
            if (cachedData) {
                stockMutation.value = cachedData;
                return cachedData;
            }

            const response = await api.get(`/stock-mutations/${id}`);

            // Handle response data - Laravel returns data directly
            let mutationData;
            if (
                response.data &&
                typeof response.data === "object" &&
                !Array.isArray(response.data)
            ) {
                // If response.data is an object (not paginated array), use it directly
                mutationData = response.data;
            } else if (response.data && response.data.data) {
                // Fallback for nested data structure
                mutationData = response.data.data;
            } else {
                // Fallback to entire response
                mutationData = response.data || response;
            }

            // Validate that we have valid data
            if (!mutationData || typeof mutationData !== "object") {
                throw new Error("Invalid response data structure");
            }

            // Cache the result
            setCachedMutation(id, mutationData);

            stockMutation.value = mutationData;
            return mutationData;
        } catch (err) {
            console.error("Error in fetchStockMutation:", err);
            error.value =
                err.response?.data?.message ||
                err.message ||
                "Failed to fetch stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Create new stock mutation
     */
    const createStockMutation = async (data) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post("/stock-mutations", data);
            return response.data.data || response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Failed to create stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Update existing stock mutation
     */
    const updateStockMutation = async (id, data) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.put(`/stock-mutations/${id}`, data);
            // Clear cache for this mutation since it was updated
            clearMutationCache(id);
            return response.data.data || response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Failed to update stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Delete stock mutation
     */
    const deleteStockMutation = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.delete(`/stock-mutations/${id}`);
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Failed to delete stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Submit stock mutation for approval (draft -> pending)
     */
    const submitStockMutation = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/stock-mutations/${id}/submit`);
            return response.data.data || response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Failed to submit stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Approve stock mutation (pending -> approved)
     */
    const approveStockMutation = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/stock-mutations/${id}/approve`);
            return response.data.data || response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Failed to approve stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Complete stock mutation (approved -> completed)
     */
    const completeStockMutation = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/stock-mutations/${id}/complete`);
            return response.data.data || response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Failed to complete stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Cancel stock mutation
     */
    const cancelStockMutation = async (id, reason = null) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/stock-mutations/${id}/cancel`, {
                reason,
            });
            return response.data.data || response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Failed to cancel stock mutation";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Check available stock for a product at a location
     */
    const checkStock = async (productId, locationId) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get("/stock-mutations/check-stock", {
                params: { product_id: productId, location_id: locationId },
            });
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to check stock";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch statistics
     */
    const fetchStatistics = async (params = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get("/stock-mutations/statistics", {
                params,
            });
            console.log("Statistics response:", response);
            statistics.value = response;
            return response;
        } catch (err) {
            console.error("Error fetching statistics:", err);
            error.value =
                err.response?.data?.message || "Failed to fetch statistics";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch dropdown options (products, locations)
     */
    const fetchOptions = async (params = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get("/stock-mutations/options", {
                params,
            });
            options.value.locations = response.locations;
            options.value.products = response.products;

            return response;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to fetch options";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch locations for dropdown
     */
    const fetchLocations = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get("/warehouse/locations/active");
            // Ensure options.value exists and has locations property
            if (!options.value) {
                options.value = { products: [], locations: [] };
            }
            options.value.locations = response.data;
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to fetch locations";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Get status badge class
     */
    const getStatusClass = (status) => {
        const classes = {
            draft: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
            pending:
                "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
            approved:
                "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
            completed:
                "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
            cancelled:
                "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        };
        return (
            classes[status] ||
            "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
        );
    };

    /**
     * Get status label
     */
    const getStatusLabel = (status) => {
        const labels = {
            draft: "Draft",
            pending: "Menunggu Persetujuan",
            approved: "Disetujui",
            completed: "Selesai",
            cancelled: "Dibatalkan",
        };
        return labels[status] || status;
    };

    /**
     * Format number
     */
    const formatNumber = (value) => {
        return new Intl.NumberFormat("id-ID", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        }).format(value || 0);
    };

    /**
     * Format date
     */
    const formatDate = (date) => {
        if (!date) return "-";
        return new Date(date).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    };

    /**
     * Format datetime
     */
    const formatDateTime = (date) => {
        if (!date) return "-";
        return new Date(date).toLocaleString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    return {
        // State
        stockMutations,
        stockMutation,
        loading,
        error,
        pagination,
        statistics,
        options,

        // Methods
        fetchStockMutations,
        fetchStockMutation,
        createStockMutation,
        updateStockMutation,
        deleteStockMutation,
        submitStockMutation,
        approveStockMutation,
        completeStockMutation,
        cancelStockMutation,
        checkStock,
        fetchStatistics,
        fetchOptions,
        fetchLocations,

        // Cache management
        clearMutationCache,

        // Helpers
        getStatusClass,
        getStatusLabel,
        formatNumber,
        formatDate,
        formatDateTime,
    };
}
