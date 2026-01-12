<template>
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <!-- Header Section -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <!-- Title and Description -->
                <div>
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        {{ title }}
                    </h3>
                    <p
                        v-if="description"
                        class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                    >
                        {{ description }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <!-- Refresh Button -->
                    <button
                        v-if="showRefresh"
                        @click="$emit('refresh')"
                        :disabled="refreshLoading"
                        class="inline-flex items-center justify-center w-10 h-10 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                        title="Refresh data"
                    >
                        <ArrowPathIcon
                            :class="[
                                'w-4 h-4',
                                refreshLoading ? 'animate-spin' : '',
                            ]"
                        />
                    </button>

                    <!-- Search -->
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <MagnifyingGlassIcon
                                class="w-4 h-4 text-gray-400"
                            />
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                            :placeholder="searchPlaceholder"
                            @keyup.enter="handleSearchEnter"
                        />
                    </div>

                    <!-- Filter Dropdown -->
                    <div
                        v-if="showFilters"
                        class="hs-dropdown relative inline-flex"
                    >
                        <button
                            id="hs-dropdown-filter"
                            type="button"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                        >
                            <FunnelIcon class="w-4 h-4" />
                            Filter
                            <ChevronDownIcon class="w-4 h-4" />
                        </button>

                        <div
                            class="hs-dropdown-menu transition-all duration-200 hs-dropdown-open:opacity-100 opacity-0 hidden min-w-48 bg-white shadow-lg rounded-lg border border-gray-200 p-2 dark:bg-gray-800 dark:border-gray-700 mt-2"
                        >
                            <slot name="filters">
                                <div
                                    class="p-2 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    No filters available
                                </div>
                            </slot>
                        </div>
                    </div>

                    <!-- Export Button -->
                    <button
                        v-if="showExport"
                        @click="exportData"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        Export
                    </button>

                    <!-- Add Button -->
                    <button
                        v-if="showAddButton"
                        @click="$emit('add')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <PlusIcon class="w-4 h-4" />
                        {{ addButtonText }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
            >
                <!-- Table Header -->
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <!-- Select All Checkbox -->
                        <th v-if="selectable" class="px-6 py-3 text-left">
                            <div class="flex items-center">
                                <input
                                    id="select-all"
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    @change="toggleSelectAll"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                />
                                <label for="select-all" class="sr-only"
                                    >Select all</label
                                >
                            </div>
                        </th>

                        <!-- Column Headers -->
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400',
                                column.sortable
                                    ? 'cursor-pointer hover:text-gray-700 dark:hover:text-gray-300'
                                    : '',
                            ]"
                            @click="
                                column.sortable ? toggleSort(column.key) : null
                            "
                        >
                            <div class="flex items-center gap-2">
                                <span>{{ column.label }}</span>
                                <div
                                    v-if="column.sortable"
                                    class="flex flex-col"
                                >
                                    <ChevronUpIcon
                                        :class="[
                                            'w-3 h-3',
                                            sortKey === column.key &&
                                            sortOrder === 'asc'
                                                ? 'text-blue-500'
                                                : 'text-gray-400',
                                        ]"
                                    />
                                    <ChevronDownIcon
                                        :class="[
                                            'w-3 h-3 -mt-1',
                                            sortKey === column.key &&
                                            sortOrder === 'desc'
                                                ? 'text-blue-500'
                                                : 'text-gray-400',
                                        ]"
                                    />
                                </div>
                            </div>
                        </th>

                        <!-- Actions Column -->
                        <th
                            v-if="showActions"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody
                    class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                >
                    <!-- Loading State -->
                    <tr v-if="loading">
                        <td
                            :colspan="totalColumns"
                            class="px-6 py-12 text-center"
                        >
                            <div
                                class="flex flex-col items-center justify-center"
                            >
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-4"
                                ></div>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Loading...
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-else-if="filteredData.length === 0">
                        <td
                            :colspan="totalColumns"
                            class="px-6 py-12 text-center"
                        >
                            <div
                                class="flex flex-col items-center justify-center"
                            >
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4"
                                >
                                    <DocumentIcon
                                        class="w-8 h-8 text-gray-400"
                                    />
                                </div>
                                <h3
                                    class="text-sm font-medium text-gray-900 dark:text-white mb-1"
                                >
                                    {{ emptyTitle }}
                                </h3>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ emptyDescription }}
                                </p>
                                <button
                                    v-if="showAddButton"
                                    type="button"
                                    @click.stop.prevent="$emit('add')"
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                >
                                    <PlusIcon class="w-4 h-4" />
                                    {{ addButtonText }}
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Data Rows -->
                    <tr
                        v-else
                        v-for="(item, index) in paginatedData"
                        :key="getItemKey(item, index)"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                        :class="{
                            'bg-blue-50 dark:bg-blue-900/20':
                                selectedItems.includes(getItemKey(item, index)),
                        }"
                    >
                        <!-- Select Checkbox -->
                        <td v-if="selectable" class="px-6 py-4">
                            <div class="flex items-center">
                                <input
                                    :id="`select-${getItemKey(item, index)}`"
                                    type="checkbox"
                                    :checked="
                                        selectedItems.includes(
                                            getItemKey(item, index)
                                        )
                                    "
                                    @change="
                                        toggleSelectItem(
                                            getItemKey(item, index)
                                        )
                                    "
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                />
                                <label
                                    :for="`select-${getItemKey(item, index)}`"
                                    class="sr-only"
                                    >Select item</label
                                >
                            </div>
                        </td>

                        <!-- Data Columns -->
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="px-6 py-4 whitespace-nowrap"
                        >
                            <!-- Custom Column Slot -->
                            <slot
                                :name="`column-${column.key}`"
                                :item="item"
                                :value="getNestedValue(item, column.key)"
                                :index="index"
                            >
                                <!-- Default Column Rendering -->
                                <div
                                    v-if="column.type === 'avatar'"
                                    class="flex items-center"
                                >
                                    <img
                                        class="w-8 h-8 rounded-full object-cover"
                                        :src="
                                            getNestedValue(item, column.key) ||
                                            'https://via.placeholder.com/32'
                                        "
                                        :alt="
                                            getNestedValue(
                                                item,
                                                column.fallback || 'name'
                                            ) || 'Avatar'
                                        "
                                    />
                                    <div v-if="column.showName" class="ml-3">
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{
                                                getNestedValue(
                                                    item,
                                                    column.nameKey || "name"
                                                )
                                            }}
                                        </p>
                                        <p
                                            v-if="column.emailKey"
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {{
                                                getNestedValue(
                                                    item,
                                                    column.emailKey
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <span
                                    v-else-if="column.type === 'badge'"
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getBadgeClasses(
                                            getNestedValue(item, column.key),
                                            column.badgeColors
                                        ),
                                    ]"
                                >
                                    {{
                                        column.formatter
                                            ? column.formatter(
                                                  getNestedValue(
                                                      item,
                                                      column.key
                                                  ),
                                                  item
                                              )
                                            : getNestedValue(item, column.key)
                                    }}
                                </span>

                                <span
                                    v-else-if="column.type === 'currency'"
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            getNestedValue(item, column.key)
                                        )
                                    }}
                                </span>

                                <span
                                    v-else-if="column.type === 'date'"
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{
                                        formatDate(
                                            getNestedValue(item, column.key)
                                        )
                                    }}
                                </span>

                                <span
                                    v-else
                                    class="text-sm text-gray-900 dark:text-white"
                                >
                                    {{
                                        column.formatter
                                            ? column.formatter(
                                                  getNestedValue(
                                                      item,
                                                      column.key
                                                  ),
                                                  item
                                              )
                                            : getNestedValue(item, column.key)
                                    }}
                                </span>
                            </slot>
                        </td>

                        <!-- Actions Column -->
                        <td
                            v-if="showActions"
                            class="px-6 py-4 text-right text-sm font-medium"
                        >
                            <slot name="actions" :item="item" :index="index">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <button
                                        type="button"
                                        @click.stop.prevent="
                                            $emit('edit', item)
                                        "
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                                        title="Edit"
                                    >
                                        <PencilIcon class="w-4 h-4" />
                                    </button>
                                    <button
                                        type="button"
                                        @click.stop.prevent="
                                            $emit('delete', item)
                                        "
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                                        title="Delete"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer with Pagination -->
        <div
            v-if="
                !loading &&
                (serverSidePagination
                    ? data.length > 0
                    : filteredData.length > 0)
            "
            class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <!-- Selection Info -->
                <div class="flex items-center gap-4">
                    <div
                        v-if="selectedItems.length > 0"
                        class="text-sm text-gray-700 dark:text-gray-300"
                    >
                        {{ selectedItems.length }} of
                        {{
                            serverSidePagination
                                ? data.length
                                : filteredData.length
                        }}
                        selected
                        <button
                            v-if="showBulkActions"
                            @click="$emit('bulk-action', selectedItems)"
                            class="ml-2 text-blue-600 hover:text-blue-700 dark:text-blue-400"
                        >
                            Bulk Actions
                        </button>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Showing
                        {{
                            serverSidePagination
                                ? (pagination.current_page - 1) *
                                      pagination.per_page +
                                  1
                                : (currentPage - 1) * itemsPerPage + 1
                        }}
                        to
                        {{
                            serverSidePagination
                                ? Math.min(
                                      pagination.current_page *
                                          pagination.per_page,
                                      pagination.total
                                  )
                                : Math.min(
                                      currentPage * itemsPerPage,
                                      filteredData.length
                                  )
                        }}
                        of
                        {{
                            serverSidePagination
                                ? pagination.total
                                : filteredData.length
                        }}
                        results
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex items-center gap-2">
                    <!-- Items per page -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-500 dark:text-gray-400"
                            >Show:</label
                        >
                        <select
                            v-model="itemsPerPage"
                            class="text-sm border border-gray-300 rounded-lg px-5 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option
                                v-for="size in pageSizes"
                                :key="size"
                                :value="size"
                            >
                                {{ size }}
                            </option>
                        </select>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="flex items-center gap-1">
                        <button
                            @click="
                                serverSidePagination
                                    ? $emit('page-change', 1, itemsPerPage.value)
                                    : (currentPage = 1)
                            "
                            :disabled="currentPage === 1"
                            class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300"
                            title="First page"
                        >
                            <ChevronDoubleLeftIcon class="w-4 h-4" />
                        </button>
                        <button
                            @click="
                                serverSidePagination
                                    ? $emit('page-change', currentPage - 1, itemsPerPage.value)
                                    : currentPage--
                            "
                            :disabled="currentPage === 1"
                            class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300"
                            title="Previous page"
                        >
                            <ChevronLeftIcon class="w-4 h-4" />
                        </button>

                        <div class="flex items-center gap-1">
                            <button
                                v-for="page in visiblePages"
                                :key="page"
                                @click="
                                    serverSidePagination
                                        ? $emit('page-change', page, itemsPerPage.value)
                                        : (currentPage = page)
                                "
                                :class="[
                                    'px-3 py-1 text-sm rounded-lg transition-colors duration-200',
                                    page === currentPage
                                        ? 'bg-blue-600 text-white'
                                        : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700',
                                ]"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <button
                            @click="
                                serverSidePagination
                                    ? $emit('page-change', currentPage + 1, itemsPerPage.value)
                                    : currentPage++
                            "
                            :disabled="currentPage === totalPages"
                            class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300"
                            title="Next page"
                        >
                            <ChevronRightIcon class="w-4 h-4" />
                        </button>
                        <button
                            @click="
                                serverSidePagination
                                    ? $emit('page-change', totalPages, itemsPerPage.value)
                                    : (currentPage = totalPages)
                            "
                            :disabled="currentPage === totalPages"
                            class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300"
                            title="Last page"
                        >
                            <ChevronDoubleRightIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from "vue";
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    ArrowDownTrayIcon,
    PlusIcon,
    ArrowPathIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    DocumentIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    title: {
        type: String,
        default: "Data Table",
    },
    description: {
        type: String,
        default: "",
    },
    data: {
        type: Array,
        default: () => [],
    },
    columns: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    selectable: {
        type: Boolean,
        default: false,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    showAddButton: {
        type: Boolean,
        default: true,
    },
    addButtonText: {
        type: String,
        default: "Add New",
    },
    showFilters: {
        type: Boolean,
        default: true,
    },
    showExport: {
        type: Boolean,
        default: true,
    },
    showBulkActions: {
        type: Boolean,
        default: true,
    },
    searchPlaceholder: {
        type: String,
        default: "Search...",
    },
    emptyTitle: {
        type: String,
        default: "No data found",
    },
    emptyDescription: {
        type: String,
        default: "Get started by creating a new item.",
    },
    itemKey: {
        type: String,
        default: "id",
    },
    showRefresh: {
        type: Boolean,
        default: false,
    },
    refreshLoading: {
        type: Boolean,
        default: false,
    },
    serverSidePagination: {
        type: Boolean,
        default: false,
    },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            per_page: 10,
            total: 0,
            last_page: 1,
        }),
    },
});

