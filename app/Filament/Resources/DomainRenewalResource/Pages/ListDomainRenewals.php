<?php

namespace App\Filament\Resources\DomainRenewalResource\Pages;

use App\Filament\Resources\DomainRenewalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDomainRenewals extends ListRecords
{
    protected static string $resource = DomainRenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
