import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";

/**
 * Chart of Accounts Service
 *
 * Provides methods for interacting with the Chart of Accounts API endpoints.
 * All methods return promises and handle error cases appropriately.
 */
const chartOfAccountService = {
    /**
     * Get all accounts with pagination and filtering options
     *
     * @param {Object} params - Query parameters
     * @param {number} params.page - Page number (default: 1)
     * @param {number} params.per_page - Items per page (default: 15)
     * @param {string} params.search - Search term for account code or name
     * @param {string} params.type - Filter by account type (asset, liability, equity, revenue, expense)
     * @param {boolean|string} params.is_active - Filter by active status
     * @returns {Promise<Object>} Promise that resolves to paginated accounts data
     * @throws {Error} When API request fails
     */
    async getAccounts(params = {}) {
        try {
            const response = await apiGet("chart-of-accounts", params);
            return response;
        } catch (error) {
            console.error("Failed to fetch chart of accounts:", error);
            throw new Error(
                "Failed to fetch chart of accounts. Please try again."
            );
        }
    },

    /**
     * Get account tree structure with hierarchical relationships
     *
     * @returns {Promise<Array>} Promise that resolves to tree structure array
     * @throws {Error} When API request fails
     */
    async getAccountTree() {
        try {
            const response = await apiGet("chart-of-accounts/tree");
            return response;
        } catch (error) {
            console.error("Failed to fetch account tree:", error);
            throw new Error(
                "Failed to fetch account tree structure. Please try again."
            );
        }
    },

    /**
     * Get active accounts for dropdown/select components
     *
     * @returns {Promise<Array>} Promise that resolves to array of active accounts
     * @throws {Error} When API request fails
     */
    async getActiveAccounts() {
        try {
            const response = await apiGet("chart-of-accounts/active");
            return response;
        } catch (error) {
            console.error("Failed to fetch active accounts:", error);
            throw new Error(
                "Failed to fetch active accounts. Please try again."
            );
        }
    },

    /**
     * Create a new chart of account
     *
     * @param {Object} accountData - Account data to create
     * @param {string} accountData.account_code - Unique account code (e.g., "1-1010")
     * @param {string} accountData.account_name - Account name
     * @param {string} accountData.account_type - Account type (asset, liability, equity, revenue, expense)
     * @param {string} accountData.normal_balance - Normal balance (debit, credit)
     * @param {number|null} accountData.parent_id - Parent account ID (null for top-level)
     * @param {number} accountData.level - Hierarchy level (1-5)
     * @param {string|null} accountData.description - Optional description
     * @param {number} accountData.opening_balance - Opening balance (default: 0)
     * @param {boolean} accountData.is_active - Active status (default: true)
     * @returns {Promise<Object>} Promise that resolves to created account data
     * @throws {Error} When validation fails or API request fails
     */
    async createAccount(accountData) {
        try {
            const response = await apiPost("chart-of-accounts", accountData);
            return response;
        } catch (error) {
            console.error("Failed to create chart of account:", error);

            // Handle validation errors specifically
            if (error.response?.status === 422) {
                const validationErrors = error.response.data.errors;
                const errorMessage = Object.values(validationErrors)
                    .flat()
                    .join(", ");
                throw new Error(`Validation failed: ${errorMessage}`);
            }

            throw new Error(
                "Failed to create chart of account. Please check your data and try again."
            );
        }
    },

    /**
     * Update an existing chart of account
     *
     * @param {number} accountId - ID of the account to update
     * @param {Object} accountData - Updated account data
     * @param {string} [accountData.account_code] - Updated account code
     * @param {string} [accountData.account_name] - Updated account name
     * @param {string} [accountData.account_type] - Updated account type
     * @param {string} [accountData.normal_balance] - Updated normal balance
     * @param {number|null} [accountData.parent_id] - Updated parent account ID
     * @param {number} [accountData.level] - Updated hierarchy level
     * @param {string|null} [accountData.description] - Updated description
     * @param {number} [accountData.opening_balance] - Updated opening balance
     * @param {boolean} [accountData.is_active] - Updated active status
     * @returns {Promise<Object>} Promise that resolves to updated account data
     * @throws {Error} When validation fails, account not found, or API request fails
     */
    async updateAccount(accountId, accountData) {
        try {
            const response = await apiPut(
                `chart-of-accounts/${accountId}`,
                accountData
            );
            return response;
        } catch (error) {
            console.error("Failed to update chart of account:", error);

            // Handle specific error cases
            if (error.response?.status === 404) {
                throw new Error("Chart of account not found.");
            }

            if (error.response?.status === 422) {
                const validationErrors = error.response.data.errors;
                const errorMessage = Object.values(validationErrors)
                    .flat()
                    .join(", ");
                throw new Error(`Validation failed: ${errorMessage}`);
            }

            throw new Error(
                "Failed to update chart of account. Please check your data and try again."
            );
        }
    },

    /**
     * Delete a chart of account
     *
     * @param {number} accountId - ID of the account to delete
     * @returns {Promise<Object>} Promise that resolves to deletion confirmation
     * @throws {Error} When account not found, has dependencies, or API request fails
     */
    async deleteAccount(accountId) {
        try {
            const response = await apiDelete(`chart-of-accounts/${accountId}`);
            return response;
        } catch (error) {
            console.error("Failed to delete chart of account:", error);

            // Handle specific error cases
            if (error.response?.status === 404) {
                throw new Error("Chart of account not found.");
            }

            if (error.response?.status === 422) {
                const errorMessage =
                    error.response.data.message ||
                    "Cannot delete account due to dependencies.";
                throw new Error(errorMessage);
            }

            throw new Error(
                "Failed to delete chart of account. Please try again."
            );
        }
    },

    /**
     * Get account balance for a specific period
     *
     * @param {number} accountId - ID of the account
     * @param {Object} params - Balance calculation parameters
     * @param {string|null} params.start_date - Start date for balance calculation (YYYY-MM-DD)
     * @param {string|null} params.end_date - End date for balance calculation (YYYY-MM-DD)
     * @returns {Promise<Object>} Promise that resolves to balance data
     * @throws {Error} When account not found or API request fails
     */
    async getAccountBalance(accountId, params = {}) {
        try {
            const response = await apiGet(
                `chart-of-accounts/${accountId}/balance`,
                params
            );
            return response;
        } catch (error) {
            console.error("Failed to get account balance:", error);

            if (error.response?.status === 404) {
                throw new Error("Chart of account not found.");
            }

            if (error.response?.status === 422) {
                const validationErrors = error.response.data.errors;
                const errorMessage = Object.values(validationErrors)
                    .flat()
                    .join(", ");
                throw new Error(`Invalid date range: ${errorMessage}`);
            }

            throw new Error(
                "Failed to calculate account balance. Please try again."
            );
        }
    },

    /**
     * Export chart of accounts to different formats
     *
     * @param {Object} options - Export options
     * @param {string} options.format - Export format ('excel' or 'pdf')
     * @param {Object} [options.filters] - Filters to apply to export
     * @param {string} [options.filters.type] - Filter by account type
     * @param {boolean|string} [options.filters.is_active] - Filter by active status
     * @param {string} [options.filters.search] - Search filter
     * @returns {Promise<Object>} Promise that resolves to export job information
     * @throws {Error} When export fails or invalid format
     */
    async exportAccounts(options) {
        try {
            const { format, filters = {} } = options;

            // Validate format
            if (!["excel", "pdf"].includes(format)) {
                throw new Error(
                    'Export format must be either "excel" or "pdf"'
                );
            }

            const params = {
                format,
                ...filters,
            };

            const response = await apiGet("chart-of-accounts/export", params);
            return response;
        } catch (error) {
            console.error("Failed to export chart of accounts:", error);

            if (error.response?.status === 422) {
                const validationErrors = error.response.data.errors;
                const errorMessage = Object.values(validationErrors)
                    .flat()
                    .join(", ");
                throw new Error(`Export validation failed: ${errorMessage}`);
            }

            throw new Error(
                "Failed to export chart of accounts. Please try again."
            );
        }
    },

    /**
     * Get a single chart of account by ID
     *
     * @param {number} accountId - ID of the account to retrieve
     * @returns {Promise<Object>} Promise that resolves to account data
     * @throws {Error} When account not found or API request fails
     */
    async getAccountById(accountId) {
        try {
            const response = await apiGet(`chart-of-accounts/${accountId}`);
            return response;
        } catch (error) {
            console.error("Failed to fetch chart of account:", error);

            if (error.response?.status === 404) {
                throw new Error("Chart of account not found.");
            }

            throw new Error(
                "Failed to fetch chart of account. Please try again."
            );
        }
    },

    /**
     * Bulk update multiple chart of accounts
     *
     * @param {Array<number>} accountIds - Array of account IDs to update
     * @param {Object} updates - Updates to apply to all accounts
     * @param {boolean} [updates.is_active] - Update active status
     * @param {string|null} [updates.description] - Update description
     * @returns {Promise<Object>} Promise that resolves to bulk update result
     * @throws {Error} When validation fails or API request fails
     */
    async bulkUpdateAccounts(accountIds, updates) {
        try {
            const response = await apiPut("chart-of-accounts/bulk-update", {
                account_ids: accountIds,
                updates,
            });
            return response;
        } catch (error) {
            console.error("Failed to bulk update chart of accounts:", error);

            if (error.response?.status === 422) {
                const validationErrors = error.response.data.errors;
                const errorMessage = Object.values(validationErrors)
                    .flat()
                    .join(", ");
                throw new Error(
                    `Bulk update validation failed: ${errorMessage}`
                );
            }

            throw new Error(
                "Failed to bulk update chart of accounts. Please try again."
            );
        }
    },

    /**
     * Bulk delete multiple chart of accounts
     *
     * @param {Array<number>} accountIds - Array of account IDs to delete
     * @returns {Promise<Object>} Promise that resolves to bulk delete result
     * @throws {Error} When validation fails or API request fails
     */
    async bulkDeleteAccounts(accountIds) {
        try {
            const response = await apiDelete("chart-of-accounts/bulk-delete", {
                account_ids: accountIds,
            });
            return response;
        } catch (error) {
            console.error("Failed to bulk delete chart of accounts:", error);

            if (error.response?.status === 422) {
                const validationErrors = error.response.data.errors;
                const errorMessage = Object.values(validationErrors)
                    .flat()
                    .join(", ");
                throw new Error(
                    `Bulk delete validation failed: ${errorMessage}`
                );
            }

            throw new Error(
                "Failed to bulk delete chart of accounts. Please try again."
            );
        }
    },
};

export default chartOfAccountService;
