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
                                    {{ stats.totalProducts.toLocaleString() }}
                                </div>
                                <div class="text-xs text-blue-100">
                                    {{ $t('dashboard.products') }}
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">
                                    {{ stats.totalCustomers.toLocaleString() }}
                                </div>
                                <div class="text-xs text-blue-100">{{ $t('dashboard.customers') }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">
                                    {{ stats.currentStock.toLocaleString() }}
                                </div>
                                <div class="text-xs text-blue-100">{{ $t('dashboard.stock') }}</div>
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
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ stats.activeProducts || 0 }} {{ $t('dashboard.active') }} •
                            {{ stats.inactiveProducts || 0 }} {{ $t('dashboard.inactive') }}
                        </div>
                    </div>
                </div>
            </div>

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
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ stats.activeCustomers || 0 }} {{ $t('dashboard.active') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Transactions -->
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
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate"
                                >
                                    {{ $t('dashboard.stockTransactions') }}
                                </dt>
                                <dd
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{
                                        stats.totalStockTransactions.toLocaleString()
                                    }}</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $t('dashboard.in') }}: {{ stats.totalStockIn.toLocaleString() }} •
                            {{ $t('dashboard.out') }}: {{ stats.totalStockOut.toLocaleString() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Stock Balance -->
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
                                    {{ $t('dashboard.currentStock') }}
                                </dt>
                                <dd
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    <span v-if="statisticsLoading">...</span>
                                    <span v-else>{{
                                        stats.currentStock.toLocaleString()
                                    }}</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ stats.locationsWithStock || 0 }} {{ $t('dashboard.locationsWithStock') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Locations -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center"
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
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                        ></path>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ $t('dashboard.totalLocations') }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ stats.totalLocations || 0 }} {{ $t('dashboard.locations') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Categories -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-pink-500 rounded-lg flex items-center justify-center"
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
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ $t('dashboard.productCategories') }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ stats.totalCategories || 0 }} {{ $t('dashboard.categories') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Orders (if available) -->
            <div
                v-if="stats.totalWorkOrders !== undefined"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center"
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
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ $t('dashboard.workOrders') }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ stats.totalWorkOrders || 0 }} {{ $t('dashboard.total') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Activity Section -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Stock Movement Trend Chart -->
            <div class="xl:col-span-2">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3
                                class="text-lg font-medium text-gray-900 dark:text-white"
                            >
                                {{ $t('dashboard.stockMovementTrend') }}
                            </h3>
                        </div>
                        <div class="h-80">
                            <canvas ref="stockChartRef"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products by Category Chart -->
            <div class="space-y-6">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3
                                class="text-lg font-medium text-gray-900 dark:text-white"
                            >
                                {{ $t('dashboard.productsByCategory') }}
                            </h3>
                        </div>
                        <div class="h-64">
                            <canvas ref="categoryChartRef"></canvas>
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

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Stock Transactions -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            {{ $t('dashboard.recentStockTransactions') }}
                        </h3>
                        <button
                            @click="navigateTo('/buku-stock')"
                            class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            {{ $t('dashboard.viewAll') }}
                        </button>
                    </div>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <div
                            v-for="transaction in recentStockTransactions"
                            :key="transaction.id"
                            class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            <div class="flex-shrink-0">
                                <div
                                    :class="[
                                        'w-10 h-10 rounded-full flex items-center justify-center text-white',
                                        transaction.quantity_in > 0 ? 'bg-green-500' : 'bg-red-500',
                                    ]"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            v-if="transaction.quantity_in > 0"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                                        />
                                        <path
                                            v-else
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 4V16m0 0L3 12m4 4l4-4m6-8v12m0 0l4-4m-4 4l-4-4"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ transaction.product_name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ transaction.transaction_type }} • {{ transaction.location_name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatTimeAgo(transaction.transaction_date) }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span
                                    :class="[
                                        'text-sm font-medium',
                                        transaction.quantity_in > 0 ? 'text-green-600' : 'text-red-600'
                                    ]"
                                >
                                    {{ transaction.quantity_in > 0 ? '+' : '' }}{{ transaction.quantity_in || transaction.quantity_out }}
                                </span>
                            </div>
                        </div>
                        <div
                            v-if="recentStockTransactions.length === 0"
                            class="text-center text-gray-500 dark:text-gray-400 py-8"
                        >
                            {{ $t('dashboard.noRecentTransactions') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div
                v-if="recentProducts.length > 0"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            {{ $t('dashboard.recentProducts') }}
                        </h3>
                        <button
                            @click="navigateTo('/products')"
                            class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            {{ $t('dashboard.viewAll') }}
                        </button>
                    </div>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <div
                            v-for="product in recentProducts"
                            :key="product.id"
                            class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            <div class="flex-shrink-0">
                                <div
                                    :class="[
                                        'w-10 h-10 rounded-full flex items-center justify-center text-white',
                                        product.is_active ? 'bg-purple-500' : 'bg-gray-500',
                                    ]"
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
                                            d="M20 7l-8-4-8 4m16 0l-8 4m0-8v16"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ product.product_name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ product.product_code }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatTimeAgo(product.created_at) }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span
                                    :class="[
                                        'text-xs px-2 py-1 rounded-full',
                                        product.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                    ]"
                                >
                                    {{ product.is_active ? $t('dashboard.active') : $t('dashboard.inactive') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Work Orders (if available) -->
            <div
                v-if="recentWorkOrders.length > 0"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            {{ $t('dashboard.recentWorkOrders') }}
                        </h3>
                        <button
                            @click="navigateTo('/work-orders')"
                            class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            {{ $t('dashboard.viewAll') }}
                        </button>
                    </div>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <div
                            v-for="wo in recentWorkOrders"
                            :key="wo.id"
                            class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            <div class="flex-shrink-0">
                                <div
                                    :class="[
                                        'w-10 h-10 rounded-full flex items-center justify-center text-white',
                                        getWorkOrderStatusColor(wo.status)
                                    ]"
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
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ wo.title }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatTimeAgo(wo.created_at) }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span
                                    :class="[
                                        'text-xs px-2 py-1 rounded-full',
                                        getWorkOrderStatusBadgeColor(wo.status)
                                    ]"
                                >
                                    {{ wo.status }}
                                </span>
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
import { useRouter } from "vue-router";
import Chart from "chart.js/auto";

