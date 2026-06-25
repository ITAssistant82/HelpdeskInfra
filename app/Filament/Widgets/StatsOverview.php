<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getStats(): array
    {
        $overdueTickets = Ticket::whereNotIn('status', ['Solved', 'Closed', 'Rejected/Out of Scope'])->where('sla_deadline', '<', now())->count();
        $pendingApproval = Ticket::where('status', 'Pending Approval')->count();
        return [Stat::make('Total Tiket', Ticket::count())->description('Semua tiket')->color('info'), Stat::make('Tiket Baru', Ticket::where('status', 'New')->count())->description('Belum ditangani')->color('warning'), Stat::make('Dalam Progres', Ticket::whereIn('status', ['Assigned', 'In Progress'])->count())->color('primary'), Stat::make('Overdue SLA', $overdueTickets)->description('Melewati deadline')->color('danger'), Stat::make('Pending Approval', $pendingApproval)->color('warning'), Stat::make('Selesai', Ticket::whereIn('status', ['Solved', 'Closed'])->count())->color('success'), ];
    }
}
