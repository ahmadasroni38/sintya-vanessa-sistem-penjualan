<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader
            title="Stock Masuk"
            description="Kelola penerimaan barang dan stok masuk"
        >
            <template #actions>
                <!-- <button
                    type="button"
                    @click.prevent="showFiltersPanel = !showFiltersPanel"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <FunnelIcon class="w-4 h-4" />
                    Filters
                </button> -->
                <button
                    type="button"
                    @click.prevent="handleAddClick"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <PlusIcon class="w-4 h-4" />
                    Stock Masuk Baru
                </button>
            </template>
        </PageHeader>

        <!-- Statistics -->
        <StockInStats :stats="statistics" />

        <!-- Filters -->
        <StockInFilters
            :show="showFiltersPanel"
            :filters="filters"
            :locations="locationOptions"
            @update:filters="handleFilterUpdate"
            @reset="resetFilters"
        />

        <!-- Data Table -->
        <DataTable
            title="Daftar Stock Masuk"
            :data="stockIns"
            :columns="tableColumns"
            :loading="loading"
            :server-side-pagination="true"
            :pagination="pagination"
            :show-refresh="true"
            :refresh-loading="refreshing"
            :show-add-button="false"
            :show-filters="false"
            :show-export="false"
            search-placeholder="Cari nomor transaksi, supplier..."
            @refresh="handleRefresh"
            @page-change="handlePageChange"
            @sort="handleSort"
            @search="handleSearch"
        >
            <template #actions="{ item }">
                <StockInActions
                    :item="item"
                    @view="viewDetails"
                    @edit="editItem"
                    @post="postItem"
                    @delete="deleteItem"
                    @cancel="cancelItem"
                />
            </template>
        </DataTable>

        <!-- Form Modal -->
        <StockInFormModal
            :is-open="showAddModal || !!editingItem"
            :editing-item="editingItem"
            :location-options="locationOptions"
            :saving="saving"
            @close="closeModal"
            @save="saveItem"
        />

        <!-- Detail Modal -->
        <StockInDetailModal
            :is-open="showDetailsModal"
            :item="selectedItem"
            @close="showDetailsModal = false"
        />

        <!-- Delete Confirmation -->
        <ConfirmationModal
            :is-open="showDeleteModal"
            title="Hapus Stock Masuk"
            message="Apakah Anda yakin ingin menghapus transaksi stock masuk ini? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Hapus"
            cancel-text="Batal"
            :loading="deleting"
            @confirm="confirmDelete"
            @cancel="showDeleteModal = false"
        />

        <!-- Post Confirmation -->
        <ConfirmationModal
            :is-open="showPostModal"
            title="Post Stock Masuk"
            :message="`Apakah Anda yakin ingin memposting transaksi ${postingItem?.transaction_number || ''}? Stock akan masuk ke sistem dan tidak dapat diubah lagi.`"
            confirm-text="Post"
            cancel-text="Batal"
            :loading="posting"
            @confirm="confirmPost"
            @cancel="cancelPost"
        />

        <!-- Cancel Confirmation -->
        <ConfirmationModal
            :is-open="showCancelModal"
            title="Batalkan Stock Masuk"
            :message="`Apakah Anda yakin ingin membatalkan transaksi ${cancellingItem?.transaction_number || ''}? Stock yang sudah masuk akan dikembalikan dari sistem.`"
            confirm-text="Batalkan"
            cancel-text="Tidak"
            :loading="cancelling"
            @confirm="confirmCancel"
            @cancel="cancelCancellation"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useNotificationStore } from "@/stores/notification";
import { useStockIn } from "@/composables/useStockIn";
import { useSelectOptions } from "@/composables/useSelectOptions";
import { PlusIcon, FunnelIcon } from "@heroicons/vue/24/outline";

// Components
import PageHeader from "@/components/Warehouse/PageHeader.vue";
import StockInStats from "@/components/Warehouse/StockInStats.vue";
import StockInFilters from "@/components/Warehouse/StockInFilters.vue";
import StockInFormModal from "@/components/Warehouse/StockInFormModal.vue";
import StockInDetailModal from "@/components/Warehouse/StockInDetailModal.vue";
import StockInActions from "@/components/Warehouse/StockInActions.vue";
import DataTable from "@/components/UI/DataTable.vue";
import ConfirmationModal from "@/components/Overlays/ConfirmationModal.vue";

// Composables
const notificationStore = useNotificationStore();
const {
    stockIns,
    loading,
    pagination,
    statistics,
    fetchStockIns,
    fetchStockIn,
    createStockIn,
    updateStockIn,
    deleteStockIn,
    postStockIn,
    cancelStockIn,
    fetchStatistics,
    getStatusLabel,
    formatCurrency,
    formatNumber,
} = useStockIn();

const { locationOptions, loadLocations } = useSelectOptions();

