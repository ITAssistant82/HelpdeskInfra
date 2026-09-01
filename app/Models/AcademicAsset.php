<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AcademicAsset extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'asset_code',
        'room_type',
        'asset_type',
        'brand',
        'model',
        'serial_number',
        'location',
        'building',
        'room',
        'condition',
        'status',
        'os',
        'processor',
        'mainboard',
        'memory_gb',
        'hard_drive_gb',
        'monitor',
        'tahun_pembelian',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function getUsiaAttribute(): ?string
    {
        if (! $this->tahun_pembelian) {
            return null;
        }

        return max(0, now()->year - (int) $this->tahun_pembelian).' tahun';
    }
}
