<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketCategoryResource\Pages;
use App\Filament\Resources\TicketCategoryResource\RelationManagers;
use App\Models\TicketCategory;
use Filament\Forms;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class TicketCategoryResource extends Resource
{
    protected static ?string $model = TicketCategory::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Tiket';
    protected static string|\UnitEnum|null $navigationGroup = 'Ticketing';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'sub_category';
    public static function canAccess(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    public static function canViewAny(): bool
    {
        return static::canAccess();
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_ticket_category') ?? false;
    }
    public static function canEdit($record): bool
    {
        return auth()->user()?->can('view_any_ticket_category') ?? false;
    }
    public static function canDelete($record): bool
    {
        return auth()->user()?->can('view_any_ticket_category') ?? false;
    }
    public static function getCreateAuthorizationResponse(): Response
    {
        return static::canCreate() ? Response::allow() : Response::deny();
    }
    public static function getEditAuthorizationResponse(Model $record): Response
    {
        return static::canEdit($record) ? Response::allow() : Response::deny();
    }
    public static function getDeleteAuthorizationResponse(Model $record): Response
    {
        return static::canDelete($record) ? Response::allow() : Response::deny();
    }
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\Select::make('type')->options(['Incident' => 'Incident', 'Service Request' => 'Service Request', ])->required(), Forms\Components\TextInput::make('main_category')->required(), Forms\Components\TextInput::make('sub_category')->required(), Forms\Components\Textarea::make('description')->columnSpanFull(), Forms\Components\Toggle::make('is_active')->default(true), Forms\Components\Toggle::make('needs_approval')->label('Butuh Persetujuan?')->helperText('Tiket dengan kategori ini otomatis memerlukan approval'), Forms\Components\Select::make('assigned_team')->label('Tim Penanganan')->options(['helpdesk_l1' => 'Helpdesk L1', 'it_infra_l1' => 'IT Infra L1', 'it_infra_l2' => 'IT Infrastructure L2', 'network_team' => 'Network Team', 'm365_team' => 'M365 Team', 'security_soc' => 'Security SOC', ])->nullable(), ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('type')->badge()->color(fn ($state) => $state === 'Incident' ? 'danger' : 'warning'), Tables\Columns\TextColumn::make('main_category')->searchable()->sortable(), Tables\Columns\TextColumn::make('sub_category')->searchable()->sortable(), Tables\Columns\IconColumn::make('is_active')->boolean(), Tables\Columns\IconColumn::make('needs_approval')->label('Approval')->boolean()->trueIcon('heroicon-o-check-badge')->falseIcon('heroicon-o-x-mark'), Tables\Columns\TextColumn::make('assigned_team')->label('Tim')->formatStateUsing(fn ($state) => match ($state) {
            'helpdesk_l1' => 'Helpdesk L1', 'it_infra_l1' => 'IT Infra L1', 'it_infra_l2' => 'IT Infra L2', 'network_team' => 'Network', 'm365_team' => 'M365', 'security_soc' => 'Security', default => $state ?? '-',
        }), ])->filters([Tables\Filters\SelectFilter::make('type')->options(['Incident' => 'Incident', 'Service Request' => 'Service Request', ]), Tables\Filters\SelectFilter::make('main_category')->options(fn () => \App\Models\TicketCategory::distinct()->pluck('main_category', 'main_category')->toArray()), ])->actions([Actions\EditAction::make(), ])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make(), ]), ]);
    }
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return ['index' => Pages\ListTicketCategories::route('/'), 'create' => Pages\CreateTicketCategory::route('/create'), 'edit' => Pages\EditTicketCategory::route('/{record}/edit'), ];
    }
}
