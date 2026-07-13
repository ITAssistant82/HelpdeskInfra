<?php

namespace App\Filament\Widgets;

use App\Models\EmployeeAsset;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestassetsTable extends BaseWidget
{
    protected static ?string $heading = 'Asset Terbaru';
    protected static ?int $sort = 9;
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected int| string| array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table->query(Employeeasset::query()->with('employee')->latest()->limit(5))->columns([Tables\Columns\TextColumn::make('asset_code')->label('Kode')->searchable(), Tables\Columns\TextColumn::make('employee.nik')->label('NIK'), Tables\Columns\TextColumn::make('employee.full_name')->label('Pemilik'), Tables\Columns\TextColumn::make('asset_type')->label('Jenis')->badge()->color(fn ($state) => match ($state) { 'Laptop' => 'warning', 'PC' => 'info', 'Pribadi' => 'success', 'Smartphone' => 'danger', default => 'gray' }), Tables\Columns\TextColumn::make('brand')->label('Brand'), Tables\Columns\TextColumn::make('condition')->label('Kondisi')->badge()->color(fn ($state) => match ($state) { 'Baik' => 'success', 'Perlu Perawatan' => 'warning', 'Rusak' => 'danger', default => 'gray' }), Tables\Columns\TextColumn::make('created_at')->label('Ditambahkan')->dateTime(), ]);
    }
}
