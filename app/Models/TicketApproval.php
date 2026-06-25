<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketApproval extends Model
{
    protected $fillable = [
        'ticket_id', 'role_name', 'approver_id', 'status', 'note', 'acted_at', 'sequence_order',
    ];

    protected function casts(): array
    {
        return [
            'acted_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
