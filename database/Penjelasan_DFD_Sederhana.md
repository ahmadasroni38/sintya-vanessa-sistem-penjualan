# Penjelasan Data Flow Diagram (DFD) Sistem SINTIYA

## 3.3.4 Data Flow Diagram (DFD)

Data Flow Diagram (DFD) adalah representasi grafis yang menggambarkan aliran data dalam sistem informasi. Berikut adalah penjelasan untuk setiap diagram DFD sistem SINTIYA.

---

## 3.3.4.1 DFD Level 0 (Context Diagram)

![DFD Level 0 Context Diagram](../DFD/DFD Level 0 (Context Diagram).png)

**Gambar 3.X DFD Level 0 (Context Diagram)**

Context Diagram menggambarkan sistem SINTIYA secara keseluruhan dengan 3 entitas eksternal:

**1. Admin**
- **Input ke sistem:** Data User & Role, Data Master Product & Lokasi, Data Stock In, Data Mutasi Stock, Data Adjustment, Data Opname, Data Penjualan
- **Output dari sistem:** Laporan Stock & Inventory, Stock Card, Konfirmasi Transaksi, Invoice/Nota Penjualan

**2. Owner**
- **Input ke sistem:** Request Laporan
- **Output dari sistem:** Laporan Keuangan & Inventory

**3. Accounting**
- **Input ke sistem:** Data Journal Entry, Data Chart of Accounts
- **Output dari sistem:** Laporan Keuangan, Status Approval

Diagram ini menunjukkan bahwa sistem menerima input dari Admin dan Accounting, memprosesnya, dan menghasilkan output berupa laporan dan konfirmasi untuk ketiga pengguna.

---

## 3.3.4.2 DFD Level 1

![DFD Level 1](../DFD/DFD Level 1.png)

**Gambar 3.X DFD Level 1**

DFD Level 1 memecah sistem menjadi 7 proses utama:

**Proses 1.0: Manajemen User & Role**
- Admin mengelola data user dan role
- Data disimpan di D1 (Users & Roles)

**Proses 2.0: Manajemen Chart of Accounts**
- Accounting mengelola bagan akun
- Data disimpan di D2 (Chart of Accounts)

**Proses 3.0: Manajemen Journal Entry**
- Accounting membuat jurnal transaksi
- Menerima journal entry otomatis dari Proses 5.0 dan 6.0
- Data disimpan di D3 (Journal Entries)

**Proses 4.0: Manajemen Product & Kategori**
- Admin mengelola data produk dan kategori
- Data disimpan di D4 (Products)

**Proses 5.0: Manajemen Inventory/Stock**
- Admin mengelola stock in, mutasi, adjustment, dan opname
- Menghasilkan journal entry otomatis
- Data disimpan di D5 (Stock Balances)

**Proses 6.0: Manajemen Sales**
- Admin mengelola transaksi penjualan
- Mengurangi stock dan menghasilkan journal entry otomatis
- Data disimpan di D6 (Sales) dan D7 (Customers)

**Proses 7.0: Generate Laporan**
- Owner meminta laporan
- Sistem membaca data dari berbagai data store dan menghasilkan laporan

---

## 3.3.4.3 DFD Level 2

### DFD Level 2 - Proses 1.0 (Manajemen User & Role)

![DFD Level 2 Proses 1.0](../DFD/DFD Level 2 - Proses 1.0 (Manajemen User & Role).png)

**Gambar 3.X DFD Level 2 Proses 1.0 - Manajemen User & Role**

Proses ini terdiri dari 4 sub-proses:

- **Proses 1.1: Create/Update User** - Admin membuat atau mengubah data user, disimpan ke D1A (Users)
- **Proses 1.2: Manage Role** - Admin mengelola role dan permissions, disimpan ke D1B (Roles)
- **Proses 1.3: Assign User Role** - Admin menugaskan role ke user, disimpan ke D1C (User Roles)
- **Proses 1.4: Activate/Deactivate User** - Admin mengaktifkan/menonaktifkan user

---

### DFD Level 2 - Proses 2.0 (Manajemen Chart of Accounts)

![DFD Level 2 Proses 2.0](../DFD/DFD Level 2 - Proses 2.0 (Manajemen Chart of Accounts).png)

**Gambar 3.X DFD Level 2 Proses 2.0 - Manajemen Chart of Accounts**

Proses ini terdiri dari 4 sub-proses:

- **Proses 2.1: Create/Update Account** - Accounting membuat atau mengubah akun, disimpan ke D2 (Chart of Accounts)
- **Proses 2.2: Set Account Hierarchy** - Accounting mengatur struktur parent-child akun
- **Proses 2.3: Set Opening Balance** - Accounting mengatur saldo awal akun, history disimpan ke D2A (Account Balance Histories)
- **Proses 2.4: Activate/Deactivate Account** - Accounting mengaktifkan/menonaktifkan akun

---

### DFD Level 2 - Proses 3.0 (Manajemen Journal Entry)

![DFD Level 2 Proses 3.0](../DFD/DFD Level 2 - Proses 3.0 (Manajemen Journal Entry).png)

