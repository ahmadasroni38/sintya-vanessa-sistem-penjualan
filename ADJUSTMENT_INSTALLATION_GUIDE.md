# Stock Adjustment - Installation & Implementation Guide

## 📦 Files Created/Modified

### **New Files Created:**

1. **Frontend Components:**
   - ✅ `resources/js/components/Warehouse/AdjustmentStats.vue`
   - ✅ `resources/js/components/Warehouse/AdjustmentFormModal.vue`
   - ✅ `resources/js/components/Warehouse/AdjustmentDetails.vue`
   - ✅ `resources/js/components/Warehouse/AdjustmentFilters.vue`
   - ✅ `resources/js/components/Warehouse/BulkActionsBar.vue`

2. **Updated View:**
   - ✅ `Adjustment_UPDATED.vue` (ready to replace existing file)

### **Modified Files:**

1. **Backend:**
   - ✅ `app/Http/Controllers/StockAdjustmentController.php` (added methods)
   - ✅ `routes/api.php` (added routes)

2. **Frontend:**
   - ✅ `resources/js/services/warehouseService.js` (added methods)
   - ✅ `resources/js/components/Warehouse/StatCard.vue` (added icons)

---

## 🚀 Installation Steps

### **Step 1: Replace Main View File**

Copy file `Adjustment_UPDATED.vue` dan ganti file yang ada:

```bash
# Backup file lama
cp resources/js/views/Dashboard/Warehouse/Adjustment.vue resources/js/views/Dashboard/Warehouse/Adjustment.vue.old

# Replace dengan file baru
cp Adjustment_UPDATED.vue resources/js/views/Dashboard/Warehouse/Adjustment.vue
```

**ATAU manual:**
1. Open `Adjustment_UPDATED.vue`
2. Copy semua isinya
3. Paste ke `resources/js/views/Dashboard/Warehouse/Adjustment.vue`

---

### **Step 2: Verify All Component Files**

Pastikan file-file berikut sudah ada dan benar:

```
resources/js/components/Warehouse/
├── AdjustmentStats.vue          ✅ Created
├── AdjustmentFormModal.vue      ✅ Created
├── AdjustmentDetails.vue        ✅ Created
├── AdjustmentFilters.vue        ✅ Created
├── BulkActionsBar.vue           ✅ Created
├── StatCard.vue                 ✅ Updated
├── PageHeader.vue               ✅ Existing
└── DetailItem.vue               ✅ Existing
```

---

### **Step 3: Verify Backend Files**

#### A. Controller (`app/Http/Controllers/StockAdjustmentController.php`)

Pastikan methods berikut sudah ada:

```php
✅ index()                      // Line ~17
✅ store()                      // Line ~98
✅ show()                       // Line ~145
✅ update()                     // Line ~185
✅ destroy()                    // Line ~240
✅ approve()                    // Line ~267
✅ cancel()                     // Line ~295
✅ calculateSystemQuantity()    // Line ~340
✅ statistics()                 // Line ~358
✅ bulkApprove()               // Line ~381  ← NEW
✅ bulkDelete()                // Line ~435  ← NEW
✅ export()                     // Line ~481  ← NEW
```

#### B. Routes (`routes/api.php`)

Pastikan routes berikut ada di section Stock Adjustment (sekitar line 306-320):

```php
Route::get('stock-adjustments/statistics', ...);
Route::get('stock-adjustments/export', ...);           // ← NEW
Route::post('stock-adjustments/bulk-approve', ...);    // ← NEW
Route::post('stock-adjustments/bulk-delete', ...);     // ← NEW
Route::post('stock-adjustments/calculate-system-quantity', ...);
Route::get('stock-adjustments', ...);
Route::post('stock-adjustments', ...);
Route::get('stock-adjustments/{stockAdjustment}', ...);
Route::put('stock-adjustments/{stockAdjustment}', ...);
Route::delete('stock-adjustments/{stockAdjustment}', ...);
Route::post('stock-adjustments/{stockAdjustment}/approve', ...);
Route::post('stock-adjustments/{stockAdjustment}/cancel', ...);
```

