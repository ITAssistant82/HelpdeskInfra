<?php

namespace App\Filament\Support;

use App\Models\AcademicAsset;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class AcademicAssetResource extends Resource
{
    protected static ?string $model = AcademicAsset::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Aset Management';

    protected static ?string $navigationParentItem = 'Akademik';

    protected static string $roomType;

    public static function getRoomType(): string
    {
        return static::$roomType;
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_academic_asset'));
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('create_academic_asset'));
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('update_academic_asset'));
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('delete_academic_asset'));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('room_type', static::$roomType);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->schema([
            Schemas\Components\Section::make('Informasi Penempatan')->columns(3)->schema([
                Forms\Components\Select::make('location')->label('Lokasi')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak'])->required(),
                Forms\Components\TextInput::make('building')->label('Gedung')->placeholder('Contoh: Gedung A')->required()->maxLength(255),
                Forms\Components\TextInput::make('room')->label('Ruangan')->placeholder(static::$roomType === 'lab' ? 'Contoh: Lab Komputer 1' : 'Contoh: Kelas 301')->required()->maxLength(255),
            ]),
            Schemas\Components\Section::make('Informasi Perangkat')->columns(2)->schema([
                Forms\Components\TextInput::make('asset_code')->label('Asset Code')->required()->unique(ignoreRecord: true)->default(function () {
                    $lastCode = DB::table('academic_assets')->orderByDesc('id')->value('asset_code');
                    if ($lastCode && preg_match('/AKD-(\d+)$/', $lastCode, $matches)) {
                        return 'AKD-'.str_pad((int) $matches[1] + 1, 5, '0', STR_PAD_LEFT);
                    }

                    return 'AKD-00001';
                }),
                Forms\Components\Select::make('asset_type')->label('Jenis Perangkat')->options([
                    'PC' => 'PC',
                    'Maxhub' => 'Maxhub',
                    'Proyektor' => 'Proyektor',
                    'Monitor' => 'Monitor / TV',
                    'Printer' => 'Printer',
                    'Lainnya' => 'Lainnya',
                ])->searchable()->required(),
                Forms\Components\TextInput::make('brand')->label('Brand')->required(),
                Forms\Components\TextInput::make('model')->label('Model')->required(),
                Forms\Components\TextInput::make('serial_number')->label('Serial Number')->unique(ignoreRecord: true)->nullable(),
                Forms\Components\TextInput::make('tahun_pembelian')->label('Tahun Pembelian')->numeric()->minValue(1990)->maxValue(now()->year)->nullable(),
            ]),
            Schemas\Components\Section::make('Spesifikasi Hardware')->description('Opsional, terutama untuk perangkat PC')->columns(2)->schema([
                Forms\Components\TextInput::make('os')->label('Operating System')->nullable(),
                Forms\Components\TextInput::make('processor')->label('Processor')->nullable(),
                Forms\Components\TextInput::make('mainboard')->label('Mainboard')->nullable(),
                Forms\Components\TextInput::make('memory_gb')->label('RAM (GB)')->numeric()->step(0.25)->nullable(),
                Forms\Components\TextInput::make('hard_drive_gb')->label('Storage (GB)')->numeric()->step(1)->nullable(),
                Forms\Components\TextInput::make('monitor')->label('Monitor')->nullable(),
            ]),
            Schemas\Components\Section::make('Status & Catatan')->columns(2)->schema([
                Forms\Components\Select::make('condition')->label('Kondisi')->options(['Baik' => 'Baik', 'Perlu Perawatan' => 'Perlu Perawatan', 'Rusak' => 'Rusak'])->default('Baik')->required(),
                Forms\Components\Select::make('status')->label('Status')->options(['Digunakan' => 'Digunakan', 'Cadangan' => 'Cadangan', 'Perbaikan' => 'Perbaikan', 'Tidak Digunakan' => 'Tidak Digunakan'])->default('Digunakan')->required(),
                Forms\Components\Textarea::make('notes')->label('Catatan')->rows(4)->columnSpanFull()->nullable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\TextColumn::make('asset_code')->label('Asset Code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('asset_type')->label('Jenis Perangkat')->badge()->searchable()->sortable(),
            Tables\Columns\TextColumn::make('brand')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('model')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('serial_number')->label('Serial Number')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('location')->label('Lokasi')->badge()->color(fn ($state) => $state === 'BSD' ? 'info' : 'warning')->sortable(),
            Tables\Columns\TextColumn::make('building')->label('Gedung')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('room')->label('Ruangan')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('condition')->label('Kondisi')->badge()->color(fn ($state) => match ($state) { 'Baik' => 'success', 'Perlu Perawatan' => 'warning', 'Rusak' => 'danger', default => 'gray' })->sortable(),
            Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn ($state) => match ($state) { 'Digunakan' => 'success', 'Cadangan' => 'info', 'Perbaikan' => 'warning', 'Tidak Digunakan' => 'gray', default => 'gray' })->sortable(),
            Tables\Columns\TextColumn::make('os')->label('OS')->searchable()->sortable()->placeholder('-'),
            Tables\Columns\TextColumn::make('processor')->label('Processor')->searchable()->sortable()->placeholder('-'),
            Tables\Columns\TextColumn::make('mainboard')->label('Mainboard')->searchable()->sortable()->placeholder('-'),
            Tables\Columns\TextColumn::make('memory_gb')->label('RAM (GB)')->numeric(decimalPlaces: 2)->sortable()->placeholder('-'),
            Tables\Columns\TextColumn::make('hard_drive_gb')->label('Storage (GB)')->numeric(decimalPlaces: 0)->sortable()->placeholder('-'),
            Tables\Columns\TextColumn::make('monitor')->label('Monitor')->searchable()->sortable()->placeholder('-'),
            Tables\Columns\TextColumn::make('tahun_pembelian')->label('Tahun Beli')->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('usia')->label('Usia')->toggleable(),
            Tables\Columns\TextColumn::make('notes')->label('Catatan')->wrap()->placeholder('-'),
            Tables\Columns\TextColumn::make('created_at')->label('Ditambahkan')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('location')->label('Lokasi')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak']),
            Tables\Filters\SelectFilter::make('asset_type')->label('Jenis Perangkat')->options(['PC' => 'PC', 'Maxhub' => 'Maxhub', 'Proyektor' => 'Proyektor', 'Monitor' => 'Monitor / TV', 'Printer' => 'Printer', 'Lainnya' => 'Lainnya']),
            Tables\Filters\SelectFilter::make('condition')->label('Kondisi')->options(['Baik' => 'Baik', 'Perlu Perawatan' => 'Perlu Perawatan', 'Rusak' => 'Rusak']),
            Tables\Filters\SelectFilter::make('status')->label('Status')->options(['Digunakan' => 'Digunakan', 'Cadangan' => 'Cadangan', 'Perbaikan' => 'Perbaikan', 'Tidak Digunakan' => 'Tidak Digunakan']),
        ])->actions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ])->bulkActions([
            Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }
}
