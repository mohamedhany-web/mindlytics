<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'message',
        'type',
        'status',
        'response_data',
        'whatsapp_message_id',
        'sent_at',
        'delivered_at',
        'read_at',
        'template_name',
        'template_params',
        'error_message',
    ];

    protected $casts = [
        'response_data' => 'array',
        'template_params' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * scope للرسائل المرسلة
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * scope للرسائل الفاشلة
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * scope للرسائل حسب النوع
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * الحصول على حالة الرسالة بالعربية
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'sent' => 'تم الإرسال',
            'delivered' => 'تم التسليم',
            'read' => 'تم القراءة',
            'failed' => 'فشل الإرسال',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * الحصول على لون حالة الرسالة
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'sent' => 'blue',
            'delivered' => 'green',
            'read' => 'purple',
            'failed' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }
}