// Emits
const emit = defineEmits([
    "add",
    "edit",
    "delete",
    "bulk-action",
    "export",
    "selection-change",
    "refresh",
    "search",
    "page-change",
    "sort",
]);

// Reactive data
const searchQuery = ref("");
const sortKey = ref("");
const sortOrder = ref("asc");
const selectedItems = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(10);
const pageSizes = ref([5, 10, 15, 25, 50, 100]);
const isInitialized = ref(false);
const updatingFromProps = ref(false);

// Computed properties
const totalColumns = computed(() => {
    let count = props.columns.length;
    if (props.selectable) count++;
    if (props.showActions) count++;
    return count;
});

const filteredData = computed(() => {
    if (props.serverSidePagination) {
        return props.data;
    }

    let filtered = [...props.data];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter((item) => {
            return props.columns.some((column) => {
                const value = getNestedValue(item, column.key);
                return value && value.toString().toLowerCase().includes(query);
            });
        });
    }

    // Apply sorting
    if (sortKey.value) {
        filtered.sort((a, b) => {
            const aVal = getNestedValue(a, sortKey.value);
            const bVal = getNestedValue(b, sortKey.value);

            if (aVal === null || aVal === undefined) return 1;
            if (bVal === null || bVal === undefined) return -1;

            let comparison = 0;
            if (aVal > bVal) comparison = 1;
            if (aVal < bVal) comparison = -1;

            return sortOrder.value === "desc" ? comparison * -1 : comparison;
        });
    }

    return filtered;
});