// State
const showAddModal = ref(false);
const editingItem = ref(null);
const showDeleteModal = ref(false);
const deletingItem = ref(null);
const deleting = ref(false);
const showDetailsModal = ref(false);
const selectedItem = ref(null);
const saving = ref(false);
const refreshing = ref(false);
const showFiltersPanel = ref(false);

// State untuk Post Modal
const showPostModal = ref(false);
const postingItem = ref(null);
const posting = ref(false);

// State untuk Cancel Modal
const showCancelModal = ref(false);
const cancellingItem = ref(null);
const cancelling = ref(false);

// Filters
const filters = ref({
    search: "",
    status: "",
    location_id: "",
    product_id: "",
    per_page: 15,
    page: 1,
    sort_by: "transaction_date",
    sort_order: "desc",
});

// Table Configuration
const tableColumns = [
    {
        key: "transaction_number",
        label: "No. Transaksi",
        sortable: true,
    },
    {
        key: "transaction_date",
        label: "Tanggal",
        sortable: true,
        type: "date",
    },
    {
        key: "location.name",
        label: "Lokasi",
    },
    {
        key: "supplier_name",
        label: "Supplier",
    },
    {
        key: "details_count",
        label: "Jumlah Item",
        align: "center",
        formatter: (value) => `${value || 0} item`,
    },
    {
        key: "total_quantity",
        label: "Total Qty",
        align: "right",
        formatter: (_value, row) => {
            const totalQty =
                row.details?.reduce(
                    (sum, detail) => sum + (detail.quantity || 0),
                    0
                ) || 0;
            return formatNumber(totalQty);
        },
    },
    {
        key: "total_price",
        label: "Total Harga",
        sortable: true,
        align: "right",
        formatter: (value) => formatCurrency(value),
    },
    {
        key: "status",
        label: "Status",
        type: "badge",
        badgeColors: {
            draft: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
            posted: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
            cancelled:
                "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        },
        formatter: (value) => getStatusLabel(value),
    },
];

// Methods
const handleAddClick = () => {
    showAddModal.value = true;
};

const loadData = async () => {
    try {
        await fetchStockIns(filters.value);
        await fetchStatistics(filters.value);
    } catch (error) {
        notificationStore.error(error.message || "Gagal memuat data");
    }
};

const handleRefresh = async () => {
    refreshing.value = true;
    await loadData();
    refreshing.value = false;
};

const handlePageChange = (page) => {
    filters.value.page = page;
    loadData();
};

const handleSort = ({ column, direction }) => {
    filters.value.sort_by = column;
    filters.value.sort_order = direction;
    filters.value.page = 1;
    loadData();
};

const handleSearch = (searchQuery) => {
    filters.value.search = searchQuery;
    filters.value.page = 1;
    loadData();
};

const handleFilterUpdate = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters };
    filters.value.page = 1;
    loadData();
};

const resetFilters = () => {
    filters.value = {
        search: "",
        status: "",
        location_id: "",
        product_id: "",
        per_page: 15,
        page: 1,
        sort_by: "transaction_date",
        sort_order: "desc",
    };
    loadData();
};

const viewDetails = async (item) => {
    console.log('viewDetails called, no refresh needed');
    // Set modal open first dengan data basic dari table
    selectedItem.value = item;
    showDetailsModal.value = true;

    // Kemudian fetch data lengkap di background
    try {
        const data = await fetchStockIn(item.id);
        selectedItem.value = data;
    } catch (error) {
        notificationStore.error("Gagal memuat detail");
        showDetailsModal.value = false;
        selectedItem.value = null;
    }
};

const editItem = async (item) => {
    console.log('editItem called with:', item);

    // Validasi hanya draft yang bisa di-edit
    if (item.status !== 'draft') {
        notificationStore.warning("Hanya stock masuk dengan status draft yang dapat diedit");
        return;
    }

    // Set editingItem dengan data basic terlebih dahulu untuk membuka modal
    editingItem.value = {
        id: item.id,
        transaction_date: item.transaction_date,
        location_id: item.location_id,
        supplier_name: item.supplier_name,
        reference_number: item.reference_number || "",
        notes: item.notes || "",
        details: item.details || [],
    };

    console.log('Modal should open now, editingItem:', editingItem.value);

    try {
        // Fetch data lengkap dari server di background
        const data = await fetchStockIn(item.id);

        console.log('Fetched full data:', data);
        console.log('Response type:', typeof data, 'Has details:', !!data?.details);

        // Handle jika data undefined atau null
        if (!data) {
            throw new Error('Data tidak ditemukan');
        }

        // Update editing item dengan data lengkap
        editingItem.value = {
            id: data.id || item.id,
            transaction_date: data.transaction_date || item.transaction_date,
            location_id: data.location_id || item.location_id,
            supplier_name: data.supplier_name || item.supplier_name,
            reference_number: data.reference_number || "",
            notes: data.notes || "",
            // Pastikan details ada dan dalam format yang benar
            details: Array.isArray(data.details) ? data.details : (data.stock_in_details || []),
        };

        console.log('Updated editingItem with full data:', editingItem.value);
    } catch (error) {
        console.error('Error fetching stock in for edit:', error);
        notificationStore.error(error.message || "Gagal memuat data untuk diedit");
        // Jangan set null agar modal tetap terbuka dengan data basic
        // editingItem.value = null;
    }
};

