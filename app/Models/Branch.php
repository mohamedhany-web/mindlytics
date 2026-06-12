<?php

namespace App\Models;

use App\Services\Branch\BranchResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'custom_domain',
        'country_code',
        'timezone',
        'currency',
        'is_active',
        'primary_color',
        'logo_path',
        'internal_notes',
        'settings',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Branch $branch): void {
            BranchResolver::forgetCachesForBranch($branch, true);
        });

        static::deleted(function (Branch $branch): void {
            BranchResolver::forgetCachesForBranch($branch, false);
        });

        static::saving(function (Branch $branch): void {
            if ($branch->slug !== null && $branch->slug !== '') {
                $branch->slug = Str::lower(trim($branch->slug));
            }
            if ($branch->custom_domain !== null && $branch->custom_domain !== '') {
                $branch->custom_domain = Str::lower(trim($branch->custom_domain));
            }
            if ($branch->country_code !== null && $branch->country_code !== '') {
                $branch->country_code = Str::upper(trim($branch->country_code));
            }
            if ($branch->currency !== null && $branch->currency !== '') {
                $branch->currency = Str::upper(trim($branch->currency));
            }
        });
    }

    /**
     * المضيف الأساسي للتطبيق (لاقتراح روابط subdomain).
     */
    public static function defaultAppHost(): ?string
    {
        $url = config('app.url');
        if (!is_string($url) || $url === '') {
            return null;
        }

        return parse_url($url, PHP_URL_HOST) ?: null;
    }

    public function suggestedSubdomainUrl(): ?string
    {
        $host = self::defaultAppHost();
        if (!$host || !$this->slug) {
            return null;
        }

        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'https';

        return $scheme.'://'.$this->slug.'.'.$host;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function studentCourseEnrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * معاملات المحاسبة (جدول transactions).
     */
    public function accountingTransactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function advancedCourses()
    {
        return $this->hasMany(AdvancedCourse::class);
    }

    public function offlineCourses()
    {
        return $this->hasMany(OfflineCourse::class);
    }

    /**
     * معرّف فرع «الأكاديمية الأساسية» (المركز): نفس منطق التعيين الافتراضي — slug `main` ثم أقدم فرع نشط.
     * بيانات المركز تُخزَّن تحت هذا الـ branch_id؛ الفروع الأخرى تمثل الامتدادات.
     */
    public static function centralAcademyBranchId(): ?int
    {
        return self::defaultAssignableId();
    }

    public static function isCentralAcademyBranch(?int $branchId): bool
    {
        if ($branchId === null) {
            return false;
        }

        $central = self::centralAcademyBranchId();

        return $central !== null && (int) $branchId === (int) $central;
    }

    /**
     * معرّف الفرع الافتراضي لتعيين مستخدمين جدد عند عدم تحديد فرع (slug: main أو أقدم فرع نشط).
     */
    public static function defaultAssignableId(): ?int
    {
        $q = static::query()->whereNull('deleted_at');

        return $q->clone()->where('slug', 'main')->value('id')
            ?? $q->clone()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->value('id')
            ?? $q->orderBy('id')->value('id');
    }
}
