<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $t('products.title') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    {{ $t('products.subtitle') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="showFilters = !showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <FunnelIcon class="w-4 h-4" />
                    {{ $t('products.filters') }}
                </button>
                <button
                    @click="handleExport"
                    :disabled="exporting"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    {{ exporting ? $t('products.exporting') : $t('products.export') }}
                </button>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <PlusIcon class="w-4 h-4" />
                    {{ $t('products.addProduct') }}
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <ProductStats :stats="stats" />

        <!-- Filters -->
        <ProductFilters
            :show="showFilters"
            :filters="filters"
            @update:filters="handleFilterChange"
            @reset="handleResetFilters"
        />
        <!-- Products Table -->
        <DataTable
            :title="$t('products.productsTable')"
            :data="products"
            :columns="columns"
            :loading="loading"
            :server-side-pagination="true"
            :pagination="pagination"
            @add="showAddModal = true"
            @edit="handleEdit"
            @delete="handleDelete"
            @refresh="handleRefresh"
            @page-change="handlePageChange"
            @sort="handleSort"
            :show-refresh="true"
            :refresh-loading="refreshing"
            :showAddButton="false"
            :showFilters="false"
            :showExport="false"
        />

        <!-- Add/Edit Product Modal -->
        <ProductFormModal
            :is-open="showAddModal || editingProduct"
            :editing-product="editingProduct"
            :unit-options="unitOptions"
            :category-options="categoryOptions"
            @close="closeModal"
            @saved="handleFormSave"
            @unit-added="handleUnitAdded"
            @product-type-added="handleProductTypeAdded"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showDeleteModal"
            :title="$t('products.deleteProduct')"
            :message="`${$t('products.deleteProductMessage')} ${
                deletingProduct?.product_name || $t('products.thisProduct')
            }?`"
            :description="$t('products.deleteProductDescription')"
            :confirm-text="$t('products.delete')"
            :cancel-text="$t('products.cancel')"
            :loading="deleting"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useProducts } from "../../../composables/useProducts";
import { useSelectOptions } from "../../../composables/useSelectOptions";
import DataTable from "../../../components/UI/DataTable.vue";
import ConfirmationModal from "../../../components/Overlays/ConfirmationModal.vue";
import ProductStats from "../../../components/Products/ProductStats.vue";
import ProductFilters from "../../../components/Products/ProductFilters.vue";
import ProductFormModal from "../../../components/Products/ProductFormModal.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    FunnelIcon,
} from "@heroicons/vue/24/outline";

const { t } = useI18n();

// Use Products Composable
const {
    products: productsData,
    loading,
    saving,
    deleting,
    pagination: paginationData,
    filters,
    fetchProducts,
    createProduct,
    updateProduct,
    deleteProduct,
    fetchProductStatistics,
    exportProducts: exportProductsComposable,
    applyFilters: applyFiltersComposable,
    resetFilters: resetFiltersComposable,
    changePage,
} = useProducts();

// Use Select Options Composable
const {
    unitOptions,
    categoryOptions,
    locationOptions,
    loadUnits,
    loadCategories,
    loadLocations,
    addUnit,
    addCategory,
    loadingUnits,
    loadingCategories,
} = useSelectOptions();

// Local State
const refreshing = ref(false);
const exporting = ref(false);
const showAddModal = ref(false);
const editingProduct = ref(null);
const showDeleteModal = ref(false);
const deletingProduct = ref(null);
const showFilters = ref(false);
const statistics = ref({
    total: 0,
    active: 0,
    low_stock: 0,
    by_type: {},
});

// Table columns
const columns = computed(() => [
    {
        key: "product_code",
        label: t("products.code"),
        sortable: true,
    },
    {
        key: "product_name",
        label: t("products.productName"),
        sortable: true,
    },
    {
        key: "product_type",
        label: t("products.type"),
        sortable: true,
        type: "badge",
        badgeColors: {
            finished_goods:
                "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
            raw_material:
                "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
            consumable:
                "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
        },
        formatter: (value) => {
            const typeMap = {
                finished_goods: t("products.typeFinishedGoods"),
                raw_material: t("products.typeRawMaterial"),
                consumable: t("products.typeConsumable"),
            };
            return typeMap[value] || value;
        },
    },
    {
        key: "unit.name",
        label: t("products.unit"),
        sortable: false,
    },
    {
        key: "purchase_price",
        label: t("products.purchasePrice"),
        sortable: true,
        type: "currency",
    },
    {
        key: "selling_price",
        label: t("products.sellingPrice"),
        sortable: true,
        type: "currency",
    },
    {
        key: "minimum_stock",
        label: t("products.minStock"),
        sortable: true,
    },
    {
        key: "maximum_stock",
        label: t("products.maxStock"),
        sortable: true,
    },
    {
        key: "is_active",
        label: t("products.status"),
        sortable: true,
        type: "badge",
        badgeColors: {
            true: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
            false: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        },
        formatter: (value) => (value ? t("products.statusActive") : t("products.statusInactive")),
    },
    {
        key: "created_at",
        label: t("products.createdAt"),
        sortable: true,
        type: "date",
    },
]);

// Computed
const products = computed(() => productsData.value || []);
const pagination = computed(() => paginationData.value || []);

const stats = computed(() => ({
    total: statistics.value.total || 0,
    active: statistics.value.active || 0,
    low_stock: statistics.value.low_stock || 0,
}));

// Methods
const loadStatistics = async () => {
    console.log("loadStatistics");

    try {
        const data = await fetchProductStatistics();
        statistics.value = data;
    } catch (error) {
        console.error("Error loading statistics:", error);
    }
};

const handleFilterChange = () => {
    console.log("handleFilterChange");
    applyFiltersComposable(filters.value);
    loadStatistics();
};

const handleResetFilters = () => {
    console.log("handleResetFilters");
    resetFiltersComposable();
    loadStatistics();
};

const handleRefresh = async () => {
    console.log("handleRefresh");
    refreshing.value = true;
    await fetchProducts(pagination.value.current_page);
    await loadStatistics();
    refreshing.value = false;
};

const handlePageChange = (page, itemsPerPage) => {
    console.log("handlePageChange");
    changePage(page, itemsPerPage);
};

const handleSort = (column, direction) => {
    filters.value.sort_by = column;
    filters.value.sort_order = direction;
    applyFiltersComposable(filters.value);
};

const handleEdit = (product) => {
    editingProduct.value = product;
    showAddModal.value = true;
};

const handleDelete = (product) => {
    deletingProduct.value = product;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    if (!deletingProduct.value || deleting.value) return;

    try {
        await deleteProduct(deletingProduct.value.id);
        cancelDelete();
        await handleRefresh();
    } catch (error) {
        console.error("Error deleting product:", error);
        // Error notification already handled in composable
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deletingProduct.value = null;
};

const handleFormSave = async ({ formData, isEditing, productId }) => {
    try {
        if (isEditing) {
            await updateProduct(productId, formData);
        } else {
            await createProduct(formData);
        }

        closeModal();
        await handleRefresh();
    } catch (error) {
        console.error("Error saving product:", error);
        // Error handling is done in the modal component
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingProduct.value = null;
};

const handleExport = async () => {
    exporting.value = true;
    try {
        await exportProductsComposable();
    } catch (error) {
        console.error("Error exporting products:", error);
    } finally {
        exporting.value = false;
    }
};

const handleUnitAdded = async () => {
    // Composable already handles adding to list and refreshing
    // Just need to reload units to ensure UI is in sync
    await loadUnits(true); // Force refresh
};

const handleProductTypeAdded = async () => {
    // Product types are enum, so this shouldn't normally be called
    // But if categories are added, reload categories
    await loadCategories(true); // Force refresh
};

// Lifecycle
onMounted(async () => {
    await Promise.all([loadUnits(), loadCategories()]);
    await fetchProducts();
    await loadStatistics();
});
</script>