---

### **Step 4: Verify Service File**

Check `resources/js/services/warehouseService.js`, section `stockAdjustmentService`:

```javascript
export const stockAdjustmentService = {
    getAll: async (params = {}) => {...},
    getById: async (id) => {...},
    create: async (data) => {...},
    update: async (id, data) => {...},
    delete: async (id) => {...},
    approve: async (id) => {...},
    cancel: async (id, reason) => {...},
    calculateSystemQuantity: async (productId, locationId) => {...},
    getStatistics: async (params = {}) => {...},
    bulkApprove: async (ids) => {...},           // ← NEW
    bulkDelete: async (ids) => {...},            // ← NEW
    export: async (params = {}) => {...},        // ← NEW
};
```

---

### **Step 5: Clear Cache & Rebuild**

```bash
# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Clear npm cache
npm cache clean --force

# Reinstall dependencies (jika perlu)
rm -rf node_modules
npm install

# Build frontend
npm run dev
# atau untuk production
npm run build
```

---

## ✅ Verification Checklist

### **Backend Verification:**

```bash
# Test API endpoints
php artisan route:list | grep stock-adjustments

# Should show 14 routes:
# GET    /api/stock-adjustments
# POST   /api/stock-adjustments
# GET    /api/stock-adjustments/statistics
# GET    /api/stock-adjustments/export
# POST   /api/stock-adjustments/bulk-approve
# POST   /api/stock-adjustments/bulk-delete
# POST   /api/stock-adjustments/calculate-system-quantity
# GET    /api/stock-adjustments/{stockAdjustment}
# PUT    /api/stock-adjustments/{stockAdjustment}
# DELETE /api/stock-adjustments/{stockAdjustment}
# POST   /api/stock-adjustments/{stockAdjustment}/approve
# POST   /api/stock-adjustments/{stockAdjustment}/cancel
```

### **Frontend Verification:**

Open browser dan check:

1. **Page Loads:**
   - ✅ Navigate to Stock Adjustment page
   - ✅ No console errors
   - ✅ Stats cards load
   - ✅ Table loads

2. **Filters:**
   - ✅ Click "Filters" button
   - ✅ Filter panel appears
   - ✅ All filter options populated
   - ✅ Filters work correctly

3. **CRUD Operations:**
   - ✅ Click "New Adjustment"
   - ✅ Form modal opens
   - ✅ All fields visible
   - ✅ Save works
   - ✅ Edit works
   - ✅ Delete works
   - ✅ Approve works

4. **Bulk Operations:**
   - ✅ Select multiple items (checkbox)
   - ✅ Bulk actions bar appears at bottom
   - ✅ Bulk approve works
   - ✅ Bulk delete works
   - ✅ Bulk export works

5. **Export:**
   - ✅ Click "Export" button
   - ✅ CSV file downloads
   - ✅ File contains correct data
   - ✅ All columns present

---

## 🐛 Troubleshooting

### **Issue 1: Components not found**

```
Error: Cannot find module './components/Warehouse/AdjustmentFilters.vue'
```

**Solution:**
- Verify all component files exist in correct location
- Check file names are exact (case-sensitive)
- Restart dev server: `npm run dev`

---

### **Issue 2: API 404 errors**

```
POST http://localhost/api/stock-adjustments/bulk-approve 404
```

**Solution:**
```bash
# Clear route cache
php artisan route:clear

# List routes to verify
php artisan route:list | grep stock-adjustments

# If routes missing, check routes/api.php
```

---

### **Issue 3: DataTable not selectable**

**Solution:**
Check DataTable component supports `:selectable="true"` prop. If not, you may need to update DataTable component or remove bulk operations features.

---

### **Issue 4: Export downloads empty file**

**Solution:**
- Check backend has data
- Verify API returns blob response type
- Check browser allows downloads
- Try incognito mode

