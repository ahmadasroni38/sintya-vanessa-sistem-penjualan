# PENYEMPURNAAN NERACA LAJUR

## 📋 Ringkasan Perubahan

Neraca Lajur telah disempurnakan untuk mengikuti standar akuntansi Indonesia dengan struktur kolom yang lengkap dan akurat.

---

## 🔄 Perubahan Backend (ReportController.php)

### Struktur Data yang Dikembalikan

API endpoint `/api/reports/neraca-lajur` sekarang mengembalikan data dengan struktur lengkap:

```php
[
    'id' => account.id,
    'account_code' => '1-1100',
    'account_name' => 'Kas',
    'account_type' => 'asset',
    'normal_balance' => 'debit',

    // Kolom 1: Saldo Awal
    'opening_balance' => 50000000,

    // Kolom 2-3: Penyesuaian (dari entry_type: 'adjustment')
    'adjustment_debit' => 5000000,
    'adjustment_credit' => 0,

    // Kolom 4-5: Saldo Disesuaikan
    'adjusted_debit' => 55000000,
    'adjusted_credit' => 0,

    // Kolom 6-7: Neraca (untuk asset, liability, equity)
    'neraca_debit' => 55000000,
    'neraca_credit' => 0,

    // Kolom 8-9: Laba Rugi (untuk revenue, expense)
    'laba_rugi_debit' => 0,
    'laba_rugi_credit' => 0,
]
```

### Logika Klasifikasi Akun

#### 1. **Saldo Disesuaikan** (Adjusted Balance)
Berdasarkan `normal_balance` akun:
```php
if ($adjustedBalance > 0) {
    // Balance positif → sesuai normal balance
    if ($account->normal_balance === 'debit') {
        $saldoDisesuaikanDebit = $adjustedBalance;
    } else {
        $saldoDisesuaikanCredit = $adjustedBalance;
    }
}
```

#### 2. **Klasifikasi ke Neraca atau Laba Rugi**
```php
if (in_array($account->account_type, ['asset', 'liability', 'equity'])) {
    // Masuk ke kolom NERACA (Balance Sheet)
    $neracaDebit = $saldoDisesuaikanDebit;
    $neracaCredit = $saldoDisesuaikanCredit;
} else {
    // Masuk ke kolom LABA RUGI (Income Statement) - revenue & expense
    $labaRugiDebit = $saldoDisesuaikanDebit;
    $labaRugiCredit = $saldoDisesuaikanCredit;
}
```

---

## 🎨 Perubahan Frontend (NeracaLajur.vue)

### Struktur Kolom yang Diperbaiki

| No | Kolom | Sumber Data | Keterangan |
|----|-------|-------------|------------|
| 1 | Akun | `account_code` + `account_name` | Kode dan nama akun |
| 2 | Saldo Awal | `opening_balance` | Dari entry_type: 'opening' |
| 3 | Penyesuaian Debit | `adjustment_debit` | Dari entry_type: 'adjustment' |
| 4 | Penyesuaian Kredit | `adjustment_credit` | Dari entry_type: 'adjustment' |
| 5 | Saldo Disesuaikan Debit | `adjusted_debit` | Hasil perhitungan |
| 6 | Saldo Disesuaikan Kredit | `adjusted_credit` | Hasil perhitungan |
| 7 | Neraca Debit | `neraca_debit` | Untuk Asset (normal debit) |
| 8 | Neraca Kredit | `neraca_credit` | Untuk Liability & Equity |
| 9 | Laba Rugi Debit | `laba_rugi_debit` | Untuk Expense |
| 10 | Laba Rugi Kredit | `laba_rugi_credit` | Untuk Revenue |

### Fungsi JavaScript yang Ditambahkan/Diperbaiki

