<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InstallmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'advanced_course_id',
        'total_amount',
        'deposit_amount',
        'installments_count',
        'frequency_unit',
        'frequency_interval',
        'grace_period_days',
        'auto_generate_on_enrollment',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'installments_count' => 'integer',
        'frequency_interval' => 'integer',
        'grace_period_days' => 'integer',
        'auto_generate_on_enrollment' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (InstallmentPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name . '-' . Str::random(4));
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(InstallmentAgreement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
