<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class HrJobPosting extends Model
{
    protected $table = 'hr_job_postings';

    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'description',
        'requirements',
        'is_published',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(HrJobApplication::class, 'job_posting_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}

