# STRUKTUR TABEL DATABASE SISTEM INFORMASI AKUNTANSI DAN INVENTORY MANAGEMENT

## Database: `db_ta_sintiya_2025`

---

## 1. USER MANAGEMENT & ACCESS CONTROL

### 1.1 Tabel: `users`
Tabel ini menyimpan seluruh data pengguna sistem yang dapat mengakses aplikasi. Setiap user memiliki informasi dasar seperti nama, email, password terenkripsi, serta data tambahan seperti nomor telepon, alamat, foto profil, dan tanggal lahir. Status user dapat diatur aktif atau non-aktif untuk mengontrol akses ke sistem.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| name | varchar(255) | Nama lengkap user |
| email | varchar(255) | Email (unique) untuk login |
| password | varchar(255) | Password terenkripsi dengan hashing |
| status | enum | Status user (active/inactive) |
| phone_number | varchar(255) | Nomor telepon user |
| address | text | Alamat lengkap user |
| avatar_url | varchar(255) | URL path foto profil user |
| date_of_birth | date | Tanggal lahir user |

---

### 1.2 Tabel: `roles`
Tabel ini menyimpan daftar peran (role) yang dapat diberikan kepada user dalam sistem. Setiap role memiliki nama unik dan kumpulan permission yang menentukan hak akses user. Role dapat berupa Admin, Accounting, Owner, atau role custom lainnya. Role dapat diaktifkan atau dinonaktifkan sesuai kebutuhan.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| name | varchar(255) | Nama role yang unik (slug format) |
| display_name | varchar(255) | Nama tampilan role yang user-friendly |
| description | text | Penjelasan fungsi dan tanggung jawab role |
| permissions | json | Array permissions yang dimiliki role dalam format JSON |
| is_active | boolean | Status aktif role (1=aktif, 0=non-aktif) |

---

### 1.3 Tabel: `permissions`
Tabel ini menyimpan daftar hak akses (permission) yang tersedia dalam sistem. Permission mendefinisikan aksi-aksi spesifik yang dapat dilakukan user, seperti create, read, update, delete untuk setiap modul. Permission dapat dikelompokkan berdasarkan modul (group) untuk memudahkan manajemen.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| name | varchar(255) | Nama permission unik (format: module.action) |
| display_name | varchar(255) | Nama tampilan permission yang mudah dipahami |
| description | text | Penjelasan detail tentang fungsi permission |
| group | varchar(255) | Grup/kategori permission berdasarkan modul |
| is_active | boolean | Status aktif permission |

---

### 1.4 Tabel: `user_roles`
Tabel relasi many-to-many yang menghubungkan user dengan role yang dimilikinya. Satu user dapat memiliki multiple roles, dan satu role dapat diberikan ke banyak user. Terdapat fitur expiry date untuk role assignment yang bersifat sementara.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| user_id | bigint | Foreign Key ke tabel users |
| role_id | bigint | Foreign Key ke tabel roles |
| is_active | boolean | Status aktif assignment role untuk user tertentu |
| assigned_at | datetime | Waktu kapan role diberikan ke user |
| expires_at | datetime | Waktu expiry role (null jika permanent) |

---

### 1.5 Tabel: `role_permissions`
Tabel relasi many-to-many yang menghubungkan role dengan permission. Tabel ini mendefinisikan permission apa saja yang dimiliki oleh sebuah role. Kombinasi role_id dan permission_id harus unik untuk mencegah duplikasi assignment.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| role_id | bigint | Foreign Key ke tabel roles |
| permission_id | bigint | Foreign Key ke tabel permissions |

---

### 1.6 Tabel: `user_permissions`
Tabel untuk memberikan permission khusus langsung ke user, melewati role. Berguna untuk memberikan hak akses spesifik ke user tertentu tanpa harus membuat role baru. Permission yang diberikan di sini akan ditambahkan (union) dengan permission dari role user.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| user_id | bigint | Foreign Key ke tabel users |
| permission_id | bigint | Foreign Key ke tabel permissions |

---

## 2. CHART OF ACCOUNTS (COA) - AKUNTANSI

