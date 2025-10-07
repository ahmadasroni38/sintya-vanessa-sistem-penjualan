<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Work Orders
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage and track all work orders and maintenance tasks
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
                        <ClipboardDocumentListIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Work Orders
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalWorkOrders }}
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
                        <ClockIcon
                            class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pending
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ pendingWorkOrders }}
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
                        <PlayIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            In Progress
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ inProgressWorkOrders }}
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
                            Completed
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ completedWorkOrders }}
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
                            Overdue
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ overdueWorkOrders }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
        >
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <FormInput
                    v-model="filters.search"
                    placeholder="Search work orders..."
                    @input="debouncedSearch"
                >
                    <template #prefix>
                        <MagnifyingGlassIcon class="w-4 h-4" />
                    </template>
                </FormInput>

                <!-- Status Filter -->
                <FormSelect
                    v-model="filters.status"
                    placeholder="All Statuses"
                    :options="statusOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Priority Filter -->
                <FormSelect
                    v-model="filters.priority"
                    placeholder="All Priorities"
                    :options="priorityOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Type Filter -->
                <FormSelect
                    v-model="filters.type"
                    placeholder="All Types"
                    :options="typeOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Asset Filter -->
                <FormSelect
                    v-model="filters.asset_id"
                    placeholder="All Assets"
                    :options="assetOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Location Filter -->
                <FormSelect
                    v-model="filters.location_id"
                    placeholder="All Locations"
                    :options="locationOptions"
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
            v-if="selectedWorkOrders.length > 0"
            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span
                        class="text-sm font-medium text-red-900 dark:text-red-100"
                    >
                        {{ selectedWorkOrders.length }} work order{{
                            selectedWorkOrders.length === 1 ? "" : "s"
                        }}
                        selected
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        variant="secondary"
                        size="sm"
                        @click="
                            handleBulkAction('update-status', selectedWorkOrders)
                        "
                    >
                        <ArrowPathIcon class="w-4 h-4 mr-2" />
                        Update Status
                    </Button>
                    <Button
                        variant="danger"
                        size="sm"
                        @click="handleBulkAction('delete', selectedWorkOrders)"
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
            title="Work Orders"
            description="A comprehensive list of all work orders and maintenance tasks."
            :data="workOrders.data || []"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="New Work Order"
            :show-filters="false"
            :show-bulk-actions="false"
            :show-export="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search work orders..."
            empty-title="No work orders found"
            empty-description="Get started by creating your first work order."
            @add="handleAddWorkOrder"
            @edit="handleEditWorkOrder"
            @delete="handleDeleteWorkOrder"
            @bulk-action="handleBulkAction"
            @export="handleExportWorkOrders"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshWorkOrders"
        >
            <!-- Custom Code Column -->
            <template #column-code="{ item }">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ item.code }}
                    </span>
                    <span
                        v-if="item.is_overdue"
                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400"
                    >
                        Overdue
                    </span>
                    <span
                        v-else-if="item.is_due_soon"
                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400"
                    >
                        Due Soon
                    </span>
                </div>
            </template>

            <!-- Custom Title Column -->
            <template #column-title="{ item }">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ item.title }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ item.type }}
                    </p>
                </div>
            </template>

            <!-- Custom Asset Column -->
            <template #column-asset="{ item }">
                <div v-if="item.asset">
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ item.asset.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ item.asset.code }}
                    </p>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    No Asset
                </span>
            </template>

            <!-- Custom Priority Column -->
            <template #column-priority="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getPriorityBadgeClass(item.priority),
                    ]"
                >
                    {{ item.priority }}
                </span>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        getStatusBadgeClass(item.status),
                    ]"
                >
                    {{ item.status.replace('_', ' ') }}
                </span>
            </template>

            <!-- Custom Assigned User Column -->
            <template #column-assigned_user="{ item }">
                <div v-if="item.assigned_user">
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ item.assigned_user.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ item.assigned_user.email }}
                    </p>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    Unassigned
                </span>
            </template>

            <!-- Custom Due Date Column -->
            <template #column-due_date="{ item }">
                <div v-if="item.due_date">
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ formatDate(item.due_date) }}
                    </p>
                    <p
                        v-if="item.days_until_due !== null"
                        :class="[
                            'text-xs',
                            item.days_until_due < 0
                                ? 'text-red-600 dark:text-red-400'
                                : item.days_until_due <= 7
                                ? 'text-yellow-600 dark:text-yellow-400'
                                : 'text-gray-500 dark:text-gray-400',
                        ]"
                    >
                        {{
                            item.days_until_due < 0
                                ? `${Math.abs(item.days_until_due)} days overdue`
                                : item.days_until_due === 0
                                ? 'Due today'
                                : `${item.days_until_due} days left`
                        }}
                    </p>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    No Due Date
                </span>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleViewWorkOrder(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-if="!item.is_completed"
                        @click="handleStartWorkOrder(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Start Work Order"
                    >
                        <PlayIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleEditWorkOrder(item)"
                        class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 dark:hover:text-yellow-400 dark:hover:bg-yellow-900/20"
                        title="Edit Work Order"
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
                        @click="handleDeleteWorkOrder(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Work Order"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Pagination -->
        <div v-if="workOrders.last_page > 1" class="flex justify-center">
            <div class="flex items-center gap-2">
                <Button
                    variant="secondary"
                    :disabled="workOrders.current_page === 1"
                    @click="goToPage(workOrders.current_page - 1)"
                >
                    Previous
                </Button>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Page {{ workOrders.current_page }} of {{ workOrders.last_page }}
                </span>
                <Button
                    variant="secondary"
                    :disabled="workOrders.current_page === workOrders.last_page"
                    @click="goToPage(workOrders.current_page + 1)"
                >
                    Next
                </Button>
            </div>
        </div>

        <!-- Create/Edit Work Order Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                {{ showCreateModal ? "Create Work Order" : "Edit Work Order" }}
            </template>

            <form @submit.prevent="saveWorkOrder" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="workOrderForm.title"
                            label="Work Order Title"
                            placeholder="Enter work order title"
                            :error="getFieldError('title')"
                            required
                            @blur="handleFieldBlur('title')"
                        />
                        <FormInput
                            v-model="workOrderForm.code"
                            label="Work Order Code"
                            placeholder="Auto-generated if empty"
                            hint="Leave empty to auto-generate"
                            :error="getFieldError('code')"
                            @blur="handleFieldBlur('code')"
                        />
                    </div>
                    <FormTextarea
                        v-model="workOrderForm.description"
                        label="Description"
                        placeholder="Enter work order description"
                        :error="getFieldError('description')"
                        rows="3"
                        @blur="handleFieldBlur('description')"
                    />
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Type Select -->
                        <FormSelect
                            v-model="workOrderForm.type"
                            label="Type"
                            placeholder="Select type"
                            :options="typeOptions"
                            :error="getFieldError('type')"
                            required
                            @blur="handleFieldBlur('type')"
                            @update:modelValue="handleFieldBlur('type')"
                        />

                        <!-- Priority Select -->
                        <FormSelect
                            v-model="workOrderForm.priority"
                            label="Priority"
                            placeholder="Select priority"
                            :options="priorityOptions"
                            :error="getFieldError('priority')"
                            required
                            @blur="handleFieldBlur('priority')"
                            @update:modelValue="handleFieldBlur('priority')"
                        />

                        <!-- Status Select -->
                        <FormSelect
                            v-model="workOrderForm.status"
                            label="Status"
                            placeholder="Select status"
                            :options="statusOptions"
                            :error="getFieldError('status')"
                            @blur="handleFieldBlur('status')"
                            @update:modelValue="handleFieldBlur('status')"
                        />
                    </div>
                </div>

                <!-- Assignment & Location -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Assignment & Location
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Asset Select -->
                        <FormSelect
                            v-model="workOrderForm.asset_id"
                            label="Asset"
                            placeholder="Select asset (optional)"
                            :options="assetOptions"
                            :error="getFieldError('asset_id')"
                            @blur="handleFieldBlur('asset_id')"
                            @update:modelValue="handleFieldBlur('asset_id')"
                        />

                        <!-- Location Select -->
                        <FormSelect
                            v-model="workOrderForm.location_id"
                            label="Location"
                            placeholder="Select location (optional)"
                            :options="locationOptions"
                            :error="getFieldError('location_id')"
                            @blur="handleFieldBlur('location_id')"
                            @update:modelValue="handleFieldBlur('location_id')"
                        />

                        <!-- Assigned User Select -->
                        <FormSelect
                            v-model="workOrderForm.assigned_to"
                            label="Assign To"
                            placeholder="Select user (optional)"
                            :options="userOptions"
                            :error="getFieldError('assigned_to')"
                            @blur="handleFieldBlur('assigned_to')"
                            @update:modelValue="handleFieldBlur('assigned_to')"
                        />
                    </div>
                </div>

                <!-- Schedule & Estimates -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Schedule & Estimates
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <FormInput
                            v-model="workOrderForm.due_date"
                            label="Due Date"
                            type="datetime-local"
                            :error="getFieldError('due_date')"
                            @blur="handleFieldBlur('due_date')"
                        />
                        <FormInput
                            v-model="workOrderForm.estimated_hours"
                            label="Estimated Hours"
                            type="number"
                            step="0.5"
                            min="0"
                            placeholder="Enter estimated hours"
                            :error="getFieldError('estimated_hours')"
                            @blur="handleFieldBlur('estimated_hours')"
                        />
                        <FormInput
                            v-model="workOrderForm.estimated_cost"
                            label="Estimated Cost"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter estimated cost"
                            :error="getFieldError('estimated_cost')"
                            @blur="handleFieldBlur('estimated_cost')"
                        />
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Additional Information
                    </h3>
                    <FormTextarea
                        v-model="workOrderForm.notes"
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
                        {{ showCreateModal ? "Create Work Order" : "Update Work Order" }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View Work Order Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="xl">
            <template #title>Work Order Details</template>

            <div v-if="selectedWorkOrder" class="space-y-6">
                <!-- Work Order Header -->
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3
                            class="text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ selectedWorkOrder.title }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ selectedWorkOrder.code }}
                        </p>
                        <div class="flex items-center gap-4 mt-2">
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getPriorityBadgeClass(selectedWorkOrder.priority),
                                ]"
                            >
                                {{ selectedWorkOrder.priority }} Priority
                            </span>
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getStatusBadgeClass(selectedWorkOrder.status),
                                ]"
                            >
                                {{ selectedWorkOrder.status.replace("_", " ") }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ selectedWorkOrder.type }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button
                            v-if="!selectedWorkOrder.is_completed"
                            @click="handleStartWorkOrder(selectedWorkOrder)"
                            variant="secondary"
                            size="sm"
                        >
                            <PlayIcon class="w-4 h-4 mr-2" />
                            Start Work
                        </Button>
                        <Button
                            @click="handleEditWorkOrder(selectedWorkOrder)"
                            variant="primary"
                            size="sm"
                        >
                            <PencilIcon class="w-4 h-4 mr-2" />
                            Edit
                        </Button>
                    </div>
                </div>

                <!-- Work Order Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Assignment Information
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Requester:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.requester ? selectedWorkOrder.requester.name : "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Assigned To:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.assigned_user ? selectedWorkOrder.assigned_user.name : "Unassigned"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Asset:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.asset ? selectedWorkOrder.asset.name : "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Location:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.location ? selectedWorkOrder.location.name : "N/A"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Schedule Information
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Requested Date:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.requested_date
                                            ? formatDate(selectedWorkOrder.requested_date)
                                            : "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Due Date:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.due_date
                                            ? formatDate(selectedWorkOrder.due_date)
                                            : "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Started At:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.started_at
                                            ? formatDate(selectedWorkOrder.started_at)
                                            : "Not Started"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Completed At:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedWorkOrder.completed_at
                                            ? formatDate(selectedWorkOrder.completed_at)
                                            : "Not Completed"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description & Notes -->
                <div class="space-y-4">
                    <div v-if="selectedWorkOrder.description">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Description
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedWorkOrder.description }}
                        </p>
                    </div>

                    <div v-if="selectedWorkOrder.notes">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Notes
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedWorkOrder.notes }}
                        </p>
                    </div>

                    <div v-if="selectedWorkOrder.completion_notes">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Completion Notes
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedWorkOrder.completion_notes }}
                        </p>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Update Status Modal -->
        <Modal :is-open="showStatusModal" @close="closeModals" size="md">
            <template #title>Update Work Order Status</template>

            <div v-if="selectedWorkOrder" class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Update the status for
                    <strong>{{ selectedWorkOrder.title }}</strong>
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
            <template #title>Bulk Update Work Order Status</template>

            <div v-if="selectedWorkOrders.length > 0" class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Update the status for
                    <strong
                        >{{ selectedWorkOrders.length }} work order{{
                            selectedWorkOrders.length === 1 ? "" : "s"
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
import { useRoute } from "vue-router";
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
    ClipboardDocumentListIcon,
    ClockIcon,
    PlayIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    PencilIcon,
    ArrowPathIcon,
    TrashIcon,
} from "@heroicons/vue/24/outline";

const route = useRoute();
const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();
const authStore = useAuthStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const workOrders = ref({});
const statistics = ref({});
const selectedWorkOrders = ref([]);
const userOptions = ref([]);
const assetOptions = ref([]);
const locationOptions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showStatusModal = ref(false);
const showBulkStatusModal = ref(false);
const selectedWorkOrder = ref(null);
const saving = ref(false);
const updatingStatus = ref(false);
const bulkUpdatingStatus = ref(false);
const newStatus = ref("");
const bulkNewStatus = ref("");

// Filters
const filters = ref({
    search: "",
    status: "",
    priority: "",
    type: "",
    asset_id: "",
    location_id: "",
});

// Form data
const workOrderForm = ref({
    title: "",
    code: "",
    description: "",
    type: "",
    priority: "",
    status: "pending",
    asset_id: "",
    location_id: "",
    assigned_to: "",
    due_date: "",
    estimated_hours: "",
    estimated_cost: "",
    notes: "",
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Options
const statusOptions = [
    { value: "draft", label: "Draft" },
    { value: "pending", label: "Pending" },
    { value: "assigned", label: "Assigned" },
    { value: "in_progress", label: "In Progress" },
    { value: "on_hold", label: "On Hold" },
    { value: "completed", label: "Completed" },
    { value: "cancelled", label: "Cancelled" },
];

const priorityOptions = [
    { value: "low", label: "Low" },
    { value: "medium", label: "Medium" },
    { value: "high", label: "High" },
    { value: "critical", label: "Critical" },
];

const typeOptions = [
    { value: "maintenance", label: "Maintenance" },
    { value: "repair", label: "Repair" },
    { value: "installation", label: "Installation" },
    { value: "inspection", label: "Inspection" },
    { value: "upgrade", label: "Upgrade" },
    { value: "replacement", label: "Replacement" },
];

// Computed properties
const totalWorkOrders = computed(() => statistics.value.total_work_orders || 0);
const pendingWorkOrders = computed(() => statistics.value.pending_work_orders || 0);
const inProgressWorkOrders = computed(() => statistics.value.in_progress_work_orders || 0);
const completedWorkOrders = computed(() => statistics.value.completed_work_orders || 0);
const overdueWorkOrders = computed(() => statistics.value.overdue_work_orders || 0);

const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some(
        (value) => value !== "" && value !== null
    );
});

// Table columns configuration
const columns = [
    {
        key: "code",
        label: "Code",
        sortable: true,
    },
    {
        key: "title",
        label: "Title",
        sortable: true,
    },
    {
        key: "asset",
        label: "Asset",
        sortable: false,
    },
    {
        key: "priority",
        label: "Priority",
        sortable: true,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
    },
    {
        key: "assigned_user",
        label: "Assigned To",
        sortable: false,
    },
    {
        key: "due_date",
        label: "Due Date",
        sortable: true,
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
        loadWorkOrders();
    }, 500);
};

