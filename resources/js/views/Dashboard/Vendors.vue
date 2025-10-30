<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Vendors
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage vendors and suppliers
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <TruckIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Vendors
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalVendors }}
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
                            Active Vendors
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activeVendors }}
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
                        <BuildingOfficeIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Suppliers
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ suppliersCount }}
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
                            Service Providers
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ serviceProvidersCount }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Vendors"
            description="A list of all vendors and suppliers in your system including their contact information and status."
            :data="vendors"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="Create Vendor"
            :show-filters="false"
            :show-bulk-actions="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search vendors..."
            empty-title="No vendors found"
            empty-description="Get started by creating your first vendor."
            @add="handleAddVendor"
            @edit="handleEditVendor"
            @delete="handleDeleteVendor"
            @bulk-action="handleBulkAction"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshVendors"
        >
            <!-- Custom Name Column -->
            <template #column-name="{ item }">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 rounded-full mr-3 flex-shrink-0 flex items-center justify-center text-white text-sm font-medium"
                        :class="getVendorTypeBadgeClass(item.vendor_type)"
                    >
                        {{ item.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <p
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ item.company_name }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Code Column -->
            <template #column-code="{ item }">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200"
                >
                    {{ item.code }}
                </span>
            </template>

            <!-- Custom Type Column -->
            <template #column-type="{ item }">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getVendorTypeBadgeClass(item.vendor_type)"
                >
                    {{ item.vendor_type_label }}
                </span>
            </template>

            <!-- Custom Contact Column -->
            <template #column-contact="{ item }">
                <div class="text-sm">
                    <div
                        v-if="item.email"
                        class="text-gray-900 dark:text-white"
                    >
                        {{ item.email }}
                    </div>
                    <div
                        v-if="item.phone"
                        class="text-gray-500 dark:text-gray-400"
                    >
                        {{ item.phone }}
                    </div>
                </div>
            </template>

            <!-- Custom Payment Terms Column -->
            <template #column-payment="{ item }">
                <span class="text-sm text-gray-900 dark:text-white">
                    {{ item.payment_terms_label }}
                </span>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            item.is_active ? 'bg-red-500' : 'bg-red-500',
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                        >{{ item.is_active ? "Active" : "Inactive" }}</span
                    >
                </div>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleEditVendor(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Edit Vendor"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleToggleStatus(item)"
                        :class="[
                            'p-1.5 rounded-lg transition-colors duration-200',
                            item.is_active
                                ? 'text-gray-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:text-orange-400 dark:hover:bg-orange-900/20'
                                : 'text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20',
                        ]"
                        :title="
                            item.is_active
                                ? 'Deactivate Vendor'
                                : 'Activate Vendor'
                        "
                    >
                        <PlayIcon v-if="!item.is_active" class="w-4 h-4" />
                        <PauseIcon v-else class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeleteVendor(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Vendor"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Create/Edit Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="xl"
        >
            <template #title>
                {{ showCreateModal ? "Create Vendor" : "Edit Vendor" }}
            </template>

            <form @submit.prevent="saveVendor" class="space-y-6">
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h4
                            class="text-md font-medium text-gray-900 dark:text-white mb-4"
                        >
                            Basic Information
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <FormInput
                                v-model="vendorForm.name"
                                label="Vendor Name"
                                placeholder="Enter vendor name"
                                :error="getFieldError('name')"
                                @blur="handleFieldBlur('name')"
                                required
                            />
                            <FormInput
                                v-model="vendorForm.code"
                                label="Vendor Code"
                                placeholder="Auto-generated if empty"
                                :error="getFieldError('code')"
                                @blur="handleFieldBlur('code')"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <FormInput
                                v-model="vendorForm.company_name"
                                label="Company Name"
                                placeholder="Enter company name"
                                :error="getFieldError('company_name')"
                                @blur="handleFieldBlur('company_name')"
                                required
                            />
                            <!-- Vendor Type Select -->
                            <div class="relative">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Vendor Type
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="vendorForm.vendor_type"
                                    data-hs-select='{
                                      "hasSearch": true,
                                      "searchPlaceholder": "Search vendor types...",
                                      "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                                      "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-900",
                                      "placeholder": "Select vendor type",
                                      "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                                      "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                                      "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700",
                                      "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                      "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-gray-800 dark:text-neutral-200 \" data-title></div></div></div>",
                                      "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                    }'
                                    class="hidden"
                                    @blur="handleFieldBlur('vendor_type')"
                                >
                                    <option value="">Select vendor type</option>
                                    <option
                                        v-for="type in vendorTypeOptions"
                                        :key="type.value"
                                        :value="type.value"
                                        :data-hs-select-option="`{&quot;icon&quot;: &quot;<svg class=\&quot;w-4 h-4 text-gray-400\&quot; fill=\&quot;none\&quot; stroke=\&quot;currentColor\&quot; viewBox=\&quot;0 0 24 24\&quot;><path stroke-linecap=\&quot;round\&quot; stroke-linejoin=\&quot;round\&quot; stroke-width=\&quot;2\&quot; d=\&quot;M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\&quot;></path></svg>&quot;}`"
                                    >
                                        {{ type.label }}
                                    </option>
                                </select>
                                <p
                                    v-if="getFieldError('vendor_type')"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ getFieldError("vendor_type") }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h4
                            class="text-md font-medium text-gray-900 dark:text-white mb-4"
                        >
                            Contact Information
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <FormInput
                                v-model="vendorForm.email"
                                label="Email"
                                type="email"
                                placeholder="vendor@company.com"
                                :error="getFieldError('email')"
                                @blur="handleFieldBlur('email')"
                            />
                            <FormInput
                                v-model="vendorForm.phone"
                                label="Phone"
                                placeholder="+62-21-1234567"
                                :error="getFieldError('phone')"
                                @blur="handleFieldBlur('phone')"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <FormInput
                                v-model="vendorForm.contact_person"
                                label="Contact Person"
                                placeholder="John Doe"
                                :error="getFieldError('contact_person')"
                                @blur="handleFieldBlur('contact_person')"
                            />
                            <FormInput
                                v-model="vendorForm.contact_email"
                                label="Contact Email"
                                type="email"
                                placeholder="john@company.com"
                                :error="getFieldError('contact_email')"
                                @blur="handleFieldBlur('contact_email')"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <FormInput
                                v-model="vendorForm.contact_phone"
                                label="Contact Phone"
                                placeholder="+62-812-3456789"
                                :error="getFieldError('contact_phone')"
                                @blur="handleFieldBlur('contact_phone')"
                            />
                            <FormInput
                                v-model="vendorForm.website"
                                label="Website"
                                type="url"
                                placeholder="https://company.com"
                                :error="getFieldError('website')"
                                @blur="handleFieldBlur('website')"
                            />
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div>
                        <h4
                            class="text-md font-medium text-gray-900 dark:text-white mb-4"
                        >
                            Address Information
                        </h4>
                        <FormTextarea
                            v-model="vendorForm.address"
                            label="Address"
                            placeholder="Enter full address"
                            :error="getFieldError('address')"
                            @blur="handleFieldBlur('address')"
                            rows="3"
                        />
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <FormInput
                                v-model="vendorForm.city"
                                label="City"
                                placeholder="Jakarta"
                                :error="getFieldError('city')"
                                @blur="handleFieldBlur('city')"
                            />
                            <FormInput
                                v-model="vendorForm.state"
                                label="State/Province"
                                placeholder="DKI Jakarta"
                                :error="getFieldError('state')"
                                @blur="handleFieldBlur('state')"
                            />
                            <FormInput
                                v-model="vendorForm.postal_code"
                                label="Postal Code"
                                placeholder="12345"
                                :error="getFieldError('postal_code')"
                                @blur="handleFieldBlur('postal_code')"
                            />
                        </div>
                        <FormInput
                            v-model="vendorForm.country"
                            label="Country"
                            placeholder="Indonesia"
                            :error="getFieldError('country')"
                            @blur="handleFieldBlur('country')"
                            class="mt-4"
                        />
                    </div>

                    <!-- Business Information -->
                    <div>
                        <h4
                            class="text-md font-medium text-gray-900 dark:text-white mb-4"
                        >
                            Business Information
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Payment Terms Select -->
                            <div class="relative">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Payment Terms
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="vendorForm.payment_terms"
                                    data-hs-select='{
                                      "hasSearch": true,
                                      "searchPlaceholder": "Search payment terms...",
                                      "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                                      "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-900",
                                      "placeholder": "Select payment terms",
                                      "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                                      "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                                      "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700",
                                      "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                      "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-gray-800 dark:text-neutral-200 \" data-title></div></div></div>",
                                      "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                    }'
                                    class="hidden"
                                    @blur="handleFieldBlur('payment_terms')"
                                >
                                    <option value="">
                                        Select payment terms
                                    </option>
                                    <option
                                        v-for="term in paymentTermsOptions"
                                        :key="term.value"
                                        :value="term.value"
                                        :data-hs-select-option="`{&quot;icon&quot;: &quot;<svg class=\&quot;w-4 h-4 text-gray-400\&quot; fill=\&quot;none\&quot; stroke=\&quot;currentColor\&quot; viewBox=\&quot;0 0 24 24\&quot;><path stroke-linecap=\&quot;round\&quot; stroke-linejoin=\&quot;round\&quot; stroke-width=\&quot;2\&quot; d=\&quot;M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1\&quot;></path></svg>&quot;}`"
                                    >
                                        {{ term.label }}
                                    </option>
                                </select>
                                <p
                                    v-if="getFieldError('payment_terms')"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ getFieldError("payment_terms") }}
                                </p>
                            </div>
                            <FormInput
                                v-model="vendorForm.credit_limit"
                                label="Credit Limit"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="50000000"
                                :error="getFieldError('credit_limit')"
                                @blur="handleFieldBlur('credit_limit')"
                            />
                        </div>
                        <FormInput
                            v-model="vendorForm.tax_id"
                            label="Tax ID (NPWP)"
                            placeholder="01.234.567.8-901.000"
                            :error="getFieldError('tax_id')"
                            @blur="handleFieldBlur('tax_id')"
                            class="mt-4"
                        />
                        <FormTextarea
                            v-model="vendorForm.description"
                            label="Description"
                            placeholder="Enter vendor description"
                            :error="getFieldError('description')"
                            @blur="handleFieldBlur('description')"
                            rows="3"
                            class="mt-4"
                        />
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
                            showCreateModal ? "Create Vendor" : "Update Vendor"
                        }}
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
import { ref, reactive, computed, onMounted } from "vue";
import DataTable from "../../components/UI/DataTable.vue";
import Modal from "../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../components/Forms/FormInput.vue";
import FormTextarea from "../../components/Forms/FormTextarea.vue";
import Button from "../../components/Base/Button.vue";
import { useNotificationStore } from "@/stores/notification";
import { useConfirmationModalStore } from "@/stores/confirmationModal";
import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";
import {
    TruckIcon,
    CheckCircleIcon,
    BuildingOfficeIcon,
    WrenchScrewdriverIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    PauseIcon,
    PlayIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const vendors = ref([]);
const selectedVendors = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedVendor = ref(null);
const saving = ref(false);

// Form data
const vendorForm = ref({
    name: "",
    code: "",
    company_name: "",
    email: "",
    phone: "",
    website: "",
    address: "",
    city: "",
    state: "",
    postal_code: "",
    country: "Indonesia",
    contact_person: "",
    contact_phone: "",
    contact_email: "",
    vendor_type: "supplier",
    description: "",
    credit_limit: "",
    payment_terms: "net_30",
    tax_id: "",
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Table columns
const columns = [
    {
        key: "name",
        label: "Vendor",
        sortable: true,
    },
    {
        key: "code",
        label: "Code",
        sortable: true,
    },
    {
        key: "type",
        label: "Type",
        sortable: true,
    },
    {
        key: "contact",
        label: "Contact",
        sortable: false,
    },
    {
        key: "payment",
        label: "Payment Terms",
        sortable: false,
    },
    {
        key: "status",
        label: "Status",
        sortable: true,
    },
];

// Options
const vendorTypeOptions = [
    { value: "supplier", label: "Supplier" },
    { value: "service_provider", label: "Service Provider" },
    { value: "contractor", label: "Contractor" },
    { value: "other", label: "Other" },
];

const paymentTermsOptions = [
    { value: "cash", label: "Cash" },
    { value: "net_15", label: "Net 15 Days" },
    { value: "net_30", label: "Net 30 Days" },
    { value: "net_45", label: "Net 45 Days" },
    { value: "net_60", label: "Net 60 Days" },
    { value: "custom", label: "Custom" },
];

// Computed properties
const totalVendors = computed(() => vendors.value.length);
const activeVendors = computed(
    () => vendors.value.filter((v) => v.is_active).length
);
const suppliersCount = computed(
    () => vendors.value.filter((v) => v.vendor_type === "supplier").length
);
const serviceProvidersCount = computed(
    () =>
        vendors.value.filter((v) => v.vendor_type === "service_provider").length
);

// Form validation functions
const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "name":
            if (!value || value.trim() === "") {
                errors.push("Name is required");
            } else if (value.length < 2) {
                errors.push("Name must be at least 2 characters");
            } else if (value.length > 255) {
                errors.push("Name must be less than 255 characters");
            }
            break;
        case "code":
            if (value && value.length > 20) {
                errors.push("Code must be less than 20 characters");
            }
            break;
        case "company_name":
            if (!value || value.trim() === "") {
                errors.push("Company name is required");
            } else if (value.length > 255) {
                errors.push("Company name must be less than 255 characters");
            }
            break;
        case "email":
        case "contact_email":
            if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                errors.push("Please enter a valid email address");
            }
            break;
        case "phone":
        case "contact_phone":
            if (value && value.length > 20) {
                errors.push("Phone number must be less than 20 characters");
            }
            break;
        case "website":
            if (value && !/^https?:\/\/.+\..+/.test(value)) {
                errors.push("Please enter a valid URL");
            }
            break;
        case "vendor_type":
            if (!value) {
                errors.push("Vendor type is required");
            }
            break;
        case "payment_terms":
            if (!value) {
                errors.push("Payment terms is required");
            }
            break;
        case "credit_limit":
            if (value && (isNaN(value) || value < 0)) {
                errors.push("Credit limit must be a positive number");
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(vendorForm.value).forEach((field) => {
        const fieldErrors = validateField(field, vendorForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

// Methods
const loadVendors = async () => {
    loading.value = true;
    try {
        const response = await apiGet("/vendors");
        vendors.value = response.data;
    } catch (error) {
        notification.error("Failed to load vendors. Please try again.");
    } finally {
        loading.value = false;
    }
};

const handleRefreshVendors = async () => {
    refreshLoading.value = true;
    try {
        await loadVendors();
        notification.success("Vendors refreshed successfully.");
    } catch (error) {
        notification.error("Failed to refresh vendors. Please try again.");
    } finally {
        refreshLoading.value = false;
    }
};

const resetForm = () => {
    Object.assign(vendorForm, {
        name: "",
        code: "",
        company_name: "",
        email: "",
        phone: "",
        website: "",
        address: "",
        city: "",
        state: "",
        postal_code: "",
        country: "Indonesia",
        contact_person: "",
        contact_phone: "",
        contact_email: "",
        vendor_type: "supplier",
        description: "",
        credit_limit: "",
        payment_terms: "net_30",
        tax_id: "",
    });
    formErrors.value = {};
    formTouched.value = {};
};

const handleAddVendor = () => {
    resetForm();
    showCreateModal.value = true;
    selectedVendor.value = null;
};

const handleEditVendor = (vendor) => {
    resetForm();
    selectedVendor.value = vendor;

    // Populate form
    Object.keys(vendorForm).forEach((key) => {
        if (vendor[key] !== undefined && vendor[key] !== null) {
            vendorForm[key] = vendor[key];
        }
    });

    showEditModal.value = true;
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    selectedVendor.value = null;
    resetForm();
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, vendorForm[field]);
    formErrors.value[field] = errors;
};

const saveVendor = async () => {
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;

    try {
        let response;
        if (showCreateModal.value) {
            response = await apiPost("/vendors", vendorForm);
        } else {
            response = await apiPut(
                `/vendors/${selectedVendor.value.id}`,
                vendorForm
            );
        }

        notification.success(response.message);
        closeModals();
        await loadVendors();
    } catch (error) {
        if (error.response?.data?.errors) {
            // Server validation errors
            Object.keys(error.response.data.errors).forEach((field) => {
                formTouched.value[field] = true;
                formErrors.value[field] = error.response.data.errors[field];
            });
        }
        notification.error(
            error.response?.data?.message ||
                "Failed to save vendor. Please try again."
        );
    } finally {
        saving.value = false;
    }
};

const handleToggleStatus = async (vendor) => {
    try {
        const response = await apiPost(`/vendors/${vendor.id}/toggle-status`);

        notification.success(response.message);

        await loadVendors();
    } catch (error) {
        notification.error(
            error.response?.data?.message ||
                "Failed to toggle vendor status. Please try again."
        );
    }
};

const handleDeleteVendor = (vendor) => {
    confirmationModal.showModal({
        title: "Delete Vendor",
        message: `Are you sure you want to delete "${vendor.name}"?`,
        description:
            "This action cannot be undone. Make sure this vendor has no assets or purchase orders assigned to it.",
        confirmText: "Delete Vendor",
        cancelText: "Cancel",
        onConfirm: async () => {
            try {
                const data = await apiDelete(`vendors/${vendor.id}`);
                notification.success(
                    data.message || "Vendor deleted successfully"
                );
                loadVendors();
                return { success: true };
            } catch (error) {
                throw new Error(
                    error.response?.data?.message || "Failed to delete vendor"
                );
            }
        },
        onSuccess: (result) => {
            console.log("Delete completed:", result);
        },
        onError: (error) => {
            console.error("Failed to delete vendor:", error);
            notification.error(error.message || "Failed to delete vendor");
        },
    });
};

const handleSelectionChange = (selectedItems) => {
    selectedVendors.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const handleBulkAction = (selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select vendors to perform bulk action");
        return;
    }

    confirmationModal.showModal({
        title: "Bulk Delete Vendors",
        message: `Are you sure you want to delete ${
            selectedItems.length
        } vendor${selectedItems.length === 1 ? "" : "s"}?`,
        description:
            "This action cannot be undone. Make sure these vendors have no assets or purchase orders assigned to them.",
        confirmText: "Delete Vendors",
        cancelText: "Cancel",
        onConfirm: async () => {
            try {
                const deletePromises = selectedItems.map((item) =>
                    apiDelete(`vendors/${item.id}`)
                );
                await Promise.all(deletePromises);
                notification.success(
                    `${selectedItems.length} vendor${
                        selectedItems.length === 1 ? "" : "s"
                    } deleted successfully`
                );
                loadVendors();
                return { success: true };
            } catch (error) {
                throw new Error("Failed to delete some vendors");
            }
        },
        onSuccess: (result) => {
            console.log("Bulk delete completed:", result);
        },
        onError: (error) => {
            console.error("Bulk delete failed:", error);
            notification.error(error.message || "Failed to delete vendors");
        },
    });
};

const getVendorTypeBadgeClass = (type) => {
    const classes = {
        supplier:
            "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        service_provider:
            "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        contractor:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        other: "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200",
    };
    return classes[type] || classes.other;
};

// Lifecycle
onMounted(() => {
    loadVendors();
});
</script>
