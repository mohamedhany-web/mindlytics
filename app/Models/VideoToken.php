<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VideoToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'token',
        'expires_at',
        'ip_address',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * العلاقة مع الدرس
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * إنشاء توكن جديد للفيديو
     */
    public static function generateToken($lessonId, $userId, $ipAddress = null)
    {
        return self::create([
            'lesson_id' => $lessonId,
            'user_id' => $userId,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(2), // صالح لمدة ساعتين
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * التحقق من صلاحية التوكن
     */
    public function isValid()
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * تعيين التوكن كمستخدم
     */
    public function markAsUsed()
    {
        $this->update(['is_used' => true]);
    }
}
