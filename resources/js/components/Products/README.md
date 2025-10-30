# Product Components Documentation

This directory contains the refactored product-related components with improved maintainability and code organization.

## Component Structure

### Core Components

#### ProductFormModal.vue
The main modal component that orchestrates the product form. It uses composition API and follows a clean architecture pattern.

**Key Features:**
- Uses `useProductFormValidation` composable for form management
- Composed of smaller, focused components
- Clean separation of concerns
- Reactive form validation

**Props:**
- `isOpen` (Boolean): Controls modal visibility
- `editingProduct` (Object): Product data for edit mode
- `unitOptions` (Array): Available unit options

**Emits:**
- `close`: Fired when modal should close
- `saved`: Fired when form is submitted with valid data
- `unit-added`: Fired when a new unit is added

### Form Section Components

#### ProductFormBasicInfo.vue
Handles basic product information fields (code, name, description).

**Features:**
- Product code generation
- Form validation integration
- Disabled state handling for edit mode

#### ProductFormDetails.vue
Manages detailed product information (type, unit, prices, stock levels).

**Features:**
- Enhanced select with add new item functionality for product types and units
- Numeric input validation
- Status selection

#### ProductFormActions.vue
Contains form action buttons (Save/Update, Cancel).

**Features:**
- Loading states
- Dynamic button text based on mode
- Disabled state management

## Composables

### useProductFormValidation.js
A comprehensive form validation composable that provides:

**State Management:**
- Reactive form data
- Error tracking
- Field touched state

**Validation Features:**
- Field-level validation
- Form-wide validation
- Custom validation rules
- Server error handling

**Utility Methods:**
- Form reset and population
- Data preparation for submission
- Error clearing and management

**Validation Rules:**
- Required field validation
- Type validation (number, boolean)
- Length constraints
- Value range validation
- Pattern matching

## Usage Example

```vue
<template>
  <ProductFormModal
    :is-open="isModalOpen"
    :editing-product="currentProduct"
    :unit-options="unitOptions"
    @close="handleModalClose"
    @saved="handleFormSubmit"
    @unit-added="handleUnitAdded"
  />
</template>

<script setup>
import { ref } from 'vue';
import ProductFormModal from './Products/ProductFormModal.vue';

const isModalOpen = ref(false);
const currentProduct = ref(null);
const unitOptions = ref([]);

const handleModalClose = () => {
  isModalOpen.value = false;
  currentProduct.value = null;
};

const handleFormSubmit = async ({ formData, isEditing, productId }) => {
  try {
    if (isEditing) {
      await updateProduct(productId, formData);
    } else {
      await createProduct(formData);
    }
    handleModalClose();
    // Refresh product list
  } catch (error) {
    // Error handling is managed by the component
  }
};

const handleUnitAdded = (newUnit) => {
  unitOptions.value.push({
    value: newUnit.id,
    label: newUnit.display_name || newUnit.name,
  });
};
</script>
```

## Benefits of the Refactored Structure

1. **Maintainability**: Each component has a single responsibility
2. **Reusability**: Form sections can be used in other contexts
3. **Testability**: Smaller components are easier to unit test
4. **Type Safety**: Clear prop and emit definitions
5. **Performance**: Better reactivity with focused updates
6. **Developer Experience**: Cleaner code with better separation of concerns

## Validation Rules

The form validation includes comprehensive rules for all product fields:

- **Product Code**: Required, max 50 characters
- **Product Name**: Required, max 255 characters
- **Description**: Optional, max 1000 characters
- **Product Type**: Required, must be in predefined list
- **Unit**: Required, must be a valid number
- **Purchase Price**: Required, non-negative number
- **Selling Price**: Optional, non-negative number
- **Minimum/Maximum Stock**: Required, non-negative numbers
- **Active Status**: Required, boolean value

## Integration Points

The components integrate with:
- **Notification Store**: For user feedback
- **Products Composable**: For API calls
- **API Utils**: For HTTP requests
- **Form Components**: Reusable form inputs

## API Integration

### Unit Management
- **GET /api/units**: Retrieve active units for dropdown selection
- **POST /api/units**: Create new unit with auto-generated code
  - Code format: `UNT001`, `UNT002`, etc.
  - Code is optional in request - auto-generated if empty

### Product Type Management
- **GET /api/product-types**: Retrieve available product types
- **POST /api/product-types**: Create new product type

## Future Enhancements

Potential improvements:
1. TypeScript interfaces for better type safety
2. Form field auto-save functionality
3. Advanced validation rules (cross-field validation)
4. Form state persistence
5. Accessibility improvements
6. Internationalization support
