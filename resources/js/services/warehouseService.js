import api from "../utils/api";

// Products Service
export const productService = {
    getAll: async (params = {}) => {
        const response = await api.get("/products", { params });
        return response.data;
    },

    getById: async (id) => {
        const response = await api.get(`/products/${id}`);
        return response.data;
    },

    create: async (data) => {
        const response = await api.post("/products", data);
        return response.data;
    },

    update: async (id, data) => {
        const response = await api.put(`/products/${id}`, data);
        return response.data;
    },

    delete: async (id) => {
        const response = await api.delete(`/products/${id}`);
        return response.data;
    },

    getStock: async (productId) => {
        const response = await api.get(`/products/${productId}/stock`);
        return response.data;
    },
};

// Stock In Service
export const stockInService = {
    getAll: async (params = {}) => {
        const response = await api.get("/stock-in", { params });
        return response.data;
    },

    getById: async (id) => {
        const response = await api.get(`/stock-in/${id}`);
        return response.data;
    },

    create: async (data) => {
        const response = await api.post("/stock-in", data);
        return response.data;
    },

    update: async (id, data) => {
        const response = await api.put(`/stock-in/${id}`, data);
        return response.data;
    },

    delete: async (id) => {
        const response = await api.delete(`/stock-in/${id}`);
        return response.data;
    },

    approve: async (id) => {
        const response = await api.post(`/stock-in/${id}/approve`);
        return response.data;
    },

    reject: async (id, reason) => {
        const response = await api.post(`/stock-in/${id}/reject`, {
            reason,
        });
        return response.data;
    },
};

// Stock Mutation Service
export const stockMutationService = {
    getAll: async (params = {}) => {
        const response = await api.get("/stock-mutations", { params });
        return response.data.data || response.data;
    },

    getById: async (id) => {
        const response = await api.get(`/stock-mutations/${id}`);
        return response.data.data || response.data;
    },

    create: async (data) => {
        const response = await api.post("/stock-mutations", data);
        return response.data.data || response.data;
    },

    update: async (id, data) => {
        const response = await api.put(`/stock-mutations/${id}`, data);
        return response.data.data || response.data;
    },

    delete: async (id) => {
        const response = await api.delete(`/stock-mutations/${id}`);
        return response.data;
    },

    submit: async (id) => {
        const response = await api.post(`/stock-mutations/${id}/submit`);
        return response.data.data || response.data;
    },

    approve: async (id) => {
        const response = await api.post(`/stock-mutations/${id}/approve`);
        return response.data.data || response.data;
    },

    complete: async (id) => {
        const response = await api.post(`/stock-mutations/${id}/complete`);
        return response.data.data || response.data;
    },

    cancel: async (id, reason) => {
        const response = await api.post(`/stock-mutations/${id}/cancel`, {
            reason,
        });
        return response.data.data || response.data;
    },

    checkStock: async (productId, locationId) => {
        const response = await api.get("/stock-mutations/check-stock", {
            params: { product_id: productId, location_id: locationId },
        });
        return response.data;
    },

    getOptions: async (params = {}) => {
        const response = await api.get("/stock-mutations/options", {
            params,
        });
        return response.data;
    },

    getStatistics: async (params = {}) => {
        const response = await api.get("/stock-mutations/statistics", {
            params,
        });
        return response.data;
    },
};

