# Stock Opname Feature - Comprehensive Generation Prompt

## 📋 Overview
Generate a complete **Stock Opname (Physical Inventory Count)** feature following the exact architectural patterns, code quality standards, and component structure established in the **Stock Adjustment** module.

---

## 🎯 Feature Requirements

### Functional Requirements

#### 1. **Master-Detail Pattern**
- **Master**: `stock_opnames` table
  - `opname_number`: Auto-generated (e.g., `OPN-2025-00001`)
  - `opname_date`: Date of stock opname
  - `location_id`: Foreign key to locations
  - `description`: Overall description
  - `notes`: Additional notes
  - `status`: Enum (`draft`, `in_progress`, `completed`, `cancelled`)
  - `total_items`: Count of products being counted
  - `created_by`: User who created
  - `completed_by`: User who completed
  - `completed_at`: Completion timestamp
  - `timestamps`, `softDeletes`

- **Detail**: `stock_opname_details` table
  - `stock_opname_id`: Foreign key to stock_opnames
  - `product_id`: Foreign key to products
  - `system_quantity`: Quantity from system (stock card)
  - `physical_quantity`: Actual counted quantity
  - `difference_quantity`: Auto-calculated (physical - system)
  - `adjustment_type`: Auto-calculated (`increase` / `decrease`)
  - `notes`: Per-item notes
  - `counted_by`: User who counted this item (optional)
  - `timestamps`

#### 2. **Status Workflow**
```
draft → in_progress → completed
           ↓
       cancelled
```

- **Draft**: Initial state, can edit/delete
- **In Progress**: Counting process started, can still edit items
- **Completed**: Finalized, creates adjustment automatically
- **Cancelled**: Cancelled opname, no adjustment created

#### 3. **Core Operations**

**CRUD Operations**:
- ✅ Create new stock opname with multiple products
- ✅ Read/View opname list with filters
- ✅ Update draft/in-progress opname
- ✅ Delete draft opname only
- ✅ View detailed opname information

**Status Actions**:
- **Start Counting**: `draft` → `in_progress`
- **Complete Opname**: `in_progress` → `completed`
  - Auto-creates Stock Adjustment if differences exist
  - Creates stock cards via adjustment
- **Cancel Opname**: Any status → `cancelled`

**Additional Features**:
- Bulk operations (delete drafts, complete multiple)
- Export to CSV (master-detail format)
- Statistics dashboard
- System quantity auto-fetch from stock cards

#### 4. **Auto-Adjustment Creation**
When completing stock opname:
- If differences exist, automatically create a Stock Adjustment
- Adjustment should include:
  - Reference to opname (description: "From Stock Opname: OPN-2025-00001")
  - All products with differences
  - System qty, actual qty (from physical count), differences
  - Status: `draft` or directly `posted` (configurable)

---

## 🗄️ Database Layer

### Migration File
**Path**: `database/migrations/YYYY_MM_DD_HHMMSS_create_stock_opnames_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master table
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique();
            $table->date('opname_date');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->integer('total_items')->default(0);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['opname_date', 'status']);
            $table->index('location_id');
        });

        // Detail table
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->decimal('system_quantity', 15, 2)->default(0);
            $table->decimal('physical_quantity', 15, 2)->default(0);
            $table->decimal('difference_quantity', 15, 2)->default(0);
            $table->enum('adjustment_type', ['increase', 'decrease'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('counted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['stock_opname_id', 'product_id']);
            $table->unique(['stock_opname_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
        Schema::dropIfExists('stock_opnames');
    }
};
```

---

## 🏗️ Backend Layer (Laravel)

### 1. Models

#### **StockOpname Model**
**Path**: `app/Models/StockOpname.php`

**Requirements**:
- Use `HasFactory`, `SoftDeletes` traits
- Fillable fields: opname_number, opname_date, location_id, total_items, description, notes, status, created_by, completed_by, completed_at
- Casts: opname_date (date), total_items (integer), completed_at (datetime)
- Relationships:
  - `details()` → hasMany StockOpnameDetail with eager load product
  - `location()` → belongsTo Location
  - `creator()` → belongsTo User (created_by)
  - `completer()` → belongsTo User (completed_by)
- Scopes:
  - `byStatus($status)`
  - `byLocation($locationId)`
  - `byDateRange($startDate, $endDate)`
