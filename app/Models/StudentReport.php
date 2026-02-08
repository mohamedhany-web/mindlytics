<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'parent_id',
        'report_month',
        'report_type',
        'report_data',
        'sent_via',
        'sent_at',
        'status',
        'error_message',
        'generated_by',
    ];

    protected $casts = [
        'report_data' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * العلاقة مع الطالب
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * علاقة مع المستخدم (alias)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * العلاقة مع ولي الأمر
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * العلاقة مع منشئ التقرير
     */
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * scope للتقارير الشهرية
     */
    public function scopeMonthly($query)
    {
        return $query->where('report_type', 'monthly');
    }

    /**
     * scope للتقارير المرسلة
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * scope للتقارير حسب الشهر
     */
    public function scopeForMonth($query, $month)
    {
        return $query->where('report_month', $month);
    }

    /**
     * الحصول على اسم الشهر بالعربية
     */
    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->report_month)->locale('ar')->format('F Y');
    }

    /**
     * الحصول على حالة التقرير بالعربية
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'sent' => 'تم الإرسال',
            'failed' => 'فشل الإرسال',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}