// Stock Adjustment Service
export const stockAdjustmentService = {
    getAll: async (params = {}) => {
        const response = await api.get("/stock-adjustments", { params });
        return response.data.data || response.data;
    },

    getById: async (id) => {
        const response = await api.get(`/stock-adjustments/${id}`);
        return response.data.data || response.data;
    },

    create: async (data) => {
        const response = await api.post("/stock-adjustments", data);
        return response.data.data || response.data;
    },

    update: async (id, data) => {
        const response = await api.put(`/stock-adjustments/${id}`, data);
        return response.data.data || response.data;
    },

    delete: async (id) => {
        const response = await api.delete(`/stock-adjustments/${id}`);
        return response.data;
    },

    approve: async (id) => {
        const response = await api.post(`/stock-adjustments/${id}/approve`);
        return response.data.data || response.data;
    },

    cancel: async (id, reason) => {
        const response = await api.post(`/stock-adjustments/${id}/cancel`, {
            reason,
        });
        return response.data.data || response.data;
    },

    calculateSystemQuantity: async (productId, locationId) => {
        const response = await api.post(
            "/stock-adjustments/calculate-system-quantity",
            {
                product_id: productId,
                location_id: locationId,
            }
        );
        return response;
    },

    getStatistics: async (params = {}) => {
        const response = await api.get("/stock-adjustments/statistics", {
            params,
        });
        return response.data;
    },

    bulkApprove: async (ids) => {
        const response = await api.post("/stock-adjustments/bulk-approve", {
            ids,
        });
        return response.data;
    },

    bulkDelete: async (ids) => {
        const response = await api.post("/stock-adjustments/bulk-delete", {
            ids,
        });
        return response.data;
    },

    export: async (params = {}) => {
        const response = await api.get("/stock-adjustments/export", {
            params,
            responseType: "blob",
        });
        return response.data;
    },
};

// Stock Opname Service
export const stockOpnameService = {
    getAll: async (params = {}) => {
        const response = await api.get("/stock-opnames", { params });
        return response.data.data || response.data;
    },

    getById: async (id) => {
        const response = await api.get(`/stock-opnames/${id}`);
        return response.data.data || response.data;
    },

    create: async (data) => {
        const response = await api.post("/stock-opnames", data);
        return response.data.data || response.data;
    },

    update: async (id, data) => {
        const response = await api.put(`/stock-opnames/${id}`, data);
        return response.data.data || response.data;
    },

    delete: async (id) => {
        const response = await api.delete(`/stock-opnames/${id}`);
        return response.data;
    },

    // Status actions
    startCounting: async (id) => {
        const response = await api.post(`/stock-opnames/${id}/start`);
        return response.data.data || response.data;
    },

    complete: async (id) => {
        const response = await api.post(`/stock-opnames/${id}/complete`);
        return response.data;
    },

    cancel: async (id) => {
        const response = await api.post(`/stock-opnames/${id}/cancel`);
        return response.data.data || response.data;
    },

    // Helpers
    calculateSystemQuantity: async (productId, locationId) => {
        const response = await api.post(
            "/stock-opnames/calculate-system-quantity",
            {
                product_id: productId,
                location_id: locationId,
            }
        );
        return response;
    },

    getStatistics: async (params = {}) => {
        const response = await api.get("/stock-opnames/statistics", {
            params,
        });
        return response;
    },

    // Bulk operations
    bulkComplete: async (ids) => {
        const response = await api.post("/stock-opnames/bulk-complete", {
            ids,
        });
        return response.data;
    },

    bulkDelete: async (ids) => {
        const response = await api.post("/stock-opnames/bulk-delete", {
            ids,
        });
        return response.data;
    },

    // Export
    export: async (params = {}) => {
        const response = await api.get("/stock-opnames/export", {
            params,
            responseType: "blob",
        });
        return response.data;
    },
};

// Stock Card Service
export const stockCardService = {
    getAll: async (params = {}) => {
        const response = await api.get("/stock-cards", { params });
        return response.data;
    },

    getById: async (id) => {
        const response = await api.get(`/stock-cards/${id}`);
        return response.data;
    },

    getByProduct: async (productId, params = {}) => {
        const response = await api.get(`/stock-cards/product/${productId}`, {
            params,
        });
        return response.data;
    },

    getByLocation: async (locationId, params = {}) => {
        const response = await api.get(`/stock-cards/location/${locationId}`, {
            params,
        });
        return response.data;
    },

    export: async (params = {}) => {
        const response = await api.get("/stock-cards/export", {
            params,
            responseType: "blob",
        });
        return response.data;
    },
};

