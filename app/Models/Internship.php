<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Internship extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const TYPE_ONSITE = 'onsite';

    public const TYPE_REMOTE = 'remote';

    public const TYPE_HYBRID = 'hybrid';

    protected $fillable = [
        'title',
        'slug',
        'department',
        'summary',
        'description',
        'requirements',
        'benefits',
        'location',
        'type',
        'duration',
        'seats',
        'status',
        'starts_at',
        'ends_at',
        'application_deadline',
        'is_featured',
        'is_published',
        'published_at',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'application_deadline' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'seats' => 'integer',
        'sort_order' => 'integer',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_OPEN => 'مفتوحة للتقديم',
            self::STATUS_CLOSED => 'مغلقة',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_ONSITE => 'حضوري',
            self::TYPE_REMOTE => 'عن بُعد',
            self::TYPE_HYBRID => 'هجين',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(InternshipApplication::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', self::STATUS_OPEN);
    }

    public function scopeOpenForApply($query)
    {
        return $query->published()
            ->where(function ($q) {
                $q->whereNull('application_deadline')
                    ->orWhere('application_deadline', '>=', now());
            });
    }

    public function isOpenForApply(): bool
    {
        if (! $this->is_published || $this->status !== self::STATUS_OPEN) {
            return false;
        }

        if ($this->application_deadline && $this->application_deadline->isPast()) {
            return false;
        }

        if ($this->seats !== null) {
            $accepted = $this->applications()->where('status', InternshipApplication::STATUS_ACCEPTED)->count();
            if ($accepted >= $this->seats) {
                return false;
            }
        }

        return true;
    }

    public function typeLabel(): string
    {
        return self::types()[$this->type] ?? $this->type;
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function remainingSeats(): ?int
    {
        if ($this->seats === null) {
            return null;
        }

        $accepted = $this->applications()->where('status', InternshipApplication::STATUS_ACCEPTED)->count();

        return max(0, $this->seats - $accepted);
    }

    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'internship-' . Str::lower(Str::random(6));
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
