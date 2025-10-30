import { ref, computed, reactive } from "vue";

/**
 * Chart of Account Form Validation Composable
 *
 * Provides reactive form validation for Chart of Account forms with comprehensive
 * validation rules and error handling.
 */
export function useChartOfAccountValidation() {
    // Reactive form data
    const form = reactive({
        account_code: "",
        account_name: "",
        account_type: "",
        normal_balance: "",
        parent_id: null,
        level: 1,
        description: "",
        opening_balance: 0,
        is_active: true,
    });

    // Reactive errors object
    const errors = ref({});

    // Reactive touched fields object for dirty state tracking
    const touched = ref({});

    // Validation rules configuration
    const validationRules = {
        account_code: {
            required: true,
            maxLength: 20,
            pattern: /^[0-9]{1,5}-[0-9]{1,5}$/,
            message:
                "Account code must be in format: 1-1010 (e.g., 1-1010, 2-2001)",
            requiredMessage: "Account code is required",
            maxLengthMessage: "Account code cannot exceed 20 characters",
            patternMessage: "Account code must be in format: 1-1010",
        },
        account_name: {
            required: true,
            maxLength: 255,
            message: "Account name is required",
            requiredMessage: "Account name is required",
            maxLengthMessage: "Account name cannot exceed 255 characters",
        },
        account_type: {
            required: true,
            in: ["asset", "liability", "equity", "revenue", "expense"],
            message: "Please select a valid account type",
            requiredMessage: "Account type is required",
            inMessage:
                "Account type must be one of: asset, liability, equity, revenue, or expense",
        },
        normal_balance: {
            required: true,
            in: ["debit", "credit"],
            message: "Please select normal balance",
            requiredMessage: "Normal balance is required",
            inMessage: "Normal balance must be either debit or credit",
        },
        level: {
            required: true,
            min: 1,
            max: 5,
            type: "number",
            message: "Level must be between 1 and 5",
            requiredMessage: "Level is required",
            minMessage: "Level must be at least 1",
            maxMessage: "Level cannot exceed 5",
            typeMessage: "Level must be a number",
        },
        parent_id: {
            nullable: true,
            type: "number",
            message: "Parent account must be a valid account",
            typeMessage: "Parent account must be a valid number",
        },
        description: {
            nullable: true,
            maxLength: 1000,
            message: "Description cannot exceed 1000 characters",
            maxLengthMessage: "Description cannot exceed 1000 characters",
        },
        opening_balance: {
            nullable: true,
            type: "number",
            min: 0,
            message: "Opening balance must be a valid number",
            typeMessage: "Opening balance must be a number",
            minMessage: "Opening balance cannot be negative",
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

        // Pattern validation
        if (
            rule.pattern &&
            typeof value === "string" &&
            !rule.pattern.test(value)
        ) {
            fieldErrors.push(rule.patternMessage || rule.message);
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

        // Max validation
        if (
            rule.max !== undefined &&
            value !== null &&
            value !== undefined &&
            value !== ""
        ) {
            const numValue = Number(value);
            if (!isNaN(numValue) && numValue > rule.max) {
                fieldErrors.push(rule.maxMessage || rule.message);
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
            account_code: "",
            account_name: "",
            account_type: "",
            normal_balance: "",
            parent_id: null,
            level: 1,
            description: "",
            opening_balance: 0,
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
     * Get available account types
     *
     * @returns {Array} - Array of account type options
     */
    const getAccountTypes = () => {
        return [
            { value: "asset", label: "Asset" },
            { value: "liability", label: "Liability" },
            { value: "equity", label: "Equity" },
            { value: "revenue", label: "Revenue" },
            { value: "expense", label: "Expense" },
        ];
    };

    /**
     * Get available normal balance options
     *
     * @returns {Array} - Array of normal balance options
     */
    const getNormalBalances = () => {
        return [
            { value: "debit", label: "Debit" },
            { value: "credit", label: "Credit" },
        ];
    };

    /**
     * Get available level options
     *
     * @returns {Array} - Array of level options
     */
    const getLevelOptions = () => {
        return [
            { value: 1, label: "Level 1 (Top Level)" },
            { value: 2, label: "Level 2" },
            { value: 3, label: "Level 3" },
            { value: 4, label: "Level 4" },
            { value: 5, label: "Level 5 (Lowest)" },
        ];
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

        // Utility methods
        getAccountTypes,
        getNormalBalances,
        getLevelOptions,

        // Validation rules (for external use if needed)
        validationRules,
    };
}
