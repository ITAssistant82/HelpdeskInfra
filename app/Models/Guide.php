<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guide extends Model
{
    protected $fillable = [
        'title', 'content', 'category', 'order', 'is_active', 'created_by', 'attachments',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
            'attachments' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getExcerptAttribute(): string
    {
        return strip_tags($this->content);
    }
}
