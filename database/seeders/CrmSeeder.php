<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealActivity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $sales = User::where('id', '!=', $admin->id)->first() ?? $admin;

        // 1. Data Klien B2B & Kontak PIC
        $clientsData = [
            [
                'name' => 'PT Kenangan Retail Nusantara',
                'industry' => 'Food & Beverage / Retail',
                'email' => 'corporate@kenangansenja.id',
                'phone' => '081234567890',
                'website' => 'https://kenangansenja.id',
                'address' => 'Jl. Senopati Raya No. 45, Kebayoran Baru, Jakarta Selatan',
                'status' => 'active',
                'user_id' => $sales->id,
                'notes' => 'Jaringan F&B dengan 15 cabang. Butuh web katalog, QR ordering, dan integrasi WhatsApp POS.',
                'contacts' => [
                    [
                        'name' => 'Budi Santoso',
                        'title' => 'Chief Technology Officer (CTO)',
                        'email' => 'budi.santoso@kenangansenja.id',
                        'phone' => '081298765432',
                        'is_primary' => true,
                        'notes' => 'Pengambil keputusan teknis utama, sangat responsif via WhatsApp.',
                    ],
                    [
                        'name' => 'Rina Wijaya',
                        'title' => 'Head of Procurement',
                        'email' => 'rina.w@kenangansenja.id',
                        'phone' => '081311223344',
                        'is_primary' => false,
                        'notes' => 'Menangani PO, Term of Payment, dan verifikasi faktur pajak.',
                    ],
                ],
            ],
            [
                'name' => 'PT Logistik Prima Express',
                'industry' => 'Logistics & Supply Chain',
                'email' => 'info@primaexpress.co.id',
                'phone' => '081122334455',
                'website' => 'https://primaexpress.co.id',
                'address' => 'Kawasan Industri MM2100 Blok C-3, Cikarang Barat, Bekasi',
                'status' => 'active',
                'user_id' => $sales->id,
                'notes' => 'Perusahaan 3PL logistik nasional. Butuh TMS (Transport Management System) & Real-time Live Tracking.',
                'contacts' => [
                    [
                        'name' => 'Hendra Pratama',
                        'title' => 'VP of Digital Transformation',
                        'email' => 'hendra.p@primaexpress.co.id',
                        'phone' => '081288990011',
                        'is_primary' => true,
                        'notes' => 'Fokus pada reliabilitas server dan integrasi API kurir pihak ketiga.',
                    ],
                ],
            ],
            [
                'name' => 'PT Medika Digital Nusantara',
                'industry' => 'Healthcare & MedTech',
                'email' => 'contact@medikanusantara.com',
                'phone' => '081566778899',
                'website' => 'https://medikanusantara.com',
                'address' => 'Cyber 2 Tower Lt. 18, Jl. HR Rasuna Said, Jakarta Selatan',
                'status' => 'prospect',
                'user_id' => $admin->id,
                'notes' => 'Platform telemedicine & sistem klinik terpadu (HIS/EMR) sesuai standar SatuSehat Kemenkes.',
                'contacts' => [
                    [
                        'name' => 'dr. Anita Rahmawati, Sp.PK',
                        'title' => 'Managing Director & Founder',
                        'email' => 'dr.anita@medikanusantara.com',
                        'phone' => '081399887766',
                        'is_primary' => true,
                        'notes' => 'Sangat mengutamakan kepatuhan privasi data medis (HIPAA / UU PDP).',
                    ],
                    [
                        'name' => 'Kevin Alamsyah',
                        'title' => 'Lead Product Architect',
                        'email' => 'kevin@medikanusantara.com',
                        'phone' => '085612345678',
                        'is_primary' => false,
                        'notes' => 'Reviewer teknis arsitektur sistem.',
                    ],
                ],
            ],
            [
                'name' => 'PT Finansial Sejahtera Pintar',
                'industry' => 'Fintech & Financial Services',
                'email' => 'business@finsejahtera.co.id',
                'phone' => '081900112233',
                'website' => 'https://finsejahtera.co.id',
                'address' => 'Sudirman Central Business District (SCBD) Lot 9, Jakarta',
                'status' => 'prospect',
                'user_id' => $sales->id,
                'notes' => 'Aplikasi micro-financing & wealth tech. Sedang membandingkan skema On-Premise (One-Off) vs SaaS Bulanan.',
                'contacts' => [
                    [
                        'name' => 'Dimas Aditya',
                        'title' => 'Head of IT & Security',
                        'email' => 'dimas.a@finsejahtera.co.id',
                        'phone' => '081233445566',
                        'is_primary' => true,
                        'notes' => 'Membutuhkan dokumentasi API lengkap dan NDA ketat.',
                    ],
                ],
            ],
            [
                'name' => 'CV Agro Mandiri Makmur',
                'industry' => 'AgriTech & Manufacturing',
                'email' => 'agromandiri@gmail.com',
                'phone' => '082133445566',
                'website' => 'https://agromandiri.co.id',
                'address' => 'Jl. Raya Solo-Jogja KM 15, Klaten, Jawa Tengah',
                'status' => 'lead',
                'user_id' => $sales->id,
                'notes' => 'Pabrik pupuk & hasil bumi. Butuh sistem inventory multi-gudang dan invoicing otomatis.',
                'contacts' => [
                    [
                        'name' => 'Suryo Wibowo',
                        'title' => 'General Manager Operasional',
                        'email' => 'suryo.w@agromandiri.co.id',
                        'phone' => '081722334455',
                        'is_primary' => true,
                        'notes' => 'Lebih suka komunikasi via telepon atau voice note WhatsApp.',
                    ],
                ],
            ],
            [
                'name' => 'PT Solusi Edukasi Digital',
                'industry' => 'Education & EdTech',
                'email' => 'partnership@edudigital.ac.id',
                'phone' => '081277889900',
                'website' => 'https://edudigital.ac.id',
                'address' => 'Gedung Graha Pena Lt. 5, Jl. Ahmad Yani, Surabaya',
                'status' => 'prospect',
                'user_id' => $admin->id,
                'notes' => 'LMS (Learning Management System) & Ujian Berbasis Komputer untuk 20+ kampus swasta.',
                'contacts' => [
                    [
                        'name' => 'Prof. Dr. Irfan Hakim',
                        'title' => 'Chief Academic & Technology Officer',
                        'email' => 'irfan.hakim@edudigital.ac.id',
                        'phone' => '081199882233',
                        'is_primary' => true,
                        'notes' => 'Suka diskusi skema per-user subscription dengan SLA 99.9%.',
                    ],
                ],
            ],
        ];

        $createdClients = [];
        foreach ($clientsData as $cData) {
            $contacts = $cData['contacts'] ?? [];
            unset($cData['contacts']);

            $client = Client::firstOrCreate(
                ['name' => $cData['name']],
                $cData
            );

            foreach ($contacts as $contactData) {
                Contact::firstOrCreate(
                    [
                        'client_id' => $client->id,
                        'email' => $contactData['email'],
                    ],
                    array_merge($contactData, ['client_id' => $client->id])
                );
            }

            $createdClients[$client->name] = $client;
        }

        // 2. Data Deals (Pipeline)
        $dealsData = [
            [
                'client_name' => 'PT Kenangan Retail Nusantara',
                'title' => 'Web App E-Commerce & WhatsApp POS Omnichannel',
                'stage' => 'won',
                'expected_value' => 45000000.00,
                'probability' => 100,
                'expected_close_date' => Carbon::now()->subDays(10)->toDateString(),
                'user_id' => $sales->id,
                'notes' => 'Deal disetujui. Quotation resmi sudah di-generate dan DP 50% sudah diterima.',
            ],
            [
                'client_name' => 'PT Logistik Prima Express',
                'title' => 'Enterprise Transport Management System (TMS)',
                'stage' => 'negotiation',
                'expected_value' => 85000000.00,
                'probability' => 80,
                'expected_close_date' => Carbon::now()->addDays(7)->toDateString(),
                'user_id' => $sales->id,
                'notes' => 'Klien meminta diskon tahunan 20% untuk paket subscription 12 bulan + addendum modul GPS tracking.',
            ],
            [
                'client_name' => 'PT Medika Digital Nusantara',
                'title' => 'Klinik Smart Health & Integrasi SatuSehat',
                'stage' => 'proposal_sent',
                'expected_value' => 60000000.00,
                'probability' => 60,
                'expected_close_date' => Carbon::now()->addDays(14)->toDateString(),
                'user_id' => $admin->id,
                'notes' => 'Draft estimasi DevCalc v2 sudah dikirim via email dan PDF. Menunggu jadwal rapat evaluasi direksi.',
            ],
            [
                'client_name' => 'PT Finansial Sejahtera Pintar',
                'title' => 'SaaS Credit Scoring & Loan Management Module',
                'stage' => 'scoping',
                'expected_value' => 120000000.00,
                'probability' => 35,
                'expected_close_date' => Carbon::now()->addDays(25)->toDateString(),
                'user_id' => $sales->id,
                'notes' => 'Sedang menyusun breakdown modul di DevCalc. Klien tertarik opsi hybrid per-user + hosting dedicated.',
            ],
            [
                'client_name' => 'CV Agro Mandiri Makmur',
                'title' => 'Sistem Gudang & Smart Inventory Barcode',
                'stage' => 'discovery',
                'expected_value' => 28000000.00,
                'probability' => 15,
                'expected_close_date' => Carbon::now()->addDays(35)->toDateString(),
                'user_id' => $sales->id,
                'notes' => 'Inbound via website. Dijadwalkan online demo awal hari Rabu.',
            ],
            [
                'client_name' => 'PT Solusi Edukasi Digital',
                'title' => 'CBT Online & AI Exam Proctoring Engine',
                'stage' => 'lost',
                'expected_value' => 75000000.00,
                'probability' => 0,
                'lost_reason' => 'Klien memilih in-house development karena kebijakan anggaran internal universitas',
                'expected_close_date' => Carbon::now()->subDays(20)->toDateString(),
                'user_id' => $admin->id,
                'notes' => 'Follow up kembali Q4 saat pembukaan tahun ajaran baru.',
            ],
        ];

        $createdDeals = [];
        foreach ($dealsData as $dData) {
            $client = $createdClients[$dData['client_name']] ?? null;
            if (!$client) continue;

            $deal = Deal::firstOrCreate(
                [
                    'client_id' => $client->id,
                    'title' => $dData['title'],
                ],
                [
                    'user_id' => $dData['user_id'],
                    'client_id' => $client->id,
                    'title' => $dData['title'],
                    'stage' => $dData['stage'],
                    'expected_value' => $dData['expected_value'],
                    'probability' => $dData['probability'],
                    'expected_close_date' => $dData['expected_close_date'],
                    'lost_reason' => $dData['lost_reason'] ?? null,
                    'notes' => $dData['notes'],
                ]
            );

            $createdDeals[$dData['title']] = $deal;

            // Buat Aktivitas Log untuk deal ini
            DealActivity::firstOrCreate(
                [
                    'deal_id' => $deal->id,
                    'title' => 'Inisiasi Requirement & Discovery Call',
                ],
                [
                    'client_id' => $client->id,
                    'user_id' => $deal->user_id,
                    'type' => 'call',
                    'title' => 'Inisiasi Requirement & Discovery Call',
                    'description' => 'Mendiskusikan latar belakang kebutuhan sistem, target peluncuran, dan integrasi API yang dibutuhkan.',
                    'performed_at' => Carbon::now()->subDays(5),
                ]
            );

            DealActivity::firstOrCreate(
                [
                    'deal_id' => $deal->id,
                    'title' => 'Pengiriman Draft Estimasi DevCalc',
                ],
                [
                    'client_id' => $client->id,
                    'user_id' => $deal->user_id,
                    'type' => 'whatsapp',
                    'title' => 'Pengiriman Draft Estimasi DevCalc',
                    'description' => 'Mengirimkan link ringkasan penawaran harga dan lampiran PDF resmi kalkulasi biaya.',
                    'performed_at' => Carbon::now()->subDays(2),
                ]
            );
        }

        // 3. Hubungkan Existing Projects dengan Clients & Deals
        $projects = Project::all();
        foreach ($projects as $proj) {
            // Cocokkan atau hubungkan ke client yang relevan
            $matchedClient = Client::where('name', 'like', "%{$proj->client_name}%")
                ->orWhere('name', 'like', '%' . explode(' ', $proj->client_name)[0] . '%')
                ->first();

            if (!$matchedClient) {
                // Buat client baru otomatis dari data project yang sudah ada
                $matchedClient = Client::firstOrCreate(
                    ['name' => $proj->client_name],
                    [
                        'user_id' => $proj->user_id,
                        'industry' => $proj->project_category ?: 'Software Development',
                        'email' => 'info@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $proj->client_name)) . '.id',
                        'phone' => '0812' . rand(10000000, 99999999),
                        'status' => $proj->status === 'Draft' ? 'prospect' : 'active',
                        'notes' => 'Klien terdaftar otomatis dari riwayat penawaran DevCalc.',
                    ]
                );

                Contact::firstOrCreate(
                    [
                        'client_id' => $matchedClient->id,
                        'name' => 'PIC ' . $proj->client_name,
                    ],
                    [
                        'title' => 'Project Lead / PIC',
                        'email' => 'pic@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $proj->client_name)) . '.id',
                        'phone' => '0812' . rand(10000000, 99999999),
                        'is_primary' => true,
                    ]
                );
            }

            // Cari atau kaitkan dengan deal
            $matchedDeal = Deal::where('client_id', $matchedClient->id)->first();
            if (!$matchedDeal) {
                $matchedDeal = Deal::create([
                    'user_id' => $proj->user_id,
                    'client_id' => $matchedClient->id,
                    'title' => 'Pengembangan ' . ($proj->project_category ?: 'Sistem Web/App') . ' - ' . $matchedClient->name,
                    'stage' => $proj->status === 'Draft' ? 'proposal_sent' : 'won',
                    'expected_value' => $proj->grand_total,
                    'probability' => $proj->status === 'Draft' ? 60 : 100,
                    'expected_close_date' => Carbon::now()->addDays(14)->toDateString(),
                    'notes' => $proj->notes ?: 'Deal dibuat otomatis dari dokumen penawaran DevCalc.',
                ]);
            }

            $proj->client_id = $matchedClient->id;
            $proj->deal_id = $matchedDeal->id;
            $proj->saveQuietly();
        }
    }
}
