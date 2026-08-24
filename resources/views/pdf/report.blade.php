<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi Penawaran Harga - DevCalc</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, table, tr, td, th, div, p, span, h1, h2, h3, strong, b {
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
            color: #64748b;
            margin-top: 2px;
        }

        .report-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-meta {
            text-align: right;
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }

        /* KPI Summary Strip */
        .kpi-table {
            width: 100%;
            margin-bottom: 22px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }

        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }

        .kpi-label {
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-value {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
        }

        /* Projects List Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 6px;
            text-align: left;
        }

        .data-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge-one-off {
            color: #0369a1;
            font-weight: bold;
        }

        .badge-subscription {
            color: #047857;
            font-weight: bold;
        }

        .footer-table {
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 9.5px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 50%;">
                <div class="company-logo">DEV<span>CALC</span></div>
                <div class="company-subtitle">Penawaran Harga & Estimasi Rekayasa Perangkat Lunak</div>
            </td>
            <td style="vertical-align: middle; text-align: right; width: 50%;">
                <div class="report-title">Laporan Rekapitulasi Penawaran</div>
                <div class="report-meta">Tanggal Cetak: <strong>{{ date('d F Y') }}</strong></div>
                <div class="report-meta">Dicetak Oleh: <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- KPI Summary Cards -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Total Penawaran</div>
                <div class="kpi-value">{{ $summary['total_count'] }} Dokumen</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Total Valuasi Kontrak</div>
                <div class="kpi-value">Rp {{ number_format($summary['total_value'], 0, ',', '.') }}</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Putus Kontrak</div>
                <div class="kpi-value">{{ $summary['one_off_count'] }} Dokumen</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Langganan SaaS</div>
                <div class="kpi-value">{{ $summary['subscription_count'] }} Dokumen</div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" style="width: 4%;">No</th>
                <th width="13%" style="width: 13%;">No. Penawaran</th>
                <th width="28.5%" style="width: 28.5%;">Nama Klien</th>
                <th width="17%" style="width: 17%;">Skema Pembayaran</th>
                <th width="15.5%" style="width: 15.5%; text-align: right;">Nilai Penawaran</th>
                <th width="13%" style="width: 13%;">Estimator</th>
                <th width="9%" style="width: 9%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; font-family: monospace;">#{{ $item->getQuotationCode() }}</td>
                    <td>
                        <strong>{{ $item->client_name }}</strong>
                        @if($item->isAddendum())
                            <div style="font-size: 8.5px; color: #d97706;">(Adendum Induk: #{{ $item->parent ? $item->parent->getQuotationCode() : '' }})</div>
                        @endif
                    </td>
                    <td>
                        @if($item->billing_type === 'subscription')
                            @php
                                $basisLabel = match($item->subscription_basis) {
                                    'per_user' => 'Per-User',
                                    'hybrid' => 'Hybrid',
                                    default => 'Modular'
                                };
                            @endphp
                            <span class="badge-subscription">Langganan ({{ $basisLabel }})</span>
                        @else
                            <span class="badge-one-off">Putus Kontrak</span>
                        @endif
                    </td>
                    <td style="font-weight: bold; text-align: right; white-space: nowrap;">Rp&nbsp;{{ number_format($item->grand_total, 0, ',', '.') }}</td>
                    <td>{{ $item->user ? $item->user->name : 'System' }}</td>
                    <td>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Tidak ada data penawaran harga yang memenuhi kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <table class="footer-table">
        <tr>
            <td style="text-align: left;">DevCalc Quotation System — Executive Summary Report</td>
            <td style="text-align: right;">Dokumen Resmi Kerahasiaan Perusahaan</td>
        </tr>
    </table>

</body>
</html>
