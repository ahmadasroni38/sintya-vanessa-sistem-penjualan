<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Assets
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage your organization's assets and inventory
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <ArchiveBoxIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Assets
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalAssets }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg"
                    >
                        <CheckCircleIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Active Assets
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activeAssets }}
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
                        <WrenchScrewdriverIcon
                            class="w-6 h-6 text-orange-600 dark:text-orange-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Under Maintenance
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ maintenanceAssets }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <ExclamationTriangleIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Poor Condition
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ poorConditionAssets }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg"
                    >
                        <CurrencyDollarIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Value
                        </p>
                        <p
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            ${{ totalValue.toLocaleString() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <FormInput
                    v-model="filters.search"
                    placeholder="Search assets..."
                    @input="debouncedSearch"
                >
                    <template #prefix>
                        <MagnifyingGlassIcon class="w-4 h-4" />
                    </template>
                </FormInput>
                <!-- Category Filter -->
                <FormSelect
                    v-model="filters.category_id"
                    placeholder="All Categories"
                    :options="categoryOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Location Filter -->
                <FormSelect
                    v-model="filters.location_id"
                    placeholder="All Locations"
                    :options="locationOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Status Filter -->
                <FormSelect
                    v-model="filters.status"
                    placeholder="All Statuses"
                    :options="statusOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Condition Filter -->
                <FormSelect
                    v-model="filters.condition"
                    placeholder="All Conditions"
                    :options="conditionOptions"
                    @update:modelValue="handleFilterChange"
                />
            </div>
            <div class="flex justify-end mt-4">
                <Button
                    variant="secondary"
                    @click="clearFilters"
                    :disabled="!hasActiveFilters"
                >
                    Clear Filters
                </Button>
            </div>
        </div>

        <!-- Bulk Actions Dropdown -->
        <div
            v-if="selectedAssets.length > 0"
            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span
                        class="text-sm font-medium text-red-900 dark:text-red-100"
                    >
                        {{ selectedAssets.length }} asset{{
                            selectedAssets.length === 1 ? "" : "s"
                        }}
                        selected
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        variant="secondary"
                        size="sm"
                        @click="
                            handleBulkAction('update-status', selectedAssets)
                        "
                    >
                        <ArrowPathIcon class="w-4 h-4 mr-2" />
                        Update Status
                    </Button>
                    <Button
                        variant="primary"
                        size="sm"
                        @click="handleBulkPrintQRCode(selectedAssets)"
                    >
                        <PrinterIcon class="w-4 h-4 mr-2" />
                        Export QR Codes
                    </Button>
                    <Button
                        variant="danger"
                        size="sm"
                        @click="handleBulkAction('delete', selectedAssets)"
                    >
                        <TrashIcon class="w-4 h-4 mr-2" />
                        Delete
                    </Button>
                    <Button
                        variant="secondary"
                        size="sm"
                        @click="clearSelection"
                    >
                        Clear Selection
                    </Button>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Assets"
            description="A comprehensive list of all assets in your inventory."
            :data="assets.data || []"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="Add Asset"
            :show-filters="false"
            :show-bulk-actions="false"
            :show-export="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search assets..."
            empty-title="No assets found"
            empty-description="Get started by adding your first asset."
            @add="handleAddAsset"
            @edit="handleEditAsset"
            @delete="handleDeleteAsset"
            @bulk-action="handleBulkAction"
            @export="handleExportAssets"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshAssets"
        >
            <!-- Custom Image Column -->
            <template #column-image="{ item }">
                <div
                    v-if="item.image_url"
                    class="w-12 h-12 rounded-lg overflow-hidden"
                >
                    <img
                        :src="item.image_url"
                        :alt="item.name"
                        class="w-full h-full object-cover"
                    />
                </div>
                <div
                    v-else
                    class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center"
                >
                    <ArchiveBoxIcon class="w-6 h-6 text-gray-400" />
                </div>
            </template>

            <!-- Custom Name Column with Hierarchy -->
            <template #column-name="{ item }">
                <div class="flex items-center">
                    <div
                        :style="{
                            paddingLeft: `${item.hierarchy_level * 20}px`,
                        }"
                    >
                        <p
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ item.code }}
                        </p>
                        <p
                            v-if="item.serial_number"
                            class="text-xs text-red-600 dark:text-red-400"
                        >
                            SN: {{ item.serial_number }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Category Column -->
            <template #column-category="{ item }">
                <div v-if="item.category" class="flex items-center">
                    <div
                        class="w-3 h-3 rounded-full mr-2"
                        :style="{ backgroundColor: item.category.color }"
                    ></div>
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ item.category.name }}
                    </span>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    No Category
                </span>
            </template>

            <!-- Custom Location Column -->
            <template #column-location="{ item }">
                <div v-if="item.location" class="max-w-xs">
                    <p class="text-sm text-gray-900 dark:text-white truncate">
                        {{ item.location.name }}
                    </p>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 truncate"
                    >
                        {{ item.location.full_address }}
                    </p>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    No Location
                </span>
            </template>

            <!-- Custom Value Column -->
            <template #column-value="{ item }">
                <div>
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        ${{
                            item.current_value
                                ? item.current_value.toLocaleString()
                                : "0"
                        }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Book: ${{
                            item.book_value
                                ? item.book_value.toLocaleString()
                                : "0"
                        }}
                    </p>
                </div>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            getStatusColor(item.status),
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                        >{{ item.status.replace("_", " ") }}</span
                    >
                </div>
            </template>

            <!-- Custom Condition Column -->
            <template #column-condition="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getConditionBadgeClass(item.condition),
                    ]"
                >
                    {{ item.condition }}
                </span>
            </template>

            <!-- Custom Warranty Column -->
            <template #column-warranty="{ item }">
                <div v-if="item.warranty_expiry">
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ formatDate(item.warranty_expiry) }}
                    </p>
                    <p
                        :class="[
                            'text-xs',
                            item.is_warranty_valid
                                ? 'text-red-600 dark:text-red-400'
                                : 'text-red-600 dark:text-red-400',
                        ]"
                    >
                        {{ item.is_warranty_valid ? "Valid" : "Expired" }}
                    </p>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    No Warranty
                </span>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleViewAsset(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handlePrintQRCode(item)"
                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-100 rounded-lg transition-colors duration-200 dark:hover:text-primary-400 dark:hover:bg-indigo-900/20"
                        title="Print QR Code"
                    >
                        <PrinterIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleEditAsset(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Edit Asset"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleUpdateStatus(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Update Status"
                    >
                        <ArrowPathIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeleteAsset(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Asset"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Pagination -->
        <div v-if="assets.last_page > 1" class="flex justify-center">
            <div class="flex items-center gap-2">
                <Button
                    variant="secondary"
                    :disabled="assets.current_page === 1"
                    @click="goToPage(assets.current_page - 1)"
                >
                    Previous
                </Button>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Page {{ assets.current_page }} of {{ assets.last_page }}
                </span>
                <Button
                    variant="secondary"
                    :disabled="assets.current_page === assets.last_page"
                    @click="goToPage(assets.current_page + 1)"
                >
                    Next
                </Button>
            </div>
        </div>

        <!-- Create/Edit Asset Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                {{ showCreateModal ? "Add Asset" : "Edit Asset" }}
            </template>

            <form @submit.prevent="saveAsset" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="assetForm.name"
                            label="Asset Name"
                            placeholder="Enter asset name"
                            :error="getFieldError('name')"
                            required
                            @blur="handleFieldBlur('name')"
                        />
                        <FormInput
                            v-model="assetForm.code"
                            label="Asset Code"
                            placeholder="Enter asset code (optional - auto-generated if empty)"
                            hint="Leave empty to auto-generate a unique code based on the asset name"
                            :error="getFieldError('code')"
                            @blur="handleFieldBlur('code')"
                        />
                    </div>
                    <FormTextarea
                        v-model="assetForm.description"
                        label="Description"
                        placeholder="Enter asset description"
                        :error="getFieldError('description')"
                        rows="3"
                        @blur="handleFieldBlur('description')"
                    />
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Category Select -->
                        <FormSelect
                            v-model="assetForm.category_id"
                            label="Category"
                            placeholder="Select category"
                            :options="categoryOptions"
                            :error="getFieldError('category_id')"
                            required
                            @blur="handleFieldBlur('category_id')"
                            @update:modelValue="handleFieldBlur('category_id')"
                        />

                        <!-- Location Select -->
                        <FormSelect
                            v-model="assetForm.location_id"
                            label="Location"
                            placeholder="Select location"
                            :options="locationOptions"
                            :error="getFieldError('location_id')"
                            required
                            @blur="handleFieldBlur('location_id')"
                            @update:modelValue="handleFieldBlur('location_id')"
                        />

                        <!-- Parent Asset Select -->
                        <FormSelect
                            v-model="assetForm.parent_asset_id"
                            label="Parent Asset"
                            placeholder="Select parent asset (optional)"
                            :options="parentAssetOptions"
                            :error="getFieldError('parent_asset_id')"
                            @blur="handleFieldBlur('parent_asset_id')"
                            @update:modelValue="
                                handleFieldBlur('parent_asset_id')
                            "
                        />
                    </div>
                </div>

                <!-- Asset Details -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Asset Details
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="assetForm.serial_number"
                            label="Serial Number"
                            placeholder="Enter serial number"
                            :error="getFieldError('serial_number')"
                            @blur="handleFieldBlur('serial_number')"
                        />
                        <FormInput
                            v-model="assetForm.model_number"
                            label="Model Number"
                            placeholder="Enter model number"
                            :error="getFieldError('model_number')"
                            @blur="handleFieldBlur('model_number')"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="assetForm.manufacturer"
                            label="Manufacturer"
                            placeholder="Enter manufacturer"
                            :error="getFieldError('manufacturer')"
                            @blur="handleFieldBlur('manufacturer')"
                        />
                        <FormInput
                            v-model="assetForm.quantity"
                            label="Quantity"
                            type="number"
                            min="1"
                            placeholder="Enter quantity"
                            :error="getFieldError('quantity')"
                            required
                            @blur="handleFieldBlur('quantity')"
                        />
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Financial Information
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <FormInput
                            v-model="assetForm.purchase_price"
                            label="Purchase Price"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter purchase price"
                            :error="getFieldError('purchase_price')"
                            @blur="handleFieldBlur('purchase_price')"
                        />
                        <FormInput
                            v-model="assetForm.current_value"
                            label="Current Value"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter current value"
                            :error="getFieldError('current_value')"
                            @blur="handleFieldBlur('current_value')"
                        />
                        <FormInput
                            v-model="assetForm.purchase_date"
                            label="Purchase Date"
                            type="date"
                            :error="getFieldError('purchase_date')"
                            @blur="handleFieldBlur('purchase_date')"
                        />
                    </div>
                </div>

                <!-- Status & Condition -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Status & Condition
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Status Select -->
                        <FormSelect
                            v-model="assetForm.status"
                            label="Status"
                            placeholder="Select status"
                            :options="statusOptions"
                            :error="getFieldError('status')"
                            required
                            @blur="handleFieldBlur('status')"
                            @update:modelValue="handleFieldBlur('status')"
                        />

                        <!-- Condition Select -->
                        <FormSelect
                            v-model="assetForm.condition"
                            label="Condition"
                            placeholder="Select condition"
                            :options="conditionOptions"
                            :error="getFieldError('condition')"
                            required
                            @blur="handleFieldBlur('condition')"
                            @update:modelValue="handleFieldBlur('condition')"
                        />

                        <FormInput
                            v-model="assetForm.warranty_expiry"
                            label="Warranty Expiry"
                            type="date"
                            :error="getFieldError('warranty_expiry')"
                            @blur="handleFieldBlur('warranty_expiry')"
                        />
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Asset Image
                    </h3>
                    <div class="flex items-center gap-4">
                        <div
                            v-if="currentImage || assetForm.image"
                            class="w-24 h-24 rounded-lg overflow-hidden"
                        >
                            <img
                                :src="
                                    currentImage ||
                                    (assetForm.image
                                        ? URL.createObjectURL(assetForm.image)
                                        : '')
                                "
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="flex-1">
                            <input
                                ref="imageInput"
                                type="file"
                                accept="image/*"
                                @change="handleImageChange"
                                class="hidden"
                            />
                            <Button
                                type="button"
                                variant="secondary"
                                @click="$refs.imageInput.click()"
                            >
                                <PhotoIcon class="w-4 h-4 mr-2" />
                                {{
                                    currentImage || assetForm.image
                                        ? "Change Image"
                                        : "Upload Image"
                                }}
                            </Button>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                            >
                                PNG, JPG, GIF up to 2MB
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Specifications & Notes -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Additional Information
                    </h3>
                    <FormTextarea
                        v-model="assetForm.notes"
                        label="Notes"
                        placeholder="Enter any additional notes"
                        :error="getFieldError('notes')"
                        rows="3"
                        @blur="handleFieldBlur('notes')"
                    />
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
                        {{ showCreateModal ? "Add Asset" : "Update Asset" }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View Asset Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="xl">
            <template #title>Asset Details</template>

            <div v-if="selectedAsset" class="space-y-6">
                <!-- Asset Header -->
                <div class="flex items-start gap-4">
                    <div
                        v-if="selectedAsset.image_url"
                        class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0"
                    >
                        <img
                            :src="selectedAsset.image_url"
                            :alt="selectedAsset.name"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3
                                    class="text-xl font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ selectedAsset.name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Code: {{ selectedAsset.code }}
                                </p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getConditionBadgeClass(
                                                selectedAsset.condition
                                            ),
                                        ]"
                                    >
                                        {{ selectedAsset.condition }}
                                    </span>
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getStatusBadgeClass(selectedAsset.status),
                                        ]"
                                    >
                                        {{ selectedAsset.status.replace("_", " ") }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    @click="handlePrintBarcode(selectedAsset)"
                                    variant="secondary"
                                    size="sm"
                                >
                                    <PrinterIcon class="w-4 h-4 mr-2" />
                                    Print Barcode
                                </Button>
                                <Button
                                    @click="handlePrintQRCode(selectedAsset)"
                                    variant="secondary"
                                    size="sm"
                                >
                                    <PrinterIcon class="w-4 h-4 mr-2" />
                                    Print QR Code
                                </Button>
                                <Button
                                    @click="handleEditAsset(selectedAsset)"
                                    variant="primary"
                                    size="sm"
                                >
                                    <PencilIcon class="w-4 h-4 mr-2" />
                                    Edit Asset
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asset Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Basic Information
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Serial Number:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedAsset.serial_number || "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Model Number:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedAsset.model_number || "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Manufacturer:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedAsset.manufacturer || "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Quantity:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{ selectedAsset.quantity }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Financial Information
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Purchase Price:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >${{
                                        selectedAsset.purchase_price
                                            ? selectedAsset.purchase_price.toLocaleString()
                                            : "0"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Current Value:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >${{
                                        selectedAsset.current_value
                                            ? selectedAsset.current_value.toLocaleString()
                                            : "0"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Book Value:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >${{
                                        selectedAsset.book_value
                                            ? selectedAsset.book_value.toLocaleString()
                                            : "0"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Purchase Date:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedAsset.purchase_date
                                            ? formatDate(
                                                  selectedAsset.purchase_date
                                              )
                                            : "N/A"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category & Location -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4
                            class="font-medium text-gray-900 dark:text-white mb-2"
                        >
                            Category
                        </h4>
                        <div
                            v-if="selectedAsset.category"
                            class="flex items-center"
                        >
                            <div
                                class="w-4 h-4 rounded-full mr-2"
                                :style="{
                                    backgroundColor:
                                        selectedAsset.category.color,
                                }"
                            ></div>
                            <span
                                class="text-sm text-gray-900 dark:text-white"
                                >{{ selectedAsset.category.name }}</span
                            >
                        </div>
                        <span
                            v-else
                            class="text-sm text-gray-500 dark:text-gray-400"
                            >No category assigned</span
                        >
                    </div>

                    <div>
                        <h4
                            class="font-medium text-gray-900 dark:text-white mb-2"
                        >
                            Location
                        </h4>
                        <div v-if="selectedAsset.location">
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ selectedAsset.location.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ selectedAsset.location.full_address }}
                            </p>
                        </div>
                        <span
                            v-else
                            class="text-sm text-gray-500 dark:text-gray-400"
                            >No location assigned</span
                        >
                    </div>
                </div>

                <!-- Warranty & Description -->
                <div class="space-y-4">
                    <div v-if="selectedAsset.warranty_expiry">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Warranty
                        </h4>
                        <p class="text-sm text-gray-900 dark:text-white">
                            Expires:
                            {{ formatDate(selectedAsset.warranty_expiry) }}
                            <span
                                :class="[
                                    'ml-2',
                                    selectedAsset.is_warranty_valid
                                        ? 'text-red-600 dark:text-red-400'
                                        : 'text-red-600 dark:text-red-400',
                                ]"
                            >
                                ({{
                                    selectedAsset.is_warranty_valid
                                        ? "Valid"
                                        : "Expired"
                                }})
                            </span>
                        </p>
                    </div>

                    <div v-if="selectedAsset.description">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Description
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedAsset.description }}
                        </p>
                    </div>

                    <div v-if="selectedAsset.notes">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Notes
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedAsset.notes }}
                        </p>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Update Status Modal -->
        <Modal :is-open="showStatusModal" @close="closeModals" size="md">
            <template #title>Update Asset Status</template>

            <div v-if="selectedAsset" class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Update the status for
                    <strong>{{ selectedAsset.name }}</strong>
                </p>

                <!-- Status Select -->
                <FormSelect
                    v-model="newStatus"
                    label="New Status"
                    placeholder="Select new status"
                    :options="statusOptions"
                />

                <div class="flex justify-end gap-3">
                    <Button @click="closeModals" variant="secondary">
                        Cancel
                    </Button>
                    <Button
                        @click="confirmStatusUpdate"
                        :loading="updatingStatus"
                    >
                        Update Status
                    </Button>
                </div>
            </div>
        </Modal>

        <!-- Bulk Status Update Modal -->
        <Modal :is-open="showBulkStatusModal" @close="closeModals" size="md">
            <template #title>Bulk Update Asset Status</template>

            <div v-if="selectedAssets.length > 0" class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Update the status for
                    <strong
                        >{{ selectedAssets.length }} asset{{
                            selectedAssets.length === 1 ? "" : "s"
                        }}</strong
                    >
                </p>

                <!-- Bulk Status Select -->
                <FormSelect
                    v-model="bulkNewStatus"
                    label="New Status"
                    placeholder="Select new status"
                    :options="statusOptions"
                />

                <div class="flex justify-end gap-3">
                    <Button @click="closeModals" variant="secondary">
                        Cancel
                    </Button>
                    <Button
                        @click="confirmBulkStatusUpdate"
                        :loading="bulkUpdatingStatus"
                    >
                        Update Status
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
import { ref, computed, onMounted, watch } from "vue";
import DataTable from "../../components/UI/DataTable.vue";
import Modal from "../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../components/Forms/FormInput.vue";
import FormSelect from "../../components/Forms/FormSelect.vue";
import FormTextarea from "../../components/Forms/FormTextarea.vue";
import Button from "../../components/Base/Button.vue";
import { useNotificationStore } from "@/stores/notification";
import { useConfirmationModalStore } from "@/stores/confirmationModal";
import { useAuthStore } from "@/stores/auth";
import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";
import {
    ArchiveBoxIcon,
    CheckCircleIcon,
    WrenchScrewdriverIcon,
    ExclamationTriangleIcon,
    CurrencyDollarIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    PencilIcon,
    ArrowPathIcon,
    TrashIcon,
    PhotoIcon,
    PrinterIcon,
} from "@heroicons/vue/24/outline";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();
const authStore = useAuthStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const assets = ref({});
const statistics = ref({});
const selectedAssets = ref([]);
const categoryOptions = ref([]);
const locationOptions = ref([]);
const parentAssetOptions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showStatusModal = ref(false);
const showBulkStatusModal = ref(false);
const selectedAsset = ref(null);
const saving = ref(false);
const updatingStatus = ref(false);
const bulkUpdatingStatus = ref(false);
const currentImage = ref(null);
const newStatus = ref("");
const bulkNewStatus = ref("");

// Filters
const filters = ref({
    search: "",
    category_id: "",
    location_id: "",
    status: "",
    condition: "",
});

// Form data
const assetForm = ref({
    name: "",
    code: "",
    description: "",
    serial_number: "",
    model_number: "",
    manufacturer: "",
    purchase_date: "",
    purchase_price: "",
    current_value: "",
    quantity: 1,
    condition: "good",
    status: "active",
    image: null,
    specifications: {},
    warranty_expiry: "",
    notes: "",
    category_id: "",
    location_id: "",
    parent_asset_id: "",
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Options
const statusOptions = [
    { value: "active", label: "Active", color: "bg-red-500" },
    { value: "inactive", label: "Inactive", color: "bg-gray-500" },
    {
        value: "maintenance",
        label: "Under Maintenance",
        color: "bg-orange-500",
    },
    { value: "retired", label: "Retired", color: "bg-red-500" },
    { value: "lost", label: "Lost", color: "bg-red-500" },
];

const conditionOptions = [
    { value: "excellent", label: "Excellent", color: "bg-red-500" },
    { value: "good", label: "Good", color: "bg-red-500" },
    { value: "fair", label: "Fair", color: "bg-yellow-500" },
    { value: "poor", label: "Poor", color: "bg-orange-500" },
    { value: "damaged", label: "Damaged", color: "bg-red-500" },
];

// Computed properties
const totalAssets = computed(() => statistics.value.total_assets || 0);
const activeAssets = computed(() => statistics.value.active_assets || 0);
const maintenanceAssets = computed(
    () => statistics.value.maintenance_assets || 0
);
const poorConditionAssets = computed(() => {
    const conditionStats = statistics.value.assets_by_condition || {};
    return (conditionStats.poor || 0) + (conditionStats.damaged || 0);
});
const totalValue = computed(() => statistics.value.total_current_value || 0);

const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some(
        (value) => value !== "" && value !== null
    );
});

// Table columns configuration
const columns = [
    {
        key: "image",
        label: "Image",
        sortable: false,
        width: "80px",
    },
    {
        key: "name",
        label: "Asset",
        sortable: true,
    },
    {
        key: "category",
        label: "Category",
        sortable: false,
    },
    {
        key: "location",
        label: "Location",
        sortable: false,
    },
    {
        key: "value",
        label: "Value",
        sortable: false,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
    },
    {
        key: "condition",
        label: "Condition",
        sortable: true,
    },
    {
        key: "warranty",
        label: "Warranty",
        sortable: false,
    },
    {
        key: "created_at",
        label: "Created",
        type: "date",
        sortable: true,
    },
];

// Methods
let searchTimeout = null;
const debouncedSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        loadAssets();
    }, 500);
};