- Methods:
  - `startCounting($userId)`: draft → in_progress
  - `complete($userId)`: in_progress → completed, create adjustment
  - `cancel()`: any → cancelled
  - `createAdjustmentFromOpname()`: Create stock adjustment with differences
- Boot method: Auto-generate opname_number (OPN-YYYY-00001 format)

#### **StockOpnameDetail Model**
**Path**: `app/Models/StockOpnameDetail.php`

**Requirements**:
- Use `HasFactory` trait
- Fillable: stock_opname_id, product_id, system_quantity, physical_quantity, difference_quantity, adjustment_type, notes, counted_by
- Casts: quantities as decimal:2
- Relationships:
  - `stockOpname()` → belongsTo StockOpname
  - `product()` → belongsTo Product
  - `counter()` → belongsTo User (counted_by)
- Boot method: Auto-calculate difference_quantity and adjustment_type on saving

---

### 2. Controller

#### **StockOpnameController**
**Path**: `app/Http/Controllers/StockOpnameController.php`

**Methods Required**:

```php
// CRUD Operations
public function index(Request $request)
// Filters: status, location_id, product_id, start_date, end_date, search
// Returns: paginated list with eager loaded relations
// Supports both JSON (API) and Inertia responses

public function store(Request $request)
// Validation: opname_date, location_id, description, notes, details array
// Details validation: product_id, system_quantity, physical_quantity, notes
// DB transaction to create master + details
// Auto-calculate total_items, difference_quantity, adjustment_type

public function show(Request $request, StockOpname $stockOpname)
// Load all relations: location, creator, completer, details.product
// Return JSON or Inertia

public function update(Request $request, StockOpname $stockOpname)
// Only draft/in_progress can be updated
// Same validation as store
// Delete old details, create new ones in transaction

public function destroy(Request $request, StockOpname $stockOpname)
// Only draft can be deleted
// Cascade delete details

// Status Actions
public function startCounting(Request $request, StockOpname $stockOpname)
// Change status: draft → in_progress

public function complete(Request $request, StockOpname $stockOpname)
// Change status: in_progress → completed
// Call createAdjustmentFromOpname()
// Return created adjustment info

public function cancel(Request $request, StockOpname $stockOpname)
// Change status to cancelled (any status)

// Helper Methods
public function calculateSystemQuantity(Request $request)
// Input: product_id, location_id
// Output: system_quantity from stock cards

public function statistics(Request $request)
// Return: total_this_month, draft, in_progress, completed, items_counted

// Bulk Operations
public function bulkComplete(Request $request)
// Complete multiple opnames at once

public function bulkDelete(Request $request)
// Delete multiple draft opnames

public function export(Request $request)
// Export to CSV (master-detail format)
```

**Code Quality Standards**:
- Use DB transactions for all multi-table operations
- Consistent error handling with try-catch
- Return both JSON (API) and Inertia responses based on `expectsJson()`
- Proper validation messages
- Eager load relationships to avoid N+1 queries
- Follow exact pattern from StockAdjustmentController

---

### 3. API Routes
**Path**: `routes/api.php`

```php
// Stock Opname routes
Route::prefix('stock-opnames')->group(function () {
    Route::get('/', [StockOpnameController::class, 'index']);
    Route::post('/', [StockOpnameController::class, 'store']);
    Route::get('/statistics', [StockOpnameController::class, 'statistics']);
    Route::post('/calculate-system-quantity', [StockOpnameController::class, 'calculateSystemQuantity']);
    Route::get('/{stockOpname}', [StockOpnameController::class, 'show']);
    Route::put('/{stockOpname}', [StockOpnameController::class, 'update']);
    Route::delete('/{stockOpname}', [StockOpnameController::class, 'destroy']);

    // Status actions
    Route::post('/{stockOpname}/start', [StockOpnameController::class, 'startCounting']);
    Route::post('/{stockOpname}/complete', [StockOpnameController::class, 'complete']);
    Route::post('/{stockOpname}/cancel', [StockOpnameController::class, 'cancel']);

    // Bulk operations
    Route::post('/bulk/complete', [StockOpnameController::class, 'bulkComplete']);
    Route::post('/bulk/delete', [StockOpnameController::class, 'bulkDelete']);

    // Export
    Route::get('/export/csv', [StockOpnameController::class, 'export']);
});
```

---

## 🎨 Frontend Layer (Vue 3 + Composition API)

