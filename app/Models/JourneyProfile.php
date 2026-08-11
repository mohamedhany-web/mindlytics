<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JourneyProfile extends Model
{
    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITY_UNLISTED = 'unlisted';

    public const VISIBILITY_PUBLIC = 'public';

    protected $fillable = [
        'user_id',
        'slug',
        'display_name',
        'headline',
        'bio',
        'career_goal',
        'github_url',
        'linkedin_url',
        'website_url',
        'visibility',
        'profile_completion',
        'is_open_to_work',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_open_to_work' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'profile_completion' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDiscoverable($query)
    {
        return $query->where('is_active', true)
            ->where('visibility', self::VISIBILITY_PUBLIC)
            ->whereNotNull('published_at');
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_active
            && $this->visibility === self::VISIBILITY_PUBLIC
            && $this->published_at !== null;
    }

    public function isUnlistedAccessible(): bool
    {
        return $this->is_active
            && in_array($this->visibility, [self::VISIBILITY_PUBLIC, self::VISIBILITY_UNLISTED], true)
            && $this->published_at !== null;
    }

    public function publicUrl(): string
    {
        return route('public.journey.show', $this->slug);
    }

    public function resolvedDisplayName(): string
    {
        return $this->display_name ?: ($this->user->name ?? 'طالب Mindlytics');
    }

    public function resolvedHeadline(): ?string
    {
        return $this->headline ?: ($this->user->headline ?? null);
    }

    public function resolvedBio(): ?string
    {
        return $this->bio ?: ($this->user->bio ?? null);
    }

    public function recalculateCompletion(): int
    {
        $checks = [
            filled($this->resolvedDisplayName()),
            filled($this->resolvedHeadline()),
            filled($this->resolvedBio()),
            filled($this->career_goal),
            filled($this->github_url) || filled($this->linkedin_url),
            filled($this->user?->profile_image),
            $this->user?->portfolioProjects()->published()->exists() ?? false,
        ];

        $score = (int) round((collect($checks)->filter()->count() / max(count($checks), 1)) * 100);
        $this->profile_completion = $score;

        return $score;
    }

    public static function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'student-' . Str::lower(Str::random(6));
        }

        $slug = $base;
        $i = 1;
        while (self::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
