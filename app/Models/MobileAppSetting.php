<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * إعدادات واجهة تطبيق الطلاب (محتوى يحرره الأدمن).
 *
 * @property string|null $welcome_title_ar
 * @property string|null $welcome_title_en
 */
class MobileAppSetting extends Model
{
    protected $fillable = [
        'welcome_title_ar',
        'welcome_title_en',
        'welcome_subtitle_ar',
        'welcome_subtitle_en',
        'mission_headline_ar',
        'mission_headline_en',
        'mission_body_ar',
        'mission_body_en',
        'no_subscription_title_ar',
        'no_subscription_title_en',
        'no_subscription_body_ar',
        'no_subscription_body_en',
        'catalog_web_path',
        'chats_full_url',
    ];

    /**
     * @return array<string, string|null>
     */
    public static function defaultAttributes(): array
    {
        return [
            'welcome_title_ar' => 'مرحباً في Mindlytics',
            'welcome_title_en' => 'Welcome to Mindlytics',
            'welcome_subtitle_ar' => 'مسارك التعليمي والمجتمع في مكان واحد',
            'welcome_subtitle_en' => 'Your learning path and community in one place',
            'mission_headline_ar' => 'مهمتك اليوم',
            'mission_headline_en' => 'Today’s mission',
            'mission_body_ar' => 'تابع دروسك وخطط تقدمك من التطبيق بعد اشتراكك في كورس.',
            'mission_body_en' => 'Follow your lessons and track progress from the app once you enroll in a course.',
            'no_subscription_title_ar' => 'اشترك في كورس للبدء',
            'no_subscription_title_en' => 'Enroll in a course to begin',
            'no_subscription_body_ar' => 'يمكنك تصفح الكورسات المتاحة على الموقع والاشتراك، ثم ستظهر بيانات تعلمك هنا.',
            'no_subscription_body_en' => 'Browse available courses on the website and subscribe — your learning data will appear here.',
            'catalog_web_path' => '/courses',
        ];
    }

    public static function singleton(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::create(static::defaultAttributes());
    }
}
