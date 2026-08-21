# Comprehensive Product Requirements Document (PRD)
**Project Name:** Internal Quotation & Estimation Calculator  
**Target User:** Tim Internal (Admin & Sales/Estimator)  
**Platform:** Web Application  

## 1. Project Overview & Architecture
Sistem manajemen penawaran (quotation) internal berbasis web. Sistem ini menggunakan kalkulasi dinamis berdasarkan bobot kompleksitas fitur (Complexity Weight) dalam mata uang Rupiah (IDR) dan menghasilkan nota berformat PDF resmi.

### Tech Stack Khusus
*   **Framework:** Laravel 13
*   **Database:** SQLite (Gunakan `database.sqlite` standar Laravel 13)
*   **Admin Panel / TALL Stack:** Filament PHP v3
*   **Authorization:** spatie/laravel-permission
*   **PDF Engine:** barryvdh/laravel-dompdf

---

## 2. Database Schema & Eloquent Models
Karena menggunakan SQLite, penanganan tipe data `DECIMAL` pada tingkat *database* mungkin diinterpretasikan sebagai `NUMERIC` atau `STRING`. Oleh karena itu, **wajib** menggunakan *Attribute Casting* (`protected $casts`) di model Eloquent untuk memastikan presisi finansial.

### A. Tabel `users` (Bawaan Laravel + Spatie)
*   Integrasikan dengan `HasRoles` trait dari Spatie.
*   **Seeder Requirement:** Buat minimal 1 akun Admin dan 1 akun Sales.

### B. Tabel `modules` (Katalog Fitur)
| Column | Type | Modifiers | Description |
| :--- | :--- | :--- | :--- |
| `id` | ID | Primary Key | - |
| `name` | String | Required | Nama fitur standar |
| `base_price` | Decimal | Total: 15, Places: 2 | Harga dasar (IDR) |
| `category` | String | Nullable | Pengelompokan fitur |
| `timestamps` | - | - | Created_at, Updated_at |

*   **Eloquent Casts:** `'base_price' => 'decimal:2'`

### C. Tabel `projects` (Dokumen Penawaran)
| Column | Type | Modifiers | Description |
| :--- | :--- | :--- | :--- |
| `id` | ID | Primary Key | - |
| `user_id` | Foreign ID | Constrained, Cascade | Pembuat dokumen |
| `client_name` | String | Required | Nama Klien |
| `grand_total` | Decimal | Total: 15, Places: 2 | Total nilai kontrak nota (Rp) |
| `status` | String | Default: 'Draft' | Enum: Draft, Generated |
| `billing_type` | String | Default: 'one_off' | Enum: one_off (Putus Kontrak), subscription (Berlangganan) |
| `billing_cycle`| String | Default: 'monthly' | Enum: monthly (Bulanan), yearly (Tahunan) |
| `subscription_duration` | Integer | Default: 12 | Durasi komitmen kontrak (Bulan/Tahun) |
| `setup_fee` | Decimal | Total: 15, Places: 2 | Biaya setup / onboarding awal (Rp) |
| `timestamps` | - | - | Created_at, Updated_at |

*   **Eloquent Casts:** `'grand_total' => 'decimal:2'`, `'setup_fee' => 'decimal:2'`, `'subscription_duration' => 'integer'`
*   **Relationships:** `hasMany(ProjectItem::class)`, `belongsTo(User::class)`
*   **Kalkulasi Total:**
    *   *Putus Kontrak:* `grand_total = sum(items.calculated_price)`
    *   *Berlangganan:* `grand_total = setup_fee + (sum(items.calculated_price) * subscription_duration)`

### D. Tabel `project_items` (Keranjang Fitur / Line Items)
| Column | Type | Modifiers | Description |
| :--- | :--- | :--- | :--- |
| `id` | ID | Primary Key | - |
| `project_id` | Foreign ID | Constrained, Cascade | Relasi ke tabel projects |
| `module_id` | Foreign ID | Nullable, Set Null | Null jika fitur custom |
| `item_name` | String | Required | Nama fitur (di nota) |
| `base_price` | Decimal | Total: 15, Places: 2 | Harga dasar (IDR) |
| `complexity_weight`| Decimal | Total: 8, Places: 2 | Default 1.00 |
| `calculated_price` | Decimal | Total: 15, Places: 2 | Final price per item (IDR) |
| `timestamps` | - | - | Created_at, Updated_at |

*   **Eloquent Casts:** `'base_price' => 'decimal:2'`, `'complexity_weight' => 'decimal:2'`, `'calculated_price' => 'decimal:2'`
*   **Relationships:** `belongsTo(Project::class)`, `belongsTo(Module::class)`

