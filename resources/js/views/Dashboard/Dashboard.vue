<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 overflow-hidden shadow-lg rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-6 h-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"
                                        ></path>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">
                                    {{ $t('dashboard.title') }}
                                </h1>
                                <p class="mt-1 text-sm text-blue-100">
                                    {{ $t('dashboard.welcomeBack') }},
                                    {{ authStore.user?.name || "Admin" }}!
                                    {{ $t('dashboard.businessOverview') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Quick Stats in Header -->
                        <div class="hidden md:flex items-center space-x-6">
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">
                                    {{ stats.totalCustomers.toLocaleString() }}
                                </div>
                                <div class="text-xs text-blue-100">
                                    {{ $t('dashboard.customers') }}
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">
                                    {{ stats.totalSales.toLocaleString() }}
                                </div>
                                <div class="text-xs text-blue-100">{{ $t('dashboard.sales') }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">
                                    {{ formatCurrency(stats.totalRevenue) }}
                                </div>
                                <div class="text-xs text-blue-100">{{ $t('dashboard.revenue') }}</div>
                            </div>
                        </div>
                        <div class="text-sm text-blue-100">
                            {{ currentDateTime }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Customers -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-300"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2M5 5h14l1 12H4L5 9z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    {{ $t('dashboard.totalCustomers') }}
                                </dt>
                                <dd
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{
                                        stats.totalCustomers.toLocaleString()
                                    }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-blue-600 dark:text-blue-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            +{{ stats.customerGrowth }}%
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ stats.activeCustomers || 0 }} {{ $t('dashboard.active') }} •
                            {{ stats.inactiveCustomers || 0 }} {{ $t('dashboard.inactive') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-300"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0h6v-1a6 6 0 00-9-5.197m13.5-14a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    {{ $t('dashboard.totalSales') }}
                                </dt>
                                <dd
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{
                                        stats.totalSales.toLocaleString()
                                    }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            +{{ stats.salesGrowth }}%
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $t('dashboard.thisMonth') }}: {{ stats.monthlySales || 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-300"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m0-8v16"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    {{ $t('dashboard.totalProducts') }}
                                </dt>
                                <dd
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{
                                        stats.totalProducts.toLocaleString()
                                    }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-purple-600 dark:text-purple-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            +{{ stats.productGrowth }}%
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $t('dashboard.lowStock') }}: {{ stats.lowStockProducts || 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-300"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    {{ $t('dashboard.totalRevenue') }}
                                </dt>
                                <dd
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{
                                        formatCurrency(stats.totalRevenue)
                                    }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div
                            class="flex items-center text-sm text-yellow-600 dark:text-yellow-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            +{{ stats.revenueGrowth }}%
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $t('dashboard.thisMonth') }}:
                            {{ formatCurrency(stats.monthlyRevenue || 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Status & Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Low Stock Alert -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ $t('dashboard.lowStockAlert') }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ stats.lowStockProducts || 0 }} {{ $t('dashboard.productsNeedAttention') }}
                                </p>
                            </div>
                        </div>
                        <button
                            class="text-red-600 hover:text-red-500 text-sm font-medium"
                        >
                            {{ $t('dashboard.viewAll') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stock Movements Today -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ $t('dashboard.stockMovements') }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ stats.todayMovements || 0 }} {{ $t('dashboard.transactionsToday') }}
                                </p>
                            </div>
                        </div>
                        <button
                            class="text-blue-600 hover:text-blue-500 text-sm font-medium"
                        >
                            {{ $t('dashboard.viewDetails') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ $t('dashboard.pendingApprovals') }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ stats.pendingApprovals || 0 }} {{ $t('dashboard.itemsWaiting') }}
                                </p>
                            </div>
                        </div>
                        <button
                            class="text-yellow-600 hover:text-yellow-500 text-sm font-medium"
                        >
                            {{ $t('dashboard.review') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Activity Section -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Sales Revenue Chart -->
            <div class="xl:col-span-2">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3
                                class="text-lg font-medium text-gray-900 dark:text-white"
                            >
                                {{ $t('dashboard.salesRevenueTrend') }}
                            </h3>
                            <div class="flex items-center space-x-2">
                                <select
                                    class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                >
                                    <option>{{ $t('dashboard.last12Months') }}</option>
                                    <option>{{ $t('dashboard.last6Months') }}</option>
                                    <option>{{ $t('dashboard.last3Months') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="h-80">
                            <canvas ref="salesChartRef"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="space-y-6">
                <!-- Recent Activity -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3
                                class="text-lg font-medium text-gray-900 dark:text-white"
                            >
                                {{ $t('dashboard.recentActivity') }}
                            </h3>
                            <button
                                class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ $t('dashboard.viewAll') }}
                            </button>
                        </div>
                        <div class="space-y-4 max-h-80 overflow-y-auto">
                            <div
                                v-for="activity in recentActivities"
                                :key="activity.id"
                                class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                <div class="flex-shrink-0">
                                    <div
                                        :class="[
                                            'w-10 h-10 rounded-full flex items-center justify-center text-white',
                                            activity.type === 'customer'
                                                ? 'bg-blue-500'
                                                : activity.type === 'sale'
                                                ? 'bg-green-500'
                                                : activity.type === 'product'
                                                ? 'bg-purple-500'
                                                : activity.type === 'user'
                                                ? 'bg-gray-500'
                                                : 'bg-red-500',
                                        ]"
                                    >
                                        <svg
                                            class="w-5 h-5 text-white"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                v-if="
                                                    activity.type === 'customer'
                                                "
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2M5 5h14l1 12H4L5 9z"
                                            />
                                            <path
                                                v-else-if="
                                                    activity.type === 'sale'
                                                "
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0h6v-1a6 6 0 00-9-5.197m13.5-14a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
                                            />
                                            <path
                                                v-else-if="
                                                    activity.type === 'product'
                                                "
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m0-8v16"
                                            />
                                            <path
                                                v-else
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0v4M5 9h14l1 12H4L5 9z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ activity.description }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ activity.time }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                        >
                            {{ $t('dashboard.quickActions') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                v-for="action in quickActions"
                                :key="action.name"
                                @click="handleQuickAction(action.action)"
                                class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-all duration-200 hover:scale-105"
                            >
                                <div
                                    :class="[
                                        'w-12 h-12 rounded-lg flex items-center justify-center mb-3',
                                        action.color,
                                    ]"
                                >
                                    <svg
                                        class="w-6 h-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            :d="action.icon"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white text-center"
                                >
                                    {{ action.name }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Insights & Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Business Insights -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            {{ $t('dashboard.businessInsights') }}
                        </h3>
                        <div
                            class="flex items-center text-sm text-gray-500 dark:text-gray-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                ></path>
                            </svg>
                            {{ $t('dashboard.analytics') }}
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg"
                        >
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-4 h-4 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 10l7-7m0 0l7 7m-7-7v18"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-sm font-medium text-green-800 dark:text-green-200"
                                    >
                                        {{ $t('dashboard.revenueGrowth') }}
                                    </p>
                                    <p
                                        class="text-xs text-green-600 dark:text-green-400"
                                    >
                                        +{{ stats.revenueGrowth }}% {{ $t('dashboard.fromLastMonth') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg"
                        >
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-4 h-4 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2M5 5h14l1 12H4L5 9z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-sm font-medium text-blue-800 dark:text-blue-200"
                                    >
                                        {{ $t('dashboard.customerAcquisition') }}
                                    </p>
                                    <p
                                        class="text-xs text-blue-600 dark:text-blue-400"
                                    >
                                        {{ stats.activeCustomers }} {{ $t('dashboard.activeCustomersThisMonth') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg"
                        >
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-4 h-4 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m0-8v16"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-sm font-medium text-purple-800 dark:text-purple-200"
                                    >
                                        {{ $t('dashboard.inventoryHealth') }}
                                    </p>
                                    <p
                                        class="text-xs text-purple-600 dark:text-purple-400"
                                    >
                                        {{ stats.lowStockProducts }} {{ $t('dashboard.productsNeedRestocking') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Alerts & Notifications -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            {{ $t('dashboard.systemAlerts') }}
                        </h3>
                        <div
                            class="flex items-center text-sm text-gray-500 dark:text-gray-400"
                        >
                            <svg
                                class="w-4 h-4 mr-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 17h5l-5 5v-5zM4.868 12.683A17.925 17.925 0 0112 21c7.962 0 12-1.21 12-2.683m-12 2.683a17.925 17.925 0 01-7.132-8.317M12 21V9m0 0l-4 4m4-4l4 4"
                                ></path>
                            </svg>
                            {{ $t('dashboard.liveUpdates') }}
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div
                            v-if="stats.lowStockProducts > 0"
                            class="flex items-start p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800"
                        >
                            <div class="flex-shrink-0">
                                <svg
                                    class="w-5 h-5 text-yellow-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p
                                    class="text-sm font-medium text-yellow-800 dark:text-yellow-200"
                                >
                                    {{ $t('dashboard.lowStockAlert') }}
                                </p>
                                <p
                                    class="text-xs text-yellow-700 dark:text-yellow-300"
                                >
                                    {{ stats.lowStockProducts }} {{ $t('dashboard.productsRunningLow') }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="stats.pendingApprovals > 0"
                            class="flex items-start p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                        >
                            <div class="flex-shrink-0">
                                <svg
                                    class="w-5 h-5 text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p
                                    class="text-sm font-medium text-blue-800 dark:text-blue-200"
                                >
                                    {{ $t('dashboard.pendingApprovals') }}
                                </p>
                                <p
                                    class="text-xs text-blue-700 dark:text-blue-300"
                                >
                                    {{ stats.pendingApprovals }} {{ $t('dashboard.itemsWaitingApproval') }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-start p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800"
                        >
                            <div class="flex-shrink-0">
                                <svg
                                    class="w-5 h-5 text-green-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p
                                    class="text-sm font-medium text-green-800 dark:text-green-200"
                                >
                                    {{ $t('dashboard.systemStatus') }}
                                </p>
                                <p
                                    class="text-xs text-green-700 dark:text-green-300"
                                >
                                    {{ $t('dashboard.allSystemsOperational') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";
import { useAuthStore } from "../../stores/auth";
import { apiGet } from "@/utils/api";
import { useNotificationStore } from "@/stores/notification";
import { useI18n } from "vue-i18n";
import Chart from "chart.js/auto";

const authStore = useAuthStore();
const notification = useNotificationStore();
const { t } = useI18n();

// Loading states
const loading = ref(true);
const statisticsLoading = ref(true);

// Current date and time
const currentTime = ref(new Date());
let timeInterval = null;

const currentDateTime = computed(() => {
    return currentTime.value.toLocaleString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

// Real-time data from API
const statistics = ref({});
const chartData = ref({});
const recentActivity = ref({});

// Chart instance and ref
let salesChart = null;
const salesChartRef = ref(null);

// Computed stats for easy access
const stats = computed(() => ({
    totalCustomers: statistics.value.customers?.total || 0,
    totalSales: statistics.value.sales?.total || 0,
    totalProducts: statistics.value.products?.total || 0,
    totalRevenue: statistics.value.sales?.revenue || 0,
    customerGrowth: statistics.value.customers?.growth || 0,
    salesGrowth: statistics.value.sales?.growth || 0,
    productGrowth: statistics.value.products?.growth || 0,
    revenueGrowth: statistics.value.sales?.growth || 0,
    lowStockProducts: statistics.value.products?.low_stock || 0,
    activeCustomers: statistics.value.customers?.active || 0,
    inactiveCustomers: statistics.value.customers?.inactive || 0,
    monthlySales: statistics.value.sales?.monthly || 0,
    monthlyRevenue: statistics.value.sales?.monthly_revenue || 0,
    todayMovements: statistics.value.inventory?.today_movements || 0,
    pendingApprovals: statistics.value.approvals?.pending || 0,
}));

const recentActivities = computed(() => {
    const activities = [];

    // Recent Sales
    if (recentActivity.value.recent_sales) {
        recentActivity.value.recent_sales.forEach((sale) => {
            activities.push({
                id: `sale-${sale.id}`,
                type: "sale",
                description: `${t('dashboard.sale')} #${
                    sale.transaction_number
                } - ${formatCurrency(sale.total_amount)}`,
                status: sale.status,
                time: formatTimeAgo(sale.transaction_date),
            });
        });
    }

    // Recent Customers
    if (recentActivity.value.recent_customers) {
        recentActivity.value.recent_customers.forEach((customer) => {
            activities.push({
                id: `customer-${customer.id}`,
                type: "customer",
                description: `${t('dashboard.newCustomer')}: ${customer.customer_name}`,
                status: customer.status,
                time: formatTimeAgo(customer.created_at),
            });
        });
    }

    // Recent Products
    if (recentActivity.value.recent_products) {
        recentActivity.value.recent_products.forEach((product) => {
            activities.push({
                id: `product-${product.id}`,
                type: "product",
                description: `${t('dashboard.newProduct')}: ${product.product_name}`,
                status: product.status,
                time: formatTimeAgo(product.created_at),
            });
        });
    }

    // Stock Movements
    if (recentActivity.value.recent_stock_movements) {
        recentActivity.value.recent_stock_movements.forEach((movement) => {
            activities.push({
                id: `movement-${movement.id}`,
                type: "stock",
                description: `${movement.type}: ${movement.product_name} (${movement.quantity})`,
                status: movement.status,
                time: formatTimeAgo(movement.created_at),
            });
        });
    }

    // Work Orders (legacy)
    if (recentActivity.value.recent_work_orders) {
        recentActivity.value.recent_work_orders.forEach((wo) => {
            activities.push({
                id: `wo-${wo.id}`,
                type: "work_order",
                description: `Work Order: ${wo.title}`,
                status: wo.status,
                time: formatTimeAgo(wo.created_at),
            });
        });
    }

    // Sort by time (most recent first) and limit to 8
    return activities.slice(0, 8);
});

const quickActions = computed(() => [
    {
        name: t("dashboard.addCustomer"),
        action: "add-customer",
        route: "/customers",
        icon: "M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2M5 5h14l1 12H4L5 9z",
        color: "bg-blue-500",
    },
    {
        name: t("dashboard.createSale"),
        action: "create-sale",
        route: "/sales",
        icon: "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0h6v-1a6 6 0 00-9-5.197m13.5-14a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z",
        color: "bg-green-500",
    },
    {
        name: t("dashboard.addProduct"),
        action: "add-product",
        route: "/products",
        icon: "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4",
        color: "bg-purple-500",
    },
    {
        name: t("dashboard.stockIn"),
        action: "stock-in",
        route: "/stock-in",
        icon: "M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4",
        color: "bg-indigo-500",
    },
    {
        name: t("dashboard.stockOut"),
        action: "stock-out",
        route: "/stock-mutations",
        icon: "M7 4V16m0 0L3 12m4 4l4-4m6-8v12m0 0l4-4m-4 4l-4-4",
        color: "bg-orange-500",
    },
    {
        name: t("dashboard.viewReports"),
        action: "view-reports",
        route: "/reports",
        icon: "M13 7h8m0 0v8m0-8l-8 8-4-4-6 6",
        color: "bg-yellow-500",
    },
]);

const recentUsers = ref([]);

// Initialize sales chart
const initSalesChart = () => {
    if (!salesChartRef.value) return;

    // Destroy existing chart if it exists
    if (salesChart) {
        salesChart.destroy();
    }

    const ctx = salesChartRef.value.getContext("2d");

    // Sample data - in real app this would come from API
    const chartData = {
        labels: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ],
        revenue: [
            12000000, 19000000, 15000000, 25000000, 22000000, 30000000,
            28000000, 35000000, 40000000, 42000000, 48000000, 52000000,
        ],
        sales: [120, 190, 150, 250, 220, 300, 280, 350, 400, 420, 480, 520],
    };

    salesChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: "Revenue (IDR)",
                    data: chartData.revenue,
                    borderColor: "rgb(59, 130, 246)",
                    backgroundColor: "rgba(59, 130, 246, 0.1)",
                    yAxisID: "y",
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: "Sales Count",
                    data: chartData.sales,
                    borderColor: "rgb(16, 185, 129)",
                    backgroundColor: "rgba(16, 185, 129, 0.1)",
                    yAxisID: "y1",
                    tension: 0.4,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: "index",
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: "top",
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            if (context.datasetIndex === 0) {
                                label +=
                                    "Rp " + context.parsed.y.toLocaleString();
                            } else {
                                label += context.parsed.y + " sales";
                            }
                            return label;
                        },
                    },
                },
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: "Month",
                    },
                },
                y: {
                    type: "linear",
                    display: true,
                    position: "left",
                    title: {
                        display: true,
                        text: "Revenue (IDR)",
                    },
                    ticks: {
                        callback: function (value) {
                            return "Rp " + (value / 1000000).toFixed(0) + "M";
                        },
                    },
                },
                y1: {
                    type: "linear",
                    display: true,
                    position: "right",
                    title: {
                        display: true,
                        text: "Sales Count",
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            },
        },
    });
};

// Load dashboard data from API
const loadDashboardData = async () => {
    loading.value = true;
    statisticsLoading.value = true;

    try {
        // Load statistics
        const statsResponse = await apiGet("/dashboard/statistics");
        if (statsResponse.success) {
            statistics.value = statsResponse.data;
        }

        // Load chart data
        const chartsResponse = await apiGet("/dashboard/chart-data");
        if (chartsResponse.success) {
            chartData.value = chartsResponse.data;
        }

        // Load recent activity
        const activityResponse = await apiGet("/dashboard/recent-activity");
        if (activityResponse.success) {
            recentActivity.value = activityResponse.data;
        }
    } catch (error) {
        console.error("Failed to load dashboard data:", error);
        notification.error("Failed to load dashboard data");
    } finally {
        loading.value = false;
        statisticsLoading.value = false;
    }
};

// Format time ago helper
const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return "Just now";
    if (diffInSeconds < 3600)
        return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    return `${Math.floor(diffInSeconds / 86400)} days ago`;
};

// Format currency helper
const formatCurrency = (value) => {
    if (!value && value !== 0) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const handleQuickAction = (action) => {
    // Handle quick actions - redirect to relevant pages
    const actionItem = quickActions.value.find((a) => a.action === action);
    if (actionItem && actionItem.route) {
        window.location.href = actionItem.route;
    }
};

onMounted(async () => {
    // Load dashboard data
    await loadDashboardData();

    // Initialize chart after data is loaded
    nextTick(() => {
        initSalesChart();
    });

    // Update time every minute
    timeInterval = setInterval(() => {
        currentTime.value = new Date();
    }, 60000);

    // Refresh dashboard data every 5 minutes
    const refreshInterval = setInterval(() => {
        loadDashboardData();
    }, 300000); // 5 minutes

    // Cleanup on unmount
    onUnmounted(() => {
        if (timeInterval) clearInterval(timeInterval);
        if (refreshInterval) clearInterval(refreshInterval);
        if (salesChart) {
            salesChart.destroy();
        }
    });
});

onUnmounted(() => {
    if (timeInterval) {
        clearInterval(timeInterval);
    }
});
</script>
