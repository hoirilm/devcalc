<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Penawaran Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(app()->getLocale() === 'id' ? 'Semua Penawaran' : 'All Quotations')
                ->badge(Project::query()->count())
                ->badgeColor('gray'),

            'generated' => Tab::make(app()->getLocale() === 'id' ? 'Resmi (Generated)' : 'Official (Generated)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Generated'))
                ->badge(Project::query()->where('status', 'Generated')->count())
                ->badgeColor('success'),

            'draft' => Tab::make(app()->getLocale() === 'id' ? 'Draft Berjalan' : 'Drafts')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Draft'))
                ->badge(Project::query()->where('status', 'Draft')->count())
                ->badgeColor('warning'),

            'subscription' => Tab::make(app()->getLocale() === 'id' ? 'Langganan (SaaS)' : 'Subscriptions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('billing_type', 'subscription'))
                ->badge(Project::query()->where('billing_type', 'subscription')->count())
                ->badgeColor('info'),

            'addendum' => Tab::make(app()->getLocale() === 'id' ? 'Adendum' : 'Addendums')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('quotation_type', 'addendum'))
                ->badge(Project::query()->where('quotation_type', 'addendum')->count())
                ->badgeColor('primary'),
        ];
    }
}
