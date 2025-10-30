<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Journal Entries
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage your company's journal entries and financial
                    transactions
                </p>
            </div>
            <div class="flex gap-2">
                <Button @click="handleAddEntry" variant="primary" size="sm">
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    New Entry
                </Button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg"
                    >
                        <svg
                            class="w-6 h-6 text-primary-600 dark:text-primary-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Entries
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalEntries }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg"
                    >
                        <svg
                            class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Draft
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ draftEntries }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg"
                    >
                        <svg
                            class="w-6 h-6 text-green-600 dark:text-green-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Posted
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ postedEntries }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <svg
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Cancelled
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ cancelledEntries }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Journal Entries"
            description="Manage your company's journal entries including drafts, posted, and cancelled entries."
            :data="entries"
            :columns="columns"
            :loading="loading"
            :selectable="false"
            :show-actions="true"
            :show-add-button="false"
            add-button-text="New Entry"
            :show-filters="true"
            :show-export="true"
            :show-bulk-actions="false"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search by entry number or description..."
            empty-title="No entries found"
            empty-description="Get started by creating your first journal entry."
            @add="handleAddEntry"
            @edit="handleEditEntry"
            @delete="handleDeleteEntry"
            @refresh="handleRefreshEntries"
        >
            <!-- Custom Entry Number Column -->
            <template #column-entry_number="{ item }">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-3"
                    >
                        <span class="text-white font-bold text-xs">{{
                            item.entry_number.split("-")[0]
                        }}</span>
                    </div>
                    <div>
                        <p
                            class="text-sm font-mono font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.entry_number }}
                        </p>
                        <p
                            v-if="item.reference_number"
                            class="text-xs text-gray-500 dark:text-gray-400"
                        >
                            Ref: {{ item.reference_number }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Entry Date Column -->
            <template #column-entry_date="{ item }">
                <div>
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ formatDate(item.entry_date) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatTime(item.entry_date) }}
                    </p>
                </div>
            </template>

            <!-- Custom Description Column -->
            <template #column-description="{ item }">
                <div>
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ item.description }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ formatAccountType(item.entry_type) }}
                    </p>
                </div>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <span
                    :class="[
                        'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full',
                        getStatusClass(item.status),
                    ]"
                >
                    {{ formatStatus(item.status) }}
                </span>
            </template>

            <!-- Custom Total Amount Column -->
            <template #column-total_amount="{ item }">
                <div class="text-right">
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ formatCurrency(calculateTotal(item.details)) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ item.details?.length || 0 }} lines
                    </p>
                </div>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleViewEntry(item)"
                        class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20"
                        title="View Details"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            />
                        </svg>
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="handleEditEntry(item)"
                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors duration-200 dark:hover:text-primary-400 dark:hover:bg-primary-900/20"
                        title="Edit"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="handlePostEntry(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Post Entry"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </button>
                    <button
                        v-if="item.status === 'posted'"
                        @click="handleCancelEntry(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Cancel Entry"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </button>
                    <button
                        v-if="item.status === 'draft'"
                        @click="handleDeleteEntry(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
            </template>

            <!-- Custom Filters -->
            <template #filters>
                <div class="p-3 space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Status
                        </label>
                        <select
                            v-model="statusFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="posted">Posted</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Entry Type
                        </label>
                        <select
                            v-model="entryTypeFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="">All Types</option>
                            <option value="general">General</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="closing">Closing</option>
                            <option value="opening">Opening</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Start Date
                            </label>
                            <input
                                v-model="startDateFilter"
                                type="date"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                End Date
                            </label>
                            <input
                                v-model="endDateFilter"
                                type="date"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="applyFilters"
                            :disabled="applyingFilters"
                            class="flex-1 px-3 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{
                                applyingFilters
                                    ? "Applying..."
                                    : "Apply Filters"
                            }}
                        </button>
                        <button
                            @click="clearFilters"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </template>
        </DataTable>

        <!-- View Entry Details Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="xl">
            <template #title> Journal Entry Details </template>

            <div v-if="selectedEntry" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Basic Information
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Entry Number
                            </dt>
                            <dd
                                class="mt-1 text-sm font-mono font-medium text-gray-900 dark:text-white"
                            >
                                {{ selectedEntry.entry_number }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Reference Number
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ selectedEntry.reference_number || "-" }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Entry Date
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDateTime(selectedEntry.entry_date) }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Entry Type
                            </dt>
                            <dd class="mt-1">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                >
                                    {{
                                        formatAccountType(
                                            selectedEntry.entry_type
                                        )
                                    }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Status
                            </dt>
                            <dd class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                        getStatusClass(selectedEntry.status),
                                    ]"
                                >
                                    {{ formatStatus(selectedEntry.status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Description
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ selectedEntry.description }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Entry Details -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6"
                >
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Entry Details
                    </h3>
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Account
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Description
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Debit
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Credit
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="detail in selectedEntry.details"
                                    :key="detail.id"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.account?.account_code }} -
                                        {{ detail.account?.account_name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ detail.description || "-" }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            detail.transaction_type === "debit"
                                                ? formatCurrency(detail.amount)
                                                : "-"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            detail.transaction_type === "credit"
                                                ? formatCurrency(detail.amount)
                                                : "-"
                                        }}
                                    </td>
                                </tr>
                                <tr
                                    class="bg-gray-50 dark:bg-gray-700 font-semibold"
                                >
                                    <td
                                        colspan="2"
                                        class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                    >
                                        Total
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                calculateTotalByType(
                                                    selectedEntry.details,
                                                    "debit"
                                                )
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                calculateTotalByType(
                                                    selectedEntry.details,
                                                    "credit"
                                                )
                                            )
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Audit Info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Audit Information
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Created At
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDateTime(selectedEntry.created_at) }}
                            </dd>
                            <dd
                                v-if="selectedEntry.creator"
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                by {{ selectedEntry.creator.name }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Updated At
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDateTime(selectedEntry.updated_at) }}
                            </dd>
                        </div>
                        <div v-if="selectedEntry.posted_at">
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Posted At
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDateTime(selectedEntry.posted_at) }}
                            </dd>
                            <dd
                                v-if="selectedEntry.poster"
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                by {{ selectedEntry.poster.name }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Modal>

        <!-- Create/Edit Entry Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                {{
                    showCreateModal
                        ? "Create Journal Entry"
                        : "Edit Journal Entry"
                }}
            </template>

            <form @submit.prevent="saveEntry" class="space-y-6">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <FormInput
                            v-model="entryForm.entry_date"
                            type="date"
                            label="Entry Date"
                            required
                        />
                        <FormInput
                            v-model="entryForm.reference_number"
                            label="Reference Number"
                            placeholder="Optional reference number"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                        >
                            Entry Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="entryForm.entry_type"
                            required
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="">Select Type</option>
                            <option value="general">General</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="closing">Closing</option>
                            <option value="opening">Opening</option>
                        </select>
                    </div>

                    <FormInput
                        v-model="entryForm.description"
                        label="Description"
                        placeholder="Entry description"
                        required
                    />

                    <!-- Entry Details -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Entry Details
                                <span class="text-red-500">*</span>
                            </label>
                            <Button
                                type="button"
                                @click="addDetailRow"
                                variant="secondary"
                                size="sm"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                                Add Line
                            </Button>
                        </div>
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        >
                                            Account
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        >
                                            Description
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        >
                                            Type
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        >
                                            Amount
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                        >
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="(
                                            detail, index
                                        ) in entryForm.details"
                                        :key="index"
                                    >
                                        <td class="px-4 py-2">
                                            <select
                                                v-model="detail.account_id"
                                                required
                                                class="w-full text-sm border border-gray-300 rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            >
                                                <option value="">
                                                    Select Account
                                                </option>
                                                <option
                                                    v-for="account in availableAccounts"
                                                    :key="account.id"
                                                    :value="account.id"
                                                >
                                                    {{ account.account_code }} -
                                                    {{ account.account_name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input
                                                v-model="detail.description"
                                                type="text"
                                                placeholder="Optional description"
                                                class="w-full text-sm border border-gray-300 rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            />
                                        </td>
                                        <td class="px-4 py-2">
                                            <select
                                                v-model="
                                                    detail.transaction_type
                                                "
                                                required
                                                class="w-full text-sm border border-gray-300 rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            >
                                                <option value="">Select</option>
                                                <option value="debit">
                                                    Debit
                                                </option>
                                                <option value="credit">
                                                    Credit
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input
                                                v-model.number="detail.amount"
                                                type="number"
                                                step="0.01"
                                                min="0.01"
                                                placeholder="0.00"
                                                required
                                                class="w-full text-sm border border-gray-300 rounded px-2 py-1 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right"
                                            />
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button
                                                type="button"
                                                @click="removeDetailRow(index)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot
                                    class="bg-gray-50 dark:bg-gray-700 font-semibold"
                                >
                                    <tr>
                                        <td
                                            colspan="3"
                                            class="px-4 py-2 text-sm text-gray-900 dark:text-white"
                                        >
                                            Total
                                        </td>
                                        <td
                                            class="px-4 py-2 text-sm text-right"
                                        >
                                            <div class="space-y-1">
                                                <div>
                                                    Debit:
                                                    {{
                                                        formatCurrency(
                                                            calculateTotalByType(
                                                                entryForm.details,
                                                                "debit"
                                                            )
                                                        )
                                                    }}
                                                </div>
                                                <div>
                                                    Credit:
                                                    {{
                                                        formatCurrency(
                                                            calculateTotalByType(
                                                                entryForm.details,
                                                                "credit"
                                                            )
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div
                            v-if="balanceError"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ balanceError }}
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0"
                >
                    <Button
                        @click="closeModals"
                        variant="secondary"
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :loading="saving"
                        :disabled="saving || balanceError"
                        class="w-full sm:w-auto"
                    >
                        {{ showCreateModal ? "Create Entry" : "Update Entry" }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :is-open="confirmationModal.isOpen"
            :title="confirmationModal.config.title"
            :message="confirmationModal.config.message"
            :description="confirmationModal.config.description"
            :confirm-text="confirmationModal.config.confirmText"
            :cancel-text="confirmationModal.config.cancelText"
            :loading="confirmationModal.loading"
            @confirm="confirmationModal.handleConfirm"
            @cancel="confirmationModal.handleCancel"
            @close="confirmationModal.handleClose"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import DataTable from "../../components/UI/DataTable.vue";
import Modal from "../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../components/Forms/FormInput.vue";
import Button from "../../components/Base/Button.vue";
import { useNotificationStore } from "@/stores/notification";
import { useConfirmationModalStore } from "@/stores/confirmationModal";
import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const entries = ref([]);
const availableAccounts = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedEntry = ref(null);
const saving = ref(false);

// Filter data
const statusFilter = ref("");
const entryTypeFilter = ref("");
const startDateFilter = ref("");
const endDateFilter = ref("");
const applyingFilters = ref(false);

// Form data
const entryForm = ref({
    entry_date: new Date().toISOString().split("T")[0],
    reference_number: "",
    description: "",
    entry_type: "",
    details: [
        { account_id: "", transaction_type: "", amount: 0, description: "" },
        { account_id: "", transaction_type: "", amount: 0, description: "" },
    ],
});

// Computed properties
const totalEntries = computed(() => entries.value.length);
const draftEntries = computed(
    () => entries.value.filter((e) => e.status === "draft").length
);
const postedEntries = computed(
    () => entries.value.filter((e) => e.status === "posted").length
);
const cancelledEntries = computed(
    () => entries.value.filter((e) => e.status === "cancelled").length
);

const balanceError = computed(() => {
    const totalDebit = calculateTotalByType(entryForm.value.details, "debit");
    const totalCredit = calculateTotalByType(entryForm.value.details, "credit");

    if (totalDebit === 0 && totalCredit === 0) {
        return null;
    }

    if (totalDebit !== totalCredit) {
        return `Total debit (${formatCurrency(
            totalDebit
        )}) must equal total credit (${formatCurrency(totalCredit)})`;
    }

    return null;
});

// Table columns configuration
const columns = [
    { key: "entry_number", label: "Entry Number", sortable: true },
    { key: "entry_date", label: "Date", sortable: true },
    { key: "description", label: "Description", sortable: true },
    { key: "status", label: "Status", sortable: true },
    { key: "total_amount", label: "Total Amount", sortable: true },
];

// Methods
const fetchEntries = async () => {
    try {
        loading.value = true;
        const params = {};
        if (statusFilter.value) params.status = statusFilter.value;
        if (entryTypeFilter.value) params.entry_type = entryTypeFilter.value;
        if (startDateFilter.value) params.start_date = startDateFilter.value;
        if (endDateFilter.value) params.end_date = endDateFilter.value;

        const response = await apiGet("journal-entries", params);
        if (response) {
            entries.value = response.data?.data || [];
        }
    } catch (error) {
        notification.error("Failed to fetch journal entries");
    } finally {
        loading.value = false;
    }
};

const fetchAccounts = async () => {
    try {
        const response = await apiGet("chart-of-accounts", { is_active: true });
        if (response) {
            availableAccounts.value = response.data?.data || [];
        }
    } catch (error) {
        notification.error("Failed to fetch accounts");
    }
};

const handleAddEntry = () => {
    entryForm.value = {
        entry_date: new Date().toISOString().split("T")[0],
        reference_number: "",
        description: "",
        entry_type: "",
        details: [
            {
                account_id: "",
                transaction_type: "",
                amount: 0,
                description: "",
            },
            {
                account_id: "",
                transaction_type: "",
                amount: 0,
                description: "",
            },
        ],
    };
    showCreateModal.value = true;
};

const handleEditEntry = async (entry) => {
    try {
        const response = await apiGet(`journal-entries/${entry.id}`);
        if (response && response.data) {
            selectedEntry.value = response.data;
            entryForm.value = {
                entry_date: response.data.entry_date,
                reference_number: response.data.reference_number || "",
                description: response.data.description,
                entry_type: response.data.entry_type,
                details: response.data.details.map((detail) => ({
                    account_id: detail.account_id,
                    transaction_type: detail.transaction_type,
                    amount: detail.amount,
                    description: detail.description || "",
                })),
            };
            showEditModal.value = true;
        }
    } catch (error) {
        notification.error("Failed to fetch entry details");
    }
};

const handleViewEntry = async (entry) => {
    try {
        const response = await apiGet(`journal-entries/${entry.id}`);
        if (response && response.data) {
            selectedEntry.value = response.data;
            showViewModal.value = true;
        }
    } catch (error) {
        notification.error("Failed to fetch entry details");
    }
};

const handleDeleteEntry = (entry) => {
    confirmationModal.showModal({
        title: "Delete Journal Entry",
        message: `Are you sure you want to delete entry "${entry.entry_number}"?`,
        description: "This action cannot be undone.",
        confirmText: "Delete Entry",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiDelete(`journal-entries/${entry.id}`);
            if (data.success || data.message) {
                notification.success("Journal entry deleted successfully");
                fetchEntries();
                return data;
            } else {
                throw new Error(
                    data.message || "Failed to delete journal entry"
                );
            }
        },
        onError: (error) => {
            notification.error(
                error.message || "Failed to delete journal entry"
            );
        },
    });
};

const handlePostEntry = (entry) => {
    confirmationModal.showModal({
        title: "Post Journal Entry",
        message: `Are you sure you want to post entry "${entry.entry_number}"?`,
        description:
            "This will finalize the entry and update account balances.",
        confirmText: "Post Entry",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiPost(`journal-entries/${entry.id}/post`);
            if (data.success || data.message) {
                notification.success("Journal entry posted successfully");
                fetchEntries();
                return data;
            } else {
                throw new Error(data.message || "Failed to post journal entry");
            }
        },
        onError: (error) => {
            notification.error(error.message || "Failed to post journal entry");
        },
    });
};

const handleCancelEntry = (entry) => {
    confirmationModal.showModal({
        title: "Cancel Journal Entry",
        message: `Are you sure you want to cancel entry "${entry.entry_number}"?`,
        description:
            "This will mark the entry as cancelled and reverse its effects.",
        confirmText: "Cancel Entry",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiPost(`journal-entries/${entry.id}/cancel`);
            if (data.success || data.message) {
                notification.success("Journal entry cancelled successfully");
                fetchEntries();
                return data;
            } else {
                throw new Error(
                    data.message || "Failed to cancel journal entry"
                );
            }
        },
        onError: (error) => {
            notification.error(
                error.message || "Failed to cancel journal entry"
            );
        },
    });
};

const handleRefreshEntries = async () => {
    refreshLoading.value = true;
    try {
        await fetchEntries();
        notification.success("Journal entries refreshed successfully");
    } catch (error) {
        notification.error("Failed to refresh journal entries");
    } finally {
        refreshLoading.value = false;
    }
};

const applyFilters = async () => {
    applyingFilters.value = true;
    try {
        await fetchEntries();
        notification.success("Filters applied successfully");
    } catch (error) {
        notification.error("Failed to apply filters");
    } finally {
        applyingFilters.value = false;
    }
};

const clearFilters = () => {
    statusFilter.value = "";
    entryTypeFilter.value = "";
    startDateFilter.value = "";
    endDateFilter.value = "";
    fetchEntries();
};

const addDetailRow = () => {
    entryForm.value.details.push({
        account_id: "",
        transaction_type: "",
        amount: 0,
        description: "",
    });
};

const removeDetailRow = (index) => {
    if (entryForm.value.details.length > 2) {
        entryForm.value.details.splice(index, 1);
    } else {
        notification.error("Journal entry must have at least 2 detail lines");
    }
};

const saveEntry = async () => {
    if (balanceError.value) {
        notification.error("Please fix the balance error before saving");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "journal-entries"
            : `journal-entries/${selectedEntry.value.id}`;

        const data = showCreateModal.value
            ? await apiPost(url, entryForm.value)
            : await apiPut(url, entryForm.value);

        if (data.success || data.data || data.message) {
            notification.success(
                `Journal entry ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            fetchEntries();
        }
    } catch (error) {
        notification.error(error.message || "Failed to save journal entry");
    } finally {
        saving.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    selectedEntry.value = null;
    entryForm.value = {
        entry_date: new Date().toISOString().split("T")[0],
        reference_number: "",
        description: "",
        entry_type: "",
        details: [
            {
                account_id: "",
                transaction_type: "",
                amount: 0,
                description: "",
            },
            {
                account_id: "",
                transaction_type: "",
                amount: 0,
                description: "",
            },
        ],
    };
};

// Utility functions
const getStatusClass = (status) => {
    const classes = {
        draft: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        posted: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        cancelled: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const formatAccountType = (type) => {
    return type.charAt(0).toUpperCase() + type.slice(1);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatTime = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatDateTime = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const calculateTotal = (details) => {
    if (!details) return 0;
    return details.reduce(
        (total, detail) => total + parseFloat(detail.amount || 0),
        0
    );
};

const calculateTotalByType = (details, type) => {
    if (!details) return 0;
    return details
        .filter((detail) => detail.transaction_type === type)
        .reduce((total, detail) => total + parseFloat(detail.amount || 0), 0);
};

// Watch for filter changes
watch([statusFilter, entryTypeFilter, startDateFilter, endDateFilter], () => {
    if (
        statusFilter.value ||
        entryTypeFilter.value ||
        startDateFilter.value ||
        endDateFilter.value
    ) {
        applyFilters();
    }
});

// Lifecycle
onMounted(() => {
    fetchEntries();
    fetchAccounts();
});
</script>
