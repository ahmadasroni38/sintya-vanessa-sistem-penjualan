# Data Flow Diagram (DFD) - Sistem 
## Sistem Informasi Akuntansi dan Inventory Management

---

## DFD Level 0 (Context Diagram)

```mermaid
graph LR
    %% External Entities
    Owner((Owner))
    Admin((Admin))
    Accounting((Accounting))

    %% Main System
    SINTIYA[Sistem <br/>Accounting & Inventory]

    %% Data Flows - Owner
    Owner -->|Request Laporan| SINTIYA
    SINTIYA -->|Laporan Keuangan & Inventory| Owner

    %% Data Flows - Admin
    Admin -->|Data User & Role| SINTIYA
    Admin -->|Data Master Product & Lokasi| SINTIYA
    Admin -->|Data Stock In| SINTIYA
    Admin -->|Data Mutasi Stock| SINTIYA
    Admin -->|Data Adjustment| SINTIYA
    Admin -->|Data Opname| SINTIYA
    Admin -->|Data Penjualan| SINTIYA
    SINTIYA -->|Laporan Stock & Inventory| Admin
    SINTIYA -->|Stock Card| Admin
    SINTIYA -->|Konfirmasi Transaksi| Admin
    SINTIYA -->|Invoice/Nota Penjualan| Admin

    %% Data Flows - Accounting
    Accounting -->|Data Journal Entry| SINTIYA
    Accounting -->|Data Chart of Accounts| SINTIYA
    SINTIYA -->|Laporan Keuangan| Accounting
    SINTIYA -->|Status Approval| Accounting
```

**Keterangan:**
- **SINTIYA**: Sistem Informasi Akuntansi dan Inventory Management
- **External Entities**: Owner (laporan), Admin (inventory & sales), Accounting (akuntansi)
- **Data Flows**: Menggambarkan aliran data dari dan ke sistem

---

## DFD Level 1

```mermaid
graph TB
    %% External Entities
    Owner((Owner))
    Admin((Admin))
    Accounting((Accounting))

    %% Processes
    P1[1.0<br/>Manajemen<br/>User & Role]
    P2[2.0<br/>Manajemen<br/>Chart of Accounts]
    P3[3.0<br/>Manajemen<br/>Journal Entry]
    P4[4.0<br/>Manajemen<br/>Product & Kategori]
    P5[5.0<br/>Manajemen<br/>Inventory/Stock]
    P6[6.0<br/>Manajemen<br/>Sales]
    P7[7.0<br/>Generate<br/>Laporan]

    %% Data Stores
    D1[(D1: Users & Roles)]
    D2[(D2: Chart of Accounts)]
    D3[(D3: Journal Entries)]
    D4[(D4: Products)]
    D5[(D5: Stock Balances)]
    D6[(D6: Sales)]
    D7[(D7: Customers)]

    %% Admin Flows - User Management
    Admin -->|Data User & Role| P1
    P1 -->|Data User & Log| Admin
    P1 <-->|Read/Write| D1

    %% Accounting Flows - COA
    Accounting -->|Data Akun| P2
    P2 -->|Konfirmasi| Accounting
    P2 <-->|Read/Write| D2

    %% Accounting Flows - Journal
    Accounting -->|Data Jurnal| P3
    P3 -->|Status Approval| Accounting
    P3 <-->|Read/Write| D3
    P3 -->|Update Saldo| D2
    P2 -->|Data Akun| P3

    %% Admin Flows - Product
    Admin -->|Data Product| P4
    P4 -->|Konfirmasi| Admin
    P4 <-->|Read/Write| D4

    %% Admin Flows - Inventory
    Admin -->|Data Stock In| P5
    Admin -->|Data Mutasi| P5
    Admin -->|Data Adjustment| P5
    Admin -->|Data Opname| P5
    P5 -->|Stock Card| Admin
    P5 <-->|Read/Write| D5
    P5 -->|Read Product| D4
    P5 -->|Journal Entry| P3

    %% Admin Flows - Sales
    Admin -->|Data Penjualan| P6
    P6 -->|Invoice| Admin
    P6 <-->|Read/Write| D6
    P6 <-->|Read/Write| D7
    P6 -->|Update Stock| P5
    P6 -->|Journal Entry| P3
    P6 -->|Read Product| D4

    %% Reporting Flows
    Owner -->|Request Laporan| P7
    P7 -->|Laporan Keuangan & Inventory| Owner
    P7 -->|Read| D2
    P7 -->|Read| D3
    P7 -->|Read| D5
    P7 -->|Read| D6

    %% User validation
    D1 -->|Hak Akses| P2
    D1 -->|Hak Akses| P3
    D1 -->|Hak Akses| P4
    D1 -->|Hak Akses| P5
    D1 -->|Hak Akses| P6
    D1 -->|Hak Akses| P7
```

