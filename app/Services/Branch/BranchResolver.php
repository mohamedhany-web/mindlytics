<?php

namespace App\Services\Branch;

use App\Models\Branch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class BranchResolver
{
    /**
     * مطابقة الـ Host مع فرع نشط: دومين مخصص أو {slug}.{دومين التطبيق}.
     * يُخزَّن مؤقتاً عند ضبط `branches.resolve_cache_ttl` > 0.
     */
    public static function fromHost(string $host): ?Branch
    {
        $host = strtolower(trim($host));
        if ($host === '') {
            return null;
        }

        $ttl = (int) Config::get('branches.resolve_cache_ttl', 0);
        if ($ttl > 0) {
            return Cache::remember(
                self::cacheKeyForHost($host),
                now()->addSeconds($ttl),
                fn () => self::resolveFromHostUncached($host)
            );
        }

        return self::resolveFromHostUncached($host);
    }

    /**
     * إبطال كاش المضيف بعد تعديل/حذف فرع.
     *
     * @param  bool  $includePreviousKeys  عند الحفظ: نُبطئ أيضاً المفاتيح القديمة إن تغيّر slug أو الدومين.
     */
    public static function forgetCachesForBranch(Branch $branch, bool $includePreviousKeys = true): void
    {
        foreach (self::hostLookupStringsForBranch($branch, $includePreviousKeys) as $lookupHost) {
            Cache::forget(self::cacheKeyForHost($lookupHost));
        }
    }

    /**
     * @return list<string>
     */
    private static function hostLookupStringsForBranch(Branch $branch, bool $includePreviousKeys): array
    {
        $hosts = [];

        if (is_string($branch->custom_domain) && $branch->custom_domain !== '') {
            $hosts[] = strtolower(trim($branch->custom_domain));
        }

        $appHost = Branch::defaultAppHost();
        if (is_string($appHost) && $appHost !== '' && is_string($branch->slug) && $branch->slug !== '') {
            $hosts[] = strtolower($branch->slug).'.'.strtolower($appHost);
        }

        if ($includePreviousKeys) {
            foreach (['custom_domain', 'slug'] as $field) {
                if (! $branch->wasChanged($field)) {
                    continue;
                }
                $orig = $branch->getOriginal($field);
                if ($field === 'custom_domain' && is_string($orig) && $orig !== '') {
                    $hosts[] = strtolower(trim($orig));
                }
                if ($field === 'slug' && is_string($orig) && $orig !== '' && is_string($appHost) && $appHost !== '') {
                    $hosts[] = strtolower($orig).'.'.strtolower($appHost);
                }
            }
        }

        return array_values(array_unique(array_filter($hosts)));
    }

    private static function cacheKeyForHost(string $host): string
    {
        return 'branches:resolve:v1:'.hash('sha256', strtolower(trim($host)));
    }

    private static function resolveFromHostUncached(string $host): ?Branch
    {
        $branch = Branch::query()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('custom_domain', $host)
            ->first();

        if ($branch) {
            return $branch;
        }

        $appHost = Branch::defaultAppHost();
        if (! is_string($appHost) || $appHost === '') {
            return null;
        }

        $appHost = strtolower($appHost);
        $suffix = '.'.$appHost;

        if ($host === $appHost || $host === 'www.'.$appHost || ! str_ends_with($host, $suffix)) {
            return null;
        }

        $sub = substr($host, 0, -strlen($suffix));
        if ($sub === '' || str_contains($sub, '.')) {
            return null;
        }

        $reserved = Config::get('branches.reserved_subdomains', []);
        if (in_array($sub, $reserved, true)) {
            return null;
        }

        return Branch::query()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('slug', $sub)
            ->first();
    }
}