### Architecture Pattern
Follow exact structure from Adjustment module:
- **Main Page**: Handles state, data loading, business logic
- **Reusable Components**: Isolated, props-driven, emit events
- **Services**: API calls abstraction
- **Utilities**: Shared helpers

---

### 1. Service Layer

#### **warehouseService.js**
**Path**: `resources/js/services/warehouseService.js`

Add to existing file:

```javascript
// Stock Opname Service
export const stockOpnameService = {
    getAll: (params = {}) => api.get('/stock-opnames', { params }),
    getById: (id) => api.get(`/stock-opnames/${id}`),
    create: (data) => api.post('/stock-opnames', data),
    update: (id, data) => api.put(`/stock-opnames/${id}`, data),
    delete: (id) => api.delete(`/stock-opnames/${id}`),

    // Status actions
    startCounting: (id) => api.post(`/stock-opnames/${id}/start`),
    complete: (id) => api.post(`/stock-opnames/${id}/complete`),
    cancel: (id) => api.post(`/stock-opnames/${id}/cancel`),

    // Helpers
    calculateSystemQuantity: (productId, locationId) =>
        api.post('/stock-opnames/calculate-system-quantity', { product_id: productId, location_id: locationId }),

    statistics: (params = {}) => api.get('/stock-opnames/statistics', { params }),

    // Bulk operations
    bulkComplete: (ids) => api.post('/stock-opnames/bulk/complete', { ids }),
    bulkDelete: (ids) => api.post('/stock-opnames/bulk/delete', { ids }),

    // Export
    export: (params = {}) => api.get('/stock-opnames/export/csv', {
        params,
        responseType: 'blob'
    }),
};
```

---

### 2. Main Page Component

#### **StockOpname.vue**
**Path**: `resources/js/views/Dashboard/Warehouse/StockOpname.vue`

**Requirements**:

**Template Structure**:
```vue
<template>
    <div class="space-y-6">
        <!-- Header with Export & New Opname buttons -->
        <PageHeader
            title="Stock Opname"
            description="Physical inventory counting and verification"
        >
            <template #actions>
                <!-- Export button -->
                <!-- New Opname button -->
            </template>
        </PageHeader>

        <!-- Stats Cards -->
        <OpnameStats :opnames="opnameList" />

        <!-- Opnames Table -->
        <DataTable
            title="Stock Opnames"
            :data="opnameList"
            :columns="columns"
            :loading="loading"
            @refresh="loadOpnames"
            :show-refresh="true"
            :refresh-loading="refreshing"
            search-placeholder="Search opnames..."
            :showAddButton="false"
            :showExport="false"
            :showFilters="false"
            :server-side-pagination="true"
        >
            <!-- Custom column templates -->
            <template #column-total_items="{ value }">
                <!-- Badge with item count -->
            </template>

            <template #column-status="{ value }">
                <!-- Status badge with color -->
            </template>

            <!-- Action buttons -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <!-- View Details button (all statuses) -->
                    <!-- Edit button (draft/in_progress only) -->
                    <!-- Start Counting (draft only) -->
                    <!-- Complete (in_progress only) -->
                    <!-- Cancel button (draft/in_progress only) -->
                    <!-- Delete button (draft only) -->
                </div>
            </template>
        </DataTable>

        <!-- Add/Edit Opname Modal -->
        <OpnameFormModal
            :show="showAddModal || !!editingOpname"
            :opname="editingOpname"
            :products="products"
            :locations="locations"
            :saving="saving"
            @close="closeModal"
            @submit="saveOpname"
            @get-system-quantity="handleGetSystemQuantity"
        />

        <!-- Details Modal -->
        <OpnameDetails
            :show="showDetailsModal"
            :opname="selectedOpname"
            @close="showDetailsModal = false"
        />

        <!-- Confirmation Modals -->
        <!-- Start Counting Confirmation -->
        <!-- Complete Confirmation -->
        <!-- Cancel Confirmation -->
        <!-- Delete Confirmation -->
    </div>
</template>
```

**Script Setup**:
- State management for all modals and operations
- Load opnames, products, locations on mount
- CRUD operations handlers
- Status action handlers (start, complete, cancel)
- Export handler
- Status badge color helper
- Column definitions matching backend structure

**Columns**:
```javascript
const columns = [
    { key: 'opname_number', label: 'Number', sortable: true },
    { key: 'opname_date', label: 'Date', sortable: true, type: 'date' },
    { key: 'location.name', label: 'Location', sortable: true },
    { key: 'total_items', label: 'Total Items', sortable: true },
    { key: 'description', label: 'Description', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
];
```