**Keterangan Proses Level 1:**

| No | Proses | Deskripsi |
|---|---|---|
| 1.0 | Manajemen User & Role | Mengelola data user, role, dan hak akses sistem |
| 2.0 | Manajemen Chart of Accounts | Mengelola bagan akun (COA) dan struktur akuntansi |
| 3.0 | Manajemen Journal Entry | Mengelola jurnal akuntansi, approval, dan posting |
| 4.0 | Manajemen Product & Kategori | Mengelola master data produk, kategori, dan satuan |
| 5.0 | Manajemen Inventory/Stock | Mengelola stock in, mutasi, adjustment, dan opname |
| 6.0 | Manajemen Sales | Mengelola transaksi penjualan dan customer |
| 7.0 | Generate Laporan | Menghasilkan berbagai laporan keuangan dan inventory |

**Data Stores:**

| ID | Data Store | Deskripsi |
|---|---|---|
| D1 | Users & Roles | Menyimpan data user, role, dan permissions |
| D2 | Chart of Accounts | Menyimpan bagan akun dan saldo |
| D3 | Journal Entries | Menyimpan jurnal entry dan detail transaksi |
| D4 | Products | Menyimpan master data produk, kategori, unit |
| D5 | Stock Balances | Menyimpan data stock, kartu stock, transaksi |
| D6 | Sales | Menyimpan transaksi penjualan dan detailnya |
| D7 | Customers | Menyimpan data customer |

---

## DFD Level 2 - Proses 1.0 (Manajemen User & Role)

```mermaid
graph TB
    %% External Entities
    Admin((Admin))

    %% Sub Processes
    P11[1.1<br/>Create/Update<br/>User]
    P12[1.2<br/>Manage<br/>Role]
    P13[1.3<br/>Assign<br/>User Role]
    P14[1.4<br/>Activate/Deactivate<br/>User]

    %% Data Stores
    D1A[(D1A: Users)]
    D1B[(D1B: Roles)]
    D1C[(D1C: User Roles)]

    %% Create/Update User Process
    Admin -->|Data User Baru| P11
    P11 -->|Konfirmasi User| Admin
    P11 <-->|Read/Write| D1A

    %% Manage Role Process
    Admin -->|Data Role| P12
    P12 -->|Konfirmasi Role| Admin
    P12 <-->|Read/Write| D1B

    %% Assign User Role Process
    Admin -->|Assign Role to User| P13
    P13 -->|Konfirmasi Assignment| Admin
    P13 -->|Read User| D1A
    P13 -->|Read Role| D1B
    P13 <-->|Write| D1C

    %% Activate/Deactivate User
    Admin -->|Status Change| P14
    P14 -->|Konfirmasi Status| Admin
    P14 <-->|Update Status| D1A
    P14 <-->|Update Status| D1C
```

**Keterangan Sub-Proses 1.0:**

| No | Sub-Proses | Deskripsi |
|---|---|---|
| 1.1 | Create/Update User | Membuat atau mengubah data user |
| 1.2 | Manage Role | Mengelola role dan permissions |
| 1.3 | Assign User Role | Assign role ke user tertentu |
| 1.4 | Activate/Deactivate User | Mengaktifkan atau menonaktifkan user |

