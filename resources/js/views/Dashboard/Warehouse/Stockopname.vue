<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Stock Opname
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Manage stock counting and inventory verification
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
                    New Stock Opname
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
                            This Month
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ stats.thisMonth }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ClipboardDocumentListIcon
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
                            In Progress
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ stats.inProgress }}
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
                            Completed
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white mt-1"
                        >
                            {{ stats.completed }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center"
                    >
                        <CheckCircleIcon
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
                            With Variance
                        </p>
                        <p
                            class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1"
                        >
                            {{ stats.withVariance }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center"
                    >
                        <ExclamationTriangleIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Opnames Table -->
        <DataTable
            title="Stock Opnames"
            :data="stockOpnameList"
            :columns="columns"
            :loading="loading"
            @add="showAddModal = true"
            @edit="editStockOpname"
            @delete="deleteStockOpname"
            @refresh="loadStockOpnames"
            :show-refresh="true"
            :refresh-loading="refreshing"
            search-placeholder="Search stock opnames..."
        >
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
                        @click="editStockOpname(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="Edit"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="startCounting(item)"
                        class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 dark:hover:text-purple-400 dark:hover:bg-purple-900/20"
                        title="Start Counting"
                    >
                        <PlayIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'in_progress'"
                        @click="continueCounting(item)"
                        class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 dark:hover:text-purple-400 dark:hover:bg-purple-900/20"
                        title="Continue Counting"
                    >
                        <PencilSquareIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'in_progress'"
                        @click="finalizeStockOpname(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Finalize"
                    >
                        <CheckIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'completed'"
                        @click="approveStockOpname(item)"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                        title="Approve"
                    >
                        <CheckCircleIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="deleteStockOpname(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Add/Edit Stock Opname Modal -->
        <Modal
            v-if="showAddModal || editingStockOpname"
            :title="
                editingStockOpname ? 'Edit Stock Opname' : 'New Stock Opname'
            "
            size="large"
            @close="closeModal"
        >
            <form @submit.prevent="saveStockOpname" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormInput
                        v-model="form.code"
                        label="Opname Code"
                        placeholder="e.g., OP000001"
                        required
                        :disabled="!!editingStockOpname"
                    />
                    <FormInput
                        v-model="form.period"
                        label="Period"
                        placeholder="e.g., 2024-01"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <FormInput
                        v-model="form.date"
                        label="Date"
                        type="date"
                        required
                    />
                    <FormSelect
                        v-model="form.location_id"
                        label="Location"
                        :options="locationOptions"
                        required
                    />
                </div>

                <FormTextarea
                    v-model="form.notes"
                    label="Notes"
                    placeholder="Enter notes for this stock opname"
                    rows="3"
                />

                <div
                    class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="saving"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="saving">Saving...</span>
                        <span v-else>{{
                            editingStockOpname ? "Update" : "Save"
                        }}</span>
                    </button>
                </div>
            </form>
        </Modal>

        <!-- Counting Modal -->
        <Modal
            v-if="showCountingModal"
            :title="`Counting - ${countingStockOpname?.code}`"
            size="large"
            @close="showCountingModal = false"
        >
            <div v-if="countingStockOpname" class="space-y-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Location
                            </label>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ countingStockOpname.location_name }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Period
                            </label>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ countingStockOpname.period }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Progress
                            </label>
                            <p
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ countedItems }} / {{ totalItems }} items
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Products
                    </h3>
                    <div class="flex items-center gap-3">
                        <FormInput
                            v-model="searchProduct"
                            placeholder="Search products..."
                            class="w-64"
                        />
                        <button
                            @click="addProductToCount"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                        >
                            <PlusIcon class="w-4 h-4" />
                            Add Product
                        </button>
                    </div>
                </div>

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
                                    Counted Qty
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                >
                                    Variance
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                >
                                    Notes
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                        >
                            <tr
                                v-for="detail in filteredCountingDetails"
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <FormInput
                                        v-model="detail.counted_quantity"
                                        type="number"
                                        min="0"
                                        class="w-24"
                                        @input="calculateVariance(detail)"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'text-sm font-semibold',
                                            detail.variance > 0
                                                ? 'text-green-600 dark:text-green-400'
                                                : detail.variance < 0
                                                ? 'text-red-600 dark:text-red-400'
                                                : 'text-gray-900 dark:text-white',
                                        ]"
                                    >
                                        {{ detail.variance > 0 ? "+" : ""
                                        }}{{ detail.variance }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <FormInput
                                        v-model="detail.notes"
                                        placeholder="Notes"
                                        class="w-32"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button
                                        @click="removeProductFromCount(detail)"
                                        class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="showCountingModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        Close
                    </button>
                    <button
                        @click="saveCountingProgress"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Save Progress
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Details Modal -->
        <Modal
            v-if="showDetailsModal"
            title="Stock Opname Details"
            size="large"
            @close="showDetailsModal = false"
        >
            <div v-if="selectedStockOpname" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Opname Code
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockOpname.code }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Period
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockOpname.period }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Date
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ formatDate(selectedStockOpname.date) }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Location
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockOpname.location_name }}
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
                                getStatusBadgeClass(selectedStockOpname.status),
                            ]"
                        >
                            {{ selectedStockOpname.status }}
                        </span>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Variance Items
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ selectedStockOpname.variance_items }}
                        </p>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Notes
                    </label>
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ selectedStockOpname.notes || "-" }}
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
                                        Counted Qty
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Variance
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Notes
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="detail in selectedStockOpname.details"
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
                                        {{ detail.counted_quantity }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                    >
                                        <span
                                            :class="[
                                                'font-semibold',
                                                detail.variance > 0
                                                    ? 'text-green-600 dark:text-green-400'
                                                    : detail.variance < 0
                                                    ? 'text-red-600 dark:text-red-400'
                                                    : 'text-gray-900 dark:text-white',
                                            ]"
                                        >
                                            {{ detail.variance > 0 ? "+" : ""
                                            }}{{ detail.variance }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.notes || "-" }}
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
            title="Delete Stock Opname"
            message="Are you sure you want to delete this stock opname? This action cannot be undone."
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
    ClipboardDocumentListIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    EyeIcon,
    PencilIcon,
    PlayIcon,
    PencilSquareIcon,
    CheckIcon,
    TrashIcon,
} from "@heroicons/vue/24/outline";
import {
    stockOpnameService,
    dummyDataService,
} from "../../../services/warehouseService";

