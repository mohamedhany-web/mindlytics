<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseCommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'body',
        'is_pinned',
        'edited_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'edited_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (CourseCommunityPost $post) {
            foreach ($post->images()->get() as $img) {
                try {
                    Storage::disk($img->disk ?? 'public')->delete($img->path);
                } catch (\Throwable) {
                    //
                }
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(CourseCommunityComment::class, 'post_id');
    }

    public function reactions()
    {
        return $this->morphMany(CourseCommunityReaction::class, 'reactable');
    }

    public function images()
    {
        return $this->hasMany(CourseCommunityPostImage::class, 'post_id')->orderBy('sort_order');
    }

    /**
     * منشورات يظهر مؤلفها في مجتمع الكورس: طالب مسجّل بنشاط في هذا الكورس، أو إداري/مدرب ينشر للفوج.
     */
    public function scopeWhereAuthorVisibleInCourse(Builder $query, int $advancedCourseId): Builder
    {
        return $query->where(function (Builder $w) use ($advancedCourseId) {
            $w->whereHas('user', function (Builder $uq) {
                $uq->whereIn('role', ['super_admin', 'admin', 'instructor']);
            })->orWhereHas('user', function (Builder $uq) use ($advancedCourseId) {
                $uq->whereHas('courseEnrollments', function (Builder $eq) use ($advancedCourseId) {
                    $eq->where('advanced_course_id', $advancedCourseId)
                        ->where('status', 'active');
                });
            });
        });
    }

    /**
     * هل يُسمح بعرض هذا المنشور لطلاب المجتمع (بعد التحقق من اشتراك المشاهد في course_id)؟
     */
    public function isAuthorVisibleInCommunityCohort(): bool
    {
        if (! $this->user_id) {
            return false;
        }
        // لا تعتمد على علاقة user المحمّلة بـ select جزئي (قد يُستثنى role فيُرفض منشور المدرب/الإدارة).
        $role = User::query()->whereKey($this->user_id)->value('role');
        if ($role !== null && in_array($role, ['super_admin', 'admin', 'instructor'], true)) {
            return true;
        }

        $author = $this->relationLoaded('user') ? $this->user : $this->user()->first();
        if ($author === null) {
            return false;
        }

        return $author->isEnrolledIn((int) $this->course_id);
    }
}
