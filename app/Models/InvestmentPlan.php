<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class InvestmentPlan extends Model
{
    public const TYPE_EQUITY = 'equity';

    public const TYPE_REVENUE_SHARE = 'revenue_share';

    public const TYPE_PARTNERSHIP = 'partnership';

    public const TYPE_FIXED_RETURN = 'fixed_return';

    public const TYPE_STRATEGIC = 'strategic';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'plan_type',
        'min_investment',
        'max_investment',
        'target_amount',
        'currency',
        'duration_months',
        'expected_return_min',
        'expected_return_max',
        'return_model',
        'risk_level',
        'eligibility_criteria',
        'benefits',
        'terms_summary',
        'legal_notes',
        'process_steps',
        'is_active',
        'is_featured',
        'sort_order',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'min_investment' => 'decimal:2',
        'max_investment' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'expected_return_min' => 'decimal:2',
        'expected_return_max' => 'decimal:2',
        'process_steps' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(InvestmentInquiry::class);
    }

    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function formattedMinInvestment(): string
    {
        return number_format((float) $this->min_investment, 0) . ' ' . $this->currency;
    }

    public function planTypeLabel(): string
    {
        return self::planTypeLabels()[$this->plan_type] ?? $this->plan_type;
    }

    public function riskLevelLabel(): string
    {
        return self::riskLevelLabels()[$this->risk_level] ?? $this->risk_level;
    }

    public function returnModelLabel(): string
    {
        return self::returnModelLabels()[$this->return_model] ?? $this->return_model;
    }

    public static function planTypeLabels(): array
    {
        return [
            self::TYPE_EQUITY => 'حصة ملكية (Equity)',
            self::TYPE_REVENUE_SHARE => 'مشاركة في الإيرادات',
            self::TYPE_PARTNERSHIP => 'شراكة استراتيجية',
            self::TYPE_FIXED_RETURN => 'عائد ثابت',
            self::TYPE_STRATEGIC => 'استثمار استراتيجي',
        ];
    }

    public static function riskLevelLabels(): array
    {
        return [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
        ];
    }

    public static function returnModelLabels(): array
    {
        return [
            'profit_share' => 'تقاسم الأرباح',
            'fixed_annual' => 'عائد سنوي ثابت',
            'equity_stake' => 'حصة في رأس المال',
            'revenue_share' => 'نسبة من الإيرادات',
            'custom' => 'نموذج مخصص',
        ];
    }

    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'plan-' . Str::random(6);
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
