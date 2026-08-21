<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestProjectsTable extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return app()->getLocale() === 'id'
            ? 'Penawaran Terbaru & Aksi Cepat'
            : 'Recent Quotations & Quick Actions';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProjectResource::getEloquentQuery()->latest()->limit(6)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#ID')
                    ->formatStateUsing(fn ($state) => 'QUO-' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->sortable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label(app()->getLocale() === 'id' ? 'Nama Klien' : 'Client Name')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(app()->getLocale() === 'id' ? 'Estimator' : 'Estimator')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->formatStateUsing(fn ($record) => \Illuminate\Support\Number::currency(
                        $record->grand_total,
                        'IDR',
                        'id'
                    ))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Generated' => 'success',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(app()->getLocale() === 'id' ? 'Tanggal' : 'Date')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('print_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Project $record): string => route('projects.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('edit')
                    ->label(app()->getLocale() === 'id' ? 'Buka' : 'Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (Project $record): string => ProjectResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create_project')
                    ->label(app()->getLocale() === 'id' ? 'Buat Penawaran Baru' : 'Create New Quotation')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->button()
                    ->url(fn (): string => ProjectResource::getUrl('create')),
            ])
            ->paginated(false);
    }
}
