<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssetAccessPoint extends Model
{
    use LogsActivity;

    protected $fillable = [
        'location',
        'nama_gedung',
        'lantai',
        'jumlah',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetAccessPointItem::class, 'asset_access_point_id');
    }
}
