<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $title = 'Asset Perangkat';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\TextInput::make('asset_code')->label('Asset Code')->required()->unique(ignoreRecord: true), Forms\Components\Select::make('asset_type')->label('Jenis Perangkat')->options(['Laptop' => 'Laptop', 'PC' => 'PC'])->required(), Forms\Components\TextInput::make('brand')->required(), Forms\Components\TextInput::make('model')->required(), Forms\Components\TextInput::make('serial_number')->label('Serial Number')->unique(ignoreRecord: true)->nullable(), Forms\Components\TextInput::make('os')->label('Operating System')->nullable(), Forms\Components\TextInput::make('processor')->label('Processor')->nullable(), Forms\Components\TextInput::make('mainboard')->label('Mainboard')->nullable(), Forms\Components\TextInput::make('memory_gb')->label('RAM (GB)')->numeric()->step(0.25)->nullable(), Forms\Components\TextInput::make('hard_drive_gb')->label('Storage (GB)')->numeric()->nullable(), Forms\Components\TextInput::make('monitor')->label('Monitor')->nullable(), Forms\Components\TextInput::make('tahun_pembelian')->label('Tahun Pembelian')->numeric()->nullable(), Forms\Components\Select::make('condition')->label('Kondisi')->options(['Baik' => 'Baik', 'Perlu Perawatan' => 'Perlu Perawatan', 'Rusak' => 'Rusak'])->required(), Forms\Components\DatePicker::make('assigned_at')->label('Tanggal Diberikan')->native(false), Forms\Components\Textarea::make('notes')->label('Catatan')->rows(4)->columnSpanFull()]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('asset_code')->searchable()->sortable(), Tables\Columns\TextColumn::make('asset_type')->badge()->color(fn ($state) => match ($state) {
            'Laptop' => 'warning', 'PC' => 'info', default => 'gray'
        }), Tables\Columns\TextColumn::make('brand')->searchable(), Tables\Columns\TextColumn::make('model')->searchable(), Tables\Columns\TextColumn::make('os')->label('OS')->searchable(), Tables\Columns\TextColumn::make('processor')->label('Processor')->searchable(), Tables\Columns\TextColumn::make('memory_gb')->label('RAM (GB)')->numeric(decimalPlaces: 1), Tables\Columns\TextColumn::make('condition')->badge()->color(fn ($state) => match ($state) {
            'Baik' => 'success', 'Perlu Perawatan' => 'warning', 'Rusak' => 'danger', default => 'gray'
        }), Tables\Columns\TextColumn::make('assigned_at')->label('Diberikan')->date()])->headerActions([
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
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
                }),
        ])->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }
}
