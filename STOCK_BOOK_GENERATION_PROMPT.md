# STOCK BOOK (BUKU STOCK) FEATURE - GENERATION PROMPT

## CONTEXT & OVERVIEW

Ini adalah sistem Warehouse Management berbasis Laravel + Vue.js yang sudah memiliki modul:
- **Products** - Master data produk
- **Locations** - Master data gudang/lokasi
- **Stock In** - Transaksi penerimaan barang
- **Stock Mutations** - Mutasi antar gudang
- **Stock Adjustments** - Penyesuaian stok (master-detail)
- **Stock Opname** - Stock counting (master-detail)
- **Stock Cards** - Kartu stok (sudah ada table & basic controller)

**Database yang sudah ada:**
```sql
stock_cards (
    id, product_id, location_id, transaction_date, transaction_type,
    reference_id, reference_number, quantity_in, quantity_out, balance,
    unit_price, notes, created_at, updated_at
)
-- Index: (product_id, location_id, transaction_date)
```

**Service yang sudah ada:**
- `stockCardService.getAll(params)`
- `stockCardService.getByProduct(productId, params)`
- `stockCardService.getByLocation(locationId, params)`
- `stockCardService.export(params)`

**Controller yang sudah ada:**
- `StockCardController@index` - List stock cards dengan filter
- `StockCardController@show` - Detail untuk product+location tertentu
- `StockCardController@summary` - Summary per transaction type
- `StockCardController@balances` - Current balance semua produk di lokasi
- `StockCardController@export` - Export (placeholder)

**Frontend yang sudah ada:**
- `BukuStock.vue` - Halaman basic dengan filter dan stats

---

## OBJECTIVE

**Generate fitur Stock Book (Buku Stock) yang lengkap, sempurna, dan TIDAK MEMBEBANI SERVER** dengan requirements:

### 1. PERFORMANCE OPTIMIZATION (CRITICAL!)
- ✅ **Query Optimization**: Gunakan eager loading, index, dan query builder yang efisien
- ✅ **Pagination**: WAJIB pagination untuk semua list (default 50 records per page)
- ✅ **Lazy Loading**: Load data saat dibutuhkan (on-demand)
- ✅ **Caching Strategy**: Cache data yang sering diakses (current balance, summary)
- ✅ **Date Range Limit**: Batasi max date range (misal: max 1 tahun) untuk prevent large data fetching
- ✅ **Indexed Queries**: Pastikan semua query memanfaatkan index yang ada
- ✅ **Chunking**: Untuk export/report besar, gunakan chunking
- ✅ **Database**: Jangan load semua data sekaligus, gunakan whereDate, whereBetween dengan tepat

### 2. FUNCTIONAL REQUIREMENTS

#### A. VIEW MODES
1. **Product-Centric View**
   - Filter by product → tampilkan all locations
   - Show movement per location for that product
   - Current balance per location

2. **Location-Centric View**
   - Filter by location → tampilkan all products
   - Show movement per product in that location
   - Current balance per product

3. **Detailed Ledger View** (Product + Location specific)
   - Traditional stock card format
   - Columns: Date | Transaction Type | Reference | In | Out | Balance | Notes
   - Opening balance (saldo awal)
   - Running balance calculation
   - Summary: Total In, Total Out, Ending Balance

4. **Current Balance View**
   - Real-time stock position
   - All products across all locations
   - Alert indicators (below min, above max)
   - Quick search & filter

#### B. FILTERING & SEARCH
- **Product Filter**: Dropdown autocomplete untuk pilih produk (load on search)
- **Location Filter**: Dropdown untuk pilih lokasi
- **Date Range**: Start date & end date (dengan validasi max range)
- **Transaction Type**: Filter by type (stock_in, mutation_in, mutation_out, adjustment, etc)
- **Quick Filters**: Today, This Week, This Month, This Year, Custom
- **Search**: Search by product code, product name, reference number

