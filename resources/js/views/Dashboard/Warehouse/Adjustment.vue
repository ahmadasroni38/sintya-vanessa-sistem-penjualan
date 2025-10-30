<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Stock Adjustment
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Manage stock adjustments and corrections
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="exportData"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Export
                </button>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <PlusIcon class="w-4 h-4" />
                    New Adjustment
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total This Month
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ stats.totalThisMonth }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <AdjustmentsHorizontalIcon
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pending Approval
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ stats.pending }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ClockIcon
                            class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                        />
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Increase
                        </p>
                        <p
                            class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1"
                        >
                            {{ stats.increase }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ArrowTrendingUpIcon
                            class="w-6 h-6 text-green-600 dark:text-green-400"
                        />
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Decrease
                        </p>
                        <p
                            class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1"
                        >
                            {{ stats.decrease }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ArrowTrendingDownIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Adjustments Table -->
        <DataTable
            title="Stock Adjustments"
            :data="adjustmentList"
            :columns="columns"
            :loading="loading"
            @add="showAddModal = true"
            @edit="editAdjustment"
            @delete="deleteAdjustment"
            @refresh="loadAdjustments"
            :show-refresh="true"
            :refresh-loading="refreshing"
            search-placeholder="Search adjustments..."
        >
            <template #column-type="{ value }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        value === 'increase'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                    ]"
                >
                    <ArrowTrendingUpIcon
                        v-if="value === 'increase'"
                        class="w-3 h-3 mr-1"
                    />
                    <ArrowTrendingDownIcon v-else class="w-3 h-3 mr-1" />
                    {{ value }}
                </span>
            </template>

            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="viewDetails(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="editAdjustment(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="Edit"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="approveAdjustment(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Approve"
                    >
                        <CheckIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="deleteAdjustment(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Add/Edit Adjustment Modal -->
        <Modal
            v-if="showAddModal || editingAdjustment"
            :title="
                editingAdjustment ? 'Edit Adjustment' : 'New Stock Adjustment'
            "
            size="large"
            @close="closeModal"
        >
            <form @submit.prevent="saveAdjustment" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormInput
                        v-model="form.code"
                        label="Transaction Code"
                        placeholder="e.g., ADJ000001"
                        required
                        :disabled="!!editingAdjustment"
                    />
                    <FormInput
                        v-model="form.date"
                        label="Date"
                        type="date"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormSelect
                        v-model="form.location_id"
                        label="Location"
                        :options="locationOptions"
                        required
                    />
                    <FormSelect
                        v-model="form.type"
                        label="Adjustment Type"
                        :options="typeOptions"
                        required
                        @change="onTypeChange"
                    />
                </div>

                <FormInput
                    v-model="form.reason"
                    label="Reason"
                    placeholder="Enter adjustment reason"
                    required
                />

                <FormTextarea
                    v-model="form.notes"
                    label="Notes"
                    placeholder="Enter additional notes"
                    rows="3"
                />

                <!-- Products Section -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            Products
                        </h3>
                        <button
                            type="button"
                            @click="addProduct"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                        >
                            <PlusIcon class="w-4 h-4" />
                            Add Product
                        </button>
                    </div>

                    <div
                        v-if="form.details.length === 0"
                        class="text-center py-8 text-gray-500 dark:text-gray-400"
                    >
                        No products added yet. Click "Add Product" to start.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="(detail, index) in form.details"
                            :key="index"
                            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <FormSelect
                                    v-model="detail.product_id"
                                    label="Product"
                                    :options="productOptions"
                                    required
                                    @change="updateProductDetail(detail)"
                                />
                                <FormInput
                                    v-model="detail.system_quantity"
                                    label="System Quantity"
                                    type="number"
                                    min="0"
                                    required
                                    @input="calculateAdjustment(detail)"
                                />
                                <FormInput
                                    v-model="detail.actual_quantity"
                                    label="Actual Quantity"
                                    type="number"
                                    min="0"
                                    required
                                    @input="calculateAdjustment(detail)"
                                />
                                <div class="flex items-end">
                                    <div class="w-full">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                        >
                                            Adjustment
                                        </label>
                                        <div
                                            :class="[
                                                'text-lg font-semibold',
                                                detail.adjustment_quantity > 0
                                                    ? 'text-green-600 dark:text-green-400'
                                                    : detail.adjustment_quantity <
                                                      0
                                                    ? 'text-red-600 dark:text-red-400'
                                                    : 'text-gray-900 dark:text-white',
                                            ]"
                                        >
                                            {{
                                                detail.adjustment_quantity > 0
                                                    ? "+"
                                                    : ""
                                            }}{{ detail.adjustment_quantity }}
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeProduct(index)"
                                        class="ml-2 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:bg-red-900/20"
                                    >
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <FormInput
                                v-model="detail.reason"
                                label="Detail Reason"
                                placeholder="Enter reason for this adjustment"
                                class="mt-3"
                            />
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <div
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Total Items: {{ form.details.length }}
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="saving || !isFormValid"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="saving">Saving...</span>
                            <span v-else>{{
                                editingAdjustment ? "Update" : "Save"
                            }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </Modal>

        <!-- Details Modal -->
        <Modal
            v-if="showDetailsModal"
            title="Adjustment Details"
            size="large"
            @close="showDetailsModal = false"
        >
            <div v-if="selectedAdjustment" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Transaction Code
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedAdjustment.code }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Date
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ formatDate(selectedAdjustment.date) }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Location
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedAdjustment.location_name }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Type
                        </label>
                        <span
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1',
                                selectedAdjustment.type === 'increase'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                    : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                            ]"
                        >
                            {{ selectedAdjustment.type }}
                        </span>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Reason
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedAdjustment.reason }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Status
                        </label>
                        <span
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1',
                                getStatusBadgeClass(selectedAdjustment.status),
                            ]"
                        >
                            {{ selectedAdjustment.status }}
                        </span>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Notes
                    </label>
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ selectedAdjustment.notes || "-" }}
                    </p>
                </div>

                <div>
                    <h4
                        class="text-lg font-medium text-gray-900 dark:text-white mb-3"
                    >
                        Products
                    </h4>
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Product Code
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Product Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        System Qty
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Actual Qty
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Adjustment
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Reason
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="detail in selectedAdjustment.details"
                                    :key="detail.id"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.product_code }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.product_name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.system_quantity }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.actual_quantity }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                    >
                                        <span
                                            :class="[
                                                'font-semibold',
                                                detail.adjustment_quantity > 0
                                                    ? 'text-green-600 dark:text-green-400'
                                                    : detail.adjustment_quantity <
                                                      0
                                                    ? 'text-red-600 dark:text-red-400'
                                                    : 'text-gray-900 dark:text-white',
                                            ]"
                                        >
                                            {{
                                                detail.adjustment_quantity > 0
                                                    ? "+"
                                                    : ""
                                            }}{{ detail.adjustment_quantity }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.reason || "-" }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            v-if="showDeleteModal"
            title="Delete Adjustment"
            message="Are you sure you want to delete this stock adjustment? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            @confirm="confirmDelete"
            @cancel="showDeleteModal = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useNotificationStore } from "../../../stores/notification";