const authStore = useAuthStore();
const notification = useNotificationStore();
const { t } = useI18n();
const router = useRouter();

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

// Chart instances and refs
let stockChart = null;
let categoryChart = null;
const stockChartRef = ref(null);
const categoryChartRef = ref(null);

// Computed stats for easy access
const stats = computed(() => ({
    totalProducts: statistics.value.products?.total || 0,
    activeProducts: statistics.value.products?.active || 0,
    inactiveProducts: statistics.value.products?.inactive || 0,
    totalCustomers: statistics.value.customers?.total || 0,
    activeCustomers: statistics.value.customers?.active || 0,
    totalStockTransactions: statistics.value.stock?.total_transactions || 0,
    totalStockIn: statistics.value.stock?.total_in || 0,
    totalStockOut: statistics.value.stock?.total_out || 0,
    currentStock: statistics.value.stock?.current_balance || 0,
    totalLocations: statistics.value.locations?.total || 0,
    locationsWithStock: statistics.value.locations?.with_stock || 0,
    totalCategories: statistics.value.product_categories?.total || 0,
    totalWorkOrders: statistics.value.work_orders?.total,
    totalUsers: statistics.value.users?.total,
    totalRoles: statistics.value.roles?.total,
}));

const recentStockTransactions = computed(() => {
    return recentActivity.value.recent_stock_transactions || [];
});

const recentProducts = computed(() => {
    return recentActivity.value.recent_products || [];
});

const recentWorkOrders = computed(() => {
    return recentActivity.value.recent_work_orders || [];
});

const quickActions = computed(() => [
    {
        name: t("dashboard.addProduct"),
        action: "add-product",
        route: "/products",
        icon: "M12 4v16m8-8H4",
        color: "bg-purple-500",
    },
    {
        name: t("dashboard.stockIn"),
        action: "stock-in",
        route: "/stock-masuk",
        icon: "M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4",
        color: "bg-green-500",
    },
    {
        name: t("dashboard.stockMutation"),
        action: "stock-mutation",
        route: "/mutasi",
        icon: "M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4",
        color: "bg-blue-500",
    },
    {
        name: t("dashboard.stockAdjustment"),
        action: "stock-adjustment",
        route: "/adjustment",
        icon: "M12 6v6m0 0v6m0-6h6m-6 0H6",
        color: "bg-yellow-500",
    },
    {
        name: t("dashboard.stockOpname"),
        action: "stock-opname",
        route: "/stockopname",
        icon: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
        color: "bg-indigo-500",
    },
    {
        name: t("dashboard.stockBook"),
        action: "stock-book",
        route: "/buku-stock",
        icon: "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
        color: "bg-pink-500",
    },
]);

