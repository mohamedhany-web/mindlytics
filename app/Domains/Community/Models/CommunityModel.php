<?php

namespace App\Domains\Community\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class CommunityModel extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'methodology_steps',
        'community_dataset_id',
        'performance_metrics',
        'license',
        'usage_instructions',
        'file_path',
        'file_size',
        'files',
        'downloads_count',
        'status',
        'is_active',
        'sort_order',
        'created_by_user_id',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /** تراخيص شائعة للعرض والاختيار */
    public const LICENSES = [
        'MIT' => 'MIT',
        'Apache-2.0' => 'Apache 2.0',
        'CC-BY-4.0' => 'Creative Commons BY 4.0',
        'personal' => 'استخدام شخصي / تعليمي فقط',
        'other' => 'أخرى',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'performance_metrics' => 'array',
            'files' => 'array',
        ];
    }

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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function dataset()
    {
        return $this->belongsTo(CommunityDataset::class, 'community_dataset_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

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

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || trim($term) === '') {
            return $query;
        }
        $term = trim($term);
        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', '%' . $term . '%')
                ->orWhere('description', 'like', '%' . $term . '%')
                ->orWhere('methodology_steps', 'like', '%' . $term . '%');
        });
    }
}