#### C. REPORTING FEATURES
1. **Summary by Period**
   - Opening balance (saldo awal periode)
   - Total movements (in/out) per transaction type
   - Ending balance (saldo akhir periode)
   - Visual charts (optional, lightweight)

2. **Movement Analysis**
   - Most active products
   - Slow moving items
   - Stock turnover ratio
   - Alert notifications (low stock, overstock)

3. **Export Options**
   - **CSV Export**: Lightweight untuk data besar
   - **Excel Export**: Dengan formatting (menggunakan Laravel Excel)
   - **PDF Export**: Formatted stock card report (per product/location)
   - **Print View**: Browser-friendly print layout

#### D. REAL-TIME FEATURES
- **Current Stock Indicator**: Real-time balance display
- **Last Transaction**: Tampilkan last transaction date & type
- **Stock Status**: In-stock, Low stock, Out of stock, Overstock
- **Auto-refresh**: Optional auto-refresh stats setiap 30 detik

---

## TECHNICAL SPECIFICATIONS

### 1. BACKEND (Laravel)

#### A. Controller Enhancements (StockCardController.php)

**Methods to Complete:**

```php
// 1. Index - Optimized with pagination & filters
public function index(Request $request)
{
    // Validation dengan max date range
    // Efficient query dengan whereHas, select, join
    // Eager loading minimal data
    // Pagination 50 per page
    // Return dengan summary stats
}

// 2. Show - Detailed ledger untuk product+location
public function show(Request $request)
{
    // Opening balance calculation (efficient)
    // Transaction list dengan running balance
    // Summary (total in, out, ending)
    // Period info
}

// 3. Current Balances - Real-time stock position
public function currentBalances(Request $request)
{
    // Use cached data atau query dengan subquery efisien
    // Group by product & location
    // Calculate current balance dari last stock card
    // Include stock status (low, normal, high, out)
}

// 4. Movement Summary - Aggregated data
public function movementSummary(Request $request)
{
    // Group by transaction_type
    // Aggregate sum(quantity_in), sum(quantity_out)
    // Period comparison (current vs previous)
    // Chart data preparation
}

// 5. Stock Position by Date - Historical balance
public function stockPositionByDate(Request $request)
{
    // Get balance at specific date
    // Use efficient query: last balance before or on date
    // Support bulk query untuk multiple products/locations
}

// 6. Export - Chunked export untuk large data
public function export(Request $request)
{
    // Validate format (csv, excel, pdf)
    // Use Laravel Excel dengan chunking
    // Queue untuk large exports
    // Return download or send email
}

// 7. Statistics - Dashboard stats
public function statistics(Request $request)
{
    // Total transactions today/week/month
    // Top moving products
    // Low stock alerts count
    // Cache hasil selama 5 menit
}
```

#### B. Optimization Techniques

**Query Optimization:**
```php
// GOOD - Efficient query
$cards = StockCard::select(['id', 'product_id', 'transaction_date', 'quantity_in', 'quantity_out', 'balance'])
    ->with(['product:id,product_code,product_name', 'location:id,name'])
    ->where('product_id', $productId)
    ->whereBetween('transaction_date', [$startDate, $endDate])
    ->orderBy('transaction_date')
    ->paginate(50);

// BAD - Load semua data
$cards = StockCard::with('product', 'location')->get();
```

**Opening Balance Calculation:**
```php
// Efficient opening balance
$openingBalance = StockCard::where('product_id', $productId)
    ->where('location_id', $locationId)
    ->where('transaction_date', '<', $startDate)
    ->orderBy('transaction_date', 'desc')
    ->value('balance') ?? 0;
```

**Current Balance (Cached):**
```php
// Cache current balance per product-location
$cacheKey = "stock_balance_{$productId}_{$locationId}";
$balance = Cache::remember($cacheKey, 300, function() use ($productId, $locationId) {
    return StockCard::where('product_id', $productId)
        ->where('location_id', $locationId)
        ->orderBy('transaction_date', 'desc')
        ->orderBy('id', 'desc')
        ->value('balance') ?? 0;
});
```

