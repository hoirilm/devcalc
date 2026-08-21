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
            padding: 28px;
            background-color: #ffffff;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
        }

        .company-logo {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }

        .company-logo span {
            color: #4f46e5;
        }

        .company-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .report-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .report-meta {
            text-align: right;
            font-size: 10px;
            color: #475569;
        }

        /* KPI Summary Strip */
        .kpi-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }

        .kpi-label {
            font-size: 9.5px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
        }

        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }

        /* Projects List Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }

        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
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
            <td style="vertical-align: top; width: 50%;">
                <div class="company-logo">Dev<span>Calc</span></div>
                <div class="company-subtitle">Software Quotation Estimator System</div>
            </td>
            <td style="vertical-align: top; text-align: right; width: 50%;">
                <div class="report-title">Laporan Rekapitulasi Penawaran</div>
                <div class="report-meta">Tanggal Cetak: {{ date('d F Y') }}</div>
                <div class="report-meta">Dicetak Oleh: {{ auth()->user()->name ?? 'Administrator' }}</div>
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
                <div class="kpi-label">Beli Putus (One-Off)</div>
                <div class="kpi-value">{{ $summary['one_off_count'] }} Doc</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Langganan SaaS</div>
                <div class="kpi-value">{{ $summary['subscription_count'] }} Doc</div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 16%;">No. Penawaran</th>
                <th style="width: 25%;">Nama Klien</th>
                <th style="width: 18%;">Skema Pembayaran</th>
                <th style="width: 16%;">Nilai Penawaran</th>
                <th style="width: 10%;">Estimator</th>
                <th style="width: 10%;">Tanggal</th>
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
                            <span class="badge-subscription">SaaS ({{ $item->subscription_basis }})</span>
                        @else
                            <span class="badge-one-off">Beli Putus (One-Off)</span>
                        @endif
                    </td>
                    <td style="font-weight: bold;">Rp {{ number_format($item->grand_total, 0, ',', '.') }}</td>
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
