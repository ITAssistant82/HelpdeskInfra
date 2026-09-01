<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Exports\TicketsExport;
use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Tiket')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->authorize(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false)
                ->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false)
                ->action(fn () => Excel::download(new TicketsExport, 'tickets-'.now()->format('Y-m-d-His').'.xlsx')),
            Actions\CreateAction::make()->url(fn (): string => static::getResource()::getUrl('create')),
        ];
    }
}
