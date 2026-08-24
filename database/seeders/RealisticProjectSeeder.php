<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RealisticProjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Roles & Users exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@devcalc.test'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $admin->assignRole($adminRole);

        $sales = User::firstOrCreate(
            ['email' => 'sales@devcalc.test'],
            ['name' => 'Sales Estimator', 'password' => Hash::make('password')]
        );
        $sales->assignRole($salesRole);

        // 2. Clear Existing Projects & Items for a clean slate
        DB::table('project_items')->delete();
        DB::table('projects')->delete();

        // 3. 5 Realistic Projects with Current Standard Modules
        $clients = [
            // --- PROYEK 1: UMKM F&B (Company Profile & Menu Online) ---
            [
                'client_name' => 'Kedai Kopi Kenangan Senja',
                'user_id' => $sales->id,
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 3,
                'setup_fee' => 0.00,
                'notes' => 'Pengembangan website profil kedai kopi, katalog menu digital interaktif, dan pemesanan langsung via WhatsApp.',
                'items' => [
                    [
                        'module_name' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Katalog Produk / Portfolio Galeri Interaktif',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Formulir Kontak / Pemesanan Dinamis + Notifikasi Email Otomatis',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)',
                        'complexity_weight' => 1.25,
                    ],
                    [
                        'module_name' => 'Shared Hosting Setup & Domain (.com / .id) - Paket 1 Tahun',
                        'complexity_weight' => 1.00,
                    ],
                ],
            ],

            // --- PROYEK 2: KLINIK KESEHATAN (Portal Booking & Rekam Medis Ringan) ---
            [
                'client_name' => 'Klinik Pratama Sehat Medika',
                'user_id' => $sales->id,
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'notes' => 'Aplikasi web pendaftaran pasien online, jadwal dokter, rekam medis ringkas, dan export laporan bulanan.',
                'items' => [
                    [
                        'module_name' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)',
                        'complexity_weight' => 1.25,
                    ],
                    [
                        'module_name' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)',
                        'complexity_weight' => 1.25,
                    ],
                    [
                        'module_name' => 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx + Free SSL HTTPS)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Paket Maintenance Sistem, Bugfix & Backup Server Rutin (Bulanan)',
                        'complexity_weight' => 1.00,
                    ],
                ],
            ],

            // --- PROYEK 3: TOKO ONLINE BUSANA (E-Commerce Sederhana + QRIS) ---
            [
                'client_name' => 'Toko Busana Muslimah Syari',
                'user_id' => $sales->id,
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 3,
                'setup_fee' => 0.00,
                'notes' => 'Website toko online katalog hijab dan gamis dengan fitur hitung ongkir otomatis dan pembayaran QRIS/Transfer Bank otomatis.',
                'items' => [
                    [
                        'module_name' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Katalog Produk / Portfolio Galeri Interaktif',
                        'complexity_weight' => 1.25,
                    ],
                    [
                        'module_name' => 'Upload Gambar & Dokumen (Multi-file + Otomatis Kompres/Resize)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Integrasi Payment Gateway Otomatis (QRIS, Midtrans, Xendit, atau Tripay)',
                        'complexity_weight' => 1.50,
                    ],
                    [
                        'module_name' => 'Kalkulator Ongkos Kirim Otomatis (Integrasi RajaOngkir API)',
                        'complexity_weight' => 1.25,
                    ],
                    [
                        'module_name' => 'Integrasi WhatsApp Gateway (Kirim Pesan / Notifikasi Order via WA)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx + Free SSL HTTPS)',
                        'complexity_weight' => 1.00,
                    ],
                ],
            ],

            // --- PROYEK 4: LEMBAGA KURSUS BAHASA (Portal Siswa & SEO Blog) ---
            [
                'client_name' => 'Lembaga Kursus Bahasa Global',
                'user_id' => $admin->id,
                'billing_type' => 'one_off',
                'status' => 'Draft',
                'maintenance_months' => 6,
                'setup_fee' => 0.00,
                'notes' => 'Portal kursus bahasa inggris dan jepang online, artikel edukasi SEO, login 1-klik akun Google, dan formulir pendaftaran kelas.',
                'items' => [
                    [
                        'module_name' => 'Landing Page Responsif & Company Profile (Mobile-Friendly)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Blog / Artikel Berita & Optimasi SEO (Meta Tags & Sitemap)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Login Cepat 1-Klik Akun Google (Google OAuth Login)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Formulir Kontak / Pemesanan Dinamis + Notifikasi Email Otomatis',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Shared Hosting Setup & Domain (.com / .id) - Paket 1 Tahun',
                        'complexity_weight' => 1.00,
                    ],
                ],
            ],

            // --- PROYEK 5: JASA LOGISTIK & DISTRIBUSI (Internal Tracking & Fleet) ---
            [
                'client_name' => 'PT Logistik Nusantara Mandiri',
                'user_id' => $sales->id,
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 0.00,
                'notes' => 'Aplikasi web internal pemantauan armada logistik, rute pengiriman Google Maps, import batch manifes excel, dan hak akses bertingkat admin/driver.',
                'items' => [
                    [
                        'module_name' => 'Sistem Autentikasi (Login, Register, Lupa Password & Verifikasi Email)',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Manajemen Role & Hak Akses Sederhana (Admin vs User Biasa)',
                        'complexity_weight' => 1.25,
                    ],
                    [
                        'module_name' => 'Dasbor Admin & Manajemen Data CRUD (Create, Read, Update, Delete)',
                        'complexity_weight' => 1.50,
                    ],
                    [
                        'module_name' => 'Integrasi Peta Interaktif & Petunjuk Arah (Google Maps / Leaflet Embed)',
                        'complexity_weight' => 1.25,
                    ],
                    [
                        'module_name' => 'Import Data Massal dari Template Excel / CSV',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'Export Data ke Excel (.xlsx) & Laporan Cetak PDF Siap Download',
                        'complexity_weight' => 1.00,
                    ],
                    [
                        'module_name' => 'High-Performance VPS Server (Docker + Redis + Auto Backup Database Rutin)',
                        'complexity_weight' => 1.00,
                    ],
                ],
            ],
        ];

        // 4. Insert 5 Projects & Calculate Line Items
        foreach ($clients as $index => $data) {
            $project = Project::create([
                'user_id' => $data['user_id'],
                'client_name' => $data['client_name'],
                'billing_type' => $data['billing_type'],
                'status' => $data['status'],
                'maintenance_months' => $data['maintenance_months'],
                'setup_fee' => $data['setup_fee'],
                'notes' => $data['notes'],
                'grand_total' => 0.00,
                'created_at' => now()->subDays((5 - $index) * 3),
                'updated_at' => now()->subDays((5 - $index) * 3),
            ]);

            $totalPrice = 0.00;

            foreach ($data['items'] as $itemData) {
                $module = Module::where('name', $itemData['module_name'])->first();

                $basePrice = $module ? (float) $module->base_price : 500000.00;
                $weight = (float) $itemData['complexity_weight'];
                $calcPrice = round($basePrice * $weight, 2);
                $totalPrice += $calcPrice;

                ProjectItem::create([
                    'project_id' => $project->id,
                    'module_id' => $module ? $module->id : null,
                    'item_name' => $itemData['module_name'],
                    'base_price' => $basePrice,
                    'complexity_weight' => $weight,
                    'calculated_price' => $calcPrice,
                ]);
            }

            $project->grand_total = $totalPrice;
            $project->saveQuietly();
        }
    }
}
