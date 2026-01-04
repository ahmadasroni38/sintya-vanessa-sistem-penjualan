# EVENT LIST SISTEM INFORMASI AKUNTANSI DAN INVENTORY MANAGEMENT

## Daftar Event Berdasarkan DFD Level 1 dan Level 2

---

## 1. MANAJEMEN USER & ROLE (Proses 1.0)

1. User Baru Dibuat (Create User)
2. User Diupdate (Update User)
3. Role Baru Dibuat (Create Role)
4. Role Diupdate (Update Role)
5. Role Ditugaskan ke User (Assign Role)
6. User Diaktifkan (Activate User)
7. User Dinonaktifkan (Deactivate User)

---

## 2. MANAJEMEN CHART OF ACCOUNTS (Proses 2.0)

1. Akun Baru Dibuat (Create Account)
2. Akun Diupdate (Update Account)
3. Hierarki Akun Diatur (Set Account Hierarchy)
4. Saldo Awal Akun Diset (Set Opening Balance)
5. Akun Diaktifkan (Activate Account)
6. Akun Dinonaktifkan (Deactivate Account)

---

## 3. MANAJEMEN JOURNAL ENTRY (Proses 3.0)

1. Draft Jurnal Dibuat (Create Draft Journal)
2. Draft Jurnal Diedit (Update Draft Journal)
3. Jurnal Disubmit untuk Approval (Submit Journal)
4. Jurnal Disetujui (Approve Journal)
5. Jurnal Ditolak (Reject Journal)
6. Jurnal Diposting (Post Journal)
7. Saldo Akun Diupdate (Update Account Balance)

---

## 4. MANAJEMEN PRODUCT & KATEGORI (Proses 4.0)

1. Kategori Produk Dibuat (Create Product Category)
2. Kategori Produk Diupdate (Update Product Category)
3. Satuan Unit Dibuat (Create Unit)
4. Satuan Unit Diupdate (Update Unit)
5. Produk Baru Dibuat (Create Product)
6. Produk Diupdate (Update Product)
7. Batas Stock Min/Max Diatur (Set Stock Limits)

---

## 5. MANAJEMEN INVENTORY & STOCK (Proses 5.0)

1. Barang Masuk dari Pembelian (Stock In)
2. Transfer Stock Antar Lokasi (Stock Mutation)
3. Penyesuaian Stock (Stock Adjustment)
4. Perhitungan Fisik Stock (Stock Opname)
5. Saldo Stock Diupdate (Update Stock Balance)
6. Stock Card Dicatat (Record Stock Card)

---

## 6. MANAJEMEN SALES (Proses 6.0)

1. Sales Order Diinput (Input Sales Order)
2. Total Penjualan Dihitung (Calculate Total & Tax)
3. Pembayaran Diproses (Process Payment)
4. Penjualan Difinalisasi (Post Sales)
5. Invoice Digenerate (Generate Invoice)

---

## 7. GENERATE LAPORAN (Proses 7.0)

1. Laporan Neraca Digenerate (Generate Balance Sheet)
2. Laporan Laba Rugi Digenerate (Generate Income Statement)
3. Buku Besar Digenerate (Generate General Ledger)
4. Laporan Stock Card Digenerate (Generate Stock Card)
5. Laporan Stock Summary Digenerate (Generate Stock Summary)
6. Laporan Stock Minimum Digenerate (Generate Stock Minimum Report)
7. Laporan Penjualan Digenerate (Generate Sales Report)
8. Laporan Top Products Digenerate (Generate Top Products Report)
9. Laporan Customer Sales Digenerate (Generate Customer Sales Report)
10. Laporan Diekspor ke PDF (Export Report to PDF)
11. Laporan Diekspor ke Excel (Export Report to Excel)

---

## 8. EVENT SISTEM LAINNYA

1. User Login
2. User Logout
3. Validasi Hak Akses
4. Activity Log Dicatat
5. Notifikasi Stock Minimum
6. Backup Database

---

## SUMMARY

**Total Events**: 59 events

**Events per Process:**
- Proses 1.0 (Manajemen User & Role): 7 events
- Proses 2.0 (Manajemen Chart of Accounts): 6 events
- Proses 3.0 (Manajemen Journal Entry): 7 events
- Proses 4.0 (Manajemen Product & Kategori): 7 events
- Proses 5.0 (Manajemen Inventory/Stock): 6 events
- Proses 6.0 (Manajemen Sales): 5 events
- Proses 7.0 (Generate Laporan): 11 events
- Event Sistem Lainnya: 6 events

**Kategori Event:**
1. **CRUD Operations**: Create, Read, Update, Delete untuk semua master data
2. **Transactional Events**: Stock movements, sales, journal postings
3. **Approval Workflow Events**: Submit, approve, reject
4. **Calculation Events**: Calculate totals, update balances
5. **Reporting Events**: Generate various reports
6. **System Events**: Login, logout, access control, notifications, backup

**Critical Business Events:**
- Journal Entry Approval & Posting (memengaruhi laporan keuangan)
- Stock Balance Updates (memengaruhi inventory value)
- Sales Posting (memengaruhi stock dan keuangan)
- Account Balance Updates (memengaruhi semua laporan keuangan)
