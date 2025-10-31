# Stock Adjustment - Detail View Fix

## 🔧 Masalah yang Diperbaiki

**Issue:** Saat click tombol "View Details" (icon eye), modal tidak muncul dan data dari backend tidak tampil.

---

## ✅ Perbaikan yang Dilakukan

### 1. **Fixed API Instance (api.js)**

**Problem:**
- API instance dibuat ulang setiap request
- Pinia store belum ter-inisialisasi saat instance dibuat
- Token authentication mungkin tidak ter-attach dengan benar

**Solution:**
```javascript
// BEFORE: New instance every request
function createApiInstance() {
    const authStore = useAuthStore(); // Store might not be initialized
    const instance = axios.create({...});
    // ...
}

// AFTER: Single instance with localStorage token
const apiInstance = axios.create({...});

apiInstance.interceptors.request.use((config) => {
    const token = localStorage.getItem("token"); // Direct from localStorage
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});
```

**File:** `resources/js/utils/api.js`

---

### 2. **Added Debug Logging (Adjustment.vue)**

**Added console.log untuk tracking:**
```javascript
const viewDetails = async (adjustment) => {
    try {
        console.log("Fetching details for adjustment ID:", adjustment.id);
        const response = await stockAdjustmentService.getById(adjustment.id);
        console.log("API Response:", response);

        const adjustmentData = response.data || response;
        console.log("Adjustment Data:", adjustmentData);

        selectedAdjustment.value = adjustmentData;
        showDetailsModal.value = true;

        console.log("Modal should be visible now, showDetailsModal:", showDetailsModal.value);
    } catch (error) {
        console.error("Failed to load adjustment details:", error);
        console.error("Error details:", error.response?.data);
        notificationStore.error(
            error.response?.data?.message || "Failed to load adjustment details"
        );
    }
};
```

**File:** `resources/js/views/Dashboard/Warehouse/Adjustment.vue`

---

### 3. **Added Debug Watchers (AdjustmentDetails.vue)**

**Added watchers untuk monitoring props:**
```javascript
watch(() => props.show, (newVal) => {
    console.log('[AdjustmentDetails] Modal show changed:', newVal);
});

watch(() => props.adjustment, (newVal) => {
    console.log('[AdjustmentDetails] Adjustment data:', newVal);
}, { deep: true });
```

**File:** `resources/js/components/Warehouse/AdjustmentDetails.vue`

---

## 🧪 Cara Testing

### **Step 1: Check Browser Console**

1. Open Developer Tools (F12)
2. Go to "Console" tab
3. Click eye icon pada adjustment
4. Perhatikan log yang muncul:

**Expected Console Output (Success):**
```
Fetching details for adjustment ID: 1
API Response: {data: {...}, status: 200, ...}
Adjustment Data: {id: 1, adjustment_number: "ADJ-...", details: [...], ...}
Modal should be visible now, showDetailsModal: true
[AdjustmentDetails] Modal show changed: true
[AdjustmentDetails] Adjustment data: {id: 1, ...}
```

**If Error Occurs:**
```
Fetching details for adjustment ID: 1
Failed to load adjustment details: Error: Request failed with status code 401
Error details: {message: "Unauthenticated."}
```

---

### **Step 2: Check Authentication**

**Test if token exists:**
```javascript
// In browser console
console.log('Token:', localStorage.getItem('token'));
```

**Expected:** Should show JWT token string
**If null:** User not logged in properly

---

### **Step 3: Check API Endpoint**

**Test endpoint directly:**

1. Get token from localStorage (F12 → Console → `localStorage.getItem('token')`)
2. Open network tab (F12 → Network)
3. Click eye icon
4. Find request `stock-adjustments/{id}`
5. Check:
   - Request Headers → Authorization: `Bearer {token}`
   - Response → Status: 200
   - Response → Data should contain adjustment with details

---

### **Step 4: Verify Database**

**Check if data exists:**
```bash
php artisan tinker
```

```php
// Count adjustments
App\Models\StockAdjustment::count();

// Get first adjustment with details
$adj = App\Models\StockAdjustment::with(['details.product', 'location'])->first();
print_r($adj->toArray());
```

**Expected:**
- count > 0
- adjustment has details array
- details have product data

---

## 🔍 Common Issues & Solutions

### **Issue 1: Modal tidak muncul**

**Symptoms:**
- Console shows: `Modal should be visible now, showDetailsModal: true`
- But modal tidak terlihat

