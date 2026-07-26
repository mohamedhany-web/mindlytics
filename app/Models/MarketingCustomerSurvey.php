<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCustomerSurvey extends Model
{
    use HasFactory;

    public const OTHER = 'other';

    protected $fillable = [
        'user_id',
        'advanced_course_id',
        'name',
        'email',
        'phone',
        'governorate',
        'job',
        'job_other',
        'heard_from',
        'heard_from_other',
        'interested_in',
        'opinion',
        'needed_courses',
        'recommendations',
        'reward_coupon_id',
        'reward_percentage',
        'reward_granted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'reward_percentage' => 'integer',
        'reward_granted_at' => 'datetime',
    ];

    /**
     * المحافظات المصرية.
     */
    public static function governorates(): array
    {
        return [
            'cairo' => 'القاهرة',
            'giza' => 'الجيزة',
            'alexandria' => 'الإسكندرية',
            'qalyubia' => 'القليوبية',
            'sharqia' => 'الشرقية',
            'dakahlia' => 'الدقهلية',
            'beheira' => 'البحيرة',
            'gharbia' => 'الغربية',
            'monufia' => 'المنوفية',
            'kafr_el_sheikh' => 'كفر الشيخ',
            'damietta' => 'دمياط',
            'port_said' => 'بورسعيد',
            'ismailia' => 'الإسماعيلية',
            'suez' => 'السويس',
            'north_sinai' => 'شمال سيناء',
            'south_sinai' => 'جنوب سيناء',
            'red_sea' => 'البحر الأحمر',
            'matrouh' => 'مطروح',
            'fayoum' => 'الفيوم',
            'beni_suef' => 'بني سويف',
            'minya' => 'المنيا',
            'assiut' => 'أسيوط',
            'sohag' => 'سوهاج',
            'qena' => 'قنا',
            'luxor' => 'الأقصر',
            'aswan' => 'أسوان',
            'new_valley' => 'الوادي الجديد',
            'outside_egypt' => 'خارج مصر',
        ];
    }

    /**
     * الوظائف / المجالات.
     */
    public static function jobs(): array
    {
        return [
            'student' => 'طالب',
            'fresh_graduate' => 'خريج حديث',
            'job_seeker' => 'باحث عن عمل',
            'data_analyst' => 'محلل بيانات',
            'data_scientist' => 'عالم بيانات / تعلم آلة',
            'software_developer' => 'مطور برمجيات',
            'engineer' => 'مهندس',
            'doctor_pharmacist' => 'طبيب / صيدلي',
            'accountant' => 'محاسب / مالي',
            'teacher' => 'مدرس / أكاديمي',
            'marketing_sales' => 'تسويق / مبيعات',
            'hr' => 'موارد بشرية',
            'administrative' => 'إداري',
            'business_owner' => 'صاحب عمل / رائد أعمال',
            'freelancer' => 'عمل حر (Freelancer)',
            self::OTHER => 'أخرى',
        ];
    }

    /**
     * قنوات المعرفة بالأكاديمية.
     */
    public static function heardFromOptions(): array
    {
        return [
            'friend' => 'صديق أو زميل',
            'facebook' => 'فيسبوك',
            'instagram' => 'إنستجرام',
            'linkedin' => 'لينكدإن',
            'tiktok' => 'تيك توك',
            'youtube' => 'يوتيوب',
            'google' => 'بحث جوجل',
            'whatsapp' => 'واتساب',
            'paid_ad' => 'إعلان مدفوع',
            'workshop' => 'ورشة أو حدث',
            'instructor' => 'أحد المدربين',
            'university' => 'الجامعة / الكلية',
            self::OTHER => 'أخرى',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function rewardCoupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'reward_coupon_id');
    }

    public function getGovernorateLabelAttribute(): string
    {
        return static::governorates()[$this->governorate] ?? (string) $this->governorate;
    }

    public function getJobLabelAttribute(): string
    {
        if ($this->job === self::OTHER && filled($this->job_other)) {
            return (string) $this->job_other;
        }

        return static::jobs()[$this->job] ?? (string) $this->job;
    }

    public function getHeardFromLabelAttribute(): string
    {
        if ($this->heard_from === self::OTHER && filled($this->heard_from_other)) {
            return (string) $this->heard_from_other;
        }

        return static::heardFromOptions()[$this->heard_from] ?? (string) $this->heard_from;
    }
}
