<?php

namespace App\Filament\Resources\ModuleResource\Pages;

use App\Filament\Resources\ModuleResource;
use App\Models\Module;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Modul Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make(app()->getLocale() === 'id' ? 'Semua Modul' : 'All Modules')
                ->badge(Module::query()->count())
                ->badgeColor('gray'),
        ];

        $categories = Module::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        foreach ($categories as $cat) {
            $tabs[$cat] = Tab::make($cat)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', $cat))
                ->badge(Module::query()->where('category', $cat)->count())
                ->badgeColor('info');
        }

        return $tabs;
    }
}
