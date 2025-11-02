import { ref } from 'vue';
import api from '@/utils/api';

export function useCustomers() {
    const loading = ref(false);
    const error = ref(null);
    const customers = ref([]);
    const customer = ref(null);
    const stats = ref(null);
    const pagination = ref({
        current_page: 1,
        per_page: 10,
        total: 0,
        last_page: 1
    });

    /**
     * Fetch customers list
     */
    const fetchCustomers = async (params = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/customers', { params });
            customers.value = response.data;
            pagination.value = {
                current_page: response.current_page,
                per_page: response.per_page,
                total: response.total,
                last_page: response.last_page
            };
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error fetching customers';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch active customers for dropdown/select
     */
    const fetchActiveCustomers = async (search = '') => {
        try {
            const response = await api.get('/customers/active', {
                params: { search }
            });
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error fetching active customers';
            throw err;
        }
    };

    /**
     * Fetch single customer
     */
    const fetchCustomer = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get(`/customers/${id}`);
            customer.value = response.data;
            stats.value = response.stats;
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error fetching customer';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Create new customer
     */
    const createCustomer = async (customerData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post('/customers', customerData);
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error creating customer';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Update customer
     */
    const updateCustomer = async (id, customerData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.put(`/customers/${id}`, customerData);
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error updating customer';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Delete customer
     */
    const deleteCustomer = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.delete(`/customers/${id}`);
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error deleting customer';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Toggle customer status
     */
    const toggleCustomerStatus = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/customers/${id}/toggle-status`);
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error toggling customer status';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch customer statistics
     */
    const fetchCustomerStats = async (params = {}) => {
        try {
            const response = await api.get('/customers/statistics', { params });
            stats.value = response;
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error fetching statistics';
            throw err;
        }
    };

    /**
     * Export customers to CSV
     */
    const exportCustomers = async (params = {}) => {
        try {
            // For export, we need to use axios directly for blob response
            const axios = (await import('axios')).default;
            const token = localStorage.getItem('token');

            const response = await axios.get('/api/customers/export', {
                params,
                responseType: 'blob',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `customers_${new Date().getTime()}.csv`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error exporting customers';
            throw err;
        }
    };

    /**
     * Search customers (debounced)
     */
    const searchCustomers = async (searchTerm, params = {}) => {
        return await fetchActiveCustomers(searchTerm);
    };

    return {
        loading,
        error,
        customers,
        customer,
        stats,
        pagination,
        fetchCustomers,
        fetchActiveCustomers,
        fetchCustomer,
        createCustomer,
        updateCustomer,
        deleteCustomer,
        toggleCustomerStatus,
        fetchCustomerStats,
        exportCustomers,
        searchCustomers
    };
}
