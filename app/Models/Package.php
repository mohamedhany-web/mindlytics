<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'features',
        'price',
        'original_price',
        'thumbnail',
        'duration_days',
        'courses_count',
        'order',
        'is_active',
        'is_featured',
        'is_popular',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });

        static::saving(function ($package) {
            // تحديث عدد الكورسات تلقائياً
            $package->courses_count = $package->courses()->count();
        });
    }

    /**
     * العلاقة مع الكورسات (many-to-many)
     */
    public function courses()
    {
        return $this->belongsToMany(AdvancedCourse::class, 'package_course', 'package_id', 'course_id')
            ->withPivot('order')
            ->orderBy('package_course.order')
            ->withTimestamps();
    }

    /**
     * حساب السعر بعد الخصم
     */
    public function getDiscountAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return $this->original_price - $this->price;
        }
        return 0;
    }

    /**
     * حساب نسبة الخصم
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > 0) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100, 0);
        }
        return 0;
    }

    /**
     * حساب إجمالي سعر الكورسات في الباقة
     */
    public function getTotalCoursesPriceAttribute()
    {
        return $this->courses()->sum('price');
    }

    /**
     * Scope للباقات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Scope للباقات المميزة
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope للباقات الشائعة
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }
}

