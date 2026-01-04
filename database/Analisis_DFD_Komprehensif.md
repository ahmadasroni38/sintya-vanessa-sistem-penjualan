# ANALISIS DIAGRAM ARUS DATA (DFD) SISTEM INFORMASI AKUNTANSI DAN INVENTORY MANAGEMENT

## 3.1 DFD Level 0 (Diagram Konteks)

Diagram Arus Data Level 0 atau Diagram Konteks merupakan representasi level tertinggi dari Sistem Informasi Akuntansi dan Inventory Management (Penjualan). Pada level ini, sistem digambarkan sebagai satu kesatuan proses tunggal yang berinteraksi dengan entitas eksternal. Diagram ini menunjukkan batas sistem dan identifikasi aliran data utama yang masuk ke dan keluar dari sistem.

Terdapat tiga entitas eksternal yang berinteraksi dengan sistem Penjualan, yaitu Owner, Admin, dan Accounting. Owner berperan sebagai pihak yang meminta laporan keuangan dan inventory dari sistem. Sebagai balasannya, sistem menyediakan Laporan Keuangan & Inventory kepada Owner. Admin memiliki peran yang lebih luas dalam interaksi dengan sistem, meliputi pengelolaan Data User & Role, Data Master Product & Lokasi, Data Stock In, Data Mutasi Stock, Data Adjustment, Data Opname, dan Data Penjualan. Sebagai respons, sistem menyediakan Laporan Stock & Inventory, Stock Card, Konfirmasi Transaksi, dan Invoice/Nota Penjualan kepada Admin. Accounting bertanggung jawab atas input Data Journal Entry dan Data Chart of Accounts ke sistem, serta menerima Laporan Keuangan dan Status Approval dari sistem.

Aliran data yang masuk ke sistem mencerminkan berbagai input yang diperlukan untuk operasional sistem, termasuk data master, transaksi harian, dan data akuntansi. Sementara itu, aliran data yang keluar dari sistem berupa informasi olahan yang siap digunakan untuk pengambilan keputusan, pelaporan, dan konfirmasi transaksi. Diagram Level 0 ini memberikan gambaran holistik tentang bagaimana sistem Penjualan berfungsi sebagai jembatan antara berbagai pengguna dengan informasi yang mereka butuhkan.

## 3.2 DFD Level 1

Diagram Arus Data Level 1 merupakan perluasan dari proses utama pada Level 0, yang memecah sistem Penjualan menjadi tujuh proses utama yang saling terkait. Setiap proses memiliki fungsi spesifik dalam mendukung operasional sistem secara keseluruhan. Tujuh proses utama tersebut adalah: Proses 1.0 (Manajemen User & Role), Proses 2.0 (Manajemen Chart of Accounts), Proses 3.0 (Manajemen Journal Entry), Proses 4.0 (Manajemen Product & Kategori), Proses 5.0 (Manajemen Inventory/Stock), Proses 6.0 (Manajemen Sales), dan Proses 7.0 (Generate Laporan).

Proses 1.0 (Manajemen User & Role) berfungsi mengelola data user, role, dan hak akses sistem. Proses ini menerima Data User & Role dari Admin dan mengembalikan Data User & Log sebagai konfirmasi. Proses 1.0 berinteraksi langsung dengan Data Store D1 (Users & Roles) untuk operasi baca dan tulis data.

Proses 2.0 (Manajemen Chart of Accounts) bertanggung jawab mengelola bagan akun dan struktur akuntansi. Accounting memberikan Data Akun ke proses ini, dan sebagai respons sistem memberikan Konfirmasi. Proses 2.0 menggunakan Data Store D2 (Chart of Accounts) untuk menyimpan dan mengelola data akun.

Proses 3.0 (Manajemen Journal Entry) mengelola jurnal akuntansi, approval, dan posting. Accounting menyediakan Data Jurnal ke proses ini, dan sistem mengembalikan Status Approval. Proses ini berinteraksi dengan Data Store D3 (Journal Entries) untuk operasi baca tulis, serta mengupdate saldo pada Data Store D2 (Chart of Accounts) dan menerima Data Akun dari Proses 2.0.

