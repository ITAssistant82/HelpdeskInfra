<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @method static Builder where(string $column, mixed $operator = null, mixed $value = null)
 * @method static Builder whereNotNull(string $column)
 * @method static Builder distinct()
 * @method static \Illuminate\Support\Collection pluck(string $value, ?string $key = null)
 */

class AssetSwitch extends Model
{
    use LogsActivity;

    protected $fillable = [
        'host_name',
        'ip',
        'network_device',
        'stacking',
        'snmp',
        'brand',
        'type',
        'series',
        'remote_type',
        'username',
        'password',
        'location',
        'ruangan',
        'tower',
        'uplink_port',
        'uplink_switch',
        'downlink_port',
        'serial_number',
        'product_name',
        'eol_announcement',
        'end_of_sale',
        'end_of_service_life',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'eol_announcement' => 'date',
            'end_of_sale' => 'date',
            'end_of_service_life' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
