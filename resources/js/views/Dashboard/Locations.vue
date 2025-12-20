<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $t('locations.title') }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $t('locations.subtitle') }}
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg"
                    >
                        <MapPinIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t('locations.totalLocations') }}
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalLocations }}
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
                            {{ $t('locations.activeLocations') }}
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activeLocations }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <BuildingOfficeIcon
                            class="w-6 h-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t('locations.rootLocations') }}
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ rootLocations }}
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
                        <PlusIcon
                            class="w-6 h-6 text-orange-600 dark:text-orange-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t('locations.newThisMonth') }}
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ newLocations }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            :title="$t('locations.tableTitle')"
            :description="$t('locations.tableDescription')"
            :data="locations"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            :add-button-text="$t('locations.createLocation')"
            :show-filters="false"
            :show-bulk-actions="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            :search-placeholder="$t('locations.searchPlaceholder')"
            :empty-title="$t('locations.emptyTitle')"
            :empty-description="$t('locations.emptyDescription')"
            @add="handleAddLocation"
            @edit="handleEditLocation"
            @delete="handleDeleteLocation"
            @bulk-action="handleBulkAction"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshLocations"
        >
            <!-- Custom Name Column with Hierarchy -->
            <template #column-name="{ item }">
                <div class="flex items-center">
                    <div
                        class="w-4 h-4 rounded-full mr-3 flex-shrink-0"
                        :style="{ backgroundColor: item.color }"
                    ></div>
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
                    </div>
                </div>
            </template>

            <!-- Custom Address Column -->
            <template #column-address="{ item }">
                <div class="max-w-xs">
                    <p class="text-sm text-gray-900 dark:text-white truncate">
                        {{ item.full_address || $t('locations.noAddress') }}
                    </p>
                    <p
                        v-if="item.city || item.country"
                        class="text-xs text-gray-500 dark:text-gray-400"
                    >
                        {{
                            [item.city, item.country].filter(Boolean).join(", ")
                        }}
                    </p>
                </div>
            </template>

            <!-- Custom Parent Column -->
            <template #column-parent="{ item }">
                <div v-if="item.parent" class="flex items-center">
                    <div
                        class="w-3 h-3 rounded-full mr-2"
                        :style="{
                            backgroundColor: item.parent.color || '#10B981',
                        }"
                    ></div>
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ item.parent.name }}
                    </span>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('locations.rootLocation') }}
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
                        >{{ item.is_active ? $t('products.statusActive') : $t('products.statusInactive') }}</span
                    >
                </div>
            </template>

            <!-- Custom Children Count Column -->
            <template #column-children="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        item.children_count > 0
                            ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                    ]"
                >
                    {{ item.children_count }} {{ item.children_count !== 1 ? $t('locations.childrenPlural') : $t('locations.child') }}
                </span>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleEditLocation(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        :title="$t('locations.editLocation')"
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
                                ? $t('locations.deactivateLocation')
                                : $t('locations.activateLocation')
                        "
                    >
                        <PlayIcon v-if="!item.is_active" class="w-4 h-4" />
                        <PauseIcon v-else class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeleteLocation(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        :title="$t('locations.deleteLocation')"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Create/Edit Location Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="lg"
        >
            <template #title>
                {{ showCreateModal ? $t('locations.createLocationTitle') : $t('locations.editLocationTitle') }}
            </template>

            <form @submit.prevent="saveLocation" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        {{ $t('locations.basicInformation') }}
                    </h3>
                    <FormInput
                        v-model="locationForm.name"
                        :label="$t('locations.name')"
                        :placeholder="$t('locations.namePlaceholder')"
                        :error="getFieldError('name')"
                        required
                        @blur="handleFieldBlur('name')"
                    />
                    <FormInput
                        v-model="locationForm.code"
                        :label="$t('locations.code')"
                        :placeholder="$t('locations.codePlaceholder')"
                        :error="getFieldError('code')"
                        @blur="handleFieldBlur('code')"
                    />
                    <FormTextarea
                        v-model="locationForm.description"
                        :label="$t('locations.description')"
                        :placeholder="$t('locations.descriptionPlaceholder')"
                        :error="getFieldError('description')"
                        rows="3"
                        @blur="handleFieldBlur('description')"
                    />
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="locationForm.color"
                            :label="$t('locations.color')"
                            type="color"
                            :error="getFieldError('color')"
                            @blur="handleFieldBlur('color')"
                        />
                        <!-- Parent Location Select -->
                        <div class="relative">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                {{ $t('locations.parentLocation') }}
                            </label>
                            <select
                                v-model="locationForm.parent_id"
                                data-hs-select='{
                                  "hasSearch": true,
                                  "searchPlaceholder": "Search parent locations...",
                                  "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                                  "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-900",
                                  "placeholder": "Select parent location (optional)",
                                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                                  "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                                  "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700",
                                  "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                  "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-gray-800 dark:text-neutral-200 \" data-title></div></div></div>",
                                  "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                }'
                                class="hidden"
                                @blur="handleFieldBlur('parent_id')"
                            >
                                <option value="">
                                    {{ $t('locations.selectParentPlaceholder') }}
                                </option>
                                <option
                                    v-for="parent in parentOptions"
                                    :key="parent.value"
                                    :value="parent.value"
                                    :data-hs-select-option="`{&quot;icon&quot;: &quot;<svg class=\&quot;w-4 h-4 text-gray-400\&quot; fill=\&quot;none\&quot; stroke=\&quot;currentColor\&quot; viewBox=\&quot;0 0 24 24\&quot;><path stroke-linecap=\&quot;round\&quot; stroke-linejoin=\&quot;round\&quot; stroke-width=\&quot;2\&quot; d=\&quot;M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z\&quot;></path><path stroke-linecap=\&quot;round\&quot; stroke-linejoin=\&quot;round\&quot; stroke-width=\&quot;2\&quot; d=\&quot;M15 11a3 3 0 11-6 0 3 3 0 016 0z\&quot;></path></svg>&quot;}`"
                                >
                                    {{ parent.label }}
                                </option>
                            </select>
                            <p
                                v-if="getFieldError('parent_id')"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ getFieldError("parent_id") }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        {{ $t('locations.addressInformation') }}
                    </h3>
                    <FormTextarea
                        v-model="locationForm.address"
                        :label="$t('locations.addressLabel')"
                        :placeholder="$t('locations.addressPlaceholder')"
                        :error="getFieldError('address')"
                        rows="2"
                        @blur="handleFieldBlur('address')"
                    />
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="locationForm.city"
                            :label="$t('locations.city')"
                            :placeholder="$t('locations.cityPlaceholder')"
                            :error="getFieldError('city')"
                            @blur="handleFieldBlur('city')"
                        />
                        <FormInput
                            v-model="locationForm.state"
                            :label="$t('locations.state')"
                            :placeholder="$t('locations.statePlaceholder')"
                            :error="getFieldError('state')"
                            @blur="handleFieldBlur('state')"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="locationForm.country"
                            :label="$t('locations.country')"
                            :placeholder="$t('locations.countryPlaceholder')"
                            :error="getFieldError('country')"
                            @blur="handleFieldBlur('country')"
                        />
                        <FormInput
                            v-model="locationForm.postal_code"
                            :label="$t('locations.postalCode')"
                            :placeholder="$t('locations.postalCodePlaceholder')"
                            :error="getFieldError('postal_code')"
                            @blur="handleFieldBlur('postal_code')"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="locationForm.latitude"
                            :label="$t('locations.latitude')"
                            type="number"
                            step="any"
                            :placeholder="$t('locations.latitudePlaceholder')"
                            :error="getFieldError('latitude')"
                            @blur="handleFieldBlur('latitude')"
                        />
                        <FormInput
                            v-model="locationForm.longitude"
                            :label="$t('locations.longitude')"
                            type="number"
                            step="any"
                            :placeholder="$t('locations.longitudePlaceholder')"
                            :error="getFieldError('longitude')"
                            @blur="handleFieldBlur('longitude')"
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
                        {{ $t('locations.cancel') }}
                    </Button>
                    <Button
                        type="submit"
                        :loading="saving"
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        {{
                            showCreateModal
                                ? $t('locations.create')
                                : $t('locations.update')
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
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
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
    MapPinIcon,
    CheckCircleIcon,
    BuildingOfficeIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    PauseIcon,
    PlayIcon,
} from "@heroicons/vue/24/outline";

const { t } = useI18n();
const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const locations = ref([]);
const selectedLocations = ref([]);
const parentOptions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedLocation = ref(null);
const saving = ref(false);

// Form data
const locationForm = ref({
    name: "",
    code: "",
    description: "",
    address: "",
    city: "",
    state: "",
    country: "",
    postal_code: "",
    latitude: null,
    longitude: null,
    color: "#10B981",
    parent_id: null,
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Validation functions
const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "name":
            if (!value || value.trim() === "") {
                errors.push(t("locations.validationNameRequired"));
            } else if (value.length < 2) {
                errors.push(t("locations.validationNameMinLength"));
            } else if (value.length > 255) {
                errors.push(t("locations.validationNameMaxLength"));
            }
            break;
        case "code":
            if (value && value.length > 20) {
                errors.push(t("locations.validationCodeMaxLength"));
            }
            break;
        case "description":
            if (value && value.length > 1000) {
                errors.push(t("locations.validationDescriptionMaxLength"));
            }
            break;
        case "address":
            if (value && value.length > 500) {
                errors.push(t("locations.validationAddressMaxLength"));
            }
            break;
        case "city":
        case "state":
        case "country":
            if (value && value.length > 100) {
                errors.push(
                    `${
                        field.charAt(0).toUpperCase() + field.slice(1)
                    } ${t("locations.validationFieldMaxLength")}`
                );
            }
            break;
        case "postal_code":
            if (value && value.length > 20) {
                errors.push(t("locations.validationPostalCodeMaxLength"));
            }
            break;
        case "latitude":
            if (value !== null && value !== "" && (value < -90 || value > 90)) {
                errors.push(t("locations.validationLatitudeRange"));
            }
            break;
        case "longitude":
            if (
                value !== null &&
                value !== "" &&
                (value < -180 || value > 180)
            ) {
                errors.push(t("locations.validationLongitudeRange"));
            }
            break;
        case "color":
            if (value && !/^#[0-9A-Fa-f]{6}$/.test(value)) {
                errors.push(t("locations.validationColorFormat"));
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(locationForm.value).forEach((field) => {
        const fieldErrors = validateField(field, locationForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, locationForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

// Table columns configuration
const columns = computed(() => [
    {
        key: "name",
        label: t("locations.location"),
        sortable: true,
    },
    {
        key: "address",
        label: t("locations.address"),
        sortable: false,
    },
    {
        key: "parent",
        label: t("locations.parent"),
        sortable: false,
    },
    {
        key: "children",
        label: t("locations.children"),
        sortable: false,
    },
    {
        key: "status",
        label: t("products.status"),
        sortable: false,
    },
    {
        key: "created_at",
        label: t("locations.created"),
        type: "date",
        sortable: true,
    },
]);

// Computed properties
const totalLocations = computed(() => locations.value.length);
const activeLocations = computed(
    () => locations.value.filter((l) => l.is_active).length
);
const rootLocations = computed(
    () => locations.value.filter((l) => !l.parent_id).length
);
const newLocations = computed(() => {
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
    return locations.value.filter((l) => new Date(l.created_at) > oneMonthAgo)
        .length;
});

// Methods
const handleRefreshLocations = async () => {
    refreshLoading.value = true;
    try {
        await loadLocations();
        notification.success(t("locations.refreshSuccess"));
    } catch (error) {
        console.error("Refresh locations error:", error);
        notification.error(t("locations.refreshError"));
    } finally {
        refreshLoading.value = false;
    }
};

const saveLocation = async () => {
    if (!validateForm()) {
        notification.error(t("locations.formError"));
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "locations"
            : `locations/${selectedLocation.value.id}`;

        // Prepare form data, converting "null" strings to null
        const formData = { ...locationForm.value };
        if (formData.parent_id === "null" || formData.parent_id === "") {
            formData.parent_id = null;
        }

        const data = showCreateModal.value
            ? await apiPost(url, formData)
            : await apiPut(url, formData);

        if (data.success) {
            notification.success(
                showCreateModal.value
                    ? t("locations.createSuccess")
                    : t("locations.updateSuccess")
            );
            closeModals();
            loadLocations();
            loadParentOptions();
        } else {
            if (data.errors) {
                formErrors.value = data.errors;
                notification.error(t("locations.formCheckError"));
            } else {
                notification.error(data.message || t("locations.saveError"));
            }
        }
    } catch (error) {
        console.error("Save location error:", error);
        notification.error(t("locations.saveError"));
    } finally {
        saving.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    selectedLocation.value = null;
    locationForm.value = {
        name: "",
        code: "",
        description: "",
        address: "",
        city: "",
        state: "",
        country: "",
        postal_code: "",
        latitude: null,
        longitude: null,
        color: "#10B981",
        parent_id: null,
    };
    formErrors.value = {};
    formTouched.value = {};
};

const handleAddLocation = () => {
    showCreateModal.value = true;
    loadParentOptions();
};

const handleEditLocation = (location) => {
    selectedLocation.value = location;
    locationForm.value = {
        name: location.name,
        code: location.code,
        description: location.description || "",
        address: location.address || "",
        city: location.city || "",
        state: location.state || "",
        country: location.country || "",
        postal_code: location.postal_code || "",
        latitude: location.latitude,
        longitude: location.longitude,
        color: location.color,
        parent_id: location.parent_id || null,
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
    loadParentOptions(location);
};

const handleDeleteLocation = (location) => {
    confirmationModal.showModal({
        title: t("locations.deleteLocationTitle"),
        message: `${t("locations.deleteLocationMessage")} "${location.name}"?`,
        description: t("locations.deleteLocationDescription"),
        confirmText: t("locations.deleteLocation"),
        cancelText: t("locations.cancelText"),
        onConfirm: async () => {
            const data = await apiDelete(`locations/${location.id}`);
            if (data.success) {
                notification.success(t("locations.deleteSuccess"));
                loadLocations();
                loadParentOptions();
                return data;
            } else {
                throw new Error(data.message || t("locations.saveError"));
            }
        },
        onSuccess: (result) => {
            console.log("Location deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete location:", error);
            notification.error(error.message || t("locations.saveError"));
        },
    });
};

const handleBulkAction = (selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning(t("locations.bulkActionWarning"));
        return;
    }

    const messageText = selectedItems.length === 1
        ? t("locations.bulkDeleteMessageSingle")
        : t("locations.bulkDeleteMessagePlural");

    confirmationModal.showModal({
        title: t("locations.bulkDeleteTitle"),
        message: `${t("locations.bulkDeleteMessage")} ${selectedItems.length} ${messageText}?`,
        description: t("locations.bulkDeleteDescription"),
        confirmText: t("locations.bulkDeleteConfirm"),
        cancelText: t("locations.cancelText"),
        onConfirm: async () => {
            try {
                const deletePromises = selectedItems.map((item) =>
                    apiDelete(`locations/${item.id}`)
                );
                await Promise.all(deletePromises);
                const successText = selectedItems.length === 1
                    ? t("locations.bulkDeleteSingle")
                    : t("locations.bulkDeletePlural");
                notification.success(
                    `${selectedItems.length} ${successText} ${t("locations.bulkDeleteSuccess")}`
                );
                loadLocations();
                loadParentOptions();
                return { success: true };
            } catch (error) {
                throw new Error(t("locations.bulkDeleteError"));
            }
        },
        onSuccess: (result) => {
            console.log("Bulk delete completed:", result);
        },
        onError: (error) => {
            console.error("Bulk delete failed:", error);
            notification.error(error.message || t("locations.bulkDeleteError"));
        },
    });
};

const handleSelectionChange = (selectedItems) => {
    selectedLocations.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const handleToggleStatus = async (location) => {
    const newStatus = location.is_active ? "inactive" : "active";

    try {
        const data = await apiPost(`locations/${location.id}/toggle-status`);

        if (data.success) {
            const successMessage = newStatus === "active"
                ? t("locations.activateSuccess")
                : t("locations.deactivateSuccess");
            notification.success(successMessage);
            loadLocations();
        } else {
            notification.error(data.message || t("locations.saveError"));
        }
    } catch (error) {
        console.error(`Toggle status error:`, error);
        notification.error(t("locations.saveError"));
    }
};

const loadLocations = async () => {
    try {
        loading.value = true;
        const data = await apiGet("locations");
        if (data.success) {
            locations.value = data.data;
        }
    } catch (error) {
        console.error("Error loading locations:", error);
        notification.error(t("locations.loadError"));
    } finally {
        loading.value = false;
    }
};

const loadParentOptions = async (excludeLocation = null) => {
    try {
        const url = excludeLocation
            ? `locations-parent-options/${excludeLocation.id}`
            : "locations-parent-options";

        const data = await apiGet(url);
        if (data.success) {
            parentOptions.value = [
                { value: null, label: t("locations.noParent") },
                ...data.data.map((location) => ({
                    value: location.id,
                    label: location.full_path,
                })),
            ];
        }
    } catch (error) {
        console.error("Error loading parent options:", error);
        notification.error(t("locations.loadParentOptionsError"));
    }
};

// Lifecycle
onMounted(() => {
    loadLocations();
    loadParentOptions();
});
</script>