---

## DFD Level 2 - Proses 2.0 (Manajemen Chart of Accounts)

```mermaid
graph TB
    %% External Entities
    Accounting((Accounting))

    %% Sub Processes
    P21[2.1<br/>Create/Update<br/>Account]
    P22[2.2<br/>Set Account<br/>Hierarchy]
    P23[2.3<br/>Set Opening<br/>Balance]
    P24[2.4<br/>Activate/Deactivate<br/>Account]

    %% Data Stores
    D2[(D2: Chart of Accounts)]
    D2A[(D2A: Account Balance Histories)]

    %% Create/Update Account Process
    Accounting -->|Data Akun| P21
    P21 -->|Konfirmasi Account| Accounting
    P21 <-->|Read/Write| D2

    %% Set Account Hierarchy
    Accounting -->|Parent-Child Relation| P22
    P22 -->|Konfirmasi Hierarchy| Accounting
    P22 <-->|Update Parent/Level| D2

    %% Set Opening Balance
    Accounting -->|Opening Balance| P23
    P23 -->|Konfirmasi Balance| Accounting
    P23 <-->|Update Balance| D2
    P23 -->|Write History| D2A

    %% Activate/Deactivate Account
    Accounting -->|Status Change| P24
    P24 -->|Konfirmasi Status| Accounting
    P24 <-->|Update Status| D2
```

**Keterangan Sub-Proses 2.0:**

| No | Sub-Proses | Deskripsi |
|---|---|---|
| 2.1 | Create/Update Account | Membuat atau mengubah akun baru |
| 2.2 | Set Account Hierarchy | Mengatur struktur parent-child akun |
| 2.3 | Set Opening Balance | Set saldo awal akun |
| 2.4 | Activate/Deactivate Account | Mengaktifkan atau menonaktifkan akun |

---

## DFD Level 2 - Proses 4.0 (Manajemen Product & Kategori)

```mermaid
graph TB
    %% External Entities
    Admin((Admin))

    %% Sub Processes
    P41[4.1<br/>Manage Product<br/>Category]
    P42[4.2<br/>Manage<br/>Unit]
    P43[4.3<br/>Create/Update<br/>Product]
    P44[4.4<br/>Set Stock<br/>Min/Max]

    %% Data Stores
    D4A[(D4A: Product Categories)]
    D4B[(D4B: Units)]
    D4C[(D4C: Products)]
    D8[(D8: Locations)]

    %% Manage Category Process
    Admin -->|Data Kategori| P41
    P41 -->|Konfirmasi Kategori| Admin
    P41 <-->|Read/Write| D4A

    %% Manage Unit Process
    Admin -->|Data Satuan| P42
    P42 -->|Konfirmasi Satuan| Admin
    P42 <-->|Read/Write| D4B

    %% Create/Update Product
    Admin -->|Data Product| P43
    P43 -->|Konfirmasi Product| Admin
    P43 <-->|Read/Write| D4C
    P43 -->|Read Category| D4A
    P43 -->|Read Unit| D4B
    P43 -->|Read Location| D8

    %% Set Stock Min/Max
    Admin -->|Stock Limits| P44
    P44 -->|Konfirmasi Limits| Admin
    P44 <-->|Update Min/Max| D4C
```

**Keterangan Sub-Proses 4.0:**

| No | Sub-Proses | Deskripsi |
|---|---|---|
| 4.1 | Manage Product Category | Mengelola kategori produk |
| 4.2 | Manage Unit | Mengelola satuan produk |
| 4.3 | Create/Update Product | Membuat atau mengubah data produk |
| 4.4 | Set Stock Min/Max | Set minimum dan maksimum stock |

---

## DFD Level 2 - Proses 5.0 (Manajemen Inventory/Stock)