// Location Service
export const locationService = {
    getAll: async (params = {}) => {
        const response = await api.get("/locations", { params });
        return response.data;
    },

    getById: async (id) => {
        const response = await api.get(`/locations/${id}`);
        return response.data;
    },

    create: async (data) => {
        const response = await api.post("/locations", data);
        return response.data;
    },

    update: async (id, data) => {
        const response = await api.put(`/locations/${id}`, data);
        return response.data;
    },

    delete: async (id) => {
        const response = await api.delete(`/locations/${id}`);
        return response.data;
    },

    getStock: async (locationId) => {
        const response = await api.get(`/locations/${locationId}/stock`);
        return response.data;
    },
};

// Reports Service
export const warehouseReportService = {
    stockSummary: async (params = {}) => {
        const response = await api.get("/reports/stock-summary", {
            params,
        });
        return response.data;
    },

    stockMovement: async (params = {}) => {
        const response = await api.get("/reports/stock-movement", {
            params,
        });
        return response.data;
    },

    mutationReport: async (params = {}) => {
        const response = await api.get("/reports/mutation", { params });
        return response.data;
    },

    adjustmentReport: async (params = {}) => {
        const response = await api.get("/reports/adjustment", { params });
        return response.data;
    },

    opnameReport: async (params = {}) => {
        const response = await api.get("/reports/opname", { params });
        return response.data;
    },

    exportStockCard: async (params = {}) => {
        const response = await api.get("/reports/stock-card/export", {
            params,
            responseType: "blob",
        });
        return response.data;
    },
};

// Stock Book Service (Buku Stock)
export const stockBookService = {
    // Main list with filters
    getAll: async (params = {}) => {
        const response = await api.get("/stock-book", { params });
        return response.data;
    },

    // Detailed ledger for product + location
    getLedger: async (params = {}) => {
        const response = await api.get("/stock-book/ledger", { params });
        return response.data;
    },

    // Current balances - Real-time stock position
    getCurrentBalances: async (params = {}) => {
        const response = await api.get("/stock-book/current-balances", {
            params,
        });
        return response.data;
    },

    // Balance by date - Historical balance
    getBalanceByDate: async (params = {}) => {
        const response = await api.get("/stock-book/balance-by-date", {
            params,
        });
        return response.data;
    },

    // Movement summary - Aggregated data
    getMovementSummary: async (params = {}) => {
        const response = await api.get("/stock-book/movement-summary", {
            params,
        });
        return response.data;
    },

    // Statistics - Dashboard stats
    getStatistics: async () => {
        const response = await api.get("/stock-book/statistics");
        return response.data;
    },

    // Get products that have stock
    getProductsWithStock: async () => {
        const response = await api.get("/stock-book/products-with-stock");
        return response.data;
    },

    // Get locations that have stock
    getLocationsWithStock: async () => {
        const response = await api.get("/stock-book/locations-with-stock");
        return response.data;
    },

    // Export
    export: async (params = {}) => {
        const response = await api.get("/stock-book/export", {
            params,
            responseType: "blob",
        });
        return response.data;
    },
};