**Chunked Export:**
```php
// Export dengan chunking
use Maatwebsite\Excel\Facades\Excel;

public function export(Request $request)
{
    return Excel::download(new StockCardExport($request->all()), 'stock_card.xlsx');
}

// StockCardExport.php
class StockCardExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    public function query()
    {
        return StockCard::query()
            ->with(['product:id,product_code,product_name', 'location:id,name'])
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->orderBy('transaction_date');
    }

    public function headings(): array { /* ... */ }
    public function map($card): array { /* ... */ }
}
```

#### C. Database Considerations

**Additional Indexes (jika perlu):**
```sql
-- Index untuk query current balance (last record)
CREATE INDEX idx_stock_cards_balance_lookup ON stock_cards(product_id, location_id, transaction_date DESC, id DESC);

-- Index untuk summary queries
CREATE INDEX idx_stock_cards_transaction_type ON stock_cards(transaction_type, transaction_date);
```

**Validation Rules:**
```php
// Batasi date range untuk mencegah overload
$validated = $request->validate([
    'product_id' => 'nullable|exists:products,id',
    'location_id' => 'nullable|exists:locations,id',
    'start_date' => 'required|date|before_or_equal:today',
    'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
    'date_range_days' => 'nullable|integer|max:365', // Max 1 year
]);

// Auto-limit date range if exceeds
$daysDiff = Carbon::parse($validated['end_date'])->diffInDays($validated['start_date']);
if ($daysDiff > 365) {
    return response()->json(['error' => 'Date range cannot exceed 365 days'], 422);
}
```

---

### 2. FRONTEND (Vue.js)

#### A. Component Structure

```
resources/js/views/Dashboard/Warehouse/
├── StockBook/
│   ├── StockBookIndex.vue          # Main page dengan tabs
│   ├── ProductView.vue             # Product-centric view
│   ├── LocationView.vue            # Location-centric view
│   ├── LedgerView.vue              # Detailed ledger (product+location)
│   ├── CurrentBalanceView.vue      # Real-time balance view
│   └── Components/
│       ├── StockBookFilters.vue    # Filter modal/panel
│       ├── StockBookStats.vue      # Statistics cards
│       ├── StockBookTable.vue      # Table component
│       ├── LedgerTable.vue         # Traditional ledger table
│       ├── BalanceCard.vue         # Balance display card
│       ├── ExportModal.vue         # Export options modal
│       └── DateRangePicker.vue     # Custom date range picker
```

#### B. State Management

```javascript
// stockBookStore.js (Pinia Store)
export const useStockBookStore = defineStore('stockBook', {
    state: () => ({
        filters: {
            product_id: null,
            location_id: null,
            start_date: null,
            end_date: null,
            transaction_type: null,
            view_mode: 'ledger', // ledger, product, location, balance
        },
        stockCards: [],
        currentBalances: [],
        statistics: {},
        loading: false,
        pagination: {
            current_page: 1,
            per_page: 50,
            total: 0,
        },
    }),

    actions: {
        async fetchStockCards() {
            // Fetch dengan debounce
            // Handle pagination
            // Update state
        },

        async fetchCurrentBalances() {
            // Fetch real-time balances
            // Cache di frontend juga (5 menit)
        },

        async exportData(format) {
            // Download file
            // Show progress
        },

        clearFilters() {
            // Reset filters
        },
    },
});
```

#### C. Performance Optimization (Frontend)

**Virtual Scrolling untuk large data:**
```vue
<template>
    <!-- Gunakan virtual-scroller untuk render banyak rows -->
    <RecycleScroller
        :items="stockCards"
        :item-size="50"
        key-field="id"
        v-slot="{ item }"
    >
        <StockCardRow :card="item" />
    </RecycleScroller>
</template>
```

