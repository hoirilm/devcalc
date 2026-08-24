<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);

        // 2. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@devcalc.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);

        $sales = User::firstOrCreate(
            ['email' => 'sales@devcalc.test'],
            [
                'name' => 'Sales Estimator',
                'password' => Hash::make('password'),
            ]
        );
        $sales->assignRole($salesRole);

        // 3. Modules Catalog (Fitur Umum & Realistis untuk Pemula / Web App Sederhana)
        $modules = [
            // --- KATEGORI 1: FRONTEND & TAMPILAN ---
            [
                'name' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)',
                'category' => 'Frontend & Tampilan',
                'description' => 'Desain tampilan antarmuka modern yang responsif di smartphone, tablet, dan desktop. Dilengkapi section hero banner, keunggulan, galeri singkat, dan tombol ajakan bertindak (Call to Action).',
                'base_price' => 750000.00,
                'subscription_price' => 50000.00,
            ],
            [
                'name' => 'Katalog Produk / Portfolio Galeri Interaktif',
                'category' => 'Frontend & Tampilan',
                'description' => 'Tampilan grid/list katalog produk atau karya portfolio interaktif dengan fitur filter kategori instan, pencarian cepat, dan pop-up modal rincian spesifikasi.',
                'base_price' => 500000.00,
                'subscription_price' => 40000.00,
            ],
            [
                'name' => 'Blog / Artikel Berita & Optimasi SEO (Meta Tags & Sitemap)',
                'category' => 'Frontend & Tampilan',
                'description' => 'Halaman publikasi artikel/berita berkala lengkap dengan optimasi SEO friendly (OpenGraph tags, auto sitemap XML, dan schema markup Google).',
                'base_price' => 600000.00,
                'subscription_price' => 45000.00,
            ],
            [
                'name' => 'Fitur Dark Mode & Multi-Bahasa Sederhana (ID / EN)',
                'category' => 'Frontend & Tampilan',
                'description' => 'Tombol toggle tema gelap/terang otomatis mengikuti preferensi sistem pengguna serta pengatur peralihan bahasa (Bahasa Indonesia & English).',
                'base_price' => 350000.00,
                'subscription_price' => 25000.00,
            ],

            // --- KATEGORI 2: AUTENTIKASI & PENGGUNA ---
            [
                'name' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)',
                'category' => 'Autentikasi & Pengguna',
                'description' => 'Sistem keamanan akun lengkap: form pendaftaran, login aman dengan enkripsi password Bcrypt, reset password via email token, dan proteksi brute force.',
                'base_price' => 650000.00,
                'subscription_price' => 50000.00,
            ],
            [
                'name' => 'Login Cepat 1-Klik Akun Google (Google OAuth Login)',
                'category' => 'Autentikasi & Pengguna',
                'description' => 'Memungkinkan pengguna masuk atau mendaftar secara instan menggunakan akun Google mereka tanpa perlu mengetik password manual.',
                'base_price' => 400000.00,
                'subscription_price' => 30000.00,
            ],
            [
                'name' => 'Manajemen Role & Hak Akses Sederhana (Admin vs User Biasa)',
                'category' => 'Autentikasi & Pengguna',
                'description' => 'Pemisahan hak akses bertingkat (misal: Admin pengelola sistem vs Pelanggan/Member biasa) dengan pembatasan menu dan rute halaman.',
                'base_price' => 550000.00,
                'subscription_price' => 40000.00,
            ],
            [
                'name' => 'Profil Pengguna (Edit Biodata, Foto Profil & Ganti Password)',
                'category' => 'Autentikasi & Pengguna',
                'description' => 'Halaman pengaturan akun pengguna untuk mengubah nama lengkap, email, nomor HP, foto avatar, dan pembaruan kata sandi.',
                'base_price' => 300000.00,
                'subscription_price' => 25000.00,
            ],

            // --- KATEGORI 3: MANAJEMEN DATA & CONTENT MANAGEMENT ---
            [
                'name' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)',
                'category' => 'Manajemen Data & CMS',
                'description' => 'Panel dasbor admin lengkap untuk mengelola data master (tambah, edit, cari, filter, dan hapus) dengan tabel interaktif dan konfirmasi aman.',
                'base_price' => 950000.00,
                'subscription_price' => 75000.00,
            ],
            [
                'name' => 'Formulir Kontak / Pemesanan Dinamis + Notifikasi Email Otomatis',
                'category' => 'Manajemen Data & CMS',
                'description' => 'Formulir input pesan atau pesanan dengan validasi data ketat dan pengiriman notifikasi otomatis ke inbox email pengelola secara real-time.',
                'base_price' => 450000.00,
                'subscription_price' => 35000.00,
            ],
            [
                'name' => 'Upload Gambar & Dokumen (Multi-file + Otomatis Kompres/Resize)',
                'category' => 'Manajemen Data & CMS',
                'description' => 'Fitur upload file gambar (JPG/PNG/WebP) atau dokumen (PDF) dengan pemrosesan otomatis kompresi ukuran file agar website tetap cepat.',
                'base_price' => 400000.00,
                'subscription_price' => 30000.00,
            ],
            [
                'name' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download',
                'category' => 'Manajemen Data & CMS',
                'description' => 'Tombol export rekap data langsung ke spreadsheet Excel (.xlsx) dan pembuatan dokumen laporan invoice/rekap resmi dalam format PDF siap cetak.',
                'base_price' => 550000.00,
                'subscription_price' => 45000.00,
            ],
            [
                'name' => 'Import Data Massal dari Template Excel / CSV',
                'category' => 'Manajemen Data & CMS',
                'description' => 'Kemudahan mengunggah data puluhan hingga ratusan entri sekaligus menggunakan template file Excel/CSV dengan validasi error baris per baris.',
                'base_price' => 500000.00,
                'subscription_price' => 40000.00,
            ],

            // --- KATEGORI 4: INTEGRASI API & GATEWAY ---
            [
                'name' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)',
                'category' => 'Integrasi API & Gateway',
                'description' => 'Integrasi API WA Gateway (Fonnte / Wwebjs / Wablas) untuk mengirimkan pesan konfirmasi pendaftaran, status pesanan, atau invoice langsung ke nomor WhatsApp klien.',
                'base_price' => 700000.00,
                'subscription_price' => 60000.00,
            ],
            [
                'name' => 'Integrasi Payment Gateway Otomatis (QRIS, Midtrans, Xendit, atau Tripay)',
                'category' => 'Integrasi API & Gateway',
                'description' => 'Menerima pembayaran digital otomatis: QRIS instan, Virtual Account bank (BCA, Mandiri, BRI, BNI), dan e-Wallet (GoPay, OVO, ShopeePay) dengan webhook callback verifikasi otomatis.',
                'base_price' => 1250000.00,
                'subscription_price' => 100000.00,
            ],
            [
                'name' => 'Integrasi Peta Interaktif & Petunjuk Arah (Google Maps / Leaflet Embed)',
                'category' => 'Integrasi API & Gateway',
                'description' => 'Penyematan peta interaktif penanda lokasi toko/kantor cabang dengan koordinat GPS dan tautan langsung ke Google Maps navigasi rute.',
                'base_price' => 450000.00,
                'subscription_price' => 35000.00,
            ],
            [
                'name' => 'Kalkulator Ongkos Kirim Otomatis (Integrasi RajaOngkir API)',
                'category' => 'Integrasi API & Gateway',
                'description' => 'Pengecekan tarif ongkos kirim ekspedisi (JNE, J&T, SiCepat, POS Indonesia) secara akurat berdasarkan kota asal, kota tujuan, dan berat barang.',
                'base_price' => 600000.00,
                'subscription_price' => 50000.00,
            ],

            // --- KATEGORI 5: HOSTING, DOMAIN & INFRASTRUKTUR SERVER ---
            [
                'name' => 'Shared Hosting Setup & Domain (.com / .id) - Paket 1 Tahun',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Ideal untuk website company profile & toko UMKM. Provider: Hostinger Premium / IDCloudHost Starter / DomaiNesia Extra / Niagahoster. Estimasi paket: SSD 20-50GB, Unlimited Bandwidth, Free SSL Let\'s Encrypt, 1 Domain .com/.id, dan 5 Akun Email Bisnis Resmi (Contoh: info@namadomain.com).',
                'base_price' => 450000.00,
                'subscription_price' => 40000.00,
            ],
            [
                'name' => 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx + Free SSL HTTPS)',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Server mandiri berkinerja tinggi untuk aplikasi web Laravel/NodeJS. Provider: IDCloudHost Cloud VPS / Hostinger KVM 1 / DigitalOcean Droplet Basic / Linode Nanode. Estimasi paket: 1 vCPU, 1-2 GB RAM, 25-50 GB NVMe SSD, OS Ubuntu 24.04 LTS, Nginx Reverse Proxy, PHP-FPM 8.3/8.4, firewall UFW, dan auto-renew SSL HTTPS.',
                'base_price' => 850000.00,
                'subscription_price' => 75000.00,
            ],
            [
                'name' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Infrastruktur siap produksi untuk sistem transaksi tinggi & multi-user. Provider: DigitalOcean Regular CPU / Linode / IDCloudHost Enterprise / AWS Lightsail 4GB. Estimasi paket: 2 vCPU, 4 GB RAM, 80 GB SSD, Docker Containerized Stack, Redis In-Memory Cache/Queue, dan automated daily backup database ke cloud storage eksternal (S3/GCS).',
                'base_price' => 1500000.00,
                'subscription_price' => 120000.00,
            ],
            [
                'name' => 'Konfigurasi Custom Domain & Keamanan Cloudflare CDN / SSL',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Routing DNS manajemen via Cloudflare Free/Pro: Proteksi proteksi Anti-DDoS Layer 7, Global Edge Caching untuk akselerasi loading gambar/asset web di seluruh Indonesia, Web Application Firewall (WAF) rule, dan enkripsi SSL/TLS Full Strict.',
                'base_price' => 250000.00,
                'subscription_price' => 20000.00,
            ],
            [
                'name' => 'Paket Maintenance Sistem, Bugfix & Backup Server Rutin (Bulanan)',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Layanan perawatan teknis berkala: Monitoring uptime 24/7, instalasi security patch OS & library framework, audit utilisasi CPU/RAM/Storage, pengecekan berkala log error, serta jaminan SLA penanganan bug/error teknis.',
                'base_price' => 400000.00,
                'subscription_price' => 50000.00,
            ],
        ];

        // Hapus modul lama yang tidak ada di daftar baru agar data rapi
        Module::whereNotIn('name', array_column($modules, 'name'))->delete();

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['name' => $module['name']],
                $module
            );
        }

        // 4. Seed Realistic Projects & Addendums
        $this->call(RealisticProjectSeeder::class);
    }
}