// Dummy Data Generator
export const dummyDataService = {
    generateProducts: () => {
        const categories = [
            "Electronics",
            "Furniture",
            "Office Supplies",
            "Raw Materials",
            "Tools",
        ];
        const units = ["pcs", "box", "kg", "meter", "liter"];

        return Array.from({ length: 50 }, (_, i) => ({
            id: i + 1,
            code: `PRD${String(i + 1).padStart(5, "0")}`,
            name: `Product ${i + 1}`,
            description: `Description for product ${i + 1}`,
            category: categories[Math.floor(Math.random() * categories.length)],
            unit: units[Math.floor(Math.random() * units.length)],
            price: Math.floor(Math.random() * 1000000) + 10000,
            min_stock: Math.floor(Math.random() * 50) + 10,
            max_stock: Math.floor(Math.random() * 500) + 100,
            status: Math.random() > 0.2 ? "active" : "inactive",
            created_at: new Date(
                Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000
            ).toISOString(),
            updated_at: new Date(
                Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
            ).toISOString(),
        }));
    },

    generateLocations: () => {
        const types = ["warehouse", "store", "production", "transit"];

        return Array.from({ length: 10 }, (_, i) => ({
            id: i + 1,
            code: `LOC${String(i + 1).padStart(3, "0")}`,
            name: `Location ${i + 1}`,
            address: `Address ${i + 1}, Street ${
                Math.floor(Math.random() * 100) + 1
            }`,
            type: types[Math.floor(Math.random() * types.length)],
            capacity: Math.floor(Math.random() * 10000) + 1000,
            status: Math.random() > 0.1 ? "active" : "inactive",
            created_at: new Date(
                Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000
            ).toISOString(),
            updated_at: new Date(
                Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
            ).toISOString(),
        }));
    },

    generateStockIn: (products, locations) => {
        const statuses = ["draft", "approved", "rejected"];

        return Array.from({ length: 30 }, (_, i) => ({
            id: i + 1,
            code: `IN${String(i + 1).padStart(6, "0")}`,
            date: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            )
                .toISOString()
                .split("T")[0],
            location_id:
                locations[Math.floor(Math.random() * locations.length)].id,
            location_name:
                locations[Math.floor(Math.random() * locations.length)].name,
            supplier: `Supplier ${Math.floor(Math.random() * 10) + 1}`,
            reference: `REF${String(i + 1).padStart(8, "0")}`,
            status: statuses[Math.floor(Math.random() * statuses.length)],
            notes: `Notes for stock in ${i + 1}`,
            total_items: Math.floor(Math.random() * 10) + 1,
            created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
            created_at: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            ).toISOString(),
            updated_at: new Date(
                Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
            ).toISOString(),
            details: Array.from(
                { length: Math.floor(Math.random() * 5) + 1 },
                (_, j) => ({
                    product_id:
                        products[Math.floor(Math.random() * products.length)]
                            .id,
                    product_name:
                        products[Math.floor(Math.random() * products.length)]
                            .name,
                    product_code:
                        products[Math.floor(Math.random() * products.length)]
                            .code,
                    quantity: Math.floor(Math.random() * 100) + 1,
                    unit_price: Math.floor(Math.random() * 100000) + 1000,
                    total_price: 0, // Will be calculated
                })
            ),
        }));
    },

    generateStockMutations: (products, locations) => {
        const statuses = ["draft", "approved", "rejected"];

        return Array.from({ length: 25 }, (_, i) => ({
            id: i + 1,
            code: `MT${String(i + 1).padStart(6, "0")}`,
            date: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            )
                .toISOString()
                .split("T")[0],
            from_location_id:
                locations[Math.floor(Math.random() * locations.length)].id,
            from_location_name:
                locations[Math.floor(Math.random() * locations.length)].name,
            to_location_id:
                locations[Math.floor(Math.random() * locations.length)].id,
            to_location_name:
                locations[Math.floor(Math.random() * locations.length)].name,
            status: statuses[Math.floor(Math.random() * statuses.length)],
            notes: `Notes for mutation ${i + 1}`,
            total_items: Math.floor(Math.random() * 10) + 1,
            created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
            created_at: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            ).toISOString(),
            updated_at: new Date(
                Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
            ).toISOString(),
            details: Array.from(
                { length: Math.floor(Math.random() * 5) + 1 },
                (_, j) => ({
                    product_id:
                        products[Math.floor(Math.random() * products.length)]
                            .id,
                    product_name:
                        products[Math.floor(Math.random() * products.length)]
                            .name,
                    product_code:
                        products[Math.floor(Math.random() * products.length)]
                            .code,
                    quantity: Math.floor(Math.random() * 100) + 1,
                })
            ),
        }));
    },

    generateStockAdjustments: (products, locations) => {
        const statuses = ["draft", "approved", "rejected"];
        const types = ["increase", "decrease"];

        return Array.from({ length: 20 }, (_, i) => ({
            id: i + 1,
            code: `ADJ${String(i + 1).padStart(6, "0")}`,
            date: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            )
                .toISOString()
                .split("T")[0],
            location_id:
                locations[Math.floor(Math.random() * locations.length)].id,
            location_name:
                locations[Math.floor(Math.random() * locations.length)].name,
            type: types[Math.floor(Math.random() * types.length)],
            status: statuses[Math.floor(Math.random() * statuses.length)],
            reason: `Reason for adjustment ${i + 1}`,
            notes: `Notes for adjustment ${i + 1}`,
            total_items: Math.floor(Math.random() * 10) + 1,
            created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
            created_at: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            ).toISOString(),
            updated_at: new Date(
                Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
            ).toISOString(),
            details: Array.from(
                { length: Math.floor(Math.random() * 5) + 1 },
                (_, j) => ({
                    product_id:
                        products[Math.floor(Math.random() * products.length)]
                            .id,
                    product_name:
                        products[Math.floor(Math.random() * products.length)]
                            .name,
                    product_code:
                        products[Math.floor(Math.random() * products.length)]
                            .code,
                    system_quantity: Math.floor(Math.random() * 100) + 1,
                    actual_quantity: Math.floor(Math.random() * 100) + 1,
                    adjustment_quantity: 0, // Will be calculated
                    reason: `Detail reason ${j + 1}`,
                })
            ),
        }));
    },

    generateStockOpnames: (products, locations) => {
        const statuses = [
            "draft",
            "in_progress",
            "completed",
            "approved",
            "rejected",
        ];

        return Array.from({ length: 15 }, (_, i) => ({
            id: i + 1,
            code: `OP${String(i + 1).padStart(6, "0")}`,
            period: `${new Date().getFullYear()}-${String(
                Math.floor(Math.random() * 12) + 1
            ).padStart(2, "0")}`,
            date: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            )
                .toISOString()
                .split("T")[0],
            location_id:
                locations[Math.floor(Math.random() * locations.length)].id,
            location_name:
                locations[Math.floor(Math.random() * locations.length)].name,
            status: statuses[Math.floor(Math.random() * statuses.length)],
            notes: `Notes for stock opname ${i + 1}`,
            total_items: Math.floor(Math.random() * 20) + 5,
            variance_items: Math.floor(Math.random() * 10),
            created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
            created_at: new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            ).toISOString(),
            updated_at: new Date(
                Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
            ).toISOString(),
            details: Array.from(
                { length: Math.floor(Math.random() * 15) + 5 },
                (_, j) => ({
                    product_id:
                        products[Math.floor(Math.random() * products.length)]
                            .id,
                    product_name:
                        products[Math.floor(Math.random() * products.length)]
                            .name,
                    product_code:
                        products[Math.floor(Math.random() * products.length)]
                            .code,
                    system_quantity: Math.floor(Math.random() * 100) + 1,
                    counted_quantity: Math.floor(Math.random() * 100) + 1,
                    variance: 0, // Will be calculated
                    notes: `Counting notes ${j + 1}`,
                })
            ),
        }));
    },

    generateStockCards: (products, locations) => {
        const transactions = [
            "stock_in",
            "stock_out",
            "mutation_in",
            "mutation_out",
            "adjustment_in",
            "adjustment_out",
            "opname_in",
            "opname_out",
        ];

        return Array.from({ length: 100 }, (_, i) => ({
            id: i + 1,
            date: new Date(
                Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000
            )
                .toISOString()
                .split("T")[0],
            product_id:
                products[Math.floor(Math.random() * products.length)].id,
            product_name:
                products[Math.floor(Math.random() * products.length)].name,
            product_code:
                products[Math.floor(Math.random() * products.length)].code,
            location_id:
                locations[Math.floor(Math.random() * locations.length)].id,
            location_name:
                locations[Math.floor(Math.random() * locations.length)].name,
            transaction_type:
                transactions[Math.floor(Math.random() * transactions.length)],
            reference: `REF${String(i + 1).padStart(8, "0")}`,
            quantity_in:
                Math.random() > 0.5 ? Math.floor(Math.random() * 100) + 1 : 0,
            quantity_out:
                Math.random() > 0.5 ? Math.floor(Math.random() * 100) + 1 : 0,
            balance: Math.floor(Math.random() * 1000) + 100,
            notes: `Transaction notes ${i + 1}`,
            created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
            created_at: new Date(
                Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000
            ).toISOString(),
        }));
    },
};