### 2.1 Tabel: `chart_of_accounts`
Tabel ini menyimpan Chart of Accounts (COA) atau daftar perkiraan akuntansi yang menjadi fondasi sistem pembukuan. Setiap akun memiliki kode unik, tipe akun (asset, liability, equity, revenue, expense), dan struktur hierarki parent-child dengan maksimal 5 level. Tabel ini juga menyimpan saldo berjalan (current_balance) untuk optimasi performa query, serta audit trail lengkap untuk tracking perubahan.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| account_code | varchar(20) | Kode akun unik (contoh: 1-1100 untuk Kas) |
| account_name | varchar(255) | Nama akun (contoh: Kas, Bank, Piutang) |
| account_type | enum | Tipe akun: asset, liability, equity, revenue, expense |
| normal_balance | enum | Saldo normal akun (debit atau credit) |
| parent_id | bigint | Foreign Key ke chart_of_accounts untuk hierarki parent-child |
| level | int | Level hierarki akun (1-5), level 1 untuk parent utama |
| is_active | boolean | Status aktif akun untuk filtering akun yang masih digunakan |
| description | text | Penjelasan detail tentang fungsi dan penggunaan akun |
| opening_balance | decimal(15,2) | Saldo awal akun saat setup sistem |
| current_balance | decimal(15,2) | Saldo saat ini (cached untuk performa) |
| balance_updated_at | timestamp | Timestamp terakhir kali saldo dihitung/diupdate |
| metadata | json | Data tambahan fleksibel dalam format JSON |
| created_by | varchar(255) | User ID atau nama yang membuat akun |
| updated_by | varchar(255) | User ID atau nama yang terakhir update akun |

**Constraints & Triggers**:
- Saldo (opening_balance & current_balance) tidak boleh negatif
- Level hierarki dibatasi antara 1 sampai 5
- Parent ID tidak boleh membentuk circular reference (dicek via trigger)
- Trigger otomatis update balance_updated_at saat saldo berubah

---

### 2.2 Tabel: `account_balance_histories`
Tabel ini menyimpan snapshot history saldo akun per periode waktu tertentu (misalnya bulanan atau per periode akuntansi). Berguna untuk membuat laporan historis dan tracking perubahan saldo akun dari waktu ke waktu tanpa harus recalculate dari jurnal.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| chart_of_account_id | bigint | Foreign Key ke chart_of_accounts |
| balance | decimal(15,2) | Saldo akun pada akhir periode |
| debit_total | decimal(15,2) | Total semua transaksi debit dalam periode |
| credit_total | decimal(15,2) | Total semua transaksi kredit dalam periode |
| period_start | date | Tanggal awal periode |
| period_end | date | Tanggal akhir periode |
| calculated_by | varchar(255) | User atau sistem yang menghitung saldo |

---

### 2.3 Tabel: `chart_of_account_audits`
Tabel audit trail untuk mencatat semua perubahan yang terjadi pada Chart of Accounts. Setiap create, update, atau delete akan direkam lengkap dengan nilai lama dan baru dalam format JSON, serta informasi user yang melakukan perubahan. Berguna untuk compliance, debugging, dan tracking siapa mengubah apa dan kapan.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| chart_of_account_id | bigint | Foreign Key ke chart_of_accounts yang diaudit |
| event_type | varchar(255) | Tipe event (created, updated, deleted) |
| old_values | json | Nilai data sebelum perubahan dalam format JSON |
| new_values | json | Nilai data setelah perubahan dalam format JSON |
| user_id | varchar(255) | ID user yang melakukan perubahan |
| user_name | varchar(255) | Nama user yang melakukan perubahan |
| ip_address | varchar(255) | IP address sumber request |
| user_agent | varchar(255) | Browser/device user agent |

---

## 3. JOURNAL ENTRIES - JURNAL

