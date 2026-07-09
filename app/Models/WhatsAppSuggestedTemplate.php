<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSuggestedTemplate extends Model
{
    protected $table = 'whatsapp_suggested_templates';

    protected $fillable = [
        'key',
        'title',
        'category',
        'language',
        'body',
        'help',
        'variables',
        'is_active',
        'sort_order',
        'meta_template_id',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /** @return array<string, string> */
    public static function categoryLabels(): array
    {
        return [
            'intro' => 'ترحيب وتعريف',
            'qualification' => 'تأهيل العميل',
            'followup' => 'متابعة',
            'pricing' => 'تسعير وعرض',
            'payment' => 'دفع واشتراك',
            'reminder' => 'تذكير',
            'faq' => 'أسئلة شائعة',
            'objection' => 'اعتراضات',
            'closing' => 'إغلاق البيع',
            'policy' => 'سياسات وشروط',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categoryLabels()[$this->category] ?? $this->category;
    }

    public function metaTemplate(): BelongsTo
    {
        return $this->belongsTo(WhatsAppMetaTemplate::class, 'meta_template_id');
    }
}

