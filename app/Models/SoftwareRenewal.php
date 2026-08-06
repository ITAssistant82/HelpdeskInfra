<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @method static Builder where(string $column, mixed $operator = null, mixed $value = null)
 * @method static Builder whereNotNull(string $column)
 * @method static Builder distinct()
 * @method static \Illuminate\Support\Collection pluck(string $value, ?string $key = null)
 */
class SoftwareRenewal extends Model
{
    use LogsActivity;

    protected $fillable = [
        'software',
        'renewal_date',
        'pricing',
        'email_registered',
        'pic',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'renewal_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function licenseSeats(): HasMany
    {
        return $this->hasMany(SoftwareLicenseSeat::class);
    }
}