### 3.1 Tabel: `journal_entries`
Tabel master/header untuk jurnal akuntansi. Menyimpan informasi jurnal seperti nomor jurnal, tanggal, deskripsi, dan status. Jurnal bisa berstatus draft (masih bisa diedit), posted (sudah final dan mempengaruhi saldo), atau cancelled (dibatalkan). Mendukung multi-currency dengan field currency dan exchange_rate. Terdapat approval workflow dengan tracking siapa yang membuat, approve, dan posting jurnal.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| entry_number | varchar(255) | Nomor jurnal unik (auto-generated atau manual) |
| entry_date | date | Tanggal transaksi jurnal |
| reference_number | varchar(255) | Nomor referensi eksternal (invoice, PO, dll) |
| description | text | Deskripsi/keterangan jurnal |
| entry_type | enum | Tipe: general (umum), adjustment (penyesuaian), closing (penutup), opening (pembukaan) |
| status | enum | Status: draft (draft), posted (sudah posting), cancelled (dibatalkan) |
| total_debit | decimal(15,2) | Total nilai debit (harus sama dengan total_credit) |
| total_credit | decimal(15,2) | Total nilai kredit (harus sama dengan total_debit) |
| currency | varchar(3) | Kode mata uang ISO (default: IDR) |
| exchange_rate | decimal(10,4) | Kurs konversi ke mata uang base (default: 1.0000) |
| created_by | bigint | Foreign Key ke users yang membuat jurnal |
| posted_by | bigint | Foreign Key ke users yang posting jurnal |
| posted_at | timestamp | Waktu kapan jurnal diposting |
| approved_by | bigint | Foreign Key ke users yang approve jurnal |
| approved_at | timestamp | Waktu kapan jurnal diapprove |
| updated_by | bigint | Foreign Key ke users yang terakhir update |
| metadata | json | Data tambahan fleksibel |

---

### 3.2 Tabel: `journal_entry_details`
Tabel detail/baris dari jurnal yang berisi entry debit dan kredit untuk setiap akun. Satu jurnal entry dapat memiliki banyak detail lines. Setiap line menunjuk ke akun tertentu dengan jumlah debit atau kredit. Total semua debit harus sama dengan total semua kredit (balanced journal).

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| journal_entry_id | bigint | Foreign Key ke journal_entries |
| account_id | bigint | Foreign Key ke chart_of_accounts yang di-debit/kredit |
| transaction_type | enum | Tipe: debit atau credit |
| amount | decimal(15,2) | Jumlah transaksi |
| debit_amount | decimal(15,2) | Jumlah debit (jika transaction_type=debit) |
| credit_amount | decimal(15,2) | Jumlah kredit (jika transaction_type=credit) |
| quantity | decimal(15,4) | Kuantitas (opsional untuk tracking qty) |
| unit_price | decimal(15,2) | Harga satuan (opsional) |
| tax_rate | decimal(5,2) | Persentase pajak (jika ada) |
| tax_amount | decimal(15,2) | Jumlah pajak |
| reconciliation_id | varchar(255) | ID untuk rekonsiliasi bank/akun |
| description | text | Deskripsi khusus untuk line ini |
| department_id | bigint | Foreign Key ke locations sebagai cost center/departemen |
| project_id | bigint | ID proyek (reserved untuk future feature) |

---

### 3.3 Tabel: `journal_entry_approvals`
Tabel untuk workflow approval jurnal. Menyimpan data siapa yang approve atau reject jurnal, kapan, dan dengan catatan apa. Mendukung multi-level approval jika diperlukan. Status approval bisa pending (menunggu), approved (disetujui), atau rejected (ditolak).

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| journal_entry_id | bigint | Foreign Key ke journal_entries yang diapprove |
| user_id | bigint | Foreign Key ke users yang bertindak sebagai approver |
| status | enum | Status: pending, approved, rejected |
| notes | text | Catatan atau alasan approve/reject |
| approved_at | timestamp | Waktu approve atau reject dilakukan |

---

### 3.4 Tabel: `journal_entry_attachments`
Tabel untuk menyimpan file attachment/lampiran yang terkait dengan jurnal. Misalnya scan invoice, bukti transfer, foto, atau dokumen pendukung lainnya. File disimpan di storage dan metadata-nya disimpan di tabel ini.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| journal_entry_id | bigint | Foreign Key ke journal_entries |
| filename | varchar(255) | Nama file di storage (biasanya hashed) |
| original_filename | varchar(255) | Nama file asli yang diupload user |
| mime_type | varchar(255) | Tipe MIME file (image/jpeg, application/pdf, dll) |
| file_size | bigint | Ukuran file dalam bytes |
| file_path | varchar(255) | Path lengkap file di storage |
| uploaded_by | bigint | Foreign Key ke users yang upload file |

