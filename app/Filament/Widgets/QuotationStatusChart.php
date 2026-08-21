<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class QuotationStatusChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md' => 7,
        'lg' => 8,
        'xl' => 8,
    ];

    protected static ?string $maxHeight = '500px';

    public ?string $filter = 'monthly_trend';

    public function getHeading(): string
    {
        return app()->getLocale() === 'id'
            ? 'Analisis Portofolio & Tren Penawaran'
            : 'Quotation Analytics & Growth Trends';
    }

    protected function getFilters(): ?array
    {
        return [
            'monthly_trend' => app()->getLocale() === 'id' ? '📈 Tren Nilai 6 Bulan Terakhir' : '📈 6-Month Value Trends',
            'scheme_breakdown' => app()->getLocale() === 'id' ? '📊 Komposisi Skema Kontrak' : '📊 Contract Schemes Breakdown',
            'conversion_rate' => app()->getLocale() === 'id' ? '🎯 Status & Konversi Nilai' : '🎯 Status & Value Conversion',
        ];
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('Admin');

        $baseQuery = Project::query();
        if (! $isAdmin && $user) {
            $baseQuery->where('user_id', $user->id);
        }

        $activeFilter = $this->filter;

        if ($activeFilter === 'scheme_breakdown') {
            // Breakdown by Contract Scheme
            $oneOffSum = (clone $baseQuery)->where('billing_type', 'one_off')->where('quotation_type', 'standard')->sum('grand_total');
            $modularSum = (clone $baseQuery)->where('billing_type', 'subscription')->where('subscription_basis', 'modular')->where('quotation_type', 'standard')->sum('grand_total');
            $perUserSum = (clone $baseQuery)->where('billing_type', 'subscription')->where('subscription_basis', 'per_user')->where('quotation_type', 'standard')->sum('grand_total');
            $hybridSum = (clone $baseQuery)->where('billing_type', 'subscription')->where('subscription_basis', 'hybrid')->where('quotation_type', 'standard')->sum('grand_total');
            $addendumSum = (clone $baseQuery)->where('quotation_type', 'addendum')->sum('grand_total');

            return [
                'datasets' => [
                    [
                        'label' => app()->getLocale() === 'id' ? 'Total Nilai (Juta Rp)' : 'Total Value (Million IDR)',
                        'data' => [
                            round($oneOffSum / 1000000, 2),
                            round($modularSum / 1000000, 2),
                            round($perUserSum / 1000000, 2),
                            round($hybridSum / 1000000, 2),
                            round($addendumSum / 1000000, 2),
                        ],
                        'backgroundColor' => [
                            '#3b82f6', // Blue (One-off)
                            '#10b981', // Emerald (Modular)
                            '#06b6d4', // Cyan (Per-user)
                            '#8b5cf6', // Violet (Hybrid)
                            '#f59e0b', // Amber (Addendum)
                        ],
                        'borderRadius' => 6,
                    ],
                ],
                'labels' => [
                    'Putus Kontrak',
                    'Langganan: Modular',
                    'Langganan: Per-User',
                    'Langganan: Hybrid',
                    'Adendum Kontrak',
                ],
            ];
        }

        if ($activeFilter === 'conversion_rate') {
            // Status Comparison: Count and Value
            $draftVal = (clone $baseQuery)->where('status', 'Draft')->sum('grand_total');
            $genVal = (clone $baseQuery)->where('status', 'Generated')->sum('grand_total');

            $draftCount = (clone $baseQuery)->where('status', 'Draft')->count();
            $genCount = (clone $baseQuery)->where('status', 'Generated')->count();

            return [
                'datasets' => [
                    [
                        'label' => app()->getLocale() === 'id' ? 'Nilai Estimasi (Juta Rp)' : 'Valuation (Million IDR)',
                        'data' => [
                            round($draftVal / 1000000, 2),
                            round($genVal / 1000000, 2),
                        ],
                        'backgroundColor' => ['#f59e0b', '#10b981'],
                        'borderRadius' => 6,
                    ],
                    [
                        'label' => app()->getLocale() === 'id' ? 'Jumlah Dokumen' : 'Total Documents',
                        'data' => [$draftCount, $genCount],
                        'backgroundColor' => ['#94a3b8', '#6366f1'],
                        'borderRadius' => 6,
                    ],
                ],
                'labels' => [
                    app()->getLocale() === 'id' ? 'Draft (Berjalan)' : 'Draft (In Progress)',
                    app()->getLocale() === 'id' ? 'Resmi (Generated)' : 'Official (Generated)',
                ],
            ];
        }

        // Default: 6 Months Trend
        $months = [];
        $oneOffData = [];
        $subData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->translatedFormat('M Y');

            $months[] = $monthLabel;

            $oneOffMonthly = (clone $baseQuery)
                ->where('billing_type', 'one_off')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('grand_total');

            $subMonthly = (clone $baseQuery)
                ->where('billing_type', 'subscription')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('grand_total');

            $oneOffData[] = round($oneOffMonthly / 1000000, 2);
            $subData[] = round($subMonthly / 1000000, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => app()->getLocale() === 'id' ? 'Langganan / SaaS (Juta Rp)' : 'Subscriptions (Million IDR)',
                    'data' => $subData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => app()->getLocale() === 'id' ? 'Putus Kontrak (Juta Rp)' : 'One-Off Builds (Million IDR)',
                    'data' => $oneOffData,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.12)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        if ($this->filter === 'scheme_breakdown' || $this->filter === 'conversion_rate') {
            return 'bar';
        }

        return 'line';
    }
}