**Lazy Loading dengan Intersection Observer:**
```javascript
// Load more saat scroll ke bottom
const loadMore = () => {
    if (pagination.current_page < pagination.last_page) {
        fetchStockCards(pagination.current_page + 1, true); // append mode
    }
};

// Intersection observer
const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
        loadMore();
    }
});
```

**Debounced Search:**
```javascript
import { debounce } from 'lodash-es';

const debouncedSearch = debounce(async (query) => {
    await searchProducts(query);
}, 300);
```

**Memoized Computeds:**
```javascript
import { computed } from 'vue';

const totalIn = computed(() => {
    return stockCards.value.reduce((sum, card) => sum + card.quantity_in, 0);
});

const totalOut = computed(() => {
    return stockCards.value.reduce((sum, card) => sum + card.quantity_out, 0);
});
```

#### D. UI/UX Features

**Date Range Presets:**
```javascript
const datePresets = [
    { label: 'Today', value: 'today' },
    { label: 'Yesterday', value: 'yesterday' },
    { label: 'Last 7 Days', value: 'last_7_days' },
    { label: 'Last 30 Days', value: 'last_30_days' },
    { label: 'This Month', value: 'this_month' },
    { label: 'Last Month', value: 'last_month' },
    { label: 'This Year', value: 'this_year' },
    { label: 'Custom', value: 'custom' },
];
```

**Stock Status Badges:**
```vue
<template>
    <span :class="stockStatusClass(balance, minStock, maxStock)">
        {{ stockStatusLabel(balance, minStock, maxStock) }}
    </span>
</template>

<script>
const stockStatusClass = (balance, min, max) => {
    if (balance === 0) return 'badge-danger';
    if (balance < min) return 'badge-warning';
    if (balance > max) return 'badge-info';
    return 'badge-success';
};

const stockStatusLabel = (balance, min, max) => {
    if (balance === 0) return 'Out of Stock';
    if (balance < min) return 'Low Stock';
    if (balance > max) return 'Overstock';
    return 'In Stock';
};
</script>
```

**Loading States:**
```vue
<template>
    <div v-if="loading" class="animate-pulse">
        <!-- Skeleton loader -->
    </div>
    <div v-else>
        <!-- Actual content -->
    </div>
</template>
```

---

## USER STORIES & USE CASES

### Use Case 1: View Product Movement Across All Locations
**Actor**: Warehouse Manager
**Flow**:
1. Open Stock Book page
2. Select "Product View" tab
3. Choose product from autocomplete search
4. Select date range (default: This Month)
5. System shows:
   - Summary card: Total In, Total Out, Current Balance (all locations)
   - Table: Location-wise breakdown
   - Each location shows: Opening Balance | In | Out | Ending Balance
6. Click on location to see detailed transactions
7. Export to Excel for reporting

**Performance**: Max 2 seconds untuk load data 1 bulan (dengan pagination)

---

### Use Case 2: Check Current Stock All Products in Warehouse
**Actor**: Warehouse Staff
**Flow**:
1. Open Stock Book → "Current Balance" tab
2. Select location (e.g., Gudang Utama)
3. System instantly shows all products with:
   - Product Code, Name
   - Current Balance
   - Min/Max Stock
   - Status (In Stock, Low Stock, Out of Stock)
   - Last Movement Date
4. Use search box to find specific product
5. Click on product to see detailed ledger
6. Export low stock items to CSV for procurement

**Performance**: Instant load dengan caching (< 1 second)

---

### Use Case 3: Audit Stock Card for Specific Product & Location
**Actor**: Auditor
**Flow**:
1. Open Stock Book → "Ledger View" tab
2. Select Product & Location
3. Select Date Range (e.g., January 2025)
4. System shows:
   - Opening Balance (saldo awal 1 Jan 2025)
   - All transactions in chronological order:
     * Date | Type | Reference | In | Out | Balance | Notes
   - Ending Balance (saldo akhir 31 Jan 2025)
   - Summary: Total In, Total Out
