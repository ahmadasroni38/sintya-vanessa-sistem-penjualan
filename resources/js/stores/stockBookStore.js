import { defineStore } from "pinia";
import { stockBookService } from "../services/stockBookService";

export const useStockBookStore = defineStore("stockBook", {
    state: () => ({
        // View modes
        viewMode: "default", // ledger, product, location, balance, best_selling, slow_moving, sales_recap

        // Filters
        filters: {
            product_id: null,
            location_id: null,
            start_date: null,
            end_date: null,
            transaction_type: null,
            search: "",
            status_filter: "all", // for current balances
        },

        // Data
        stockCards: [],
        currentBalances: [],
        statistics: {},
        movementSummary: null,
        ledgerData: null,

        // Options
        products: [],
        locations: [],

        // UI State
        loading: false,
        refreshing: false,
        exporting: false,

        // Pagination
        pagination: {
            current_page: 1,
            per_page: 50,
            total: 0,
            last_page: 1,
        },

        // Cache timestamps
        cacheTimestamps: {
            statistics: null,
            currentBalances: null,
        },

        // Search timeout for debouncing
        searchTimeout: null,
    }),

    getters: {
        // Check if filters are active
        hasActiveFilters: (state) => {
            return Object.values(state.filters).some(
                (value) => value !== null && value !== "" && value !== "all"
            );
        },

        // Get active filters count
        activeFiltersCount: (state) => {
            return Object.values(state.filters).filter(
                (value) => value !== null && value !== "" && value !== "all"
            ).length;
        },

        // Check if cache is valid (5 minutes)
        isCacheValid: (state) => (cacheKey) => {
            const timestamp = state.cacheTimestamps[cacheKey];
            if (!timestamp) return false;
            return Date.now() - timestamp < 5 * 60 * 1000; // 5 minutes
        },

        // Get selected product
        selectedProduct: (state) => {
            return state.products.find(
                (p) => p.id === state.filters.product_id
            );
        },

        // Get selected location
        selectedLocation: (state) => {
            return state.locations.find(
                (l) => l.id === state.filters.location_id
            );
        },

        // Product options for select
        productOptions: (state) => {
            if (!Array.isArray(state.products)) return [];
            return state.products.map((product) => ({
                value: product.id,
                label: `${product.product_code} - ${product.product_name}`,
            }));
        },

        // Location options for select
        locationOptions: (state) => {
            if (!Array.isArray(state.locations)) return [];
            return state.locations.map((location) => ({
                value: location.id,
                label: `${location.code} - ${location.name}`,
            }));
        },
    },

    actions: {
        // Initialize store
        async initialize() {
            await Promise.all([
                this.loadProducts(),
                this.loadLocations(),
                this.loadStatistics(),
            ]);
        },

        // Load products with stock
        async loadProducts() {
            try {
                const response = await stockBookService.getProductsWithStock();
                this.products = response || [];
            } catch (error) {
                console.error("Failed to load products:", error);
                this.products = [];
                throw error;
            }
        },

        // Load locations with stock
        async loadLocations() {
            try {
                const response = await stockBookService.getLocationsWithStock();
                this.locations = response || [];
            } catch (error) {
                console.error("Failed to load locations:", error);
                this.locations = [];
                throw error;
            }
        },

        // Load statistics (with caching)
        async loadStatistics(forceRefresh = false) {
            if (!forceRefresh && this.isCacheValid("statistics")) {
                return;
            }

            try {
                const response = await stockBookService.getStatistics();
                this.statistics = response;
                this.cacheTimestamps.statistics = Date.now();
            } catch (error) {
                console.error("Failed to load statistics:", error);
                throw error;
            }
        },

        // Load stock cards with filters
        async fetchStockCards(append = false) {
            this.loading = true;

            try {
                const params = {
                    ...this.filters,
                    per_page: this.pagination.per_page,
                    page: append ? this.pagination.current_page + 1 : 1,
                };

                const response = await stockBookService.getAll(params);

                if (append) {
                    this.stockCards = [...this.stockCards, ...response];
                } else {
                    this.stockCards = response;
                }

                this.pagination = {
                    current_page: response.current_page,
                    per_page: response.per_page,
                    total: response.total,
                    last_page: response.last_page,
                };
            } catch (error) {
                console.error("Failed to fetch stock cards:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        // Load ledger data for specific product and location
        async fetchLedgerData() {
            if (!this.filters.product_id || !this.filters.location_id) {
                throw new Error(
                    "Product and Location are required for Ledger View"
                );
            }

            this.loading = true;

            try {
                const params = {
                    product_id: this.filters.product_id,
                    location_id: this.filters.location_id,
                    start_date: this.filters.start_date,
                    end_date: this.filters.end_date,
                    per_page: this.pagination.per_page,
                };

                this.ledgerData = await stockBookService.getLedger(params);
            } catch (error) {
                console.error("Failed to fetch ledger data:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        // Load current balances
        async fetchCurrentBalances(forceRefresh = false) {
            if (!this.filters.location_id) {
                throw new Error(
                    "Location is required for Current Balance View"
                );
            }

            if (!forceRefresh && this.isCacheValid("currentBalances")) {
                return;
            }

            this.loading = true;

            try {
                const params = {
                    location_id: this.filters.location_id,
                    search: this.filters.search,
                    status_filter: this.filters.status_filter,
                };

                const response = await stockBookService.getCurrentBalances(
                    params
                );
                this.currentBalances = response?.balances || response || [];
                this.cacheTimestamps.currentBalances = Date.now();
            } catch (error) {
                console.error("Failed to fetch current balances:", error);
                this.currentBalances = [];
                throw error;
            } finally {
                this.loading = false;
            }
        },

        // Load movement summary
        async fetchMovementSummary() {
            if (!this.filters.start_date || !this.filters.end_date) {
                throw new Error("Date range is required for Movement Summary");
            }

            this.loading = true;

            try {
                const params = {
                    product_id: this.filters.product_id,
                    location_id: this.filters.location_id,
                    start_date: this.filters.start_date,
                    end_date: this.filters.end_date,
                };

                this.movementSummary =
                    await stockBookService.getMovementSummary(params);
            } catch (error) {
                console.error("Failed to fetch movement summary:", error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        // Export data
        async exportData(format = "xlsx") {
            this.exporting = true;

            try {
                const params = {
                    format,
                    ...this.filters,
                };

                const response = await stockBookService.export(params);

                // Create download link
                const url = window.URL.createObjectURL(new Blob([response]));
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", `stock-book.${format}`);
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error("Failed to export data:", error);
                throw error;
            } finally {
                this.exporting = false;
            }
        },

        // Update filters
        updateFilters(newFilters) {
            this.filters = { ...this.filters, ...newFilters };
        },

        // Clear all filters
        clearFilters() {
            this.filters = {
                product_id: null,
                location_id: null,
                start_date: null,
                end_date: null,
                transaction_type: null,
                search: "",
                status_filter: "all",
            };
            this.pagination.current_page = 1;
        },

        // Set view mode
        setViewMode(mode) {
            this.viewMode = mode;
        },

        // Set pagination
        setPagination(page, perPage = null) {
            this.pagination.current_page = page;
            if (perPage) {
                this.pagination.per_page = perPage;
            }
        },

        // Refresh current view
        async refreshCurrentView() {
            this.refreshing = true;

            try {
                switch (this.viewMode) {
                    case "ledger":
                        if (
                            this.filters.product_id &&
                            this.filters.location_id
                        ) {
                            await this.fetchLedgerData();
                        }
                        break;
                    case "balance":
                        if (this.filters.location_id) {
                            await this.fetchCurrentBalances(true);
                        }
                        break;
                    case "product":
                    case "location":
                        await this.fetchStockCards();
                        break;
                    case "best_selling":
                    case "slow_moving":
                    case "sales_recap":
                        // These views manage their own data loading
                        break;
                    default:
                        await this.fetchStockCards();
                }
            } catch (error) {
                console.error("Failed to refresh:", error);
                throw error;
            } finally {
                this.refreshing = false;
            }
        },

        // Debounced search
        debouncedSearch: async function () {
            // Simple debounce implementation
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(async () => {
                await this.refreshCurrentView();
            }, 300);
        },

        // Load more data (infinite scroll)
        async loadMore() {
            if (
                this.pagination.current_page < this.pagination.last_page &&
                !this.loading
            ) {
                this.setPagination(this.pagination.current_page + 1);
                await this.fetchStockCards(true);
            }
        },
    },
});
