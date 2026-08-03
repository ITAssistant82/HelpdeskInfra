<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @method static Builder where(string $column, mixed $operator = null, mixed $value = null)
 * @method static Builder whereNotNull(string $column)
 * @method static Builder distinct()
 * @method static \Illuminate\Support\Collection pluck(string $value, ?string $key = null)
 */
class DomainRenewal extends Model
{
    use LogsActivity;

    protected $fillable = [
        'domain',
        'registration_date',
        'expiration_date',
        'platform',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'expiration_date' => 'date',
        ];
    }

    public function computeStatus(): string
    {
        if (in_array($this->attributes['status'] ?? null, ['Renewed', 'Cancelled'])) {
            return $this->attributes['status'];
        }

        if (! $this->expiration_date) {
            return 'Active';
        }

        $now = Carbon::now();
        $expiry = Carbon::parse($this->expiration_date);

        if ($expiry->isPast()) {
            return 'Expired';
        }

        if ($expiry->lte($now->copy()->addDays(30))) {
            return 'Expiring Soon';
        }

        return 'Active';
    }

    public function getRealStatusAttribute(): string
    {
        return $this->computeStatus();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