const totalPages = computed(() => {
    if (props.serverSidePagination) {
        return props.pagination.last_page || 1;
    }
    return Math.ceil(filteredData.value.length / itemsPerPage.value);
});

const paginatedData = computed(() => {
    if (props.serverSidePagination) {
        return props.data;
    }
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredData.value.slice(start, end);
});

const isAllSelected = computed(() => {
    return (
        filteredData.value.length > 0 &&
        selectedItems.value.length === filteredData.value.length
    );
});

const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    const current = currentPage.value;

    // If total pages is 6 or less, show all pages
    if (total <= 6) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
        return pages;
    }

    // Always show first 2 pages
    pages.push(1, 2);

    // Add ellipsis if current page is far from start
    if (current > 4) {
        pages.push('...');
    }

    // Show current page if it's not in the first 2 or last 3
    if (current > 2 && current < total - 2) {
        pages.push(current);
    }

    // Add ellipsis if current page is far from end
    if (current < total - 3) {
        pages.push('...');
    }

    // Always show last 3 pages
    pages.push(total - 2, total - 1, total);

    return pages;
});

// Methods
const getNestedValue = (obj, path) => {
    return path.split(".").reduce((o, p) => o && o[p], obj);
};

const getItemKey = (item, index) => {
    return getNestedValue(item, props.itemKey) || index;
};

