<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @method static Builder where(string $column, mixed $operator = null, mixed $value = null)
 * @method static int count(array|string $columns = '*')
 */

class EmployeeAsset extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    protected $fillable = [
        'employee_id',
        'asset_code',
        'asset_type',
        'brand',
        'model',
        'os',
        'mainboard',
        'processor',
        'memory_gb',
        'hard_drive_gb',
        'monitor',
        'tahun_pembelian',
        'serial_number',
        'condition',
        'assigned_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getRouteKeyName(): string
    {
        return 'asset_code';
    }

    public function getUsiaAttribute(): ?string
    {
        if (!$this->tahun_pembelian) {
            return null;
        }
        $tahun = (int) $this->tahun_pembelian;
        $umur = now()->year - $tahun;
        if ($umur < 0) {
            return '0 tahun';
        }
        return $umur . ' tahun';
    }
}