---

### 3. Reusable Components

#### **OpnameFormModal.vue**
**Path**: `resources/js/components/Warehouse/OpnameFormModal.vue`

**Requirements**:
- Follow exact pattern from `AdjustmentFormModal.vue`
- Modal with size="4xl"
- Two sections: Master Info + Product Details

**Master Section Fields**:
- Opname Date (date input)
- Location (select dropdown)
- Description (textarea)
- Notes (textarea)

**Product Details Section**:
- Table with columns:
  - Product (select dropdown with search)
  - System Qty (readonly, auto-fetched)
  - Physical Qty (editable input)
  - Difference (auto-calculated, colored badge)
  - Type (increase/decrease badge)
  - Notes (text input)
  - Action (remove button)
- "Add Product" button above table
- Product summary at bottom

**Features**:
- Add/remove product rows
- Auto-fetch system quantity on product select
- Auto-calculate difference and type
- Prevent duplicate products
- Form validation
- Edit mode support (populate existing data)
- Disabled system quantity field (readonly)

**Props**: show, opname, products, locations, saving
**Emits**: close, submit, get-system-quantity

---

#### **OpnameDetails.vue**
**Path**: `resources/js/components/Warehouse/OpnameDetails.vue`

**Requirements**:
- Modal size="4xl"
- Display master information (number, date, location, status, description, notes)
- Display creator and completer info
- Table of product details with:
  - Product code & name
  - System quantity
  - Physical quantity
  - Difference (colored)
  - Type badge
  - Notes
- Summary section:
  - Total products
  - Total increases
  - Total decreases
  - Status badge
- Close button

**Props**: show, opname
**Emits**: close

---

#### **OpnameStats.vue**
**Path**: `resources/js/components/Warehouse/OpnameStats.vue`

**Requirements**:
- Display 5 stat cards using `StatCard` component:
  1. **Total This Month** (calendar icon, blue)
  2. **Draft** (document icon, yellow)
  3. **In Progress** (clock icon, blue)
  4. **Completed** (check icon, green)
  5. **Items Counted** (cube icon, purple)

- Calculate stats from opnames prop
- Responsive grid layout (1 col mobile, 2 col tablet, 5 col desktop)

**Props**: opnames (Array)

---

#### **StatCard.vue** (Reuse existing)
**Path**: `resources/js/components/Warehouse/StatCard.vue`

Already exists, should work with OpnameStats.

---

### 4. Component File Structure
```
resources/js/
├── views/Dashboard/Warehouse/
│   └── StockOpname.vue                 # Main page
├── components/Warehouse/
│   ├── OpnameFormModal.vue             # Add/Edit form
│   ├── OpnameDetails.vue               # Detail view modal
│   ├── OpnameStats.vue                 # Statistics cards
│   ├── PageHeader.vue                  # Reuse existing
│   └── StatCard.vue                    # Reuse existing
├── services/
│   └── warehouseService.js             # Add stockOpnameService
└── stores/
    └── notification.js                 # Reuse existing
```

---

## 🎨 UI/UX Requirements

### Status Badge Colors
```javascript
const statusColors = {
    draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
};
```

### Action Button Visibility
| Status | View | Edit | Start | Complete | Cancel | Delete |
|--------|------|------|-------|----------|--------|--------|
| draft | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ |
| in_progress | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| completed | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| cancelled | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |

### Responsive Design
- Mobile-first approach
- Breakpoints: sm (640px), md (768px), lg (1024px), xl (1280px)
- Stack forms vertically on mobile
- Horizontal scrolling for tables on mobile

### Dark Mode Support
- All components must support dark mode
- Use Tailwind dark: prefix classes
- Test both light and dark themes

---

## ✅ Code Quality Standards

