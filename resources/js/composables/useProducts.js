import { ref, computed } from "vue";
import api from "../utils/api";
import { useNotificationStore } from "../stores/notification";

export function useProducts() {
    const notificationStore = useNotificationStore();

    // State
    const products = ref([]);
    const product = ref(null);
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
        product_type: "",
        category_id: "",
        is_active: "",
        location_id: "",
        low_stock: false,
        sort_by: "product_code",
        sort_order: "asc",
    });

    // Fetch products with filters
    const fetchProducts = async (options = {}) => {
        console.log("=== fetchProducts called ===", new Date().toISOString());
        console.log("Options:", options, "Loading:", loading.value);

        // Handle both old format (page number) and new format (options object)
        let page = 1;
        let perPage = pagination.value.per_page;

        if (typeof options === 'number') {
            page = options;
        } else {
            page = options.page || 1;
            perPage = options.per_page || pagination.value.per_page;
        }

        console.time("fetchProducts-" + page);

        // Prevent concurrent fetches
        if (loading.value) {
            console.log("fetchProducts blocked - already loading");
            return;
        }

        console.log("fetchProducts starting - setting loading to true");
        loading.value = true;
        try {
            const params = {
                page,
                per_page: perPage,
                ...filters.value,
                ...options, // Allow overriding filters with direct options
            };

            const response = await api.get("/products", { params });
            products.value = response.data.data;
            pagination.value = {
                current_page: response.data.current_page,
                per_page: response.data.per_page,
                total: response.data.total,
                last_page: response.data.last_page,
            };
            return response.data;
        } catch (error) {
            console.error("Error fetching products:", error);
            notificationStore.error(
                "Failed to load products",
                error.response?.data?.message ||
                    "An error occurred while loading products"
            );
            throw error;
        } finally {
            console.log("fetchProducts completed - setting loading to false");
            console.timeEnd("fetchProducts-" + page);
            loading.value = false;
        }
    };

    // Fetch single product
    const fetchProduct = async (id, withStock = false) => {
        loading.value = true;
        try {
            const params = withStock ? { with_stock: true } : {};
            const response = await api.get(`/products/${id}`, { params });

            product.value = response.data.data;
            return response.data;
        } catch (error) {
            console.error("Error fetching product:", error);
            notificationStore.error(
                "Failed to load product",
                error.response?.data?.message ||
                    "An error occurred while loading the product"
            );
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Create product
    const createProduct = async (data) => {
        saving.value = true;
        try {
            const response = await api.post("/products", data);

            notificationStore.success("Product created successfully");

            return response.data;
        } catch (error) {
            console.error("Error creating product:", error);

            const message =
                error.response?.data?.message || "Failed to create product";
            notificationStore.error("Failed to create product", message);

            throw error;
        } finally {
            saving.value = false;
        }
    };

    // Update product
    const updateProduct = async (id, data) => {
        saving.value = true;
        try {
            const response = await api.put(`/products/${id}`, data);

            notificationStore.success("Product updated successfully");

            return response.data;
        } catch (error) {
            console.error("Error updating product:", error);

            const message =
                error.response?.data?.message || "Failed to update product";
            notificationStore.error("Failed to update product", message);

            throw error;
        } finally {
            saving.value = false;
        }
    };

    // Delete product
    const deleteProduct = async (id) => {
        deleting.value = true;
        try {
            await api.delete(`/products/${id}`);

            notificationStore.success("Product deleted successfully");
        } catch (error) {
            console.error("Error deleting product:", error);

            const message =
                error.response?.data?.message || "Failed to delete product";
            notificationStore.error("Failed to delete product", message);

            throw error;
        } finally {
            deleting.value = false;
        }
    };

    // Toggle product status
    const toggleProductStatus = async (id) => {
        try {
            const response = await api.post(`/products/${id}/toggle-status`);

            notificationStore.success("Product status updated successfully");

            return response.data;
        } catch (error) {
            console.error("Error toggling product status:", error);
            notificationStore.error("Failed to update product status");
            throw error;
        }
    };

    // Get active products
    const fetchActiveProducts = async () => {
        try {
            const response = await api.get("/products/active");
            return response.data.data;
        } catch (error) {
            console.error("Error fetching active products:", error);
            notificationStore.error("Failed to load active products");
            throw error;
        }
    };

    // Get product stock
    const fetchProductStock = async (id, locationId = null) => {
        try {
            const params = locationId ? { location_id: locationId } : {};
            const response = await api.get(`/products/${id}/stock`, { params });
            return response.data.data;
        } catch (error) {
            console.error("Error fetching product stock:", error);
            notificationStore.error("Failed to load product stock");
            throw error;
        }
    };

    // Get product statistics
    const fetchProductStatistics = async () => {
        try {
            const response = await api.get("/products/statistics");
            return response.data;
        } catch (error) {
            console.error("Error fetching product statistics:", error);
            throw error;
        }
    };

    // Generate product code
    const generateProductCode = async (productType) => {
        try {
            const response = await api.get("/products/generate-code", {
                params: { product_type: productType },
            });
            return response.data.code;
        } catch (error) {
            console.error("Error generating product code:", error);
            notificationStore.error("Failed to generate product code");
            throw error;
        }
    };

    // Get low stock products
    const fetchLowStockProducts = async (locationId = null) => {
        try {
            const params = locationId ? { location_id: locationId } : {};
            const response = await api.get("/products/low-stock", { params });
            return response.data.data;
        } catch (error) {
            console.error("Error fetching low stock products:", error);
            throw error;
        }
    };

    // Export products
    const exportProducts = async () => {
        try {
            const response = await api.get("/products/export", {
                params: filters.value,
                responseType: "blob",
            });

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", `products-${Date.now()}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            notificationStore.success("Products exported successfully");
        } catch (error) {
            console.error("Error exporting products:", error);
            notificationStore.error("Failed to export products");
            throw error;
        }
    };

    // Apply filters and refresh
    const applyFilters = (newFilters) => {
        console.log("applyFilters called with:", newFilters);
        filters.value = { ...filters.value, ...newFilters };
        return fetchProducts(1);
    };

    // Reset filters
    const resetFilters = () => {
        console.log("resetFilters called");
        filters.value = {
            search: "",
            product_type: "",
            category_id: "",
            is_active: "",
            location_id: "",
            low_stock: false,
            sort_by: "product_code",
            sort_order: "asc",
        };
        return fetchProducts(1);
    };

    // Change page
    const changePage = (page, itemsPerPage) => {
        console.log("changePage called with:", page, itemsPerPage);
        if (itemsPerPage?.value) {
            pagination.value.per_page = itemsPerPage?.value;
        }

        return fetchProducts(page);
    };

    // Change sorting
    const changeSort = (sortBy, sortOrder = "asc") => {
        console.log("changeSort called with:", sortBy, sortOrder);
        filters.value.sort_by = sortBy;
        filters.value.sort_order = sortOrder;
        return fetchProducts(1);
    };

    // Computed
    const hasProducts = computed(() => products.value.length > 0);
    const isEmpty = computed(
        () => !loading.value && products.value.length === 0
    );

    return {
        // State
        products,
        product,
        loading,
        saving,
        deleting,
        pagination,
        filters,

        // Computed
        hasProducts,
        isEmpty,

        // Methods
        fetchProducts,
        fetchProduct,
        createProduct,
        updateProduct,
        deleteProduct,
        toggleProductStatus,
        fetchActiveProducts,
        fetchProductStock,
        fetchProductStatistics,
        generateProductCode,
        fetchLowStockProducts,
        exportProducts,
        applyFilters,
        resetFilters,
        changePage,
        changeSort,
    };
}
