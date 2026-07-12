<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ScholarshipProgram extends Model
{
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_ACTIVATED = 'activated';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_DEACTIVATED = 'deactivated';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'instructor_id',
        'advanced_course_id',
        'is_active',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ScholarshipProgram $program) {
            if (empty($program->slug) && ! empty($program->name)) {
                $program->slug = static::generateUniqueSlug($program->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'scholarship';
        }
        $slug = $base;
        $i = 1;
        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function advancedCourse(): BelongsTo
    {
        return $this->course();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ScholarshipRegistration::class, 'scholarship_program_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(ScholarshipGroup::class, 'scholarship_program_id')->orderBy('name');
    }

    public function isRegistrationOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function registrationUrl(): string
    {
        return url('/scholarships/' . $this->slug . '/register');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