```javascript
// Total Saldo Awal
const getTotalOpeningBalance = () => {
    return reportData.value.reduce(
        (total, account) => total + Math.abs(account.opening_balance || 0),
        0
    );
};

// Total Penyesuaian Debit
const getTotalAdjustmentDebit = () => {
    return reportData.value.reduce(
        (total, account) => total + (account.adjustment_debit || 0),
        0
    );
};

// Total Penyesuaian Kredit
const getTotalAdjustmentCredit = () => {
    return reportData.value.reduce(
        (total, account) => total + (account.adjustment_credit || 0),
        0
    );
};

// Total Saldo Disesuaikan Debit
const getTotalAdjustedDebit = () => {
    return reportData.value.reduce(
        (total, account) => total + (account.adjusted_debit || 0),
        0
    );
};

// Total Saldo Disesuaikan Kredit
const getTotalAdjustedCredit = () => {
    return reportData.value.reduce(
        (total, account) => total + (account.adjusted_credit || 0),
        0
    );
};
```

---

## 📊 Flow Data Neraca Lajur

```
1. USER INPUT
   ├─ Start Date: 2025-01-01
   └─ End Date: 2025-12-31

2. BACKEND PROCESSING
   ├─ Ambil semua akun aktif
   │
   ├─ Untuk setiap akun:
   │  ├─ Calculate balance (periode)
   │  ├─ Get opening balance (tanggal awal)
   │  ├─ Get adjustments (entry_type: adjustment)
   │  └─ Klasifikasi ke Neraca/Laba Rugi
   │
   └─ Return data lengkap ke frontend

3. FRONTEND RENDERING
   ├─ Group by account_type
   │  ├─ Asset
   │  ├─ Liability
   │  ├─ Equity
   │  ├─ Revenue
   │  └─ Expense
   │
   ├─ Display 10 kolom
   │  ├─ Saldo Awal
   │  ├─ Penyesuaian D/C
   │  ├─ Saldo Disesuaikan D/C
   │  ├─ Neraca D/C
   │  └─ Laba Rugi D/C
   │
   └─ Calculate totals
```

---

## ✅ Validasi Balance

### Neraca (Balance Sheet)
```
Total Neraca Debit = Total Neraca Credit
```

Jika balance:
- **Neraca Debit = Neraca Credit** ✓ BALANCE
- **Neraca Debit ≠ Neraca Credit** ✗ TIDAK BALANCE

### Laba Rugi (Income Statement)
```
Laba/Rugi = Total Laba Rugi Credit - Total Laba Rugi Debit
```

Jika hasil:
- **Positif** → LABA (Profit)
- **Negatif** → RUGI (Loss)
- **Nol** → BREAK-EVEN

---

## 🔍 Contoh Data

### Akun Asset (1-1100 - Kas)
```json
{
    "account_code": "1-1100",
    "account_name": "Kas",
    "account_type": "asset",
    "normal_balance": "debit",
    "opening_balance": 50000000,
    "adjustment_debit": 0,
    "adjustment_credit": 0,
    "adjusted_debit": 50000000,
    "adjusted_credit": 0,
    "neraca_debit": 50000000,
    "neraca_credit": 0,
    "laba_rugi_debit": 0,
    "laba_rugi_credit": 0
}
```

### Akun Liability (2-1100 - Hutang Usaha)
```json
{
    "account_code": "2-1100",
    "account_name": "Hutang Usaha",
    "account_type": "liability",
    "normal_balance": "credit",
    "opening_balance": 50000000,
    "adjustment_debit": 0,
    "adjustment_credit": 0,
    "adjusted_debit": 0,
    "adjusted_credit": 50000000,
    "neraca_debit": 0,
    "neraca_credit": 50000000,
    "laba_rugi_debit": 0,
    "laba_rugi_credit": 0
}
```

### Akun Contra Asset (1-2210 - Akum. Penyusutan Bangunan)
```json
{
    "account_code": "1-2210",
    "account_name": "Akumulasi Penyusutan Bangunan",
    "account_type": "asset",
    "normal_balance": "credit",
    "opening_balance": 50000000,
    "adjustment_debit": 0,
    "adjustment_credit": 0,
    "adjusted_debit": 0,
    "adjusted_credit": 50000000,
    "neraca_debit": 0,
    "neraca_credit": 50000000,
    "laba_rugi_debit": 0,
    "laba_rugi_credit": 0
}
```