Proses 4.0 (Manajemen Product & Kategori) mengelola master data produk, kategori, dan satuan. Admin menyediakan Data Product ke proses ini dan menerima Konfirmasi sebagai balasan. Proses 4.0 berinteraksi dengan Data Store D4 (Products) untuk penyimpanan data produk.

Proses 5.0 (Manajemen Inventory/Stock) mengelola stock in, mutasi, adjustment, dan opname. Admin memberikan berbagai data terkait inventory termasuk Data Stock In, Data Mutasi, Data Adjustment, dan Data Opname. Sistem merespons dengan Stock Card sebagai informasi kepada Admin. Proses ini menggunakan Data Store D5 (Stock Balances) dan membaca data produk dari Data Store D4. Selain itu, Proses 5.0 juga mengirimkan Journal Entry ke Proses 3.0 untuk pencatatan akuntansi.

Proses 6.0 (Manajemen Sales) mengelola transaksi penjualan dan customer. Admin menyediakan Data Penjualan ke proses ini dan menerima Invoice sebagai balasan. Proses 6.0 berinteraksi dengan Data Store D6 (Sales) dan Data Store D7 (Customers), serta membaca data produk dari Data Store D4. Proses ini juga mengupdate stock melalui Proses 5.0 dan mengirimkan Journal Entry ke Proses 3.0.

Proses 7.0 (Generate Laporan) menghasilkan berbagai laporan keuangan dan inventory. Owner meminta laporan dari proses ini dan menerima Laporan Keuangan & Inventory sebagai hasilnya. Proses 7.0 membaca data dari berbagai Data Store termasuk D2 (Chart of Accounts), D3 (Journal Entries), D5 (Stock Balances), dan D6 (Sales) untuk menghasilkan laporan yang komprehensif.

Seluruh proses dalam DFD Level 1 ini diatur oleh mekanisme hak akses yang dikelola melalui Data Store D1 (Users & Roles). Setiap proses menerima informasi hak akses dari D1 untuk memastikan hanya user yang berwenang dapat mengakses fungsi-fungsi tertentu dalam sistem.

## 3.3 DFD Level 2

### 3.3.1 DFD Level 2 - Proses 1.0 (Manajemen User & Role)

Diagram Arus Data Level 2 untuk Proses 1.0 menguraikan secara detail fungsi manajemen user dan role dalam sistem. Proses ini dipecah menjadi empat sub-proses utama yang saling terkait untuk mengelola seluruh aspek pengelolaan pengguna sistem.

Sub-proses 1.1 (Create/Update User) bertanggung jawab untuk membuat atau mengubah data user dalam sistem. Admin menyediakan Data User Baru ke sub-proses ini, dan sebagai respons sistem memberikan Konfirmasi User. Sub-proses ini berinteraksi langsung dengan Data Store D1A (Users) untuk operasi baca dan tulis data pengguna.

Sub-proses 1.2 (Manage Role) mengelola role dan permissions dalam sistem. Admin menyediakan Data Role ke sub-proses ini dan menerima Konfirmasi Role sebagai balasan. Sub-proses ini menggunakan Data Store D1B (Roles) untuk menyimpan dan mengelola data role.

Sub-proses 1.3 (Assign User Role) berfungsi untuk mengaitkan role dengan user tertentu. Admin melakukan Assign Role to User melalui sub-proses ini, dan sistem memberikan Konfirmasi Assignment. Sub-proses ini membaca data user dari Data Store D1A (Users), membaca data role dari Data Store D1B (Roles), dan menulis hubungan user-role ke Data Store D1C (User Roles).

Sub-proses 1.4 (Activate/Deactivate User) mengelola status aktif atau non-aktif dari user. Admin dapat mengubah status user melalui Status Change, dan sistem merespons dengan Konfirmasi Status. Sub-proses ini mengupdate status pada Data Store D1A (Users) dan Data Store D1C (User Roles) untuk memastikan konsistensi data.

