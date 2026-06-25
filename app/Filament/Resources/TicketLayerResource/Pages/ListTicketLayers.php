<?php

namespace App\Filament\Resources\TicketLayerResource\Pages;

use App\Filament\Resources\TicketLayerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListTicketLayers extends ListRecords
{
    protected static string $resource = TicketLayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('run_auto_escalation')
                ->label('Jalankan Auto Eskalasi')
                ->icon('heroicon-o-rocket-launch')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $exitCode = Artisan::call('tickets:auto-escalate');
                    $output = Artisan::output();

                    if ($exitCode === 0) {
                        Notification::make()
                            ->success()
                            ->title('Auto Eskalasi Selesai')
                            ->body($output)
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('Auto Eskalasi Gagal')
                            ->body($output)
                            ->send();
                    }
                }),
        ];
    }
}