const deleteItem = (item) => {
    deletingItem.value = item;
    showDeleteModal.value = true;
};

const postItem = (item) => {
    // Validasi hanya draft yang bisa di-post
    if (item.status !== 'draft') {
        notificationStore.warning("Hanya stock masuk dengan status draft yang dapat diposting");
        return;
    }

    // Tampilkan modal konfirmasi
    postingItem.value = item;
    showPostModal.value = true;
};

const confirmPost = async () => {
    posting.value = true;
    try {
        const result = await postStockIn(postingItem.value.id);
        notificationStore.success("Stock masuk berhasil diposting. Stock telah masuk ke sistem.");

        // Update status di array tanpa full refresh
        const index = stockIns.value.findIndex(i => i.id === postingItem.value.id);
        if (index !== -1) {
            stockIns.value[index] = {
                ...stockIns.value[index],
                status: 'posted',
                posted_at: result?.data?.posted_at || new Date().toISOString(),
                posted_by: result?.data?.posted_by
            };
        }

        // Hanya refresh statistics
        await fetchStatistics(filters.value);

        // Tutup modal
        showPostModal.value = false;
        postingItem.value = null;

        console.log('confirmPost: Updated status locally, no full refresh needed');
    } catch (error) {
        notificationStore.error(
            error.message || "Gagal memposting stock masuk"
        );
    } finally {
        posting.value = false;
    }
};

const cancelPost = () => {
    showPostModal.value = false;
    postingItem.value = null;
};

const cancelItem = (item) => {
    // Validasi hanya posted yang bisa di-cancel
    if (item.status !== 'posted') {
        notificationStore.warning("Hanya stock masuk dengan status posted yang dapat dibatalkan");
        return;
    }

    // Tampilkan modal konfirmasi
    cancellingItem.value = item;
    showCancelModal.value = true;
};

const confirmCancel = async () => {
    cancelling.value = true;
    try {
        const result = await cancelStockIn(cancellingItem.value.id);
        notificationStore.success("Stock masuk berhasil dibatalkan. Stock telah dikembalikan dari sistem.");

        // Update status di array tanpa full refresh
        const index = stockIns.value.findIndex(i => i.id === cancellingItem.value.id);
        if (index !== -1) {
            stockIns.value[index] = {
                ...stockIns.value[index],
                status: 'cancelled',
                cancelled_at: result?.data?.cancelled_at || new Date().toISOString(),
            };
        }

        // Hanya refresh statistics
        await fetchStatistics(filters.value);

        // Tutup modal
        showCancelModal.value = false;
        cancellingItem.value = null;

        console.log('confirmCancel: Updated status locally, no full refresh needed');
    } catch (error) {
        notificationStore.error(
            error.message || "Gagal membatalkan stock masuk"
        );
    } finally {
        cancelling.value = false;
    }
};

const cancelCancellation = () => {
    showCancelModal.value = false;
    cancellingItem.value = null;
};

const saveItem = async (formData) => {
    saving.value = true;
    try {
        if (editingItem.value) {
            await updateStockIn(editingItem.value.id, formData);
            notificationStore.success("Stock masuk berhasil diupdate");
        } else {
            await createStockIn(formData);
            notificationStore.success("Stock masuk berhasil dibuat");
        }

        closeModal();

        // Refresh data table dan statistics
        await loadData();

        console.log('saveItem: Data refreshed successfully');
    } catch (error) {
        notificationStore.error(error.message || "Gagal menyimpan data");
    } finally {
        saving.value = false;
    }
};

const confirmDelete = async () => {
    deleting.value = true;
    try {
        await deleteStockIn(deletingItem.value.id);
        notificationStore.success("Stock masuk berhasil dihapus");

        // Hapus dari array tanpa full refresh
        const index = stockIns.value.findIndex(item => item.id === deletingItem.value.id);
        if (index !== -1) {
            stockIns.value.splice(index, 1);
            pagination.value.total -= 1;
        }

        showDeleteModal.value = false;
        deletingItem.value = null;

        // Hanya refresh statistics
        await fetchStatistics(filters.value);

        console.log('confirmDelete: Removed item locally, no full refresh needed');
    } catch (error) {
        notificationStore.error(error.message || "Gagal menghapus stock masuk");
    } finally {
        deleting.value = false;
    }
};

const closeModal = () => {
    console.log('closeModal called, no refresh needed');
    showAddModal.value = false;
    editingItem.value = null;
    // TIDAK memanggil loadData() saat close modal tanpa save
};

// Lifecycle
onMounted(async () => {
    await loadLocations();
    await loadData();
});
</script>
