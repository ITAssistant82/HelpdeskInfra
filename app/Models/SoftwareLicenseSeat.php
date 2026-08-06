<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwareLicenseSeat extends Model
{
    protected $fillable = [
        'software_renewal_id',
        'email',
        'slot_name',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function softwareRenewal(): BelongsTo
    {
        return $this->belongsTo(SoftwareRenewal::class);
    }
}
