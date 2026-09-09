<?php

namespace App\Filament\Resources\TicketCategoryResource\Pages;

use App\Filament\Resources\TicketCategoryResource;
use App\Filament\Resources\TicketTypeResource;
use App\Models\TicketType;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTicketCategories extends ListRecords
{
    protected static string $resource = TicketCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manageTicketTypes')
                ->label('Kelola Tipe Tiket')
                ->icon('heroicon-o-list-bullet')
                ->color('info')
                ->authorize(fn (): bool => Auth::user()?->isITStaff() ?? false)
                ->visible(fn (): bool => Auth::user()?->isITStaff() ?? false)
                ->url(fn (): string => TicketTypeResource::getUrl('index')),
            Actions\Action::make('createTicketType')
                ->label('Tambah Tipe Tiket')
                ->icon('heroicon-o-plus-circle')
                ->color('gray')
                ->authorize(fn (): bool => Auth::user()?->isITStaff() ?? false)
                ->visible(fn (): bool => Auth::user()?->isITStaff() ?? false)
                ->modalHeading('Tambah Tipe Tiket')
                ->modalSubmitActionLabel('Simpan Tipe')
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Tipe')
                        ->placeholder('Contoh: Change Request')
                        ->required()
                        ->maxLength(255)
                        ->unique(TicketType::class, 'name'),
                    Forms\Components\Select::make('color')
                        ->label('Warna Badge')
                        ->options(TicketType::COLORS)
                        ->default(fn (): string => TicketType::suggestedColor())
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(3),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                    Forms\Components\Toggle::make('it_only')
                        ->label('Khusus IT')
                        ->helperText('Jika aktif, tipe ini hanya muncul untuk role IT.')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    TicketType::create($data);

                    Notification::make()
                        ->title('Tipe tiket berhasil ditambahkan')
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('Tambah Kategori')
                ->url(fn (): string => static::getResource()::getUrl('create')),
        ];
    }
}
