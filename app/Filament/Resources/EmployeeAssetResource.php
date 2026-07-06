<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeAssetResource\Pages;
use App\Models\Employee;
use App\Models\EmployeeAsset;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeAssetResource extends Resource
{
    protected static ?string $model = EmployeeAsset::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Asset Perangkat';

    protected static string|\UnitEnum|null $navigationGroup = 'Employees';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_employee_asset'));
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('create_employee_asset'));
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_employee_asset'));
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_employee_asset'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->schema([Schemas\Components\Section::make('Informasi Perangkat')->columns(2)->schema([Forms\Components\Select::make('employee_id')->label('Employee')->relationship('employee', 'nik')->getOptionLabelFromRecordUsing(fn (Employee $record): string => $record->nik.' - '.$record->full_name)->searchable()->preload()->required()->columnSpanFull(), Forms\Components\TextInput::make('asset_code')->label('Asset Code')->required()->unique(ignoreRecord: true)->default(function () {
            $lastCode = DB::table('employee_assets')->orderBy('id', 'desc')->value('asset_code');
            if ($lastCode) {
                preg_match('/(\d+)$/', $lastCode, $matches);
                if ($matches) {
                    $num = (int) $matches[1];
                    $prefix = substr($lastCode, 0, -strlen($matches[1]));

                    return $prefix.str_pad($num + 1, strlen($matches[1]), '0', STR_PAD_LEFT);
                }
            }

            return 'ASIT000001';
        }), Forms\Components\Select::make('asset_type')->label('Jenis Perangkat')->options(['Laptop' => 'Laptop', 'PC' => 'PC', 'Smartphone' => 'Smartphone', 'Pribadi' => 'Pribadi'])->required(), Forms\Components\TextInput::make('brand')->label('Brand')->required(), Forms\Components\TextInput::make('model')->label('Model')->required(), Forms\Components\TextInput::make('serial_number')->label('Serial Number')->unique(ignoreRecord: true)->nullable(), ]), Schemas\Components\Section::make('Spesifikasi Hardware')->description('Detail teknis perangkat')->columns(2)->schema([Forms\Components\TextInput::make('os')->label('Operating System')->placeholder('e.g., Windows 11, Ubuntu 22.04')->nullable(), Forms\Components\TextInput::make('processor')->label('Processor')->placeholder('e.g., Intel Core i7')->nullable(), Forms\Components\TextInput::make('mainboard')->label('Mainboard')->nullable(), Forms\Components\TextInput::make('memory_gb')->label('RAM (GB)')->numeric()->step(0.25)->nullable(), Forms\Components\TextInput::make('hard_drive_gb')->label('Storage (GB)')->numeric()->step(1)->nullable(), Forms\Components\TextInput::make('monitor')->label('Monitor')->placeholder('e.g., 24 inch FHD')->nullable(), Forms\Components\TextInput::make('tahun_pembelian')->label('Tahun Pembelian')->numeric()->minValue(1990)->maxValue(now()->year)->nullable()]), Schemas\Components\Section::make('Status & Catatan')->columns(2)->schema([Forms\Components\Select::make('location')->label('Lokasi')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak', 'Mobile' => 'Mobile (Laptop)'])->nullable(), Forms\Components\Select::make('condition')->label('Kondisi')->options(['Baik' => 'Baik', 'Perlu Perawatan' => 'Perlu Perawatan', 'Rusak' => 'Rusak'])->required(), Forms\Components\DatePicker::make('assigned_at')->label('Tanggal Diberikan')->native(false), Forms\Components\Textarea::make('notes')->label('Catatan')->rows(4)->columnSpanFull()->nullable()]), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('asset_code')->searchable()->sortable(), Tables\Columns\TextColumn::make('employee.nik')->label('NIK')->searchable()->sortable(), Tables\Columns\TextColumn::make('employee.full_name')->label('Employee')->searchable()->sortable(), Tables\Columns\TextColumn::make('asset_type')->badge()->color(fn ($state) => match ($state) {
            'Laptop' => 'warning', 'PC' => 'info', 'Pribadi' => 'success', 'Smartphone' => 'danger', default => 'gray'
        })->sortable(), Tables\Columns\TextColumn::make('brand')->searchable()->sortable(), Tables\Columns\TextColumn::make('model')->searchable()->sortable(), Tables\Columns\TextColumn::make('location')->label('Lokasi')->badge()->color(fn ($state) => match ($state) {
            'BSD' => 'info', 'Cilandak' => 'warning', 'Mobile' => 'gray', default => 'gray'
        })->sortable(), Tables\Columns\TextColumn::make('os')->label('OS')->searchable()->sortable(), Tables\Columns\TextColumn::make('processor')->label('Processor')->searchable()->sortable(), Tables\Columns\TextColumn::make('memory_gb')->label('RAM (GB)')->sortable()->numeric(decimalPlaces: 1), Tables\Columns\TextColumn::make('hard_drive_gb')->label('Storage (GB)')->sortable()->numeric(decimalPlaces: 0), Tables\Columns\TextColumn::make('condition')->badge()->color(fn ($state) => match ($state) {
            'Baik' => 'success', 'Perlu Perawatan' => 'warning', 'Rusak' => 'danger', default => 'gray'
        })->sortable(), Tables\Columns\TextColumn::make('tahun_pembelian')->label('Tahun Beli')->sortable(), Tables\Columns\TextColumn::make('usia')->label('Usia Perangkat')->badge()->color(fn ($record) => $record->usia && (int) $record->tahun_pembelian && (now()->year - (int) $record->tahun_pembelian) >= 10 ? 'danger' : ((now()->year - (int) $record->tahun_pembelian) >= 5 ? 'warning' : 'success'))->sortable(query: fn ($query, $direction) => $query->orderBy('tahun_pembelian', $direction)), Tables\Columns\TextColumn::make('assigned_at')->label('Diberikan')->date()->sortable()])->filters([Tables\Filters\SelectFilter::make('asset_type')->label('Jenis Perangkat')->options(['Laptop' => 'Laptop', 'PC' => 'PC', 'Smartphone' => 'Smartphone', 'Pribadi' => 'Pribadi']), Tables\Filters\SelectFilter::make('location')->label('Lokasi')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak', 'Mobile' => 'Mobile']), Tables\Filters\SelectFilter::make('condition')->label('Kondisi')->options(['Baik' => 'Baik', 'Perlu Perawatan' => 'Perlu Perawatan', 'Rusak' => 'Rusak'])])->actions([Actions\Action::make('preview_barcode')->label('Preview Barcode')->icon('heroicon-o-qr-code')->color('info')->url(fn (EmployeeAsset $record): string => route('asset.barcode.preview', $record))->openUrlInNewTab(), Actions\Action::make('print_sticker')->label('Cetak Stiker')->icon('heroicon-o-tag')->color('danger')->url(fn (EmployeeAsset $record): string => route('asset.barcode.sticker', $record))->openUrlInNewTab(), Actions\EditAction::make()])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListEmployeeAssets::route('/'), 'create' => Pages\CreateEmployeeAsset::route('/create'), 'edit' => Pages\EditEmployeeAsset::route('/{record}/edit')];
    }
}