---

## 3. Security & Access Control (Policies)
Implementasikan Laravel Policies untuk setiap Model dan integrasikan dengan Spatie Permission.

*   **Admin Role:** Memiliki akses `viewAny`, `view`, `create`, `update`, `delete` pada semua resources.
*   **Sales Role:** 
    *   `ModulePolicy`: Return `false` untuk semua method.
    *   `ProjectPolicy`:
        *   `viewAny`: Return `true` (di-filter via Eloquent Builder di tahap UI).
        *   `view`, `update`, `delete`: Return `true` HANYA JIKA `$user->id === $project->user_id`.

---

## 4. Filament v3 Implementation Details

### A. ModuleResource (Master Data)
*   **Akses:** Hanya Admin. Navigasi disembunyikan untuk Sales.
*   **Dual Pricing:**
    *   `base_price`: Harga beli putus / *one-off build price* (Rp).
    *   `subscription_price`: Harga sewa & pemeliharaan modul per bulan / *subscription base price* (Rp).
*   **Tabel & Form:** Input teks standar dan `TextInput` dengan masking mata uang interaktif `$money($input, ',', '.', 0)`.

### B. ProjectResource (Transaction & Logic)
*   **Data Ownership (Query Builder):** 
    Terapkan modifikasi pada `getEloquentQuery()` untuk memfilter `user_id` jika user bukan Admin.
*   **Header Form (Project Details):**
    *   `TextInput` untuk `client_name`.
    *   `Select` untuk `status`.
    *   `Select` untuk `billing_type` (`one_off` vs `subscription`).
    *   `Select` untuk `subscription_basis` (`modular`, `per_user`, `hybrid`).
    *   `Select` untuk `billing_cycle` (`monthly` vs `yearly`).
    *   `TextInput` untuk `subscription_duration`, `user_count`, `price_per_user`, dan `setup_fee`.
*   **Repeater Form (Line Items Logic):**
    Gunakan `Repeater::make('items')->relationship()`.
    *   **Standar vs Custom:** Dropdown `module_id`. Jika penawaran adalah *Putus Kontrak*, mengambil `base_price`. Jika *Berlangganan*, otomatis mengambil `subscription_price` (atau rasio 8% jika kosong).
    *   **Kalkulasi Real-time:** Ketika `base_price` atau `complexity_weight` berubah, hitung ulang: 
        `calculated_price = base_price * complexity_weight`.
*   **Formula Kalkulasi Kontrak:**
    *   **Putus Kontrak (One-Off):**
        $$\text{Grand Total} = \sum \text{Calculated Price}$$
    *   **Berlangganan - Flat Modular:**
        $$\text{Grand Total} = \text{Setup Fee} + \left( \sum \text{Calculated Price} \times \text{Duration} \right)$$
    *   **Berlangganan - Per-User:**
        $$\text{Grand Total} = \text{Setup Fee} + \left( (\text{User Count} \times \text{Price per User}) \times \text{Duration} \right)$$
    *   **Berlangganan - Hybrid:**
        $$\text{Grand Total} = \text{Setup Fee} + \left( \left(\sum \text{Calculated Price} + (\text{User Count} \times \text{Price per User})\right) \times \text{Duration} \right)$$

### C. PDF Export Action
*   Tambahkan Custom Action di halaman `ListRecords` (kolom tabel) dan `ViewRecord`/`EditRecord` (header).
*   Gunakan fungsi `Action::make('print_pdf')` yang mengarah ke *route* spesifik untuk men-*generate* dokumen menggunakan DOMPDF dan membukanya di *tab* baru (`openUrlInNewTab()`).

---

## 5. PDF Generation Pipeline (DOMPDF)
*   **Controller:** Buat `QuotationController@downloadPdf`.
*   **Routing:** Daftarkan *route* GET dengan *middleware* `auth` dan proteksi *Policy* agar user tidak bisa men-*download* ID PDF milik orang lain.
*   **View (Blade):**
    *   Gunakan struktur tabel HTML tradisional (`<table>`, `<tr>`, `<td>`).
    *   Sematkan *Internal CSS* (`<style>`).
    *   Gunakan `Illuminate\Support\Number::currency()` bawaan Laravel 10+ (didukung penuh di Laravel 13) untuk *formatting* mata uang Rupiah (IDR).
    *   Hitung `grand_total` secara dinamis di *controller* dengan melakukan *sum* pada kolom `calculated_price` dari relasi `items`, lalu simpan hasilnya kembali ke database jika perlu (atau tampilkan langsung).