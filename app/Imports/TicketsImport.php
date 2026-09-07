<?php

namespace App\Imports;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketLayer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TicketsImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    public int $created = 0;

    public int $updated = 0;

    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows): void {
            foreach ($rows as $index => $row) {
                $this->importRow($row, $index + 2);
            }
        });
    }

    private function importRow(Collection $row, int $rowNumber): void
    {
        $required = ['tipe', 'judul', 'deskripsi', 'kategori_utama', 'subkategori', 'email_pemohon', 'lokasi'];
        foreach ($required as $heading) {
            if (blank($row->get($heading))) {
                $this->invalid($rowNumber, "Kolom {$heading} wajib diisi.");
            }
        }

        $category = TicketCategory::query()
            ->where('main_category', trim((string) $row['kategori_utama']))
            ->where('sub_category', trim((string) $row['subkategori']))
            ->first();
        if (! $category) {
            $this->invalid($rowNumber, 'Kombinasi kategori utama dan subkategori tidak ditemukan.');
        }

        $requester = User::query()->where('email', trim((string) $row['email_pemohon']))->first();
        if (! $requester) {
            $this->invalid($rowNumber, 'Email pemohon tidak terdaftar.');
        }

        $assignee = filled($row->get('teknisi'))
            ? User::query()->where('name', trim((string) $row['teknisi']))->first()
            : null;
        if (filled($row->get('teknisi')) && ! $assignee) {
            $this->invalid($rowNumber, 'Teknisi tidak ditemukan.');
        }

        $type = trim((string) $row['tipe']);
        if (! in_array($type, ['Incident', 'Service Request'], true)) {
            $this->invalid($rowNumber, 'Tipe harus Incident atau Service Request.');
        }

        $assignedGroup = $this->nullableString($row->get('grup_penugasan')) ?: $category->assigned_team;
        $layer = $assignedGroup
            ? TicketLayer::query()->where('role_name', $assignedGroup)->first()
            : null;

        $attributes = [
            'type' => $type,
            'category_id' => $category->id,
            'title' => trim((string) $row['judul']),
            'description' => trim((string) $row['deskripsi']),
            'location' => trim((string) $row['lokasi']),
            'requester_id' => $requester->id,
            'requester_unit' => $this->nullableString($row->get('unit_departemen')),
            'impact' => $this->nullableString($row->get('dampak')) ?: 'Medium',
            'urgency' => $this->nullableString($row->get('urgensi')),
            'status' => $this->nullableString($row->get('status')) ?: 'New',
            'assigned_to' => $assignee?->id,
            'assigned_group' => $assignedGroup,
            'team_key' => $layer?->team_key,
            'current_layer' => $layer?->level,
            'first_response_at' => $this->date($row->get('respons_pertama'), $rowNumber),
            'sla_deadline' => $this->date($row->get('deadline_sla'), $rowNumber),
            'sla_achieved' => $this->boolean($row->get('sla_tercapai')),
            'solved_at' => $this->date($row->get('diselesaikan_pada'), $rowNumber),
            'closed_at' => $this->date($row->get('ditutup_pada'), $rowNumber),
            'resolution_note' => $this->nullableString($row->get('catatan_solusi')),
            'closure_note' => $this->nullableString($row->get('catatan_penutupan')),
        ];

        $ticketNumber = $this->nullableString($row->get('nomor_tiket'));
        $ticket = $ticketNumber ? Ticket::withTrashed()->where('ticket_number', $ticketNumber)->first() : null;

        if ($ticket) {
            $ticket->restore();
            $ticket->update($attributes);
            $this->updated++;
        } else {
            Ticket::create($attributes);
            $this->created++;
        }
    }

    private function nullableString(mixed $value): ?string
    {
        return blank($value) ? null : trim((string) $value);
    }

    private function boolean(mixed $value): ?bool
    {
        if (blank($value)) {
            return null;
        }

        return in_array(strtolower(trim((string) $value)), ['ya', 'yes', '1', 'true'], true);
    }

    private function date(mixed $value, int $rowNumber): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return is_numeric($value)
                ? Carbon::instance(Date::excelToDateTimeObject($value))
                : Carbon::parse((string) $value);
        } catch (\Throwable) {
            $this->invalid($rowNumber, "Format tanggal '{$value}' tidak valid.");
        }
    }

    private function invalid(int $rowNumber, string $message): never
    {
        throw ValidationException::withMessages([
            'file' => "Baris {$rowNumber}: {$message}",
        ]);
    }
}