5. Verify each transaction against source documents
6. Export to PDF for audit trail

**Performance**: Load 1 bulan data dengan 100+ transaksi < 3 seconds

---

### Use Case 4: Generate Monthly Stock Movement Report
**Actor**: Finance Manager
**Flow**:
1. Open Stock Book → "Movement Summary" tab
2. Select month (e.g., October 2024)
3. System shows aggregated summary:
   - By Transaction Type (Stock In, Mutation, Adjustment, Opname)
   - Each type shows: Count, Total In, Total Out
   - Top 10 most active products
   - Products with variances (adjustment > 10%)
4. Filter by specific transaction type
5. Export to Excel with charts

**Performance**: Aggregate query < 2 seconds (menggunakan SQL grouping)

---

## API ENDPOINTS

```php
// routes/api.php

// Stock Book Main Routes
Route::prefix('stock-book')->group(function () {
    // List & filters
    Route::get('/', [StockBookController::class, 'index']);                    // GET /api/stock-book
    Route::get('/ledger', [StockBookController::class, 'ledger']);             // GET /api/stock-book/ledger?product_id=1&location_id=2&start_date=2025-01-01&end_date=2025-01-31

    // Current balances
    Route::get('/current-balances', [StockBookController::class, 'currentBalances']); // GET /api/stock-book/current-balances?location_id=1
    Route::get('/balance-by-date', [StockBookController::class, 'balanceByDate']);    // GET /api/stock-book/balance-by-date?product_id=1&location_id=1&date=2025-01-15

    // Summaries & Analytics
    Route::get('/movement-summary', [StockBookController::class, 'movementSummary']); // GET /api/stock-book/movement-summary?start_date=2025-01-01&end_date=2025-01-31
    Route::get('/transaction-summary', [StockBookController::class, 'transactionSummary']); // Group by transaction_type
    Route::get('/statistics', [StockBookController::class, 'statistics']);            // Dashboard stats

    // Export
    Route::get('/export', [StockBookController::class, 'export']);             // GET /api/stock-book/export?format=xlsx&product_id=1&...
    Route::post('/export-queue', [StockBookController::class, 'exportQueue']); // Queue large exports

    // Utilities
    Route::get('/products-with-stock', [StockBookController::class, 'productsWithStock']); // Products yang ada transaksi
    Route::get('/locations-with-stock', [StockBookController::class, 'locationsWithStock']); // Locations yang ada stock
});
```

**Response Format Examples:**

```json
// GET /api/stock-book/ledger
{
    "product": {
        "id": 1,
        "product_code": "PRD-001",
        "product_name": "Laptop Dell XPS 13"
    },
    "location": {
        "id": 2,
        "code": "WH-JKT",
        "name": "Warehouse Jakarta"
    },
    "period": {
        "start_date": "2025-01-01",
        "end_date": "2025-01-31"
    },
    "opening_balance": 50,
    "transactions": [
        {
            "id": 101,
            "transaction_date": "2025-01-05",
            "transaction_type": "stock_in",
            "reference_number": "IN-2025-00001",
            "reference_id": 1,
            "quantity_in": 20,
            "quantity_out": 0,
            "balance": 70,
            "unit_price": 15000000.00,
            "notes": "Purchase Order #PO-2025-001"
        },
        {
            "id": 102,
            "transaction_date": "2025-01-10",
            "transaction_type": "mutation_out",
            "reference_number": "MUT-2025-00003",
            "reference_id": 3,
            "quantity_in": 0,
            "quantity_out": 10,
            "balance": 60,
            "unit_price": 0,
            "notes": "Transfer to Warehouse Surabaya"
        }
    ],
    "summary": {
        "total_in": 20,
        "total_out": 10,
        "ending_balance": 60,
        "net_change": 10
    },
    "pagination": {
        "current_page": 1,
        "per_page": 50,
        "total": 25,
        "last_page": 1
    }
}
```

