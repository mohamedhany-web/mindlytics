<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class CrmDocument extends Model
{
    protected $fillable = [
        'title', 'category', 'disk', 'path', 'original_name', 'mime', 'size',
        'parent_type', 'parent_id', 'description', 'assigned_to', 'created_by',
    ];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function parent(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withDefault(['name' => '—']);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => '—']);
    }

    public function url(): string
    {
        try {
            return Storage::disk($this->disk ?: 'public')->url($this->path);
        } catch (\Throwable) {
            return asset('storage/'.ltrim((string) $this->path, '/'));
        }
    }
}
