<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Exports\TicketsExport;
use App\Filament\Resources\TicketResource;
use App\Imports\TicketsImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
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
                ->authorize(fn (): bool => TicketResource::canManageImportsAndExports())
                ->visible(fn (): bool => TicketResource::canManageImportsAndExports())
                ->action(fn () => Excel::download(new TicketsExport, 'tickets-'.now()->format('Y-m-d-His').'.xlsx')),
            Actions\Action::make('import')
                ->label('Import Tiket')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->authorize(fn (): bool => TicketResource::canManageImportsAndExports())
                ->visible(fn (): bool => TicketResource::canManageImportsAndExports())
                ->modalDescription('Gunakan file hasil Export Tiket. Nomor tiket yang sudah ada akan diperbarui; baris tanpa nomor atau nomor yang tidak ditemukan akan dibuat sebagai tiket baru.')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->disk('local')
                        ->directory('ticket-imports')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    abort_unless(TicketResource::canManageImportsAndExports(), 403);

                    $import = new TicketsImport;
                    $relativePath = $data['file'];

                    try {
                        Excel::import($import, Storage::disk('local')->path($relativePath));
                    } finally {
                        Storage::disk('local')->delete($relativePath);
                    }

                    Notification::make()
                        ->title('Import tiket berhasil')
                        ->body("{$import->created} tiket dibuat dan {$import->updated} tiket diperbarui.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()->url(fn (): string => static::getResource()::getUrl('create')),
        ];
    }
}