```json
// GET /api/stock-book/current-balances?location_id=2
{
    "location": {
        "id": 2,
        "code": "WH-JKT",
        "name": "Warehouse Jakarta"
    },
    "balances": [
        {
            "product_id": 1,
            "product_code": "PRD-001",
            "product_name": "Laptop Dell XPS 13",
            "current_balance": 60,
            "minimum_stock": 10,
            "maximum_stock": 100,
            "last_transaction_date": "2025-01-10",
            "last_transaction_type": "mutation_out",
            "status": "in_stock",
            "is_below_minimum": false,
            "is_above_maximum": false
        }
    ],
    "summary": {
        "total_products": 150,
        "in_stock": 145,
        "low_stock": 5,
        "out_of_stock": 0,
        "overstock": 3
    }
}
```

```json
// GET /api/stock-book/movement-summary?start_date=2025-01-01&end_date=2025-01-31
{
    "period": {
        "start_date": "2025-01-01",
        "end_date": "2025-01-31"
    },
    "by_transaction_type": [
        {
            "transaction_type": "stock_in",
            "transaction_count": 15,
            "total_quantity_in": 500,
            "total_quantity_out": 0,
            "percentage": 45.5
        },
        {
            "transaction_type": "mutation_out",
            "transaction_count": 10,
            "total_quantity_in": 0,
            "total_quantity_out": 200,
            "percentage": 18.2
        }
    ],
    "top_products": [
        {
            "product_id": 4,
            "product_code": "PRD-004",
            "product_name": "Mouse Logitech G502",
            "total_movements": 150,
            "total_in": 100,
            "total_out": 50
        }
    ],
    "overall_summary": {
        "total_transactions": 45,
        "total_in": 1200,
        "total_out": 800,
        "net_change": 400
    }
}
```

---

## VALIDATION & BUSINESS RULES

### 1. Date Range Validation
```php
// Max date range: 1 year
$maxDays = 365;
$daysDiff = Carbon::parse($endDate)->diffInDays($startDate);
if ($daysDiff > $maxDays) {
    throw ValidationException::withMessages([
        'end_date' => "Date range cannot exceed {$maxDays} days"
    ]);
}

// End date tidak boleh lebih dari hari ini
if (Carbon::parse($endDate)->isFuture()) {
    throw ValidationException::withMessages([
        'end_date' => 'End date cannot be in the future'
    ]);
}
```

### 2. Required Filters
```php
// Untuk Ledger View: product_id DAN location_id wajib
if ($viewMode === 'ledger') {
    if (!$request->filled('product_id') || !$request->filled('location_id')) {
        throw ValidationException::withMessages([
            'filters' => 'Product and Location are required for Ledger View'
        ]);
    }
}

// Untuk Movement Summary: date range wajib
if ($viewMode === 'summary') {
    if (!$request->filled('start_date') || !$request->filled('end_date')) {
        throw ValidationException::withMessages([
            'filters' => 'Date range is required for Movement Summary'
        ]);
    }
}
```

### 3. Pagination Limits
```php
// Max per page: 100
$perPage = min($request->input('per_page', 50), 100);
```

### 4. Export Limits
```php
// Untuk export besar (> 10,000 rows), gunakan queue
$estimatedRows = $this->estimateRowCount($filters);
if ($estimatedRows > 10000) {
    // Dispatch job ke queue
    ExportStockBookJob::dispatch($filters, auth()->user());

    return response()->json([
        'message' => 'Export is being processed. You will receive an email when ready.',
        'estimated_rows' => $estimatedRows,
        'queued' => true
    ]);
}
```

---

## ERROR HANDLING

