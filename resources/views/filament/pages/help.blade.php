<x-filament-panels::page>
    <style>
        .devcalc-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #4f46e5 100%);
            padding: 26px 30px;
            border-radius: 14px;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.35);
            transition: all 0.3s ease;
        }
        .dark .devcalc-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #312e81 100%);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .devcalc-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }
        .devcalc-pill-red {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .dark .devcalc-pill-red {
            background-color: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .devcalc-pill-green {
            background-color: #dcfce7;
            color: #166534;
        }
        .dark .devcalc-pill-green {
            background-color: rgba(34, 197, 94, 0.15);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.25);
        }

        .devcalc-pill-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .dark .devcalc-pill-blue {
            background-color: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }

        .devcalc-formula-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-left: 5px solid #2563eb;
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        .dark .devcalc-formula-box {
            background-color: rgba(30, 58, 138, 0.25);
            border: 1px solid rgba(59, 130, 246, 0.35);
            border-left: 5px solid #3b82f6;
        }

        .devcalc-formula-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .dark .devcalc-formula-title {
            color: #94a3b8;
        }

        .devcalc-formula-text {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 15px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .dark .devcalc-formula-text {
            color: #93c5fd;
        }

        .devcalc-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }
        .dark .devcalc-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background-color: rgba(255, 255, 255, 0.03);
        }
        .devcalc-card:hover {
            border-color: #cbd5e1;
        }
        .dark .devcalc-card:hover {
            border-color: rgba(59, 130, 246, 0.4);
            background-color: rgba(255, 255, 255, 0.05);
        }

        .devcalc-card-title {
            font-weight: 700;
            font-size: 14.5px;
            color: #0f172a;
            margin: 0;
        }
        .dark .devcalc-card-title {
            color: #f8fafc;
        }

        .devcalc-card-desc {
            font-size: 12px;
            color: #64748b;
            margin: 0;
            line-height: 1.45;
        }
        .dark .devcalc-card-desc {
            color: #94a3b8;
        }

        .devcalc-step-desc {
            font-size: 12.5px;
            line-height: 1.5;
            color: #475569;
            margin: 8px 0 0 0;
        }
        .dark .devcalc-step-desc {
            color: #94a3b8;
        }

        .devcalc-faq-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }
        .dark .devcalc-faq-item {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background-color: rgba(255, 255, 255, 0.03);
        }
        .devcalc-faq-item:hover {
            border-color: #cbd5e1;
        }
        .dark .devcalc-faq-item:hover {
            border-color: rgba(59, 130, 246, 0.4);
            background-color: rgba(37, 99, 235, 0.05);
        }

        .devcalc-faq-title {
            font-weight: 700;
            font-size: 13.5px;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .dark .devcalc-faq-title {
            color: #f8fafc;
        }

        .devcalc-faq-text {
            font-size: 12.5px;
            color: #475569;
            line-height: 1.5;
        }
        .dark .devcalc-faq-text {
            color: #cbd5e1;
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- 1. Hero Banner -->
        <div class="devcalc-hero">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                <div style="max-width: 750px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <span style="background-color: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); padding: 3px 8px; border-radius: 9999px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            DevCalc Quotation Engine
                        </span>
                    </div>
                    <h2 style="font-size: 22px; font-weight: 800; line-height: 1.3; color: #ffffff; margin: 0 0 8px 0;">
                        {{ app()->getLocale() === 'id' ? 'Panduan Penggunaan & Dokumentasi Sistem' : 'System Guide & Technical Documentation' }}
                    </h2>
                    <p style="font-size: 13.5px; line-height: 1.55; color: #e0e7ff; margin: 0;">
                        {{ app()->getLocale() === 'id' 
                            ? 'Sistem penawaran software dinamis dengan bobot kompleksitas fitur (Complexity Weight) dan standarisasi mata uang Rupiah (IDR).' 
                            : 'Dynamic software quotation calculator with complexity multiplier and standardized Indonesian Rupiah (IDR) pricing.' 
                        }}
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.15); padding: 6px 12px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <x-heroicon-m-globe-alt style="width: 16px; height: 16px; color: #ffffff;" />
                    <span style="font-size: 11.5px; font-weight: 600;">
                        {{ app()->getLocale() === 'id' ? 'Bahasa: Indonesia' : 'Language: English' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Workflow Steps (3 Simple Cards with Clean Badges) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
            <!-- Step 1 -->
            <div class="devcalc-card">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <x-heroicon-o-squares-2x2 style="width: 18px; height: 18px; color: #3b82f6; flex-shrink: 0;" />
                        <h3 class="devcalc-card-title">1. {{ app()->getLocale() === 'id' ? 'Katalog Modul' : 'Feature Catalog' }}</h3>
                    </div>
                    <span class="devcalc-pill devcalc-pill-red">
                        {{ app()->getLocale() === 'id' ? 'Admin' : 'Admin' }}
                    </span>
                </div>
                <p class="devcalc-step-desc">
                    {{ app()->getLocale() === 'id'
                        ? 'Admin menyusun katalog fitur standar software beserta harga dasar (Base Price dalam Rp) dan kategori domain.'
                        : 'Administrators maintain standard feature templates, base pricing in Rp, and domain categories.'
                    }}
                </p>
            </div>

            <!-- Step 2 -->
            <div class="devcalc-card">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <x-heroicon-o-calculator style="width: 18px; height: 18px; color: #3b82f6; flex-shrink: 0;" />
                        <h3 class="devcalc-card-title">2. {{ app()->getLocale() === 'id' ? 'Kalkulasi Estimasi' : 'Quotation Estimator' }}</h3>
                    </div>
                    <span class="devcalc-pill devcalc-pill-green">
                        {{ app()->getLocale() === 'id' ? 'Admin & Sales' : 'Admin & Sales' }}
                    </span>
                </div>
                <p class="devcalc-step-desc">
                    {{ app()->getLocale() === 'id'
                        ? 'Sales membuat penawaran baru, memasukkan item modul dari katalog atau fitur kustom, dan mengatur bobot kompleksitas.'
                        : 'Sales create quotations, add catalog or custom scope items, and adjust complexity multipliers.'
                    }}
                </p>
            </div>

            <!-- Step 3 -->
            <div class="devcalc-card">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <x-heroicon-o-document-arrow-down style="width: 18px; height: 18px; color: #3b82f6; flex-shrink: 0;" />
                        <h3 class="devcalc-card-title">3. {{ app()->getLocale() === 'id' ? 'Cetak & Ekspor PDF' : 'PDF Export' }}</h3>
                    </div>
                    <span class="devcalc-pill devcalc-pill-blue">
                        {{ app()->getLocale() === 'id' ? 'Nota Resmi' : 'Official PDF' }}
                    </span>
                </div>
                <p class="devcalc-step-desc">
                    {{ app()->getLocale() === 'id'
                        ? 'Cetak dokumen penawaran resmi berstandar industri dengan format tabel profesional, rincian biaya transparan, dan kolom tanda tangan persetujuan.'
                        : 'Generate official PDF quotation with itemized breakdown, transparent cost computation, and approval signature block.'
                    }}
                </p>
            </div>
        </div>

        <!-- 3. Subscription & Billing Models Section -->
        <x-filament::section
            icon="heroicon-o-credit-card"
            :heading="app()->getLocale() === 'id' ? 'Skema Kontrak & Metode Tagihan Berlangganan (SaaS)' : 'Contract Schemes & Subscription Billing Models'"
            :description="app()->getLocale() === 'id' ? 'Panduan memilih model penetapan harga sesuai karakteristik proyek dan kesepakatan klien.' : 'Guide to selecting the optimal pricing model for one-off build vs recurring SaaS contracts.'"
        >
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-bottom: 16px;">
                <!-- One-Off -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">{{ app()->getLocale() === 'id' ? 'Putus Kontrak (One-Off)' : 'One-Off Fixed Build' }}</span>
                        <span class="devcalc-pill devcalc-pill-blue">Capex</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' 
                            ? 'Proyek pengembangan software sekali bayar (lisensi lepas). Menggunakan Harga Beli Putus modul dan dibayar bertahap sesuai termin (DP 50%, 30%, 20%).' 
                            : 'Fixed-price development handover. Uses One-Off Base Price and standard milestone payment terms.' 
                        }}
                    </p>
                </div>

                <!-- Modular Subscription -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">{{ app()->getLocale() === 'id' ? 'Langganan: Flat Modular' : 'Sub: Flat Modular' }}</span>
                        <span class="devcalc-pill devcalc-pill-green">Per-Modul</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' 
                            ? 'Biaya berlangganan dihitung dari akumulasi Harga Langganan Bulanan modul yang diaktifkan dikalikan durasi komitmen kontrak.' 
                            : 'Recurring fee computed strictly from cumulative monthly module subscription prices across commitment duration.' 
                        }}
                    </p>
                </div>

                <!-- Per-User Subscription -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">{{ app()->getLocale() === 'id' ? 'Langganan: Per-User' : 'Sub: User-Based (Per-Seat)' }}</span>
                        <span class="devcalc-pill devcalc-pill-green">Kapasitas</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' 
                            ? 'Tagihan dihitung murni berdasarkan kuota kapasitas pengguna aktif: (Jumlah User × Tarif per User / Bulan) × Durasi Kontrak.' 
                            : 'Billed purely on active user seats quota: (User Count × Price per User / Month) × Duration.' 
                        }}
                    </p>
                </div>

                <!-- Hybrid Subscription -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">{{ app()->getLocale() === 'id' ? 'Langganan: Hybrid' : 'Sub: Hybrid Enterprise' }}</span>
                        <span class="devcalc-pill devcalc-pill-red">Enterprise</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' 
                            ? 'Model enterprise yang menggabungkan biaya sewa infrastruktur fitur modul dasar + biaya variabel per kuota pengguna aktif.' 
                            : 'Combines baseline module infrastructure rental fee + variable active user seats quota fee.' 
                        }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- 4. Contract Addendum Workflow Section -->
        <x-filament::section
            icon="heroicon-o-document-duplicate"
            :heading="app()->getLocale() === 'id' ? 'Alur Kerja Adendum Kontrak & Penyesuaian Kapasitas' : 'Contract Addendum & Scope Modification Workflow'"
            :description="app()->getLocale() === 'id' ? 'Mekanisme resmi penerbitan adendum saat terjadi perubahan kuota user atau penambahan fitur di tengah masa kontrak.' : 'Official procedure for issuing contract addendums when user quota or scope changes mid-contract.'"
        >
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div class="devcalc-card" style="border-left: 4px solid #f59e0b;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <h4 class="devcalc-card-title" style="color: #b45309;">
                            {{ app()->getLocale() === 'id' ? 'Skenario Penambahan Kuota di Tengah Periode (Contoh: +50 User untuk Sisa 6 Bulan)' : 'Mid-Contract Capacity Upgrade Scenario (e.g. +50 Users for remaining 6 months)' }}
                        </h4>
                        <span class="devcalc-pill" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                            {{ app()->getLocale() === 'id' ? 'Alur Adendum' : 'Addendum Flow' }}
                        </span>
                    </div>
                    <p class="devcalc-step-desc" style="margin-top: 0; line-height: 1.6;">
                        {{ app()->getLocale() === 'id'
                            ? 'Ketika kontrak berjalan (misal 12 bulan) dan di bulan ke-6 klien ingin menambah kapasitas pengguna aktif dari 50 menjadi 100 user, Sales tidak perlu mengubah kontrak awal yang sudah ditandatangani. Cukup klik tombol "Buat Adendum" pada kontrak induk.'
                            : 'When a contract is active and the client requests to expand user capacity from 50 to 100 users mid-term, Sales can click "Create Addendum" on the parent contract instead of modifying the signed agreement.'
                        }}
                    </p>
                    <ol style="margin: 10px 0 0 18px; padding: 0; font-size: 12.5px; color: #475569; line-height: 1.7;">
                        <li><strong>{{ app()->getLocale() === 'id' ? 'Klik "Buat Adendum":' : 'Click "Create Addendum":' }}</strong> {{ app()->getLocale() === 'id' ? 'Tersedia di tabel daftar penawaran atau tombol atas di halaman Edit.' : 'Available in the projects table actions or top header on the Edit screen.' }}</li>
                        <li><strong>{{ app()->getLocale() === 'id' ? 'Tentukan Sisa Periode:' : 'Set Remaining Duration:' }}</strong> {{ app()->getLocale() === 'id' ? 'Masukkan sisa bulan yang akan ditagihkan (misal 6 bulan).' : 'Specify remaining billing months (e.g. 6 months).' }}</li>
                        <li><strong>{{ app()->getLocale() === 'id' ? 'Masukkan Kapasitas Baru:' : 'Enter New Quota:' }}</strong> {{ app()->getLocale() === 'id' ? 'Isi jumlah user tambahan (misal 50 user) atau kuota total baru.' : 'Input additional user seats (e.g. 50 users) or updated total quota.' }}</li>
                        <li><strong>{{ app()->getLocale() === 'id' ? 'Penomoran Otomatis & Cetak PDF:' : 'Automatic Code & PDF Output:' }}</strong> {{ app()->getLocale() === 'id' ? 'Sistem otomatis menghasilkan nomor dokumen resmi #QUO-00003-ADD-01 yang tertaut ke kontrak induk dengan judul Surat Penawaran Adendum dan catatan ruang lingkup.' : 'System generates official document code #QUO-00003-ADD-01 referencing parent contract with dedicated addendum terms.' }}</li>
                    </ol>
                </div>
            </div>
        </x-filament::section>

        <!-- 5. Formula & Technical Calculation Breakdown -->
        <x-filament::section
            icon="heroicon-o-variable"
            :heading="app()->getLocale() === 'id' ? 'Rumus Kalkulasi & Panduan Bobot Kompleksitas' : 'Calculation Formula & Complexity Multipliers'"
            :description="app()->getLocale() === 'id' ? 'Standarisasi komputasi harga untuk menjamin transparansi dan konsistensi perhitungan.' : 'Standardized pricing formulas to ensure financial precision and transparency.'"
        >
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                <!-- Formula 1: Per Item -->
                <div class="devcalc-formula-box" style="margin-bottom: 0;">
                    <div class="devcalc-formula-title">
                        {{ app()->getLocale() === 'id' ? '1. Harga Terhitung Per Fitur' : '1. Per-Item Calculated Price' }}
                    </div>
                    <div class="devcalc-formula-text">
                        {{ app()->getLocale() === 'id' ? 'Harga Terhitung = Harga Dasar × Bobot Kompleksitas' : 'Calculated Price = Base Price × Complexity Weight' }}
                    </div>
                </div>

                <!-- Formula 2: One-Off -->
                <div class="devcalc-formula-box" style="margin-bottom: 0;">
                    <div class="devcalc-formula-title">
                        {{ app()->getLocale() === 'id' ? '2. Total Skema Putus Kontrak' : '2. One-Off Contract Total' }}
                    </div>
                    <div class="devcalc-formula-text">
                        {{ app()->getLocale() === 'id' ? 'Total Penawaran = Total Penjumlahan Seluruh Harga Terhitung Fitur' : 'Total Quotation = Sum of All Calculated Feature Prices' }}
                    </div>
                </div>

                <!-- Formula 3: Recurring Fee per Basis -->
                <div class="devcalc-formula-box" style="margin-bottom: 0;">
                    <div class="devcalc-formula-title">
                        {{ app()->getLocale() === 'id' ? '3. Biaya Berulang Langganan (Per Siklus)' : '3. Subscription Recurring Fee (Per Cycle)' }}
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <div class="devcalc-formula-text" style="font-size: 13.5px; font-weight: 600;">
                            <span style="color: #64748b; font-weight: 500;">• Flat Modular:</span> {{ app()->getLocale() === 'id' ? 'Biaya Berulang = Total Harga Langganan Modul Terhitung' : 'Recurring Fee = Sum of Module Subscription Prices' }}
                        </div>
                        <div class="devcalc-formula-text" style="font-size: 13.5px; font-weight: 600;">
                            <span style="color: #64748b; font-weight: 500;">• Per-User:</span> {{ app()->getLocale() === 'id' ? 'Biaya Berulang = Jumlah Pengguna Aktif × Tarif per Pengguna' : 'Recurring Fee = Active Users × Price per User' }}
                        </div>
                        <div class="devcalc-formula-text" style="font-size: 13.5px; font-weight: 600;">
                            <span style="color: #64748b; font-weight: 500;">• Hybrid:</span> {{ app()->getLocale() === 'id' ? 'Biaya Berulang = Total Harga Modul + (Jumlah Pengguna × Tarif per Pengguna)' : 'Recurring Fee = Module Total + (Active Users × Price per User)' }}
                        </div>
                    </div>
                </div>

                <!-- Formula 4: Subscription Grand Total -->
                <div class="devcalc-formula-box" style="margin-bottom: 0;">
                    <div class="devcalc-formula-title">
                        {{ app()->getLocale() === 'id' ? '4. Total Nilai Kontrak Langganan' : '4. Subscription Total Contract Value' }}
                    </div>
                    <div class="devcalc-formula-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Total Nilai Kontrak = Biaya Setup Awal + (Biaya Berulang × Durasi Komitmen)' 
                            : 'Total Contract Value = Setup Fee + (Recurring Fee × Commitment Duration)' 
                        }}
                    </div>
                </div>

                <!-- Formula 5: Addendum Contract Value -->
                <div class="devcalc-formula-box" style="margin-bottom: 0;">
                    <div class="devcalc-formula-title">
                        {{ app()->getLocale() === 'id' ? '5. Total Nilai Adendum Kontrak' : '5. Contract Addendum Total Value' }}
                    </div>
                    <div class="devcalc-formula-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Total Nilai Adendum = Biaya Penyesuaian per Siklus × Sisa Durasi Kontrak' 
                            : 'Total Addendum Value = Adjustment Fee per Cycle × Remaining Duration' 
                        }}
                    </div>
                </div>
            </div>

            <!-- Multipliers Table/Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                <!-- 1.00x -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">1.00x</span>
                        <span class="devcalc-pill devcalc-pill-blue">Standar</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' ? 'CRUD standar, template bawaan, dan fitur tanpa integrasi eksternal rumit.' : 'Standard CRUD, boilerplate modules, straightforward UI forms.' }}
                    </p>
                </div>

                <!-- 1.25x - 1.50x -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">1.25x - 1.50x</span>
                        <span class="devcalc-pill devcalc-pill-green">Menengah</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' ? 'Integrasi API pihak ketiga (Payment Gateway, SMS/Email OTP, Webhook listener).' : 'Third-party API integrations, payment gateways, webhooks, or OAuth.' }}
                    </p>
                </div>

                <!-- 1.75x - 2.00x -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">1.75x - 2.00x</span>
                        <span class="devcalc-pill devcalc-pill-blue">Kompleks</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' ? 'Logika bisnis rumit, WebSockets realtime, multi-tenancy, reporting analitik.' : 'Complex business rules, realtime WebSockets, multi-tenant databases.' }}
                    </p>
                </div>

                <!-- > 2.00x -->
                <div class="devcalc-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span class="devcalc-card-title">&gt; 2.00x</span>
                        <span class="devcalc-pill devcalc-pill-red">Tinggi / Riset</span>
                    </div>
                    <p class="devcalc-card-desc">
                        {{ app()->getLocale() === 'id' ? 'Arsitektur microservices terdistribusi tinggi, machine learning, riset algoritma khusus.' : 'High-concurrency distributed engines, custom AI/ML pipelines, deep research.' }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- 6. FAQ Section -->
        <x-filament::section
            icon="heroicon-o-question-mark-circle"
            :heading="app()->getLocale() === 'id' ? 'Pertanyaan yang Sering Diajukan (FAQ)' : 'Frequently Asked Questions (FAQ)'"
        >
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <!-- FAQ 1 -->
                <div class="devcalc-faq-item">
                    <div class="devcalc-faq-title">
                        {{ app()->getLocale() === 'id' ? '1. Bagaimana cara menambahkan fitur kustom yang tidak ada di katalog modul?' : '1. How to add a custom feature not listed in the catalog?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Pada form Proyek di bagian Line Items, kosongkan dropdown Feature Catalog Template. Lalu ketik langsung Nama Fitur dan Base Price secara manual.' 
                            : 'In the Project form under Line Items, leave the Feature Catalog Template dropdown unselected. Then type the custom feature name and base price directly.' 
                        }}
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="devcalc-faq-item">
                    <div class="devcalc-faq-title">
                        {{ app()->getLocale() === 'id' ? '2. Bagaimana penentuan harga akhir untuk setiap fitur?' : '2. How is the final price for each feature calculated?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Harga akhir per fitur dihitung otomatis dengan mengalikan Harga Dasar (Base Price IDR) dengan Bobot Kompleksitas (Complexity Weight).' 
                            : 'The final item price is automatically calculated by multiplying the Base Price (in IDR) by the Complexity Weight multiplier.' 
                        }}
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="devcalc-faq-item">
                    <div class="devcalc-faq-title">
                        {{ app()->getLocale() === 'id' ? '3. Mengapa Sales tidak dapat melihat menu Master Data Modul?' : '3. Why can Sales not see the Master Data Modules menu?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Berdasarkan kebijakan keamanan (RBAC Spatie & Policies), hanya Administrator yang memiliki otorisasi untuk mengubah basis data modul perusahaan.' 
                            : 'Under security policy (Spatie RBAC & Policies), only Administrator has permission to edit company standardized base pricing.' 
                        }}
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="devcalc-faq-item">
                    <div class="devcalc-faq-title">
                        {{ app()->getLocale() === 'id' ? '4. Apa perbedaan Skema Putus Kontrak dan Skema Berlangganan (Subscription)?' : '4. What is the difference between One-Off Contract and Subscription schemes?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Skema Putus Kontrak (One-Off) ditujukan untuk proyek pengembangan sekali bayar (lisensi lepas) dengan termin pembayaran standar (DP 50%, 30%, 20%). Sedangkan Skema Berlangganan (Subscription / SaaS) ditujukan untuk layanan berulang (Bulanan/Tahunan) dengan durasi komitmen kontrak tertentu, opsi Biaya Setup Awal, serta klausul SLA dan pemeliharaan aktif.' 
                            : 'One-Off scheme is intended for fixed-price project handovers with milestone payments. Subscription scheme is tailored for recurring SaaS / retainers (Monthly/Yearly) with contract commitment periods, optional setup fees, and active SLA/maintenance clauses.' 
                        }}
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="devcalc-faq-item">
                    <div class="devcalc-faq-title">
                        {{ app()->getLocale() === 'id' ? '5. Bagaimana cara membuat dokumen Adendum jika klien ingin menambah kuota user di pertengahan kontrak?' : '5. How to issue an Addendum if a client upgrades user capacity mid-contract?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Buka daftar penawaran atau halaman edit penawaran induk, lalu klik tombol "Buat Adendum". Masukkan tipe penyesuaian, sisa bulan berjalan (misal 6 bulan), dan kuota user baru. Sistem akan membuat dokumen penawaran adendum bernomor resmi #QUO-xxxxx-ADD-01 yang menghitung nilai penambahan secara tepat.' 
                            : 'Open the quotations table or parent quotation edit page, then click "Create Addendum". Enter the adjustment type, remaining months (e.g. 6 months), and new user quota. The system creates an official addendum document #QUO-xxxxx-ADD-01 calculating exact prorated upgrade value.' 
                        }}
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="devcalc-faq-item">
                    <div class="devcalc-faq-title">
                        {{ app()->getLocale() === 'id' ? '6. Mengapa pada dokumen adendum biaya setup awal otomatis menjadi Rp 0?' : '6. Why is setup fee automatically Rp 0 on addendum documents?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Karena biaya setup / onboarding awal platform sudah dibebankan pada kontrak induk awal. Namun jika pada adendum terdapat biaya konfigurasi khusus tambahan, Sales tetap dapat mengisi nominal setup fee secara manual.' 
                            : 'Because initial platform onboarding fees were already billed in the parent contract. However, if custom configuration is required for the addendum, Sales can still specify a setup fee manually.' 
                        }}
                    </div>
                </div>

                <!-- FAQ 7 -->
                <div class="devcalc-faq-item">
                    <div class="devcalc-faq-title">
                        {{ app()->getLocale() === 'id' ? '7. Bagaimana ketentuan Garansi Maintenance & SLA Pemeliharaan?' : '7. What are the rules for Maintenance SLA Guarantee?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Setiap penawaran (baik Beli Putus maupun Langganan) dilengkapi pilihan Masa Garansi Maintenance (1 Bulan, 3 Bulan Standar SLA, 6 Bulan Extended, atau 12 Bulan Full Year). Garansi ini memberikan jaminan pemeliharaan & perbaikan bug gratis pasca serah terima (Handover & UAT) dan tercantum resmi pada klausul dokumen penawaran PDF.' 
                            : 'Every quotation (both One-Off and Subscription) includes a customizable Maintenance SLA Guarantee period (1 Month, 3 Months Standard SLA, 6 Months Extended, or 12 Months Full Year). This guarantees free bug fixes & support post-handover, explicitly listed in the PDF agreement terms.' 
                        }}
                    </div>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
