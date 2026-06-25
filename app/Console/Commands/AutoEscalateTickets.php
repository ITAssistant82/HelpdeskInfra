<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\TicketLayer;
use Illuminate\Console\Command;

class AutoEscalateTickets extends Command
{
    protected $signature = 'tickets:auto-escalate';
    protected $description = 'Auto-escalate tickets that have exceeded the layer time limit';

    public function handle(): int
    {
        $escalated = 0;
        $layers = TicketLayer::whereNotNull('escalation_hours')->get();

        foreach ($layers as $layer) {
            $nextLayer = TicketLayer::where('team_key', $layer->team_key)
                ->where('level', $layer->level + 1)
                ->first();
            if (!$nextLayer) continue;

            $tickets = Ticket::where('current_layer', $layer->level)
                ->where('team_key', $layer->team_key)
                ->whereNotIn('status', ['Solved', 'Closed', 'Rejected/Out of Scope'])
                ->where('current_layer_entered_at', '<=', now()->subHours($layer->escalation_hours))
                ->get();

            foreach ($tickets as $ticket) {
                $ticket->escalateToNextLayer();
                $escalated++;
                $this->info("Ticket {$ticket->ticket_number} auto-escalated from {$layer->name} to {$nextLayer->name}");
            }
        }

        $this->info("Done. {$escalated} ticket(s) escalated.");
        return Command::SUCCESS;
    }
}
