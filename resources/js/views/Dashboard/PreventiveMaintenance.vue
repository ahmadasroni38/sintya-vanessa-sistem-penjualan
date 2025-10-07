<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Preventive Maintenance
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Schedule and track preventive maintenance tasks for your assets
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
                        <CalendarDaysIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Schedules
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalPreventiveMaintenances }}
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
                            Active
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activePreventiveMaintenances }}
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
                            Due Soon
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ dueSoonPreventiveMaintenances }}
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
                            {{ overduePreventiveMaintenances }}
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
                        <ChartBarIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Compliance Rate
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ averageComplianceRate }}%
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
                    placeholder="Search preventive maintenance..."
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
                    v-model="filters.maintenance_type"
                    placeholder="All Types"
                    :options="maintenanceTypeOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Asset Filter -->
                <FormSelect
                    v-model="filters.asset_id"
                    placeholder="All Assets"
                    :options="assetOptions"
                    @update:modelValue="handleFilterChange"
                />

                <!-- Frequency Filter -->
                <FormSelect
                    v-model="filters.frequency_type"
                    placeholder="All Frequencies"
                    :options="frequencyOptions"
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

        <!-- DataTable -->
        <DataTable
            title="Preventive Maintenance Schedules"
            description="A comprehensive list of all preventive maintenance schedules and their execution status."
            :data="preventiveMaintenances.data || []"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="New Schedule"
            :show-filters="false"
            :show-bulk-actions="false"
            :show-export="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search schedules..."
            empty-title="No preventive maintenance schedules found"
            empty-description="Get started by creating your first preventive maintenance schedule."
            @add="handleAddPM"
            @edit="handleEditPM"
            @delete="handleDeletePM"
            @bulk-action="handleBulkAction"
            @export="handleExportPM"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshPM"
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
                        {{ item.maintenance_type }}
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
                    {{ item.status }}
                </span>
            </template>

            <!-- Custom Frequency Column -->
            <template #column-frequency="{ item }">
                <div>
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ formatFrequency(item.frequency_type, item.frequency_value) }}
                    </p>
                </div>
            </template>

            <!-- Custom Next Due Date Column -->
            <template #column-next_due_date="{ item }">
                <div v-if="item.next_due_date">
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ formatDate(item.next_due_date) }}
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

            <!-- Custom Compliance Rate Column -->
            <template #column-compliance_rate="{ item }">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-900 dark:text-white">{{ item.compliance_rate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div
                                :class="[
                                    'h-2 rounded-full',
                                    item.compliance_rate >= 90
                                        ? 'bg-green-600'
                                        : item.compliance_rate >= 70
                                        ? 'bg-yellow-600'
                                        : 'bg-red-600',
                                ]"
                                :style="{ width: `${item.compliance_rate}%` }"
                            ></div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleViewPM(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="View Details"
                    >
                        <EyeIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleEditPM(item)"
                        class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 dark:hover:text-yellow-400 dark:hover:bg-yellow-900/20"
                        title="Edit Schedule"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeletePM(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Schedule"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Pagination -->
        <div v-if="preventiveMaintenances.last_page > 1" class="flex justify-center">
            <div class="flex items-center gap-2">
                <Button
                    variant="secondary"
                    :disabled="preventiveMaintenances.current_page === 1"
                    @click="goToPage(preventiveMaintenances.current_page - 1)"
                >
                    Previous
                </Button>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Page {{ preventiveMaintenances.current_page }} of {{ preventiveMaintenances.last_page }}
                </span>
                <Button
                    variant="secondary"
                    :disabled="preventiveMaintenances.current_page === preventiveMaintenances.last_page"
                    @click="goToPage(preventiveMaintenances.current_page + 1)"
                >
                    Next
                </Button>
            </div>
        </div>

        <!-- Create/Edit PM Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                {{ showCreateModal ? "Create Preventive Maintenance Schedule" : "Edit Preventive Maintenance Schedule" }}
            </template>

            <form @submit.prevent="savePM" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="pmForm.title"
                            label="Schedule Title"
                            placeholder="Enter schedule title"
                            :error="getFieldError('title')"
                            required
                            @blur="handleFieldBlur('title')"
                        />
                        <FormInput
                            v-model="pmForm.code"
                            label="Schedule Code"
                            placeholder="Auto-generated if empty"
                            hint="Leave empty to auto-generate"
                            :error="getFieldError('code')"
                            @blur="handleFieldBlur('code')"
                        />
                    </div>
                    <FormTextarea
                        v-model="pmForm.description"
                        label="Description"
                        placeholder="Enter schedule description"
                        :error="getFieldError('description')"
                        rows="3"
                        @blur="handleFieldBlur('description')"
                    />
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Type Select -->
                        <FormSelect
                            v-model="pmForm.maintenance_type"
                            label="Maintenance Type"
                            placeholder="Select type"
                            :options="maintenanceTypeOptions"
                            :error="getFieldError('maintenance_type')"
                            required
                            @blur="handleFieldBlur('maintenance_type')"
                            @update:modelValue="handleFieldBlur('maintenance_type')"
                        />

                        <!-- Priority Select -->
                        <FormSelect
                            v-model="pmForm.priority"
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
                            v-model="pmForm.status"
                            label="Status"
                            placeholder="Select status"
                            :options="statusOptions"
                            :error="getFieldError('status')"
                            @blur="handleFieldBlur('status')"
                            @update:modelValue="handleFieldBlur('status')"
                        />
                    </div>
                </div>

                <!-- Asset & Assignment -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Asset & Assignment
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Asset Select -->
                        <FormSelect
                            v-model="pmForm.asset_id"
                            label="Asset"
                            placeholder="Select asset"
                            :options="assetOptions"
                            :error="getFieldError('asset_id')"
                            required
                            @blur="handleFieldBlur('asset_id')"
                            @update:modelValue="handleFieldBlur('asset_id')"
                        />

                        <!-- Location Select -->
                        <FormSelect
                            v-model="pmForm.location_id"
                            label="Location"
                            placeholder="Select location (optional)"
                            :options="locationOptions"
                            :error="getFieldError('location_id')"
                            @blur="handleFieldBlur('location_id')"
                            @update:modelValue="handleFieldBlur('location_id')"
                        />

                        <!-- Assigned User Select -->
                        <FormSelect
                            v-model="pmForm.assigned_to"
                            label="Assign To"
                            placeholder="Select user (optional)"
                            :options="userOptions"
                            :error="getFieldError('assigned_to')"
                            @blur="handleFieldBlur('assigned_to')"
                            @update:modelValue="handleFieldBlur('assigned_to')"
                        />
                    </div>
                </div>

                <!-- Schedule Configuration -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Schedule Configuration
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <FormSelect
                            v-model="pmForm.frequency_type"
                            label="Frequency Type"
                            placeholder="Select frequency"
                            :options="frequencyOptions"
                            :error="getFieldError('frequency_type')"
                            required
                            @blur="handleFieldBlur('frequency_type')"
                            @update:modelValue="handleFieldBlur('frequency_type')"
                        />
                        <FormInput
                            v-model="pmForm.frequency_value"
                            label="Frequency Value"
                            type="number"
                            min="1"
                            placeholder="Every X frequency"
                            :error="getFieldError('frequency_value')"
                            @blur="handleFieldBlur('frequency_value')"
                        />
                        <FormInput
                            v-model="pmForm.start_date"
                            label="Start Date"
                            type="date"
                            :error="getFieldError('start_date')"
                            required
                            @blur="handleFieldBlur('start_date')"
                        />
                    </div>
                </div>

                <!-- Work Details -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Work Details
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="pmForm.estimated_duration_hours"
                            label="Estimated Duration (Hours)"
                            type="number"
                            step="0.5"
                            min="0"
                            placeholder="Enter estimated hours"
                            :error="getFieldError('estimated_duration_hours')"
                            @blur="handleFieldBlur('estimated_duration_hours')"
                        />
                        <FormInput
                            v-model="pmForm.estimated_cost"
                            label="Estimated Cost"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter estimated cost"
                            :error="getFieldError('estimated_cost')"
                            @blur="handleFieldBlur('estimated_cost')"
                        />
                    </div>
                    <FormTextarea
                        v-model="pmForm.work_instructions"
                        label="Work Instructions"
                        placeholder="Enter detailed work instructions"
                        :error="getFieldError('work_instructions')"
                        rows="4"
                        @blur="handleFieldBlur('work_instructions')"
                    />
                    <FormTextarea
                        v-model="pmForm.notes"
                        label="Additional Notes"
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
                        {{ showCreateModal ? "Create Schedule" : "Update Schedule" }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View PM Modal -->
        <Modal :is-open="showViewModal" @close="closeModals" size="xl">
            <template #title>Preventive Maintenance Details</template>

            <div v-if="selectedPM" class="space-y-6">
                <!-- PM Header -->
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3
                            class="text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ selectedPM.title }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ selectedPM.code }}
                        </p>
                        <div class="flex items-center gap-4 mt-2">
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getPriorityBadgeClass(selectedPM.priority),
                                ]"
                            >
                                {{ selectedPM.priority }} Priority
                            </span>
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getStatusBadgeClass(selectedPM.status),
                                ]"
                            >
                                {{ selectedPM.status }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ selectedPM.maintenance_type }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button
                            @click="handleEditPM(selectedPM)"
                            variant="primary"
                            size="sm"
                        >
                            <PencilIcon class="w-4 h-4 mr-2" />
                            Edit
                        </Button>
                    </div>
                </div>

                <!-- PM Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Asset & Assignment
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Asset:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedPM.asset ? selectedPM.asset.name : "N/A"
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
                                        selectedPM.location ? selectedPM.location.name : "N/A"
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
                                        selectedPM.assigned_user ? selectedPM.assigned_user.name : "Unassigned"
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
                                    >Frequency:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        formatFrequency(selectedPM.frequency_type, selectedPM.frequency_value)
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Start Date:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedPM.start_date
                                            ? formatDate(selectedPM.start_date)
                                            : "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Next Due Date:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{
                                        selectedPM.next_due_date
                                            ? formatDate(selectedPM.next_due_date)
                                            : "N/A"
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Compliance Rate:</span
                                >
                                <span
                                    class="text-sm text-gray-900 dark:text-white"
                                    >{{ selectedPM.compliance_rate }}%</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Instructions -->
                <div class="space-y-4">
                    <div v-if="selectedPM.description">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Description
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedPM.description }}
                        </p>
                    </div>

                    <div v-if="selectedPM.work_instructions">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Work Instructions
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                            {{ selectedPM.work_instructions }}
                        </p>
                    </div>

                    <div v-if="selectedPM.notes">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Notes
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ selectedPM.notes }}
                        </p>
                    </div>
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
    CalendarDaysIcon,
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    ChartBarIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/outline";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();
const authStore = useAuthStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const preventiveMaintenances = ref({});
const statistics = ref({});
const selectedPMs = ref([]);
const userOptions = ref([]);
const assetOptions = ref([]);
const locationOptions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedPM = ref(null);
const saving = ref(false);