Keempat sub-proses ini bekerja secara terintegrasi untuk memastikan manajemen pengguna sistem berjalan dengan baik, mulai dari pembuatan user hingga pengaturan hak akses dan pengelolaan status aktif user.

### 3.3.2 DFD Level 2 - Proses 2.0 (Manajemen Chart of Accounts)

Diagram Arus Data Level 2 untuk Proses 2.0 memperinci fungsi manajemen Chart of Accounts (COA) dalam sistem. Proses ini diuraikan menjadi empat sub-proses yang mengelola seluruh aspek bagan akun mulai dari pembuatan hingga pengaturan saldo awal.

Sub-proses 2.1 (Create/Update Account) bertanggung jawab untuk membuat atau mengubah akun baru dalam sistem. Accounting menyediakan Data Akun ke sub-proses ini, dan sistem memberikan Konfirmasi Account sebagai respons. Sub-proses ini berinteraksi langsung dengan Data Store D2 (Chart of Accounts) untuk operasi baca dan tulis data akun.

Sub-proses 2.2 (Set Account Hierarchy) mengatur struktur parent-child dari akun-akun dalam sistem. Accounting menyediakan Parent-Child Relation ke sub-proses ini dan menerima Konfirmasi Hierarchy sebagai balasan. Sub-proses ini mengupdate informasi parent dan level pada Data Store D2 (Chart of Accounts) untuk membentuk struktur hierarki yang logis.

Sub-proses 2.3 (Set Opening Balance) berfungsi untuk menetapkan saldo awal dari setiap akun. Accounting menyediakan Opening Balance ke sub-proses ini, dan sistem merespons dengan Konfirmasi Balance. Sub-proses ini mengupdate saldo pada Data Store D2 (Chart of Accounts) dan menulis history perubahan saldo ke Data Store D2A (Account Balance Histories) untuk pelacakan audit.

Sub-proses 2.4 (Activate/Deactivate Account) mengelola status aktif atau non-aktif dari akun. Accounting dapat mengubah status akun melalui Status Change, dan sistem memberikan Konfirmasi Status. Sub-proses ini mengupdate status pada Data Store D2 (Chart of Accounts) untuk mengontrol akun mana saja yang aktif digunakan dalam transaksi.

Keempat sub-proses ini memastikan pengelolaan bagan akun berjalan secara terstruktur dan terkontrol, mulai dari pembentukan struktur akun hingga penyiapan saldo awal untuk operasional akuntansi.

### 3.3.3 DFD Level 2 - Proses 3.0 (Manajemen Journal Entry)

Diagram Arus Data Level 2 untuk Proses 3.0 menguraikan secara detail fungsi manajemen journal entry dalam sistem. Proses ini dipecah menjadi lima sub-proses yang mengelola siklus hidup jurnal akuntansi mulai dari pembuatan hingga posting.

Sub-proses 3.1 (Create/Draft Journal Entry) bertanggung jawab untuk membuat draft jurnal baru dengan detail debit/kredit. Staff Accounting menyediakan Data Jurnal ke sub-proses ini dan menerima Draft Jurnal sebagai respons. Sub-proses ini menulis data jurnal ke Data Store D3A (Journal Entries), menulis detail jurnal ke Data Store D3B (Journal Entry Details), membaca data akun dari Data Store D2 (Chart of Accounts), dan mengupload attachment ke Data Store D3D (Journal Attachments).

Sub-proses 3.2 (Review & Submit) berfungsi untuk validasi dan submit jurnal untuk proses approval. Accounting melakukan Submit for Approval melalui sub-proses ini dan menerima Validation Result sebagai balasan. Sub-proses ini mengupdate status pada Data Store D3A (Journal Entries), membaca detail jurnal dari Data Store D3B (Journal Entry Details), dan memvalidasi keseimbangan debit/kredit dengan Data Store D2 (Chart of Accounts).

