<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketLayer;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id() ?? $ticket->requester_id,
            'action' => 'created',
            'description' => "Tiket <strong>{$ticket->ticket_number}</strong> dibuat oleh <strong>" . e($ticket->requester?->name ?? 'System') . "</strong>",
            'new_values' => $ticket->toArray(),
        ]);
    }

    public function updated(Ticket $ticket): void
    {
        $changed = $ticket->getDirty();
        $original = $ticket->getOriginal();
        $descriptions = [];
        $action = 'updated';

        foreach ($changed as $field => $newValue) {
            if (in_array($field, ['updated_at', 'ticket_number'])) continue;
            $old = $original[$field] ?? null;
            if ($old == $newValue) continue;

            $formatted = static::formatFieldChange($field, $old, $newValue, $ticket);
            if ($formatted) {
                $descriptions[] = $formatted;
            }
        }

        if (empty($descriptions)) return;

        $action = static::determineAction($changed, $original);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id() ?? $ticket->requester_id,
            'action' => $action,
            'description' => implode('<br>', $descriptions),
            'old_values' => $original,
            'new_values' => $ticket->toArray(),
        ]);
    }

    protected static function formatFieldChange(string $field, mixed $old, mixed $new, Ticket $ticket): ?string
    {
        $label = match ($field) {
            'status'          => 'Status',
            'assigned_to'     => 'Ditugaskan ke',
            'assigned_group'  => 'Grup',
            'priority'        => 'Prioritas',
            'impact'          => 'Dampak',
            'urgency'         => 'Urgensi',
            'current_layer'   => 'Layer',
            'team_key'        => 'Tim',
            'category_id'     => 'Kategori',
            'solved_at'       => 'Waktu solve',
            'closed_at'       => 'Waktu close',
            'sla_deadline'    => 'Deadline SLA',
            'sla_achieved'    => 'SLA',
            'resolution_note' => 'Catatan resolusi',
            'closure_note'    => 'Catatan penutupan',
            'first_response_at' => 'Respon pertama',
            'approved_by'     => 'Disetujui oleh',
            'approved_at'     => 'Waktu approval',
            'current_approver_sequence' => 'Tahap approval',
            default           => str_replace('_', ' ', $field),
        };

        $oldDisplay = static::displayValue($field, $old, $ticket);
        $newDisplay = static::displayValue($field, $new, $ticket);

        if ($oldDisplay === $newDisplay) return null;

        return "<strong>{$label}:</strong> {$oldDisplay} <span class=\"text-gray-400\">→</span> {$newDisplay}";
    }

    protected static function displayValue(string $field, mixed $value, Ticket $ticket): string
    {
        if ($value === null || $value === '') return '<span class="text-gray-400 italic">-</span>';

        return match ($field) {
            'assigned_to' => e(User::find($value)?->name ?? "(#{$value})"),
            'assigned_group' => e(TicketLayer::where('role_name', $value)->value('name') ?? ucfirst(str_replace('_', ' ', $value))),
            'current_layer' => 'L' . e((string) $value),
            'category_id' => e(TicketCategory::find($value)?->sub_category ?? "(#{$value})"),
            'sla_deadline' => $value ? date('d/m/Y H:i', strtotime($value)) : '-',
            'solved_at' => $value ? date('d/m/Y H:i', strtotime($value)) : '-',
            'closed_at' => $value ? date('d/m/Y H:i', strtotime($value)) : '-',
            'approved_at' => $value ? date('d/m/Y H:i', strtotime($value)) : '-',
            'first_response_at' => $value ? date('d/m/Y H:i', strtotime($value)) : '-',
            'sla_achieved' => $value ? '<span class="text-success">Tercapai</span>' : '<span class="text-danger">Tidak tercapai</span>',
            'status' => static::statusLabel($value),
            default => e((string) $value),
        };
    }

    protected static function statusLabel(string $status): string
    {
        $colors = [
            'New' => 'info',
            'Assigned' => 'primary',
            'In Progress' => 'warning',
            'Pending User' => 'gray',
            'Pending Vendor' => 'gray',
            'Pending Procurement' => 'gray',
            'Pending Approval' => 'warning',
            'Escalated' => 'danger',
            'Solved' => 'success',
            'Closed' => 'success',
            'Reopened' => 'warning',
            'Rejected/Out of Scope' => 'danger',
        ];
        $color = $colors[$status] ?? 'gray';
        return "<span class=\"text-{$color} font-medium\">{$status}</span>";
    }

    protected static function determineAction(array $changed, array $original): string
    {
        if (isset($changed['current_layer']) && $changed['current_layer'] !== ($original['current_layer'] ?? null)) {
            return 'escalated';
        }
        if (isset($changed['assigned_to'])) {
            return 'assigned';
        }
        if (isset($changed['status'])) {
            $new = $changed['status'];
            return match ($new) {
                'Solved' => 'solved',
                'Closed' => 'closed',
                'Reopened' => 'reopened',
                'Rejected/Out of Scope' => 'rejected',
                'Assigned' => 'assigned',
                default => 'status_updated',
            };
        }
        return 'updated';
    }
}
