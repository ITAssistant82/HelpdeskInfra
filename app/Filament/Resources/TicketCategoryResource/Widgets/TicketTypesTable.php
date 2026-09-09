<?php

namespace App\Filament\Resources\TicketCategoryResource\Widgets;

use App\Filament\Resources\TicketTypeResource;
use App\Models\TicketType;
use Filament\Actions;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Livewire\Attributes\On;

class TicketTypesTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return TicketTypeResource::canViewAny();
    }

    public function boot(): void
    {
        abort_unless(static::canView(), 403);
    }

    #[On('ticket-type-created')]
    public function refreshTicketTypes(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return TicketTypeResource::table($table)
            ->query(TicketType::query())
            ->heading('Tipe Tiket')
            ->description('Edit tipe untuk mengatur status aktif, warna, dan akses khusus IT.')
            ->queryStringIdentifier('ticketTypes')
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->actions([
                Actions\EditAction::make()
                    ->label('Edit Tipe')
                    ->authorize(fn (TicketType $record): bool => TicketTypeResource::canEdit($record))
                    ->schema(fn (Schema $schema): Schema => TicketTypeResource::form($schema)),
                Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->authorize(fn (TicketType $record): bool => TicketTypeResource::canDelete($record))
                    ->before(fn (Actions\DeleteAction $action, TicketType $record) => TicketTypeResource::preventDeletingUsedType($action, $record)),
            ]);
    }
}