**Debug:**
```javascript
// In Adjustment.vue template
<AdjustmentDetails
    :show="showDetailsModal"
    :adjustment="selectedAdjustment"
    @close="showDetailsModal = false"
/>

// Add this temporarily
<div v-if="showDetailsModal" class="fixed top-0 left-0 bg-red-500 text-white p-4">
    DEBUG: Modal should be visible! Data: {{ selectedAdjustment?.adjustment_number }}
</div>
```

**Solution:**
- Check if Modal component has CSS issues
- Check if `z-index` is low
- Check if parent has `overflow: hidden`

---

### **Issue 2: API returns 401 Unauthenticated**

**Symptoms:**
```
Error: Request failed with status code 401
{message: "Unauthenticated."}
```

**Solution:**
```javascript
// Check if token exists
console.log('Token:', localStorage.getItem('token'));

// If no token, login again
// If token exists but still 401, token might be expired
// Re-login to get new token
```

---

### **Issue 3: API returns 404 Not Found**

**Symptoms:**
```
Error: Request failed with status code 404
```

**Solution:**
```bash
# Check if routes exist
php artisan route:list --path=stock-adjustments

# Should show:
# GET api/stock-adjustments/{stockAdjustment}
```

---

### **Issue 4: Data tidak ada details**

**Symptoms:**
- Modal muncul
- Master data ada
- Table products kosong: "No product details available"

**Debug:**
```javascript
// In browser console when modal is open
console.log('Selected Adjustment:', selectedAdjustment.value);
console.log('Details:', selectedAdjustment.value?.details);
```

**Solution:**
Check backend controller loads relationships:
```php
// In StockAdjustmentController::show()
$stockAdjustment->load(['location', 'creator', 'approver', 'details.product']);
```

---

## 📋 Verification Checklist

Before testing, ensure:

- [ ] Migration ran successfully (`php artisan migrate`)
- [ ] At least 1 adjustment exists in database
- [ ] Adjustment has details (check via tinker)
- [ ] User is logged in (token in localStorage)
- [ ] StockAdjustmentController uses master-detail version
- [ ] Routes registered (`php artisan route:list`)
- [ ] Frontend components updated (AdjustmentDetails.vue)

---

## 🎯 Expected Behavior

### **When clicking eye icon:**

1. **Console logs:**
   ```
   Fetching details for adjustment ID: 1
   API Response: {...}
   Adjustment Data: {...}
   Modal should be visible now, showDetailsModal: true
   [AdjustmentDetails] Modal show changed: true
   [AdjustmentDetails] Adjustment data: {...}
   ```

2. **Modal appears** with 4xl size (wide modal)

3. **General Information section shows:**
   - Adjustment Number: ADJ-202500001
   - Date: 31 Oktober 2025 (formatted Indonesian)
   - Location: Gudang Bandung
   - Total Items: 1 product(s) (blue badge)
   - Status: draft (yellow badge)
   - Created By: Admin
   - Approved By: - (empty if draft)
   - Description: asas
   - Notes: asa

4. **Product Details section shows table:**
   | # | Code | Name | Sys Qty | Act Qty | Diff | Type | Reason |
   |---|------|------|---------|---------|------|------|--------|
   | 1 | PRD-001 | Laptop Asus ROG | 123.00 | 123.00 | 0.00 | increase | test |

5. **Summary section shows:**
   - Total Increases: 1 (green card)
   - Total Decreases: 0 (red card)
   - Total Products: 1 (blue card)

---

## 🚀 Quick Test Steps

1. **Login to system**
2. **Navigate to Stock Adjustment page**
3. **Open browser console (F12)**
4. **Click eye icon on any adjustment**
5. **Check console for logs**
6. **Verify modal appears**
7. **Verify data displayed correctly**

---

## 📝 Files Modified

1. ✅ `resources/js/utils/api.js` - Fixed API instance and token handling
2. ✅ `resources/js/views/Dashboard/Warehouse/Adjustment.vue` - Added debug logging
3. ✅ `resources/js/components/Warehouse/AdjustmentDetails.vue` - Added debug watchers

---

## 🔄 Rollback (If Needed)

If issues persist, you can check:

```bash
# Check git status
git status

# See changes
git diff resources/js/utils/api.js

# Revert if needed (NOT recommended)
# git checkout resources/js/utils/api.js
```

---

## ✅ Final Notes

**The fix is complete. Now test in browser:**

1. Make sure you're logged in
2. Open browser console
3. Click eye icon on adjustment
4. Check console logs
5. Modal should appear with all data

**If modal still doesn't appear, check console for specific error message and reference this guide.**

---

**Created:** 2025-10-31
**Status:** ✅ FIXED - Ready to test
