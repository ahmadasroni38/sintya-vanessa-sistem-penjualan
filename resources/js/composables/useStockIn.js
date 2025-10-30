import { ref } from "vue";
import api from "@/utils/api";

export function useStockIn() {
    const stockIns = ref([]);
    const stockIn = ref(null);
    const loading = ref(false);
    const error = ref(null);
    const pagination = ref({
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1,
    });
    const statistics = ref({
        total_transactions: 0,
        draft_count: 0,
        posted_count: 0,
        cancelled_count: 0,
        total_quantity: 0,
        total_value: 0,
    });
    const options = ref({
        products: [],
        locations: [],
    });

    /**
     * Fetch stock in list with filters and pagination
     */
    const fetchStockIns = async (params = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get("/stock-in", { params });

            stockIns.value = response.data;
            pagination.value = {
                current_page: response.current_page,
                per_page: response.per_page,
                total: response.total,
                last_page: response.last_page,
                from: response.from,
                to: response.to,
            };
            console.log(stockIns.value);

            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to fetch stock in data";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch single stock in by ID
     */
    const fetchStockIn = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            // api.get sudah return response.data langsung
            const data = await api.get(`/stock-in/${id}`);

            console.log('useStockIn.fetchStockIn - raw data:', data);

            // Handle jika response wrapped atau langsung
            const stockInData = data.data ? data.data : data;

            console.log('useStockIn.fetchStockIn - processed data:', stockInData);

            stockIn.value = stockInData;
            return stockInData;
        } catch (err) {
            console.error('useStockIn.fetchStockIn - error:', err);
            error.value =
                err.response?.data?.message || "Failed to fetch stock in";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Create new stock in
     */
    const createStockIn = async (data) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post("/stock-in", data);
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to create stock in";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Update existing stock in
     */
    const updateStockIn = async (id, data) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.put(`/stock-in/${id}`, data);
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to update stock in";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Delete stock in
     */
    const deleteStockIn = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.delete(`/stock-in/${id}`);
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to delete stock in";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Post stock in (change status from draft to posted)
     */
    const postStockIn = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/stock-in/${id}/post`);
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to post stock in";
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Cancel stock in
     */
    const cancelStockIn = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/stock-in/${id}/cancel`);
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to cancel stock in";
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
            const response = await api.get("/stock-in/statistics", { params });
            statistics.value = response.data;
            return response.data;
        } catch (err) {
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
    const fetchOptions = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get("/stock-in/options");
            options.value = response.data;
            return response.data;
        } catch (err) {
            error.value =
                err.response?.data?.message || "Failed to fetch options";
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
            draft: "bg-gray-100 text-gray-800",
            posted: "bg-green-100 text-green-800",
            cancelled: "bg-red-100 text-red-800",
        };
        return classes[status] || "bg-gray-100 text-gray-800";
    };

    /**
     * Get status label
     */
    const getStatusLabel = (status) => {
        const labels = {
            draft: "Draft",
            posted: "Posted",
            cancelled: "Cancelled",
        };
        return labels[status] || status;
    };

    /**
     * Format currency
     */
    const formatCurrency = (value) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value || 0);
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
        stockIns,
        stockIn,
        loading,
        error,
        pagination,
        statistics,
        options,

        // Methods
        fetchStockIns,
        fetchStockIn,
        createStockIn,
        updateStockIn,
        deleteStockIn,
        postStockIn,
        cancelStockIn,
        fetchStatistics,
        fetchOptions,

        // Helpers
        getStatusClass,
        getStatusLabel,
        formatCurrency,
        formatNumber,
        formatDate,
        formatDateTime,
    };
}