const handleFilterChange = () => {
    loadAssets();
};

const clearFilters = () => {
    filters.value = {
        search: "",
        category_id: "",
        location_id: "",
        status: "",
        condition: "",
    };
    loadAssets();
};

const handleRefreshAssets = async () => {
    refreshLoading.value = true;
    try {
        await Promise.all([loadAssets(), loadStatistics()]);
        notification.success("Assets data refreshed successfully");
    } catch (error) {
        console.error("Refresh assets error:", error);
        notification.error("Failed to refresh assets data");
    } finally {
        refreshLoading.value = false;
    }
};

const goToPage = (page) => {
    loadAssets(page);
};

const getStatusColor = (status) => {
    const colors = {
        active: "bg-red-500",
        inactive: "bg-gray-500",
        maintenance: "bg-orange-500",
        retired: "bg-red-500",
        lost: "bg-red-500",
    };
    return colors[status] || "bg-gray-500";
};

const getConditionColor = (condition) => {
    const colors = {
        excellent: "bg-red-500",
        good: "bg-red-500",
        fair: "bg-yellow-500",
        poor: "bg-orange-500",
        damaged: "bg-red-500",
    };
    return colors[condition] || "bg-gray-500";
};

const getCategoryColor = (categoryId) => {
    const category = categoryOptions.value.find(
        (cat) => cat.value === categoryId
    );
    if (category && category.color) {
        return category.color;
    }
    return "#6366F1"; // Default color
};

