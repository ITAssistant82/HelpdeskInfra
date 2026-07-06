<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'jabatan',
        'unit',
        'no_hp',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'user', 'helpdesk_l1', 'it_infra_l1', 'it_infra_l2', 'it_infra_l3', 'network_team', 'm365_team', 'security_soc', 'approver']);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function helperTickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_helpers', 'user_id', 'ticket_id')
            ->withTimestamps()
            ->withPivot('added_by');
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'helpdesk_l1', 'it_infra_l1', 'it_infra_l2', 'it_infra_l3', 'network_team', 'm365_team', 'security_soc', 'approver']);
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            if (!$user->hasAnyRole(['super_admin', 'admin', 'helpdesk_l1', 'it_infra_l1', 'it_infra_l2', 'it_infra_l3', 'network_team', 'm365_team', 'security_soc', 'approver', 'user'])) {
                $user->assignRole('user');
            }
        });
    }
}
