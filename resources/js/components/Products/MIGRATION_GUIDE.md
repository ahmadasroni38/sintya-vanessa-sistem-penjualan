# ProductFormModal Migration Guide

This guide helps you migrate from the old ProductFormModal to the new refactored version.

## Overview of Changes

The ProductFormModal has been completely refactored to improve maintainability, reusability, and code organization. The main changes include:

1. **Component Decomposition**: Split into smaller, focused components
2. **Composable Extraction**: Form logic moved to a dedicated composable
3. **Type Safety**: Added TypeScript interfaces
4. **Improved Validation**: Enhanced validation system with better error handling
5. **Better Documentation**: Comprehensive documentation and testing

## Breaking Changes

### Props
No breaking changes in props - all existing props are maintained.

### Emits
- New emit added: `unit-added` - fired when a new unit is added through the form

### Internal Structure
The internal structure has changed significantly, but the external API remains the same.

## Migration Steps

### 1. Update Imports
If you were importing the component directly:

```javascript
// Before
import ProductFormModal from '@/components/Products/ProductFormModal.vue';

// After (no change needed)
import ProductFormModal from '@/components/Products/ProductFormModal.vue';
```

### 2. Update Event Handlers
If you were handling form submission:

```javascript
// Before
const handleSaved = (data) => {
  const { formData, isEditing, productId } = data;
  // Handle submission
};

// After (no change needed)
const handleSaved = (data) => {
  const { formData, isEditing, productId } = data;
  // Handle submission - formData is now properly typed
};
```

### 3. Add New Event Handlers (Optional)
If you want to handle new unit or product type additions:

```javascript
// Add this to your parent component
const handleUnitAdded = (newUnit) => {
  // Update your unit options
  unitOptions.value.push({
    value: newUnit.id,
    label: newUnit.display_name || newUnit.name,
  });
};

const handleProductTypeAdded = (newProductType) => {
  // Update your product type options
  productTypeOptions.value.push({
    value: newProductType.value || newProductType.id,
    label: newProductType.name || newProductType.label,
  });
};

<!-- Add to template -->
<ProductFormModal
  @unit-added="handleUnitAdded"
  @product-type-added="handleProductTypeAdded"
  ...otherProps
/>
```

### 4. API Endpoints
The refactored component now includes API endpoints for adding new items:
- `POST /api/units` - Add new unit (code is auto-generated if empty)
- `POST /api/product-types` - Add new product type

These endpoints are automatically handled by the EnhancedFormSelect components.

**Unit Code Auto-Generation:**
When adding a new unit through the form, the code field is optional. If left empty, it will be automatically generated with format `UNT001`, `UNT002`, etc.

## New Features

### Enhanced Validation
- Field-level validation on blur
- Better error messages
- Server error handling
- Form dirty state tracking

### Improved Code Generation
- Better loading states
- Error handling for code generation
- Integration with validation system

### Better Accessibility
- Proper ARIA labels
- Keyboard navigation
- Screen reader compatibility

## Testing

### Manual Testing
Run through the manual testing checklist in `ProductForm.test.js`:

1. **Form Rendering**
   - [ ] Modal opens and closes correctly
   - [ ] All form fields are displayed
   - [ ] Form sections are properly separated
   - [ ] Buttons are in correct positions

2. **Form Validation**
   - [ ] Required fields show errors when empty
   - [ ] Invalid data shows appropriate error messages
   - [ ] Valid data clears error messages
   - [ ] Form submission is blocked with invalid data

3. **Form Functionality**
   - [ ] Product code generation works
   - [ ] New product types can be added
   - [ ] New units can be added
   - [ ] Form data is correctly formatted on submission

### Automated Testing
If you have Jest set up, run the test suite:

```bash
npm test ProductForm.test.js
```

## Performance Considerations

The refactored component is designed to be more performant:

1. **Reduced Bundle Size**: Components are split and can be lazy-loaded
2. **Better Reactivity**: Focused updates prevent unnecessary re-renders
3. **Memory Management**: Proper cleanup of form state and event listeners

## Troubleshooting

### Common Issues

1. **Form Not Validating**
   - Ensure you're using the new validation composable
   - Check that field names match the validation rules

2. **Unit Options Not Updating**
   - Make sure to handle the `unit-added` event
   - Verify the unit options are reactive

3. **Type Errors**
   - If using TypeScript, ensure you're importing the types
   - Check that your data matches the interface definitions

### Debug Mode
To enable debug mode for validation:

```javascript
const { form, errors, validateField } = useProductFormValidation();

// Add this to your component for debugging
window.debugProductForm = {
  form,
  errors,
  validateField,
};
```

Then in browser console:
```javascript
// Check form state
debugProductForm.form

// Validate specific field
debugProductForm.validateField('product_name')

// Check errors
debugProductForm.errors
```

## Future Enhancements

The new architecture makes it easier to add:

1. **Auto-save functionality**
2. **Form state persistence**
3. **Advanced validation rules**
4. **Multi-step forms**
5. **Form templates**

## Support

If you encounter issues during migration:

1. Check the documentation in `README.md`
2. Review the test file for expected behavior
3. Compare with the original component if needed
4. Check the TypeScript interfaces for expected data structures

## Rollback Plan

If you need to rollback quickly:

1. Keep a backup of the original component
2. Revert the import statements
3. Remove the new composable and component files
4. Restore the original event handlers

The refactored version maintains API compatibility, so rollback should be straightforward if needed.
