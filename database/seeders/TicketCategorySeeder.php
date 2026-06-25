<?php

namespace Database\Seeders;

use App\Models\CategoryApprover;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // === INCIDENTS ===
            [
                'type' => 'Incident', 'main_category' => 'Hardware', 'sub_category' => 'Laptop/PC Rusak',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Hardware', 'sub_category' => 'Printer Error',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Hardware', 'sub_category' => 'Monitor/Aksesoris Rusak',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Software', 'sub_category' => 'Aplikasi Error',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Software', 'sub_category' => 'Sistem Operasi Error',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Network', 'sub_category' => 'Koneksi Internet Putus',
                'assigned_team' => 'network_team', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Network', 'sub_category' => 'VLAN/Jaringan Lokal',
                'assigned_team' => 'network_team', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Email & Collaboration', 'sub_category' => 'M365/Outlook Error',
                'assigned_team' => 'm365_team', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Akun', 'sub_category' => 'Reset Password',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Akun', 'sub_category' => 'Akun Terkunci',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Incident', 'main_category' => 'Security', 'sub_category' => 'Potensi Ancaman/Phishing',
                'assigned_team' => 'security_soc', 'needs_approval' => false,
            ],

            // === SERVICE REQUESTS ===
            [
                'type' => 'Service Request', 'main_category' => 'Hardware', 'sub_category' => 'Pengajuan Laptop Baru',
                'assigned_team' => 'it_infra_l2', 'needs_approval' => true,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Hardware', 'sub_category' => 'Pengajuan Monitor Baru',
                'assigned_team' => 'it_infra_l2', 'needs_approval' => true,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Software', 'sub_category' => 'Instalasi Aplikasi',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Software', 'sub_category' => 'Upgrade Software',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Akun', 'sub_category' => 'Pembuatan Akun Baru',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => true,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Akun', 'sub_category' => 'Akses Folder/Drive',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => true,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Procurement', 'sub_category' => 'Pembelian Aset IT',
                'assigned_team' => 'it_infra_l2', 'needs_approval' => true,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Procurement', 'sub_category' => 'Pengajuan Vendor',
                'assigned_team' => 'it_infra_l2', 'needs_approval' => true,
            ],
            [
                'type' => 'Service Request', 'main_category' => 'Lainnya', 'sub_category' => 'Permintaan Lain',
                'assigned_team' => 'helpdesk_l1', 'needs_approval' => false,
            ],
        ];

        foreach ($categories as $data) {
            $category = TicketCategory::firstOrCreate(
                ['type' => $data['type'], 'main_category' => $data['main_category'], 'sub_category' => $data['sub_category']],
                [
                    'description' => null,
                    'is_active' => true,
                    'assigned_team' => $data['assigned_team'],
                    'needs_approval' => $data['needs_approval'],
                ]
            );

            if ($data['needs_approval'] && $category->approvers()->count() === 0) {
                CategoryApprover::create([
                    'category_id' => $category->id,
                    'role_name' => 'approver',
                    'sequence_order' => 1,
                ]);
            }
        }

        $this->command->info('Ticket categories seeded: ' . count($categories));
    }
}