Sub-proses 3.3 (Approval Process) mengelola proses persetujuan jurnal oleh pihak yang berwenang. Owner atau Accounting memberikan Approval Decision ke sub-proses ini, dan sistem merespons dengan Approval Status. Sub-proses ini menulis data approval ke Data Store D3C (Journal Approvals), membaca data jurnal dari Data Store D3A (Journal Entries), dan memeriksa otoritas approver dari Data Store D1 (Users).

Sub-proses 3.4 (Posting Journal) bertanggung jawab untuk posting jurnal yang sudah disetujui. Accounting melakukan Post Journal melalui sub-proses ini dan menerima Posting Result sebagai balasan. Sub-proses ini mengupdate status pada Data Store D3A (Journal Entries), membaca detail jurnal dari Data Store D3B (Journal Entry Details), memeriksa status approval dari Data Store D3C (Journal Approvals), dan memicu proses update saldo melalui Sub-proses 3.5.

Sub-proses 3.5 (Update Account Balance) berfungsi untuk memperbarui saldo akun setelah proses posting. Sub-proses ini menerima data debit/kredit dari Data Store D3B (Journal Entry Details) dan mengupdate saldo pada Data Store D2 (Chart of Accounts).

Kelima sub-proses ini membentuk alur kerja yang terstruktur untuk pengelolaan jurnal akuntansi, memastikan setiap transaksi akuntansi melalui proses validasi, approval, dan posting yang sesuai dengan standar akuntansi.

### 3.3.4 DFD Level 2 - Proses 4.0 (Manajemen Product & Kategori)

Diagram Arus Data Level 2 untuk Proses 4.0 memperinci fungsi manajemen produk dan kategori dalam sistem. Proses ini diuraikan menjadi empat sub-proses yang mengelola seluruh aspek master data produk mulai dari kategori hingga pengaturan batas stock.

Sub-proses 4.1 (Manage Product Category) bertanggung jawab untuk mengelola kategori produk dalam sistem. Admin menyediakan Data Kategori ke sub-proses ini dan menerima Konfirmasi Kategori sebagai respons. Sub-proses ini berinteraksi langsung dengan Data Store D4A (Product Categories) untuk operasi baca dan tulis data kategori.

Sub-proses 4.2 (Manage Unit) mengelola satuan unit produk dalam sistem. Admin menyediakan Data Satuan ke sub-proses ini dan menerima Konfirmasi Satuan sebagai balasan. Sub-proses ini menggunakan Data Store D4B (Units) untuk menyimpan dan mengelola data satuan produk.

Sub-proses 4.3 (Create/Update Product) berfungsi untuk membuat atau mengubah data produk. Admin menyediakan Data Product ke sub-proses ini dan sistem memberikan Konfirmasi Product. Sub-proses ini berinteraksi dengan Data Store D4C (Products) untuk operasi baca tulis data produk, membaca data kategori dari Data Store D4A (Product Categories), membaca data satuan dari Data Store D4B (Units), dan membaca data lokasi dari Data Store D8 (Locations).

Sub-proses 4.4 (Set Stock Min/Max) bertanggung jawab untuk menetapkan batas minimum dan maksimum stock untuk setiap produk. Admin menyediakan Stock Limits ke sub-proses ini dan menerima Konfirmasi Limits sebagai balasan. Sub-proses ini mengupdate batas stock pada Data Store D4C (Products) untuk keperluan monitoring dan kontrol inventory.

Keempat sub-proses ini memastikan pengelolaan master data produk berjalan secara terstruktur, mulai dari pengelolaan kategori dan satuan hingga pembuatan produk dan pengaturan parameter stock untuk mendukung operasional inventory yang efektif.

### 3.3.5 DFD Level 2 - Proses 5.0 (Manajemen Inventory & Stock)

Diagram Arus Data Level 2 untuk Proses 5.0 menguraikan secara detail fungsi manajemen inventory dan stock dalam sistem. Proses ini dipecah menjadi lima sub-proses yang mengelola seluruh aspek pergerakan stock mulai dari barang masuk hingga penyesuaian.

