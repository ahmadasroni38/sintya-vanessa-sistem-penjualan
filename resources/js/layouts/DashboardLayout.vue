<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 overflow-x-hidden">
        <!-- Modern Sidebar -->
        <div
            :class="[
                'hs-overlay hs-overlay-open:translate-x-0 transition-all duration-300 transform fixed top-0 start-0 bottom-0 z-[60] bg-white dark:bg-gray-800 border-e border-gray-200 dark:border-gray-700 hs-overlay-backdrop-open:bg-gray-900/20 dark:hs-overlay-backdrop-open:bg-black/30 lg:block lg:end-auto lg:bottom-0 shadow-xl flex flex-col',
                sidebarState === 'mobile-collapsed' ? '-translate-x-full' : '',
                sidebarState === 'mobile-expanded' ? 'w-72 translate-x-0' : '',
                sidebarState === 'desktop-collapsed'
                    ? 'lg:w-24 lg:-translate-x-0 overflow-hidden'
                    : '',
                sidebarState === 'desktop-hovered'
                    ? 'lg:w-72 lg:-translate-x-0'
                    : '',
                sidebarState === 'desktop-expanded'
                    ? 'w-72 translate-x-0 lg:translate-x-0'
                    : '',
            ]"
            id="application-sidebar"
            @mouseenter="handleMouseEnter"
            @mouseleave="handleMouseLeave"
        >
            <!-- Sidebar Header with Toggle -->
            <div
                class="flex items-center justify-between px-6 pt-7 pb-5 border-b border-gray-100 dark:border-gray-700"
                :class="
                    sidebarState === 'desktop-collapsed'
                        ? 'lg:justify-center lg:px-3'
                        : ''
                "
            >
                <!-- Logo Section -->
                <a
                    class="flex items-center gap-3 text-xl font-bold dark:text-white group"
                    href="#"
                    aria-label="Brand"
                >
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center group-hover:from-primary-500 group-hover:to-primary-700 transition-all duration-200"
                        :class="
                            sidebarState === 'desktop-collapsed'
                                ? 'lg:w-10 lg:h-10'
                                : ''
                        "
                    >
                        <BuildingOffice2Icon
                            class="w-7 h-7 text-white"
                            :class="
                                sidebarState === 'desktop-collapsed'
                                    ? 'lg:w-6 lg:h-6'
                                    : ''
                            "
                        />
                    </div>
                    <span
                        v-if="sidebarState !== 'desktop-collapsed'"
                        class="bg-gradient-to-r from-primary-500 to-primary-600 bg-clip-text text-transparent dark:from-primary-400 dark:to-primary-500 transition-opacity duration-300"
                        >AdminPanel</span
                    >
                </a>

                <!-- Toggle Button -->
                <button
                    @click="toggleSidebar"
                    class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                    :class="
                        sidebarState === 'desktop-collapsed' ? 'lg:hidden' : ''
                    "
                    :title="
                        sidebarState === 'desktop-collapsed'
                            ? $t('sidebar.expandSidebar')
                            : $t('sidebar.collapseSidebar')
                    "
                >
                    <component :is="toggleIcon" class="w-5 h-5" />
                </button>
            </div>

            <!-- Search Section in Sidebar -->
            <div
                class="px-6 py-4 transition-all duration-300"
                :class="sidebarState === 'desktop-collapsed' ? 'lg:hidden' : ''"
            >
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                    >
                        <MagnifyingGlassIcon class="w-4 h-4 text-gray-400" />
                    </div>
                    <input
                        v-model="searchQuery"
                        @input="handleSidebarSearch"
                        type="text"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-primary-400 transition-all duration-200"
                        :placeholder="$t('sidebar.search')"
                    />
                </div>
            </div>

            <!-- Navigation -->
            <nav
                class="px-6 pb-6 flex-1 overflow-y-auto max-h-[calc(100vh-250px)] transition-all duration-300"
                :class="sidebarState === 'desktop-collapsed' ? 'lg:px-3' : ''"
            >
                <div class="space-y-8">
                    <!-- Main Navigation -->
                    <div v-if="filteredMenuSections.main.items.length > 0">
                        <h3
                            v-if="sidebarState !== 'desktop-collapsed'"
                            class="mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 transition-opacity duration-300"
                        >
                            {{ $t(filteredMenuSections.main.title) }}
                        </h3>
                        <ul class="space-y-1">
                            <li
                                v-for="item in filteredMenuSections.main.items"
                                :key="item.route"
                                class="relative group"
                            >
                                <router-link
                                    :to="item.route"
                                    :class="[
                                        'group flex items-center gap-x-3 py-3 px-3 text-sm font-medium rounded-xl transition-all duration-200',
                                        $route.name === item.routeName
                                            ? 'bg-gradient-to-r from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                                            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white',
                                        sidebarState === 'desktop-collapsed'
                                            ? 'lg:justify-center lg:px-4'
                                            : '',
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="[
                                            'w-5 h-5 transition-transform duration-200 group-hover:scale-110 flex-shrink-0',
                                            $route.name === item.routeName
                                                ? 'text-white'
                                                : 'text-gray-500 dark:text-gray-400',
                                        ]"
                                    />
                                    <span
                                        v-if="
                                            sidebarState !== 'desktop-collapsed'
                                        "
                                        class="transition-opacity duration-300"
                                    >
                                        {{ $t(item.text) }}
                                    </span>
                                </router-link>

                                <!-- Tooltip for collapsed state -->
                                <div
                                    v-if="sidebarState === 'desktop-collapsed'"
                                    class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-w-64 lg:block hidden"
                                >
                                    {{ $t(item.text) }}
                                    <div
                                        class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-t-transparent border-b-4 border-b-transparent border-r-4 border-r-gray-900"
                                    ></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Sales Management Section -->
                    <div
                        v-if="
                            filteredMenuSections.salesManagement.items.length >
                            0
                        "
                    >
                        <h3
                            v-if="sidebarState !== 'desktop-collapsed'"
                            class="mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 transition-opacity duration-300"
                        >
                            {{ $t(filteredMenuSections.salesManagement.title) }}
                        </h3>
                        <ul class="space-y-1">
                            <li
                                v-for="item in filteredMenuSections
                                    .salesManagement.items"
                                :key="item.route"
                                class="relative group"
                            >
                                <router-link
                                    :to="item.route"
                                    :class="[
                                        'group flex items-center gap-x-3 py-3 px-3 text-sm font-medium rounded-xl transition-all duration-200',
                                        $route.name === item.routeName
                                            ? 'bg-gradient-to-r from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                                            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white',
                                        sidebarState === 'desktop-collapsed'
                                            ? 'lg:justify-center lg:px-4'
                                            : '',
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="[
                                            'w-5 h-5 transition-transform duration-200 group-hover:scale-110 flex-shrink-0',
                                            $route.name === item.routeName
                                                ? 'text-white'
                                                : 'text-gray-500 dark:text-gray-400',
                                        ]"
                                    />
                                    <span
                                        v-if="
                                            sidebarState !== 'desktop-collapsed'
                                        "
                                        class="transition-opacity duration-300"
                                    >
                                        {{ $t(item.text) }}
                                    </span>
                                </router-link>

                                <!-- Tooltip for collapsed state -->
                                <div
                                    v-if="sidebarState === 'desktop-collapsed'"
                                    class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-w-64 lg:block hidden"
                                >
                                    {{ $t(item.text) }}
                                    <div
                                        class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-t-transparent border-b-4 border-b-transparent border-r-4 border-r-gray-900"
                                    ></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Warehouse Management Section -->
                    <div
                        v-if="
                            filteredMenuSections.warehouseManagement.items
                                .length > 0
                        "
                    >
                        <h3
                            v-if="sidebarState !== 'desktop-collapsed'"
                            class="mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 transition-opacity duration-300"
                        >
                            {{ $t(filteredMenuSections.warehouseManagement.title) }}
                        </h3>
                        <ul class="space-y-1">
                            <li
                                v-for="item in filteredMenuSections
                                    .warehouseManagement.items"
                                :key="item.route"
                                class="relative group"
                            >
                                <router-link
                                    :to="item.route"
                                    :class="[
                                        'group flex items-center gap-x-3 py-3 px-3 text-sm font-medium rounded-xl transition-all duration-200',
                                        $route.name === item.routeName
                                            ? 'bg-gradient-to-r from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                                            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white',
                                        sidebarState === 'desktop-collapsed'
                                            ? 'lg:justify-center lg:px-4'
                                            : '',
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="[
                                            'w-5 h-5 transition-transform duration-200 group-hover:scale-110 flex-shrink-0',
                                            $route.name === item.routeName
                                                ? 'text-white'
                                                : 'text-gray-500 dark:text-gray-400',
                                        ]"
                                    />
                                    <span
                                        v-if="
                                            sidebarState !== 'desktop-collapsed'
                                        "
                                        class="transition-opacity duration-300"
                                    >
                                        {{ $t(item.text) }}
                                    </span>
                                </router-link>

                                <!-- Tooltip for collapsed state -->
                                <div
                                    v-if="sidebarState === 'desktop-collapsed'"
                                    class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-w-64 lg:block hidden"
                                >
                                    {{ $t(item.text) }}
                                    <div
                                        class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-t-transparent border-b-4 border-b-transparent border-r-4 border-r-gray-900"
                                    ></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Manajemen Accounting Section -->
                    <div
                        v-if="
                            filteredMenuSections.accountingManagement.items
                                .length > 0
                        "
                    >
                        <h3
                            v-if="sidebarState !== 'desktop-collapsed'"
                            class="mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 transition-opacity duration-300"
                        >
                            {{
                                $t(filteredMenuSections.accountingManagement.title)
                            }}
                        </h3>
                        <ul class="space-y-1">
                            <li
                                v-for="item in filteredMenuSections
                                    .accountingManagement.items"
                                :key="item.route"
                                class="relative group"
                            >
                                <router-link
                                    :to="item.route"
                                    :class="[
                                        'group flex items-center gap-x-3 py-3 px-3 text-sm font-medium rounded-xl transition-all duration-200',
                                        $route.name === item.routeName
                                            ? 'bg-gradient-to-r from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                                            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white',
                                        sidebarState === 'desktop-collapsed'
                                            ? 'lg:justify-center lg:px-4'
                                            : '',
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="[
                                            'w-5 h-5 transition-transform duration-200 group-hover:scale-110 flex-shrink-0',
                                            $route.name === item.routeName
                                                ? 'text-white'
                                                : 'text-gray-500 dark:text-gray-400',
                                        ]"
                                    />
                                    <span
                                        v-if="
                                            sidebarState !== 'desktop-collapsed'
                                        "
                                        class="transition-opacity duration-300"
                                    >
                                        {{ $t(item.text) }}
                                    </span>
                                </router-link>

                                <!-- Tooltip for collapsed state -->
                                <div
                                    v-if="sidebarState === 'desktop-collapsed'"
                                    class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-w-64 lg:block hidden"
                                >
                                    {{ $t(item.text) }}
                                    <div
                                        class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-t-transparent border-b-4 border-b-transparent border-r-4 border-r-gray-900"
                                    ></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- User Management Section -->
                    <div
                        v-if="
                            filteredMenuSections.userManagement.items.length > 0
                        "
                    >
                        <h3
                            v-if="sidebarState !== 'desktop-collapsed'"
                            class="mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 transition-opacity duration-300"
                        >
                            {{ $t(filteredMenuSections.userManagement.title) }}
                        </h3>
                        <ul class="space-y-1">
                            <li
                                v-for="item in filteredMenuSections
                                    .userManagement.items"
                                :key="item.route"
                                class="relative group"
                            >
                                <router-link
                                    :to="item.route"
                                    :class="[
                                        'group flex items-center gap-x-3 py-3 px-3 text-sm font-medium rounded-xl transition-all duration-200',
                                        $route.name === item.routeName
                                            ? 'bg-gradient-to-r from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                                            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white',
                                        sidebarState === 'desktop-collapsed'
                                            ? 'lg:justify-center lg:px-4'
                                            : '',
                                    ]"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="[
                                            'w-5 h-5 transition-transform duration-200 group-hover:scale-110 flex-shrink-0',
                                            $route.name === item.routeName
                                                ? 'text-white'
                                                : 'text-gray-500 dark:text-gray-400',
                                        ]"
                                    />
                                    <span
                                        v-if="
                                            sidebarState !== 'desktop-collapsed'
                                        "
                                        class="transition-opacity duration-300"
                                    >
                                        {{ $t(item.text) }}
                                    </span>
                                </router-link>

                                <!-- Tooltip for collapsed state -->
                                <div
                                    v-if="sidebarState === 'desktop-collapsed'"
                                    class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-w-64 lg:block hidden"
                                >
                                    {{ $t(item.text) }}
                                    <div
                                        class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-t-transparent border-b-4 border-b-transparent border-r-4 border-r-gray-900"
                                    ></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div
                class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 transition-all duration-300"
                :class="sidebarState === 'desktop-collapsed' ? 'lg:px-2' : ''"
            >
                <!-- System Settings -->
                <div class="mb-3">
                    <router-link
                        to="/settings"
                        :class="[
                            'group flex items-center gap-x-3 py-3 px-3 text-sm font-medium rounded-xl transition-all duration-200',
                            $route.name === 'Settings'
                                ? 'bg-gradient-to-r from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white',
                            sidebarState === 'desktop-collapsed'
                                ? 'lg:justify-center lg:px-4'
                                : '',
                        ]"
                    >
                        <AdjustmentsHorizontalIcon
                            :class="[
                                'w-5 h-5 transition-transform duration-200 group-hover:scale-110 flex-shrink-0',
                                $route.name === 'Settings'
                                    ? 'text-white'
                                    : 'text-gray-500 dark:text-gray-400',
                            ]"
                        />
                        <span
                            v-if="sidebarState !== 'desktop-collapsed'"
                            class="transition-opacity duration-300"
                        >
                            {{ $t('sidebar.settings') }}
                        </span>
                    </router-link>

                    <!-- Tooltip for collapsed state -->
                    <div
                        v-if="sidebarState === 'desktop-collapsed'"
                        class="absolute left-full bottom-4 transform -translate-y-1/2 ml-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-w-64 lg:block hidden"
                    >
                        {{ $t('sidebar.settings') }}
                        <div
                            class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-t-transparent border-b-4 border-b-transparent border-r-4 border-r-gray-900"
                        ></div>
                    </div>
                </div>

                <!-- Upgrade to Pro -->
                <div
                    v-if="false"
                    class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-primary-50 to-primary-100 dark:from-gray-700 dark:to-gray-700"
                    :class="
                        sidebarState === 'desktop-collapsed'
                            ? 'lg:justify-center'
                            : ''
                    "
                >
                    <div
                        class="w-8 h-7 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center flex-shrink-0"
                        :class="
                            sidebarState === 'desktop-collapsed'
                                ? 'lg:w-6 lg:h-6'
                                : ''
                        "
                    >
                        <SparklesIcon
                            class="w-4 h-4 text-white"
                            :class="
                                sidebarState === 'desktop-collapsed'
                                    ? 'lg:w-3 lg:h-3'
                                    : ''
                            "
                        />
                    </div>
                    <div
                        v-if="sidebarState !== 'desktop-collapsed'"
                        class="flex-1 transition-opacity duration-300"
                    >
                        <p
                            class="text-xs font-semibold text-gray-900 dark:text-white"
                        >
                            Upgrade to Pro
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Get unlimited access
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div
            :class="[
                'w-full transition-all duration-300',
                sidebarState === 'desktop-collapsed' ? 'lg:ps-24' : '',
                sidebarState === 'desktop-hovered' ? 'lg:ps-72' : '',
                sidebarState === 'desktop-expanded' ? 'lg:ps-72' : '',
            ]"
        >
            <!-- Toggle Button for Collapsed Sidebar -->
            <button
                v-if="sidebarState === 'desktop-collapsed'"
                @click="toggleSidebar"
                class="fixed top-4 left-4 z-50 lg:flex hidden p-2 bg-white dark:bg-gray-800 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700 shadow-lg border border-gray-200 dark:border-gray-700"
                title="Show Sidebar"
            >
                <Bars3Icon class="w-5 h-5" />
            </button>
            <!-- Mobile Header -->
            <div
                class="sticky top-0 inset-x-0 z-20 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-4 sm:px-6 md:px-8 lg:hidden dark:bg-gray-800/95 dark:border-gray-700"
            >
                <div class="flex items-center justify-between py-4">
                    <!-- Navigation Toggle -->
                    <button
                        type="button"
                        class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                        data-hs-overlay="#application-sidebar"
                        aria-controls="application-sidebar"
                        aria-label="Toggle navigation"
                    >
                        <span class="sr-only">Toggle Navigation</span>
                        <Bars3Icon class="w-5 h-5" />
                    </button>

                    <!-- Mobile Logo -->
                    <a
                        class="flex items-center gap-2 text-lg font-bold dark:text-white"
                        href="#"
                    >
                        <div
                            class="w-7 h-7 bg-gradient-to-br from-primary-400 to-primary-600 rounded-md flex items-center justify-center"
                        >
                            <BuildingOffice2Icon class="w-5 h-5 text-white" />
                        </div>
                        <span
                            class="bg-gradient-to-r from-primary-500 to-primary-600 bg-clip-text text-transparent dark:from-primary-400 dark:to-primary-500"
                            >AdminPanel</span
                        >
                    </a>

                    <!-- Mobile Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Language Switcher -->
                        <div class="relative">
                            <button
                                @click="showLanguageDropdown = !showLanguageDropdown"
                                type="button"
                                class="language-button p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                                :title="$t('language.select')"
                            >
                                <GlobeAltIcon class="w-5 h-5" />
                            </button>
                            <!-- Language Dropdown -->
                            <div
                                v-if="showLanguageDropdown"
                                class="language-dropdown absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                                @click.stop
                            >
                                <div class="py-1">
                                    <button
                                        @click="changeLanguage('id')"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200"
                                        :class="{ 'bg-primary-50 text-primary-700 dark:bg-primary-900 dark:text-primary-300': locale === 'id' }"
                                    >
                                        🇮🇩 {{ $t('language.indonesian') }}
                                    </button>
                                    <button
                                        @click="changeLanguage('en')"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200"
                                        :class="{ 'bg-primary-50 text-primary-700 dark:bg-primary-900 dark:text-primary-300': locale === 'en' }"
                                    >
                                        🇺🇸 {{ $t('language.english') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="themeStore.toggleTheme"
                            type="button"
                            class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                        >
                            <MoonIcon
                                v-if="!themeStore.isDark"
                                class="w-5 h-5"
                            />
                            <SunIcon v-else class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modern Main Header -->
            <Navbar
                :page-title="getPageTitle()"
                :page-description="getPageDescription()"
                @search="handleSearch"
            />

            <!-- Main Content -->
            <main class="p-6 min-h-screen">
                <router-view />
            </main>

            <!-- Notification Container -->
            <NotificationContainer />

            <!-- Modern Footer -->
            <footer
                class="bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700"
            >
                <div class="px-6 py-6">
                    <div
                        class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-6 h-6 bg-gradient-to-br from-primary-400 to-primary-600 rounded-md flex items-center justify-center"
                                >
                                    <BuildingOffice2Icon
                                        class="w-4 h-4 text-white"
                                    />
                                </div>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white"
                                    >AdminPanel</span
                                >
                            </div>
                            <div
                                class="h-4 w-px bg-gray-300 dark:bg-gray-600"
                            ></div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                &copy; 2025 {{ $t('footer.company') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-6">
                            <span
                                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-200"
                            >
                                {{ $t('footer.version') }} 1.0.0
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useThemeStore } from "../stores/theme";
import { useAuthStore } from "../stores/auth";
import { useI18n } from "vue-i18n";
import Navbar from "../components/Layout/Navbar.vue";
import NotificationContainer from "../components/UI/NotificationContainer.vue";
import {
    BuildingOffice2Icon,
    HomeIcon,
    UsersIcon,
    MagnifyingGlassIcon,
    ChevronLeftIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    Bars3Icon,
    SparklesIcon,
    MoonIcon,
    SunIcon,
    ShieldCheckIcon,
    KeyIcon,
    TagIcon,
    TruckIcon,
    MapPinIcon,
    ArchiveBoxIcon,
    BookOpenIcon,
    BanknotesIcon,
    DocumentTextIcon,
    ChartBarIcon,
    CurrencyDollarIcon,
    ArrowsRightLeftIcon,
    ClipboardDocumentListIcon,
    AdjustmentsHorizontalIcon,
    DocumentDuplicateIcon,
    ShoppingCartIcon,
    GlobeAltIcon,
} from "@heroicons/vue/24/outline";

const route = useRoute();
const router = useRouter();
const themeStore = useThemeStore();
const authStore = useAuthStore();
const { t, locale } = useI18n();

const searchQuery = ref("");
const sidebarCollapsed = ref(false);
const isHovering = ref(false);
const isMobile = ref(false);
const showLanguageDropdown = ref(false);

// Check if mobile device
const checkIfMobile = () => {
    isMobile.value = window.innerWidth < 1024;
};

// Handle mouse enter for sidebar expansion on hover
const handleMouseEnter = () => {
    if (!isMobile.value && sidebarCollapsed.value) {
        isHovering.value = true;
    }
};

// Handle mouse leave for sidebar collapse on hover out
const handleMouseLeave = () => {
    if (!isMobile.value && sidebarCollapsed.value) {
        isHovering.value = false;
    }
};

// Menu data structure
const menuSections = {
    main: {
        title: "sidebar.main",
        items: [
            {
                text: "sidebar.dashboard",
                route: "/",
                routeName: "Dashboard",
                icon: HomeIcon,
            },
        ],
    },
    accountingManagement: {
        title: "sidebar.accountingManagement",
        items: [
            {
                text: "sidebar.journalEntries",
                route: "/journal-entries",
                routeName: "journal-entries.index",
                icon: BookOpenIcon,
            },
            {
                text: "sidebar.coa",
                route: "/chart-of-accounts",
                routeName: "coa.index",
                icon: ClipboardDocumentListIcon,
            },
            {
                text: "sidebar.neracaLajur",
                route: "/reports/neraca-lajur",
                routeName: "reports.neraca-lajur",
                icon: DocumentDuplicateIcon,
            },
            {
                text: "sidebar.neraca",
                route: "/reports/neraca",
                routeName: "reports.neraca",
                icon: DocumentTextIcon,
            },
            {
                text: "sidebar.labaRugi",
                route: "/reports/laba-rugi",
                routeName: "reports.laba-rugi",
                icon: ChartBarIcon,
            },
            {
                text: "sidebar.perubahanModal",
                route: "/reports/perubahan-modal",
                routeName: "reports.perubahan-modal",
                icon: CurrencyDollarIcon,
            },
            {
                text: "sidebar.arusKas",
                route: "/reports/arus-kas",
                routeName: "reports.arus-kas",
                icon: BanknotesIcon,
            },
        ],
    },
    warehouseManagement: {
        title: "sidebar.warehouseManagement",
        items: [
            {
                text: "sidebar.products",
                route: "/products",
                routeName: "products.index",
                icon: TagIcon,
            },
            {
                text: "sidebar.locations",
                route: "/locations",
                routeName: "locations.index",
                icon: MapPinIcon,
            },
            {
                text: "sidebar.stockMasuk",
                route: "/stock-masuk",
                routeName: "stock-masuk.index",
                icon: ArchiveBoxIcon,
            },
            {
                text: "sidebar.mutasi",
                route: "/mutasi",
                routeName: "mutasi.index",
                icon: ArrowsRightLeftIcon,
            },
            {
                text: "sidebar.adjustment",
                route: "/adjustment",
                routeName: "adjustment.index",
                icon: AdjustmentsHorizontalIcon,
            },
            {
                text: "sidebar.stockopname",
                route: "/stockopname",
                routeName: "stockopname.index",
                icon: ClipboardDocumentListIcon,
            },
            {
                text: "sidebar.bukuStock",
                route: "/buku-stock",
                routeName: "buku-stock.index",
                icon: BookOpenIcon,
            },
        ],
    },
    userManagement: {
        title: "sidebar.userManagement",
        items: [
            {
                text: "sidebar.users",
                route: "/users",
                routeName: "Users",
                icon: UsersIcon,
            },
            {
                text: "sidebar.roles",
                route: "/roles",
                routeName: "Roles",
                icon: ShieldCheckIcon,
            },
            {
                text: "sidebar.permissions",
                route: "/permissions",
                routeName: "Permissions",
                icon: KeyIcon,
            },
        ],
    },
    salesManagement: {
        title: "sidebar.salesManagement",
        items: [
            {
                text: "sidebar.sales",
                route: "/sales",
                routeName: "sales.index",
                icon: ShoppingCartIcon,
            },
            {
                text: "sidebar.customers",
                route: "/customers",
                routeName: "customers.index",
                icon: UsersIcon,
            },
        ],
    },
};

// Computed property for filtered menu sections
const filteredMenuSections = computed(() => {
    try {
        if (!searchQuery.value || !searchQuery.value.trim()) {
            return menuSections;
        }

        const query = searchQuery.value.toLowerCase().trim();
        const filtered = {};

        Object.keys(menuSections).forEach((sectionKey) => {
            const section = menuSections[sectionKey];
            const filteredItems = section.items.filter((item) => {
                try {
                    return t(item.text).toLowerCase().includes(query);
                } catch (error) {
                    console.error('Translation error for:', item.text, error);
                    return false;
                }
            });

            // Always include the section, even if no items match
            filtered[sectionKey] = {
                ...section,
                items: filteredItems,
            };
        });

        return filtered;
    } catch (error) {
        console.error('Error in filteredMenuSections:', error);
        // Return the original menuSections as fallback
        return menuSections;
    }
});

// Computed property for sidebar state
const sidebarState = computed(() => {
    if (isMobile.value) {
        return sidebarCollapsed.value ? "mobile-collapsed" : "mobile-expanded";
    } else {
        if (sidebarCollapsed.value) {
            return isHovering.value ? "desktop-hovered" : "desktop-collapsed";
        } else {
            return "desktop-expanded";
        }
    }
});

// Computed property for toggle icon
const toggleIcon = computed(() => {
    return sidebarState.value === "desktop-collapsed"
        ? ChevronDoubleRightIcon
        : ChevronDoubleLeftIcon;
});

// Toggle sidebar function
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

const getPageTitle = () => {
    const titles = {
        Dashboard: "header.dashboard",
        Users: "header.users",
        Roles: "header.roles",
        Permissions: "header.permissions",
        settings: "header.settings",
        "journal-entries.index": "header.journalEntries",
        "coa.index": "header.coa",
        "reports.neraca-lajur": "header.neracaLajur",
        "reports.neraca": "header.neraca",
        "reports.laba-rugi": "header.labaRugi",
        "reports.perubahan-modal": "header.perubahanModal",
        "reports.arus-kas": "header.arusKas",
        "locations.index": "header.locations",
        "stock-masuk.index": "header.stockMasuk",
        "mutasi.index": "header.mutasi",
        "adjustment.index": "header.adjustment",
        "stockopname.index": "header.stockopname",
        "buku-stock.index": "header.bukuStock",
        "sales.index": "header.sales",
        "products.index": "header.products",
    };
    const key = titles[route.name] || "header.dashboard";
    return t(key);
};

const getPageDescription = () => {
    const descriptions = {
        Dashboard: "Welcome back! Here's what's happening with your business.",
        Profile: "Manage your account settings and preferences.",
        Settings: "Configure your application settings.",
        Users: "Manage user accounts and permissions.",
        Analytics: "View detailed analytics and reports.",
        Products: "Manage your product catalog.",
        Orders: "Track and manage customer orders.",
    };
    return descriptions[route.name] || "Welcome to your dashboard";
};

const handleSearch = (query) => {
    searchQuery.value = query;
    console.log("Search query:", query);
};

const handleSidebarSearch = () => {
    // The search is already handled by the computed property 'filteredMenuSections'
    // This function ensures the search input is properly reactive
    console.log("Sidebar search query:", searchQuery.value);
};

const changeLanguage = (lang) => {
    locale.value = lang;
    showLanguageDropdown.value = false;
    // Store language preference in localStorage
    localStorage.setItem('language', lang);
};

const ensureUserData = async () => {
    if (!authStore.user && authStore.token) {
        await authStore.checkAuth();
    }
};

onMounted(async () => {
    await ensureUserData();

    // Check if mobile on mount
    checkIfMobile();

    // Add event listener for window resize
    window.addEventListener("resize", checkIfMobile);

    // Load saved language preference
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage && (savedLanguage === 'en' || savedLanguage === 'id')) {
        locale.value = savedLanguage;
    }

    // Add click outside handler for language dropdown
    document.addEventListener('click', (event) => {
        const target = event.target;
        const dropdown = document.querySelector('.language-dropdown');
        const button = document.querySelector('.language-button');
        if (!button?.contains(target) && !dropdown?.contains(target)) {
            showLanguageDropdown.value = false;
        }
    });
});
</script>