**Gambar 3.X DFD Level 2 Proses 3.0 - Manajemen Journal Entry**

Proses ini terdiri dari 5 sub-proses:

- **Proses 3.1: Create/Draft Journal Entry** - Accounting membuat draft jurnal dengan detail debit/kredit, disimpan ke D3A (Journal Entries) dan D3B (Journal Entry Details)
- **Proses 3.2: Review & Submit** - Staff Accounting mereview dan submit jurnal untuk approval
- **Proses 3.3: Approval Process** - Owner/Accounting approve jurnal, disimpan ke D3C (Journal Approvals)
- **Proses 3.4: Posting Journal** - Sistem posting jurnal yang sudah approved
- **Proses 3.5: Update Account Balance** - Sistem otomatis update saldo akun di D2 (Chart of Accounts)

---

### DFD Level 2 - Proses 4.0 (Manajemen Product & Kategori)

![DFD Level 2 Proses 4.0](../DFD/DFD Level 2 - Proses 4.0 (Manajemen Product & Kategori).png)

**Gambar 3.X DFD Level 2 Proses 4.0 - Manajemen Product & Kategori**

Proses ini terdiri dari 4 sub-proses:

- **Proses 4.1: Manage Product Category** - Admin mengelola kategori produk, disimpan ke D4A (Product Categories)
- **Proses 4.2: Manage Unit** - Admin mengelola satuan produk (pcs, box, kg), disimpan ke D4B (Units)
- **Proses 4.3: Create/Update Product** - Admin membuat atau mengubah data produk, disimpan ke D4C (Products)
- **Proses 4.4: Set Stock Min/Max** - Admin mengatur batas minimum dan maksimum stock

---

### DFD Level 2 - Proses 5.0 (Manajemen Inventory & Stock)

![DFD Level 2 Proses 5.0](../DFD/DFD Level 2 - Proses 5.0 (Manajemen Inventory & Stock).png)

**Gambar 3.X DFD Level 2 Proses 5.0 - Manajemen Inventory & Stock**

Proses ini terdiri dari 5 sub-proses:

- **Proses 5.1: Stock In (Pembelian)** - Admin mencatat barang masuk, disimpan ke D5A (Stock In), menghasilkan journal entry
- **Proses 5.2: Stock Mutation (Transfer)** - Admin mencatat perpindahan barang antar lokasi, disimpan ke D5B (Stock Mutations)
- **Proses 5.3: Stock Adjustment (Penyesuaian)** - Admin mencatat penyesuaian stock, disimpan ke D5C (Stock Adjustments), menghasilkan journal entry
- **Proses 5.4: Stock Opname (Perhitungan Fisik)** - Admin mencatat hasil stock opname, disimpan ke D5D (Stock Opnames)
- **Proses 5.5: Update Stock Balance** - Sistem otomatis update saldo stock di D5E (Stock Balances) dan kartu stock di D5F (Stock Cards)

---

### DFD Level 2 - Proses 6.0 (Manajemen Sales)

![DFD Level 2 Proses 6.0](../DFD/DFD Level 2 - Proses 6.0 (Manajemen Sales).png)

**Gambar 3.X DFD Level 2 Proses 6.0 - Manajemen Sales**

Proses ini terdiri dari 5 sub-proses:

- **Proses 6.1: Input Sales Order** - Admin input data penjualan, disimpan ke D6 (Sales) dan D6A (Sale Details), data customer ke D7 (Customers)
- **Proses 6.2: Calculate Total & Tax** - Sistem hitung subtotal, pajak, dan total penjualan
- **Proses 6.3: Process Payment** - Admin mencatat pembayaran customer
- **Proses 6.4: Post Sales** - Sistem posting penjualan, kurangi stock, buat journal entry otomatis
- **Proses 6.5: Generate Invoice** - Sistem generate invoice/nota penjualan

---

### DFD Level 2 - Proses 7.0 (Generate Laporan)

![DFD Level 2 Proses 7.0](../DFD/DFD Level 2 - Proses 7.0 (Generate Laporan).png)

**Gambar 3.X DFD Level 2 Proses 7.0 - Generate Laporan**

Proses ini terdiri dari 4 sub-proses:

- **Proses 7.1: Generate Laporan Keuangan** - Owner request laporan neraca, laba rugi, buku besar dari D2 (Chart of Accounts) dan D3 (Journal Entries)
- **Proses 7.2: Generate Laporan Stock** - Owner request laporan stock balance dan stock card dari D4 (Products), D5 (Stock Balances), dan D5F (Stock Cards)
- **Proses 7.3: Generate Laporan Sales** - Owner request laporan penjualan dari D6 (Sales) dan D4 (Products)
- **Proses 7.4: Export Laporan** - Sistem export laporan ke format PDF atau Excel

---

## Kesimpulan

Sistem SINTIYA memiliki 7 proses utama yang terdiri dari 31 sub-proses detail. Setiap proses saling terintegrasi untuk memastikan konsistensi data. Transaksi inventory dan sales otomatis menghasilkan journal entry sehingga laporan keuangan selalu real-time dan akurat.