Sub-proses 5.1 (Stock In Pembelian) bertanggung jawab untuk mencatat barang masuk dari supplier. Admin menyediakan Data Barang Masuk ke sub-proses ini dan menerima Konfirmasi Stock In sebagai respons. Sub-proses ini berinteraksi dengan Data Store D5A (Stock In) untuk operasi baca tulis, membaca data produk dari Data Store D4 (Products), mengirimkan Qty In ke Sub-proses 5.5, dan membuat journal entry ke Data Store D3 (Journal Entries) untuk pencatatan akuntansi.

Sub-proses 5.2 (Stock Mutation Transfer) mengelola transfer stock antar lokasi atau gudang. Admin menyediakan Data Transfer ke sub-proses ini dan menerima Status Mutasi sebagai balasan. Sub-proses ini menggunakan Data Store D5B (Stock Mutations) untuk mencatat transaksi transfer, membaca data produk dari Data Store D4 (Products), dan mengirimkan Qty Out/In ke Sub-proses 5.5.

Sub-proses 5.3 (Stock Adjustment Penyesuaian) berfungsi untuk mencatat penyesuaian stock karena terjadi selisih. Admin menyediakan Data Penyesuaian ke sub-proses ini dan sistem memberikan Konfirmasi Adjustment. Sub-proses ini berinteraksi dengan Data Store D5C (Stock Adjustments), membaca data produk dari Data Store D4 (Products), memeriksa stock saat ini dari Data Store D5E (Stock Balances), mengirimkan Qty Adjustment ke Sub-proses 5.5, dan membuat journal entry ke Data Store D3 (Journal Entries).

Sub-proses 5.4 (Stock Opname Perhitungan Fisik) bertanggong jawab untuk mencatat hasil perhitungan fisik stock. Admin menyediakan Data Perhitungan ke sub-proses ini dan menerima Laporan Opname sebagai balasan. Sub-proses ini menggunakan Data Store D5D (Stock Opnames) untuk mencatat hasil opname, membaca data produk dari Data Store D4 (Products), memeriksa stock sistem dari Data Store D5E (Stock Balances), dan mengirimkan Qty Difference ke Sub-proses 5.5.

Sub-proses 5.5 (Update Stock Balance) berfungsi sebagai pusat pemrosesan untuk memperbarui saldo stock dan kartu stock. Sub-proses ini menerima berbagai input qty dari sub-proses lainnya, mengupdate saldo pada Data Store D5E (Stock Balances), dan menulis pergerakan stock ke Data Store D5F (Stock Cards).

Kelima sub-proses ini membentuk sistem manajemen inventory yang komprehensif, memastikan setiap pergerakan stock tercatat dengan baik dan akurat, serta terintegrasi dengan sistem akuntansi untuk pencatatan keuangan.

### 3.3.6 DFD Level 2 - Proses 6.0 (Manajemen Sales)

Diagram Arus Data Level 2 untuk Proses 6.0 memperinci fungsi manajemen penjualan dalam sistem. Proses ini diuraikan menjadi lima sub-proses yang mengelola seluruh siklus transaksi penjualan mulai dari input order hingga pembuatan invoice.

Sub-proses 6.1 (Input Sales Order) bertanggung jawab untuk input data penjualan dan item yang dijual. Admin melakukan Input Penjualan melalui sub-proses ini dan menerima Sales Order sebagai respons. Sub-proses ini menulis data penjualan ke Data Store D6 (Sales), menulis detail penjualan ke Data Store D6A (Sale Details), membaca/menulis data customer ke Data Store D7 (Customers), membaca data produk dari Data Store D4 (Products), dan memeriksa ketersediaan stock dari Data Store D5E (Stock Balances).

Sub-proses 6.2 (Calculate Total & Tax) berfungsi untuk menghitung subtotal, pajak, dan total penjualan. Sub-proses ini menerima Sales Data dari Sub-proses 6.1, menghitung Total & Tax yang dikembalikan ke Sub-proses 6.1, membaca detail penjualan dari Data Store D6A (Sale Details), dan mengupdate total pada Data Store D6 (Sales).

