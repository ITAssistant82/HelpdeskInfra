<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $recordTitleAttribute = 'id';
    public function isReadOnly(): bool
    {
        return! auth()->user()->isStaff();
    }
    public function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\Textarea::make('content')->required()->columnSpanFull(), Forms\Components\Toggle::make('is_internal')->label('Internal note (hanya staff)')->visible(fn () => auth()->user()?->isStaff() ?? false), ]);
    }
    public function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('user.name')->label('Dari')->badge()->color(fn ($record) => $record && $record->user && $record->user->isStaff() ? 'warning' : 'gray')->formatStateUsing(fn ($state, $record) => $record && $record->user && $record->user->isStaff() ? 'Admin: ' . $state : $state), Tables\Columns\TextColumn::make('content')->html()->limit(120), Tables\Columns\TextColumn::make('is_internal')->label('Internal')->badge()->color('danger')->visible(fn () => auth()->user()?->isStaff() ?? false), Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i'), ])->defaultSort('created_at', 'desc')->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->when(! auth()->user()?->isStaff(), fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('is_internal', false), ))->headerActions([Actions\CreateAction::make()->visible(fn () => auth()->user()?->isStaff() ?? false)->mutateFormDataUsing(fn (array $data) => [... $data, 'user_id' => auth()->id(), ]), ])->actions([Actions\DeleteAction::make()->visible(fn () => auth()->user()?->isStaff() ?? false), ]);
    }
}
