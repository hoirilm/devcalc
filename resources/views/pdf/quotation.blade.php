<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Penawaran Harga #{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }} - {{ $project->client_name }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, table, tr, td, th, div, p, span, h1, h2, h3, strong, b, em, i, ol, ul, li {
            font-family: 'Helvetica', 'Arial', sans-serif !important;
        }

        body {
            color: #1e293b;
            font-size: 11px;
            line-height: 1.5;
            padding: 32px 36px;
            background-color: #ffffff;
        }

        .header-table {
            width: 100%;
            margin-bottom: 24px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
        }

        .company-logo {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .company-logo span {
            color: #2563eb;
        }

        .company-subtitle {
            font-size: 9.5px;
            font-weight: normal;
            color: #64748b;
            margin-top: 2px;
        }

        .quotation-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quotation-number {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            font-family: monospace;
            margin-top: 2px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 22px;
        }

        .meta-col {
            width: 50%;
            vertical-align: top;
        }

        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 14px;
            margin-right: 8px;
            min-height: 110px;
        }

        .meta-box-right {
            margin-right: 0;
            margin-left: 8px;
        }

        .meta-heading {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .meta-client-name {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.3;
        }

        .meta-sub {
            font-size: 10.5px;
            color: #475569;
            padding: 2px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .status-generated {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            table-layout: fixed;
        }

        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 9px 12px;
        }

        .items-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 10.5px;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center, th.text-center, td.text-center {
            text-align: center !important;
        }

        .text-right, th.text-right, td.text-right {
            text-align: right !important;
        }

        .text-left, th.text-left, td.text-left {
            text-align: left !important;
        }

        .item-title {
            font-weight: bold;
            color: #0f172a;
            font-size: 11px;
        }

        .item-category {
            font-size: 9px;
            color: #64748b;
            margin-top: 1px;
        }

        .weight-pill {
            display: inline-block;
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            font-weight: bold;
            font-size: 9.5px;
            padding: 1px 6px;
            border-radius: 4px;
        }

        /* Summary Section */
        .summary-table {
            width: 100%;
            margin-bottom: 22px;
        }

        .rate-note-col {
            width: 52%;
            vertical-align: top;
            padding-right: 12px;
        }

        .totals-col {
            width: 48%;
            vertical-align: top;
        }

        .rate-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 10px;
            color: #334155;
            line-height: 1.5;
        }

        .totals-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }

        .totals-card td {
            padding: 7px 12px;
            font-size: 10.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .totals-card tr:last-child td {
            border-bottom: none;
        }

        .grand-total-row td {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: bold;
            padding: 9px 12px !important;
        }

        .terms-box {
            margin-top: 14px;
            padding: 10px 14px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        .terms-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .terms-list {
            padding-left: 14px;
            font-size: 9.5px;
            color: #475569;
            line-height: 1.5;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
        }

        .sig-col {
            width: 45%;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-spacer {
            width: 10%;
        }

        .sig-line {
            border-bottom: 1px solid #0f172a;
            margin-top: 50px;
            margin-bottom: 4px;
        }

        .sig-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 11px;
        }

        .sig-role {
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="company-logo">DEV<span>CALC</span></div>
                <div class="company-subtitle">Penawaran Harga & Estimasi Rekayasa Perangkat Lunak</div>
            </td>
            <td style="vertical-align: middle; text-align: right;">
                <div class="quotation-title">
                    {{ $project->isAddendum() ? 'Surat Penawaran Adendum' : 'Surat Penawaran Harga' }}
                </div>
                <div class="quotation-number">#{{ $project->getQuotationCode() }}</div>
            </td>
        </tr>
    </table>

    <!-- Project and Client Metadata -->
    <table class="meta-table">
        <tr>
            <td class="meta-col">
                <div class="meta-box">
                    <div class="meta-heading">Ditujukan Kepada (Klien)</div>
                    <div class="meta-client-name">{{ $project->client_name }}</div>
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 6px;">
                        Dokumen Resmi Penawaran Pengembangan Perangkat Lunak
                    </div>
                </div>
            </td>
            <td class="meta-col">
                <div class="meta-box meta-box-right">
                    <div class="meta-heading">Informasi Dokumen</div>
                    <table style="width: 100%;">
                        <tr>
                            <td class="meta-sub">Tanggal Terbit:</td>
                            <td class="meta-sub text-right"><strong>{{ $project->created_at->format('d M Y') }}</strong></td>
                        </tr>
                        @if($project->isAddendum() && $project->parent_id)
                            <tr>
                                <td class="meta-sub">Kontrak Induk:</td>
                                <td class="meta-sub text-right">
                                    <strong style="color: #2563eb;">#{{ $project->parent ? $project->parent->getQuotationCode() : 'QUO-' . str_pad($project->parent_id, 5, '0', STR_PAD_LEFT) }}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="meta-sub">Estimator:</td>
                            <td class="meta-sub text-right"><strong>{{ $project->user->name ?? 'Tim Internal' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="meta-sub">Skema Kontrak:</td>
                            <td class="meta-sub text-right">
                                <strong>
                                    @if($project->billing_type === 'subscription')
                                        @php
                                            $basisName = match($project->subscription_basis) {
                                                'per_user' => 'Per-User',
                                                'hybrid' => 'Hybrid',
                                                default => 'Modular'
                                            };
                                            $cycleName = $project->billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan';
                                        @endphp
                                        Langganan ({{ $basisName }} - {{ $cycleName }})
                                    @else
                                        Beli Putus (One-Off)
                                    @endif
                                </strong>
                            </td>
                        </tr>
                        @if($project->billing_type === 'subscription' && in_array($project->subscription_basis, ['per_user', 'hybrid']))
                            <tr>
                                <td class="meta-sub">Kapasitas User:</td>
                                <td class="meta-sub text-right">
                                    <strong>{{ $project->user_count }} Pengguna (@ {{ \Illuminate\Support\Number::currency($project->price_per_user, 'IDR', 'id') }})</strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="meta-sub">Status:</td>
                            <td class="text-right">
                                <span class="status-badge {{ $project->status === 'Generated' ? 'status-generated' : 'status-draft' }}">
                                    {{ $project->status === 'Generated' ? 'RESMI' : 'DRAFT' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>



    @if($project->isAddendum() && $project->addendum_notes)
        <div style="background: #f8fafc; border-left: 3px solid #0f172a; padding: 10px 14px; margin-bottom: 16px; border-radius: 0 4px 4px 0; border: 1px solid #cbd5e1;">
            <div style="font-size: 9.5px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 2px;">Ruang Lingkup & Catatan Adendum:</div>
            <div style="font-size: 9.5px; color: #334155; line-height: 1.4;">{{ $project->addendum_notes }}</div>
        </div>
    @endif

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" width="4%" style="width: 4%;">#</th>
                <th class="text-left" width="58%" style="width: 58%;">Deskripsi Fitur / Lingkup Pekerjaan</th>
                <th class="text-right" width="15%" style="width: 15%;">Harga Dasar</th>
                <th class="text-center" width="7%" style="width: 7%;">Bobot</th>
                <th class="text-right" width="16%" style="width: 16%;">
                    {{ $project->billing_type === 'subscription' ? 'Biaya / Siklus' : 'Harga Terhitung' }}
                </th>
            </tr>
        </thead>
        <tbody>
            @if($project->billing_type === 'subscription' && $project->subscription_basis === 'per_user')
                @php
                    $cycleMultiplier = $project->billing_cycle === 'yearly' ? 12 : 1;
                    $userCycleRate = $project->price_per_user * $cycleMultiplier;
                    $userTotalCycle = $project->user_count * $userCycleRate;
                @endphp
                <!-- Primary User License Row -->
                <tr style="background-color: #f8fafc;">
                    <td class="text-center" style="font-weight: bold; color: #0f172a;">1</td>
                    <td class="text-left">
                        <div class="item-title" style="color: #0f172a;">
                            Paket Lisensi Kapasitas Pengguna ({{ $project->user_count }} Pengguna Aktif)
                        </div>
                        <div class="item-category">
                            Akses sistem, pemeliharaan & infrastruktur cloud (@ {{ \Illuminate\Support\Number::currency($userCycleRate, 'IDR', 'id') }} / user / {{ $project->billing_cycle === 'yearly' ? 'th' : 'bln' }})
                        </div>
                    </td>
                    <td class="text-right">
                        {{ \Illuminate\Support\Number::currency($userCycleRate, 'IDR', 'id') }}
                    </td>
                    <td class="text-center">
                        <span class="weight-pill">{{ $project->user_count }} user</span>
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #0f172a;">
                        {{ \Illuminate\Support\Number::currency($userTotalCycle, 'IDR', 'id') }}
                    </td>
                </tr>

                <!-- Attached Included Scope Modules -->
                @foreach($project->items as $index => $item)
                    <tr>
                        <td class="text-center" style="color: #64748b;">{{ $index + 2 }}</td>
                        <td class="text-left">
                            <div class="item-title">{{ $item->item_name }}</div>
                            @if($item->module && $item->module->category)
                                <div class="item-category">{{ $item->module->category }}</div>
                            @endif
                        </td>
                        <td class="text-right" style="color: #64748b; font-size: 10px;">
                            Termasuk
                        </td>
                        <td class="text-center">
                            <span class="weight-pill">{{ number_format($item->complexity_weight, 2) }}x</span>
                        </td>
                        <td class="text-right" style="font-weight: bold; color: #0f172a; font-size: 10px;">
                            Rp 0 (Termasuk)
                        </td>
                    </tr>
                @endforeach
            @else
                @forelse($project->items as $index => $item)
                    <tr>
                        <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <div class="item-title">{{ $item->item_name }}</div>
                            @if($item->module && $item->module->category)
                                <div class="item-category">{{ $item->module->category }}</div>
                            @endif
                        </td>
                        <td class="text-right">
                            {{ \Illuminate\Support\Number::currency($item->base_price, 'IDR', 'id') }}
                        </td>
                        <td class="text-center">
                            <span class="weight-pill">{{ number_format($item->complexity_weight, 2) }}x</span>
                        </td>
                        <td class="text-right" style="font-weight: bold; color: #0f172a;">
                            {{ \Illuminate\Support\Number::currency($item->calculated_price, 'IDR', 'id') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">
                            Belum ada item fitur yang ditambahkan ke penawaran ini.
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>

    <!-- Summary and Total -->
    <table class="summary-table">
        <tr>
            <td class="rate-note-col">
                <div class="rate-box">
                    @if($project->billing_type === 'subscription')
                        <strong style="text-transform: uppercase; font-size: 9px; color: #0f172a;">Ketentuan Skema Berlangganan (SaaS):</strong><br>
                        @if($project->subscription_basis === 'per_user')
                            Tagihan dihitung berdasarkan kapasitas pengguna aktif ({{ $project->user_count }} User) per siklus ({{ $project->billing_cycle === 'yearly' ? 'tahunan' : 'bulanan' }}).
                        @elseif($project->subscription_basis === 'hybrid')
                            Tagihan menggabungkan sewa infrastruktur fitur modul dan kuota pengguna aktif ({{ $project->user_count }} User) per siklus ({{ $project->billing_cycle === 'yearly' ? 'tahunan' : 'bulanan' }}).
                        @else
                            Biaya modul dihitung per siklus ({{ $project->billing_cycle === 'yearly' ? 'tahunan' : 'bulanan' }}), mencakup server & maintenance rutin.
                        @endif
                    @else
                        <strong style="text-transform: uppercase; font-size: 9px; color: #0f172a;">Ketentuan Perhitungan Standar:</strong><br>
                        Harga dihitung dari harga dasar modul dikalikan bobot kompleksitas pengerjaan.
                    @endif
                    <div style="font-size: 9px; color: #64748b; margin-top: 4px;">
                        * Seluruh nominal penawaran diterbitkan dalam mata uang Rupiah (Rp).
                    </div>
                </div>

                @if($project->notes)
                    <div class="rate-box" style="margin-top: 10px; background-color: #f8fafc; border: 1px solid #cbd5e1; border-left: 3px solid #0f172a; color: #1e293b;">
                        <strong style="text-transform: uppercase; font-size: 9px; color: #0f172a;">Catatan Penawaran / Scope Notes:</strong><br>
                        <div style="font-size: 9.5px; color: #334155; margin-top: 3px; line-height: 1.4;">{{ $project->notes }}</div>
                    </div>
                @endif
            </td>
            <td class="totals-col">
                <table class="totals-card">
                    <tr>
                        <td style="color: #64748b;">
                            {{ $project->subscription_basis === 'per_user' ? 'Fitur Terlampir:' : 'Jumlah Fitur:' }}
                        </td>
                        <td class="text-right" style="font-weight: bold;">
                            {{ $project->items->count() }} Modul {{ $project->subscription_basis === 'per_user' ? '(Termasuk)' : '' }}
                        </td>
                    </tr>
                    @if($project->billing_type === 'subscription')
                        @php
                            $cycleUnit = $project->billing_cycle === 'yearly' ? 'th' : 'bln';
                            $multiplier = $project->billing_cycle === 'yearly' ? 12 : 1;
                            $itemsSum = ((float) $project->items->sum('calculated_price')) * $multiplier;
                            $userSum = (((int) $project->user_count) * ((float) $project->price_per_user)) * $multiplier;
                        @endphp

                        @if(in_array($project->subscription_basis, ['modular', 'hybrid']))
                            <tr>
                                <td style="color: #64748b;">Biaya Modul:</td>
                                <td class="text-right" style="font-weight: bold;">
                                    {{ \Illuminate\Support\Number::currency($itemsSum, 'IDR', 'id') }} / {{ $cycleUnit }}
                                </td>
                            </tr>
                        @endif

                        @if(in_array($project->subscription_basis, ['per_user', 'hybrid']))
                            <tr>
                                <td style="color: #64748b;">Biaya Pengguna ({{ $project->user_count }} User):</td>
                                <td class="text-right" style="font-weight: bold;">
                                    {{ \Illuminate\Support\Number::currency($userSum, 'IDR', 'id') }} / {{ $cycleUnit }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td style="color: #64748b;">Total Biaya Berulang:</td>
                            <td class="text-right" style="font-weight: bold; color: #2563eb;">
                                {{ \Illuminate\Support\Number::currency($project->getRecurringPerCycle(), 'IDR', 'id') }} / {{ $cycleUnit }}
                            </td>
                        </tr>
                        @if((float) $project->setup_fee > 0)
                            <tr>
                                <td style="color: #64748b;">Biaya Setup Awal:</td>
                                <td class="text-right" style="font-weight: bold;">
                                    {{ \Illuminate\Support\Number::currency($project->setup_fee, 'IDR', 'id') }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="color: #64748b;">Durasi Komitmen:</td>
                            <td class="text-right" style="font-weight: bold;">
                                {{ $project->subscription_duration }} {{ $project->billing_cycle === 'yearly' ? 'Tahun' : 'Bulan' }}
                            </td>
                        </tr>
                        <tr class="grand-total-row">
                            <td>Total Nilai Kontrak:</td>
                            <td class="text-right">
                                {{ \Illuminate\Support\Number::currency($project->grand_total, 'IDR', 'id') }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td style="color: #64748b;">Masa Garansi SLA:</td>
                            <td class="text-right" style="font-weight: bold; color: #047857;">
                                {{ $project->getMaintenanceMonths() }} Bulan Gratis
                            </td>
                        </tr>
                        <tr class="grand-total-row">
                            <td>Total Akhir:</td>
                            <td class="text-right">
                                {{ \Illuminate\Support\Number::currency($project->grand_total, 'IDR', 'id') }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>



    <!-- Terms and Conditions -->
    <div class="terms-box">
        <div class="terms-title">Syarat & Ketentuan</div>
        <ol class="terms-list">
            @if($project->billing_type === 'subscription')
                <li>Penawaran harga ini berlaku selama 30 (tiga puluh) hari kalender terhitung sejak tanggal dokumen diterbitkan.</li>
                <li>Biaya langganan ditagihkan di awal setiap siklus ({{ $project->billing_cycle === 'yearly' ? 'tahunan' : 'bulanan' }}), dengan durasi komitmen minimum {{ $project->subscription_duration }} {{ $project->billing_cycle === 'yearly' ? 'tahun' : 'bulan' }}.</li>
                <li>Layanan mencakup ketersediaan sistem, pemeliharaan rutin, pembaruan keamanan, dan dukungan teknis garansi SLA selama {{ $project->getMaintenanceMonths() }} bulan awal sesuai kesepakatan.</li>
                <li>Pembatalan atau penyesuaian paket langganan wajib disampaikan melalui pemberitahuan tertulis sekurang-kurangnya 30 hari kalender sebelum periode tagihan berakhir.</li>
            @else
                <li>Penawaran harga ini berlaku selama 30 (tiga puluh) hari kalender terhitung sejak tanggal dokumen diterbitkan.</li>
                <li>Lingkup pekerjaan terikat secara ketat pada rincian fitur di atas. Penambahan fitur atau perubahan kebutuhan di luar rincian akan dikenakan biaya terpisah melalui <em>Change Request (CR)</em>.</li>
                <li>Termin Pembayaran Standar: Uang Muka (DP) 50% saat penandatanganan kontrak, 30% pada tahap Evaluasi Tengah (Mid-Development), dan 20% saat Serah Terima Akhir (UAT & Handover).</li>
                <li>Garansi Pemeliharaan SLA: Penawaran mencakup garansi pemeliharaan & perbaikan bug gratis selama {{ $project->getMaintenanceMonths() }} bulan pasca Serah Terima Akhir (Handover).</li>
            @endif
        </ol>
    </div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td class="sig-col">
                <div style="font-size: 9.5px; color: #64748b; margin-bottom: 4px;">Diajukan Oleh:</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $project->user->name ?? 'Sales Representative' }}</div>
                <div class="sig-role">Solution Architect / Estimator DevCalc</div>
            </td>
            <td class="sig-spacer"></td>
            <td class="sig-col">
                <div style="font-size: 9.5px; color: #64748b; margin-bottom: 4px;">Disetujui & Diterima Oleh Klien:</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $project->client_name }}</div>
                <div class="sig-role">Tanda Tangan & Cap Perusahaan</div>
            </td>
        </tr>
    </table>

</body>
</html>
