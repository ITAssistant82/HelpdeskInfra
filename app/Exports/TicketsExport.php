<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function query()
    {
        return Ticket::query()->with(['category', 'requester', 'assignee']);
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->type,
            $ticket->title,
            $ticket->description,
            $ticket->category?->main_category,
            $ticket->category?->sub_category,
            $ticket->requester?->name,
            $ticket->requester?->email,
            $ticket->requester_unit,
            $ticket->location,
            $ticket->impact,
            $ticket->urgency,
            $ticket->priority,
            $ticket->status,
            $ticket->assignee?->name,
            $ticket->assigned_group,
            $ticket->created_at?->format('Y-m-d H:i:s'),
            $ticket->first_response_at?->format('Y-m-d H:i:s'),
            $ticket->sla_deadline?->format('Y-m-d H:i:s'),
            $ticket->sla_achieved ? 'Ya' : 'Tidak',
            $ticket->solved_at?->format('Y-m-d H:i:s'),
            $ticket->closed_at?->format('Y-m-d H:i:s'),
            $ticket->resolution_note,
            $ticket->closure_note,
        ];
    }

    public function headings(): array
    {
        return [
            'Nomor Tiket',
            'Tipe',
            'Judul',
            'Deskripsi',
            'Kategori Utama',
            'Subkategori',
            'Pemohon',
            'Email Pemohon',
            'Unit / Departemen',
            'Lokasi',
            'Dampak',
            'Urgensi',
            'Prioritas',
            'Status',
            'Teknisi',
            'Grup Penugasan',
            'Dibuat Pada',
            'Respons Pertama',
            'Deadline SLA',
            'SLA Tercapai',
            'Diselesaikan Pada',
            'Ditutup Pada',
            'Catatan Solusi',
            'Catatan Penutupan',
        ];
    }
}
