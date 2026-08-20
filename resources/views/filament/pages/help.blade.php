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
                            ? 'Sistem penawaran software dinamis dengan bobot kompleksitas fitur (Complexity Weight), konversi multi-mata uang, dan sistem penguncian kurs (Lock-Rate) otomatis.' 
                            : 'Dynamic software quotation calculator with complexity multiplier, multi-currency conversion, and fixed lock-rate exchange protection.' 
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
                        ? 'Admin menyusun katalog fitur standar software beserta harga dasar (Base Price dalam IDR) dan kategori domain.'
                        : 'Administrators maintain standard feature templates, base pricing in IDR, and domain categories.'
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
                        ? 'Sales memilih mata uang (IDR, USD, EUR, SGD), memasukkan item modul dari katalog atau fitur kustom, dan mengatur bobot kompleksitas.'
                        : 'Sales choose target currency (IDR, USD, EUR, SGD), add catalog or custom scope items, and adjust complexity multipliers.'
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
                        ? 'Cetak dokumen penawaran resmi berstandar industri dengan format tabel profesional, catatan lock-rate kurs, dan kolom tanda tangan persetujuan.'
                        : 'Generate official PDF quotation with itemized breakdown, currency lock-rate notice, and approval signature block.'
                    }}
                </p>
            </div>
        </div>

        <!-- 3. Formula & Technical Calculation Breakdown -->
        <x-filament::section
            icon="heroicon-o-variable"
            :heading="app()->getLocale() === 'id' ? 'Rumus Kalkulasi & Panduan Bobot Kompleksitas' : 'Calculation Formula & Complexity Multipliers'"
            :description="app()->getLocale() === 'id' ? 'Standarisasi komputasi harga untuk menjamin transparansi dan konsistensi perhitungan.' : 'Standardized pricing formulas to ensure financial precision and transparency.'"
        >
            <!-- Formula Box -->
            <div class="devcalc-formula-box">
                <div class="devcalc-formula-title">
                    {{ app()->getLocale() === 'id' ? 'Rumus Harga Per Fitur (Calculated Price)' : 'Per-Item Price Formula' }}
                </div>
                <div class="devcalc-formula-text">
                    Calculated Price = (Base Price IDR &times; Complexity Weight) &divide; Exchange Rate
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

        <!-- 4. FAQ Section -->
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
                        {{ app()->getLocale() === 'id' ? '2. Apakah nilai kurs (Exchange Rate) akan berubah jika kurs pasar naik/turun?' : '2. Does the exchange rate change automatically if market currency fluctuates?' }}
                    </div>
                    <div class="devcalc-faq-text">
                        {{ app()->getLocale() === 'id' 
                            ? 'Tidak. Sistem menerapkan metode Lock-Rate. Nilai kurs yang tersimpan di dokumen penawaran akan terkunci permanen untuk proyek tersebut agar tidak terjadi selisih harga kepada klien.' 
                            : 'No. The system uses a Lock-Rate mechanism. The exchange rate saved on the quotation is permanently locked to protect against currency fluctuations.' 
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
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
