import api from "../utils/api";

export const stockBookService = {
    // Get all stock cards with filters
    async getAll(params = {}) {
        const response = await api.get("/stock-book", { params });
        return response.data;
    },

    // Get products with stock
    async getProductsWithStock() {
        const response = await api.get("/stock-book/products");
        console.log(`Response Product`, response);
        return response.data;
    },

    // Get locations with stock
    async getLocationsWithStock() {
        const response = await api.get("/stock-book/locations");
        return response.data;
    },

    // Get statistics
    async getStatistics() {
        const response = await api.get("/stock-book/statistics");
        return response.data;
    },

    // Get ledger data for specific product and location
    async getLedger(params = {}) {
        const response = await api.get("/stock-book/ledger", { params });
        return response.data;
    },

    // Get current balances
    async getCurrentBalances(params = {}) {
        const response = await api.get("/stock-book/current-balances", {
            params,
        });
        return response.data;
    },

    // Get movement summary
    async getMovementSummary(params = {}) {
        const response = await api.get("/stock-book/movement-summary", {
            params,
        });
        return response.data;
    },

    // Export data
    async export(params = {}) {
        const response = await api.get("/stock-book/export", {
            params,
            responseType: "blob",
        });
        return response.data;
    },
};
