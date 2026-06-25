<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class UserTicketStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Tiket Saya';
    protected static ?int $sort = 2;
    public static function canView(): bool
    {
        return! auth()->user()?->isStaff();
    }
    protected function getData(): array
    {
        $userId = auth()->id();
        $statuses = Ticket::where('requester_id', $userId)->selectRaw('status, count(*) AS total')->groupBy('status')->pluck('total', 'status')->toArray();
        $colors = ['New' => '#6b7280', 'Assigned' => '#3b82f6', 'In Progress' => '#f59e0b', 'Pending Approval' => '#8b5cf6', 'Reopened' => '#ef4444', 'Solved' => '#22c55e', 'Closed' => '#10b981', 'Rejected/Out of Scope' => '#9ca3af', ];
        $labels = array_keys($statuses);
        $data = array_values($statuses);
        $chartColors = array_map(fn ($s) => $colors [$s] ?? '#6b7280', $labels);
        return ['labels' => $labels, 'datasets' => [['label' => 'Tiket', 'data' => $data, 'backgroundColor' => $chartColors, 'borderColor' => $chartColors, ], ], ];
    }
    protected function getType(): string
    {
        return 'doughnut';
    }
}
