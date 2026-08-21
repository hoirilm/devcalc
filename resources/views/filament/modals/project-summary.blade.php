<div style="display: flex; flex-direction: column; gap: 16px; font-size: 13px;">
    <!-- Top Metadata Card -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <div>
            <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Kode Penawaran</div>
            <div style="font-weight: 700; color: #1e3a8a; font-size: 14px; margin-top: 2px;">#{{ $project->getQuotationCode() }}</div>
        </div>
        <div>
            <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Klien</div>
            <div style="font-weight: 700; color: #0f172a; font-size: 14px; margin-top: 2px;">{{ $project->client_name }}</div>
        </div>
        <div>
            <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Skema Kontrak</div>
            <div style="font-weight: 600; color: #0f172a; margin-top: 2px;">
                @if($project->billing_type === 'subscription')
                    Langganan ({{ ucfirst($project->subscription_basis) }})
                @else
                    Putus Kontrak
                @endif
            </div>
        </div>
        <div>
            <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Status</div>
            <div style="margin-top: 2px;">
                <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: {{ $project->status === 'Generated' ? '#dcfce7' : '#fef3c7' }}; color: {{ $project->status === 'Generated' ? '#15803d' : '#b45309' }};">
                    {{ $project->status === 'Generated' ? 'Resmi (Generated)' : 'Draft' }}
                </span>
            </div>
        </div>
    </div>

    @if($project->isAddendum() && $project->addendum_notes)
        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 10px 14px; border-radius: 0 8px 8px 0;">
            <div style="font-size: 11.5px; font-weight: 700; color: #1e40af;">Ruang Lingkup Adendum:</div>
            <div style="font-size: 12px; color: #334155; margin-top: 2px;">{{ $project->addendum_notes }}</div>
        </div>
    @endif

    <!-- Financial Card -->
    <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 16px 20px; border-radius: 12px; color: #ffffff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="font-size: 11.5px; font-weight: 600; color: #bfdbfe; text-transform: uppercase; letter-spacing: 0.5px;">Total Nilai Penawaran</div>
            <div style="font-size: 22px; font-weight: 800; margin-top: 2px;">{{ \Illuminate\Support\Number::currency($project->grand_total, 'IDR', 'id') }}</div>
        </div>
        @if($project->billing_type === 'subscription')
            <div style="text-align: right;">
                <div style="font-size: 11px; color: #bfdbfe;">Biaya Berulang:</div>
                <div style="font-size: 15px; font-weight: 700;">{{ \Illuminate\Support\Number::currency($project->getRecurringPerCycle(), 'IDR', 'id') }} / {{ $project->billing_cycle === 'yearly' ? 'th' : 'bln' }}</div>
                <div style="font-size: 11px; color: #e0e7ff; margin-top: 2px;">Durasi: {{ $project->subscription_duration }} {{ $project->billing_cycle === 'yearly' ? 'Tahun' : 'Bulan' }}</div>
            </div>
        @endif
    </div>

    <!-- Items List Table Preview -->
    <div>
        <div style="font-size: 12px; font-weight: 700; color: #0f172a; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">
            Daftar Modul & Fitur ({{ $project->items->count() }} Item)
        </div>
        <div style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 12px;">
                <thead style="background: #f1f5f9; color: #475569; font-weight: 700; font-size: 11px; text-transform: uppercase;">
                    <tr>
                        <th style="padding: 8px 12px;">Fitur</th>
                        <th style="padding: 8px 12px; text-align: right;">Harga Dasar</th>
                        <th style="padding: 8px 12px; text-align: center;">Bobot</th>
                        <th style="padding: 8px 12px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->items as $item)
                        <tr style="border-top: 1px solid #e2e8f0;">
                            <td style="padding: 8px 12px; font-weight: 600; color: #0f172a;">{{ $item->item_name }}</td>
                            <td style="padding: 8px 12px; text-align: right; color: #64748b;">{{ \Illuminate\Support\Number::currency($item->base_price, 'IDR', 'id') }}</td>
                            <td style="padding: 8px 12px; text-align: center;"><span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-size: 10.5px; font-weight: 700;">{{ number_format($item->complexity_weight, 2) }}x</span></td>
                            <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #0f172a;">{{ \Illuminate\Support\Number::currency($item->calculated_price, 'IDR', 'id') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 16px; text-align: center; color: #94a3b8;">Tidak ada item fitur modul terlampir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
