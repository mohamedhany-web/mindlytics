<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SalesDiplomaBoardEntry extends Model
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
        'booking_methods',
        'free_lecture_links',
        'academic_year_id',
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
            'booking_methods' => 'array',
            'free_lecture_links' => 'array',
            'is_active' => 'boolean',
            'landing_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SalesDiplomaBoardEntry $entry) {
            if (empty($entry->slug) && filled($entry->name)) {
                $entry->slug = static::generateUniqueSlug($entry->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'diploma';
        }

        $slug = $base;
        $i = 1;
        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(SalesDiplomaBoardVisit::class, 'sales_diploma_board_entry_id');
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

    public function landingUrl(): ?string
    {
        if (! $this->landing_published || blank($this->slug)) {
            return null;
        }

        return route('public.sales-diploma-board.show', $this->slug);
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

    public function startDisplay(): ?string
    {
        if ($this->starts_at) {
            return $this->starts_at->format('Y-m-d');
        }

        return filled($this->start_label) && $this->start_label !== '—'
            ? $this->start_label
            : null;
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
        $start = $this->startDisplay();
        if (filled($start)) {
            $auto[] = 'موعد البداية: '.$start;
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

    /** @return list<string> */
    public function bookingMethodList(): array
    {
        return is_array($this->booking_methods)
            ? array_values(array_filter(array_map('strval', $this->booking_methods)))
            : [];
    }

    /**
     * @return list<array{title: string, url: string}>
     */
    public function freeLectureLinkList(): array
    {
        $raw = is_array($this->free_lecture_links) ? $this->free_lecture_links : [];
        $out = [];

        foreach ($raw as $item) {
            if (is_string($item)) {
                $parts = array_map('trim', explode('|', $item, 2));
                $title = $parts[0] ?? '';
                $url = $parts[1] ?? $parts[0] ?? '';
            } elseif (is_array($item)) {
                $title = trim((string) ($item['title'] ?? ''));
                $url = trim((string) ($item['url'] ?? ''));
            } else {
                continue;
            }

            if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $out[] = [
                'title' => $title !== '' ? $title : $url,
                'url' => $url,
            ];
        }

        return $out;
    }
}
