<?php

namespace App\Filament\Resources\DomainRenewalResource\Pages;

use App\Filament\Resources\DomainRenewalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainRenewal extends CreateRecord
{
    protected static string $resource = DomainRenewalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
