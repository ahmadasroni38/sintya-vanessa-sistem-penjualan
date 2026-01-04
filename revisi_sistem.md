<!-- - Menambahkan tanggal print di bagian cetak semua laporan -->
<!-- - Menambahkan siapa yang menandatangan di bagian bawah laporan keuangannya -->
- Sesuaikan 3.3.2	Analisis Sistem Yang Diusulkan, karena ada penambahkan role user accounting
- Sesuaikan 3.3.3	Gambaran Umum Sistem, masih belum menggambarkan sistem secara utuh dan versi terbaru
<!-- - Tambahkan ERD sebelum pembahasan CDM -->
<!-- - Bab 4 hilangkan 1 UI yaitu "Pengaturan Sistem" -->
<!-- - Bab 4 perlu review dan cocokan dengan bab 3 -->
<!-- - Sesuaikan isi konten blackbox testing di bab 3 agar sesuai dengan bab 4 -->

## Cross-Functional Flowchart Sistem

Berikut adalah diagram alir berbasis fungsi sederhana yang menggambarkan interaksi antara entitas dalam sistem:

```mermaid
flowchart TD
    subgraph "Owner"
        O1[Request Report/Approval]
        O2[Receive and Review Reports]
    end

    subgraph "Staff Admin"
        SA1[Manage Users & Permissions]
        SA2[Configure System Settings]
    end

    subgraph "Accounting"
        A1[Create Journal Entries]
        A2[Generate Financial Reports]
        A3[Review Transactions]
    end

    subgraph "System"
        S1[Process Data & Calculations]
        S2[Store Transactions]
        S3[Generate Report Output]
    end

    O1 --> SA1
    SA1 --> A1
    A1 --> S1
    S1 --> S2
    S2 --> A2
    A2 --> S3
    S3 --> A3
    A3 --> SA2
    SA2 --> O2
```
