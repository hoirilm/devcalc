<div align="center">
  <img src="public/favicon.svg" width="72" height="72" alt="DevCalc Logo" />
  <h1>DevCalc</h1>
  <p><strong>Software Quotation Estimator & Sales CRM Pipeline Platform</strong></p>
  <p>Platform manajemen penawaran harga software internal berbasis web dengan formula kalkulasi bobot kompleksitas dinamis, skema penagihan multi-model, dan penerbitan nota resmi PDF otomatis.</p>

  <p>
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13" /></a>
    <a href="https://vuejs.org"><img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue 3" /></a>
    <a href="https://inertiajs.com"><img src="https://img.shields.io/badge/Inertia.js-2.x-9553E9?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js" /></a>
    <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind_CSS-v4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS v4" /></a>
    <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-%3E%3D_8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+" /></a>
  </p>
</div>

---

## 📌 Tentang Proyek

**DevCalc** dirancang khusus untuk agensi perangkat lunak, software house, dan konsultan IT guna menyelesaikan kendala klasik dalam penyusunan proposal proyek:
1. **Inkonsistensi Estimasi**: Mencegah perbedaan penentuan harga antar estimator/sales melalui katalog modul terstandarisasi.
2. **Kalkulasi Kompleksitas Dinamis**: Memperhitungkan tingkat kesulitan teknis modul dengan pengali bobot kompleksitas (*Complexity Multiplier*).
3. **Fleksibilitas Model Bisnis**: Mendukung penawaran Beli Putus (*One-off*), Berlangganan (*Subscription* bulanan/tahunan), serta model *Hybrid*.
4. **Otomasi Dokumen**: Menerbitkan nota penawaran resmi berformat PDF berstandar IDR siap kirim ke klien hanya dalam hitungan detik.

---

## ✨ Fitur Utama

### 🧮 1. Dynamic Quotation & CPQ Engine
- **Katalog Master Modul**: Database modul fitur standar lengkap dengan harga dasar (*base price*) dan estimasi biaya sewa/pemeliharaan bulanan (*subscription price*).
- **Pengali Bobot Kompleksitas (1.0x - 3.5x)**: Menyesuaikan harga setiap fitur berdasarkan tingkat kerumitan integrasi atau kebutuhan khusus klien.
- **Dukungan Multi-Skema Penagihan**:
  - **Beli Putus (*One-Off*)**: Pembayaran kontrak penuh untuk pengembangan sistem.
  - **Berlangganan (*Subscription Flat*)**: Kombinasi *Setup Fee* awal + biaya lisensi/perawatan rutin per bulan/tahun.
  - **Berlangganan Per-User (*SaaS Mode*)**: Perhitungan berbasis jumlah pengguna aktif (*user seats*).
  - **Model Hybrid**: Penggabungan fitur modular khusus + biaya per-user + biaya setup.
- **Manajemen Addendum & Revisi**: Pelacakan riwayat perubahan cakupan fitur (*scope change*) pada kontrak yang telah berjalan.

### 📄 2. Automated Official PDF Generator
- Menghasilkan dokumen penawaran PDF resmi (*letterhead*, rincian tabel modul, pembagian termin pembayaran, klausul garansi/SLA, dan blok tanda tangan).
- Didukung oleh `barryvdh/laravel-dompdf` dengan penanganan presisi mata uang Rupiah (`IDR`).

### 📊 3. Sales CRM & Pipeline Tracker
- **Kanban Board Interaktif**: Visualisasi alur kesepakatan (*Lead*, *Qualified*, *Proposal Sent*, *Negotiation*, *Won*, *Lost*).
- **Direktori Klien & Kontak**: Manajemen database klien korporat beserta kontak person per divisi.
- **Pencatatan Aktivitas (*Activity Log*)**: Rekam jejak meeting, panggilan telepon, email, dan catatan negosiasi.
- **Analitik Real-Time**: Monitoring *pipeline value*, *win rate percentage*, dan proyeksi pendapatan.

### 🔒 4. Keamanan & Role-Based Access Control (RBAC)
- Manajemen hak akses terintegrasi menggunakan `spatie/laravel-permission`:
  - **Admin**: Akses penuh ke master data harga, konfigurasi sistem, dan semua penawaran.
  - **Sales / Estimator**: Pembuatan penawaran dan pengelolaan deal pribadi secara terisolasi via *Eloquent Policy*.

---

