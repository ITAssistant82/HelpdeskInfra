<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketLayer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InfrastructureTicketEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $event,
        public ?int $previousLayer = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket->loadMissing(['requester', 'category']);
        $ticketUrl = url("/admin/tickets/{$ticket->getKey()}");

        if ($this->event === 'escalated') {
            $previousLayerName = $this->layerName($this->previousLayer);
            $currentLayerName = $this->layerName($ticket->current_layer);

            return (new MailMessage)
                ->subject("[InfraDesk] Eskalasi {$ticket->ticket_number} ke {$currentLayerName}")
                ->greeting('Notifikasi Eskalasi Tiket')
                ->line("Tiket {$ticket->ticket_number} telah dinaikkan dari {$previousLayerName} ke {$currentLayerName}.")
                ->line("Judul: {$ticket->title}")
                ->line('Requester: '.($ticket->requester?->name ?? '-'))
                ->line('Prioritas: '.($ticket->priority ?? '-'))
                ->action('Buka Tiket', $ticketUrl)
                ->line('Mohon segera ditindaklanjuti sesuai layer tujuan.');
        }

        return (new MailMessage)
            ->subject("[InfraDesk] Tiket Baru {$ticket->ticket_number}")
            ->greeting('Tiket Baru Masuk')
            ->line("Tiket {$ticket->ticket_number} baru saja dibuat.")
            ->line("Judul: {$ticket->title}")
            ->line('Requester: '.($ticket->requester?->name ?? '-'))
            ->line('Kategori: '.($ticket->category?->sub_category ?? '-'))
            ->line('Lokasi: '.($ticket->location ?? '-'))
            ->line('Prioritas: '.($ticket->priority ?? '-'))
            ->action('Buka Tiket', $ticketUrl)
            ->line('Silakan buka InfraDesk untuk melihat detail dan menindaklanjuti tiket.');
    }

    private function layerName(?int $level): string
    {
        if ($level === null) {
            return 'Tanpa Layer';
        }

        return TicketLayer::query()
            ->where('team_key', $this->ticket->team_key)
            ->where('level', $level)
            ->value('name') ?? "Layer {$level}";
    }
}
