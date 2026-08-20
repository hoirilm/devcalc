<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class QuotationStatusChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public function getHeading(): string
    {
        return app()->getLocale() === 'id'
            ? 'Distribusi Mata Uang Penawaran'
            : 'Quotations by Currency Distribution';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('Admin');

        $query = Project::query();
        if (! $isAdmin && $user) {
            $query->where('user_id', $user->id);
        }

        $idrCount = (clone $query)->where('currency_code', 'IDR')->count();
        $usdCount = (clone $query)->where('currency_code', 'USD')->count();
        $eurCount = (clone $query)->where('currency_code', 'EUR')->count();
        $sgdCount = (clone $query)->where('currency_code', 'SGD')->count();

        // If all 0, provide sample baseline
        $counts = [$idrCount, $usdCount, $eurCount, $sgdCount];

        return [
            'datasets' => [
                [
                    'label' => app()->getLocale() === 'id' ? 'Jumlah Proyek' : 'Project Count',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3b82f6', // Blue for IDR
                        '#10b981', // Green for USD
                        '#f59e0b', // Amber for EUR
                        '#6366f1', // Indigo for SGD
                    ],
                ],
            ],
            'labels' => ['IDR (Rupiah)', 'USD (Dollar)', 'EUR (Euro)', 'SGD (Singapore)'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
