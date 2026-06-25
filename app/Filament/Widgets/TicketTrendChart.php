<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketTrendChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected ?string $heading = 'Tiket per Bulan';
    protected function getData(): array
    {
        $data = Ticket::selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS bulan, COUNT(*) AS total")->where('created_at', '>=', now()->subMonths(6))->groupBy('bulan')->orderBy('bulan')->get();
        return ['datasets' => [['label' => 'Tiket', 'data' => $data->pluck('total'), ], ], 'labels' => collect($data->pluck('bulan'))->map(fn ($b) => \Carbon\Carbon::createFromFormat('Y-m', $b)->translatedFormat('M Y')), ];
    }
    protected function getType(): string
    {
        return 'line';
    }
}