// Filters
const filters = ref({
    search: "",
    status: "",
    priority: "",
    maintenance_type: "",
    asset_id: "",
    frequency_type: "",
});

// Form data
const pmForm = ref({
    title: "",
    code: "",
    description: "",
    maintenance_type: "",
    priority: "",
    status: "active",
    asset_id: "",
    location_id: "",
    assigned_to: "",
    frequency_type: "",
    frequency_value: 1,
    start_date: "",
    estimated_duration_hours: "",
    estimated_cost: "",
    work_instructions: "",
    notes: "",
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Options
const statusOptions = [
    { value: "active", label: "Active" },
    { value: "inactive", label: "Inactive" },
    { value: "suspended", label: "Suspended" },
    { value: "completed", label: "Completed" },
];

const priorityOptions = [
    { value: "low", label: "Low" },
    { value: "medium", label: "Medium" },
    { value: "high", label: "High" },
    { value: "critical", label: "Critical" },
];

const maintenanceTypeOptions = [
    { value: "inspection", label: "Inspection" },
    { value: "cleaning", label: "Cleaning" },
    { value: "lubrication", label: "Lubrication" },
    { value: "calibration", label: "Calibration" },
    { value: "replacement", label: "Replacement" },
    { value: "testing", label: "Testing" },
    { value: "adjustment", label: "Adjustment" },
    { value: "other", label: "Other" },
];

const frequencyOptions = [
    { value: "daily", label: "Daily" },
    { value: "weekly", label: "Weekly" },
    { value: "monthly", label: "Monthly" },
    { value: "quarterly", label: "Quarterly" },
    { value: "semi_annual", label: "Semi-Annual" },
    { value: "annual", label: "Annual" },
    { value: "custom", label: "Custom" },
];

// Computed properties
const totalPreventiveMaintenances = computed(() => statistics.value.total_preventive_maintenances || 0);
const activePreventiveMaintenances = computed(() => statistics.value.active_preventive_maintenances || 0);
const dueSoonPreventiveMaintenances = computed(() => statistics.value.due_soon_preventive_maintenances || 0);
const overduePreventiveMaintenances = computed(() => statistics.value.overdue_preventive_maintenances || 0);
const averageComplianceRate = computed(() => statistics.value.average_compliance_rate || 0);

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
        key: "frequency",
        label: "Frequency",
        sortable: false,
    },
    {
        key: "next_due_date",
        label: "Next Due Date",
        sortable: true,
    },
    {
        key: "compliance_rate",
        label: "Compliance",
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
        loadPreventiveMaintenances();
    }, 500);
};

