<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'main_category', 'sub_category', 'description', 'is_active', 'assigned_team', 'needs_approval',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'needs_approval' => 'boolean',
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    public function approvers(): HasMany
    {
        return $this->hasMany(CategoryApprover::class, 'category_id')->orderBy('sequence_order');
    }
}