import DataTable from "../../../components/UI/DataTable.vue";
import Modal from "../../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../../components/Forms/FormInput.vue";
import FormSelect from "../../../components/Forms/FormSelect.vue";
import FormTextarea from "../../../components/Forms/FormTextarea.vue";
import {
    PlusIcon,
    ArrowDownTrayIcon,
    AdjustmentsHorizontalIcon,
    ClockIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    EyeIcon,
    PencilIcon,
    CheckIcon,
    TrashIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import {
    stockAdjustmentService,
    dummyDataService,
} from "../../../services/warehouseService";

const notificationStore = useNotificationStore();

// State
const loading = ref(false);
const refreshing = ref(false);
const saving = ref(false);
const adjustmentList = ref([]);
const showAddModal = ref(false);
const editingAdjustment = ref(null);
const showDeleteModal = ref(false);
const deletingAdjustment = ref(null);
const showDetailsModal = ref(false);
const selectedAdjustment = ref(null);

// Dummy data
const products = ref([]);
const locations = ref([]);

// Form
const form = ref({
    code: "",
    date: new Date().toISOString().split("T")[0],
    location_id: "",
    type: "increase",
    reason: "",
    notes: "",
    details: [],
});

// Options
const locationOptions = computed(() =>
    locations.value.map((loc) => ({ value: loc.id, label: loc.name }))
);

const productOptions = computed(() =>
    products.value.map((product) => ({
        value: product.id,
        label: `${product.code} - ${product.name}`,
    }))
);

const typeOptions = [
    { value: "increase", label: "Increase" },
    { value: "decrease", label: "Decrease" },
];

// Table columns
const columns = [
    {
        key: "code",
        label: "Code",
        sortable: true,
    },
    {
        key: "date",
        label: "Date",
        sortable: true,
        type: "date",
    },
    {
        key: "location_name",
        label: "Location",
        sortable: true,
    },
    {
        key: "type",
        label: "Type",
        sortable: true,
    },
    {
        key: "reason",
        label: "Reason",
        sortable: true,
    },
    {
        key: "total_items",
        label: "Items",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
        type: "badge",
    },
    {
        key: "created_by",
        label: "Created By",
        sortable: true,
    },
    {
        key: "created_at",
        label: "Created At",
        sortable: true,
        type: "date",
    },
];

// Computed
const stats = computed(() => {
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();

    const thisMonth = adjustmentList.value.filter((item) => {
        const itemDate = new Date(item.date);
        return (
            itemDate.getMonth() === currentMonth &&
            itemDate.getFullYear() === currentYear
        );
    });

    const pending = adjustmentList.value.filter(
        (item) => item.status === "draft"
    ).length;
    const increase = adjustmentList.value.filter(
        (item) => item.type === "increase"
    ).length;
    const decrease = adjustmentList.value.filter(
        (item) => item.type === "decrease"
    ).length;

    return {
        totalThisMonth: thisMonth.length,
        pending,
        increase,
        decrease,
    };
});

const isFormValid = computed(() => {
    return (
        form.value.location_id &&
        form.value.type &&
        form.value.reason &&
        form.value.details.length > 0 &&
        form.value.details.every(
            (detail) =>
                detail.product_id &&
                detail.system_quantity >= 0 &&
                detail.actual_quantity >= 0
        )
    );
});

// Methods
const loadAdjustments = async () => {
    loading.value = true;
    try {
        // Load dummy data
        products.value = dummyDataService.generateProducts();
        locations.value = dummyDataService.generateLocations();
        adjustmentList.value = dummyDataService.generateStockAdjustments(
            products.value,
            locations.value
        );

        // Calculate adjustment quantities for details
        adjustmentList.value.forEach((item) => {
            item.details.forEach((detail) => {
                detail.adjustment_quantity =
                    detail.actual_quantity - detail.system_quantity;
            });
        });
    } catch (error) {
        notificationStore.error("Failed to load adjustment data");
    } finally {
        loading.value = false;
    }
};

const refreshAdjustments = async () => {
    refreshing.value = true;
    await loadAdjustments();
    refreshing.value = false;
};

const editAdjustment = (adjustment) => {
    editingAdjustment.value = adjustment;
    form.value = {
        ...adjustment,
        details: [...adjustment.details],
    };
};

const deleteAdjustment = (adjustment) => {
    deletingAdjustment.value = adjustment;
    showDeleteModal.value = true;
};

const viewDetails = (adjustment) => {
    selectedAdjustment.value = adjustment;
    showDetailsModal.value = true;
};

const approveAdjustment = async (adjustment) => {
    try {
        const index = adjustmentList.value.findIndex(
            (item) => item.id === adjustment.id
        );
        if (index !== -1) {
            adjustmentList.value[index].status = "approved";
        }

        notificationStore.success("Adjustment approved successfully");
    } catch (error) {
        notificationStore.error("Failed to approve adjustment");
    }
};

const confirmDelete = async () => {
    try {
        adjustmentList.value = adjustmentList.value.filter(
            (item) => item.id !== deletingAdjustment.value.id
        );
        notificationStore.success("Adjustment deleted successfully");
    } catch (error) {
        notificationStore.error("Failed to delete adjustment");
    } finally {
        showDeleteModal.value = false;
        deletingAdjustment.value = null;
    }
};

const saveAdjustment = async () => {
    if (!isFormValid.value) {
        notificationStore.error("Please fill all required fields correctly");
        return;
    }

    saving.value = true;
    try {
        if (editingAdjustment.value) {
            // Update existing adjustment
            const index = adjustmentList.value.findIndex(
                (item) => item.id === editingAdjustment.value.id
            );
            if (index !== -1) {
                adjustmentList.value[index] = {
                    ...form.value,
                    id: editingAdjustment.value.id,
                    total_items: form.value.details.length,
                    updated_at: new Date().toISOString(),
                };
            }
            notificationStore.success("Adjustment updated successfully");
        } else {
            // Create new adjustment
            const newAdjustment = {
                ...form.value,
                id:
                    Math.max(
                        ...adjustmentList.value.map((item) => item.id),
                        0
                    ) + 1,
                total_items: form.value.details.length,
                status: "draft",
                created_by: "Current User",
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
            };
            adjustmentList.value.unshift(newAdjustment);
            notificationStore.success("Adjustment created successfully");
        }
        closeModal();
    } catch (error) {
        notificationStore.error("Failed to save adjustment");
    } finally {
        saving.value = false;
    }
};

const addProduct = () => {
    form.value.details.push({
        product_id: "",
        product_name: "",
        product_code: "",
        system_quantity: 0,
        actual_quantity: 0,
        adjustment_quantity: 0,
        reason: "",
    });
};

const removeProduct = (index) => {
    form.value.details.splice(index, 1);
};

const updateProductDetail = (detail) => {
    const product = products.value.find((p) => p.id === detail.product_id);
    if (product) {
        detail.product_name = product.name;
        detail.product_code = product.code;
    }
};

const calculateAdjustment = (detail) => {
    detail.adjustment_quantity =
        detail.actual_quantity - detail.system_quantity;
};

const onTypeChange = () => {
    // Reset details when type changes
    form.value.details = [];
};

const closeModal = () => {
    showAddModal.value = false;
    editingAdjustment.value = null;
    form.value = {
        code: "",
        date: new Date().toISOString().split("T")[0],
        location_id: "",
        type: "increase",
        reason: "",
        notes: "",
        details: [],
    };
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        approved:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        rejected:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return (
        classes[status] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("id-ID");
};

const exportData = () => {
    notificationStore.info("Export feature coming soon");
};

onMounted(() => {
    loadAdjustments();
});
</script>
