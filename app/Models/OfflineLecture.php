<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class OfflineLecture extends Model
{
    protected $fillable = [
        'offline_course_id',
        'group_id',
        'offline_group_session_id',
        'instructor_id',
        'title',
        'description',
        'session_agenda',
        'offline_attendee_mindmap',
        'scheduled_at',
        'meeting_url',
        'duration_minutes',
        'recording_url',
        'recording_path',
        'recording_disk',
        'recording_original_name',
        'recording_mime',
        'recording_size',
        'download_links',
        'attachments',
        'notes',
        'order',
        'is_active',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'download_links' => 'array',
        'attachments' => 'array',
        'is_active' => 'boolean',
        'recording_size' => 'integer',
    ];

    public function hasStoredRecording(): bool
    {
        return filled($this->recording_path);
    }

    public function hasPlayableRecording(): bool
    {
        return $this->hasStoredRecording() || filled($this->recording_url);
    }

    /**
     * رابط تشغيل HTML5 (CDN عام أو رابط موقّع مؤقت من R2).
     */
    public function playbackUrl(?\DateTimeInterface $expires = null): ?string
    {
        if ($this->hasStoredRecording()) {
            $disk = $this->recording_disk ?: offline_lecture_recordings_disk();
            $cdnBase = rtrim((string) config('filesystems.disks.'.$disk.'.url', ''), '/');

            // إذا وُجد دومين CDN عام (مثل cdn.yourdomain.com) استخدمه مباشرة
            if ($cdnBase !== '' && ! str_contains($cdnBase, 'r2.cloudflarestorage.com')) {
                return $cdnBase.'/'.ltrim($this->recording_path, '/');
            }

            $url = storage_inline_media_url($disk, $this->recording_path, $expires ?? now()->addHours(4));
            if ($url !== '') {
                return $url;
            }
        }

        $external = trim((string) ($this->recording_url ?? ''));

        return $external !== '' ? $external : null;
    }

    /**
     * بث/توجيه ملف التسجيل المخزَّن (R2 موقّع أو قرص محلي) للمشغّل HTML5.
     */
    public function streamStoredRecordingResponse(): SymfonyResponse
    {
        if (! $this->hasStoredRecording()) {
            abort(404, 'لا يوجد تسجيل لهذه المحاضرة');
        }

        $disk = $this->recording_disk ?: offline_lecture_recordings_disk();
        $path = (string) $this->recording_path;
        $mime = $this->recording_mime ?: 'video/mp4';
        $filename = $this->recording_original_name ?: basename($path);

        try {
            if (! Storage::disk($disk)->exists($path)) {
                abort(404, 'ملف التسجيل غير موجود');
            }
        } catch (\Throwable $e) {
            report($e);
            abort(404, 'ملف التسجيل غير متاح');
        }

        $url = $this->playbackUrl(now()->addHours(4));
        if (is_string($url) && $url !== '' && preg_match('#^https?://#i', $url)) {
            $appHost = strtolower((string) (parse_url((string) config('app.url'), PHP_URL_HOST) ?: ''));
            $urlHost = strtolower((string) (parse_url($url, PHP_URL_HOST) ?: ''));
            // روابط R2/CDN الخارجية → تحويل سريع (لا نمرّر الجيجابايت عبر PHP)
            if ($urlHost !== '' && ($appHost === '' || $urlHost !== $appHost)) {
                return redirect()->away($url);
            }
        }

        return Storage::disk($disk)->response($path, $filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $filename).'"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'group_id');
    }

    public function groupSession(): BelongsTo
    {
        return $this->belongsTo(OfflineGroupSession::class, 'offline_group_session_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function offlineCurriculumItems(): MorphMany
    {
        return $this->morphMany(OfflineCurriculumItem::class, 'item');
    }

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(
            OfflineCourseResource::class,
            'offline_lecture_resource',
            'offline_lecture_id',
            'offline_course_resource_id'
        )->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('scheduled_at')->orderBy('id');
    }
}
