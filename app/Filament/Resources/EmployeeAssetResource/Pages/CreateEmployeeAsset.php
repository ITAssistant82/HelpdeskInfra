<?php

namespace App\Filament\Resources\EmployeeAssetResource\Pages;

use App\Filament\Resources\EmployeeAssetResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateEmployeeAsset extends CreateRecord
{
    protected static string $resource = EmployeeAssetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['asset_code'])) {
            $lastCode = DB::table('employee_assets')
                ->orderBy('id', 'desc')
                ->value('asset_code');

            if ($lastCode) {
                preg_match('/(\d+)$/', $lastCode, $matches);
                if ($matches) {
                    $num = (int) $matches[1];
                    $prefix = substr($lastCode, 0, -strlen($matches[1]));
                    $data['asset_code'] = $prefix.str_pad($num + 1, strlen($matches[1]), '0', STR_PAD_LEFT);
                } else {
                    $data['asset_code'] = 'AST-00001';
                }
            } else {
                $data['asset_code'] = 'AST-00001';
            }
        }

        return $data;
    }
}
