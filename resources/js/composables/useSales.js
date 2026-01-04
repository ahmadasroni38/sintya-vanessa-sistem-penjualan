import { ref } from "vue";
import api from "@/utils/api";
import { useNotificationStore } from "@/stores/notification";

export function useSales() {
    const notificationStore = useNotificationStore();

    const sales = ref([]);
    const sale = ref(null);
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
        status: "",
        location_id: "",
        customer_id: "",
        product_id: "",
        start_date: "",
        end_date: "",
        sort_by: "transaction_date",
        sort_order: "desc",
    });

    /**
     * Fetch sales list with filters and pagination
     */
    const fetchSales = async (page = 1) => {
        if (loading.value) return;

        loading.value = true;

        try {
            const params = {
                page,
                per_page: pagination.value.per_page,
                ...filters.value,
            };

            const response = await api.get("/sales", { params });

            // Laravel pagination response structure
            if (response.data) {
                sales.value = response.data || [];
                pagination.value = {
                    current_page: response.current_page || 1,
                    per_page: response.per_page || 15,
                    total: response.total || 0,
                    last_page: response.last_page || 1,
                };
            } else {
                // Fallback
                sales.value = [];
                pagination.value = {
                    current_page: 1,
                    per_page: 15,
                    total: 0,
                    last_page: 1,
                };
            }

            return response;
        } catch (err) {
            console.error("Error fetching sales:", err);
            notificationStore.error(
                "Failed to fetch sales data",
                err.response?.data?.message || "An error occurred while loading sales"
            );

            // Set empty data on error
            sales.value = [];
            pagination.value = {
                current_page: 1,
                per_page: 15,
                total: 0,
                last_page: 1,
            };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch single sale by ID
     */
    const fetchSale = async (id) => {
        loading.value = true;

        try {
            const response = await api.get(`/sales/${id}`);
            sale.value = response.data;
            return response.data;
        } catch (err) {
            console.error("Error fetching sale:", err);
            notificationStore.error(
                "Failed to fetch sale",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Create new sale
     */
    const createSale = async (data) => {
        saving.value = true;

        try {
            const response = await api.post("/sales", data);
            notificationStore.success("Sale created successfully");
            return response.data;
        } catch (err) {
            console.error("Error creating sale:", err);
            notificationStore.error(
                "Failed to create sale",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            saving.value = false;
        }
    };

    /**
     * Update existing sale
     */
    const updateSale = async (id, data) => {
        saving.value = true;

        try {
            const response = await api.put(`/sales/${id}`, data);
            notificationStore.success("Sale updated successfully");
            return response.data;
        } catch (err) {
            console.error("Error updating sale:", err);
            notificationStore.error(
                "Failed to update sale",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            saving.value = false;
        }
    };

    /**
     * Delete sale
     */
    const deleteSale = async (id) => {
        deleting.value = true;

        try {
            const response = await api.delete(`/sales/${id}`);
            notificationStore.success("Sale deleted successfully");
            return response.data;
        } catch (err) {
            console.error("Error deleting sale:", err);
            notificationStore.error(
                "Failed to delete sale",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            deleting.value = false;
        }
    };

    /**
     * Post sale (change status from draft to posted)
     */
    const postSale = async (id) => {
        saving.value = true;

        try {
            const response = await api.post(`/sales/${id}/post`);
            notificationStore.success("Sale posted successfully");
            return response.data;
        } catch (err) {
            console.error("Error posting sale:", err);
            notificationStore.error(
                "Failed to post sale",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            saving.value = false;
        }
    };

    /**
     * Cancel sale
     */
    const cancelSale = async (id) => {
        saving.value = true;

        try {
            const response = await api.post(`/sales/${id}/cancel`);
            notificationStore.success("Sale cancelled successfully");
            return response.data;
        } catch (err) {
            console.error("Error cancelling sale:", err);
            notificationStore.error(
                "Failed to cancel sale",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            saving.value = false;
        }
    };

    /**
     * Fetch statistics
     */
    const fetchSaleStatistics = async () => {
        try {
            const params = {};

            // Only add date filters if they have values
            if (filters.value.start_date) {
                params.start_date = filters.value.start_date;
            }
            if (filters.value.end_date) {
                params.end_date = filters.value.end_date;
            }

            const response = await api.get("/sales/statistics", { params });

            // Handle both response.data and response.data.data patterns
            return response.data || response;
        } catch (err) {
            console.error("Error fetching statistics:", err);
            // Don't show notification for statistics error, just log it
            // Return default values instead of throwing
            return {
                total_transactions: 0,
                draft_count: 0,
                posted_count: 0,
                cancelled_count: 0,
                total_items: 0,
                total_revenue: 0,
                total_customers: 0,
            };
        }
    };

    /**
     * Fetch dropdown options (products, locations, customers)
     */
    const fetchOptions = async (locationId = null) => {
        try {
            const params = {};
            if (locationId) {
                params.location_id = locationId;
            }

            const response = await api.get("/sales/options", { params });

            // Handle both response.data and response.data.data patterns
            const data = response.data || response;

            return {
                products: data.products || [],
                locations: data.locations || [],
                customers: data.customers || [],
            };
        } catch (err) {
            console.error("Error fetching options:", err);
            // Don't show notification for options error, just log it
            // Return empty arrays instead of throwing
            return {
                products: [],
                locations: [],
                customers: [],
            };
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
     * Get payment method label
     */
    const getPaymentMethodLabel = (method) => {
        const labels = {
            cash: "Cash",
            transfer: "Bank Transfer",
            credit: "Credit",
        };
        return labels[method] || method;
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

    /**
     * Calculate change amount
     */
    const calculateChange = (total, paid) => {
        return Math.max(0, (paid || 0) - (total || 0));
    };

    /**
     * Bulk post sales
     */
    const bulkPostSales = async (ids) => {
        saving.value = true;

        try {
            const response = await api.post("/sales/bulk-post", { ids });
            notificationStore.success(`${response.data.posted} sale(s) posted successfully`);
            return response.data;
        } catch (err) {
            console.error("Error bulk posting sales:", err);
            notificationStore.error(
                "Failed to bulk post sales",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            saving.value = false;
        }
    };

    /**
     * Bulk delete sales
     */
    const bulkDeleteSales = async (ids) => {
        deleting.value = true;

        try {
            const response = await api.post("/sales/bulk-delete", { ids });
            notificationStore.success(`${response.data.deleted} sale(s) deleted successfully`);
            return response.data;
        } catch (err) {
            console.error("Error bulk deleting sales:", err);
            notificationStore.error(
                "Failed to bulk delete sales",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        } finally {
            deleting.value = false;
        }
    };

    /**
     * Export sales to CSV
     */
    const exportSales = async () => {
        try {
            const params = { ...filters.value };
            const response = await api.get("/sales/export", {
                params,
                responseType: "blob",
            });

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute(
                "download",
                `sales_export_${new Date().toISOString().split("T")[0]}.csv`
            );
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);

            notificationStore.success("Sales exported successfully");
            return true;
        } catch (err) {
            console.error("Error exporting sales:", err);
            notificationStore.error(
                "Failed to export sales",
                err.response?.data?.message || "An error occurred"
            );
            throw err;
        }
    };

    // Filter and pagination helpers
    const applyFilters = () => {
        fetchSales(1);
    };

    const resetFilters = () => {
        filters.value = {
            search: "",
            status: "",
            location_id: "",
            customer_id: "",
            product_id: "",
            start_date: "",
            end_date: "",
            sort_by: "transaction_date",
            sort_order: "desc",
        };
        fetchSales(1);
    };

    const changePage = (page) => {
        fetchSales(page);
    };

    return {
        // State
        sales,
        sale,
        loading,
        saving,
        deleting,
        pagination,
        filters,

        // Methods
        fetchSales,
        fetchSale,
        createSale,
        updateSale,
        deleteSale,
        postSale,
        cancelSale,
        fetchSaleStatistics,
        fetchOptions,
        bulkPostSales,
        bulkDeleteSales,
        exportSales,
        applyFilters,
        resetFilters,
        changePage,

        // Helpers
        getStatusClass,
        getStatusLabel,
        getPaymentMethodLabel,
        formatCurrency,
        formatNumber,
        formatDate,
        formatDateTime,
        calculateChange,
    };
}