```mermaid
graph TB
    %% External Entities
    Admin((Admin))

    %% Sub Processes
    P51[5.1<br/>Stock In<br/>Pembelian]
    P52[5.2<br/>Stock Mutation<br/>Transfer]
    P53[5.3<br/>Stock Adjustment<br/>Penyesuaian]
    P54[5.4<br/>Stock Opname<br/>Perhitungan Fisik]
    P55[5.5<br/>Update Stock<br/>Balance]

    %% Data Stores
    D4[(D4: Products)]
    D5A[(D5A: Stock In)]
    D5B[(D5B: Stock Mutations)]
    D5C[(D5C: Stock Adjustments)]
    D5D[(D5D: Stock Opnames)]
    D5E[(D5E: Stock Balances)]
    D5F[(D5F: Stock Cards)]
    D3[(D3: Journal Entries)]

    %% Stock In Process
    Admin -->|Data Barang Masuk| P51
    P51 -->|Konfirmasi Stock In| Admin
    P51 <-->|Read/Write| D5A
    P51 -->|Read Product| D4
    P51 -->|Qty In| P55
    P51 -->|Journal Entry| D3

    %% Stock Mutation Process
    Admin -->|Data Transfer| P52
    P52 -->|Status Mutasi| Admin
    P52 <-->|Read/Write| D5B
    P52 -->|Read Product| D4
    P52 -->|Qty Out/In| P55

    %% Stock Adjustment Process
    Admin -->|Data Penyesuaian| P53
    P53 -->|Konfirmasi Adjustment| Admin
    P53 <-->|Read/Write| D5C
    P53 -->|Read Product| D4
    P53 -->|Check Stock| D5E
    P53 -->|Qty Adjustment| P55
    P53 -->|Journal Entry| D3

    %% Stock Opname Process
    Admin -->|Data Perhitungan| P54
    P54 -->|Laporan Opname| Admin
    P54 <-->|Read/Write| D5D
    P54 -->|Read Product| D4
    P54 -->|Check Stock| D5E
    P54 -->|Qty Difference| P55

    %% Update Stock Balance
    P55 <-->|Update Balance| D5E
    P55 <-->|Write Movement| D5F
```

**Keterangan Sub-Proses 5.0:**

| No | Sub-Proses | Deskripsi |
|---|---|---|
| 5.1 | Stock In (Pembelian) | Mencatat barang masuk dari supplier |
| 5.2 | Stock Mutation (Transfer) | Mencatat transfer antar lokasi/gudang |
| 5.3 | Stock Adjustment (Penyesuaian) | Mencatat penyesuaian stock karena selisih |
| 5.4 | Stock Opname (Perhitungan Fisik) | Mencatat hasil perhitungan fisik stock |
| 5.5 | Update Stock Balance | Memperbarui saldo stock dan kartu stock |

---

## DFD Level 2 - Proses 3.0 (Manajemen Journal Entry)

```mermaid
graph TB
    %% External Entities
    Accounting((Staff Accounting))
    Owner((Owner))

    %% Sub Processes
    P31[3.1<br/>Create/Draft<br/>Journal Entry]
    P32[3.2<br/>Review &<br/>Submit]
    P33[3.3<br/>Approval<br/>Process]
    P34[3.4<br/>Posting<br/>Journal]
    P35[3.5<br/>Update<br/>Account Balance]

    %% Data Stores
    D2[(D2: Chart of Accounts)]
    D3A[(D3A: Journal Entries)]
    D3B[(D3B: Journal Entry Details)]
    D3C[(D3C: Journal Approvals)]
    D3D[(D3D: Journal Attachments)]
    D1[(D1: Users)]

    %% Create Journal Process
    Accounting -->|Data Jurnal| P31
    P31 -->|Draft Jurnal| Accounting
    P31 <-->|Write| D3A
    P31 <-->|Write Details| D3B
    P31 -->|Read Accounts| D2
    P31 -->|Upload Attachment| D3D

    %% Review & Submit
    Accounting -->|Submit for Approval| P32
    P32 -->|Validation Result| Accounting
    P32 <-->|Update Status| D3A
    P32 -->|Read Details| D3B
    P32 -->|Validate Balance| D2

    %% Approval Process
    Owner -->|Approval Decision| P33
    Accounting -->|Approval Decision| P33
    P33 -->|Approval Status| Owner
    P33 -->|Approval Status| Accounting
    P33 <-->|Write Approval| D3C
    P33 <-->|Read Journal| D3A
    P33 -->|Check Approver| D1

    %% Posting Process
    Accounting -->|Post Journal| P34
    P34 -->|Posting Result| Accounting
    P34 <-->|Update Status| D3A
    P34 -->|Read Details| D3B
    P34 -->|Check Approval| D3C
    P34 -->|Update Balance| P35

    %% Update Account Balance
    P35 <-->|Update| D2
    D3B -->|Debit/Credit| P35
```