---

### 3.5 Tabel: `journal_entry_revisions`
Tabel untuk menyimpan history revisi jurnal. Setiap kali jurnal diubah, perubahan akan dicatat di sini dengan nomor revisi yang increment. Berguna untuk audit dan rollback jika diperlukan.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| journal_entry_id | bigint | Foreign Key ke journal_entries |
| revision_number | int | Nomor revisi yang increment (1, 2, 3, ...) |
| changes | json | Data perubahan dalam format JSON (old vs new) |
| revised_by | bigint | Foreign Key ke users yang melakukan revisi |
| revision_notes | text | Catatan tentang alasan atau detail revisi |

---

## 4. PRODUCTS & INVENTORY

### 4.1 Tabel: `product_categories`
Tabel untuk mengelompokkan produk berdasarkan kategori. Mendukung hierarki kategori parent-child untuk membuat sub-kategori. Misalnya kategori "Elektronik" dengan sub-kategori "Laptop", "Smartphone", dll.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| code | varchar(50) | Kode kategori unik |
| name | varchar(255) | Nama kategori produk |
| description | text | Deskripsi kategori |
| parent_id | bigint | Foreign Key ke product_categories untuk sub-kategori |
| is_active | boolean | Status aktif kategori |

---

### 4.2 Tabel: `units`
Tabel untuk menyimpan satuan unit produk seperti Kg, Liter, Pcs, Box, Dozen, dll. Setiap produk harus memiliki satuan unit untuk tracking quantity.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| code | varchar(20) | Kode satuan unik (KG, PCS, LTR, dll) |
| name | varchar(100) | Nama satuan (Kilogram, Pieces, Liter, dll) |
| symbol | varchar(10) | Simbol satuan untuk tampilan (kg, pcs, l) |
| description | text | Deskripsi satuan |
| is_active | boolean | Status aktif satuan |

---

