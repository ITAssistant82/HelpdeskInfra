<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssetStock extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_code',
        'asset_type',
        'brand',
        'model',
        'serial_number',
        'condition',
        'os',
        'processor',
        'mainboard',
        'memory_gb',
        'hard_drive_gb',
        'monitor',
        'tahun_pembelian',
        'location',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
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