const notificationStore = useNotificationStore();

// State
const loading = ref(false);
const refreshing = ref(false);
const saving = ref(false);
const stockOpnameList = ref([]);
const showAddModal = ref(false);
const editingStockOpname = ref(null);
const showDeleteModal = ref(false);
const deletingStockOpname = ref(null);
const showDetailsModal = ref(false);
const selectedStockOpname = ref(null);
const showCountingModal = ref(false);
const countingStockOpname = ref(null);
const searchProduct = ref("");

// Dummy data
const products = ref([]);
const locations = ref([]);

// Form
const form = ref({
    code: "",
    period: `${new Date().getFullYear()}-${String(
        new Date().getMonth() + 1
    ).padStart(2, "0")}`,
    date: new Date().toISOString().split("T")[0],
    location_id: "",
    notes: "",
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

// Table columns
const columns = [
    {
        key: "code",
        label: "Code",
        sortable: true,
    },
    {
        key: "period",
        label: "Period",
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
        key: "status",
        label: "Status",
        sortable: true,
        type: "badge",
    },
    {
        key: "total_items",
        label: "Total Items",
        sortable: true,
    },
    {
        key: "variance_items",
        label: "Variance Items",
        sortable: true,
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

    const thisMonth = stockOpnameList.value.filter((item) => {
        const itemDate = new Date(item.date);
        return (
            itemDate.getMonth() === currentMonth &&
            itemDate.getFullYear() === currentYear
        );
    });

    const inProgress = stockOpnameList.value.filter(
        (item) => item.status === "in_progress"
    ).length;
    const completed = stockOpnameList.value.filter(
        (item) => item.status === "completed" || item.status === "approved"
    ).length;
    const withVariance = stockOpnameList.value.filter(
        (item) => item.variance_items > 0
    ).length;

    return {
        thisMonth: thisMonth.length,
        inProgress,
        completed,
        withVariance,
    };
});

const countingDetails = computed(() => {
    return countingStockOpname.value?.details || [];
});

const filteredCountingDetails = computed(() => {
    if (!searchProduct.value) return countingDetails.value;

    const query = searchProduct.value.toLowerCase();
    return countingDetails.value.filter(
        (detail) =>
            detail.product_code.toLowerCase().includes(query) ||
            detail.product_name.toLowerCase().includes(query)
    );
});

const countedItems = computed(() => {
    return countingDetails.value.filter((detail) => detail.counted_quantity > 0)
        .length;
});

const totalItems = computed(() => {
    return countingDetails.value.length;
});

// Methods
const loadStockOpnames = async () => {
    loading.value = true;
    try {
        // Load dummy data
        products.value = dummyDataService.generateProducts();
        locations.value = dummyDataService.generateLocations();
        stockOpnameList.value = dummyDataService.generateStockOpnames(
            products.value,
            locations.value
        );

        // Calculate variance for details
        stockOpnameList.value.forEach((item) => {
            item.details.forEach((detail) => {
                detail.variance =
                    detail.counted_quantity - detail.system_quantity;
            });
        });
    } catch (error) {
        notificationStore.error("Failed to load stock opname data");
    } finally {
        loading.value = false;
    }
};

const refreshStockOpnames = async () => {
    refreshing.value = true;
    await loadStockOpnames();
    refreshing.value = false;
};

const editStockOpname = (stockOpname) => {
    editingStockOpname.value = stockOpname;
    form.value = { ...stockOpname };
};

const deleteStockOpname = (stockOpname) => {
    deletingStockOpname.value = stockOpname;
    showDeleteModal.value = true;
};

const viewDetails = (stockOpname) => {
    selectedStockOpname.value = stockOpname;
    showDetailsModal.value = true;
};

const startCounting = (stockOpname) => {
    countingStockOpname.value = { ...stockOpname };
    showCountingModal.value = true;
};

const continueCounting = (stockOpname) => {
    countingStockOpname.value = { ...stockOpname };
    showCountingModal.value = true;
};

const finalizeStockOpname = async (stockOpname) => {
    try {
        const index = stockOpnameList.value.findIndex(
            (item) => item.id === stockOpname.id
        );
        if (index !== -1) {
            stockOpnameList.value[index].status = "completed";
        }

        notificationStore.success("Stock opname finalized successfully");
    } catch (error) {
        notificationStore.error("Failed to finalize stock opname");
    }
};

const approveStockOpname = async (stockOpname) => {
    try {
        const index = stockOpnameList.value.findIndex(
            (item) => item.id === stockOpname.id
        );
        if (index !== -1) {
            stockOpnameList.value[index].status = "approved";
        }

        notificationStore.success("Stock opname approved successfully");
    } catch (error) {
        notificationStore.error("Failed to approve stock opname");
    }
};

const confirmDelete = async () => {
    try {
        stockOpnameList.value = stockOpnameList.value.filter(
            (item) => item.id !== deletingStockOpname.value.id
        );
        notificationStore.success("Stock opname deleted successfully");
    } catch (error) {
        notificationStore.error("Failed to delete stock opname");
    } finally {
        showDeleteModal.value = false;
        deletingStockOpname.value = null;
    }
};

const saveStockOpname = async () => {
    saving.value = true;
    try {
        if (editingStockOpname.value) {
            // Update existing stock opname
            const index = stockOpnameList.value.findIndex(
                (item) => item.id === editingStockOpname.value.id
            );
            if (index !== -1) {
                stockOpnameList.value[index] = {
                    ...form.value,
                    id: editingStockOpname.value.id,
                    updated_at: new Date().toISOString(),
                };
            }
            notificationStore.success("Stock opname updated successfully");
        } else {
            // Create new stock opname
            const newStockOpname = {
                ...form.value,
                id:
                    Math.max(
                        ...stockOpnameList.value.map((item) => item.id),
                        0
                    ) + 1,
                status: "draft",
                total_items: 0,
                variance_items: 0,
                details: [],
                created_by: "Current User",
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
            };
            stockOpnameList.value.unshift(newStockOpname);
            notificationStore.success("Stock opname created successfully");
        }
        closeModal();
    } catch (error) {
        notificationStore.error("Failed to save stock opname");
    } finally {
        saving.value = false;
    }
};

const addProductToCount = () => {
    if (!countingStockOpname.value) return;

    // Find products not yet added
    const existingProductIds = countingStockOpname.value.details.map(
        (d) => d.product_id
    );
    const availableProducts = products.value.filter(
        (p) => !existingProductIds.includes(p.id)
    );

    if (availableProducts.length === 0) {
        notificationStore.info("All products have been added");
        return;
    }

    // Add first available product (in real app, show product selector)
    const product = availableProducts[0];
    countingStockOpname.value.details.push({
        id:
            Math.max(
                ...countingStockOpname.value.details.map((d) => d.id || 0),
                0
            ) + 1,
        product_id: product.id,
        product_code: product.code,
        product_name: product.name,
        system_quantity: Math.floor(Math.random() * 100) + 10,
        counted_quantity: 0,
        variance: 0,
        notes: "",
    });
};

const removeProductFromCount = (detail) => {
    if (!countingStockOpname.value) return;

    const index = countingStockOpname.value.details.findIndex(
        (d) => d.id === detail.id
    );
    if (index !== -1) {
        countingStockOpname.value.details.splice(index, 1);
    }
};

const calculateVariance = (detail) => {
    detail.variance = detail.counted_quantity - detail.system_quantity;
};

const saveCountingProgress = async () => {
    try {
        // Update the stock opname with counting progress
        const index = stockOpnameList.value.findIndex(
            (item) => item.id === countingStockOpname.value.id
        );
        if (index !== -1) {
            stockOpnameList.value[index] = {
                ...countingStockOpname.value,
                status: "in_progress",
                total_items: countingStockOpname.value.details.length,
                variance_items: countingStockOpname.value.details.filter(
                    (d) => d.variance !== 0
                ).length,
                updated_at: new Date().toISOString(),
            };
        }

        showCountingModal.value = false;
        notificationStore.success("Counting progress saved");
    } catch (error) {
        notificationStore.error("Failed to save counting progress");
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingStockOpname.value = null;
    form.value = {
        code: "",
        period: `${new Date().getFullYear()}-${String(
            new Date().getMonth() + 1
        ).padStart(2, "0")}`,
        date: new Date().toISOString().split("T")[0],
        location_id: "",
        notes: "",
    };
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        in_progress:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        completed:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
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
    loadStockOpnames();
});
</script>
