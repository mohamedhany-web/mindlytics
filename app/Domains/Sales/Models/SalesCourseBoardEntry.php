<?php

namespace App\Domains\Sales\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Domains\Learning\Models\AdvancedCourse;

class SalesCourseBoardEntry extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'audience',
        'instructor_name',
        'start_label',
        'starts_at',
        'schedule_days',
        'duration',
        'hours',
        'price_online',
        'price_recorded',
        'format',
        'summary',
        'landing_details',
        'highlights',
        'advanced_course_id',
        'sort_order',
        'is_active',
        'landing_published',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'price_online' => 'decimal:2',
            'price_recorded' => 'decimal:2',
            'highlights' => 'array',
            'is_active' => 'boolean',
            'landing_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SalesCourseBoardEntry $entry) {
            if (empty($entry->slug) && filled($entry->name)) {
                $entry->slug = static::generateUniqueSlug($entry->name);
            }
        });

        // الموقع العام بيعمل cache لوجود دفعات (شريط «القبول مفتوح») ولقائمة المجالات في بوب أب الإرشاد
        $flush = function (): void {
            \Illuminate\Support\Facades\Cache::forget('site.has_intakes');
            \Illuminate\Support\Facades\Cache::forget('advising.interests.ar');
            \Illuminate\Support\Facades\Cache::forget('advising.interests.en');
        };
        static::saved($flush);
        static::deleted($flush);
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'course';
        }

        $slug = $base;
        $i = 1;
        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function advancedCourse(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublishedLanding(Builder $query): Builder
    {
        return $query->where('landing_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /** تاريخ البداية لو موجود، وإلا موعد البداية النصي. */
    public function startDisplay(): ?string
    {
        if ($this->starts_at) {
            return $this->starts_at->format('Y-m-d');
        }

        return filled($this->start_label) && $this->start_label !== '—' ? (string) $this->start_label : null;
    }

    /**
     * السعر اللي بيظهر برّه: سعر الكورس المربوط بعد الخصم (نفس اللي بيتدفع في الـ checkout)، وإلا السعر الأونلاين المكتوب في اللوحة.
     * 0 = الكورس المربوط مجاني. null = مفيش سعر.
     */
    public function publicPrice(): ?float
    {
        $course = $this->advancedCourse;
        if ($course && $course->is_active) {
            return $course->effectivePrice();
        }

        return $this->price_online !== null && (float) $this->price_online > 0 ? (float) $this->price_online : null;
    }

    /** السعر المكتوب في اللوحة مختلف عن سعر الكورس المربوط؟ (تنبيه للإدارة) */
    public function priceConflict(): ?array
    {
        $course = $this->advancedCourse;
        if (! $course || $this->price_online === null || (float) $this->price_online <= 0) {
            return null;
        }
        $linked = $course->effectivePrice();

        return abs($linked - (float) $this->price_online) > 0.009 ? ['board' => (float) $this->price_online, 'linked' => $linked, 'linked_name' => $course->title] : null;
    }

    /** خطة التقسيط الفعّالة للكورس المربوط (لو موجودة). */
    public function installmentPlan(): ?\App\Domains\Accounting\Models\InstallmentPlan
    {
        return $this->advanced_course_id
            ? \App\Domains\Accounting\Models\InstallmentPlan::query()->where('advanced_course_id', $this->advanced_course_id)->where('is_active', true)->orderBy('installments_count')->first()
            : null;
    }

    /** صفحة التفاصيل: صفحة التوصيف لو منشورة، وإلا صفحة الكورس المربوط. */
    public function detailsUrl(): ?string
    {
        if ($url = $this->landingUrl()) {
            return $url;
        }
        $course = $this->advancedCourse;

        return $course && $course->is_active ? route('public.course.show', $course->id) : null;
    }

    public function landingUrl(): ?string
    {
        if (! $this->landing_published || blank($this->slug)) {
            return null;
        }

        return route('public.sales-course-board.show', $this->slug);
    }

    public function priceLabel(): string
    {
        $parts = [];
        if ($this->price_online !== null && (float) $this->price_online > 0) {
            $parts[] = 'أونلاين: '.number_format((float) $this->price_online, 0).' ج.م';
        }
        if ($this->price_recorded !== null && (float) $this->price_recorded > 0) {
            $parts[] = 'مسجّل: '.number_format((float) $this->price_recorded, 0).' ج.م';
        }

        return $parts !== [] ? implode(' · ', $parts) : '—';
    }

    public function isComplete(): bool
    {
        return filled($this->instructor_name)
            && filled($this->format)
            && ($this->price_online !== null || $this->price_recorded !== null);
    }

    /** @return list<string> */
    public function landingHighlights(): array
    {
        $items = is_array($this->highlights) ? array_values(array_filter($this->highlights)) : [];

        if ($items !== []) {
            return $items;
        }

        $auto = [];
        if (filled($this->audience)) {
            $auto[] = 'الفئة المستهدفة: '.$this->audience;
        }
        if (filled($this->startDisplay())) {
            $auto[] = 'موعد البداية: '.$this->startDisplay();
        }
        if (filled($this->schedule_days) && $this->schedule_days !== '—') {
            $auto[] = 'أيام المحاضرات: '.$this->schedule_days;
        }
        if (filled($this->duration) && $this->duration !== '—') {
            $auto[] = 'المدة: '.$this->duration;
        }
        if (filled($this->hours) && $this->hours !== '—') {
            $auto[] = 'عدد الساعات: '.$this->hours;
        }
        if (filled($this->format) && $this->format !== '—') {
            $auto[] = 'النظام: '.$this->format;
        }

        return $auto;
    }
}
