<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyActiveTickets extends BaseWidget
{
    protected int| string| array $columnSpan = 'full';
    public static function canView(): bool
    {
        return! auth()->user()?->isStaff();
    }
    public function table(Table $table): Table
    {
        return $table->query(Ticket::query()->with('category')->where('requester_id', auth()->id()))->heading('Tiket Saya')->poll('30s')->columns([Tables\Columns\TextColumn::make('ticket_number')->label('Ticket'), Tables\Columns\TextColumn::make('title')->searchable()->limit(40), Tables\Columns\TextColumn::make('category.main_category')->label('Kategori'), Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => match ($state) {
            'New' => 'gray', 'Assigned' => 'info', 'In Progress' => 'warning', 'Reopened' => 'danger', default => 'gray',
        }), Tables\Columns\TextColumn::make('updated_at')->dateTime('d/m/Y H:i')->label('Update'), ])->defaultSort('updated_at', 'desc')->paginated(false);
    }
}