// Initialize stock movement trend chart
const initStockChart = () => {
    if (!stockChartRef.value) return;

    if (stockChart) {
        stockChart.destroy();
    }

    const ctx = stockChartRef.value.getContext("2d");

    const stockMovementData = chartData.value.stock_movement_trend || [];
    const labels = stockMovementData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
    });
    const totalIn = stockMovementData.map(item => item.total_in || 0);
    const totalOut = stockMovementData.map(item => item.total_out || 0);

    stockChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels.length ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: t('dashboard.stockIn'),
                    data: totalIn.length ? totalIn : [100, 150, 120, 180, 200, 170],
                    borderColor: "rgb(16, 185, 129)",
                    backgroundColor: "rgba(16, 185, 129, 0.1)",
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: t('dashboard.stockOut'),
                    data: totalOut.length ? totalOut : [80, 120, 100, 150, 180, 150],
                    borderColor: "rgb(239, 68, 68)",
                    backgroundColor: "rgba(239, 68, 68, 0.1)",
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
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: t('dashboard.month'),
                    },
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: t('dashboard.quantity'),
                    },
                },
            },
        },
    });
};

// Initialize products by category chart
const initCategoryChart = () => {
    if (!categoryChartRef.value) return;

    if (categoryChart) {
        categoryChart.destroy();
    }

    const ctx = categoryChartRef.value.getContext("2d");

    const categoryData = chartData.value.products_by_category || [];
    const labels = categoryData.map(item => item.label);
    const values = categoryData.map(item => item.value);

    const colors = [
        'rgb(59, 130, 246)',
        'rgb(16, 185, 129)',
        'rgb(139, 92, 246)',
        'rgb(245, 158, 11)',
        'rgb(239, 68, 68)',
        'rgb(236, 72, 153)',
        'rgb(20, 184, 166)',
        'rgb(99, 102, 241)',
    ];

    categoryChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: labels.length ? labels : [t('dashboard.noData')],
            datasets: [
                {
                    data: values.length ? values : [1],
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: "bottom",
                    labels: {
                        boxWidth: 12,
                        padding: 10,
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
    if (!dateString) return "";
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return t('dashboard.justNow');
    if (diffInSeconds < 3600)
        return `${Math.floor(diffInSeconds / 60)} ${t('dashboard.minutesAgo')}`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)} ${t('dashboard.hoursAgo')}`;
    return `${Math.floor(diffInSeconds / 86400)} ${t('dashboard.daysAgo')}`;
};

// Get work order status color
const getWorkOrderStatusColor = (status) => {
    const colors = {
        draft: 'bg-gray-500',
        pending: 'bg-yellow-500',
        assigned: 'bg-blue-500',
        in_progress: 'bg-indigo-500',
        on_hold: 'bg-orange-500',
        completed: 'bg-green-500',
        cancelled: 'bg-red-500',
    };
    return colors[status] || 'bg-gray-500';
};

// Get work order status badge color
const getWorkOrderStatusBadgeColor = (status) => {
    const colors = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        assigned: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        in_progress: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
        on_hold: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
};

const handleQuickAction = (action) => {
    const actionItem = quickActions.value.find((a) => a.action === action);
    if (actionItem && actionItem.route) {
        router.push(actionItem.route);
    }
};

const navigateTo = (path) => {
    router.push(path);
};

onMounted(async () => {
    await loadDashboardData();

    nextTick(() => {
        initStockChart();
        initCategoryChart();
    });

    timeInterval = setInterval(() => {
        currentTime.value = new Date();
    }, 60000);

    const refreshInterval = setInterval(() => {
        loadDashboardData();
    }, 300000);

    onUnmounted(() => {
        if (timeInterval) clearInterval(timeInterval);
        if (refreshInterval) clearInterval(refreshInterval);
        if (stockChart) {
            stockChart.destroy();
        }
        if (categoryChart) {
            categoryChart.destroy();
        }
    });
});

onUnmounted(() => {
    if (timeInterval) {
        clearInterval(timeInterval);
    }
});
</script>
