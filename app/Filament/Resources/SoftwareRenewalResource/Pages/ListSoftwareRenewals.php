<?php

namespace App\Filament\Resources\SoftwareRenewalResource\Pages;

use App\Filament\Resources\SoftwareRenewalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSoftwareRenewals extends ListRecords
{
    protected static string $resource = SoftwareRenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
