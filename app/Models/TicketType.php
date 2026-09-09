<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    protected $fillable = ['name', 'color', 'description', 'is_active', 'it_only', 'sort_order'];

    public const COLORS = [
        'primary' => 'Kuning',
        'danger' => 'Merah',
        'warning' => 'Oranye',
        'success' => 'Hijau',
        'info' => 'Biru',
        'gray' => 'Abu-abu',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'it_only' => 'boolean', 'sort_order' => 'integer'];
    }

    public static function options(bool $activeOnly = true): array
    {
        $canViewITOnly = auth()->user()?->isITStaff() ?? false;

        return static::query()
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->when(! $canViewITOnly, fn ($query) => $query->where('it_only', false))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }

    public static function suggestedColor(): string
    {
        $palette = ['success', 'info', 'primary', 'gray', 'danger', 'warning'];
        $usedColors = static::query()->pluck('color')->all();

        return collect($palette)->first(fn (string $color): bool => ! in_array($color, $usedColors, true))
            ?? $palette[static::query()->count() % count($palette)];
    }

    public static function colorFor(?string $name): string
    {
        static $colors;

        $colors ??= static::query()->pluck('color', 'name')->all();

        return $colors[$name] ?? 'gray';
    }

    public static function isAvailableToCurrentUser(string $name): bool
    {
        $canViewITOnly = auth()->user()?->isITStaff() ?? false;

        return static::query()
            ->where('name', $name)
            ->where('is_active', true)
            ->when(! $canViewITOnly, fn ($query) => $query->where('it_only', false))
            ->exists();
    }

    public function isInUse(): bool
    {
        return Ticket::query()->where('type', $this->name)->exists()
            || TicketCategory::query()->where('type', $this->name)->exists();
    }
}
