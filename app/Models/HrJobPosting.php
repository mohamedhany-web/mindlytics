<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class HrJobPosting extends Model
{
    protected $table = 'hr_job_postings';

    public const STATUSES = [
        'open' => 'مفتوحة',
        'closed' => 'مغلقة',
    ];

    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'description',
        'requirements',
        'required_skills',
        'required_experience',
        'required_education',
        'status',
        'is_published',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'required_experience' => 'integer',
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
                $q->whereNull('status')->orWhere('status', 'open');
            })
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeOpen(Builder $q): Builder
    {
        return $q->where(function (Builder $q) {
            $q->whereNull('status')->orWhere('status', 'open');
        });
    }

    /**
     * @return list<string>
     */
    public function normalizedRequiredSkills(): array
    {
        $raw = $this->required_skills;

        if (is_string($raw)) {
            $raw = array_filter(array_map('trim', explode(',', $raw)));
        }

        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($s) => trim((string) $s),
            $raw
        ))));
    }

    public function requiredEducationLabel(): string
    {
        if (! $this->required_education) {
            return '—';
        }

        return config('hr.education_levels.'.$this->required_education.'.label', $this->required_education);
    }

    public function isOpen(): bool
    {
        return ($this->status ?? 'open') === 'open';
    }
}
