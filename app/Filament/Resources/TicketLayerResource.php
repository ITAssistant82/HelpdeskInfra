<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketLayerResource\Pages;
use App\Models\TicketLayer;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class TicketLayerResource extends Resource
{
    protected static ?string $model = TicketLayer::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Ticket Layers';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_ticket_layer'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->schema([
            Forms\Components\TextInput::make('name')->label('Nama Layer')->required(),
            Forms\Components\TextInput::make('role_name')->label('Role')->required()->helperText('Nama role Spatie, contoh: it_infra_l1'),
            Forms\Components\TextInput::make('level')->label('Level')->required()->numeric()->minValue(1),
            Forms\Components\TextInput::make('escalation_hours')->label('Auto Eskalasi (jam)')->numeric()->minValue(1)->nullable()->helperText('Kosongkan jika tidak ada auto eskalasi'),
            Forms\Components\TextInput::make('team_key')->label('Team Key')->required()->helperText('Contoh: it_infra, helpdesk, network'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama Layer')->searchable(),
            Tables\Columns\TextColumn::make('role_name')->label('Role')->badge(),
            Tables\Columns\TextColumn::make('level')->label('Level')->sortable(),
            Tables\Columns\TextColumn::make('escalation_hours')->label('Auto Eskalasi')->formatStateUsing(fn ($state) => $state ? "{$state} jam" : '-')->sortable(),
            Tables\Columns\TextColumn::make('team_key')->label('Tim')->badge()->color('info'),
        ])->actions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ])->bulkActions([
            Actions\BulkActionGroup::make([
                Actions\DeleteBulkAction::make(),
            ]),
        ])->defaultSort('team_key', 'level');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketLayers::route('/'),
            'create' => Pages\CreateTicketLayer::route('/create'),
            'edit' => Pages\EditTicketLayer::route('/{record}/edit'),
        ];
    }
}
