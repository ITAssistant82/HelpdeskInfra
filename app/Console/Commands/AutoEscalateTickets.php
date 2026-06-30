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
                ->where('status', 'New')
                ->where('current_layer_entered_at', '<=', now()->subHours($layer->escalation_hours))
                ->get();

            foreach ($tickets as $ticket) {
                $this->info("Escalating ticket {$ticket->ticket_number} from {$layer->name} to {$nextLayer->name}");

                $ticket->activities()->create([
                    'user_id' => null,
                    'action' => 'system',
                    'description' => "Tiket otomatis dinaikkan ke {$nextLayer->name} karena melebihi batas waktu {$layer->escalation_hours} jam di {$layer->name}",
                ]);

                $ticket->escalateToNextLayer();
                $escalated++;
            }
        }

        $this->info("Done. {$escalated} ticket(s) escalated.");
        return Command::SUCCESS;
    }
}
