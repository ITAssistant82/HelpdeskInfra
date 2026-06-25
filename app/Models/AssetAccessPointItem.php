<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssetAccessPointItem extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_access_point_id',
        'host_name',
        'ip',
        'product_name',
        'eol_announcement',
        'end_of_sale',
        'end_of_service_life',
    ];

    protected $casts = [
        'eol_announcement' => 'date',
        'end_of_sale' => 'date',
        'end_of_service_life' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function assetAccessPoint()
    {
        return $this->belongsTo(AssetAccessPoint::class);
    }
}
