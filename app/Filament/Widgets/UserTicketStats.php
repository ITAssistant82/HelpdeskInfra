<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserTicketStats extends BaseWidget
{
    protected static ?int $sort = 1;
    public static function canView(): bool
    {
        return! auth()->user()?->isStaff();
    }
    protected function getStats(): array
    {
        $userId = auth()->id();
        $total = Ticket::where('requester_id', $userId)->count();
        $active = Ticket::where('requester_id', $userId)->whereNotIn('status', ['Solved', 'Closed', 'Rejected/Out of Scope'])->count();
        $doneThisMonth = Ticket::where('requester_id', $userId)->whereIn('status', ['Solved', 'Closed'])->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count();
        return [Stat::make('Total Tiket Saya', $total)->description('Seluruh tiket')->color('info'), Stat::make('Tiket Aktif', $active)->description('Belum selesai')->color('warning'), Stat::make('Selesai Bulan Ini', $doneThisMonth)->description(now()->format('F Y'))->color('success'), ];
    }
}