---

### **Issue 5: FunnelIcon not found**

```
Error: FunnelIcon is not exported from @heroicons/vue/24/outline
```

**Solution:**
```bash
# Update heroicons
npm install @heroicons/vue@latest

# Or use alternative icon
import { AdjustmentsHorizontalIcon } from '@heroicons/vue/24/outline';
# Replace FunnelIcon with AdjustmentsHorizontalIcon
```

---

## 📊 Database Check

Verify your database has correct schema:

```sql
-- Check stock_adjustments table structure
DESCRIBE stock_adjustments;

-- Should have columns:
-- id, adjustment_number, adjustment_date, product_id, location_id
-- system_quantity, actual_quantity, difference_quantity
-- adjustment_type, reason, notes, status
-- created_by, approved_by, approved_at
-- created_at, updated_at, deleted_at

-- Check sample data
SELECT * FROM stock_adjustments LIMIT 5;
```

---

## 🧪 Testing Flow

### **Manual Test Scenario:**

1. **Create Draft Adjustment:**
   - Click "New Adjustment"
   - Fill form
   - Select product & location
   - Check system quantity auto-loads
   - Enter actual quantity
   - Check difference calculates
   - Save
   - Verify appears in list with "draft" status

2. **Edit Draft:**
   - Click edit icon on draft item
   - Modify actual quantity
   - Save
   - Verify changes reflected

3. **Approve Adjustment:**
   - Click approve icon on draft
   - Verify status changes to "posted"
   - Check stock card created in database
   - Verify edit/delete buttons hidden

4. **Use Filters:**
   - Click "Filters" button
   - Select "Status: Draft"
   - Verify only drafts shown
   - Clear filters
   - Verify all items shown

5. **Bulk Operations:**
   - Select 2-3 draft items
   - Click "Approve Selected"
   - Verify all change to "posted"
   - Select 2-3 draft items
   - Click "Delete Selected"
   - Confirm
   - Verify items deleted

6. **Export:**
   - Click "Export"
   - Check CSV downloads
   - Open file
   - Verify data correct

---

## 📱 Browser Compatibility

Tested on:
- ✅ Chrome 120+
- ✅ Firefox 120+
- ✅ Edge 120+
- ✅ Safari 17+

---

## 🔒 Security Checklist

Before deploying to production:

- [ ] All API endpoints require authentication
- [ ] CSRF protection enabled
- [ ] Input validation on all forms
- [ ] SQL injection prevention (using Eloquent)
- [ ] XSS prevention (Vue auto-escapes)
- [ ] Authorization checks (user permissions)
- [ ] Rate limiting on API
- [ ] Audit logging enabled

---

## 📚 Additional Resources

- **Backend Documentation:** `ADJUSTMENT_FEATURES_COMPLETE.md`
- **API Documentation:** Coming soon (Swagger/OpenAPI)
- **Component Storybook:** Coming soon
- **Video Tutorial:** Coming soon

---

## 🆘 Support

Jika ada masalah atau error:

1. Check console for errors (F12)
2. Check Laravel logs: `storage/logs/laravel.log`
3. Check network tab for API errors
4. Refer to troubleshooting section above
5. Check comprehensive docs: `ADJUSTMENT_FEATURES_COMPLETE.md`

---

## ✨ Success Indicators

Your installation is successful when:

1. ✅ Page loads without errors
2. ✅ Stats cards show correct counts
3. ✅ Table displays adjustments
4. ✅ Filters work correctly
5. ✅ Can create new adjustment
6. ✅ Can edit draft adjustment
7. ✅ Can delete draft adjustment
8. ✅ Can approve adjustment
9. ✅ Bulk operations work
10. ✅ Export downloads CSV

---

**Installation Time:** ~15 minutes
**Difficulty:** Intermediate
**Prerequisites:** Laravel 10+, Vue 3+, PHP 8.1+, Node 18+

---

Good luck! 🚀
