<?php

namespace App\Filament\Resources\SoftwareRenewalResource\Pages;

use App\Filament\Resources\SoftwareRenewalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSoftwareRenewal extends CreateRecord
{
    protected static string $resource = SoftwareRenewalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
