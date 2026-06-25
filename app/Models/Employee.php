<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @method static int count(array|string $columns = '*')
 */

class Employee extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nik',
        'email',
        'inisial',
        'full_name',
        'prodi_unit_kerja',
        'employee_group',
        'work_contract',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(EmployeeAsset::class);
    }
}