**Keterangan Sub-Proses 3.0:**

| No | Sub-Proses | Deskripsi |
|---|---|---|
| 3.1 | Create/Draft Journal Entry | Membuat draft jurnal baru dengan detail debit/kredit |
| 3.2 | Review & Submit | Validasi dan submit jurnal untuk approval |
| 3.3 | Approval Process | Proses persetujuan jurnal oleh approver |
| 3.4 | Posting Journal | Posting jurnal yang sudah diapprove |
| 3.5 | Update Account Balance | Memperbarui saldo akun setelah posting |

---

## DFD Level 2 - Proses 6.0 (Manajemen Sales)

```mermaid
graph TB
    %% External Entities
    Admin((Admin))

    %% Sub Processes
    P61[6.1<br/>Input<br/>Sales Order]
    P62[6.2<br/>Calculate<br/>Total & Tax]
    P63[6.3<br/>Process<br/>Payment]
    P64[6.4<br/>Post<br/>Sales]
    P65[6.5<br/>Generate<br/>Invoice]

    %% Data Stores
    D6[(D6: Sales)]
    D6A[(D6A: Sale Details)]
    D7[(D7: Customers)]
    D4[(D4: Products)]
    D5E[(D5E: Stock Balances)]
    D3[(D3: Journal Entries)]

    %% Input Sales Order
    Admin -->|Input Penjualan| P61
    P61 -->|Sales Order| Admin
    P61 <-->|Write| D6
    P61 <-->|Write Details| D6A
    P61 -->|Read/Write Customer| D7
    P61 -->|Read Product| D4
    P61 -->|Check Stock| D5E

    %% Calculate Total
    P61 -->|Sales Data| P62
    P62 -->|Total & Tax| P61
    P62 -->|Read Details| D6A
    P62 <-->|Update Total| D6

    %% Process Payment
    Admin -->|Input Payment| P63
    P63 -->|Konfirmasi Payment| Admin
    P63 <-->|Update Payment| D6

    %% Post Sales
    Admin -->|Finalize Sale| P64
    P64 -->|Status| Admin
    P64 <-->|Update Status| D6
    P64 -->|Read Details| D6A
    P64 -->|Reduce Stock| D5E
    P64 -->|Create Journal| D3

    %% Generate Invoice
    P64 -->|Posted Sale| P65
    P65 -->|Invoice| Admin
    P65 -->|Read Sale| D6
    P65 -->|Read Details| D6A
    P65 -->|Read Customer| D7
```

**Keterangan Sub-Proses 6.0:**

| No | Sub-Proses | Deskripsi |
|---|---|---|
| 6.1 | Input Sales Order | Input data penjualan dan item yang dijual |
| 6.2 | Calculate Total & Tax | Hitung subtotal, pajak, dan total penjualan |
| 6.3 | Process Payment | Proses pembayaran dari customer |
| 6.4 | Post Sales | Posting penjualan, kurangi stock, buat jurnal |
| 6.5 | Generate Invoice | Generate invoice/nota untuk customer |

---

## DFD Level 2 - Proses 7.0 (Generate Laporan)

