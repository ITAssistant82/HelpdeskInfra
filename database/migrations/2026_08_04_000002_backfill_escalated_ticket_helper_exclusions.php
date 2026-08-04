<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $oldHelpers = DB::table('ticket_helpers as helper')
            ->join('tickets as ticket', 'ticket.id', '=', 'helper.ticket_id')
            ->where('ticket.status', 'Escalated')
            ->where(function ($query) {
                $query->whereNull('ticket.current_layer_entered_at')
                    ->orWhereColumn('helper.created_at', '<', 'ticket.current_layer_entered_at');
            })
            ->select('helper.id', 'helper.ticket_id', 'helper.user_id')
            ->get();

        if ($oldHelpers->isEmpty()) {
            return;
        }

        $now = now();

        DB::table('ticket_escalation_exclusions')->insertOrIgnore(
            $oldHelpers
                ->map(fn ($helper) => [
                    'ticket_id' => $helper->ticket_id,
                    'user_id' => $helper->user_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all(),
        );

        DB::table('ticket_helpers')->whereIn('id', $oldHelpers->pluck('id'))->delete();
    }

    public function down(): void
    {
        // Historical helper assignments cannot safely be restored.
    }
};