## 📐 Formula Kalkulasi Penawaran

$$\text{Item Price} = \text{Base Price} \times \text{Complexity Multiplier}$$

| Model Kontrak | Formula Grand Total |
| :--- | :--- |
| **Putus Kontrak (One-Off)** | $\text{Grand Total} = \sum \text{Item Price}$ |
| **Berlangganan (Flat)** | $\text{Grand Total} = \text{Setup Fee} + \left( \sum \text{Item Price} \times \text{Durasi} \right)$ |
| **Berlangganan (Per-User)** | $\text{Grand Total} = \text{Setup Fee} + \left( (\text{User Count} \times \text{Price/User}) \times \text{Durasi} \right)$ |
| **Hybrid** | $\text{Grand Total} = \text{Setup Fee} + \left( \left(\sum \text{Item Price} + (\text{User Count} \times \text{Price/User})\right) \times \text{Durasi} \right)$ |

---

## 🛠️ Tech Stack

| Layer | Teknologi |
| :--- | :--- |
| **Backend Framework** | [Laravel 13](https://laravel.com/) (PHP 8.2+) |
| **Frontend Framework** | [Vue.js 3](https://vuejs.org/) (Composition API, `<script setup>`) |
| **Adapter Layer** | [Inertia.js v2](https://inertiajs.com/) |
| **Styling & Design** | [Tailwind CSS v4](https://tailwindcss.com/) & [Lucide Icons](https://lucide.dev/) |
| **Tipografi** | [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans) & [Inter](https://fonts.google.com/specimen/Inter) |
| **Database** | SQLite (Default) / MySQL / PostgreSQL |
| **PDF Renderer** | [Laravel-DomPDF](https://github.com/barryvdh/laravel-dompdf) |
| **Otorisasi & RBAC** | [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) |

---

## 🚀 Panduan Instalasi & Menjalankan Lokal

### Prasyarat Sistem
Pastikan perangkat Anda telah terpasang:
- **PHP** >= 8.2 (dengan ekstensi `pdo_sqlite`, `mbstring`, `openssl`, `gd`)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **Laravel Herd** / **Valet** / PHP Built-in Server

### Langkah-demi-Langkah

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/username/devcalc.git
   cd devcalc
   ```

2. **Install Dependensi PHP & Node**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment (`.env`)**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeding Data Awal**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**:
   - Jika menggunakan **Laravel Herd**: Buka langsung `https://devcalc.test` di browser.
   - Jika menggunakan **PHP CLI**:
     ```bash
     # Terminal 1 (Backend Server)
     php artisan serve

     # Terminal 2 (Vite Hot Reload)
     npm run dev
     ```
   - Untuk kompilasi bundle produksi:
     ```bash
     npm run build
     ```

---

## 🔑 Kredensial Pengguna Bawaan (Seeder)

Setelah menjalankan `php artisan migrate --seed`, Anda dapat langsung masuk menggunakan akun default berikut:

| Role | Email | Kata Sandi | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@devcalc.test` | `password` | Akses penuh (Master Modul, User, Setting, Seluruh Penawaran & CRM) |
| **Sales Estimator** | `sales@devcalc.test` | `password` | Akses pembuatan penawaran & pelacakan deal pipeline personal |

---

## 📁 Struktur Direktori Utama

```
devcalc/
├── app/
│   ├── Http/Controllers/    # Controller API & Inertia Gateway
│   ├── Models/              # Eloquent Models (Project, Module, Deal, Client, dll.)
│   └── Policies/            # Spatie & Laravel Gate Authorization Policies
├── database/
│   ├── migrations/          # Struktur skema tabel database
│   └── seeders/             # Data awal katalog modul, role, & akun demo
├── resources/
│   ├── css/                 # Konfigurasi Tailwind CSS v4 & theme tokens
│   ├── js/
│   │   ├── Components/      # Komponen UI Vue reusable (AppLogo, Modal, Charts)
│   │   ├── Layouts/         # Shell AppLayout (Sidebar, Navbar, Theme Toggle)
│   │   └── Pages/           # Halaman Inertia (Auth, Dashboard, Deals, Projects)
│   └── views/               # Blade root template (app.blade.php) & PDF templates
└── routes/
    └── web.php              # Rute aplikasi terproteksi auth middleware
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).  
Hak Cipta &copy; 2026 **DevCalc Engine**. Seluruh hak cipta dilindungi undang-undang.
