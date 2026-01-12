<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Chart of Accounts
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage your company's chart of accounts and financial
                    structure
                </p>
            </div>
            <div class="flex gap-2 hidden">
                <!-- Export Dropdown -->
                <div class="relative" ref="exportDropdownRef">
                    <Button
                        @click="toggleExportDropdown"
                        variant="secondary"
                        size="sm"
                        :loading="exporting"
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
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        Export
                        <svg
                            class="w-4 h-4 ml-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </Button>

                    <!-- Dropdown Menu -->
                    <div
                        v-if="showExportDropdown"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-lg z-10"
                    >
                        <button
                            @click="handleExport('excel')"
                            class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center rounded-t-lg transition-colors"
                        >
                            <svg
                                class="w-4 h-4 mr-2 text-green-600"
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
                            Export to Excel
                        </button>
                        <button
                            @click="handleExport('pdf')"
                            class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center rounded-b-lg transition-colors"
                        >
                            <svg
                                class="w-4 h-4 mr-2 text-red-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                />
                            </svg>
                            Export to PDF
                        </button>
                    </div>
                </div>

                <Button
                    @click="showTreeView = !showTreeView"
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
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                    {{ showTreeView ? "Table View" : "Tree View" }}
                </Button>
                <Button
                    @click="toggleShowDeleted"
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
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                    </svg>
                    {{ showDeleted ? "Hide Deleted" : "Show Deleted" }}
                </Button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
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
                            Total Accounts
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalAccounts }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Assets
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ assetAccounts }}
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
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Liabilities
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ liabilityAccounts }}
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
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Revenue
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ revenueAccounts }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg"
                    >
                        <svg
                            class="w-6 h-6 text-orange-600 dark:text-orange-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
                            />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Expenses
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ expenseAccounts }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tree View -->
        <div
            v-if="showTreeView"
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Account Hierarchy
                </h2>
                <Button
                    @click="fetchTreeData"
                    size="sm"
                    variant="secondary"
                    :loading="loadingTree"
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
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                    </svg>
                    Refresh
                </Button>
            </div>
            <div v-if="loadingTree" class="text-center py-12">
                <svg
                    class="animate-spin h-8 w-8 mx-auto text-primary-600"
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
            </div>
            <div v-else-if="treeData.length === 0" class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">
                    No accounts found
                </p>
            </div>
            <div v-else class="space-y-2">
                <TreeNode
                    v-for="node in treeData"
                    :key="node.id"
                    :node="node"
                    @edit="handleEditAccount"
                    @delete="handleDeleteAccount"
                    @view="handleViewAccount"
                    @move="handleMoveAccount"
                />
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            v-else
            title="Chart of Accounts"
            description="Manage your company's chart of accounts including assets, liabilities, equity, revenue, and expenses."
            :data="displayAccounts"
            :columns="columns"
            :loading="loading"
            :selectable="false"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="Add Account"
            :show-filters="false"
            :show-export="false"
            :show-bulk-actions="false"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            :server-side="true"
            :pagination="pagination"
            search-placeholder="Search by code or name..."
            empty-title="No accounts found"
            empty-description="Get started by creating your first account."
            @add="handleAddAccount"
            @edit="handleEditAccount"
            @delete="handleDeleteAccount"
            @refresh="handleRefreshAccounts"
            @page-change="handlePageChange"
            @per-page-change="handlePerPageChange"
            @search="handleSearch"
            @sort="handleSort"
        >
            <!-- Custom Account Code Column -->
            <template #column-account_code="{ item }">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center mr-3"
                    >
                        <span class="text-white font-bold text-xs">{{
                            item.account_code.split("-")[0]
                        }}</span>
                    </div>
                    <div>
                        <p
                            class="text-sm font-mono font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.account_code }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Level {{ item.level }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Account Name Column -->
            <template #column-account_name="{ item }">
                <div>
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ item.account_name }}
                    </p>
                    <p
                        v-if="item.parent"
                        class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                    >
                        Parent: {{ item.parent.account_code }} -
                        {{ item.parent.account_name }}
                    </p>
                    <span
                        v-if="item.deleted_at"
                        class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1"
                    >
                        Deleted
                    </span>
                </div>
            </template>

            <!-- Custom Account Type Column -->
            <template #column-account_type="{ item }">
                <span
                    :class="[
                        'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full',
                        getAccountTypeClass(item.account_type),
                    ]"
                >
                    {{ formatAccountType(item.account_type) }}
                </span>
            </template>

            <!-- Custom Normal Balance Column -->
            <template #column-normal_balance="{ item }">
                <span
                    :class="[
                        'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full',
                        item.normal_balance === 'debit'
                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    ]"
                >
                    {{ item.normal_balance.toUpperCase() }}
                </span>
            </template>

            <!-- Custom Current Balance Column -->
            <template #column-current_balance="{ item }">
                <div class="text-right">
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ formatCurrency(item.current_balance || 0) }}
                    </p>
                    <p
                        v-if="item.balance_updated_at"
                        class="text-xs text-gray-500 dark:text-gray-400"
                    >
                        {{ formatDate(item.balance_updated_at) }}
                    </p>
                </div>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            item.is_active ? 'bg-primary-500' : 'bg-gray-400',
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                    >
                        {{ item.is_active ? "Active" : "Inactive" }}
                    </span>
                </div>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        v-if="item.deleted_at"
                        @click="handleRestoreAccount(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Restore"
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
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                    </button>
                    <template v-else>
                        <button
                            @click="handleViewAccount(item)"
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
                            @click="handleViewBalanceHistory(item)"
                            class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 dark:hover:text-purple-400 dark:hover:bg-purple-900/20"
                            title="Balance History"
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
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                        </button>
                        <button
                            @click="handleViewAudit(item)"
                            class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 dark:hover:text-yellow-400 dark:hover:bg-yellow-900/20"
                            title="Audit Trail"
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </button>
                        <button
                            @click="handleMoveAccount(item)"
                            class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-50 rounded-lg transition-colors duration-200 dark:hover:text-cyan-400 dark:hover:bg-cyan-900/20"
                            title="Move Account"
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
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                                />
                            </svg>
                        </button>
                        <button
                            @click="handleEditAccount(item)"
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
                            @click="handleDeleteAccount(item)"
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
                    </template>
                </div>
            </template>

            <!-- Custom Filters -->
            <template #filters>
                <div class="p-3 space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Account Type
                        </label>
                        <select
                            v-model="typeFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="">All Types</option>
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
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
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Level
                        </label>
                        <select
                            v-model="levelFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="">All Levels</option>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                            <option value="4">Level 4</option>
                            <option value="5">Level 5</option>
                        </select>
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

        <!-- Create/Edit Account Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                {{ showCreateModal ? "Create Account" : "Edit Account" }}
            </template>

            <form @submit.prevent="saveAccount" class="space-y-6">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                            >
                                Account Code <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input
                                    v-model="accountForm.account_code"
                                    type="text"
                                    placeholder="e.g. 1-1010"
                                    required
                                    class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono"
                                />
                                <Button
                                    type="button"
                                    @click="generateAccountCode"
                                    variant="secondary"
                                    size="sm"
                                    title="Auto Generate Code"
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
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                        />
                                    </svg>
                                </Button>
                            </div>
                        </div>
                        <FormInput
                            v-model="accountForm.account_name"
                            label="Account Name"
                            placeholder="e.g. Cash in Bank"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                            >
                                Account Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="accountForm.account_type"
                                required
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">Select Type</option>
                                <option value="asset">Asset</option>
                                <option value="liability">Liability</option>
                                <option value="equity">Equity</option>
                                <option value="revenue">Revenue</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                            >
                                Normal Balance
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="accountForm.normal_balance"
                                required
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">Select Balance</option>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                            >
                                Parent Account
                            </label>
                            <select
                                v-model="accountForm.parent_id"
                                @change="updateLevelFromParent"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option :value="null">None (Top Level)</option>
                                <option
                                    v-for="acc in availableParents"
                                    :key="acc.id"
                                    :value="acc.id"
                                >
                                    {{ acc.account_code }} -
                                    {{ acc.account_name }}
                                </option>
                            </select>
                        </div>
                        <FormInput
                            v-model.number="accountForm.level"
                            type="number"
                            min="1"
                            max="5"
                            label="Level"
                            placeholder="1-5"
                            required
                            readonly
                        />
                    </div>

                    <FormInput
                        v-model.number="accountForm.opening_balance"
                        type="number"
                        step="0.01"
                        min="0"
                        label="Opening Balance"
                        placeholder="0.00"
                    />

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                        >
                            Description
                        </label>
                        <textarea
                            v-model="accountForm.description"
                            rows="3"
                            placeholder="Optional description"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <!-- Metadata JSON Editor -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                        >
                            Metadata (JSON)
                        </label>
                        <textarea
                            v-model="metadataString"
                            rows="4"
                            placeholder='{"key": "value"}'
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono"
                        ></textarea>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                        >
                            Optional JSON metadata for additional account
                            information
                        </p>
                    </div>

                    <div class="flex items-center">
                        <input
                            id="is_active"
                            v-model="accountForm.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded focus:ring-2"
                        />
                        <label
                            for="is_active"
                            class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            Active
                        </label>
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
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        {{
                            showCreateModal
                                ? "Create Account"
                                : "Update Account"
                        }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View Account Details Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="xl">
            <template #title> Account Details </template>

            <div v-if="selectedAccount" class="space-y-6">
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
                                Account Code
                            </dt>
                            <dd
                                class="mt-1 text-sm font-mono font-medium text-gray-900 dark:text-white"
                            >
                                {{ selectedAccount.account_code }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Account Name
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{ selectedAccount.account_name }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Account Type
                            </dt>
                            <dd class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                        getAccountTypeClass(
                                            selectedAccount.account_type
                                        ),
                                    ]"
                                >
                                    {{
                                        formatAccountType(
                                            selectedAccount.account_type
                                        )
                                    }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Normal Balance
                            </dt>
                            <dd class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                        selectedAccount.normal_balance ===
                                        'debit'
                                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    ]"
                                >
                                    {{
                                        selectedAccount.normal_balance.toUpperCase()
                                    }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                Level
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                Level {{ selectedAccount.level }}
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
                                        selectedAccount.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                    ]"
                                >
                                    {{
                                        selectedAccount.is_active
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Balance Information -->
                <div
                    class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-lg p-6"
                >
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Balance Information
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Opening Balance
                            </dt>
                            <dd
                                class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{
                                    formatCurrency(
                                        selectedAccount.opening_balance || 0
                                    )
                                }}
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Current Balance
                            </dt>
                            <dd
                                class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{
                                    formatCurrency(
                                        selectedAccount.current_balance || 0
                                    )
                                }}
                            </dd>
                        </div>
                        <div
                            class="md:col-span-2"
                            v-if="selectedAccount.balance_updated_at"
                        >
                            <dt
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Last Balance Update
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 dark:text-white"
                            >
                                {{
                                    formatDateTime(
                                        selectedAccount.balance_updated_at
                                    )
                                }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Hierarchy -->
                <div
                    v-if="selectedAccount.parent"
                    class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6"
                >
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Hierarchy
                    </h3>
                    <div>
                        <dt
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2"
                        >
                            Parent Account
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ selectedAccount.parent.account_code }} -
                            {{ selectedAccount.parent.account_name }}
                        </dd>
                    </div>
                </div>

                <!-- Description -->
                <div
                    v-if="selectedAccount.description"
                    class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6"
                >
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Description
                    </h3>
                    <p
                        class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap"
                    >
                        {{ selectedAccount.description }}
                    </p>
                </div>

                <!-- Metadata -->
                <div
                    v-if="selectedAccount.metadata"
                    class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6"
                >
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Metadata
                    </h3>
                    <pre
                        class="text-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800 p-4 rounded-lg overflow-x-auto"
                        >{{
                            JSON.stringify(selectedAccount.metadata, null, 2)
                        }}</pre
                    >
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
                                {{ formatDateTime(selectedAccount.created_at) }}
                            </dd>
                            <dd
                                v-if="selectedAccount.created_by"
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                by {{ selectedAccount.created_by }}
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
                                {{ formatDateTime(selectedAccount.updated_at) }}
                            </dd>
                            <dd
                                v-if="selectedAccount.updated_by"
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                by {{ selectedAccount.updated_by }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Modal>

        <!-- Balance History Modal -->
        <Modal
            :is-open="showBalanceHistoryModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                Balance History: {{ selectedAccount?.account_name }}
            </template>

            <div v-if="selectedAccount" class="space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Account Code
                        </p>
                        <p
                            class="text-lg font-mono font-semibold text-gray-900 dark:text-white"
                        >
                            {{ selectedAccount.account_code }}
                        </p>
                    </div>
                    <Button
                        @click="calculateCurrentBalance"
                        :loading="calculatingBalance"
                        size="sm"
                    >
                        Calculate Balance
                    </Button>
                </div>

                <div v-if="loadingBalanceHistory" class="text-center py-12">
                    <svg
                        class="animate-spin h-8 w-8 mx-auto text-primary-600"
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
                </div>

                <div
                    v-else-if="balanceHistory.length === 0"
                    class="text-center py-12"
                >
                    <svg
                        class="w-16 h-16 mx-auto text-gray-400"
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
                    <p class="mt-4 text-gray-500 dark:text-gray-400">
                        No balance history found
                    </p>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="history in balanceHistory"
                        :key="history.id"
                        class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ formatDate(history.period_start) }} -
                                        {{ formatDate(history.period_end) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            Balance
                                        </p>
                                        <p
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatCurrency(history.balance)
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            Debit Total
                                        </p>
                                        <p
                                            class="text-lg font-semibold text-blue-600 dark:text-blue-400"
                                        >
                                            {{
                                                formatCurrency(
                                                    history.debit_total
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            Credit Total
                                        </p>
                                        <p
                                            class="text-lg font-semibold text-green-600 dark:text-green-400"
                                        >
                                            {{
                                                formatCurrency(
                                                    history.credit_total
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <p
                                    v-if="history.calculated_by"
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-2"
                                >
                                    Calculated by {{ history.calculated_by }} on
                                    {{ formatDateTime(history.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Audit Trail Modal -->
        <Modal :is-open="showAuditModal" @close="closeModals" size="xl">
            <template #title>
                Audit Trail: {{ selectedAccount?.account_name }}
            </template>

            <div v-if="selectedAccount" class="space-y-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Account Code
                    </p>
                    <p
                        class="text-lg font-mono font-semibold text-gray-900 dark:text-white"
                    >
                        {{ selectedAccount.account_code }}
                    </p>
                </div>

                <div v-if="loadingAuditTrail" class="text-center py-12">
                    <svg
                        class="animate-spin h-8 w-8 mx-auto text-primary-600"
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
                </div>

                <div
                    v-else-if="auditTrail.length === 0"
                    class="text-center py-12"
                >
                    <svg
                        class="w-16 h-16 mx-auto text-gray-400"
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
                    <p class="mt-4 text-gray-500 dark:text-gray-400">
                        No audit trail found
                    </p>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="audit in auditTrail"
                        :key="audit.id"
                        class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                :class="[
                                    'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center',
                                    audit.event_type === 'created'
                                        ? 'bg-green-100 dark:bg-green-900'
                                        : audit.event_type === 'updated'
                                        ? 'bg-blue-100 dark:bg-blue-900'
                                        : audit.event_type === 'deleted'
                                        ? 'bg-red-100 dark:bg-red-900'
                                        : 'bg-gray-100 dark:bg-gray-900',
                                ]"
                            >
                                <svg
                                    v-if="audit.event_type === 'created'"
                                    class="w-4 h-4 text-green-600 dark:text-green-400"
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
                                <svg
                                    v-else-if="audit.event_type === 'updated'"
                                    class="w-4 h-4 text-blue-600 dark:text-blue-400"
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
                                <svg
                                    v-else-if="audit.event_type === 'deleted'"
                                    class="w-4 h-4 text-red-600 dark:text-red-400"
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
                                <svg
                                    v-else
                                    class="w-4 h-4 text-gray-600 dark:text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                    />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-0.5 text-xs font-semibold rounded-full uppercase',
                                            audit.event_type === 'created'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : audit.event_type === 'updated'
                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                                : audit.event_type === 'deleted'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                        ]"
                                    >
                                        {{ audit.event_type }}
                                    </span>
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ formatDateTime(audit.created_at) }}
                                    </span>
                                </div>
                                <p
                                    v-if="audit.user_name"
                                    class="text-sm text-gray-900 dark:text-white mb-2"
                                >
                                    by {{ audit.user_name }}
                                </p>
                                <div
                                    v-if="audit.old_values || audit.new_values"
                                    class="text-xs space-y-2"
                                >
                                    <details
                                        class="bg-gray-50 dark:bg-gray-800 rounded p-2"
                                    >
                                        <summary
                                            class="cursor-pointer text-gray-600 dark:text-gray-400 font-medium"
                                        >
                                            View Changes
                                        </summary>
                                        <div
                                            class="mt-2 grid grid-cols-2 gap-4"
                                        >
                                            <div v-if="audit.old_values">
                                                <p
                                                    class="font-medium text-red-600 dark:text-red-400 mb-1"
                                                >
                                                    Old Values:
                                                </p>
                                                <pre
                                                    class="text-xs overflow-x-auto"
                                                    >{{
                                                        JSON.stringify(
                                                            audit.old_values,
                                                            null,
                                                            2
                                                        )
                                                    }}</pre
                                                >
                                            </div>
                                            <div v-if="audit.new_values">
                                                <p
                                                    class="font-medium text-green-600 dark:text-green-400 mb-1"
                                                >
                                                    New Values:
                                                </p>
                                                <pre
                                                    class="text-xs overflow-x-auto"
                                                    >{{
                                                        JSON.stringify(
                                                            audit.new_values,
                                                            null,
                                                            2
                                                        )
                                                    }}</pre
                                                >
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Move Account Modal -->
        <Modal :is-open="showMoveModal" @close="closeModals">
            <template #title> Move Account </template>

            <div v-if="selectedAccount" class="space-y-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Moving Account:
                    </p>
                    <p
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        {{ selectedAccount.account_code }} -
                        {{ selectedAccount.account_name }}
                    </p>
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                    >
                        New Parent Account
                    </label>
                    <select
                        v-model="moveToParentId"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                        <option :value="null">None (Top Level)</option>
                        <option
                            v-for="acc in availableParentsForMove"
                            :key="acc.id"
                            :value="acc.id"
                        >
                            {{ acc.account_code }} -
                            {{ acc.account_name }} (Level {{ acc.level }})
                        </option>
                    </select>
                </div>

                <div
                    class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0"
                >
                    <Button
                        @click="closeModals"
                        variant="secondary"
                        :disabled="movingAccount"
                        class="w-full sm:w-auto"
                    >
                        Cancel
                    </Button>
                    <Button
                        @click="confirmMoveAccount"
                        :loading="movingAccount"
                        :disabled="movingAccount"
                        class="w-full sm:w-auto"
                    >
                        Move Account
                    </Button>
                </div>
            </div>
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
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import DataTable from "../../components/UI/DataTable.vue";
import Modal from "../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../components/Forms/FormInput.vue";
import Button from "../../components/Base/Button.vue";
import TreeNode from "../../components/ChartOfAccounts/TreeNode.vue";
import { useNotificationStore } from "@/stores/notification";
import { useConfirmationModalStore } from "@/stores/confirmationModal";
import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const allAccounts = ref([]);
const pagination = ref({
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1,
    from: 0,
    to: 0,
});
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showBalanceHistoryModal = ref(false);
const showAuditModal = ref(false);
const showMoveModal = ref(false);
const showTreeView = ref(false);
const showDeleted = ref(false);
const selectedAccount = ref(null);
const saving = ref(false);
const calculatingBalance = ref(false);
const movingAccount = ref(false);
const loadingTree = ref(false);
const loadingBalanceHistory = ref(false);
const loadingAuditTrail = ref(false);
const exporting = ref(false);
const showExportDropdown = ref(false);
const exportDropdownRef = ref(null);

// Tree data
const treeData = ref([]);

// Balance history
const balanceHistory = ref([]);

// Audit trail
const auditTrail = ref([]);

// Filter data
const typeFilter = ref("");
const statusFilter = ref("");
const levelFilter = ref("");
const applyingFilters = ref(false);

// Move account
const moveToParentId = ref(null);

// Form data
const accountForm = ref({
    account_code: "",
    account_name: "",
    account_type: "",
    normal_balance: "",
    parent_id: null,
    level: 1,
    description: "",
    opening_balance: 0,
    metadata: null,
    is_active: true,
});

const metadataString = ref("");

// Computed properties
const totalAccounts = computed(() => pagination.value.total);
const assetAccounts = computed(() => {
    // This would need a separate API call for accurate counts
    return allAccounts.value.filter((a) => a.account_type === "asset").length;
});
const liabilityAccounts = computed(() => {
    return allAccounts.value.filter((a) => a.account_type === "liability")
        .length;
});
const revenueAccounts = computed(() => {
    return allAccounts.value.filter((a) => a.account_type === "revenue").length;
});
const expenseAccounts = computed(() => {
    return allAccounts.value.filter((a) => a.account_type === "expense").length;
});

const displayAccounts = computed(() => {
    return allAccounts.value;
});

const availableParents = computed(() => {
    let parents = allAccounts.value.filter((a) => !a.deleted_at);
    if (selectedAccount.value) {
        parents = parents.filter((acc) => acc.id !== selectedAccount.value.id);
    }
    return parents;
});

const availableParentsForMove = computed(() => {
    if (!selectedAccount.value) return [];
    return allAccounts.value.filter(
        (acc) =>
            acc.id !== selectedAccount.value.id &&
            acc.account_type === selectedAccount.value.account_type &&
            !acc.deleted_at
    );
});

// Table columns configuration
const columns = [
    { key: "account_code", label: "Code", sortable: true },
    { key: "account_name", label: "Account Name", sortable: true },
    { key: "account_type", label: "Type", sortable: true },
    { key: "normal_balance", label: "Normal Balance", sortable: false },
    { key: "current_balance", label: "Current Balance", sortable: true },
    { key: "status", label: "Status", sortable: true },
];

// Methods
const fetchAccounts = async (params = {}) => {
    try {
        loading.value = true;

        // Build query parameters for server-side pagination
        const queryParams = {
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
            ...params,
        };

        if (showDeleted.value) {
            queryParams.with_deleted = true;
        }

        // Add filters if any
        if (typeFilter.value) queryParams.type = typeFilter.value;
        if (statusFilter.value) queryParams.is_active = statusFilter.value;
        if (levelFilter.value) queryParams.level = levelFilter.value;

        const response = await apiGet("chart-of-accounts", queryParams);
        if (response && response.success) {
            // Server-side pagination response
            allAccounts.value = response.data || [];
            if (response.pagination) {
                pagination.value = response.pagination;
            }
        }
    } catch (error) {
        notification.error("Failed to fetch accounts");
    } finally {
        loading.value = false;
    }
};

const fetchTreeData = async () => {
    try {
        loadingTree.value = true;
        const response = await apiGet("chart-of-accounts/tree");
        if (response) {
            treeData.value = response;
        }
    } catch (error) {
        notification.error("Failed to fetch tree data");
    } finally {
        loadingTree.value = false;
    }
};

const toggleShowDeleted = () => {
    showDeleted.value = !showDeleted.value;
    fetchAccounts();
};

const handleAddAccount = () => {
    accountForm.value = {
        account_code: "",
        account_name: "",
        account_type: "",
        normal_balance: "",
        parent_id: null,
        level: 1,
        description: "",
        opening_balance: 0,
        metadata: null,
        is_active: true,
    };
    metadataString.value = "";
    showCreateModal.value = true;
};

const handleEditAccount = (account) => {
    selectedAccount.value = account;
    accountForm.value = {
        account_code: account.account_code,
        account_name: account.account_name,
        account_type: account.account_type,
        normal_balance: account.normal_balance,
        parent_id: account.parent_id,
        level: account.level,
        description: account.description || "",
        opening_balance: account.opening_balance || 0,
        metadata: account.metadata,
        is_active: account.is_active,
    };
    metadataString.value = account.metadata
        ? JSON.stringify(account.metadata, null, 2)
        : "";
    showEditModal.value = true;
};

const handleViewAccount = async (account) => {
    selectedAccount.value = account;
    showViewModal.value = true;
};

const handleViewBalanceHistory = async (account) => {
    selectedAccount.value = account;
    showBalanceHistoryModal.value = true;
    await fetchBalanceHistory(account.id);
};

const handleViewAudit = async (account) => {
    selectedAccount.value = account;
    showAuditModal.value = true;
    await fetchAuditTrail(account.id);
};

const handleMoveAccount = (account) => {
    selectedAccount.value = account;
    moveToParentId.value = account.parent_id;
    showMoveModal.value = true;
};

const handleDeleteAccount = (account) => {
    confirmationModal.showModal({
        title: "Delete Account",
        message: `Are you sure you want to delete "${account.account_name}"?`,
        description:
            "This action will soft delete the account. You can restore it later.",
        confirmText: "Delete Account",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiDelete(`chart-of-accounts/${account.id}`);
            if (data.success || data.message) {
                notification.success("Account deleted successfully");
                fetchAccounts();
                if (showTreeView.value) fetchTreeData();
                return data;
            } else {
                throw new Error(data.message || "Failed to delete account");
            }
        },
        onError: (error) => {
            notification.error(error.message || "Failed to delete account");
        },
    });
};

const handleRestoreAccount = (account) => {
    confirmationModal.showModal({
        title: "Restore Account",
        message: `Are you sure you want to restore "${account.account_name}"?`,
        description: "This action will restore the deleted account.",
        confirmText: "Restore Account",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiPost(
                `chart-of-accounts/${account.id}/restore`
            );
            if (data.success || data.message) {
                notification.success("Account restored successfully");
                fetchAccounts();
                return data;
            } else {
                throw new Error(data.message || "Failed to restore account");
            }
        },
        onError: (error) => {
            notification.error(error.message || "Failed to restore account");
        },
    });
};

const handleRefreshAccounts = async () => {
    refreshLoading.value = true;
    try {
        await fetchAccounts();
        if (showTreeView.value) await fetchTreeData();
        notification.success("Accounts refreshed successfully");
    } catch (error) {
        notification.error("Failed to refresh accounts");
    } finally {
        refreshLoading.value = false;
    }
};

const applyFilters = async () => {
    applyingFilters.value = true;
    try {
        // Reset to first page when applying filters
        pagination.value.current_page = 1;

        const params = {
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
        };

        if (typeFilter.value) params.type = typeFilter.value;
        if (statusFilter.value) params.is_active = statusFilter.value;
        if (levelFilter.value) params.level = levelFilter.value;
        if (showDeleted.value) params.with_deleted = true;

        const response = await apiGet("chart-of-accounts", params);
        if (response && response.success) {
            // Server-side pagination response
            allAccounts.value = response.data || [];
            if (response.pagination) {
                pagination.value = response.pagination;
            }
            notification.success("Filters applied successfully");
        }
    } catch (error) {
        notification.error("Failed to apply filters");
    } finally {
        applyingFilters.value = false;
    }
};

const clearFilters = () => {
    typeFilter.value = "";
    statusFilter.value = "";
    levelFilter.value = "";
    pagination.value.current_page = 1;
    fetchAccounts();
};

const updateLevelFromParent = () => {
    if (accountForm.value.parent_id) {
        const parent = allAccounts.value.find(
            (a) => a.id === accountForm.value.parent_id
        );
        if (parent) {
            accountForm.value.level = parent.level + 1;
        }
    } else {
        accountForm.value.level = 1;
    }
};

const generateAccountCode = async () => {
    try {
        const params = {};
        if (accountForm.value.parent_id) {
            params.parent_id = accountForm.value.parent_id;
        }
        const response = await apiGet(
            "chart-of-accounts/generate-code",
            params
        );
        if (response && response.code) {
            accountForm.value.account_code = response.code;
            notification.success("Account code generated");
        }
    } catch (error) {
        notification.error("Failed to generate account code");
    }
};

const saveAccount = async () => {
    saving.value = true;
    try {
        // Parse metadata JSON
        if (metadataString.value.trim()) {
            try {
                accountForm.value.metadata = JSON.parse(metadataString.value);
            } catch (e) {
                notification.error("Invalid JSON in metadata field");
                saving.value = false;
                return;
            }
        } else {
            accountForm.value.metadata = null;
        }

        const url = showCreateModal.value
            ? "chart-of-accounts"
            : `chart-of-accounts/${selectedAccount.value.id}`;

        const data = showCreateModal.value
            ? await apiPost(url, accountForm.value)
            : await apiPut(url, accountForm.value);

        if (data.success || data.data || data.message) {
            notification.success(
                `Account ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            fetchAccounts();
            if (showTreeView.value) fetchTreeData();
        }
    } catch (error) {
        notification.error(error.message || "Failed to save account");
    } finally {
        saving.value = false;
    }
};

const calculateCurrentBalanceSedangDiclick = ref(false);
const calculateCurrentBalance = async () => {
    if (!selectedAccount.value) return;

    if(calculateCurrentBalanceSedangDiclick.value) {
        return;
    }

    calculateCurrentBalanceSedangDiclick.value = true
    calculatingBalance.value = true;
    try {
        const data = await apiPost(
            `chart-of-accounts/${selectedAccount.value.id}/calculate-balance`,
            { async: false } // Force synchronous calculation
        );
        if (data && data.success) {
            notification.success("Balance calculated successfully");
            await fetchBalanceHistory(selectedAccount.value.id);
            fetchAccounts();
        } else {
            notification.error(data.message || "Failed to calculate balance");
        }
    } catch (error) {
        notification.error(error.message || "Failed to calculate balance");
    } finally {
        calculatingBalance.value = false;
        calculateCurrentBalanceSedangDiclick.value = false
    }
};

const fetchBalanceHistory = async (accountId) => {
    try {
        loadingBalanceHistory.value = true;
        const response = await apiGet(
            `chart-of-accounts/${accountId}/balance-history`
        );
        if (response && response.success) {
            balanceHistory.value = response.data || [];
        }
    } catch (error) {
        notification.error("Failed to fetch balance history");
    } finally {
        loadingBalanceHistory.value = false;
    }
};

const fetchAuditTrail = async (accountId) => {
    try {
        loadingAuditTrail.value = true;
        const response = await apiGet(`chart-of-accounts/${accountId}/audit`);
        if (response && response.success) {
            auditTrail.value = response.data || [];
        }
    } catch (error) {
        notification.error("Failed to fetch audit trail");
    } finally {
        loadingAuditTrail.value = false;
    }
};

const confirmMoveAccount = async () => {
    if (!selectedAccount.value) return;

    movingAccount.value = true;
    try {
        const data = await apiPost(
            `chart-of-accounts/${selectedAccount.value.id}/move`,
            {
                new_parent_id: moveToParentId.value,
            }
        );

        if (data.success || data.message) {
            notification.success("Account moved successfully");
            closeModals();
            fetchAccounts();
            if (showTreeView.value) fetchTreeData();
        }
    } catch (error) {
        notification.error(error.message || "Failed to move account");
    } finally {
        movingAccount.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    showBalanceHistoryModal.value = false;
    showAuditModal.value = false;
    showMoveModal.value = false;
    selectedAccount.value = null;
    accountForm.value = {
        account_code: "",
        account_name: "",
        account_type: "",
        normal_balance: "",
        parent_id: null,
        level: 1,
        description: "",
        opening_balance: 0,
        metadata: null,
        is_active: true,
    };
    metadataString.value = "";
    balanceHistory.value = [];
    auditTrail.value = [];
    moveToParentId.value = null;
};

// Utility functions
const getAccountTypeClass = (type) => {
    const classes = {
        asset: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        liability: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        equity: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200",
        revenue:
            "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        expense:
            "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200",
    };
    return classes[type] || "bg-gray-100 text-gray-800";
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

// Watchers for auto server-side filtering
watch(
    [typeFilter, statusFilter, levelFilter, showDeleted],
    () => {
        pagination.value.current_page = 1;
        fetchAccounts();
    },
    { deep: true }
);

// Methods for server-side operations
const handlePageChange = (page) => {
    pagination.value.current_page = page;
    fetchAccounts();
};

const handlePerPageChange = (perPage) => {
    pagination.value.per_page = perPage;
    pagination.value.current_page = 1;
    fetchAccounts();
};

const handleSearch = (searchQuery) => {
    pagination.value.current_page = 1;
    fetchAccounts({ search: searchQuery });
};

const handleSort = (sortField, sortDirection) => {
    pagination.value.current_page = 1;
    fetchAccounts({
        sort_field: sortField,
        sort_direction: sortDirection,
    });
};

// Export methods
const toggleExportDropdown = () => {
    showExportDropdown.value = !showExportDropdown.value;
};

const handleExport = async (format) => {
    showExportDropdown.value = false;
    exporting.value = true;

    try {
        // Build filter params
        const params = {
            format: format, // 'excel' or 'pdf'
        };

        // Add current filters
        if (typeFilter.value) params.type = typeFilter.value;
        if (statusFilter.value) params.is_active = statusFilter.value;
        if (levelFilter.value) params.level = levelFilter.value;
        if (showDeleted.value) params.with_deleted = true;

        // Make POST request to export endpoint
        const response = await apiPost("chart-of-accounts/export", params);

        if (response && response.success && response.filename) {
            notification.success(
                `Export ${format.toUpperCase()} started. File: ${response.filename}`
            );

            // Auto-download the file
            setTimeout(() => {
                downloadExportFile(response.filename);
            }, 1500);
        } else {
            notification.error("Export failed. Please try again.");
        }
    } catch (error) {
        console.error("Export error:", error);
        notification.error(
            error.message || `Failed to export to ${format.toUpperCase()}`
        );
    } finally {
        exporting.value = false;
    }
};

const downloadExportFile = async (filename) => {
    try {
        // Create download link
        const token = localStorage.getItem("token");
        const downloadUrl = `/api/chart-of-accounts/export/${filename}?token=${token}`;

        // Create temporary anchor element for download
        const link = document.createElement("a");
        link.href = downloadUrl;
        link.setAttribute("download", filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        notification.success("Download started");
    } catch (error) {
        console.error("Download error:", error);
        notification.error("Failed to download file");
    }
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (
        exportDropdownRef.value &&
        !exportDropdownRef.value.contains(event.target)
    ) {
        showExportDropdown.value = false;
    }
};

// Lifecycle
onMounted(() => {
    fetchAccounts();
    // Add click outside listener
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
