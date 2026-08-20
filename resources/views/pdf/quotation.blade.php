<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quotation #{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }} - {{ $project->client_name }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        body {
            color: #1e293b;
            font-size: 12px;
            line-height: 1.5;
            padding: 30px;
            background-color: #ffffff;
        }

        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
        }

        .company-logo {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .company-logo span {
            color: #2563eb;
        }

        .company-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }

        .quotation-title {
            text-align: right;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .quotation-number {
            text-align: right;
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
            margin-top: 4px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }

        .meta-col {
            width: 50%;
            vertical-align: top;
        }

        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-right: 10px;
        }

        .meta-box-right {
            margin-right: 0;
            margin-left: 10px;
        }

        .meta-heading {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .meta-value {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        .meta-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 4px;
        }

        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-generated {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 9px 10px;
            text-align: left;
        }

        .items-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .item-title {
            font-weight: 600;
            color: #0f172a;
        }

        .item-category {
            font-size: 10px;
            color: #64748b;
        }

        .weight-pill {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 25px;
        }

        .rate-note-col {
            width: 55%;
            vertical-align: top;
            padding-right: 15px;
        }

        .totals-col {
            width: 45%;
            vertical-align: top;
        }

        .rate-box {
            background-color: #eff6ff;
            border: 1px dashed #93c5fd;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 11px;
            color: #1e40af;
        }

        .totals-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            width: 100%;
            border-collapse: collapse;
        }

        .totals-card td {
            padding: 8px 12px;
        }

        .grand-total-row td {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
        }

        .terms-box {
            margin-top: 15px;
            padding: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .terms-title {
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .terms-list {
            padding-left: 15px;
            font-size: 10.5px;
            color: #64748b;
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
            border-bottom: 1px solid #475569;
            margin-top: 50px;
            margin-bottom: 4px;
        }

        .sig-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 11px;
        }

        .sig-role {
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="company-logo">DEV<span>CALC</span></div>
                <div class="company-subtitle">Software Engineering & Development Quotation</div>
            </td>
            <td style="vertical-align: middle;">
                <div class="quotation-title">Quotation</div>
                <div class="quotation-number">#QUO-{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</div>
            </td>
        </tr>
    </table>

    <!-- Project and Client Metadata -->
    <table class="meta-table">
        <tr>
            <td class="meta-col">
                <div class="meta-box">
                    <div class="meta-heading">Prepared For (Client)</div>
                    <div class="meta-value">{{ $project->client_name }}</div>
                    <div class="meta-sub">Currency: <strong>{{ $project->currency_code }}</strong></div>
                </div>
            </td>
            <td class="meta-col">
                <div class="meta-box meta-box-right">
                    <div class="meta-heading">Quotation Info</div>
                    <table style="width: 100%;">
                        <tr>
                            <td class="meta-sub">Date:</td>
                            <td class="meta-sub text-right"><strong>{{ $project->created_at->format('d M Y') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="meta-sub">Estimator:</td>
                            <td class="meta-sub text-right"><strong>{{ $project->user->name ?? 'Internal Team' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="meta-sub">Status:</td>
                            <td class="text-right">
                                <span class="status-badge {{ $project->status === 'Generated' ? 'status-generated' : 'status-draft' }}">
                                    {{ $project->status }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th>Feature / Scope of Work</th>
                <th class="text-right" style="width: 110px;">Base Price (IDR)</th>
                <th class="text-center" style="width: 80px;">Weight</th>
                <th class="text-right" style="width: 120px;">Price ({{ $project->currency_code }})</th>
            </tr>
        </thead>
        <tbody>
            @forelse($project->items as $index => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                    <td>
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
                    <td class="text-right" style="font-weight: 600; color: #0f172a;">
                        {{ \Illuminate\Support\Number::currency($item->calculated_price, $project->currency_code, $project->currency_code === 'IDR' ? 'id' : 'en') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">
                        No line items added to this quotation yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Rate Info and Total -->
    <table class="summary-table">
        <tr>
            <td class="rate-note-col">
                <div class="rate-box">
                    <strong>Exchange Rate & Lock-rate Notice:</strong><br>
                    Applied Rate: <strong>1 {{ $project->currency_code }} = Rp {{ number_format($project->exchange_rate, 2, ',', '.') }}</strong><br>
                    <span style="font-size: 10px; color: #3b82f6;">
                        * This exchange rate is locked for this quotation document.
                    </span>
                </div>
            </td>
            <td class="totals-col">
                <table class="totals-card">
                    <tr>
                        <td style="color: #64748b;">Subtotal Items:</td>
                        <td class="text-right" style="font-weight: 600;">
                            {{ $project->items->count() }} Features
                        </td>
                    </tr>
                    <tr class="grand-total-row">
                        <td>Grand Total:</td>
                        <td class="text-right">
                            {{ \Illuminate\Support\Number::currency($project->grand_total, $project->currency_code, $project->currency_code === 'IDR' ? 'id' : 'en') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Terms and Conditions -->
    <div class="terms-box">
        <div class="terms-title">Terms & Conditions</div>
        <ol class="terms-list">
            <li>Quotation is valid for 30 (thirty) calendar days from the date of issue.</li>
            <li>Scope of work is strictly bounded to the line items detailed above. Any scope creep or additional requirements will be billed via separate Change Request (CR).</li>
            <li>Standard Payment Milestone: 50% Initial Down Payment upon contract signing, 30% Mid-Development Review, 20% Final UAT & Source Code Handover.</li>
            <li>Estimated timeline will be provided upon formal acceptance of this quotation.</li>
        </ol>
    </div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td class="sig-col">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 4px;">Prepared By:</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $project->user->name ?? 'Sales Representative' }}</div>
                <div class="sig-role">DevCalc Solution Architect / Estimator</div>
            </td>
            <td class="sig-spacer"></td>
            <td class="sig-col">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 4px;">Client Acceptance & Approval:</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $project->client_name }}</div>
                <div class="sig-role">Authorized Signature & Stamp</div>
            </td>
        </tr>
    </table>

</body>
</html>