const getConditionBadgeClass = (condition) => {
    const classes = {
        excellent:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        good: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        fair: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        poor: "bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400",
        damaged: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return (
        classes[condition] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const getStatusBadgeClass = (status) => {
    const classes = {
        active: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        inactive:
            "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        maintenance:
            "bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400",
        retired:
            "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        lost: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return (
        classes[status] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400"
    );
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        assetForm.value.image = file;
    }
};

const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "name":
            if (!value || value.trim() === "") {
                errors.push("Asset name is required");
            } else if (value.length < 2) {
                errors.push("Asset name must be at least 2 characters");
            } else if (value.length > 255) {
                errors.push("Asset name must be less than 255 characters");
            }
            break;
        case "code":
            if (value && value.length > 20) {
                errors.push("Asset code must be less than 20 characters");
            }
            break;
        case "description":
            if (value && value.length > 1000) {
                errors.push("Description must be less than 1000 characters");
            }
            break;
        case "quantity":
            if (!value || value < 1) {
                errors.push("Quantity must be at least 1");
            }
            break;
        case "purchase_price":
        case "current_value":
            if (value !== null && value !== "" && value < 0) {
                errors.push("Value cannot be negative");
            }
            break;
        case "category_id":
            if (!value) {
                errors.push("Category is required");
            }
            break;
        case "location_id":
            if (!value) {
                errors.push("Location is required");
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(assetForm.value).forEach((field) => {
        const fieldErrors = validateField(field, assetForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, assetForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

const saveAsset = async () => {
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const formData = new FormData();

        // Add all form fields to FormData
        Object.keys(assetForm.value).forEach((key) => {
            if (assetForm.value[key] !== null && assetForm.value[key] !== "") {
                if (
                    key === "specifications" &&
                    typeof assetForm.value[key] === "object"
                ) {
                    formData.append(key, JSON.stringify(assetForm.value[key]));
                } else {
                    formData.append(key, assetForm.value[key]);
                }
            }
        });

        const url = showCreateModal.value
            ? "assets"
            : `assets/${selectedAsset.value.id}`;

        const response = showCreateModal.value
            ? await apiPost(url, formData, {
                  headers: { "Content-Type": "multipart/form-data" },
              })
            : await apiPut(url, formData, {
                  headers: { "Content-Type": "multipart/form-data" },
              });

        if (response.success) {
            notification.success(
                `Asset ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            await Promise.all([loadAssets(), loadStatistics()]);
        } else {
            if (response.errors) {
                formErrors.value = response.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(response.message || "Failed to save asset");
            }
        }
    } catch (error) {
        console.error("Save asset error:", error);
        notification.error("Failed to save asset. Please try again.");
    } finally {
        saving.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    showStatusModal.value = false;
    showBulkStatusModal.value = false;
    selectedAsset.value = null;
    currentImage.value = null;
    newStatus.value = "";
    bulkNewStatus.value = "";
    assetForm.value = {
        name: "",
        code: "",
        description: "",
        serial_number: "",
        model_number: "",
        manufacturer: "",
        purchase_date: "",
        purchase_price: "",
        current_value: "",
        quantity: 1,
        condition: "good",
        status: "active",
        image: null,
        specifications: {},
        warranty_expiry: "",
        notes: "",
        category_id: "",
        location_id: "",
        parent_asset_id: "",
    };
    formErrors.value = {};
    formTouched.value = {};
};

const handleAddAsset = () => {
    showCreateModal.value = true;
    loadOptions();
};

const handleEditAsset = (asset) => {
    selectedAsset.value = asset;
    currentImage.value = asset.image_url;
    assetForm.value = {
        name: asset.name,
        code: asset.code,
        description: asset.description || "",
        serial_number: asset.serial_number || "",
        model_number: asset.model_number || "",
        manufacturer: asset.manufacturer || "",
        purchase_date: asset.purchase_date || "",
        purchase_price: asset.purchase_price || "",
        current_value: asset.current_value || "",
        quantity: asset.quantity,
        condition: asset.condition,
        status: asset.status,
        image: null,
        specifications: asset.specifications || {},
        warranty_expiry: asset.warranty_expiry || "",
        notes: asset.notes || "",
        category_id: asset.category_id,
        location_id: asset.location_id,
        parent_asset_id: asset.parent_asset_id || "",
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
    loadOptions(asset);
};

const handleViewAsset = (asset) => {
    selectedAsset.value = asset;
    showViewModal.value = true;
};

const handleUpdateStatus = (asset) => {
    selectedAsset.value = asset;
    newStatus.value = asset.status;
    showStatusModal.value = true;
};

const confirmStatusUpdate = async () => {
    if (!selectedAsset.value || !newStatus.value) return;

    updatingStatus.value = true;
    try {
        const response = await apiPost(
            `assets/${selectedAsset.value.id}/update-status`,
            {
                status: newStatus.value,
            }
        );

        if (response.success) {
            notification.success("Asset status updated successfully");
            closeModals();
            await Promise.all([loadAssets(), loadStatistics()]);
        } else {
            notification.error(
                response.message || "Failed to update asset status"
            );
        }
    } catch (error) {
        console.error("Update status error:", error);
        notification.error("Failed to update asset status");
    } finally {
        updatingStatus.value = false;
    }
};

const confirmBulkStatusUpdate = async () => {
    if (!selectedAssets.value.length || !bulkNewStatus.value) return;

    bulkUpdatingStatus.value = true;
    try {
        const response = await apiPost("assets/bulk-update-status", {
            asset_ids: selectedAssets.value.map((asset) => asset.id),
            status: bulkNewStatus.value,
        });

        if (response.success) {
            notification.success(
                `${selectedAssets.value.length} asset${
                    selectedAssets.value.length === 1 ? "" : "s"
                } status updated successfully`
            );
            closeModals();
            await Promise.all([loadAssets(), loadStatistics()]);
        } else {
            notification.error(
                response.message || "Failed to update asset statuses"
            );
        }
    } catch (error) {
        console.error("Bulk update status error:", error);
        notification.error("Failed to update asset statuses");
    } finally {
        bulkUpdatingStatus.value = false;
    }
};

const handleDeleteAsset = (asset) => {
    confirmationModal.showModal({
        title: "Delete Asset",
        message: `Are you sure you want to delete "${asset.name}"?`,
        description:
            "This action cannot be undone. This will permanently delete the asset and all its associated data.",
        confirmText: "Delete Asset",
        cancelText: "Cancel",
        onConfirm: async () => {
            const response = await apiDelete(`assets/${asset.id}`);
            if (response.success) {
                notification.success("Asset deleted successfully");
                await Promise.all([loadAssets(), loadStatistics()]);
                return response;
            } else {
                throw new Error(response.message || "Failed to delete asset");
            }
        },
        onSuccess: (result) => {
            console.log("Asset deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete asset:", error);
            notification.error(error.message || "Failed to delete asset");
        },
    });
};

const handleBulkAction = (action, selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select assets to perform bulk action");
        return;
    }

    if (action === "delete") {
        confirmationModal.showModal({
            title: "Bulk Delete Assets",
            message: `Are you sure you want to delete ${
                selectedItems.length
            } asset${selectedItems.length === 1 ? "" : "s"}?`,
            description:
                "This action cannot be undone. This will permanently delete the selected assets and all their associated data.",
            confirmText: "Delete Assets",
            cancelText: "Cancel",
            onConfirm: async () => {
                try {
                    const deletePromises = selectedItems.map((item) =>
                        apiDelete(`assets/${item.id}`)
                    );
                    await Promise.all(deletePromises);
                    notification.success(
                        `${selectedItems.length} asset${
                            selectedItems.length === 1 ? "" : "s"
                        } deleted successfully`
                    );
                    await Promise.all([loadAssets(), loadStatistics()]);
                    return { success: true };
                } catch (error) {
                    throw new Error("Failed to delete some assets");
                }
            },
            onSuccess: (result) => {
                console.log("Bulk delete completed:", result);
            },
            onError: (error) => {
                console.error("Bulk delete failed:", error);
                notification.error(error.message || "Failed to delete assets");
            },
        });
    } else if (action === "update-status") {
        selectedAssets.value = selectedItems;
        showBulkStatusModal.value = true;
    }
};

const handleSelectionChange = (selectedItems) => {
    selectedAssets.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const clearSelection = () => {
    selectedAssets.value = [];
};

const handleExportAssets = async () => {
    try {
        // Build query parameters from current filters
        const params = new URLSearchParams(filters.value);

        // Create a temporary link to download the file
        const link = document.createElement("a");
        link.href = `/api/assets-export?${params}`;
        link.download = `assets_${new Date().toISOString().split("T")[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        notification.success(
            "Assets export started. Download will begin shortly."
        );
    } catch (error) {
        console.error("Export assets error:", error);
        notification.error("Failed to export assets");
    }
};

const loadAssets = async (page = 1) => {
    try {
        loading.value = true;
        const params = new URLSearchParams({
            page: page.toString(),
            ...filters.value,
        });

        const response = await apiGet(`assets?${params}`);
        if (response.success) {
            assets.value = response.data;
        }
    } catch (error) {
        console.error("Error loading assets:", error);
        notification.error("Failed to load assets");
    } finally {
        loading.value = false;
    }
};

const loadStatistics = async () => {
    try {
        const response = await apiGet("assets-statistics");
        if (response.success) {
            statistics.value = response.data;
        }
    } catch (error) {
        console.error("Error loading statistics:", error);
        notification.error("Failed to load statistics");
    }
};

const loadOptions = async (excludeAsset = null) => {
    try {
        // Load categories
        const categoriesResponse = await apiGet("asset-categories");
        if (categoriesResponse.success) {
            categoryOptions.value = categoriesResponse.data.map((category) => ({
                value: category.id,
                label: category.full_path,
                color: category.color,
            }));
        }

        // Load locations
        const locationsResponse = await apiGet("locations");
        if (locationsResponse.success) {
            locationOptions.value = locationsResponse.data.map((location) => ({
                value: location.id,
                label: location.full_path,
            }));
        }

        // Load parent assets
        const parentAssetsUrl = excludeAsset
            ? `assets-parent-options/${excludeAsset.id}`
            : "assets-parent-options";
        const parentAssetsResponse = await apiGet(parentAssetsUrl);
        if (parentAssetsResponse.success) {
            parentAssetOptions.value = [
                { value: "", label: "No Parent Asset" },
                ...parentAssetsResponse.data.map((asset) => ({
                    value: asset.id,
                    label: asset.full_path,
                })),
            ];
        }
    } catch (error) {
        console.error("Error loading options:", error);
        notification.error("Failed to load form options");
    }
};

// Watch for filter changes
watch(
    filters,
    () => {
        loadAssets();
    },
    { deep: true }
);

const handlePrintBarcode = async (asset) => {
    try {
        const response = await fetch(`/api/assets/${asset.id}/barcode`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/pdf',
            },
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `barcode_${asset.code || asset.id}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            notification.success(`Barcode for ${asset.name} downloaded successfully`);
        } else {
            throw new Error('Failed to generate barcode');
        }
    } catch (error) {
        console.error('Print barcode error:', error);
        notification.error('Failed to print barcode. Please try again.');
    }
};

const handleBulkPrintBarcode = async (selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select assets to print barcodes");
        return;
    }

    try {
        const assetIds = selectedItems.map(asset => asset.id);

        const response = await fetch('/api/assets/bulk-barcode', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/pdf',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ asset_ids: assetIds }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `bulk_barcodes_${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            notification.success(`Bulk barcodes for ${selectedItems.length} assets downloaded successfully`);
        } else {
            throw new Error('Failed to generate bulk barcodes');
        }
    } catch (error) {
        console.error('Bulk print barcode error:', error);
        notification.error('Failed to print bulk barcodes. Please try again.');
    }
};

const handlePrintQRCode = async (asset, size = 'medium') => {
    try {
        const response = await fetch(`/api/assets/${asset.id}/qrcode-pdf?size=${size}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/pdf',
            },
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `qrcode_${asset.code || asset.id}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            notification.success(`QR Code for ${asset.name} downloaded successfully`);
        } else {
            throw new Error('Failed to generate QR code');
        }
    } catch (error) {
        console.error('Print QR code error:', error);
        notification.error('Failed to print QR code. Please try again.');
    }
};

const handleBulkPrintQRCode = async (selectedItems, size = 'medium') => {
    if (selectedItems.length === 0) {
        notification.warning("Please select assets to export QR codes");
        return;
    }

    try {
        // Pastikan selectedItems adalah array of objects dengan property id
        const assetIds = Array.isArray(selectedItems)
            ? selectedItems.map(asset => typeof asset === 'object' ? asset.id : asset)
            : [];

        if (assetIds.length === 0) {
            notification.warning("No valid assets selected");
            return;
        }

        const response = await fetch('/api/assets/bulk-qrcode-pdf', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/pdf',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                asset_ids: assetIds,
                size: size
            }),
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `bulk_qrcodes_${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            notification.success(`Bulk QR codes for ${selectedItems.length} assets downloaded successfully`);
        } else {
            const errorData = await response.json();
            console.error('Server error:', errorData);
            throw new Error(errorData.message || 'Failed to generate bulk QR codes');
        }
    } catch (error) {
        console.error('Bulk print QR code error:', error);
        notification.error(error.message || 'Failed to export bulk QR codes. Please try again.');
    }
};

// Lifecycle
onMounted(() => {
    loadAssets();
    loadStatistics();
    loadOptions();
});
</script>
