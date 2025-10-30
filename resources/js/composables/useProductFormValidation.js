import { ref, computed, reactive } from "vue";

/**
 * Product Form Validation Composable
 *
 * Provides reactive form validation for Product forms with comprehensive
 * validation rules and error handling.
 */
export function useProductFormValidation() {
    // Reactive form data
    const form = reactive({
        product_code: "",
        product_name: "",
        description: "",
        product_type: "finished_goods",
        category_id: null,
        unit_id: null,
        purchase_price: 0,
        selling_price: 0,
        minimum_stock: 0,
        maximum_stock: 0,
        is_active: true,
    });

    // Reactive errors object
    const errors = ref({});

    // Reactive touched fields object for dirty state tracking
    const touched = ref({});

    // Validation rules configuration
    const validationRules = {
        product_code: {
            required: true,
            maxLength: 50,
            message: "Product code is required",
            requiredMessage: "Product code is required",
            maxLengthMessage: "Product code cannot exceed 50 characters",
        },
        product_name: {
            required: true,
            maxLength: 255,
            message: "Product name is required",
            requiredMessage: "Product name is required",
            maxLengthMessage: "Product name cannot exceed 255 characters",
        },
        description: {
            nullable: true,
            maxLength: 1000,
            message: "Description cannot exceed 1000 characters",
            maxLengthMessage: "Description cannot exceed 1000 characters",
        },
        product_type: {
            required: true,
            in: ["finished_goods", "raw_material", "consumable"],
            message: "Please select a valid product type",
            requiredMessage: "Product type is required",
            inMessage:
                "Product type must be one of: finished goods, raw material, or consumable",
        },
        unit_id: {
            required: true,
            type: "number",
            message: "Unit is required",
            requiredMessage: "Unit is required",
            typeMessage: "Unit must be a valid number",
        },
        purchase_price: {
            required: true,
            type: "number",
            min: 0,
            message: "Purchase price must be a valid number",
            requiredMessage: "Purchase price is required",
            typeMessage: "Purchase price must be a number",
            minMessage: "Purchase price cannot be negative",
        },
        selling_price: {
            nullable: true,
            type: "number",
            min: 0,
            message: "Selling price must be a valid number",
            typeMessage: "Selling price must be a number",
            minMessage: "Selling price cannot be negative",
        },
        minimum_stock: {
            required: true,
            type: "number",
            min: 0,
            message: "Minimum stock must be a valid number",
            requiredMessage: "Minimum stock is required",
            typeMessage: "Minimum stock must be a number",
            minMessage: "Minimum stock cannot be negative",
        },
        maximum_stock: {
            required: true,
            type: "number",
            min: 0,
            message: "Maximum stock must be a valid number",
            requiredMessage: "Maximum stock is required",
            typeMessage: "Maximum stock must be a number",
            minMessage: "Maximum stock cannot be negative",
        },
        is_active: {
            type: "boolean",
            message: "Active status must be true or false",
            typeMessage: "Active status must be true or false",
        },
    };

    /**
     * Validate a single field
     *
     * @param {string} field - Field name to validate
     * @returns {boolean} - True if valid, false otherwise
     */
    const validateField = (field) => {
        const rule = validationRules[field];
        if (!rule) return true;

        const value = form[field];
        const fieldErrors = [];

        // Mark field as touched
        touched.value[field] = true;

        // Required validation
        if (
            rule.required &&
            (value === null || value === undefined || value === "")
        ) {
            fieldErrors.push(rule.requiredMessage || rule.message);
        }

        // Skip other validations if field is empty and not required
        if (
            !rule.required &&
            (value === null || value === undefined || value === "")
        ) {
            if (rule.nullable) {
                delete errors.value[field];
                return true;
            }
        }

        // Type validation
        if (
            rule.type &&
            value !== null &&
            value !== undefined &&
            value !== ""
        ) {
            if (rule.type === "number" && isNaN(Number(value))) {
                fieldErrors.push(rule.typeMessage || rule.message);
            }
            if (rule.type === "boolean" && typeof value !== "boolean") {
                fieldErrors.push(rule.typeMessage || rule.message);
            }
        }

        // Max length validation
        if (
            rule.maxLength &&
            typeof value === "string" &&
            value.length > rule.maxLength
        ) {
            fieldErrors.push(rule.maxLengthMessage || rule.message);
        }

        // In validation (for select fields)
        if (rule.in && !rule.in.includes(value)) {
            fieldErrors.push(rule.inMessage || rule.message);
        }

        // Min validation
        if (
            rule.min !== undefined &&
            value !== null &&
            value !== undefined &&
            value !== ""
        ) {
            const numValue = Number(value);
            if (!isNaN(numValue) && numValue < rule.min) {
                fieldErrors.push(rule.minMessage || rule.message);
            }
        }

        // Update errors object
        if (fieldErrors.length > 0) {
            errors.value[field] = fieldErrors;
            return false;
        } else {
            delete errors.value[field];
            return true;
        }
    };

    /**
     * Validate the entire form
     *
     * @returns {boolean} - True if all fields are valid, false otherwise
     */
    const validateForm = () => {
        let isValid = true;

        // Validate all fields
        Object.keys(validationRules).forEach((field) => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    };

    /**
     * Get error message for a specific field
     *
     * @param {string} field - Field name
     * @returns {string} - First error message or empty string
     */
    const getFieldError = (field) => {
        const fieldErrors = errors.value[field];
        return fieldErrors && fieldErrors.length > 0 ? fieldErrors[0] : "";
    };

    /**
     * Get all error messages for a specific field
     *
     * @param {string} field - Field name
     * @returns {Array} - Array of error messages
     */
    const getFieldErrors = (field) => {
        return errors.value[field] || [];
    };

    /**
     * Check if a field has errors
     *
     * @param {string} field - Field name
     * @returns {boolean} - True if field has errors
     */
    const hasFieldError = (field) => {
        return (
            touched.value[field] &&
            errors.value[field] &&
            errors.value[field].length > 0
        );
    };

    /**
     * Check if the form is valid
     *
     * @returns {boolean} - True if form has no errors
     */
    const isFormValid = computed(() => {
        return Object.keys(errors.value).length === 0;
    });

    /**
     * Check if the form is dirty (has been touched)
     *
     * @returns {boolean} - True if any field has been touched
     */
    const isFormDirty = computed(() => {
        return Object.keys(touched.value).some((field) => touched.value[field]);
    });

    /**
     * Clear all errors
     */
    const clearErrors = () => {
        errors.value = {};
    };

    /**
     * Clear errors for a specific field
     *
     * @param {string} field - Field name
     */
    const clearFieldError = (field) => {
        delete errors.value[field];
    };

    /**
     * Reset form to initial state
     */
    const resetForm = () => {
        Object.assign(form, {
            product_code: "",
            product_name: "",
            description: "",
            product_type: "finished_goods",
            category_id: null,
            unit_id: null,
            purchase_price: 0,
            selling_price: 0,
            minimum_stock: 0,
            maximum_stock: 0,
            is_active: true,
        });
        errors.value = {};
        touched.value = {};
    };

    /**
     * Set form data from an object
     *
     * @param {Object} data - Data to populate form with
     */
    const setFormData = (data) => {
        Object.keys(data).forEach((key) => {
            if (form.hasOwnProperty(key)) {
                form[key] = data[key];
            }
        });
    };

    /**
     * Get form data as plain object
     *
     * @returns {Object} - Form data object
     */
    const getFormData = () => {
        return { ...form };
    };

    /**
     * Mark all fields as touched
     */
    const touchAllFields = () => {
        Object.keys(validationRules).forEach((field) => {
            touched.value[field] = true;
        });
    };

    /**
     * Set server validation errors
     *
     * @param {Object} serverErrors - Server errors object
     */
    const setServerErrors = (serverErrors) => {
        errors.value = { ...serverErrors };
        touchAllFields();
    };

    /**
     * Get available product type options
     *
     * @returns {Array} - Array of product type options
     */
    const getProductTypeOptions = () => {
        return [
            { value: "finished_goods", label: "Finished Goods" },
            { value: "raw_material", label: "Raw Material" },
            { value: "consumable", label: "Consumable" },
        ];
    };

    /**
     * Get available status options
     *
     * @returns {Array} - Array of status options
     */
    const getStatusOptions = () => {
        return [
            { value: true, label: "Active" },
            { value: false, label: "Inactive" },
        ];
    };

    /**
     * Prepare form data for submission
     *
     * @returns {Object} - Formatted form data
     */
    const prepareFormData = () => {
        return {
            ...form,
            purchase_price: parseFloat(form.purchase_price) || 0,
            selling_price: parseFloat(form.selling_price) || 0,
            minimum_stock: parseInt(form.minimum_stock) || 0,
            maximum_stock: parseInt(form.maximum_stock) || 0,
        };
    };

    // Return reactive object with all necessary data and methods
    return {
        // Form data
        form,

        // Error state
        errors,
        touched,

        // Computed properties
        isFormValid,
        isFormDirty,

        // Validation methods
        validateField,
        validateForm,
        getFieldError,
        getFieldErrors,
        hasFieldError,

        // Form management methods
        clearErrors,
        clearFieldError,
        resetForm,
        setFormData,
        getFormData,
        touchAllFields,
        setServerErrors,
        prepareFormData,

        // Utility methods
        getProductTypeOptions,
        getStatusOptions,

        // Validation rules (for external use if needed)
        validationRules,
    };
}
