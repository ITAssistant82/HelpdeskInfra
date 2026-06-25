<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $recordTitleAttribute = 'id';
    protected static bool $isLazy = false;

    public static function canViewForRecord($ownerRecord, ?string $pageClass = null): bool
    {
        if (! auth()->user()?->isStaff()) {
            return false;
        }
        return parent::canViewForRecord($ownerRecord, $pageClass ?? static::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'assigned' => 'primary',
                        'escalated' => 'danger',
                        'solved' => 'success',
                        'closed' => 'gray',
                        'reopened' => 'warning',
                        'rejected' => 'danger',
                        'status_updated' => 'info',
                        'updated' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'Dibuat',
                        'assigned' => 'Ditugaskan',
                        'escalated' => 'Eskalasi',
                        'solved' => 'Diselesaikan',
                        'closed' => 'Ditutup',
                        'reopened' => 'Dibuka Kembali',
                        'rejected' => 'Ditolak',
                        'status_updated' => 'Perubahan Status',
                        'updated' => 'Diubah',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Detail')
                    ->html()
                    ->wrap()
                    ->extraAttributes(['class' => 'max-w-md']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