### Backend Error Responses
```php
try {
    // Query
} catch (QueryException $e) {
    Log::error('Stock Book Query Error', [
        'error' => $e->getMessage(),
        'filters' => $request->all(),
    ]);

    return response()->json([
        'error' => 'Failed to fetch stock data. Please try again.',
        'details' => config('app.debug') ? $e->getMessage() : null
    ], 500);
}
```

### Frontend Error Handling
```javascript
try {
    const response = await stockBookService.fetchLedger(filters);
    stockCards.value = response.data.transactions;
} catch (error) {
    if (error.response?.status === 422) {
        // Validation errors
        notificationStore.error(error.response.data.message);
    } else if (error.response?.status === 500) {
        // Server errors
        notificationStore.error('Failed to load stock data. Please try again later.');
    } else {
        // Network errors
        notificationStore.error('Network error. Please check your connection.');
    }
    console.error('Stock Book Error:', error);
}
```

---

## TESTING CONSIDERATIONS

### Performance Benchmarks
- ✅ Query execution < 2 seconds untuk 1 bulan data
- ✅ Page load < 3 seconds total (dengan pagination)
- ✅ Export < 10 seconds untuk < 10,000 rows
- ✅ Cache hit rate > 80% untuk current balances

### Test Scenarios
1. **Large Date Range**: Test dengan 1 tahun data (365 hari)
2. **Many Transactions**: Product dengan > 1000 transactions
3. **Multiple Locations**: Product di 10+ locations
4. **Concurrent Users**: 50 users akses bersamaan
5. **Export Large Data**: Export 50,000+ rows

### Load Testing
```bash
# Menggunakan Apache Bench
ab -n 1000 -c 50 http://localhost:8000/api/stock-book/current-balances?location_id=1

# Target:
# - Response time < 500ms untuk 95% requests
# - Zero errors
```

---

## SECURITY CONSIDERATIONS

### 1. Authorization
```php
// Policy untuk StockBook
Gate::define('view-stock-book', function (User $user) {
    return $user->hasPermissionTo('view_stock_book');
});

Gate::define('export-stock-book', function (User $user) {
    return $user->hasPermissionTo('export_stock_book');
});

// Middleware
Route::middleware(['auth', 'can:view-stock-book'])->group(function () {
    Route::get('/stock-book', [StockBookController::class, 'index']);
});
```

### 2. SQL Injection Prevention
```php
// GOOD - Menggunakan parameter binding
$cards = DB::table('stock_cards')
    ->where('product_id', $productId)
    ->whereBetween('transaction_date', [$startDate, $endDate])
    ->get();

// BAD - Raw SQL tanpa binding
$cards = DB::select("SELECT * FROM stock_cards WHERE product_id = {$productId}");
```

### 3. Mass Assignment Protection
```php
// Model StockCard
protected $fillable = [
    'product_id', 'location_id', 'transaction_date', 'transaction_type',
    'reference_id', 'reference_number', 'quantity_in', 'quantity_out',
    'balance', 'unit_price', 'notes',
];

protected $guarded = ['id', 'created_at', 'updated_at'];
```

---

## DEPLOYMENT CHECKLIST

### Pre-Production
- [ ] Run migrations (no new tables needed, existing stock_cards OK)
- [ ] Add additional indexes if needed
- [ ] Set up Redis cache for production
- [ ] Configure queue workers for export jobs
- [ ] Test dengan production-like data volume
- [ ] Set up monitoring (query performance, cache hit rate)

### Production Configuration
```env
# .env
STOCK_BOOK_MAX_DATE_RANGE_DAYS=365
STOCK_BOOK_DEFAULT_PER_PAGE=50
STOCK_BOOK_MAX_PER_PAGE=100
STOCK_BOOK_CACHE_TTL=300
STOCK_BOOK_EXPORT_QUEUE_THRESHOLD=10000
```

