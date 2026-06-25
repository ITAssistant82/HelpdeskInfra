<?php

namespace Database\Seeders;

use App\Models\TicketLayer;
use Illuminate\Database\Seeder;

class TicketLayerSeeder extends Seeder
{
    public function run(): void
    {
        $layers = [
            // IT Infra — 3 layer escalation
            ['name' => 'IT Infra L1', 'role_name' => 'it_infra_l1', 'level' => 1, 'escalation_hours' => 4,  'team_key' => 'IT_INFRA'],
            ['name' => 'IT Infra L2', 'role_name' => 'it_infra_l2', 'level' => 2, 'escalation_hours' => 8,  'team_key' => 'IT_INFRA'],
            ['name' => 'IT Infra L3', 'role_name' => 'it_infra_l3', 'level' => 3, 'escalation_hours' => null,'team_key' => 'IT_INFRA'],

            // Helpdesk — single layer (no escalation)
            ['name' => 'Helpdesk L1',   'role_name' => 'helpdesk_l1', 'level' => 1, 'escalation_hours' => null, 'team_key' => 'HELPDESK'],

            // Network — single layer
            ['name' => 'Network Team',  'role_name' => 'network_team', 'level' => 1, 'escalation_hours' => null, 'team_key' => 'NETWORK'],

            // M365 — single layer
            ['name' => 'M365 Team',     'role_name' => 'm365_team',   'level' => 1, 'escalation_hours' => null, 'team_key' => 'M365'],

            // Security SOC — single layer
            ['name' => 'Security SOC',  'role_name' => 'security_soc','level' => 1, 'escalation_hours' => null, 'team_key' => 'SECURITY'],
        ];

        foreach ($layers as $data) {
            TicketLayer::firstOrCreate(
                ['team_key' => $data['team_key'], 'level' => $data['level']],
                $data
            );
        }

        $this->command->info('Ticket layers seeded: ' . count($layers));
    }
}