```mermaid
graph TB
    %% External Entities
    Owner((Owner))

    %% Sub Processes
    P71[7.1<br/>Generate Laporan<br/>Keuangan]
    P72[7.2<br/>Generate Laporan<br/>Stock]
    P73[7.3<br/>Generate Laporan<br/>Sales]
    P74[7.4<br/>Export<br/>Laporan]

    %% Data Stores
    D2[(D2: Chart of Accounts)]
    D3[(D3: Journal Entries)]
    D5[(D5: Stock Balances)]
    D5F[(D5F: Stock Cards)]
    D6[(D6: Sales)]
    D4[(D4: Products)]

    %% Generate Laporan Keuangan
    Owner -->|Request Laporan Keuangan| P71
    P71 -->|Laporan Keuangan| Owner
    P71 -->|Read COA| D2
    P71 -->|Read Journals| D3

    %% Generate Laporan Stock
    Owner -->|Request Laporan Stock| P72
    P72 -->|Laporan Inventory| Owner
    P72 -->|Read Balances| D5
    P72 -->|Read Stock Cards| D5F
    P72 -->|Read Products| D4

    %% Generate Laporan Sales
    Owner -->|Request Laporan Sales| P73
    P73 -->|Laporan Penjualan| Owner
    P73 -->|Read Sales| D6
    P73 -->|Read Products| D4

    %% Export Laporan
    Owner -->|Request Export| P74
    P71 -->|Report Data| P74
    P72 -->|Report Data| P74
    P73 -->|Report Data| P74
    P74 -->|File Export| Owner
```

**Keterangan Sub-Proses 7.0:**

| No | Sub-Proses | Deskripsi |
|---|---|---|
| 7.1 | Generate Laporan Keuangan | Generate neraca, laba rugi, buku besar |
| 7.2 | Generate Laporan Stock | Generate laporan stock, kartu stock |
| 7.3 | Generate Laporan Sales | Generate laporan penjualan |
| 7.4 | Export Laporan | Export laporan ke PDF/Excel |

---

## Rangkuman DFD

### DFD Level 0
- Menggambarkan sistem  sebagai satu kesatuan
- Menunjukkan 3 external entities: Owner (laporan), Admin (inventory & sales), Accounting (akuntansi)
- Menunjukkan aliran data utama dari/ke sistem

### DFD Level 1
- Memecah sistem menjadi 7 proses utama
- Menunjukkan 7 data stores utama
- Menunjukkan aliran data antar proses dan data stores

### DFD Level 2
- Detail dari 7 proses utama:
  - **Proses 1.0**: Manajemen User & Role (4 sub-proses)
  - **Proses 2.0**: Manajemen Chart of Accounts (4 sub-proses)
  - **Proses 3.0**: Manajemen Journal Entry (5 sub-proses)
  - **Proses 4.0**: Manajemen Product & Kategori (4 sub-proses)
  - **Proses 5.0**: Manajemen Inventory (5 sub-proses)
  - **Proses 6.0**: Manajemen Sales (5 sub-proses)
  - **Proses 7.0**: Generate Laporan (4 sub-proses)

---

## Catatan Penting

1. **Konvensi Penomoran:**
   - Level 0: Sistem tunggal
   - Level 1: Proses utama (1.0, 2.0, 3.0, ...)
   - Level 2: Sub-proses (3.1, 3.2, 3.3, ...)

2. **Data Stores:**
   - Menggunakan notasi D1, D2, D3, dst untuk data stores utama
   - Sub-stores menggunakan notasi D3A, D3B, dst

3. **Aliran Data:**
   - Panah menunjukkan arah aliran data
   - Setiap aliran data harus diberi label yang jelas
   - Tidak ada control flow, hanya data flow

4. **External Entities:**
   - Digambarkan sebagai persegi atau lingkaran
   - Merepresentasikan sumber atau tujuan data di luar sistem

5. **Proses:**
   - Digambarkan sebagai lingkaran atau persegi dengan sudut melengkung
   - Setiap proses harus memiliki input dan output
   - Diberi nomor untuk identifikasi

---

**Dibuat untuk:** Sistem  - Sistem Informasi Akuntansi dan Inventory Management
**Versi:** 1.0
**Tanggal:** 24 Desember 2025
