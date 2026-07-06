<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TicketApprovalsRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketApprovals';

    protected static ?string $recordTitleAttribute = 'id';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table->heading('Progres Persetujuan')->columns([Tables\Columns\TextColumn::make('sequence_order')->label('Urutan')->badge()->color('gray'), Tables\Columns\TextColumn::make('approver.name')->label('Approver'), Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn ($state) => match ($state) {
            'approved' => 'success', 'rejected' => 'danger', default => 'gray',
        })->formatStateUsing(fn ($state) => match ($state) {
            'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => 'Menunggu',
        }), Tables\Columns\TextColumn::make('note')->label('Catatan')->limit(40), Tables\Columns\TextColumn::make('acted_at')->label('Waktu')->dateTime('d/m/Y H:i'), ])->defaultSort('sequence_order', 'asc')->paginated(false);
    }
}
