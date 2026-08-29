<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealActivity;
use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RealisticProjectSeeder extends Seeder
{
    /**
     * Run the comprehensive realistic database seeder.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. Roles & Permissions Setup
        // ---------------------------------------------------------------------
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);

        // ---------------------------------------------------------------------
        // 2. Realistic Team Users
        // ---------------------------------------------------------------------
        $admin = User::updateOrCreate(
            ['email' => 'admin@devcalc.test'],
            [
                'name' => 'Hoiril Mochtar (Principal Architect)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$adminRole]);

        $sales1 = User::updateOrCreate(
            ['email' => 'sales@devcalc.test'],
            [
                'name' => 'Dimas Wicaksono (Senior Tech Sales)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $sales1->syncRoles([$salesRole]);

        $sales2 = User::updateOrCreate(
            ['email' => 'sarah@devcalc.test'],
            [
                'name' => 'Sarah Az-Zahra (Enterprise Account Exec)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $sales2->syncRoles([$salesRole]);

        $estimator = User::updateOrCreate(
            ['email' => 'rio@devcalc.test'],
            [
                'name' => 'Rio Pratama (Technical Consultant)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $estimator->syncRoles([$salesRole]);

        // ---------------------------------------------------------------------
        // 3. Standard Modules Catalog (21 Feature Modules)
        // ---------------------------------------------------------------------
        $modulesCatalog = [
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
                'description' => 'Ideal untuk website company profile & toko UMKM. Provider: Hostinger Premium / IDCloudHost Starter / DomaiNesia Extra. Estimasi paket: SSD 20-50GB, Unlimited Bandwidth, Free SSL Let\'s Encrypt, 1 Domain .com/.id, dan 5 Akun Email Bisnis Resmi.',
                'base_price' => 450000.00,
                'subscription_price' => 40000.00,
            ],
            [
                'name' => 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx + Free SSL HTTPS)',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Server mandiri berkinerja tinggi untuk aplikasi web Laravel/NodeJS. Provider: IDCloudHost Cloud VPS / Hostinger KVM 1 / DigitalOcean Droplet Basic. Estimasi paket: 1 vCPU, 1-2 GB RAM, 25-50 GB NVMe SSD, OS Ubuntu 24.04 LTS, Nginx Reverse Proxy, PHP-FPM 8.3, firewall UFW, dan auto-renew SSL HTTPS.',
                'base_price' => 850000.00,
                'subscription_price' => 75000.00,
            ],
            [
                'name' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Infrastruktur siap produksi untuk sistem transaksi tinggi & multi-user. Provider: DigitalOcean Regular CPU / Linode / IDCloudHost Enterprise. Estimasi paket: 2 vCPU, 4 GB RAM, 80 GB SSD, Docker Containerized Stack, Redis In-Memory Cache/Queue, dan automated daily backup database ke cloud storage eksternal (S3/GCS).',
                'base_price' => 1500000.00,
                'subscription_price' => 120000.00,
            ],
            [
                'name' => 'Konfigurasi Custom Domain & Keamanan Cloudflare CDN / SSL',
                'category' => 'Hosting & Infrastruktur',
                'description' => 'Routing DNS manajemen via Cloudflare: Proteksi Anti-DDoS Layer 7, Global Edge Caching untuk akselerasi loading gambar/asset web, Web Application Firewall (WAF) rule, dan enkripsi SSL/TLS Full Strict.',
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

        $modulesMap = [];
        foreach ($modulesCatalog as $modData) {
            $m = Module::updateOrCreate(['name' => $modData['name']], $modData);
            $modulesMap[$m->name] = $m;
        }

        // ---------------------------------------------------------------------
        // 4. Reset CRM, Projects & Activities Tables for Clean Production Slate
        // ---------------------------------------------------------------------
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            DB::table('deal_activities')->delete();
            DB::table('project_items')->delete();
            DB::table('projects')->delete();
            DB::table('deals')->delete();
            DB::table('contacts')->delete();
            DB::table('clients')->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name IN ('deal_activities', 'project_items', 'projects', 'deals', 'contacts', 'clients')");
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('deal_activities')->truncate();
            DB::table('project_items')->truncate();
            DB::table('projects')->truncate();
            DB::table('deals')->truncate();
            DB::table('contacts')->truncate();
            DB::table('clients')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // ---------------------------------------------------------------------
        // 5. Realistic Clients & PIC Contacts Data
        // ---------------------------------------------------------------------
        $clientsDefinitions = [
            [
                'name' => 'PT Kenangan Retail Nusantara',
                'industry' => 'Food & Beverage / Retail',
                'email' => 'corporate@kenangansenja.id',
                'phone' => '081234567890',
                'website' => 'https://kenangansenja.id',
                'address' => 'Jl. Senopati Raya No. 45, Kebayoran Baru, Jakarta Selatan 12190',
                'status' => 'active',
                'user_id' => $sales1->id,
                'notes' => 'Jaringan gerai kopi & resto dengan 18 cabang aktif di Jabodetabek & Bandung. Mengembangkan sistem POS Omnichannel, QR Dine-in, dan web ordering terintegrasi WhatsApp gateway.',
                'contacts' => [
                    [
                        'name' => 'Budi Santoso, S.Kom',
                        'title' => 'Chief Technology Officer (CTO)',
                        'email' => 'budi.santoso@kenangansenja.id',
                        'phone' => '081298765432',
                        'is_primary' => true,
                        'notes' => 'Pengambil keputusan teknis utama. Sangat responsif via WhatsApp dan menyukai arsitektur modern berbasis API.',
                    ],
                    [
                        'name' => 'Rina Wijaya, S.E.',
                        'title' => 'Head of Procurement & Finance',
                        'email' => 'rina.w@kenangansenja.id',
                        'phone' => '081311223344',
                        'is_primary' => false,
                        'notes' => 'Menangani legalitas kontrak, Purchase Order resmi (PO), dan term of payment (termin DP 50%).',
                    ],
                ],
            ],
            [
                'name' => 'PT Logistik Prima Express',
                'industry' => 'Logistics & Supply Chain',
                'email' => 'info@primaexpress.co.id',
                'phone' => '081122334455',
                'website' => 'https://primaexpress.co.id',
                'address' => 'Kawasan Industri MM2100 Blok C-3, Cikarang Barat, Bekasi, Jawa Barat 17530',
                'status' => 'active',
                'user_id' => $sales1->id,
                'notes' => 'Perusahaan logistik kargo B2B nasional dengan 150+ armada truk pendingin & box kontainer. Butuh Transport Management System (TMS) realtime & live dispatching.',
                'contacts' => [
                    [
                        'name' => 'Hendra Pratama, M.T.',
                        'title' => 'VP of Digital Transformation',
                        'email' => 'hendra.p@primaexpress.co.id',
                        'phone' => '081288990011',
                        'is_primary' => true,
                        'notes' => 'Fokus utama pada skalabilitas server VPS, auto-backup database rutin, dan integrasi webhook GPS tracker.',
                    ],
                    [
                        'name' => 'Yudi Kurniawan',
                        'title' => 'Head of Fleet Operations',
                        'email' => 'yudi.k@primaexpress.co.id',
                        'phone' => '081255667788',
                        'is_primary' => false,
                        'notes' => 'User operasional harian yang menguji kemudahan input rute dan export laporan manifest ke Excel.',
                    ],
                ],
            ],
            [
                'name' => 'PT Medika Digital Nusantara',
                'industry' => 'Healthcare & MedTech',
                'email' => 'contact@medikanusantara.com',
                'phone' => '081566778899',
                'website' => 'https://medikanusantara.com',
                'address' => 'Cyber 2 Tower Lt. 18, Jl. HR Rasuna Said Blok X-5, Kuningan, Jakarta Selatan 12950',
                'status' => 'active',
                'user_id' => $admin->id,
                'notes' => 'Penyedia platform telemedicine & sistem manajemen klinik terpadu (HIS/EMR) yang wajib patuh standar integrasi SatuSehat Kemenkes RI dan UU Perlindungan Data Pribadi (UU PDP).',
                'contacts' => [
                    [
                        'name' => 'dr. Anita Rahmawati, Sp.PK',
                        'title' => 'Managing Director & Founder',
                        'email' => 'dr.anita@medikanusantara.com',
                        'phone' => '081399887766',
                        'is_primary' => true,
                        'notes' => 'Pemilik yayasan medika. Sangat teliti dalam privasi data rekam medis elektronik dan SLA keamanan server Cloudflare.',
                    ],
                    [
                        'name' => 'Kevin Alamsyah, M.Kom',
                        'title' => 'Lead Product Architect',
                        'email' => 'kevin@medikanusantara.com',
                        'phone' => '085612345678',
                        'is_primary' => false,
                        'notes' => 'Reviewer teknis yang memverifikasi flow autentikasi role bertingkat (Dokter, Perawat, Apoteker, Kasir, Admin).',
                    ],
                ],
            ],
            [
                'name' => 'PT Finansial Sejahtera Pintar',
                'industry' => 'Fintech & Financial Services',
                'email' => 'business@finsejahtera.co.id',
                'phone' => '081900112233',
                'website' => 'https://finsejahtera.co.id',
                'address' => 'Sudirman Central Business District (SCBD) Lot 9, Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan 12190',
                'status' => 'prospect',
                'user_id' => $sales2->id,
                'notes' => 'Lembaga fintech micro-financing & wealth technology berizin OJK. Membutuhkan portal survei lapangan petugas kredit berbasis skema user subscription bulanan.',
                'contacts' => [
                    [
                        'name' => 'Dimas Aditya, CFA',
                        'title' => 'Head of IT & Information Security',
                        'email' => 'dimas.a@finsejahtera.co.id',
                        'phone' => '081233445566',
                        'is_primary' => true,
                        'notes' => 'Meminta simulasi skema hybrid (modul inti + per-user 75 surveyor aktif) dan NDA ketat.',
                    ],
                    [
                        'name' => 'Jessica Tan, S.E.',
                        'title' => 'Head of Compliance & Risk',
                        'email' => 'jessica.tan@finsejahtera.co.id',
                        'phone' => '081822334455',
                        'is_primary' => false,
                        'notes' => 'Melakukan audit keamanan aplikasi dan enkripsi transaksi.',
                    ],
                ],
            ],
            [
                'name' => 'CV Agro Mandiri Makmur',
                'industry' => 'AgriTech & Manufacturing',
                'email' => 'agromandiri@gmail.com',
                'phone' => '082133445566',
                'website' => 'https://agromandiri.co.id',
                'address' => 'Jl. Raya Solo - Yogyakarta KM 15, Delanggu, Klaten, Jawa Tengah 57471',
                'status' => 'active',
                'user_id' => $estimator->id,
                'notes' => 'Pabrik produsen pupuk organik & benih unggul dengan 4 gudang distribusi di Jawa Tengah dan Jawa Timur. Membutuhkan inventory multi-gudang dan cetak barcode faktur.',
                'contacts' => [
                    [
                        'name' => 'Suryo Wibowo, S.T.',
                        'title' => 'General Manager Operasional',
                        'email' => 'suryo.w@agromandiri.co.id',
                        'phone' => '081722334455',
                        'is_primary' => true,
                        'notes' => 'Menyukai penjelasan teknis yang lugas dan berorientasi efisiensi staf gudang non-teknis.',
                    ],
                    [
                        'name' => 'Tri Lestari',
                        'title' => 'Kepala Administrasi & Gudang',
                        'email' => 'tri.lestari@agromandiri.co.id',
                        'phone' => '082233445566',
                        'is_primary' => false,
                        'notes' => 'Menangani input data harian stok barang masuk dan keluar.',
                    ],
                ],
            ],
            [
                'name' => 'PT Solusi Edukasi Digital',
                'industry' => 'Education & EdTech',
                'email' => 'partnership@edudigital.ac.id',
                'phone' => '081277889900',
                'website' => 'https://edudigital.ac.id',
                'address' => 'Gedung Graha Pena Lt. 5, Jl. Ahmad Yani No. 88, Ketintang, Gayungan, Surabaya 60234',
                'status' => 'active',
                'user_id' => $admin->id,
                'notes' => 'Penyedia platform Learning Management System (LMS) dan Computer-Based Testing (CBT) skala nasional untuk 25+ universitas dan sekolah mitra.',
                'contacts' => [
                    [
                        'name' => 'Prof. Dr. Irfan Hakim, M.Eng',
                        'title' => 'Chief Academic & Technology Officer',
                        'email' => 'irfan.hakim@edudigital.ac.id',
                        'phone' => '081199882233',
                        'is_primary' => true,
                        'notes' => 'Ahli sistem terdistribusi. Menilai kapasitas konkurensi server saat ribuan mahasiswa ujian serentak.',
                    ],
                ],
            ],
            [
                'name' => 'PT Properti Graha Lestari',
                'industry' => 'Real Estate & Property',
                'email' => 'info@grahalestari.co.id',
                'phone' => '081388776655',
                'website' => 'https://grahalestari.co.id',
                'address' => 'BSD Green Office Park 6 Lt. 3, Jl. Grand Boulevard, BSD City, Tangerang 15345',
                'status' => 'prospect',
                'user_id' => $sales2->id,
                'notes' => 'Pengembang perumahan modern cluster dan komersial area di Jabodetabek. Butuh portal listing properti interaktif dengan virtual tour 3D dan WhatsApp lead routing.',
                'contacts' => [
                    [
                        'name' => 'Maya Anggraini, B.A.',
                        'title' => 'VP of Marketing & Digital Sales',
                        'email' => 'maya.a@grahalestari.co.id',
                        'phone' => '081377889900',
                        'is_primary' => true,
                        'notes' => 'Fokus pada user experience visual yang estetik dan kecepatan loading gambar resolusi tinggi via CDN.',
                    ],
                ],
            ],
            [
                'name' => 'PT Surya Energi Terbarukan',
                'industry' => 'CleanTech & Industrial IoT',
                'email' => 'contact@suryaenergi.id',
                'phone' => '081244556677',
                'website' => 'https://suryaenergi.id',
                'address' => 'Kawasan Inovasi Dago Asri No. 12, Coblong, Kota Bandung, Jawa Barat 40135',
                'status' => 'lead',
                'user_id' => $estimator->id,
                'notes' => 'Pengembang PLTS (Pembangkit Listrik Tenaga Surya) atap industri. Mengembangkan dashboard telemetry realtime pemantauan daya listrik dan inverter berbasis REST API.',
                'contacts' => [
                    [
                        'name' => 'Ir. Bambang Sugiarto, M.Sc.',
                        'title' => 'Head of IoT & Smart Grid Solutions',
                        'email' => 'bambang.s@suryaenergi.id',
                        'phone' => '081233221100',
                        'is_primary' => true,
                        'notes' => 'Sedang menyusun spesifikasi teknis komunikasi MQTT/REST dari gateway IoT ke server web Laravel.',
                    ],
                ],
            ],
            [
                'name' => 'PT Bahari Nusantara Tour',
                'industry' => 'Hospitality & Tourism Tech',
                'email' => 'reservation@baharitour.com',
                'phone' => '081933445566',
                'website' => 'https://baharitour.com',
                'address' => 'Jl. Sunset Road No. 88, Seminyak, Kuta, Kabupaten Badung, Bali 80361',
                'status' => 'active',
                'user_id' => $sales1->id,
                'notes' => 'Operator tur wisata bahari & kapal pesiar carter di Bali, Labuan Bajo, dan Raja Ampat. Membutuhkan booking engine multi-mata uang dan payment gateway otomatis.',
                'contacts' => [
                    [
                        'name' => 'I Wayan Sudarta, S.E.',
                        'title' => 'Managing Director & Co-Founder',
                        'email' => 'wayan@baharitour.com',
                        'phone' => '081944556677',
                        'is_primary' => true,
                        'notes' => 'Sangat mengapresiasi kemudahan pembayaran QRIS instan dan notifikasi pemesanan instan ke WhatsApp pelanggan.',
                    ],
                    [
                        'name' => 'Kadek Ayu Lestari',
                        'title' => 'Head of Digital Marketing & Customer Service',
                        'email' => 'kadek.ayu@baharitour.com',
                        'phone' => '081855667788',
                        'is_primary' => false,
                        'notes' => 'Mengelola konten paket liburan, promo musiman, dan integrasi live chat WhatsApp.',
                    ],
                ],
            ],
            [
                'name' => 'PT Distribusi Farmasi Prima',
                'industry' => 'Pharma & Cold Chain Logistics',
                'email' => 'contact@farmasiprima.co.id',
                'phone' => '081155667788',
                'website' => 'https://farmasiprima.co.id',
                'address' => 'Kawasan Industri Rungkut SIER Blok E-10, Surabaya, Jawa Timur 60293',
                'status' => 'inactive',
                'user_id' => $sales1->id,
                'notes' => 'Distributor obat dan vaksin bersuhu khusus. Peluang proyek sempat dibuka namun ditunda karena restrukturisasi internal.',
                'contacts' => [
                    [
                        'name' => 'drh. Gunawan Wibisono',
                        'title' => 'Supply Chain & Compliance Director',
                        'email' => 'gunawan@farmasiprima.co.id',
                        'phone' => '081133445566',
                        'is_primary' => true,
                        'notes' => 'PIC saat proses bidding awal.',
                    ],
                ],
            ],
        ];

        $clients = [];
        $contacts = [];

        foreach ($clientsDefinitions as $cDef) {
            $cContacts = $cDef['contacts'] ?? [];
            unset($cDef['contacts']);

            $client = Client::create($cDef);
            $clients[$client->name] = $client;

            foreach ($cContacts as $cntDef) {
                $contact = Contact::create(array_merge($cntDef, ['client_id' => $client->id]));
                $contacts[$client->name . ':' . $contact->name] = $contact;
            }
        }

        // ---------------------------------------------------------------------
        // 6. Realistic Deals Pipeline across All Stages (Won, Negotiation, Proposal Sent, Scoping, Lost)
        // ---------------------------------------------------------------------
        $dealsDefinitions = [
            // --- STAGE: WON (Closed Won) ---
            [
                'key' => 'deal_kenangan_pos',
                'client_name' => 'PT Kenangan Retail Nusantara',
                'user_id' => $sales1->id,
                'title' => 'Omnichannel F&B POS & Web Store Multi-Cabang',
                'stage' => 'won',
                'expected_value' => 45000000.00,
                'probability' => 100,
                'expected_close_date' => Carbon::now()->subDays(14)->toDateString(),
                'notes' => 'Deal disetujui manajemen. Dokumen penawaran QUO-00001 ditandatangani, DP 50% telah diterima via Bank Mandiri Escrow, dan sprint development sedang berjalan.',
            ],
            [
                'key' => 'deal_prima_tms',
                'client_name' => 'PT Logistik Prima Express',
                'user_id' => $sales1->id,
                'title' => 'Enterprise Transport Management System (TMS) & Fleet Tracking',
                'stage' => 'won',
                'expected_value' => 82500000.00,
                'probability' => 100,
                'expected_close_date' => Carbon::now()->subDays(7)->toDateString(),
                'notes' => 'Proposal QUO-00002 disetujui setelah presentasi teknis di Cikarang. Skema include Cloud VPS dedicated dan masa garansi SLA 6 bulan.',
            ],
            [
                'key' => 'deal_medika_his',
                'client_name' => 'PT Medika Digital Nusantara',
                'user_id' => $admin->id,
                'title' => 'Hospital Information System (HIS) & SatuSehat Clinic Portal',
                'stage' => 'won',
                'expected_value' => 68000000.00,
                'probability' => 100,
                'expected_close_date' => Carbon::now()->subDays(21)->toDateString(),
                'notes' => 'Deal resmi dimenangkan. Klien memilih paket komprehensif one-off dengan server VPS High-Performance dan SLA pemeliharaan 12 bulan penuh.',
            ],
            [
                'key' => 'deal_edudigital_lms',
                'client_name' => 'PT Solusi Edukasi Digital',
                'user_id' => $admin->id,
                'title' => 'Multi-Campus Cloud LMS & Computer-Based Testing Engine',
                'stage' => 'won',
                'expected_value' => 42000000.00,
                'probability' => 100,
                'expected_close_date' => Carbon::now()->subDays(30)->toDateString(),
                'notes' => 'Kontrak berlangganan tahunan (Subscription Modular) aktif selama 12 bulan untuk 25 kampus mitra. Setup fee dan onboarding selesai.',
            ],
            [
                'key' => 'deal_bahari_tour',
                'client_name' => 'PT Bahari Nusantara Tour',
                'user_id' => $sales1->id,
                'title' => 'Booking Engine & Dynamic Tour Package Portal',
                'stage' => 'won',
                'expected_value' => 32000000.00,
                'probability' => 100,
                'expected_close_date' => Carbon::now()->subDays(10)->toDateString(),
                'notes' => 'Klien sepakat dengan modul booking engine, katalog produk dinamis, dan payment gateway Midtrans QRIS. Deployment telah live.',
            ],

            // --- STAGE: NEGOTIATION (Negosiasi & Review) ---
            [
                'key' => 'deal_finsejahtera_app',
                'client_name' => 'PT Finansial Sejahtera Pintar',
                'user_id' => $sales2->id,
                'title' => 'Field Officer Microfinance & Credit Scoring App',
                'stage' => 'negotiation',
                'expected_value' => 120000000.00,
                'probability' => 80,
                'expected_close_date' => Carbon::now()->addDays(5)->toDateString(),
                'notes' => 'Tahap review kontrak final. Klien meminta klausul SLA 99.9% dan diskon tahunan 15% untuk paket subscription per-user (75 surveyor lapangan).',
            ],
            [
                'key' => 'deal_agro_inventory',
                'client_name' => 'CV Agro Mandiri Makmur',
                'user_id' => $estimator->id,
                'title' => 'Smart Warehouse Management & QR Barcode Dispatch',
                'stage' => 'negotiation',
                'expected_value' => 28500000.00,
                'probability' => 80,
                'expected_close_date' => Carbon::now()->addDays(8)->toDateString(),
                'notes' => 'Pak Suryo menyetujui rincian modul DevCalc QUO-00004. Sedang menunggu persetujuan anggaran final dari Direktur Utama CV Agro.',
            ],

            // --- STAGE: PROPOSAL_SENT (Proposal Terkirim) ---
            [
                'key' => 'deal_graha_property',
                'client_name' => 'PT Properti Graha Lestari',
                'user_id' => $sales2->id,
                'title' => 'Smart Property 3D Virtual Tour & Lead CRM Portal',
                'stage' => 'proposal_sent',
                'expected_value' => 38000000.00,
                'probability' => 60,
                'expected_close_date' => Carbon::now()->addDays(14)->toDateString(),
                'notes' => 'Draft estimasi DevCalc QUO-00006 dan lampiran proposal teknis PDF telah dikirim ke Ibu Maya Anggraini. Menunggu jadwal rapat evaluasi direksi BSD.',
            ],
            [
                'key' => 'deal_medika_telemed',
                'client_name' => 'PT Medika Digital Nusantara',
                'user_id' => $admin->id,
                'title' => 'Telemedicine SaaS & Doctor Consultation On-Demand',
                'stage' => 'proposal_sent',
                'expected_value' => 55000000.00,
                'probability' => 60,
                'expected_close_date' => Carbon::now()->addDays(18)->toDateString(),
                'notes' => 'Penawaran hybrid SaaS (modul konsultasi video + 50 lisensi dokter) telah diajukan ke dr. Anita Rahmawati.',
            ],

            // --- STAGE: SCOPING (Scoping & Draf) ---
            [
                'key' => 'deal_surya_iot',
                'client_name' => 'PT Surya Energi Terbarukan',
                'user_id' => $estimator->id,
                'title' => 'IoT Solar Telemetry Dashboard & Smart Inverter Alert',
                'stage' => 'scoping',
                'expected_value' => 54000000.00,
                'probability' => 30,
                'expected_close_date' => Carbon::now()->addDays(28)->toDateString(),
                'notes' => 'Sedang merumuskan arsitektur pipeline data throughput tinggi (MQTT broker ke Laravel WebSocket) dan estimasi server cloud VPS.',
            ],

            // --- STAGE: LOST (Closed Lost) ---
            [
                'key' => 'deal_farmasi_coldchain',
                'client_name' => 'PT Distribusi Farmasi Prima',
                'user_id' => $sales1->id,
                'title' => 'Cold Chain Storage Temperature & Batch Tracking System',
                'stage' => 'lost',
                'expected_value' => 35000000.00,
                'probability' => 0,
                'expected_close_date' => Carbon::now()->subDays(45)->toDateString(),
                'lost_reason' => 'Klien menunda anggaran implementasi digitalisasi sampai audit kepatuhan BPOM internal selesai pada Q4.',
                'notes' => 'Akan dihubungi kembali pada awal November untuk evaluasi pembukaan anggaran tahun fiskal berikutnya.',
            ],
        ];

        $deals = [];
        foreach ($dealsDefinitions as $dDef) {
            $key = $dDef['key'];
            $cName = $dDef['client_name'];
            unset($dDef['key'], $dDef['client_name']);

            $client = $clients[$cName] ?? null;
            if (!$client) continue;

            $deal = Deal::create(array_merge($dDef, ['client_id' => $client->id]));
            $deals[$key] = $deal;
        }

        // ---------------------------------------------------------------------
        // 7. Comprehensive Realistic Projects / Quotations with Full Formulas & Items
        // ---------------------------------------------------------------------
        $projectsDefinitions = [
            // =================================================================
            // PROYEK 1: ONE-OFF B2B E-COMMERCE & POS OMNICHANNEL
            // =================================================================
            [
                'client_name' => 'PT Kenangan Retail Nusantara',
                'user_id' => $sales1->id,
                'deal_key' => 'deal_kenangan_pos',
                'project_category' => 'E-Commerce & Toko Online',
                'estimated_timeline' => '2 - 3 Bulan (Komprehensif Enterprise)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'created_at' => Carbon::now()->subDays(20),
                'notes' => "Pengembangan platform Omnichannel F&B terpadu:\n1. Front-end website katalog menu responsif & mobile-friendly.\n2. Sistem autentikasi role kasir, admin outlet, dan super admin pusat.\n3. Integrasi payment gateway otomatis QRIS instan & Virtual Account bank (Midtrans/Tripay).\n4. Integrasi WhatsApp Gateway untuk pengiriman struk pesanan & tracking order real-time.\n5. Infrastruktur Cloud VPS Nginx Ubuntu + SSL Cloudflare WAF + Garansi Pemeliharaan SLA 6 Bulan.",
                'items' => [
                    ['module' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)', 'complexity' => 1.25],
                    ['module' => 'Katalog Produk / Portfolio Galeri Interaktif', 'complexity' => 1.50],
                    ['module' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)', 'complexity' => 1.00],
                    ['module' => 'Manajemen Role & Hak Akses Sederhana (Admin vs User Biasa)', 'complexity' => 1.25],
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 1.50],
                    ['module' => 'Integrasi Payment Gateway Otomatis (QRIS, Midtrans, Xendit, atau Tripay)', 'complexity' => 1.50],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.25],
                    ['module' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download', 'complexity' => 1.00],
                    ['module' => 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx + Free SSL HTTPS)', 'complexity' => 1.00],
                    ['module' => 'Konfigurasi Custom Domain & Keamanan Cloudflare CDN / SSL', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 2: ONE-OFF ENTERPRISE TRANSPORT MANAGEMENT SYSTEM (TMS)
            // =================================================================
            [
                'client_name' => 'PT Logistik Prima Express',
                'user_id' => $sales1->id,
                'deal_key' => 'deal_prima_tms',
                'project_category' => 'Sistem Informasi Internal / ERP & CRM',
                'estimated_timeline' => '3 - 6 Bulan (Multi-Phase Project)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'created_at' => Carbon::now()->subDays(12),
                'notes' => "Pengembangan Sistem Transport Management System (TMS) Armada Logistik:\n1. Autentikasi keamanan tinggi dan manajemen role bertingkat (Dispatcher, Driver, Manajer Gudang, Direksi).\n2. Dasbor monitoring penugasan armada dan manifest pengiriman barang.\n3. Integrasi peta Google Maps API untuk plotting rute antar-kota dan pelacakan titik henti.\n4. Import massal manifest pengiriman dari Excel dan export laporan operasional PDF.\n5. High-Performance VPS Server dengan Redis queue dan daily auto-backup database.",
                'items' => [
                    ['module' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)', 'complexity' => 1.25],
                    ['module' => 'Manajemen Role & Hak Akses Sederhana (Admin vs User Biasa)', 'complexity' => 1.50],
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 2.00],
                    ['module' => 'Integrasi Peta Interaktif & Petunjuk Arah (Google Maps / Leaflet Embed)', 'complexity' => 1.75],
                    ['module' => 'Import Data Massal dari Template Excel / CSV', 'complexity' => 1.50],
                    ['module' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download', 'complexity' => 1.25],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.25],
                    ['module' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)', 'complexity' => 1.00],
                    ['module' => 'Konfigurasi Custom Domain & Keamanan Cloudflare CDN / SSL', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 3: ONE-OFF HOSPITAL INFORMATION SYSTEM & SATUSEHAT PORTAL
            // =================================================================
            [
                'client_name' => 'PT Medika Digital Nusantara',
                'user_id' => $admin->id,
                'deal_key' => 'deal_medika_his',
                'project_category' => 'Sistem Informasi Internal / ERP & CRM',
                'estimated_timeline' => '3 - 6 Bulan (Multi-Phase Project)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 0.00,
                'created_at' => Carbon::now()->subDays(25),
                'notes' => "Pengembangan Sistem Informasi Manajemen Rumah Sakit & Klinik (SIMRS/HIS):\n1. Standarisasi Rekam Medis Elektronik (RME) sesuai format FHIR SatuSehat Kemenkes RI.\n2. Hak akses multi-role (Dokter Spesialis, Perawat, Laboratorium, Farmasi, Kasir, Admin Rekam Medis).\n3. Upload dokumen hasil lab dan resep digital dengan enkripsi AES-256.\n4. Cetak faktur tagihan pasien, resep obat PDF, dan rekapitulasi data BPJS/Asuransi ke Excel.\n5. Infrastruktur High-Performance VPS Server dengan Redis & Cloudflare WAF + Garansi Pemeliharaan SLA 12 Bulan Penuh.",
                'items' => [
                    ['module' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)', 'complexity' => 1.50],
                    ['module' => 'Manajemen Role & Hak Akses Sederhana (Admin vs User Biasa)', 'complexity' => 2.00],
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 2.00],
                    ['module' => 'Upload Gambar & Dokumen (Multi-file + Otomatis Kompres/Resize)', 'complexity' => 1.50],
                    ['module' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download', 'complexity' => 1.50],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.25],
                    ['module' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)', 'complexity' => 1.00],
                    ['module' => 'Konfigurasi Custom Domain & Keamanan Cloudflare CDN / SSL', 'complexity' => 1.00],
                    ['module' => 'Paket Maintenance Sistem, Bugfix & Backup Server Rutin (Bulanan)', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 4: ONE-OFF SMART WAREHOUSE MANAGEMENT SYSTEM
            // =================================================================
            [
                'client_name' => 'CV Agro Mandiri Makmur',
                'user_id' => $estimator->id,
                'deal_key' => 'deal_agro_inventory',
                'project_category' => 'Sistem Informasi Internal / ERP & CRM',
                'estimated_timeline' => '1 - 2 Bulan (Skala Sedang)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 3,
                'setup_fee' => 0.00,
                'created_at' => Carbon::now()->subDays(5),
                'notes' => "Pengembangan Sistem Manajemen Inventori & Pergudangan Agro:\n1. Input barang masuk/keluar berbasis scan QR barcode.\n2. Rekonsiliasi stok multi-gudang (Klaten, Solo, Jogja, Semarang).\n3. Import data stok awal dari Excel dan export surat jalan siap cetak PDF.\n4. Shared Hosting Enterprise 1 Tahun dan domain .co.id resmi.",
                'items' => [
                    ['module' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)', 'complexity' => 1.00],
                    ['module' => 'Manajemen Role & Hak Akses Sederhana (Admin vs User Biasa)', 'complexity' => 1.25],
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 1.50],
                    ['module' => 'Import Data Massal dari Template Excel / CSV', 'complexity' => 1.25],
                    ['module' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download', 'complexity' => 1.25],
                    ['module' => 'Shared Hosting Setup & Domain (.com / .id) - Paket 1 Tahun', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 5: ONE-OFF BOOKING ENGINE & TOUR PORTAL
            // =================================================================
            [
                'client_name' => 'PT Bahari Nusantara Tour',
                'user_id' => $sales1->id,
                'deal_key' => 'deal_bahari_tour',
                'project_category' => 'E-Commerce & Toko Online',
                'estimated_timeline' => '3 - 4 Minggu (Standar Pengerjaan)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'created_at' => Carbon::now()->subDays(15),
                'notes' => "Pengembangan Web Portal Reservasi Wisata Bahari & Kapal Pesiar:\n1. Landing page modern bergaya resort & katalog paket tur interaktif.\n2. Booking form dinamis dengan pemilihan tanggal kalender dan jumlah pax.\n3. Integrasi payment gateway QRIS & Credit Card otomatis.\n4. Integrasi WhatsApp auto-responder untuk voucher tiket elektronik.",
                'items' => [
                    ['module' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)', 'complexity' => 1.25],
                    ['module' => 'Katalog Produk / Portfolio Galeri Interaktif', 'complexity' => 1.25],
                    ['module' => 'Formulir Kontak / Pemesanan Dinamis + Notifikasi Email Otomatis', 'complexity' => 1.25],
                    ['module' => 'Integrasi Payment Gateway Otomatis (QRIS, Midtrans, Xendit, atau Tripay)', 'complexity' => 1.25],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.00],
                    ['module' => 'Fitur Dark Mode & Multi-Bahasa Sederhana (ID / EN)', 'complexity' => 1.00],
                    ['module' => 'Shared Hosting Setup & Domain (.com / .id) - Paket 1 Tahun', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 6: DRAFT ONE-OFF SMART PROPERTY LISTING PORTAL
            // =================================================================
            [
                'client_name' => 'PT Properti Graha Lestari',
                'user_id' => $sales2->id,
                'deal_key' => 'deal_graha_property',
                'project_category' => 'Company Profile & Landing Page',
                'estimated_timeline' => '3 - 4 Minggu (Standar Pengerjaan)',
                'billing_type' => 'one_off',
                'status' => 'Draft',
                'maintenance_months' => 3,
                'setup_fee' => 0.00,
                'created_at' => Carbon::now()->subDays(3),
                'notes' => "Draf penawaran web portal showcase cluster perumahan mewah BSD:\n1. Desain landing page prestisius dengan galeri foto unit resolusi tinggi.\n2. Integrasi Google Maps petunjuk arah ke show unit lokasi.\n3. Form reservasi jadwal kunjungan sales yang terhubung ke WhatsApp PIC sales marketing.",
                'items' => [
                    ['module' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)', 'complexity' => 1.25],
                    ['module' => 'Katalog Produk / Portfolio Galeri Interaktif', 'complexity' => 1.25],
                    ['module' => 'Integrasi Peta Interaktif & Petunjuk Arah (Google Maps / Leaflet Embed)', 'complexity' => 1.00],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.00],
                    ['module' => 'Shared Hosting Setup & Domain (.com / .id) - Paket 1 Tahun', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 7: DRAFT ONE-OFF IOT SOLAR TELEMETRY DASHBOARD
            // =================================================================
            [
                'client_name' => 'PT Surya Energi Terbarukan',
                'user_id' => $estimator->id,
                'deal_key' => 'deal_surya_iot',
                'project_category' => 'Custom Backend API & Integrasi',
                'estimated_timeline' => '2 - 3 Bulan (Komprehensif Enterprise)',
                'billing_type' => 'one_off',
                'status' => 'Draft',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'created_at' => Carbon::now()->subDays(2),
                'notes' => "Draf estimasi teknis dashboard telemetri PLTS Industri:\n1. Autentikasi token API aman untuk ribuan payload telemetri inverter harian.\n2. Dasbor grafik pemantauan output daya kWh real-time.\n3. High-Performance VPS Server dengan Redis TimeSeries cache dan Cloudflare CDN.",
                'items' => [
                    ['module' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)', 'complexity' => 1.50],
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 1.75],
                    ['module' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download', 'complexity' => 1.25],
                    ['module' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)', 'complexity' => 1.00],
                    ['module' => 'Konfigurasi Custom Domain & Keamanan Cloudflare CDN / SSL', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 8: SUBSCRIPTION (MODULAR) MULTI-CAMPUS CLOUD LMS
            // =================================================================
            [
                'client_name' => 'PT Solusi Edukasi Digital',
                'user_id' => $admin->id,
                'deal_key' => 'deal_edudigital_lms',
                'project_category' => 'Sistem Informasi Internal / ERP & CRM',
                'estimated_timeline' => '1 - 2 Bulan (Skala Sedang)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'modular',
                'billing_cycle' => 'monthly',
                'apply_annual_discount' => false,
                'discount_percentage' => 0.00,
                'subscription_duration' => 12,
                'user_count' => 1,
                'price_per_user' => 0.00,
                'setup_fee' => 3500000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'created_at' => Carbon::now()->subDays(28),
                'notes' => "Paket berlangganan bulanan modular (SaaS LMS Kampus):\n- Biaya Setup & Onboarding Master Data: Rp 3.500.000 (One-Off).\n- Modul Langganan: Autentikasi Role, Dasbor CRUD Soal & Nilai, Upload Berkas PDF/Video Materi, Export Nilai Excel, Notifikasi WA Pengumuman Ujian, dan Dedicated High-Performance VPS Cloud Server.",
                'items' => [
                    ['module' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)', 'complexity' => 1.25],
                    ['module' => 'Manajemen Role & Hak Akses Sederhana (Admin vs User Biasa)', 'complexity' => 1.25],
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 1.50],
                    ['module' => 'Upload Gambar & Dokumen (Multi-file + Otomatis Kompres/Resize)', 'complexity' => 1.25],
                    ['module' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download', 'complexity' => 1.25],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.25],
                    ['module' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 9: SUBSCRIPTION (MODULAR - YEARLY DISCOUNT 20%) CLOUD POS KITCHEN
            // =================================================================
            [
                'client_name' => 'PT Kenangan Retail Nusantara',
                'user_id' => $sales1->id,
                'deal_key' => 'deal_kenangan_pos',
                'project_category' => 'E-Commerce & Toko Online',
                'estimated_timeline' => '1 - 2 Bulan (Skala Sedang)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'modular',
                'billing_cycle' => 'yearly',
                'apply_annual_discount' => true,
                'discount_percentage' => 20.00,
                'subscription_duration' => 1,
                'user_count' => 1,
                'price_per_user' => 0.00,
                'setup_fee' => 2500000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'created_at' => Carbon::now()->subDays(18),
                'notes' => "Paket berlangganan tahunan Cloud Kitchen POS Display:\n- Setup & Konfigurasi Printer Thermal & Kitchen Barcode: Rp 2.500.000.\n- Siklus Penagihan: Tahunan (Mendapatkan Diskon Spesial 20% Hemat Biaya Operasional).\n- Termasuk Cloud VPS Nginx dan integrasi payment gateway Midtrans QRIS.",
                'items' => [
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 1.25],
                    ['module' => 'Integrasi Payment Gateway Otomatis (QRIS, Midtrans, Xendit, atau Tripay)', 'complexity' => 1.25],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.25],
                    ['module' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download', 'complexity' => 1.00],
                    ['module' => 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx + Free SSL HTTPS)', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 10: SUBSCRIPTION (PER-USER) FIELD OFFICER MICROFINANCE APP
            // =================================================================
            [
                'client_name' => 'PT Finansial Sejahtera Pintar',
                'user_id' => $sales2->id,
                'deal_key' => 'deal_finsejahtera_app',
                'project_category' => 'Mobile Application (Android / iOS)',
                'estimated_timeline' => '2 - 3 Bulan (Komprehensif Enterprise)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'per_user',
                'billing_cycle' => 'monthly',
                'apply_annual_discount' => false,
                'discount_percentage' => 0.00,
                'subscription_duration' => 12,
                'user_count' => 75,
                'price_per_user' => 45000.00,
                'setup_fee' => 15000000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'created_at' => Carbon::now()->subDays(8),
                'notes' => "Paket berlangganan berbasis kapasitas lisensi surveyor lapangan (Per-User Seat SaaS):\n- Setup Fee Awal (Kustomisasi Modul Skor Kredit & Deployment Dedicated Cloud VPS): Rp 15.000.000.\n- Kapasitas: 75 Akun Surveyor Aktif @ Rp 45.000 / user / bulan.\n- Termasuk Cloud VPS Server berkecepatan tinggi dengan SLA uptime 99.9%.",
                'items' => [
                    ['module' => 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx + Free SSL HTTPS)', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 11: SUBSCRIPTION (PER-USER - YEARLY DISCOUNT 15%) TMS FLEET DRIVER HUB
            // =================================================================
            [
                'client_name' => 'PT Logistik Prima Express',
                'user_id' => $sales1->id,
                'deal_key' => 'deal_prima_tms',
                'project_category' => 'Sistem Informasi Internal / ERP & CRM',
                'estimated_timeline' => '1 - 2 Bulan (Skala Sedang)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'per_user',
                'billing_cycle' => 'yearly',
                'apply_annual_discount' => true,
                'discount_percentage' => 15.00,
                'subscription_duration' => 1,
                'user_count' => 120,
                'price_per_user' => 35000.00,
                'setup_fee' => 10000000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'created_at' => Carbon::now()->subDays(6),
                'notes' => "Paket berlangganan tahunan Driver Mobile Hub & Live Proof of Delivery (POD):\n- Biaya Setup & Integrasi GPS Gateway: Rp 10.000.000.\n- Kapasitas: 120 Driver Armada Logistik @ Rp 35.000 / driver / bulan.\n- Penagihan: Tahunan (Hemat Diskon 15%) + Dedicated High-Performance VPS Cloud.",
                'items' => [
                    ['module' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)', 'complexity' => 1.00],
                ],
            ],

            // =================================================================
            // PROYEK 12: SUBSCRIPTION (HYBRID - YEARLY DISCOUNT 20%) TELEMEDICINE SAAS
            // =================================================================
            [
                'client_name' => 'PT Medika Digital Nusantara',
                'user_id' => $admin->id,
                'deal_key' => 'deal_medika_telemed',
                'project_category' => 'Sistem Informasi Internal / ERP & CRM',
                'estimated_timeline' => '2 - 3 Bulan (Komprehensif Enterprise)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'yearly',
                'apply_annual_discount' => true,
                'discount_percentage' => 20.00,
                'subscription_duration' => 1,
                'user_count' => 50,
                'price_per_user' => 50000.00,
                'setup_fee' => 20000000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'created_at' => Carbon::now()->subDays(4),
                'notes' => "Paket berlangganan Hybrid SaaS Telemedicine (Software Modules + 50 Dokter Spesialis):\n- Setup Fee Arsitektur Enkripsi Rekam Medis & Onboarding: Rp 20.000.000.\n- Biaya Modul Bulanan: Dasbor Konsultasi, Upload Resep Medis, Notifikasi WA Pasien, & High-Performance VPS.\n- Lisensi Dokter Aktif: 50 Dokter @ Rp 50.000 / dokter / bulan.\n- Penagihan: Tahunan (Diskon 20% Annual Plan).",
                'items' => [
                    ['module' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)', 'complexity' => 1.50],
                    ['module' => 'Upload Gambar & Dokumen (Multi-file + Otomatis Kompres/Resize)', 'complexity' => 1.25],
                    ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.25],
                    ['module' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)', 'complexity' => 1.00],
                ],
            ],
        ];

        $createdProjects = [];
        $projectIndex = 1;

        foreach ($projectsDefinitions as $pDef) {
            $cName = $pDef['client_name'];
            $dealKey = $pDef['deal_key'] ?? null;
            $items = $pDef['items'] ?? [];
            $createdAt = $pDef['created_at'] ?? now();

            unset($pDef['deal_key'], $pDef['items'], $pDef['created_at']);

            $client = $clients[$cName] ?? null;
            $deal = $dealKey && isset($deals[$dealKey]) ? $deals[$dealKey] : null;

            $pDef['client_id'] = $client?->id;
            $pDef['deal_id'] = $deal?->id;
            $pDef['quotation_type'] = 'standard';
            $pDef['addendum_number'] = 0;
            $pDef['grand_total'] = 0.00;

            $project = Project::create($pDef);
            $project->created_at = $createdAt;
            $project->updated_at = $createdAt;
            $project->saveQuietly();

            // Create Items
            foreach ($items as $itemData) {
                $modName = $itemData['module'];
                $complexity = (float) ($itemData['complexity'] ?? 1.00);
                $mod = $modulesMap[$modName] ?? null;

                if ($project->isSubscription()) {
                    $basePrice = $mod ? (float) $mod->subscription_price : 50000.00;
                } else {
                    $basePrice = $mod ? (float) $mod->base_price : 500000.00;
                }

                $calculatedPrice = round($basePrice * $complexity, 2);

                ProjectItem::create([
                    'project_id' => $project->id,
                    'module_id' => $mod?->id,
                    'item_name' => $modName,
                    'base_price' => $basePrice,
                    'complexity_weight' => $complexity,
                    'calculated_price' => $calculatedPrice,
                ]);
            }

            // Recalculate precision grand total
            $project->recalculateGrandTotal();

            // Sync expected value to Deal if applicable
            if ($deal && $project->status === 'Generated') {
                $deal->expected_value = $project->grand_total;
                $deal->saveQuietly();
            }

            $createdProjects[$projectIndex] = $project;
            $projectIndex++;
        }

        // ---------------------------------------------------------------------
        // 8. Realistic Addendums (Parent-Child Quotation Expansion)
        // ---------------------------------------------------------------------
        // Addendum 1 for Project 1 (PT Kenangan Retail Nusantara)
        $parentProject1 = $createdProjects[1] ?? null;
        if ($parentProject1) {
            $addendum1 = Project::create([
                'parent_id' => $parentProject1->id,
                'quotation_type' => 'addendum',
                'addendum_number' => 1,
                'user_id' => $parentProject1->user_id,
                'client_id' => $parentProject1->client_id,
                'deal_id' => $parentProject1->deal_id,
                'client_name' => $parentProject1->client_name,
                'project_category' => 'E-Commerce & Toko Online',
                'estimated_timeline' => '2 - 3 Minggu (Addendum Scope)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'grand_total' => 0.00,
                'notes' => 'Adendum penambahan fitur Customer Loyalty Rewards, Kupon Diskon Dinamis, dan integrasi auto-split mutasi bank BCA/Mandiri.',
                'addendum_notes' => "Amandemen Kontrak Penawaran #QUO-00001:\n1. Penambahan modul Loyalty Program & Saldo Cashback Pelanggan.\n2. Integrasi Multi-channel Payment QRIS Dinamis ShopeePay & GoPay Callback Webhook.\n3. Tambahan estimasi pengerjaan: 2 minggu kerja.",
            ]);
            $addendum1->created_at = Carbon::now()->subDays(3);
            $addendum1->updated_at = Carbon::now()->subDays(3);
            $addendum1->saveQuietly();

            $add1Items = [
                ['module' => 'Katalog Produk / Portfolio Galeri Interaktif', 'complexity' => 1.25],
                ['module' => 'Integrasi Payment Gateway Otomatis (QRIS, Midtrans, Xendit, atau Tripay)', 'complexity' => 1.50],
            ];

            foreach ($add1Items as $itemData) {
                $mod = $modulesMap[$itemData['module']] ?? null;
                $basePrice = $mod ? (float) $mod->base_price : 500000.00;
                $complexity = (float) $itemData['complexity'];
                $calc = round($basePrice * $complexity, 2);

                ProjectItem::create([
                    'project_id' => $addendum1->id,
                    'module_id' => $mod?->id,
                    'item_name' => $itemData['module'] . ' (Loyalty & Dynamic Voucher Extension)',
                    'base_price' => $basePrice,
                    'complexity_weight' => $complexity,
                    'calculated_price' => $calc,
                ]);
            }

            $addendum1->recalculateGrandTotal();
            $createdProjects['addendum_1'] = $addendum1;
        }

        // Addendum 2 for Project 2 (PT Logistik Prima Express)
        $parentProject2 = $createdProjects[2] ?? null;
        if ($parentProject2) {
            $addendum2 = Project::create([
                'parent_id' => $parentProject2->id,
                'quotation_type' => 'addendum',
                'addendum_number' => 1,
                'user_id' => $parentProject2->user_id,
                'client_id' => $parentProject2->client_id,
                'deal_id' => $parentProject2->deal_id,
                'client_name' => $parentProject2->client_name,
                'project_category' => 'Sistem Informasi Internal / ERP & CRM',
                'estimated_timeline' => '3 - 4 Minggu (Addendum Scope)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'grand_total' => 0.00,
                'notes' => 'Adendum penambahan modul Live Geofencing Alert & Webhook integrasi kurir ekspedisi pihak ketiga (J&T Cargo / SiCepat).',
                'addendum_notes' => "Amandemen Kontrak Penawaran #QUO-00002:\n1. Fitur notifikasi otomatis via WhatsApp saat armada truk memasuki/meninggalkan zona geofence gudang.\n2. Integrasi webhook multi-ekspedisi pihak ketiga untuk agregasi status resi kargo.\n3. Tambahan durasi pengembangan: 3 minggu kerja.",
            ]);
            $addendum2->created_at = Carbon::now()->subDays(1);
            $addendum2->updated_at = Carbon::now()->subDays(1);
            $addendum2->saveQuietly();

            $add2Items = [
                ['module' => 'Integrasi Peta Interaktif & Petunjuk Arah (Google Maps / Leaflet Embed)', 'complexity' => 1.50],
                ['module' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)', 'complexity' => 1.50],
            ];

            foreach ($add2Items as $itemData) {
                $mod = $modulesMap[$itemData['module']] ?? null;
                $basePrice = $mod ? (float) $mod->base_price : 500000.00;
                $complexity = (float) $itemData['complexity'];
                $calc = round($basePrice * $complexity, 2);

                ProjectItem::create([
                    'project_id' => $addendum2->id,
                    'module_id' => $mod?->id,
                    'item_name' => $itemData['module'] . ' (Live Geofencing & Partner Courier Webhook)',
                    'base_price' => $basePrice,
                    'complexity_weight' => $complexity,
                    'calculated_price' => $calc,
                ]);
            }

            $addendum2->recalculateGrandTotal();
            $createdProjects['addendum_2'] = $addendum2;
        }

        // ---------------------------------------------------------------------
        // 9. Chronological Deal Activities (35+ Detailed CRM Timeline Logs)
        // ---------------------------------------------------------------------
        $activitiesData = [
            // Kenangan Retail Nusantara Activities
            [
                'client' => 'PT Kenangan Retail Nusantara',
                'deal' => 'deal_kenangan_pos',
                'user' => $sales1,
                'type' => 'meeting',
                'title' => 'Discovery Meeting & Demo POS Omnichannel',
                'description' => 'Meeting tatap muka di kantor pusat Senopati bersama Pak Budi Santoso (CTO) dan Ibu Rina Wijaya. Membahas integrasi struk WhatsApp dan kecepatan respon payment QRIS saat jam sibuk.',
                'time' => Carbon::now()->subDays(22)->setTime(10, 30),
            ],
            [
                'client' => 'PT Kenangan Retail Nusantara',
                'deal' => 'deal_kenangan_pos',
                'user' => $sales1,
                'type' => 'project_created',
                'title' => 'Dokumen Penawaran QUO-00001 Diterbitkan',
                'description' => 'Estimasi biaya pengembangan aplikasi POS Omnichannel senilai ' . ($createdProjects[1] ? 'Rp ' . number_format($createdProjects[1]->grand_total, 0, ',', '.') : 'Rp 8.850.000') . ' berhasil di-generate dan dikirim ke klien.',
                'time' => Carbon::now()->subDays(20)->setTime(14, 15),
            ],
            [
                'client' => 'PT Kenangan Retail Nusantara',
                'deal' => 'deal_kenangan_pos',
                'user' => $sales1,
                'type' => 'whatsapp',
                'title' => 'Follow up Finalisasi Kontrak via WhatsApp',
                'description' => 'Pak Budi mengabarkan bahwa dewan direksi menyetujui anggaran dan Purchase Order (PO) resmi telah diterbitkan oleh bagian procurement.',
                'time' => Carbon::now()->subDays(15)->setTime(16, 45),
            ],
            [
                'client' => 'PT Kenangan Retail Nusantara',
                'deal' => 'deal_kenangan_pos',
                'user' => $sales1,
                'type' => 'stage_change',
                'title' => 'Stage Kanban Berpindah: Closed Won',
                'description' => 'Peluang "Omnichannel F&B POS & Web Store Multi-Cabang" resmi dimenangkan (Closed Won 100%). DP 50% telah terkonfirmasi lunas.',
                'time' => Carbon::now()->subDays(14)->setTime(11, 0),
            ],
            [
                'client' => 'PT Kenangan Retail Nusantara',
                'deal' => 'deal_kenangan_pos',
                'user' => $sales1,
                'type' => 'addendum_created',
                'title' => 'Dokumen Adendum Diterbitkan (#QUO-00001-ADD-01)',
                'description' => 'Adendum penambahan fitur Customer Loyalty Points dan Kupon Diskon Dinamis resmi disetujui klien.',
                'time' => Carbon::now()->subDays(3)->setTime(15, 20),
            ],

            // Logistik Prima Express Activities
            [
                'client' => 'PT Logistik Prima Express',
                'deal' => 'deal_prima_tms',
                'user' => $sales1,
                'type' => 'meeting',
                'title' => 'Technical Scoping Onsite di Cikarang',
                'description' => 'Kunjungan onsite ke pool armada Cikarang bersama Pak Hendra Pratama dan tim dispatcher. Menguji integrasi GPS vendor armada dan kebutuhan import manifest Excel ribuan baris.',
                'time' => Carbon::now()->subDays(16)->setTime(13, 0),
            ],
            [
                'client' => 'PT Logistik Prima Express',
                'deal' => 'deal_prima_tms',
                'user' => $sales1,
                'type' => 'project_created',
                'title' => 'Dokumen Penawaran QUO-00002 Diterbitkan',
                'description' => 'Kalkulasi penawaran TMS senilai ' . ($createdProjects[2] ? 'Rp ' . number_format($createdProjects[2]->grand_total, 0, ',', '.') : 'Rp 12.300.000') . ' selesai disusun dan dilampirkan via email resmi.',
                'time' => Carbon::now()->subDays(12)->setTime(11, 30),
            ],
            [
                'client' => 'PT Logistik Prima Express',
                'deal' => 'deal_prima_tms',
                'user' => $sales1,
                'type' => 'call',
                'title' => 'Telepon Negosiasi Paket High-Performance VPS',
                'description' => 'Mendiskusikan garansi SLA 99.9% dan spesifikasi server High-Performance VPS (Docker + Redis + Daily Backup S3). Klien menyetujui penawaran tanpa pengurangan scope.',
                'time' => Carbon::now()->subDays(8)->setTime(10, 0),
            ],
            [
                'client' => 'PT Logistik Prima Express',
                'deal' => 'deal_prima_tms',
                'user' => $sales1,
                'type' => 'stage_change',
                'title' => 'Stage Kanban Berpindah: Closed Won',
                'description' => 'Deal TMS Logistik Prima Express resmi Closed Won. Tim teknis memulai fase instalasi server dan skema database.',
                'time' => Carbon::now()->subDays(7)->setTime(14, 0),
            ],
            [
                'client' => 'PT Logistik Prima Express',
                'deal' => 'deal_prima_tms',
                'user' => $sales1,
                'type' => 'addendum_created',
                'title' => 'Dokumen Adendum Diterbitkan (#QUO-00002-ADD-01)',
                'description' => 'Klien meminta penambahan fitur Live Geofencing Alert otomatis ke WhatsApp driver dan dispatcher.',
                'time' => Carbon::now()->subDays(1)->setTime(16, 10),
            ],

            // Medika Digital Nusantara Activities
            [
                'client' => 'PT Medika Digital Nusantara',
                'deal' => 'deal_medika_his',
                'user' => $admin,
                'type' => 'meeting',
                'title' => 'Presentasi Arsitektur Kepatuhan SatuSehat & UU PDP',
                'description' => 'Presentasi online via Google Meet dengan dr. Anita Rahmawati dan Kevin Alamsyah. Memaparkan enkripsi AES data rekam medis dan integrasi webhook Kemenkes.',
                'time' => Carbon::now()->subDays(26)->setTime(9, 0),
            ],
            [
                'client' => 'PT Medika Digital Nusantara',
                'deal' => 'deal_medika_his',
                'user' => $admin,
                'type' => 'project_created',
                'title' => 'Dokumen Penawaran QUO-00003 Diterbitkan',
                'description' => 'Penawaran paket lengkap HIS & SIMRS klinik senilai ' . ($createdProjects[3] ? 'Rp ' . number_format($createdProjects[3]->grand_total, 0, ',', '.') : 'Rp 15.650.000') . ' resmi diterbitkan dengan SLA 12 bulan.',
                'time' => Carbon::now()->subDays(25)->setTime(15, 0),
            ],
            [
                'client' => 'PT Medika Digital Nusantara',
                'deal' => 'deal_medika_his',
                'user' => $admin,
                'type' => 'stage_change',
                'title' => 'Stage Kanban Berpindah: Closed Won',
                'description' => 'Kontrak SIMRS Medika Digital Nusantara resmi ditandatangani. Tahap UAT rekam medis elektronik dijadwalkan.',
                'time' => Carbon::now()->subDays(21)->setTime(16, 30),
            ],
            [
                'client' => 'PT Medika Digital Nusantara',
                'deal' => 'deal_medika_telemed',
                'user' => $admin,
                'type' => 'project_created',
                'title' => 'Draf Proposal Telemedicine SaaS (#QUO-00012)',
                'description' => 'Mengajukan skema Hybrid Subscription (modul konsultasi video + 50 lisensi dokter aktif) dengan diskon tahunan 20%.',
                'time' => Carbon::now()->subDays(4)->setTime(11, 0),
            ],

            // Finansial Sejahtera Pintar Activities
            [
                'client' => 'PT Finansial Sejahtera Pintar',
                'deal' => 'deal_finsejahtera_app',
                'user' => $sales2,
                'type' => 'meeting',
                'title' => 'Rapat Koordinasi IT Security SCBD',
                'description' => 'Meeting di SCBD Lot 9 bersama Pak Dimas Aditya dan Ibu Jessica Tan. Membahas parameter simulasi per-user untuk 75 akun surveyor lapangan.',
                'time' => Carbon::now()->subDays(10)->setTime(14, 0),
            ],
            [
                'client' => 'PT Finansial Sejahtera Pintar',
                'deal' => 'deal_finsejahtera_app',
                'user' => $sales2,
                'type' => 'project_created',
                'title' => 'Dokumen Penawaran QUO-00010 Diterbitkan',
                'description' => 'Kalkulasi penawaran subscription per-user senilai ' . ($createdProjects[10] ? 'Rp ' . number_format($createdProjects[10]->grand_total, 0, ',', '.') : 'Rp 55.500.000') . ' selesai disusun di DevCalc.',
                'time' => Carbon::now()->subDays(8)->setTime(16, 0),
            ],
            [
                'client' => 'PT Finansial Sejahtera Pintar',
                'deal' => 'deal_finsejahtera_app',
                'user' => $sales2,
                'type' => 'email',
                'title' => 'Pengiriman Draf NDA & Lampiran Teknis DevCalc',
                'description' => 'Mengirimkan dokumen Non-Disclosure Agreement (NDA) yang telah ditandatangani serta rincian breakdown kalkulasi biaya bulanan per-user.',
                'time' => Carbon::now()->subDays(5)->setTime(9, 30),
            ],
            [
                'client' => 'PT Finansial Sejahtera Pintar',
                'deal' => 'deal_finsejahtera_app',
                'user' => $sales2,
                'type' => 'whatsapp',
                'title' => 'Negosiasi Termin Pembayaran & SLA Uptime',
                'description' => 'Pak Dimas mengonfirmasi bahwa proposal masuk tahap negosiasi akhir dengan target penandatanganan pekan ini.',
                'time' => Carbon::now()->subDays(2)->setTime(15, 45),
            ],

            // Agro Mandiri Makmur Activities
            [
                'client' => 'CV Agro Mandiri Makmur',
                'deal' => 'deal_agro_inventory',
                'user' => $estimator,
                'type' => 'call',
                'title' => 'Telepon Diskusi Kebutuhan Barcode Scanner Gudang',
                'description' => 'Membahas spesifikasi printer thermal Bluetooth dan scanner barcode wireless yang akan digunakan tim gudang Pak Suryo di Klaten.',
                'time' => Carbon::now()->subDays(7)->setTime(11, 0),
            ],
            [
                'client' => 'CV Agro Mandiri Makmur',
                'deal' => 'deal_agro_inventory',
                'user' => $estimator,
                'type' => 'project_created',
                'title' => 'Dokumen Penawaran QUO-00004 Diterbitkan',
                'description' => 'Estimasi sistem inventory multi-gudang senilai ' . ($createdProjects[4] ? 'Rp ' . number_format($createdProjects[4]->grand_total, 0, ',', '.') : 'Rp 6.200.000') . ' resmi di-generate.',
                'time' => Carbon::now()->subDays(5)->setTime(14, 30),
            ],
            [
                'client' => 'CV Agro Mandiri Makmur',
                'deal' => 'deal_agro_inventory',
                'user' => $estimator,
                'type' => 'whatsapp',
                'title' => 'Konfirmasi Jadwal Training Operator Gudang',
                'description' => 'Pak Suryo meminta jadwal pelatihan staf gudang dilakukan secara hybrid (video tutorial + sesi live Q&A).',
                'time' => Carbon::now()->subDays(1)->setTime(10, 15),
            ],

            // Solusi Edukasi Digital Activities
            [
                'client' => 'PT Solusi Edukasi Digital',
                'deal' => 'deal_edudigital_lms',
                'user' => $admin,
                'type' => 'meeting',
                'title' => 'Review Performa Server Ujian Serentak',
                'description' => 'Uji beban simulasi 5.000 request/menit bersama Prof. Irfan Hakim. Konfigurasi Redis cache dan auto-scaling berjalan sukses.',
                'time' => Carbon::now()->subDays(29)->setTime(14, 0),
            ],
            [
                'client' => 'PT Solusi Edukasi Digital',
                'deal' => 'deal_edudigital_lms',
                'user' => $admin,
                'type' => 'stage_change',
                'title' => 'Stage Kanban Berpindah: Closed Won',
                'description' => 'Paket Modular SaaS LMS aktif 12 bulan untuk 25 kampus mitra.',
                'time' => Carbon::now()->subDays(30)->setTime(17, 0),
            ],

            // Bahari Nusantara Tour Activities
            [
                'client' => 'PT Bahari Nusantara Tour',
                'deal' => 'deal_bahari_tour',
                'user' => $sales1,
                'type' => 'meeting',
                'title' => 'Demo Booking Engine & Integrasi Payment Gateway',
                'description' => 'Demo sistem reservasi online langsung kepada Pak Wayan Sudarta di Seminyak Bali via Zoom. Integrasi QRIS instan dan WhatsApp notifikasi disetujui.',
                'time' => Carbon::now()->subDays(16)->setTime(11, 0),
            ],
            [
                'client' => 'PT Bahari Nusantara Tour',
                'deal' => 'deal_bahari_tour',
                'user' => $sales1,
                'type' => 'stage_change',
                'title' => 'Stage Kanban Berpindah: Closed Won',
                'description' => 'Peluang Web Portal Booking Tour Bahari resmi Closed Won. Deployment server live selesai.',
                'time' => Carbon::now()->subDays(10)->setTime(13, 30),
            ],

            // Properti Graha Lestari Activities
            [
                'client' => 'PT Properti Graha Lestari',
                'deal' => 'deal_graha_property',
                'user' => $sales2,
                'type' => 'meeting',
                'title' => 'Eksplorasi Konsep Web 3D Virtual Tour BSD',
                'description' => 'Meeting presentasi ide dengan Ibu Maya Anggraini di BSD Green Office Park. Membahas viewer 360 derajat foto rumah contoh.',
                'time' => Carbon::now()->subDays(4)->setTime(14, 0),
            ],
            [
                'client' => 'PT Properti Graha Lestari',
                'deal' => 'deal_graha_property',
                'user' => $sales2,
                'type' => 'project_created',
                'title' => 'Draf Penawaran QUO-00006 Dibuat',
                'description' => 'Draf penawaran web properti senilai ' . ($createdProjects[6] ? 'Rp ' . number_format($createdProjects[6]->grand_total, 0, ',', '.') : 'Rp 3.900.000') . ' selesai disusun dan diajukan ke klien.',
                'time' => Carbon::now()->subDays(3)->setTime(16, 20),
            ],

            // Surya Energi Terbarukan Activities
            [
                'client' => 'PT Surya Energi Terbarukan',
                'deal' => 'deal_surya_iot',
                'user' => $estimator,
                'type' => 'call',
                'title' => 'Panggilan Teknis Protokol IoT MQTT & Inverter Data',
                'description' => 'Diskusi teknis dengan Ir. Bambang Sugiarto mengenai frekuensi pengiriman data sensor tegangan dan arus listrik inverter solar.',
                'time' => Carbon::now()->subDays(2)->setTime(13, 30),
            ],
            [
                'client' => 'PT Surya Energi Terbarukan',
                'deal' => 'deal_surya_iot',
                'user' => $estimator,
                'type' => 'project_created',
                'title' => 'Draf Penawaran QUO-00007 Dibuat',
                'description' => 'Draf awal kalkulasi dashboard telemetri PLTS senilai ' . ($createdProjects[7] ? 'Rp ' . number_format($createdProjects[7]->grand_total, 0, ',', '.') : 'Rp 8.450.000') . ' disimpan dalam mode Draf.',
                'time' => Carbon::now()->subDays(2)->setTime(15, 0),
            ],

            // Distribusi Farmasi Prima Activities (Closed Lost)
            [
                'client' => 'PT Distribusi Farmasi Prima',
                'deal' => 'deal_farmasi_coldchain',
                'user' => $sales1,
                'type' => 'stage_change',
                'title' => 'Stage Kanban Berpindah: Closed Lost',
                'description' => 'Deal ditutup Closed Lost karena klien menunda anggaran digitalisasi hingga selesai audit BPOM internal pada Q4.',
                'time' => Carbon::now()->subDays(45)->setTime(16, 0),
            ],
        ];

        foreach ($activitiesData as $act) {
            $client = $clients[$act['client']] ?? null;
            $deal = isset($act['deal']) && isset($deals[$act['deal']]) ? $deals[$act['deal']] : null;
            $user = $act['user'] ?? $admin;

            $activity = DealActivity::create([
                'client_id' => $client?->id,
                'deal_id' => $deal?->id,
                'user_id' => $user->id,
                'type' => $act['type'],
                'title' => $act['title'],
                'description' => $act['description'],
                'performed_at' => $act['time'],
            ]);
            $activity->created_at = $act['time'];
            $activity->updated_at = $act['time'];
            $activity->saveQuietly();
        }
    }
}
