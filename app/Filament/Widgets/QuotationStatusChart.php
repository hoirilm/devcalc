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
            ? 'Distribusi Status Penawaran'
            : 'Quotations by Status Distribution';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('Admin');

        $query = Project::query();
        if (! $isAdmin && $user) {
            $query->where('user_id', $user->id);
        }

        $draftCount = (clone $query)->where('status', 'Draft')->count();
        $generatedCount = (clone $query)->where('status', 'Generated')->count();

        $counts = [$draftCount, $generatedCount];

        return [
            'datasets' => [
                [
                    'label' => app()->getLocale() === 'id' ? 'Jumlah Dokumen' : 'Document Count',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#f59e0b', // Amber for Draft
                        '#10b981', // Emerald for Generated
                    ],
                ],
            ],
            'labels' => [
                app()->getLocale() === 'id' ? 'Draft (Proses)' : 'Draft (In Progress)',
                app()->getLocale() === 'id' ? 'Generated (Siap Cetak)' : 'Generated (Ready)',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
