<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SummonsRelationManager extends RelationManager
{
    protected static string $relationship = 'helpers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isLazy = false;

    public static function canViewForRecord($ownerRecord, ?string $pageClass = null): bool
    {
        if (! Auth::user()?->isStaff()) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass ?? static::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pivot_added_by_user.name')
                    ->label('Ditambahkan Oleh')
                    ->state(fn ($record) => User::query()->find($record->pivot->added_by)?->name ?? '-'),
                Tables\Columns\TextColumn::make('pivot_created_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->state(fn ($record) => $record->pivot->created_at),
            ])
            ->headerActions([
                Action::make('summon')
                    ->label('Summon')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Cari User')
                            ->options(function () {
                                return User::whereHas('roles', fn ($q) => $q->where('name', '!=', 'user'))
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [
                                        $u->id => $u->name.' ('.($u->roles->pluck('name')->implode(', ')).')',
                                    ]);
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $user = User::query()->find($data['user_id']);
                        if ($user) {
                            $this->getOwnerRecord()->addHelper($user);
                        }
                    }),
            ])
            ->actions([
                Action::make('remove')
                    ->label('Hapus')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $this->getOwnerRecord()->removeHelper($record);
                    }),
            ])
            ->defaultSort('pivot_created_at', 'desc');
    }
}
