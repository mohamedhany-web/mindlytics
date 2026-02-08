<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'response_code',
        'duration',
    ];
    
    /**
     * Override create method to handle session_id dynamically
     */
    public static function create(array $attributes = [])
    {
        // محاولة إضافة session_id إذا كان مفقوداً والـ session نشط
        if (!isset($attributes['session_id']) && session()->isStarted()) {
            try {
                $attributes['session_id'] = session()->getId();
            } catch (\Exception $e) {
                // تجاهل إذا فشل الحصول على session
            }
        }
        
        try {
            return parent::create($attributes);
        } catch (\Exception $e) {
            // إذا فشل بسبب session_id (عمود غير موجود)، حاول مرة أخرى بدونه
            if (isset($attributes['session_id']) && (str_contains($e->getMessage(), 'session_id') || str_contains($e->getMessage(), 'no such column'))) {
                unset($attributes['session_id']);
                return parent::create($attributes);
            }
            throw $e;
        }
    }

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على النموذج المرتبط
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * تسجيل نشاط جديد
     */
    public static function logActivity($action, $model = null, $oldValues = null, $newValues = null, $description = null)
    {
        $data = [
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'response_code' => null,
            'duration' => null,
        ];
        
        // إضافة session_id فقط إذا كان العمود موجوداً والجلسة متاحة
        if (Schema::hasColumn('activity_logs', 'session_id')) {
            try {
                if (session()->isStarted()) {
                    $data['session_id'] = session()->getId();
                }
            } catch (\Throwable $e) {
                // تجاهل فشل الجلسة حتى لا يكسر طلب المستخدم
            }
        }
        
        return self::create($data);
    }

    /**
     * الحصول على وصف النشاط بالعربية
     */
    public function getActionDescriptionAttribute()
    {
        // إذا كان هناك وصف مخصص، استخدمه
        if ($this->description) {
            return $this->description;
        }

        $descriptions = [
            // المستخدمين
            'user_created' => 'إنشاء مستخدم جديد',
            'user_updated' => 'تحديث بيانات المستخدم',
            'user_deleted' => 'حذف مستخدم',
            'user_restored' => 'استعادة مستخدم محذوف',
            'user_force_deleted' => 'حذف مستخدم نهائياً',
            'user_profile_viewed' => 'عرض ملف المستخدم',

            // الكورسات
            'course_created' => 'إنشاء كورس جديد',
            'course_updated' => 'تحديث كورس',
            'course_status_changed' => 'تغيير حالة الكورس',
            'course_price_changed' => 'تغيير سعر الكورس',
            'course_deleted' => 'حذف كورس',
            'course_restored' => 'استعادة كورس محذوف',
            'course_viewed' => 'عرض تفاصيل كورس',

            // الدروس
            'lesson_created' => 'إنشاء درس جديد',
            'lesson_updated' => 'تحديث درس',
            'lesson_deleted' => 'حذف درس',
            'lesson_activity' => 'نشاط في الدروس',
            'lesson_watched' => 'مشاهدة درس',

            // الامتحانات
            'exam_created' => 'إنشاء امتحان جديد',
            'exam_updated' => 'تحديث امتحان',
            'exam_deleted' => 'حذف امتحان',
            'exam_restored' => 'استعادة امتحان محذوف',
            'exam_viewed' => 'عرض تفاصيل امتحان',
            'exam_status_changed' => 'تغيير حالة الامتحان',
            'exam_published_status_changed' => 'تغيير حالة نشر الامتحان',
            'exam_questions_managed' => 'إدارة أسئلة امتحان',
            'exam_statistics_viewed' => 'عرض إحصائيات امتحان',
            'exam_previewed' => 'معاينة امتحان',

            // محاولات الامتحان
            'exam_attempt_started' => 'بدء محاولة امتحان',
            'exam_attempt_submitted' => 'تسليم امتحان',
            'exam_attempt_auto_submitted' => 'تسليم امتحان تلقائياً',
            'exam_attempt_updated' => 'تحديث محاولة امتحان',
            'exam_attempt_deleted' => 'حذف محاولة امتحان',
            'exam_answer_saved' => 'حفظ إجابة سؤال',
            'exam_tab_switch' => 'تبديل تبويب أثناء الامتحان',
            'exam_result_viewed' => 'عرض نتائج امتحان',

            // الأسئلة
            'question_created' => 'إنشاء سؤال جديد',
            'question_updated' => 'تحديث سؤال',
            'question_deleted' => 'حذف سؤال',

            // التسجيل والدخول
            'login' => 'تسجيل دخول',
            'logout' => 'تسجيل خروج',
            'failed_login' => 'محاولة دخول فاشلة',
            'password_reset' => 'إعادة تعيين كلمة المرور',

            // التسجيلات والطلبات
            'enrollment_created' => 'تسجيل في كورس',
            'enrollment_cancelled' => 'إلغاء تسجيل في كورس',
            'order_created' => 'إنشاء طلب جديد',
            'order_approved' => 'الموافقة على طلب',
            'order_rejected' => 'رفض طلب',

            // الإشعارات
            'notification_sent' => 'إرسال إشعار',
            'notification_read' => 'قراءة إشعار',

            // أنشطة عامة
            'page_visited' => 'زيارة صفحة',
            'data_created' => 'إنشاء بيانات',
            'data_updated' => 'تحديث بيانات',
            'data_deleted' => 'حذف بيانات',
            'file_uploaded' => 'رفع ملف',
            'file_downloaded' => 'تحميل ملف',
        ];

        return $descriptions[$this->action] ?? $this->action;
    }

    /**
     * الحصول على لون النشاط حسب النوع
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'user_created' => 'green',
            'user_updated' => 'blue',
            'user_deleted' => 'red',
            'course_created' => 'purple',
            'course_updated' => 'blue',
            'course_status_changed' => 'yellow',
            'course_deleted' => 'red',
            'exam_created' => 'indigo',
            'exam_attempt_started' => 'blue',
            'exam_attempt_submitted' => 'green',
            'video_token_generated' => 'cyan',
            'video_watched' => 'teal',
            'login' => 'green',
            'logout' => 'gray',
        ];

        return $colors[$this->action] ?? 'gray';
    }

    /**
     * الحصول على أيقونة النشاط
     */
    public function getActionIconAttribute()
    {
        $icons = [
            'user_created' => 'fa-user-plus',
            'user_updated' => 'fa-user-edit',
            'user_deleted' => 'fa-user-times',
            'course_created' => 'fa-plus-circle',
            'course_updated' => 'fa-edit',
            'course_status_changed' => 'fa-toggle-on',
            'course_deleted' => 'fa-trash',
            'exam_created' => 'fa-clipboard-list',
            'exam_attempt_started' => 'fa-play',
            'exam_attempt_submitted' => 'fa-check',
            'video_token_generated' => 'fa-key',
            'video_watched' => 'fa-play',
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
        ];

        return $icons[$this->action] ?? 'fa-info-circle';
    }
}