### Monitoring
- Query slow log (queries > 2 seconds)
- Cache hit/miss ratio
- Export job failures
- API endpoint response times

---

## SUCCESS CRITERIA

### Functional
- ✅ User dapat view stock movement dengan 4 view modes
- ✅ Filter berfungsi dengan baik dan user-friendly
- ✅ Export ke CSV, Excel, PDF berfungsi
- ✅ Real-time balance akurat
- ✅ Summary & analytics tepat

### Performance
- ✅ Page load < 3 seconds
- ✅ Query execution < 2 seconds
- ✅ Export < 10 seconds (untuk normal size)
- ✅ No server overload pada concurrent access
- ✅ Cache efektif (hit rate > 80%)

### User Experience
- ✅ Intuitive navigation
- ✅ Clear error messages
- ✅ Loading indicators
- ✅ Responsive design
- ✅ Print-friendly layout

---

## IMPLEMENTATION PRIORITY

### Phase 1 (High Priority - Core Features)
1. **Ledger View** - Traditional stock card per product+location
2. **Current Balance View** - Real-time stock position
3. **Basic Filters** - Product, Location, Date Range
4. **Pagination** - Efficient data loading
5. **CSV Export** - Basic export functionality

### Phase 2 (Medium Priority - Enhanced Features)
1. **Product View** - Product-centric analysis
2. **Location View** - Location-centric analysis
3. **Movement Summary** - Aggregated reporting
4. **Excel Export** - Formatted export
5. **Statistics Dashboard** - Key metrics

### Phase 3 (Low Priority - Advanced Features)
1. **PDF Export** - Formatted reports
2. **Charts & Visualizations** - Data visualization
3. **Auto-refresh** - Real-time updates
4. **Advanced Analytics** - Turnover ratio, slow moving
5. **Email Reports** - Scheduled reports

---

## EXAMPLE WORKFLOWS

### Developer Workflow
```bash
# 1. Backend
php artisan make:controller StockBookController
# Implement methods: index, ledger, currentBalances, movementSummary, export, statistics

# 2. Frontend
# Create components in resources/js/views/Dashboard/Warehouse/StockBook/
# Create Pinia store: stockBookStore.js
# Update routes in router

# 3. Service
# Update warehouseService.js with stockBookService

# 4. Testing
php artisan test --filter StockBookTest
npm run test

# 5. Optimization
# Check query performance dengan Laravel Debugbar
# Verify cache dengan Redis CLI

# 6. Documentation
# Update API docs
# Create user guide
```

---

## BEST PRACTICES SUMMARY

### DO ✅
- Use pagination ALWAYS
- Cache frequently accessed data (current balances)
- Validate & limit date ranges
- Use indexed queries
- Chunk large exports
- Show loading states
- Handle errors gracefully
- Log performance metrics
- Use eager loading wisely
- Optimize frontend re-renders

### DON'T ❌
- Load all data without pagination
- Query without date range limits
- Ignore database indexes
- Block UI during long operations
- Expose sensitive data in logs
- Use SELECT * in production
- Forget to sanitize user inputs
- Skip error handling
- Over-fetch data
- Nest loops in queries

---

## FINAL NOTES

Fitur Stock Book ini adalah **CRITICAL** untuk tracking inventory movement dan **HARUS PERFORMANT**.

**Key Success Factors:**
1. **Efficient Queries** - Leverage indexes, limit data fetching
2. **Smart Caching** - Cache current balances and summaries
3. **Pagination** - Never load all data at once
4. **User-Friendly Filters** - Make it easy to find data
5. **Reliable Exports** - Queue large exports, chunk data
6. **Real-time Accuracy** - Balances must always be correct

**Remember**: Buku Stock adalah source of truth untuk inventory position. Data harus **akurat**, **cepat diakses**, dan **tidak membebani server**.

Generate fitur ini dengan mengikuti semua best practices di atas!

---

END OF PROMPT