const handleFilterChange = () => {
    loadPreventiveMaintenances();
};

const clearFilters = () => {
    filters.value = {
        search: "",
        status: "",
        priority: "",
        maintenance_type: "",
        asset_id: "",
        frequency_type: "",
    };
    loadPreventiveMaintenances();
};

const handleRefreshPM = async () => {
    refreshLoading.value = true;
    try {
        await Promise.all([loadPreventiveMaintenances(), loadStatistics()]);
        notification.success("Preventive maintenance data refreshed successfully");
    } catch (error) {
        console.error("Refresh PM error:", error);
        notification.error("Failed to refresh preventive maintenance data");
    } finally {
        refreshLoading.value = false;
    }
};

const goToPage = (page) => {
    loadPreventiveMaintenances(page);
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
        active: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        inactive: "bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400",
        suspended: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        completed: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
    };
    return classes[status] || classes.active;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const formatFrequency = (type, value) => {
    const typeMap = {
        daily: "Daily",
        weekly: "Weekly",
        monthly: "Monthly",
        quarterly: "Quarterly",
        semi_annual: "Semi-Annual",
        annual: "Annual",
        custom: "Custom"
    };

    const typeLabel = typeMap[type] || type;
    return value > 1 ? `Every ${value} ${typeLabel}` : typeLabel;
};

