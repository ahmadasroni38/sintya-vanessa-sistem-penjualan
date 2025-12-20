<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $t('customers.title') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    {{ $t('customers.subtitle') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="handleExport"
                    :disabled="exporting"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <svg
                        v-if="exporting"
                        class="animate-spin -ml-1 mr-2 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    <ArrowDownTrayIcon v-else class="w-4 h-4" />
                    {{ exporting ? $t('customers.exporting') : $t('customers.export') }}
                </button>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <PlusIcon class="w-4 h-4" />
                    {{ $t('customers.newCustomer') }}
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <CustomerStats :stats="stats" :loading="statsLoading" />

        <!-- Customers Table -->
        <DataTable
            :title="$t('customers.customersTable')"
            :data="customers"
            :columns="columns"
            :loading="loading"
            :server-side-pagination="true"
            :pagination="pagination"
            @edit="handleEdit"
            @delete="handleDelete"
            @refresh="handleRefresh"
            @page-change="handlePageChange"
            @sort="handleSort"
            :show-refresh="true"
            :refresh-loading="refreshing"
            :showAddButton="false"
            :showFilters="true"
            :showExport="false"
        >
            <!-- Custom Actions Slot -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <!-- View Details Button -->
                    <button
                        type="button"
                        @click.stop.prevent="handleCustomAction('view', item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        :title="$t('customers.viewDetails')"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>

                    <!-- Edit Button -->
                    <button
                        type="button"
                        @click.stop.prevent="handleEdit(item)"
                        class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors duration-200 dark:hover:text-amber-400 dark:hover:bg-amber-900/20"
                        :title="$t('customers.editCustomer')"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>

                    <!-- Toggle Status Button -->
                    <button
                        type="button"
                        @click.stop.prevent="showToggleStatusConfirmation(item)"
                        class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 dark:hover:text-purple-400 dark:hover:bg-purple-900/20"
                        :title="
                            item.status === 'active'
                                ? $t('customers.setInactive')
                                : $t('customers.setActive')
                        "
                    >
                        <ArrowPathIcon class="w-4 h-4" />
                    </button>

                    <!-- Delete Button -->
                    <button
                        type="button"
                        @click.stop.prevent="handleDelete(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        :title="$t('customers.deleteCustomer')"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Add/Edit Customer Modal -->
        <CustomerFormModal
            :is-open="showAddModal || !!editingCustomer"
            :editing-item="editingCustomer"
            @close="closeModal"
            @saved="handleFormSave"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :is-open="showDeleteModal"
            :title="$t('customers.deleteCustomerTitle')"
            :message="`${$t('customers.deleteCustomerMessage')} ${
                deletingCustomer?.customer_name || $t('customers.thisCustomer')
            }?`"
            :description="$t('customers.deleteCustomerDescription')"
            :confirm-text="$t('customers.delete')"
            :cancel-text="$t('customers.cancel')"
            :loading="deleting"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />

        <!-- Toggle Status Confirmation Modal -->
        <ConfirmationModal
            :is-open="showToggleStatusModal"
            :title="
                togglingCustomer?.status === 'active'
                    ? $t('customers.deactivateCustomer')
                    : $t('customers.activateCustomer')
            "
            :message="`${
                togglingCustomer?.status === 'active'
                    ? $t('customers.deactivateMessage')
                    : $t('customers.activateMessage')
            } ${togglingCustomer?.customer_name || $t('customers.thisCustomer')}?`"
            :description="$t('customers.toggleStatusDescription')"
            :confirm-text="$t('customers.confirm')"
            :cancel-text="$t('customers.cancel')"
            :loading="toggleStatusLoading"
            @confirm="confirmToggleStatus"
            @cancel="cancelToggleStatus"
        />

        <!-- Customer Details Modal -->
        <CustomerDetailsModal
            :is-open="showDetailsModal"
            :customer="viewingCustomer"
            :loading="customerDetailsLoading"
            @close="closeDetailsModal"
            @edit="handleEditFromModal"
            @delete="handleDeleteFromModal"
            @view-sale="handleViewSale"
        />

        <!-- Sales Details Modal -->
        <SalesDetailsModal
            :is-open="showSalesDetailsModal"
            :sale="viewingSale"
            @close="closeSalesDetailsModal"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useCustomers } from "@/composables/useCustomers";
import { useSales } from "@/composables/useSales";
import DataTable from "@/components/UI/DataTable.vue";
import ConfirmationModal from "@/components/Overlays/ConfirmationModal.vue";
import CustomerStats from "@/components/Customers/CustomerStats.vue";
import CustomerFormModal from "@/components/Customers/CustomerFormModal.vue";
import CustomerDetailsModal from "@/components/Customers/CustomerDetailsModal.vue";
import SalesDetailsModal from "@/components/Sales/SalesDetailsModal.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/outline";
import { useNotificationStore } from "@/stores/notification";

const { t } = useI18n();

// Use Customers Composable
const {
    customers: customersData,
    loading,
    pagination: paginationData,
    fetchCustomers,
    fetchCustomer,
    createCustomer,
    updateCustomer,
    deleteCustomer: deleteCustomerApi,
    toggleCustomerStatus,
    fetchCustomerStats,
    exportCustomers,
} = useCustomers();

// Use Sales Composable
const { fetchSale } = useSales();

const notificationStore = useNotificationStore();

// Local State
const refreshing = ref(false);
const exporting = ref(false);
const deleting = ref(false);
const statsLoading = ref(false);
const showAddModal = ref(false);
const editingCustomer = ref(null);
const showDeleteModal = ref(false);
const deletingCustomer = ref(null);
const showDetailsModal = ref(false);
const viewingCustomer = ref(null);
const customerDetailsLoading = ref(false);
const showSalesDetailsModal = ref(false);
const viewingSale = ref(null);
const toggleStatusLoading = ref(false);
const showToggleStatusModal = ref(false);
const togglingCustomer = ref(null);
const stats = ref({
    total_customers: 0,
    active_customers: 0,
    inactive_customers: 0,
    new_customers_this_month: 0,
});

// Computed
const customers = computed(() => customersData.value);
const pagination = computed(() => paginationData.value);

// Table columns
const columns = computed(() => [
    {
        key: "customer_code",
        label: t("customers.code"),
        sortable: true,
    },
    {
        key: "customer_name",
        label: t("customers.name"),
        sortable: true,
    },
    {
        key: "phone",
        label: t("customers.phone"),
        sortable: false,
        format: (value) => value || "-",
    },
    {
        key: "email",
        label: t("customers.email"),
        sortable: false,
        format: (value) => value || "-",
    },
    {
        key: "status",
        label: t("customers.status"),
        sortable: true,
        format: (value) => {
            const statuses = {
                active: t("customers.statusActive"),
                inactive: t("customers.statusInactive"),
            };
            return statuses[value] || value;
        },
        class: (value) => getStatusClass(value),
    },
    {
        key: "created_at",
        label: t("customers.created"),
        sortable: true,
        format: (value) => formatDate(value),
    },
]);

// Event Handlers
const handleRefresh = async () => {
    refreshing.value = true;
    statsLoading.value = true;
    try {
        await fetchCustomers({
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
        });
        await fetchCustomerStats().then((data) => {
            stats.value = data;
        });
        notificationStore.success(t("customers.refreshSuccess"));
    } catch (error) {
        notificationStore.error(t("customers.refreshError"), error.message);
    } finally {
        refreshing.value = false;
        statsLoading.value = false;
    }
};

const handlePageChange = (page) => {
    fetchCustomers({
        page,
        per_page: pagination.value.per_page,
    });
};

const handleSort = ({ column, direction }) => {
    fetchCustomers({
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
        sort_by: column,
        sort_order: direction,
    });
};

const handleEdit = async (customer) => {
    editingCustomer.value = { ...customer };
};

const handleDelete = (customer) => {
    deletingCustomer.value = customer;
    showDeleteModal.value = true;
};

const handleCustomAction = async (action, customer) => {
    if (action === "view") {
        customerDetailsLoading.value = true;
        showDetailsModal.value = true;
        try {
            const response = await fetchCustomer(customer.id);
            viewingCustomer.value = response.data;
        } catch (error) {
            notificationStore.error(
                t("customers.detailsError"),
                error.message
            );
        } finally {
            customerDetailsLoading.value = false;
        }
    } else if (action === "toggle-status") {
        // Show confirmation modal instead of directly toggling
        showToggleStatusConfirmation(customer);
    }
};

const showToggleStatusConfirmation = (customer) => {
    togglingCustomer.value = customer;
    showToggleStatusModal.value = true;
};

const confirmToggleStatus = async () => {
    toggleStatusLoading.value = true;
    try {
        await toggleCustomerStatus(togglingCustomer.value.id);
        notificationStore.success(t("customers.statusUpdateSuccess"));
        await handleRefresh();
        cancelToggleStatus();
    } catch (error) {
        notificationStore.error(
            t("customers.statusError"),
            error.message
        );
    } finally {
        toggleStatusLoading.value = false;
    }
};

const cancelToggleStatus = () => {
    showToggleStatusModal.value = false;
    togglingCustomer.value = null;
};

const handleExport = async () => {
    exporting.value = true;
    try {
        await exportCustomers();
        notificationStore.success(t("customers.exportSuccess"));
    } catch (error) {
        notificationStore.error(t("customers.exportError"), error.message);
    } finally {
        exporting.value = false;
    }
};

const handleFormSave = async (customerData) => {
    try {
        if (editingCustomer.value) {
            await updateCustomer(editingCustomer.value.id, customerData);
            notificationStore.success(t("customers.updateSuccess"));
        } else {
            await createCustomer(customerData);
            notificationStore.success(t("customers.createSuccess"));
        }
        closeModal();
        await handleRefresh();
    } catch (error) {
        notificationStore.error(t("customers.saveError"), error.message);
    }
};

const confirmDelete = async () => {
    deleting.value = true;
    try {
        await deleteCustomerApi(deletingCustomer.value.id);
        notificationStore.success(t("customers.deleteSuccess"));
        showDeleteModal.value = false;
        deletingCustomer.value = null;
        await handleRefresh();
    } catch (error) {
        notificationStore.error(t("customers.deleteError"), error.message);
    } finally {
        deleting.value = false;
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deletingCustomer.value = null;
};

const closeModal = () => {
    showAddModal.value = false;
    editingCustomer.value = null;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    viewingCustomer.value = null;
};

const handleEditFromModal = (customer) => {
    closeDetailsModal();
    handleEdit(customer);
};

const handleDeleteFromModal = (customer) => {
    closeDetailsModal();
    handleDelete(customer);
};

const handleViewSale = async (sale) => {
    try {
        const fullSale = await fetchSale(sale.id);
        viewingSale.value = fullSale;
        showSalesDetailsModal.value = true;
    } catch (error) {
        notificationStore.error(t("customers.detailsError"), error.message);
    }
};

const closeSalesDetailsModal = () => {
    showSalesDetailsModal.value = false;
    viewingSale.value = null;
};

// Utility Functions
const getStatusClass = (status) => {
    const classes = {
        active: "inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        inactive:
            "inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200",
    };
    return classes[status] || classes.inactive;
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

// Lifecycle
onMounted(async () => {
    statsLoading.value = true;
    try {
        await fetchCustomers();
        await fetchCustomerStats().then((data) => {
            stats.value = data;
        });
    } finally {
        statsLoading.value = false;
    }
});
</script>
