<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetStockResource\Pages;
use App\Models\AssetStock;
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

class AssetStockResource extends Resource
{
    protected static ?string $model = AssetStock::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Stock Asset';

    protected static string|\UnitEnum|null $navigationGroup = 'Employees';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->schema([
            Schemas\Components\Section::make('Informasi Asset')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('asset_code')
                        ->label('Asset Code')->required()->columnSpanFull()->default(function () {
                            $lastCode = DB::table('asset_stocks')->orderBy('id', 'desc')->value('asset_code');
                            if ($lastCode) {
                                preg_match('/(\d+)$/', $lastCode, $matches);
                                if ($matches) {
                                    $num = (int) $matches[1];
                                    $prefix = substr($lastCode, 0, -strlen($matches[1]));

                                    return $prefix.str_pad($num + 1, strlen($matches[1]), '0', STR_PAD_LEFT);
                                }
                            }

                            return 'STK-00001';
                        }),
                    Forms\Components\TextInput::make('asset_type')
                        ->label('Jenis Perangkat')->required(),
                    Forms\Components\TextInput::make('brand')
                        ->label('Brand')->required(),
                    Forms\Components\TextInput::make('model')
                        ->label('Model')->required(),
                    Forms\Components\TextInput::make('serial_number')
                        ->label('Serial Number')->nullable(),
                    Forms\Components\Select::make('condition')
                        ->label('Kondisi')
                        ->options([
                            'Baik' => 'Baik',
                            'Perlu Perawatan' => 'Perlu Perawatan',
                            'Rusak' => 'Rusak',
                        ])->required(),
                    Forms\Components\TextInput::make('os')
                        ->label('OS')->nullable(),
                    Forms\Components\TextInput::make('processor')
                        ->label('Processor')->nullable(),
                    Forms\Components\TextInput::make('mainboard')
                        ->label('Mainboard')->nullable(),
                    Forms\Components\TextInput::make('memory_gb')
                        ->label('RAM (GB)')->numeric()->nullable(),
                    Forms\Components\TextInput::make('hard_drive_gb')
                        ->label('Storage (GB)')->numeric()->nullable(),
                    Forms\Components\TextInput::make('monitor')
                        ->label('Monitor')->nullable(),
                    Forms\Components\TextInput::make('tahun_pembelian')
                        ->label('Tahun Pembelian')->nullable(),
                    Forms\Components\TextInput::make('location')
                        ->label('Lokasi')->nullable()->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan')->nullable()->columnSpanFull(),
                ]),
        ]);
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('view_any_asset_stock') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('create_asset_stock') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()?->can('view_any_asset_stock') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->can('view_any_asset_stock') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')->label('Asset Code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('asset_type')->label('Jenis Perangkat')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('brand')->label('Brand')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('model')->label('Model')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('serial_number')->label('S/N')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('condition')->label('Kondisi')->badge()->color(fn ($state) => match ($state) {
                    'Baik' => 'success',
                    'Perlu Perawatan' => 'warning',
                    'Rusak' => 'danger',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('location')->label('Lokasi')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Ditambahkan')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition')->options([
                    'Baik' => 'Baik',
                    'Perlu Perawatan' => 'Perlu Perawatan',
                    'Rusak' => 'Rusak',
                ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAssetStocks::route('/'), 'create' => Pages\CreateAssetStock::route('/create'), 'edit' => Pages\EditAssetStock::route('/{record}/edit')];
    }
}