const toggleSort = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }

    // Only emit sort if component is initialized to prevent initial unwanted calls
    if (isInitialized.value) {
        emit("sort", key, sortOrder.value);
    }
};

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedItems.value = [];
    } else {
        selectedItems.value = filteredData.value.map((item) =>
            getItemKey(item)
        );
    }
};

const toggleSelectItem = (key) => {
    const index = selectedItems.value.indexOf(key);
    if (index > -1) {
        selectedItems.value.splice(index, 1);
    } else {
        selectedItems.value.push(key);
    }
};

const getBadgeClasses = (value, colors = {}) => {
    const defaultColors = {
        active: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        inactive:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        pending:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        completed:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
    };

    const colorMap = { ...defaultColors, ...colors };
    return (
        colorMap[value] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const formatCurrency = (value) => {
    if (!value) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDate = (value) => {
    if (!value) return "";
    return new Date(value).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const handleSearchEnter = () => {
    if (props.serverSidePagination) {
        emit("search", searchQuery.value);
    }
};

const exportData = () => {
    emit("export", filteredData.value);
};

// Watchers
watch(searchQuery, (newQuery) => {
    if (!props.serverSidePagination) {
        currentPage.value = 1;
    }
    // Removed automatic search emission - now only triggers on Enter key
});

watch(
    selectedItems,
    (newVal) => {
        emit("selection-change", newVal);
    },
    { deep: true }
);

watch(itemsPerPage, () => {
    if (!props.serverSidePagination) {
        currentPage.value = 1;
    } else if (isInitialized.value && !updatingFromProps.value) {
        emit("page-change", 1, itemsPerPage.value);
    }
});

watch(
    () => props.pagination,
    (newPagination) => {
        if (props.serverSidePagination && newPagination) {
            updatingFromProps.value = true;
            currentPage.value = newPagination.current_page || 1;
            itemsPerPage.value = newPagination.per_page || 10;
            // Mark as initialized after the first pagination update
            if (!isInitialized.value) {
                isInitialized.value = true;
            }
            // Reset flag after update
            nextTick(() => {
                updatingFromProps.value = false;
            });
        }
    },
    { immediate: true }
);
</script>

<style scoped>
/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .overflow-x-auto::-webkit-scrollbar-track {
    background: #374151;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