const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "title":
            if (!value || value.trim() === "") {
                errors.push("Schedule title is required");
            } else if (value.length < 3) {
                errors.push("Title must be at least 3 characters");
            } else if (value.length > 255) {
                errors.push("Title must be less than 255 characters");
            }
            break;
        case "maintenance_type":
            if (!value) {
                errors.push("Maintenance type is required");
            }
            break;
        case "priority":
            if (!value) {
                errors.push("Priority is required");
            }
            break;
        case "asset_id":
            if (!value) {
                errors.push("Asset is required");
            }
            break;
        case "frequency_type":
            if (!value) {
                errors.push("Frequency type is required");
            }
            break;
        case "start_date":
            if (!value) {
                errors.push("Start date is required");
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(pmForm.value).forEach((field) => {
        const fieldErrors = validateField(field, pmForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, pmForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

const savePM = async () => {
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "preventive-maintenances"
            : `preventive-maintenances/${selectedPM.value.id}`;

        const response = showCreateModal.value
            ? await apiPost(url, pmForm.value)
            : await apiPut(url, pmForm.value);

        if (response.success) {
            notification.success(
                `Preventive maintenance schedule ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            await Promise.all([loadPreventiveMaintenances(), loadStatistics()]);
        } else {
            if (response.errors) {
                formErrors.value = response.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(response.message || "Failed to save preventive maintenance schedule");
            }
        }
    } catch (error) {
        console.error("Save PM error:", error);
        notification.error("Failed to save preventive maintenance schedule. Please try again.");
    } finally {
        saving.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    selectedPM.value = null;
    pmForm.value = {
        title: "",
        code: "",
        description: "",
        maintenance_type: "",
        priority: "",
        status: "active",
        asset_id: "",
        location_id: "",
        assigned_to: "",
        frequency_type: "",
        frequency_value: 1,
        start_date: "",
        estimated_duration_hours: "",
        estimated_cost: "",
        work_instructions: "",
        notes: "",
    };
    formErrors.value = {};
    formTouched.value = {};
};

const handleAddPM = () => {
    showCreateModal.value = true;
    loadOptions();
};

const handleEditPM = (pm) => {
    selectedPM.value = pm;
    pmForm.value = {
        title: pm.title,
        code: pm.code,
        description: pm.description || "",
        maintenance_type: pm.maintenance_type,
        priority: pm.priority,
        status: pm.status,
        asset_id: pm.asset_id || "",
        location_id: pm.location_id || "",
        assigned_to: pm.assigned_to || "",
        frequency_type: pm.frequency_type,
        frequency_value: pm.frequency_value || 1,
        start_date: pm.start_date,
        estimated_duration_hours: pm.estimated_duration_hours || "",
        estimated_cost: pm.estimated_cost || "",
        work_instructions: pm.work_instructions || "",
        notes: pm.notes || "",
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
    loadOptions();
};

const handleViewPM = (pm) => {
    selectedPM.value = pm;
    showViewModal.value = true;
};

const handleDeletePM = (pm) => {
    confirmationModal.showModal({
        title: "Delete Preventive Maintenance Schedule",
        message: `Are you sure you want to delete "${pm.title}"?`,
        description:
            "This action cannot be undone. This will permanently delete the preventive maintenance schedule and all its associated data.",
        confirmText: "Delete Schedule",
        cancelText: "Cancel",
        onConfirm: async () => {
            const response = await apiDelete(`preventive-maintenances/${pm.id}`);
            if (response.success) {
                notification.success("Preventive maintenance schedule deleted successfully");
                await Promise.all([loadPreventiveMaintenances(), loadStatistics()]);
                return response;
            } else {
                throw new Error(response.message || "Failed to delete preventive maintenance schedule");
            }
        },
        onSuccess: (result) => {
            console.log("PM deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete PM:", error);
            notification.error(error.message || "Failed to delete preventive maintenance schedule");
        },
    });
};

const handleBulkAction = (action, selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select preventive maintenance schedules to perform bulk action");
        return;
    }

    if (action === "delete") {
        confirmationModal.showModal({
            title: "Bulk Delete Preventive Maintenance Schedules",
            message: `Are you sure you want to delete ${
                selectedItems.length
            } preventive maintenance schedule${selectedItems.length === 1 ? "" : "s"}?`,
            description:
                "This action cannot be undone. This will permanently delete the selected preventive maintenance schedules and all their associated data.",
            confirmText: "Delete Schedules",
            cancelText: "Cancel",
            onConfirm: async () => {
                try {
                    const deletePromises = selectedItems.map((item) =>
                        apiDelete(`preventive-maintenances/${item.id}`)
                    );
                    await Promise.all(deletePromises);
                    notification.success(
                        `${selectedItems.length} preventive maintenance schedule${
                            selectedItems.length === 1 ? "" : "s"
                        } deleted successfully`
                    );
                    await Promise.all([loadPreventiveMaintenances(), loadStatistics()]);
                    return { success: true };
                } catch (error) {
                    throw new Error("Failed to delete some preventive maintenance schedules");
                }
            },
            onSuccess: (result) => {
                console.log("Bulk delete completed:", result);
            },
            onError: (error) => {
                console.error("Bulk delete failed:", error);
                notification.error(error.message || "Failed to delete preventive maintenance schedules");
            },
        });
    }
};

const handleSelectionChange = (selectedItems) => {
    selectedPMs.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const handleExportPM = async () => {
    try {
        // Build query parameters from current filters
        const params = new URLSearchParams(filters.value);

        // Create a temporary link to download the file
        const link = document.createElement("a");
        link.href = `/api/preventive-maintenances-export?${params}`;
        link.download = `preventive_maintenances_${new Date().toISOString().split("T")[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        notification.success(
            "Preventive maintenance export started. Download will begin shortly."
        );
    } catch (error) {
        console.error("Export PM error:", error);
        notification.error("Failed to export preventive maintenance schedules");
    }
};

const loadPreventiveMaintenances = async (page = 1) => {
    try {
        loading.value = true;
        const params = new URLSearchParams({
            page: page.toString(),
            ...filters.value,
        });

        const response = await apiGet(`preventive-maintenances?${params}`);
        if (response.success) {
            preventiveMaintenances.value = response.data;
        }
    } catch (error) {
        console.error("Error loading preventive maintenances:", error);
        notification.error("Failed to load preventive maintenance schedules");
    } finally {
        loading.value = false;
    }
};

const loadStatistics = async () => {
    try {
        const response = await apiGet("preventive-maintenances-statistics");
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
        const usersResponse = await apiGet("preventive-maintenances-user-options");
        if (usersResponse.success) {
            userOptions.value = usersResponse.data;
        }

        // Load assets
        const assetsResponse = await apiGet("preventive-maintenances-asset-options");
        if (assetsResponse.success) {
            assetOptions.value = assetsResponse.data;
        }

        // Load locations
        const locationsResponse = await apiGet("preventive-maintenances-location-options");
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
        loadPreventiveMaintenances();
    },
    { deep: true }
);

// Lifecycle
onMounted(() => {
    loadPreventiveMaintenances();
    loadStatistics();
    loadOptions();
});
</script>