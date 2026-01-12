<template>
    <Modal
        :is-open="isOpen"
        @close="handleClose"
        size="lg"
    >
        <template #title>
            {{ editingLocation ? $t('locations.editLocationTitle') : $t('locations.createLocationTitle') }}
        </template>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Basic Information -->
            <div class="space-y-5">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white"
                >
                    {{ $t('locations.basicInformation') }}
                </h3>
                <FormInput
                    v-model="formData.name"
                    :label="$t('locations.name')"
                    :placeholder="$t('locations.namePlaceholder')"
                    :error="getFieldError('name')"
                    required
                    @blur="handleFieldBlur('name')"
                />
                <FormInput
                    v-model="formData.code"
                    :label="$t('locations.code')"
                    :placeholder="$t('locations.codePlaceholder')"
                    :error="getFieldError('code')"
                    @blur="handleFieldBlur('code')"
                />
                <FormTextarea
                    v-model="formData.description"
                    :label="$t('locations.description')"
                    :placeholder="$t('locations.descriptionPlaceholder')"
                    :error="getFieldError('description')"
                    rows="3"
                    @blur="handleFieldBlur('description')"
                />
                <div class="grid grid-cols-2 gap-4">
                    <FormInput
                        v-model="formData.color"
                        :label="$t('locations.color')"
                        type="color"
                        :error="getFieldError('color')"
                        @blur="handleFieldBlur('color')"
                    />
                    <FormSelect
                        v-model="formData.parent_id"
                        :label="$t('locations.parentLocation')"
                        :options="parentOptions"
                        :placeholder="$t('locations.selectParentPlaceholder')"
                        :error="getFieldError('parent_id')"
                        @blur="handleFieldBlur('parent_id')"
                    />
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
                    v-model="formData.address"
                    :label="$t('locations.addressLabel')"
                    :placeholder="$t('locations.addressPlaceholder')"
                    :error="getFieldError('address')"
                    rows="2"
                    @blur="handleFieldBlur('address')"
                />
                <div class="grid grid-cols-2 gap-4">
                    <FormInput
                        v-model="formData.city"
                        :label="$t('locations.city')"
                        :placeholder="$t('locations.cityPlaceholder')"
                        :error="getFieldError('city')"
                        @blur="handleFieldBlur('city')"
                    />
                    <FormInput
                        v-model="formData.state"
                        :label="$t('locations.state')"
                        :placeholder="$t('locations.statePlaceholder')"
                        :error="getFieldError('state')"
                        @blur="handleFieldBlur('state')"
                    />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <FormInput
                        v-model="formData.country"
                        :label="$t('locations.country')"
                        :placeholder="$t('locations.countryPlaceholder')"
                        :error="getFieldError('country')"
                        @blur="handleFieldBlur('country')"
                    />
                    <FormInput
                        v-model="formData.postal_code"
                        :label="$t('locations.postalCode')"
                        :placeholder="$t('locations.postalCodePlaceholder')"
                        :error="getFieldError('postal_code')"
                        @blur="handleFieldBlur('postal_code')"
                    />
                </div>
                <div class="grid grid-cols-2 gap-4 hidden">
                    <FormInput
                        v-model="formData.latitude"
                        :label="$t('locations.latitude')"
                        :placeholder="$t('locations.latitudePlaceholder')"
                        type="number"
                        step="any"
                        :error="getFieldError('latitude')"
                        @blur="handleFieldBlur('latitude')"
                    />
                    <FormInput
                        v-model="formData.longitude"
                        :label="$t('locations.longitude')"
                        :placeholder="$t('locations.longitudePlaceholder')"
                        type="number"
                        step="any"
                        :error="getFieldError('longitude')"
                        @blur="handleFieldBlur('longitude')"
                    />
                </div>
            </div>

            <div
                class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0"
            >
                <Button
                    @click="handleClose"
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
                        editingLocation
                            ? $t('locations.update')
                            : $t('locations.create')
                    }}
                </Button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import Modal from "../Overlays/Modal.vue";
import FormInput from "../Forms/FormInput.vue";
import FormSelect from "../Forms/FormSelect.vue";
import FormTextarea from "../Forms/FormTextarea.vue";
import Button from "../Base/Button.vue";

const { t } = useI18n();

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    editingLocation: {
        type: Object,
        default: null,
    },
    parentOptions: {
        type: Array,
        default: () => [],
    },
    saving: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "saved"]);

const formData = ref({
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

const formErrors = ref({});
const formTouched = ref({});

const resetForm = () => {
    formData.value = {
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
};

// Watch for editing location changes
watch(
    () => props.editingLocation,
    (newLocation) => {
        if (newLocation) {
            formData.value = {
                name: newLocation.name,
                code: newLocation.code,
                description: newLocation.description || "",
                address: newLocation.address || "",
                city: newLocation.city || "",
                state: newLocation.state || "",
                country: newLocation.country || "",
                postal_code: newLocation.postal_code || "",
                latitude: newLocation.latitude,
                longitude: newLocation.longitude,
                color: newLocation.color,
                parent_id: newLocation.parent_id || null,
            };
        } else {
            resetForm();
        }
        formErrors.value = {};
        formTouched.value = {};
    },
    { immediate: true }
);

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

    Object.keys(formData.value).forEach((field) => {
        const fieldErrors = validateField(field, formData.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, formData.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

const handleSubmit = async () => {
    if (!validateForm()) {
        return;
    }

    // Prepare form data, converting "null" strings to null
    const data = { ...formData.value };
    if (data.parent_id === "null" || data.parent_id === "") {
        data.parent_id = null;
    }

    emit("saved", {
        formData: data,
        isEditing: !!props.editingLocation,
        locationId: props.editingLocation?.id,
    });
};

const handleClose = () => {
    emit("close");
};
</script>