const handleFilterChange = () => {
    loadWorkOrders();
};

const clearFilters = () => {
    filters.value = {
        search: "",
        status: "",
        priority: "",
        type: "",
        asset_id: "",
        location_id: "",
    };
    loadWorkOrders();
};

const handleRefreshWorkOrders = async () => {
    refreshLoading.value = true;
    try {
        await Promise.all([loadWorkOrders(), loadStatistics()]);
        notification.success("Work orders data refreshed successfully");
    } catch (error) {
        console.error("Refresh work orders error:", error);
        notification.error("Failed to refresh work orders data");
    } finally {
        refreshLoading.value = false;
    }
};

const goToPage = (page) => {
    loadWorkOrders(page);
};

const getPriorityBadgeClass = (priority) => {
    const classes = {
        low: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        medium: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        high: "bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400",
        critical: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return classes[priority] || classes.medium;
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        pending: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        assigned: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        in_progress: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        on_hold: "bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400",
        completed: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        cancelled: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return classes[status] || classes.pending;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "title":
            if (!value || value.trim() === "") {
                errors.push("Work order title is required");
            } else if (value.length < 3) {
                errors.push("Title must be at least 3 characters");
            } else if (value.length > 255) {
                errors.push("Title must be less than 255 characters");
            }
            break;
        case "type":
            if (!value) {
                errors.push("Work order type is required");
            }
            break;
        case "priority":
            if (!value) {
                errors.push("Priority is required");
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(workOrderForm.value).forEach((field) => {
        const fieldErrors = validateField(field, workOrderForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, workOrderForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

const saveWorkOrder = async () => {
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "work-orders"
            : `work-orders/${selectedWorkOrder.value.id}`;

        const response = showCreateModal.value
            ? await apiPost(url, workOrderForm.value)
            : await apiPut(url, workOrderForm.value);

        if (response.success) {
            notification.success(
                `Work order ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            await Promise.all([loadWorkOrders(), loadStatistics()]);
        } else {
            if (response.errors) {
                formErrors.value = response.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(response.message || "Failed to save work order");
            }
        }
    } catch (error) {
        console.error("Save work order error:", error);
        notification.error("Failed to save work order. Please try again.");
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
    selectedWorkOrder.value = null;
    newStatus.value = "";
    bulkNewStatus.value = "";
    workOrderForm.value = {
        title: "",
        code: "",
        description: "",
        type: "",
        priority: "",
        status: "pending",
        asset_id: "",
        location_id: "",
        assigned_to: "",
        due_date: "",
        estimated_hours: "",
        estimated_cost: "",
        notes: "",
    };
    formErrors.value = {};
    formTouched.value = {};
};

const handleAddWorkOrder = () => {
    showCreateModal.value = true;
    loadOptions();
};

const handleEditWorkOrder = (workOrder) => {
    selectedWorkOrder.value = workOrder;
    workOrderForm.value = {
        title: workOrder.title,
        code: workOrder.code,
        description: workOrder.description || "",
        type: workOrder.type,
        priority: workOrder.priority,
        status: workOrder.status,
        asset_id: workOrder.asset_id || "",
        location_id: workOrder.location_id || "",
        assigned_to: workOrder.assigned_to || "",
        due_date: workOrder.due_date ? workOrder.due_date.slice(0, 16) : "",
        estimated_hours: workOrder.estimated_hours || "",
        estimated_cost: workOrder.estimated_cost || "",
        notes: workOrder.notes || "",
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
    loadOptions();
};

const handleViewWorkOrder = (workOrder) => {
    selectedWorkOrder.value = workOrder;
    showViewModal.value = true;
};

const handleStartWorkOrder = async (workOrder) => {
    try {
        const response = await apiPost(`work-orders/${workOrder.id}/start`);
        if (response.success) {
            notification.success("Work order started successfully");
            await Promise.all([loadWorkOrders(), loadStatistics()]);
        } else {
            notification.error(response.message || "Failed to start work order");
        }
    } catch (error) {
        console.error("Start work order error:", error);
        notification.error("Failed to start work order");
    }
};

const handleUpdateStatus = (workOrder) => {
    selectedWorkOrder.value = workOrder;
    newStatus.value = workOrder.status;
    showStatusModal.value = true;
};

const confirmStatusUpdate = async () => {
    if (!selectedWorkOrder.value || !newStatus.value) return;

    updatingStatus.value = true;
    try {
        const response = await apiPost(
            `work-orders/${selectedWorkOrder.value.id}/update-status`,
            {
                status: newStatus.value,
            }
        );

        if (response.success) {
            notification.success("Work order status updated successfully");
            closeModals();
            await Promise.all([loadWorkOrders(), loadStatistics()]);
        } else {
            notification.error(
                response.message || "Failed to update work order status"
            );
        }
    } catch (error) {
        console.error("Update status error:", error);
        notification.error("Failed to update work order status");
    } finally {
        updatingStatus.value = false;
    }
};

const confirmBulkStatusUpdate = async () => {
    if (!selectedWorkOrders.value.length || !bulkNewStatus.value) return;

    bulkUpdatingStatus.value = true;
    try {
        const response = await apiPost("work-orders/bulk-update-status", {
            work_order_ids: selectedWorkOrders.value.map((wo) => wo.id),
            status: bulkNewStatus.value,
        });

        if (response.success) {
            notification.success(
                `${selectedWorkOrders.value.length} work order${
                    selectedWorkOrders.value.length === 1 ? "" : "s"
                } status updated successfully`
            );
            closeModals();
            await Promise.all([loadWorkOrders(), loadStatistics()]);
        } else {
            notification.error(
                response.message || "Failed to update work order statuses"
            );
        }
    } catch (error) {
        console.error("Bulk update status error:", error);
        notification.error("Failed to update work order statuses");
    } finally {
        bulkUpdatingStatus.value = false;
    }
};

const handleDeleteWorkOrder = (workOrder) => {
    confirmationModal.showModal({
        title: "Delete Work Order",
        message: `Are you sure you want to delete "${workOrder.title}"?`,
        description:
            "This action cannot be undone. This will permanently delete the work order and all its associated data.",
        confirmText: "Delete Work Order",
        cancelText: "Cancel",
        onConfirm: async () => {
            const response = await apiDelete(`work-orders/${workOrder.id}`);
            if (response.success) {
                notification.success("Work order deleted successfully");
                await Promise.all([loadWorkOrders(), loadStatistics()]);
                return response;
            } else {
                throw new Error(response.message || "Failed to delete work order");
            }
        },
        onSuccess: (result) => {
            console.log("Work order deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete work order:", error);
            notification.error(error.message || "Failed to delete work order");
        },
    });
};

const handleBulkAction = (action, selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select work orders to perform bulk action");
        return;
    }

    if (action === "delete") {
        confirmationModal.showModal({
            title: "Bulk Delete Work Orders",
            message: `Are you sure you want to delete ${
                selectedItems.length
            } work order${selectedItems.length === 1 ? "" : "s"}?`,
            description:
                "This action cannot be undone. This will permanently delete the selected work orders and all their associated data.",
            confirmText: "Delete Work Orders",
            cancelText: "Cancel",
            onConfirm: async () => {
                try {
                    const deletePromises = selectedItems.map((item) =>
                        apiDelete(`work-orders/${item.id}`)
                    );
                    await Promise.all(deletePromises);
                    notification.success(
                        `${selectedItems.length} work order${
                            selectedItems.length === 1 ? "" : "s"
                        } deleted successfully`
                    );
                    await Promise.all([loadWorkOrders(), loadStatistics()]);
                    return { success: true };
                } catch (error) {
                    throw new Error("Failed to delete some work orders");
                }
            },
            onSuccess: (result) => {
                console.log("Bulk delete completed:", result);
            },
            onError: (error) => {
                console.error("Bulk delete failed:", error);
                notification.error(error.message || "Failed to delete work orders");
            },
        });
    } else if (action === "update-status") {
        selectedWorkOrders.value = selectedItems;
        showBulkStatusModal.value = true;
    }
};

const handleSelectionChange = (selectedItems) => {
    selectedWorkOrders.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const clearSelection = () => {
    selectedWorkOrders.value = [];
};

const handleExportWorkOrders = async () => {
    try {
        // Build query parameters from current filters
        const params = new URLSearchParams(filters.value);

        // Create a temporary link to download the file
        const link = document.createElement("a");
        link.href = `/api/work-orders-export?${params}`;
        link.download = `work_orders_${new Date().toISOString().split("T")[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        notification.success(
            "Work orders export started. Download will begin shortly."
        );
    } catch (error) {
        console.error("Export work orders error:", error);
        notification.error("Failed to export work orders");
    }
};

const loadWorkOrders = async (page = 1) => {
    try {
        loading.value = true;
        const params = new URLSearchParams({
            page: page.toString(),
            ...filters.value,
        });

        const response = await apiGet(`work-orders?${params}`);
        if (response.success) {
            workOrders.value = response.data;
        }
    } catch (error) {
        console.error("Error loading work orders:", error);
        notification.error("Failed to load work orders");
    } finally {
        loading.value = false;
    }
};

const loadStatistics = async () => {
    try {
        const response = await apiGet("work-orders-statistics");
        if (response.success) {
            statistics.value = response.data;
        }
    } catch (error) {
        console.error("Error loading statistics:", error);
        notification.error("Failed to load statistics");
    }
};

const loadOptions = async () => {
    try {
        // Load users for assignment
        const usersResponse = await apiGet("work-orders-user-options");
        if (usersResponse.success) {
            userOptions.value = usersResponse.data;
        }

        // Load assets
        const assetsResponse = await apiGet("work-orders-asset-options");
        if (assetsResponse.success) {
            assetOptions.value = assetsResponse.data;
        }

        // Load locations
        const locationsResponse = await apiGet("work-orders-location-options");
        if (locationsResponse.success) {
            locationOptions.value = locationsResponse.data;
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
        loadWorkOrders();
    },
    { deep: true }
);

// Lifecycle
onMounted(async () => {
    // Check for prefill data from QR code scan
    if (route.query.action === 'create') {
        // Load options first before prefilling
        await loadOptions();

        // Prefill form with data from query params
        if (route.query.asset_id) {
            workOrderForm.value.asset_id = parseInt(route.query.asset_id);
        }
        if (route.query.location_id) {
            workOrderForm.value.location_id = parseInt(route.query.location_id);
        }
        if (route.query.asset_name) {
            // Set title with asset name
            workOrderForm.value.title = `Maintenance for ${route.query.asset_name}`;
        }
        if (route.query.asset_code) {
            // Add asset code to description
            workOrderForm.value.description = `Asset Code: ${route.query.asset_code}\n`;
        }

        // Auto-open create modal
        showCreateModal.value = true;

        notification.info('Form has been pre-filled with asset information from QR code');
    }

    loadWorkOrders();
    loadStatistics();
    if (!route.query.action) {
        loadOptions();
    }
});
</script>