Sub-proses 6.3 (Process Payment) mengelola proses pembayaran dari customer. Admin menyediakan Input Payment ke sub-proses ini dan menerima Konfirmasi Payment sebagai balasan. Sub-proses ini mengupdate informasi pembayaran pada Data Store D6 (Sales).

Sub-proses 6.4 (Post Sales) bertanggung jawab untuk finalisasi penjualan, pengurangan stock, dan pembuatan jurnal akuntansi. Admin melakukan Finalize Sale melalui sub-proses ini dan menerima Status sebagai balasan. Sub-proses ini mengupdate status pada Data Store D6 (Sales), membaca detail penjualan dari Data Store D6A (Sale Details), mengurangi stock pada Data Store D5E (Stock Balances), dan membuat journal entry ke Data Store D3 (Journal Entries).

Sub-proses 6.5 (Generate Invoice) berfungsi untuk membuat invoice/nota untuk customer. Sub-proses ini menerima Posted Sale dari Sub-proses 6.4, menghasilkan Invoice untuk Admin, membaca data penjualan dari Data Store D6 (Sales), membaca detail penjualan dari Data Store D6A (Sale Details), dan membaca data customer dari Data Store D7 (Customers).

Kelima sub-proses ini membentuk alur kerja penjualan yang terintegrasi, memastikan setiap transaksi penjualan tercatat dengan akurat, stock terupdate secara otomatis, dan pencatatan akuntansi terjadi secara konsisten.

### 3.3.7 DFD Level 2 - Proses 7.0 (Generate Laporan)

Diagram Arus Data Level 2 untuk Proses 7.0 menguraikan secara detail fungsi pembuatan laporan dalam sistem. Proses ini dipecah menjadi empat sub-proses yang mengelola pembuatan berbagai jenis laporan untuk kebutuhan manajerial.

Sub-proses 7.1 (Generate Laporan Keuangan) bertanggung jawab untuk membuat laporan keuangan seperti neraca, laba rugi, dan buku besar. Owner melakukan Request Laporan Keuangan ke sub-proses ini dan menerima Laporan Keuangan sebagai respons. Sub-proses ini membaca data COA dari Data Store D2 (Chart of Accounts) dan membaca data jurnal dari Data Store D3 (Journal Entries) untuk menghasilkan laporan yang akurat.

Sub-proses 7.2 (Generate Laporan Stock) berfungsi untuk membuat laporan inventory dan kartu stock. Owner melakukan Request Laporan Stock ke sub-proses ini dan menerima Laporan Inventory sebagai balasan. Sub-proses ini membaca data saldo stock dari Data Store D5 (Stock Balances), membaca data kartu stock dari Data Store D5F (Stock Cards), dan membaca data produk dari Data Store D4 (Products) untuk menghasilkan laporan inventory yang komprehensif.

Sub-proses 7.3 (Generate Laporan Sales) bertanggung jawab untuk membuat laporan penjualan. Owner melakukan Request Laporan Sales ke sub-proses ini dan menerima Laporan Penjualan sebagai respons. Sub-proses ini membaca data penjualan dari Data Store D6 (Sales) dan membaca data produk dari Data Store D4 (Products) untuk menghasilkan laporan penjualan yang detail.

Sub-proses 7.4 (Export Laporan) berfungsi untuk mengekspor laporan ke berbagai format seperti PDF atau Excel. Owner melakukan Request Export ke sub-proses ini dan menerima File Export sebagai balasan. Sub-proses ini menerima Report Data dari Sub-proses 7.1, 7.2, dan 7.3, kemudian menghasilkan file export yang dapat diunduh oleh Owner.

Keempat sub-proses ini memastikan ketersediaan laporan yang komprehensif untuk mendukung pengambilan keputusan manajerial, dengan kemampuan untuk menghasilkan laporan keuangan, inventory, dan penjualan dalam berbagai format yang sesuai dengan kebutuhan pengguna.
