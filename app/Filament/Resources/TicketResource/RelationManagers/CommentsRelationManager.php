<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $recordTitleAttribute = 'id';

    public function isReadOnly(): bool
    {
        return ! Auth::user()->isStaff();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\Textarea::make('content')->required()->columnSpanFull(), Forms\Components\Toggle::make('is_internal')->label('Internal note (hanya staff)')->visible(fn () => Auth::user()?->isStaff() ?? false)]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('user.name')->label('Dari')->badge()->color(fn ($record) => $record && $record->user && $record->user->isStaff() ? 'warning' : 'gray')->formatStateUsing(fn ($state, $record) => $record && $record->user && $record->user->isStaff() ? 'Admin: '.$state : $state), Tables\Columns\TextColumn::make('content')->html()->limit(120), Tables\Columns\TextColumn::make('is_internal')->label('Internal')->badge()->color('danger')->visible(fn () => Auth::user()?->isStaff() ?? false), Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')])->defaultSort('created_at', 'desc')->modifyQueryUsing(fn (Builder $query) => $query->when(! Auth::user()?->isStaff(), fn (Builder $q) => $q->where('is_internal', false)))->headerActions([Actions\CreateAction::make()->visible(fn () => Auth::user()?->isStaff() ?? false)->mutateFormDataUsing(fn (array $data) => [...$data, 'user_id' => Auth::id()])])->actions([Actions\DeleteAction::make()->visible(fn () => Auth::user()?->isStaff() ?? false)]);
    }
}