### 4.3 Tabel: `locations`
Tabel untuk menyimpan data lokasi fisik seperti gudang, warehouse, cabang, atau department. Mendukung hierarki parent-child untuk struktur organisasi lokasi. Setiap lokasi bisa memiliki koordinat GPS dan warna identitas untuk visualisasi di map atau dashboard.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| code | varchar(255) | Kode lokasi unik |
| name | varchar(255) | Nama lokasi (Gudang A, Cabang Jakarta, dll) |
| description | text | Deskripsi lokasi |
| address | varchar(255) | Alamat lengkap lokasi |
| city | varchar(255) | Kota |
| state | varchar(255) | Provinsi/state |
| country | varchar(255) | Negara |
| postal_code | varchar(255) | Kode pos |
| latitude | decimal(10,8) | Koordinat latitude GPS |
| longitude | decimal(11,8) | Koordinat longitude GPS |
| color | varchar(7) | Warna identitas lokasi dalam hex (#10B981) |
| is_active | boolean | Status aktif lokasi |
| parent_id | bigint | Foreign Key ke locations untuk hierarki |
| metadata | json | Data tambahan fleksibel |

---

### 4.4 Tabel: `products`
Tabel master data produk/barang. Menyimpan semua informasi produk termasuk kode, nama, kategori, satuan, harga beli dan jual, serta batas minimum dan maksimum stock. Product type membedakan antara bahan baku (raw_material), barang jadi (finished_goods), atau consumable.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| product_code | varchar(50) | Kode produk unik (SKU) |
| product_name | varchar(255) | Nama produk |
| description | text | Deskripsi detail produk |
| product_type | enum | Tipe: raw_material, finished_goods, consumable |
| category_id | bigint | Foreign Key ke product_categories |
| unit_id | bigint | Foreign Key ke units |
| purchase_price | decimal(15,2) | Harga beli/modal produk |
| selling_price | decimal(15,2) | Harga jual produk |
| minimum_stock | int | Batas minimum stock untuk warning |
| maximum_stock | int | Batas maksimum stock untuk reorder |
| location_id | bigint | Foreign Key ke locations (lokasi default produk) |
| is_active | boolean | Status aktif produk |

---

## 5. STOCK TRANSACTIONS

### 5.1 Tabel: `stock_in`
Tabel master/header untuk transaksi barang masuk atau pembelian. Mencatat informasi supplier, tanggal, lokasi penerimaan barang, dan total nilai pembelian. Status draft memungkinkan transaksi disimpan sementara sebelum diposting final.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| transaction_number | varchar(255) | Nomor transaksi barang masuk (unique, auto-generated) |
| transaction_date | date | Tanggal penerimaan barang |
| location_id | bigint | Foreign Key ke locations (gudang penerima) |
| total_price | decimal(15,2) | Total nilai pembelian seluruh item |
| supplier_name | varchar(255) | Nama supplier/pemasok |
| reference_number | varchar(255) | Nomor PO, Invoice supplier, atau dokumen referensi |
| notes | text | Catatan tambahan transaksi |
| status | enum | Status: draft, posted, cancelled |
| created_by | bigint | Foreign Key ke users (pembuat transaksi) |
| posted_by | bigint | Foreign Key ke users (yang posting) |
| posted_at | timestamp | Waktu posting transaksi |

---

### 5.2 Tabel: `stock_in_details`
Tabel detail item-item barang yang masuk dalam satu transaksi stock_in. Setiap baris menunjukkan produk, quantity, harga satuan, dan total harga. Saat diposting, quantity akan menambah stock balance.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| stock_in_id | bigint | Foreign Key ke stock_in |
| product_id | bigint | Foreign Key ke products |
| quantity | decimal(15,2) | Jumlah barang yang masuk |
| unit_price | decimal(15,2) | Harga satuan barang |
| total_price | decimal(15,2) | Total harga (quantity × unit_price) |
| notes | text | Catatan khusus item ini |

---

### 5.3 Tabel: `stock_mutations`
Tabel master untuk transaksi transfer/mutasi stock antar lokasi. Misalnya transfer barang dari Gudang A ke Gudang B. Memiliki workflow status: draft → pending → approved → completed. Transfer bisa di-reject dengan alasan yang dicatat di rejection_reason.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| transaction_number | varchar(255) | Nomor transaksi mutasi (unique) |
| transaction_date | date | Tanggal transaksi mutasi |
| from_location_id | bigint | Foreign Key ke locations (lokasi asal) |
| to_location_id | bigint | Foreign Key ke locations (lokasi tujuan) |
| reference_number | varchar(255) | Nomor referensi eksternal |
| notes | text | Catatan transaksi |
| status | enum | Status: draft, pending, approved, completed, cancelled |
| created_by | bigint | Foreign Key ke users (pembuat) |
| submitted_by | bigint | Foreign Key ke users (yang submit untuk approval) |
| submitted_at | timestamp | Waktu submit |
| approved_by | bigint | Foreign Key ke users (approver) |
| approved_at | timestamp | Waktu approval |
| completed_by | bigint | Foreign Key ke users (yang menyelesaikan) |
| completed_at | timestamp | Waktu penyelesaian |
| rejection_reason | text | Alasan penolakan (jika ditolak) |

---

### 5.4 Tabel: `stock_mutation_details`
Tabel detail item-item yang ditransfer dalam transaksi mutasi. Mencatat produk, quantity yang ditransfer, dan available stock di lokasi asal untuk validasi.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| stock_mutation_id | bigint | Foreign Key ke stock_mutations |
| product_id | bigint | Foreign Key ke products |
| quantity | decimal(15,2) | Jumlah yang ditransfer |
| available_stock | decimal(15,2) | Stock tersedia di lokasi asal saat transaksi dibuat |
| notes | text | Catatan item |

---

### 5.5 Tabel: `stock_adjustments`
Tabel master untuk penyesuaian stock karena selisih, kerusakan, kadaluarsa, atau alasan lain yang menyebabkan perubahan quantity stock. Adjustment bisa menambah (increase) atau mengurangi (decrease) stock.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| adjustment_number | varchar(255) | Nomor adjustment (unique) |
| adjustment_date | date | Tanggal adjustment |
| description | text | Deskripsi alasan adjustment |
| total_items | int | Total item yang di-adjust |
| location_id | bigint | Foreign Key ke locations |
| notes | text | Catatan tambahan |
| status | enum | Status: draft, posted, cancelled |
| created_by | bigint | Foreign Key ke users (pembuat) |
| approved_by | bigint | Foreign Key ke users (approver) |
| approved_at | timestamp | Waktu approval |

---

### 5.6 Tabel: `stock_adjustment_details`
Tabel detail item adjustment dengan informasi jumlah di sistem vs jumlah aktual, selisihnya, dan tipe adjustment (increase/decrease).

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| stock_adjustment_id | bigint | Foreign Key ke stock_adjustments |
| product_id | bigint | Foreign Key ke products |
| system_quantity | decimal(15,2) | Jumlah stock menurut sistem |
| actual_quantity | decimal(15,2) | Jumlah stock aktual/fisik |
| difference_quantity | decimal(15,2) | Selisih (actual - system) |
| adjustment_type | enum | Tipe: increase (tambah) atau decrease (kurang) |
| reason | varchar(255) | Alasan adjustment (rusak, hilang, kadaluarsa, dll) |
| notes | text | Catatan detail |

---

### 5.7 Tabel: `stock_opnames`
Tabel master untuk stock opname/physical count. Stock opname adalah proses menghitung fisik seluruh barang di gudang dan membandingkan dengan data sistem untuk menemukan selisih. Status in_progress menandakan proses perhitungan sedang berjalan.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| opname_number | varchar(255) | Nomor stock opname (unique) |
| opname_date | date | Tanggal stock opname |
| location_id | bigint | Foreign Key ke locations (lokasi yang di-opname) |
| total_items | int | Total item yang dihitung |
| description | text | Deskripsi kegiatan opname |
| notes | text | Catatan hasil opname |
| status | enum | Status: draft, in_progress, completed, cancelled |
| created_by | bigint | Foreign Key ke users (inisiator) |
| completed_by | bigint | Foreign Key ke users (yang menyelesaikan) |
| completed_at | timestamp | Waktu penyelesaian |

---

### 5.8 Tabel: `stock_opname_details`
Tabel detail hasil perhitungan stock opname per produk. Mencatat quantity sistem, quantity hasil hitung fisik, selisihnya, dan siapa yang menghitung.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| stock_opname_id | bigint | Foreign Key ke stock_opnames |
| product_id | bigint | Foreign Key ke products |
| system_quantity | int | Jumlah stock menurut sistem |
| physical_quantity | int | Jumlah stock hasil hitung fisik |
| difference_quantity | int | Selisih (physical - system) |
| adjustment_type | enum | Tipe: increase atau decrease |
| notes | text | Catatan hasil perhitungan |
| counted_by | bigint | Foreign Key ke users (penghitung) |

---

### 5.9 Tabel: `stock_balances`
Tabel snapshot saldo stock untuk setiap kombinasi produk-lokasi. Ini adalah tabel cache yang menyimpan current balance untuk performa query. Diupdate otomatis setiap kali ada transaksi stock (in, out, mutation, adjustment).

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| product_id | bigint | Foreign Key ke products |
| location_id | bigint | Foreign Key ke locations |
| current_balance | decimal(10,2) | Saldo stock saat ini |
| minimum_stock | decimal(10,2) | Batas minimum stock untuk alert |
| maximum_stock | decimal(10,2) | Batas maksimum stock |
| status | varchar(255) | Status: in_stock, low_stock, out_of_stock |
| last_transaction_date | date | Tanggal transaksi stock terakhir |
| last_transaction_type | varchar(255) | Tipe transaksi terakhir (in/out/adjustment/mutation) |

**Note**: Kombinasi product_id dan location_id unik per row

---

### 5.10 Tabel: `stock_cards`
Tabel kartu stock yang mencatat semua pergerakan stock secara kronologis. Setiap transaksi yang mempengaruhi stock akan menambah record di sini. Berguna untuk audit trail dan membuat laporan stock card.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| product_id | bigint | Foreign Key ke products |
| location_id | bigint | Foreign Key ke locations |
| transaction_date | date | Tanggal transaksi |
| transaction_type | varchar(255) | Tipe: stock_in, stock_out, mutation_in, mutation_out, adjustment |
| reference_id | bigint | ID transaksi sumber (stock_in_id, sale_id, dll) |
| reference_number | varchar(255) | Nomor transaksi sumber |
| quantity_in | int | Jumlah barang masuk (plus) |
| quantity_out | int | Jumlah barang keluar (minus) |
| balance | int | Saldo stock setelah transaksi ini |
| unit_price | decimal(15,2) | Harga satuan saat transaksi |
| notes | text | Catatan transaksi |

---

## 6. SALES TRANSACTIONS

### 6.1 Tabel: `customers`
Tabel master data customer/pelanggan. Menyimpan informasi kontak customer untuk keperluan transaksi penjualan dan laporan. Customer bisa diatur aktif atau non-aktif.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| customer_code | varchar(20) | Kode customer unik (auto-generated atau manual) |
| customer_name | varchar(255) | Nama customer |
| address | text | Alamat lengkap customer |
| phone | varchar(20) | Nomor telepon customer |
| email | varchar(255) | Email customer |
| notes | text | Catatan tentang customer |
| status | enum | Status: active atau inactive |

---

### 6.2 Tabel: `sales`
Tabel master transaksi penjualan. Menyimpan informasi header penjualan termasuk customer, tanggal, lokasi penjualan, total amount, metode pembayaran, dan status. Saat diposting, akan mengurangi stock dan generate journal entry.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| transaction_number | varchar(20) | Nomor transaksi penjualan (unique) |
| transaction_date | date | Tanggal transaksi penjualan |
| customer_id | bigint | Foreign Key ke customers (null untuk cash/walk-in customer) |
| location_id | bigint | Foreign Key ke locations (lokasi penjualan) |
| subtotal | decimal(15,2) | Subtotal sebelum pajak dan diskon |
| tax_amount | decimal(15,2) | Jumlah pajak (PPN, dll) |
| discount_amount | decimal(15,2) | Jumlah diskon yang diberikan |
| total_amount | decimal(15,2) | Total yang harus dibayar (subtotal + tax - discount) |
| paid_amount | decimal(15,2) | Jumlah uang yang dibayar customer |
| change_amount | decimal(15,2) | Jumlah kembalian (jika cash) |
| payment_method | enum | Metode: cash, transfer, credit |
| notes | text | Catatan transaksi |
| status | enum | Status: draft, posted, cancelled |
| created_by | bigint | Foreign Key ke users (kasir/sales) |
| posted_by | bigint | Foreign Key ke users (yang posting) |
| posted_at | timestamp | Waktu posting |

---

### 6.3 Tabel: `sale_details`
Tabel detail item-item yang dijual dalam satu transaksi penjualan. Setiap baris adalah satu produk dengan quantity, harga, diskon, pajak, dan total.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| sale_id | bigint | Foreign Key ke sales |
| product_id | bigint | Foreign Key ke products |
| quantity | decimal(15,2) | Jumlah produk yang dijual |
| unit_price | decimal(15,2) | Harga satuan produk |
| discount_percent | decimal(5,2) | Persentase diskon item (0-100) |
| discount_amount | decimal(15,2) | Jumlah diskon dalam rupiah |
| tax_percent | decimal(5,2) | Persentase pajak item (biasanya 11% untuk PPN) |
| tax_amount | decimal(15,2) | Jumlah pajak dalam rupiah |
| total_price | decimal(15,2) | Total harga item (unit_price × qty - discount + tax) |
| notes | text | Catatan khusus item |

---

## 7. SYSTEM TABLES

### 7.1 Tabel: `settings`
Tabel untuk menyimpan pengaturan global sistem dan informasi perusahaan. Biasanya hanya ada 1 row dalam tabel ini. Data digunakan untuk branding laporan, email notification, dan konfigurasi sistem.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| logo_sistem | varchar(255) | Path ke file logo sistem/perusahaan |
| nama_sistem | varchar(255) | Nama aplikasi/sistem |
| deskripsi_sistem | text | Deskripsi singkat sistem |
| nama_perusahaan | varchar(255) | Nama perusahaan pengguna sistem |
| alamat_lengkap | text | Alamat lengkap perusahaan |
| email_perusahaan | varchar(255) | Email resmi perusahaan |
| nomor_telepon | varchar(255) | Nomor telepon perusahaan |
| footer_text | text | Teks footer yang muncul di laporan/invoice |

---

### 7.2 Tabel: `password_reset_otps`
Tabel untuk menyimpan OTP (One-Time Password) saat user melakukan reset password. OTP dikirim via email dan memiliki waktu expiry. Tracking attempts untuk mencegah brute force attack.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| email | varchar(255) | Email user yang request reset password |
| otp | varchar(60) | Kode OTP terenkripsi dengan hashing |
| expires_at | timestamp | Waktu expiry OTP (biasanya 15-30 menit dari create) |
| used_at | timestamp | Waktu OTP digunakan (null jika belum digunakan) |
| attempts | tinyint | Jumlah percobaan salah memasukkan OTP |
| ip_address | varchar(255) | IP address yang request OTP |
| user_agent | text | Browser/device user agent |

---

### 7.3 Tabel: `migrations`
Tabel sistem Laravel untuk tracking database migration. Setiap kali menjalankan migration, Laravel akan mencatat nama file migration dan batch number. Digunakan untuk rollback migration jika diperlukan.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | int | Primary Key |
| migration | varchar(255) | Nama file migration (dengan timestamp) |
| batch | int | Nomor batch migration (increment setiap kali run migration) |

---

### 7.4 Tabel: `failed_jobs`
Tabel sistem Laravel untuk mencatat job queue yang gagal. Job queue digunakan untuk task asynchronous seperti kirim email, generate laporan besar, export data, dll. Jika job gagal, akan disimpan di sini untuk di-retry atau investigasi.

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | bigint | Primary Key |
| uuid | varchar(255) | UUID unik untuk job (unique) |
| connection | text | Nama koneksi queue (database, redis, dll) |
| queue | text | Nama queue channel |
| payload | longtext | Data payload job dalam format serialized |
| exception | longtext | Exception/error message lengkap saat job gagal |
| failed_at | timestamp | Waktu job gagal |

---

## RINGKASAN STRUKTUR DATABASE

**Total Tabel**: 35 tabel

**Kategori Tabel**:
1. **User Management** (6 tabel): users, roles, permissions, user_roles, role_permissions, user_permissions
2. **Chart of Accounts** (3 tabel): chart_of_accounts, account_balance_histories, chart_of_account_audits
3. **Journal Entries** (5 tabel): journal_entries, journal_entry_details, journal_entry_approvals, journal_entry_attachments, journal_entry_revisions
4. **Products** (4 tabel): products, product_categories, units, locations
5. **Stock Transactions** (10 tabel): stock_in, stock_in_details, stock_mutations, stock_mutation_details, stock_adjustments, stock_adjustment_details, stock_opnames, stock_opname_details, stock_balances, stock_cards
6. **Sales** (3 tabel): sales, sale_details, customers
7. **System** (4 tabel): settings, password_reset_otps, migrations, failed_jobs

**Fitur Database**:
- ✅ **Foreign Key Constraints** untuk menjaga data integrity dan relasi antar tabel
- ✅ **Unique Constraints** untuk mencegah duplikasi data penting (kode, email, nomor transaksi)
- ✅ **Indexes** untuk optimasi performa query pada kolom yang sering dicari
- ✅ **Soft Delete** (deleted_at) pada tabel master untuk audit trail
- ✅ **Audit Trail** (created_by, updated_by, timestamps) untuk tracking perubahan
- ✅ **JSON Field** untuk data fleksibel dan dynamic attributes
- ✅ **Database Triggers** untuk validasi circular reference pada COA dan auto-update timestamps
- ✅ **Enum Types** untuk membatasi nilai input dan data consistency
- ✅ **Decimal Precision** (15,2) untuk akurasi perhitungan keuangan
- ✅ **Multi-level Approval Workflow** untuk jurnal dan transaksi penting
