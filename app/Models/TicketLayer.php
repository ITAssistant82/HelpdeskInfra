<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketLayer extends Model
{
    protected $fillable = [
        'name',
        'role_name',
        'level',
        'escalation_hours',
        'team_key',
    ];

    public function scopeForTeam($query, string $teamKey)
    {
        return $query->where('team_key', $teamKey);
    }

    public function scopeHigherThan($query, int $level)
    {
        return $query->where('level', '>', $level)->orderBy('level');
    }
}
