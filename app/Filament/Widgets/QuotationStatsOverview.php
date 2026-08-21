<?php

namespace App\Filament\Widgets;

use App\Models\Module;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class QuotationStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('Admin');
        $isIndo = app()->getLocale() === 'id';

        $query = Project::query();
        if (! $isAdmin && $user) {
            $query->where('user_id', $user->id);
        }

        $totalProjects = (clone $query)->count();
        $generatedProjects = (clone $query)->where('status', 'Generated')->count();
        $draftProjects = (clone $query)->where('status', 'Draft')->count();

        // Calculate total valuation in IDR
        $totalValuationIdr = (float) (clone $query)->sum('grand_total');

        $totalModules = Module::count();

        return [
            Stat::make(
                label: $isIndo ? 'Total Penawaran' : 'Total Quotations',
                value: $totalProjects . ' ' . ($isIndo ? 'Dokumen' : 'Docs')
            )
                ->description($isIndo ? 'Total kalkulasi quotation' : 'Total created quotations')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary')
                ->chart([3, 5, 8, 6, 9, 12, max($totalProjects, 1)]),

            Stat::make(
                label: $isIndo ? 'Penawaran Generated' : 'Generated Quotations',
                value: $generatedProjects . ' ' . ($isIndo ? 'Siap Cetak' : 'Ready')
            )
                ->description($isIndo ? 'Nota siap dikirim ke klien' : 'Ready for client delivery')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([1, 2, 4, 3, 7, max($generatedProjects, 1)]),

            Stat::make(
                label: $isIndo ? 'Akumulasi Nilai Estimasi (Rp)' : 'Total Valuation (Rp)',
                value: Number::currency($totalValuationIdr, 'IDR', 'id')
            )
                ->description($isIndo ? 'Estimasi nilai portofolio' : 'Estimated portfolio value')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([10, 25, 45, 60, 80, 100]),

            $isAdmin ? Stat::make(
                label: $isIndo ? 'Katalog Modul Standar' : 'Standard Modules',
                value: $totalModules . ' ' . ($isIndo ? 'Fitur' : 'Features')
            )
                ->description($isIndo ? 'Template fitur master data' : 'Master standardized modules')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info') : Stat::make(
                label: $isIndo ? 'Draft Dalam Proses' : 'Draft Quotations',
                value: $draftProjects . ' ' . ($isIndo ? 'Draft' : 'Drafts')
            )
                ->description($isIndo ? 'Menunggu finalisasi' : 'Pending finalization')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('danger'),
        ];
    }
}
