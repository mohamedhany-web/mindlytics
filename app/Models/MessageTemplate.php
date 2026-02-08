<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'content',
        'type',
        'variables',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المنشئ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * scope للقوالب النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * scope للقوالب حسب النوع
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * تطبيق القالب مع المتغيرات
     */
    public function render(array $variables = []): string
    {
        $content = $this->content;

        // استبدال المتغيرات في النص
        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }

        return $content;
    }

    /**
     * أنواع القوالب المتاحة
     */
    public static function getTypes(): array
    {
        return [
            'student_report' => 'تقرير طالب',
            'exam_result' => 'نتيجة امتحان',
            'general_announcement' => 'إعلان عام',
            'parent_report' => 'تقرير لولي الأمر',
            'course_reminder' => 'تذكير بالكورس',
            'payment_reminder' => 'تذكير بالدفع',
            'welcome_message' => 'رسالة ترحيب',
        ];
    }

    /**
     * المتغيرات المتاحة لكل نوع
     */
    public static function getAvailableVariables(string $type): array
    {
        $variables = [
            'student_report' => [
                'student_name', 'courses_count', 'avg_score', 'total_exams', 'month_name'
            ],
            'exam_result' => [
                'student_name', 'exam_title', 'score', 'total_marks', 'percentage', 'status'
            ],
            'general_announcement' => [
                'student_name', 'announcement_title', 'date'
            ],
            'parent_report' => [
                'parent_name', 'student_name', 'month_name', 'overall_grade', 'courses_progress'
            ],
            'course_reminder' => [
                'student_name', 'course_title', 'next_lesson', 'due_date'
            ],
            'payment_reminder' => [
                'student_name', 'course_title', 'amount', 'due_date'
            ],
            'welcome_message' => [
                'student_name', 'platform_name', 'support_phone'
            ],
        ];

        return $variables[$type] ?? [];
    }
}