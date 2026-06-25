<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $message,
        public string $type = 'status',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $title = match ($this->type) {
            'new_ticket' => "Tiket Baru: {$this->ticket->ticket_number}",
            'assigned' => "Ditugaskan: {$this->ticket->ticket_number}",
            'escalation' => "Eskalasi: {$this->ticket->ticket_number}",
            'approval' => "Persetujuan: {$this->ticket->ticket_number}",
            'status_team', 'status' => "Status Berubah: {$this->ticket->ticket_number}",
            default => "Notifikasi: {$this->ticket->ticket_number}",
        };

        return [
            'title' => $title,
            'body' => $this->message,
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => $this->message,
            'type' => $this->type,
            'url' => "/admin/tickets/{$this->ticket->id}",
        ];
    }
}