---

## 🎯 Keunggulan Sistem

### 1. **Real-time Balance Calculation**
- Tidak ada cache
- Data selalu akurat
- Berdasarkan posted journal entries

### 2. **Akurat Berdasarkan Normal Balance**
- Asset dengan normal balance credit (contra) → Neraca Credit ✓
- Liability dengan normal balance debit → Neraca Debit ✓
- Mengikuti standar akuntansi Indonesia

### 3. **Support Jurnal Penyesuaian**
- Entry type: `adjustment` otomatis masuk kolom Penyesuaian
- Memudahkan audit trail
- Clear separation of opening vs adjustment entries

### 4. **Flexible Date Range**
- Support periode custom
- Opening balance dari tanggal awal periode
- Adjustments dalam periode yang dipilih

### 5. **Balance Validation**
- Validasi Neraca (Debit = Credit)
- Perhitungan Laba/Rugi otomatis
- Summary section untuk quick review

---

## 📝 Cara Penggunaan

### Generate Laporan Neraca Lajur

1. Buka menu **Reports → Neraca Lajur**
2. Pilih **Start Date** dan **End Date**
3. Klik **Generate Report**
4. Sistem akan menampilkan:
   - Semua akun dengan balance dalam periode
   - 10 kolom lengkap sesuai standar
   - Total per kolom
   - Summary Neraca dan Laba Rugi
   - Validasi Balance

### Membuat Jurnal Penyesuaian

1. Buka menu **Journal Entries**
2. Klik **New Entry**
3. Pilih **Entry Type**: Adjustment
4. Input tanggal dan detail transaksi
5. Pastikan Debit = Credit
6. **Post** jurnal penyesuaian
7. Data otomatis muncul di kolom Penyesuaian di Neraca Lajur

---

## 🔧 Technical Notes

### Database Query Optimization

Backend menggunakan query yang efisien:
```php
// Get adjustments only (tidak ambil semua entries)
$adjustmentQuery = $account->journalEntryDetails()
    ->whereHas('journalEntry', function ($q) use ($validated) {
        $q->where('status', 'posted')
          ->where('entry_type', 'adjustment')
          ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
    });
```

### Conditional Rendering di Frontend

Hanya menampilkan nilai jika > 0:
```vue
{{ account.adjustment_debit > 0 ? formatCurrency(account.adjustment_debit) : '-' }}
```

Lebih clean dan mudah dibaca dibanding menampilkan Rp0.

---

## 📚 Referensi Akuntansi

### Neraca Lajur (Worksheet)
Neraca Lajur adalah kertas kerja akuntansi yang digunakan untuk:
1. Memudahkan penyusunan laporan keuangan
2. Mengecek keseimbangan debit dan kredit
3. Memisahkan akun neraca dan laba rugi
4. Memudahkan proses closing

### Struktur Standar
- **Kolom 1-2**: Neraca Saldo (Trial Balance)
- **Kolom 3-4**: Jurnal Penyesuaian (Adjustments)
- **Kolom 5-6**: Neraca Saldo Disesuaikan (Adjusted Trial Balance)
- **Kolom 7-8**: Neraca (Balance Sheet)
- **Kolom 9-10**: Laba Rugi (Income Statement)

---

## 🎉 Hasil Akhir

Sistem Neraca Lajur sekarang:
- ✅ Akurat dan mengikuti standar akuntansi
- ✅ Real-time tanpa cache
- ✅ Support jurnal penyesuaian
- ✅ Validasi balance otomatis
- ✅ UI/UX yang clean dan professional
- ✅ Export ke PDF/Excel ready

**Sistem siap untuk produksi!** 🚀