### General Principles
1. **DRY (Don't Repeat Yourself)**: Reuse components, utilities, and services
2. **Single Responsibility**: Each component/method does one thing well
3. **Clean Code**: Meaningful names, proper indentation, comments for complex logic
4. **Error Handling**: Try-catch blocks, user-friendly error messages
5. **Performance**: Lazy loading, eager loading relationships, pagination
6. **Security**: Input validation, SQL injection prevention, CSRF protection

### Backend (Laravel)
- ✅ Use Eloquent ORM (no raw queries unless necessary)
- ✅ DB transactions for multi-table operations
- ✅ Request validation with proper rules
- ✅ Resource controllers with RESTful conventions
- ✅ Soft deletes for audit trail
- ✅ Eloquent relationships over joins
- ✅ Service layer for complex business logic
- ✅ Consistent API response format
- ✅ Error logging

### Frontend (Vue 3)
- ✅ Composition API with `<script setup>`
- ✅ Reactive state with `ref` / `reactive`
- ✅ Computed properties for derived state
- ✅ Props validation with types
- ✅ Event emitters for parent-child communication
- ✅ Async/await for API calls
- ✅ Loading states and error handling
- ✅ Debouncing for search inputs
- ✅ Modals for forms and confirmations
- ✅ Notifications for success/error feedback

### Component Architecture
- ✅ **Container/Presentational Pattern**:
  - Container (Main page): Business logic, state, API calls
  - Presentational (Components): UI rendering, props, events
- ✅ **Single File Components (SFC)**
- ✅ **Props down, events up**
- ✅ **Reusable, composable components**

### Naming Conventions
- **Backend**:
  - Models: PascalCase (StockOpname, StockOpnameDetail)
  - Controllers: PascalCase + Controller suffix
  - Methods: camelCase (startCounting, createAdjustmentFromOpname)
  - Routes: kebab-case (/stock-opnames, /start-counting)
  - Database: snake_case (stock_opnames, opname_number)

- **Frontend**:
  - Components: PascalCase (OpnameFormModal.vue)
  - Variables: camelCase (opnameList, showAddModal)
  - Props: camelCase
  - Events: kebab-case (@get-system-quantity)
  - CSS: Tailwind utility classes

---

## 🔄 Integration with Stock Adjustment

### Auto-Adjustment Creation Flow

When completing stock opname (`complete()` method):

1. **Check for differences**:
   - Query all details where `difference_quantity != 0`

2. **Create Stock Adjustment** if differences exist:
   ```php
   $adjustment = StockAdjustment::create([
       'adjustment_date' => $opname->opname_date,
       'location_id' => $opname->location_id,
       'total_items' => $differenceCount,
       'description' => "Auto-generated from Stock Opname: {$opname->opname_number}",
       'notes' => $opname->notes,
       'status' => 'draft', // or 'posted' based on config
       'created_by' => $userId,
   ]);
   ```

3. **Create Adjustment Details**:
   ```php
   foreach ($opname->details as $detail) {
       if ($detail->difference_quantity != 0) {
           StockAdjustmentDetail::create([
               'stock_adjustment_id' => $adjustment->id,
               'product_id' => $detail->product_id,
               'system_quantity' => $detail->system_quantity,
               'actual_quantity' => $detail->physical_quantity,
               'difference_quantity' => $detail->difference_quantity,
               'adjustment_type' => $detail->adjustment_type,
               'reason' => "Stock Opname: {$opname->opname_number}",
               'notes' => $detail->notes,
           ]);
       }
   }
   ```

4. **Optional**: Auto-post adjustment
   - If configured, call `$adjustment->post($userId)` to create stock cards immediately

5. **Return adjustment info** in response:
   ```php
   return response()->json([
       'message' => 'Stock Opname completed successfully.',
       'data' => $opname->fresh(['details.product']),
       'adjustment' => $adjustment->load('details.product'),
   ]);
   ```

6. **Frontend**: Show success notification with adjustment link
   ```javascript
   notificationStore.success(
       `Opname completed. Adjustment ${response.adjustment.adjustment_number} created.`
   );
   ```

---

## 📊 Reporting & Export

### CSV Export Format
Master-detail flattened format:

```csv
Opname Number,Date,Location,Product Code,Product Name,System Qty,Physical Qty,Difference,Type,Status,Description,Created By,Completed By,Notes
OPN-2025-00001,2025-01-15,Main Warehouse,PRD-001,Product A,100,95,-5,decrease,completed,"Monthly count",John Doe,Jane Smith,"Missing items"
OPN-2025-00001,2025-01-15,Main Warehouse,PRD-002,Product B,50,52,+2,increase,completed,"Monthly count",John Doe,Jane Smith,"Extra found"
```

---

## 🧪 Testing Checklist

### Backend Tests
- [ ] Create opname with valid data
- [ ] Create opname with invalid data (validation errors)
- [ ] Update draft opname
- [ ] Cannot update completed opname
- [ ] Delete draft opname
- [ ] Cannot delete completed opname
- [ ] Start counting (status change)
- [ ] Complete opname (creates adjustment)
- [ ] Complete opname with no differences (no adjustment)
- [ ] Cancel opname
- [ ] Bulk operations
- [ ] Export CSV
- [ ] Filters and search
- [ ] Pagination

### Frontend Tests
- [ ] Load opname list
- [ ] Create new opname
- [ ] Edit draft opname
- [ ] View opname details
- [ ] Add/remove products in form
- [ ] Auto-fetch system quantity
- [ ] Auto-calculate differences
- [ ] Prevent duplicate products
- [ ] Status actions (start, complete, cancel)
- [ ] Delete opname
- [ ] Export data
- [ ] Statistics display
- [ ] Error handling and notifications
- [ ] Responsive design (mobile, tablet, desktop)
- [ ] Dark mode

---

## 🚀 Implementation Steps

### Phase 1: Database & Models
1. Create migration file
2. Run migration
3. Create StockOpname model with relationships and methods
4. Create StockOpnameDetail model
5. Test models in Tinker

### Phase 2: Backend API
1. Create StockOpnameController
2. Implement CRUD methods
3. Implement status action methods
4. Implement helper methods (statistics, export, etc.)
5. Add API routes
6. Test with Postman/Insomnia

### Phase 3: Frontend Service
1. Add stockOpnameService to warehouseService.js
2. Test API calls

### Phase 4: Frontend Components
1. Create OpnameFormModal.vue
2. Create OpnameDetails.vue
3. Create OpnameStats.vue
4. Test components in isolation

### Phase 5: Main Page
1. Create StockOpname.vue main page
2. Integrate all components
3. Implement state management
4. Test full workflow

### Phase 6: Integration & Testing
1. Test complete workflow (create → start → complete → adjustment)
2. Test all status transitions
3. Test bulk operations
4. Test export
5. Test error scenarios
6. Cross-browser testing
7. Mobile responsive testing
8. Dark mode testing

### Phase 7: Polish & Documentation
1. Code review and refactoring
2. Add code comments
3. Update user documentation
4. Performance optimization

---

## 📝 Notes & Best Practices

1. **Follow existing patterns**: Copy structure from Stock Adjustment module exactly
2. **Code consistency**: Match naming conventions, indentation, and style
3. **Reuse components**: Don't recreate what already exists (PageHeader, StatCard, DataTable, Modal, etc.)
4. **Dark mode**: Always test both themes
5. **Mobile first**: Design for mobile, then scale up
6. **Error handling**: User-friendly messages, proper logging
7. **Performance**: Eager load relationships, use pagination, lazy load components
8. **Security**: Validate all inputs, prevent SQL injection, use CSRF tokens
9. **Audit trail**: Use soft deletes, track created_by/completed_by
10. **Comments**: Add comments for complex business logic only, let code be self-documenting

---

## 🎯 Success Criteria

The Stock Opname feature is complete when:
- ✅ All CRUD operations work correctly
- ✅ Status workflow functions properly
- ✅ Auto-adjustment creation works on completion
- ✅ All validations are in place
- ✅ Export functionality works
- ✅ Statistics display correctly
- ✅ UI is responsive and supports dark mode
- ✅ All components follow established patterns
- ✅ Code is clean, documented, and maintainable
- ✅ No console errors or warnings
- ✅ All tests pass

---

## 🔗 Reference Files

Study these files for implementation patterns:
- `app/Http/Controllers/StockAdjustmentController.php`
- `app/Models/StockAdjustment.php`
- `app/Models/StockAdjustmentDetail.php`
- `resources/js/views/Dashboard/Warehouse/Adjustment.vue`
- `resources/js/components/Warehouse/AdjustmentFormModal.vue`
- `resources/js/components/Warehouse/AdjustmentDetails.vue`
- `resources/js/components/Warehouse/AdjustmentStats.vue`
- `resources/js/services/warehouseService.js`
- `database/migrations/2025_01_31_100000_refactor_stock_adjustments_to_master_detail.php`

---

**END OF PROMPT**

Generated for: Stock Opname Feature Implementation
Based on: Stock Adjustment Module Architecture
Framework: Laravel 10 + Vue 3 + Tailwind CSS
Pattern: Master-Detail with Status Workflow
