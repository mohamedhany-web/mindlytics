<?php

namespace App\Domains\Community\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class CommunityDataset extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'file_path',
        'file_url',
        'file_size',
        'files',
        'downloads_count',
        'is_active',
        'status',
        'sort_order',
        'created_by_user_id',
    ];

    /** تصنيفات مجموعة البيانات (للعرض والفلترة) */
    public const CATEGORIES = [
        'education' => 'تعليمي',
        'finance' => 'مالي',
        'health' => 'صحي',
        'commerce' => 'تجاري',
        'marketing' => 'تسويق',
        'general' => 'عام',
        'other' => 'أخرى',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'files' => 'array',
        ];
    }

    /** الملفات المرفوعة (عند وجود عدة ملفات) */
    public function getFilesListAttribute(): array
    {
        $files = $this->files;
        if (is_array($files) && !empty($files)) {
            return $files;
        }
        if ($this->file_path) {
            return [['path' => $this->file_path, 'original_name' => basename($this->file_path), 'size' => $this->file_size ?? '']];
        }
        return [];
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** للمحتوى المعروض للجمهور: معتمد ونشط فقط */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED)->where('is_active', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if ($category === null || $category === '') {
            return $query;
        }
        return $query->where('category', $category);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || trim($term) === '') {
            return $query;
        }
        $term = trim($term);
        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', '%' . $term . '%')
                ->orWhere('description', 'like', '%' . $term . '%');
        });
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category ?? '—';
    }

    /** النماذج المدربة المرتبطة بهذه المجموعة */
    public function communityModels()
    {
        return $this->hasMany(CommunityModel::class, 'community_dataset_id');
    }
}
