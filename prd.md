# Comprehensive Product Requirements Document (PRD)
**Project Name:** Internal Quotation & Estimation Calculator  
**Target User:** Tim Internal (Admin & Sales/Estimator)  
**Platform:** Web Application  

## 1. Project Overview & Architecture
Sistem manajemen penawaran (quotation) internal berbasis web. Sistem ini menggunakan kalkulasi dinamis berdasarkan bobot kompleksitas fitur, mendukung multi-mata uang (*cross-currency*) dengan sistem *lock-rate*, dan menghasilkan nota berformat PDF.

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
| `base_price` | Decimal | Total: 15, Places: 2 | Harga dasar |
| `category` | String | Nullable | Pengelompokan fitur |
| `timestamps` | - | - | Created_at, Updated_at |

*   **Eloquent Casts:** `'base_price' => 'decimal:2'`

### C. Tabel `projects` (Dokumen Penawaran)
| Column | Type | Modifiers | Description |
| :--- | :--- | :--- | :--- |
| `id` | ID | Primary Key | - |
| `user_id` | Foreign ID | Constrained, Cascade | Pembuat dokumen |
| `client_name` | String | Required | Nama Klien |
| `currency_code` | String | Length: 3, Default: IDR| Kode mata uang (IDR, USD) |
| `exchange_rate` | Decimal | Total: 15, Places: 2 | Kurs saat dokumen dibuat |
| `grand_total` | Decimal | Total: 15, Places: 2 | Total keseluruhan nota |
| `status` | String | Default: 'Draft' | Enum: Draft, Generated |
| `timestamps` | - | - | Created_at, Updated_at |

*   **Eloquent Casts:** `'exchange_rate' => 'decimal:2'`, `'grand_total' => 'decimal:2'`
*   **Relationships:** `hasMany(ProjectItem::class)`, `belongsTo(User::class)`

### D. Tabel `project_items` (Keranjang Fitur / Line Items)
| Column | Type | Modifiers | Description |
| :--- | :--- | :--- | :--- |
| `id` | ID | Primary Key | - |
| `project_id` | Foreign ID | Constrained, Cascade | Relasi ke tabel projects |
| `module_id` | Foreign ID | Nullable, Set Null | Null jika fitur custom |
| `item_name` | String | Required | Nama fitur (di nota) |
| `base_price` | Decimal | Total: 15, Places: 2 | Harga sebelum bobot & kurs |
| `complexity_weight`| Decimal | Total: 8, Places: 2 | Default 1.00 |
| `calculated_price` | Decimal | Total: 15, Places: 2 | Final price per item |
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
*   **Tabel:** Kolom pencarian pada `name` dan `category`. Format uang pada `base_price`.
*   **Form:** Input teks standar dan `TextInput::make('base_price')->numeric()`.

### B. ProjectResource (Transaction & Logic)
*   **Data Ownership (Query Builder):** 
    Terapkan modifikasi pada `getEloquentQuery()` untuk memfilter `user_id` jika user bukan Admin.
*   **Header Form (Project Details):**
    *   `Select` untuk `currency_code` (live reaktif).
    *   `TextInput` untuk `exchange_rate` (nilai *default* menyesuaikan mata uang yang dipilih, bisa diedit manual).
*   **Repeater Form (Line Items Logic):**
    Gunakan `Repeater::make('items')->relationship()`.
    *   **Standar vs Custom:** Buat *dropdown* `module_id`. Jika dipilih (standar), gunakan `afterStateUpdated` untuk menyalin `name` ke `item_name` dan `base_price` ke `base_price`. Jika tidak dipilih (NULL), user mengisi manual (custom).
    *   **Kalkulasi Real-time:** Ketika `base_price`, `complexity_weight`, atau `exchange_rate` (dari form induk) berubah, hitung ulang secara otomatis: 
        `calculated_price = (base_price * complexity_weight) / exchange_rate`.
    *   Gunakan `live(onBlur: true)` pada input angka untuk optimalisasi performa UX.
*   **Footer Form:**
    *   `Placeholder` (Read-only) untuk menampilkan komputasi sementara dari `grand_total` sebelum disimpan.

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
    *   Gunakan `Illuminate\Support\Number::currency()` bawan Laravel 10+ (didukung penuh di Laravel 13) untuk *formatting* mata uang berdasarkan `currency_code` milik proyek.
    *   Hitung `grand_total` secara dinamis di *controller* dengan melakukan *sum* pada kolom `calculated_price` dari relasi `items`, lalu simpan hasilnya kembali ke database jika perlu (atau tampilkan langsung).