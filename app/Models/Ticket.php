<?php

namespace App\Models;

use App\Notifications\TicketNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number', 'type', 'category_id',
        'title', 'description', 'location',
        'requester_id', 'requester_unit',
        'impact', 'urgency', 'priority',
        'status',
        'assigned_to', 'assigned_group',
        'first_response_at', 'sla_deadline', 'sla_achieved',
        'solved_at', 'closed_at', 'closure_note', 'resolution_note',
        'needs_approval', 'current_approver_sequence',
        'approved_by', 'approved_at',
        'justification', 'due_date', 'cost_center', 'vendor_name',
        'device_asset', 'application_service',
        'team_key', 'current_layer', 'current_layer_entered_at',
    ];

    protected function casts(): array
    {
        return [
            'sla_deadline' => 'datetime',
            'first_response_at' => 'datetime',
            'solved_at' => 'datetime',
            'closed_at' => 'datetime',
            'approved_at' => 'datetime',
            'due_date' => 'date',
            'current_layer_entered_at' => 'datetime',
            'sla_achieved' => 'boolean',
            'needs_approval' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ticketApprovals(): HasMany
    {
        return $this->hasMany(TicketApproval::class)->orderBy('sequence_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function currentLayer(): BelongsTo
    {
        return $this->belongsTo(TicketLayer::class, 'current_layer', 'level')
            ->whereColumn('team_key', 'tickets.team_key');
    }

    public function helpers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_helpers', 'ticket_id', 'user_id')
            ->withTimestamps()
            ->withPivot('added_by');
    }

    public function addHelper(User $user): void
    {
        $exists = $this->helpers()->where('user_id', $user->id)->exists();
        if ($exists) return;

        $this->helpers()->attach($user->id, ['added_by' => auth()->id()]);

        $this->activities()->create([
            'user_id' => auth()->id(),
            'action' => 'system',
            'description' => "Menambahkan {$user->name} sebagai pembantu",
        ]);

        Notification::send($user, new \App\Notifications\TicketNotification(
            ticket: $this,
            message: "Anda di-summon untuk membantu tiket {$this->ticket_number} oleh " . (auth()->user()?->name ?? 'System'),
            type: 'helper',
        ));
    }

    public function removeHelper(User $user): void
    {
        $this->helpers()->detach($user->id);

        $this->activities()->create([
            'user_id' => auth()->id(),
            'action' => 'system',
            'description' => "Menghapus {$user->name} dari pembantu",
        ]);
    }

    public function nextLayer(): ?TicketLayer
    {
        if (!$this->team_key || !$this->current_layer) return null;
        return TicketLayer::where('team_key', $this->team_key)
            ->where('level', $this->current_layer + 1)
            ->first();
    }

    public function higherLayers(): \Illuminate\Support\Collection
    {
        if (!$this->team_key || !$this->current_layer) return collect();
        return TicketLayer::where('team_key', $this->team_key)
            ->where('level', '>', $this->current_layer)
            ->orderBy('level')
            ->get();
    }

    public function escalateToNextLayer(): void
    {
        $next = $this->nextLayer();
        if (!$next) return;

        $this->update([
            'assigned_group' => $next->role_name,
            'current_layer' => $next->level,
            'current_layer_entered_at' => now(),
            'status' => 'Escalated',
            'assigned_to' => null,
            'team_key' => $next->team_key,
        ]);
    }

    public function escalateToLayer(TicketLayer $layer): void
    {
        $this->update([
            'assigned_group' => $layer->role_name,
            'current_layer' => $layer->level,
            'current_layer_entered_at' => now(),
            'status' => 'Escalated',
            'assigned_to' => null,
            'team_key' => $layer->team_key,
        ]);
    }

    public function currentApproval(): ?TicketApproval
    {
        return $this->ticketApprovals()->where('sequence_order', $this->current_approver_sequence)->first();
    }

    public function isApprovalComplete(): bool
    {
        return $this->ticketApprovals()->where('status', '!=', 'approved')->doesntExist();
    }

    public function isOverdue(): bool
    {
        if (!$this->sla_deadline) return false;
        if (in_array($this->status, ['Solved', 'Closed', 'Rejected/Out of Scope'])) return false;
        return now()->gt($this->sla_deadline);
    }

    public function slaStatus(): string
    {
        if (!$this->sla_deadline) return 'none';
        if (in_array($this->status, ['Solved', 'Closed', 'Rejected/Out of Scope'])) {
            return $this->sla_achieved ? 'achieved' : 'overdue';
        }
        if ($this->isOverdue()) return 'overdue';

        $remaining = now()->diffInSeconds($this->sla_deadline, false);
        $total = $this->created_at->diffInSeconds($this->sla_deadline);
        $pct = $total > 0 ? ($remaining / $total) : 0;

        if ($pct <= 0.25) return 'warning';
        return 'on_track';
    }

    public function isOutsideWorkingHours(): bool
    {
        $created = $this->created_at;
        if (!$created) return false;

        if ($created->isWeekend()) return true;

        $hour = (int) $created->format('H');
        $minute = (int) $created->format('i');
        $time = $hour * 60 + $minute;

        return $time < 8 * 60 || $time >= 17 * 60;
    }

    public static function businessHoursElapsed(\Carbon\Carbon $from): float
    {
        $now = now();
        if ($from->gte($now)) return 0;

        $hours = 0;
        $current = $from->copy();

        while ($current->lt($now)) {
            if ($current->isWeekend()) {
                $current->startOfDay()->addDay();
                continue;
            }

            $dayStart = $current->copy()->setTime(8, 0);
            $dayEnd = $current->copy()->setTime(17, 0);

            if ($current->lt($dayStart)) {
                $current = $dayStart;
                continue;
            }

            $segmentEnd = $now->lt($dayEnd) ? $now : $dayEnd;
            if ($current->lt($segmentEnd)) {
                $hours += $current->diffInMinutes($segmentEnd) / 60;
            }

            $current = $dayEnd;
            if ($current->lt($now)) {
                $current->startOfDay()->addDay();
            }
        }

        return round($hours, 2);
    }

    public static function calculatePriority(string $impact, string $urgency): string
    {
        $matrix = [
            'Critical' => ['Critical' => 'Critical', 'High' => 'Critical', 'Medium' => 'High', 'Low' => 'High'],
            'High'     => ['Critical' => 'Critical', 'High' => 'High',    'Medium' => 'High', 'Low' => 'Medium'],
            'Medium'   => ['Critical' => 'High',     'High' => 'Medium',  'Medium' => 'Medium', 'Low' => 'Low'],
            'Low'      => ['Critical' => 'Medium',   'High' => 'Low',     'Medium' => 'Low',   'Low' => 'Low'],
        ];

        return $matrix[$impact][$urgency] ?? 'Medium';
    }

    public static function slaHoursForPriority(string $priority): int
    {
        return match ($priority) {
            'Critical' => 1,
            'High' => 4,
            'Medium' => 8,
            'Low' => 24,
            default => 24,
        };
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $ticket->ticket_number = static::generateTicketNumber();
            $ticket->impact = $ticket->impact ?? 'Medium';
            $ticket->sla_deadline = $ticket->sla_deadline ?? now()->addDay();

            if (!$ticket->assigned_group && $ticket->category_id) {
                $category = \App\Models\TicketCategory::find($ticket->category_id);
                if ($category) {
                    $ticket->assigned_group = $category->assigned_team;
                }
            }

            if ($ticket->assigned_group) {
                if (!$ticket->team_key) {
                    $layerFromGroup = TicketLayer::where('role_name', $ticket->assigned_group)->first();
                    if ($layerFromGroup) {
                        $ticket->team_key = $layerFromGroup->team_key;
                    }
                }
                if ($ticket->team_key) {
                    $lowest = TicketLayer::where('team_key', $ticket->team_key)
                        ->orderBy('level')->first();
                    if ($lowest) {
                        $ticket->assigned_group = $lowest->role_name;
                        $ticket->current_layer = $lowest->level;
                        $ticket->current_layer_entered_at = now();
                    }
                }
            }
        });

        static::created(function (Ticket $ticket) {
            $category = \App\Models\TicketCategory::with('approvers')->find($ticket->category_id);
            if ($category && $category->needs_approval) {
                if ($category && $category->approvers->isNotEmpty()) {
                    foreach ($category->approvers as $ca) {
                        $ticket->ticketApprovals()->create([
                            'role_name' => $ca->role_name,
                            'sequence_order' => $ca->sequence_order,
                        ]);
                    }

                    $firstStep = $category->approvers->first();
                    if ($firstStep && \Spatie\Permission\Models\Role::where('name', $firstStep->role_name)->exists()) {
                        $notifyUsers = User::whereHas('roles', fn ($q) => $q->where('name', $firstStep->role_name))->get();
                        Notification::send($notifyUsers, new TicketNotification(
                            ticket: $ticket,
                            message: "Tiket {$ticket->ticket_number} menunggu persetujuan",
                            type: 'approval',
                        ));
                    }
                }
            }

            if ($ticket->isOutsideWorkingHours()) {
                $ticket->activities()->create([
                    'user_id' => null,
                    'action' => 'system',
                    'description' => 'Tiket masuk di luar jam kerja (Senin-Jumat 08:00-17:00). Akan diproses pada jam kerja berikutnya.',
                ]);
            }

            static::notifyTeam("Tiket baru: {$ticket->ticket_number} - {$ticket->title}", $ticket);
        });

        static::updated(function (Ticket $ticket) {
            $changed = $ticket->getDirty();
            $original = $ticket->getOriginal();

            if (isset($changed['assigned_to']) && $changed['assigned_to'] && $changed['assigned_to'] !== $original['assigned_to']) {
                $user = User::find($changed['assigned_to']);
                if ($user) {
                    Notification::send($user, new TicketNotification(
                        ticket: $ticket,
                        message: "Tiket {$ticket->ticket_number} ditugaskan kepada Anda",
                        type: 'assigned',
                    ));
                }
            }

            if (isset($changed['current_layer']) && $changed['current_layer'] !== ($original['current_layer'] ?? null)) {
                $newLayer = TicketLayer::where('team_key', $ticket->team_key)
                    ->where('level', $changed['current_layer'])
                    ->first();
                if ($newLayer) {
                    $notifyUsers = User::whereHas('roles', fn ($q) => $q->where('name', $newLayer->role_name))->get();
                    Notification::send($notifyUsers, new TicketNotification(
                        ticket: $ticket,
                        message: "Tiket {$ticket->ticket_number} dinaikkan ke {$newLayer->name} — mohon ditindaklanjuti",
                        type: 'escalation',
                    ));
                }
            }

            if (isset($changed['status']) && $changed['status'] !== $original['status']) {
                if ($ticket->requester_id !== auth()->id()) {
                    Notification::send($ticket->requester, new TicketNotification(
                        ticket: $ticket,
                        message: "Status tiket {$ticket->ticket_number} berubah menjadi {$changed['status']}",
                        type: 'status',
                    ));
                }
            }
        });
    }

    protected static function notifyTeam(string $message, Ticket $ticket): void
    {
        $roleNames = collect(['super_admin', 'admin']);

        if ($ticket->assigned_group) {
            $roleNames->push($ticket->assigned_group);
        } elseif ($ticket->team_key) {
            $lowest = TicketLayer::where('team_key', $ticket->team_key)->orderBy('level')->first();
            if ($lowest) {
                $roleNames->push($lowest->role_name);
            }
        }

        $users = User::whereHas('roles', fn ($q) => $q->whereIn('name', $roleNames->toArray()))->get();

        if ($users->isEmpty()) {
            $users = User::whereHas('roles', fn ($q) => $q->where('name', '!=', 'user'))->get();
        }

        Notification::send($users, new TicketNotification(
            ticket: $ticket,
            message: $message,
            type: 'new_ticket',
        ));
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'IT-' . now()->format('Ymd') . '-';
        $last = static::withTrashed()
            ->where('ticket_number', 'like', $prefix . '%')
            ->latest('id')->first();

        if ($last) {
            $lastNum = (int) substr($last->ticket_number, -4);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return $prefix . str_pad((string) $newNum, 4, '0', STR_PAD_LEFT);
    }
}
