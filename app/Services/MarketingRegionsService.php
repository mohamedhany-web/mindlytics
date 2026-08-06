<?php

namespace App\Services;

use App\Models\MarketingRegionDailyStat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarketingRegionsService
{
    public function __construct(
        protected GeoIpLookupService $geoIp
    ) {}

    /**
     * @return array{
     *   from: string,
     *   to: string,
     *   metric: string,
     *   summary: array<string, int>,
     *   countries: list<array<string, mixed>>,
     *   map: array<string, int>,
     *   governorates: list<array{name: string, count: int}>,
     *   recent_logins: list<array<string, mixed>>,
     *   phone_countries: list<array<string, mixed>>
     * }
     */
    public function dashboard(?string $from = null, ?string $to = null, string $metric = 'combined'): array
    {
        $toDate = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay();
        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        $metric = in_array($metric, ['visits', 'registrations', 'logins', 'combined'], true)
            ? $metric
            : 'combined';

        $registrations = $this->registrationsByCountry($fromDate, $toDate);
        $logins = $this->loginsByCountry($fromDate, $toDate);
        $visits = $this->visitsByCountry($fromDate, $toDate);

        $merged = $this->mergeCountrySeries($registrations, $logins, $visits, $metric);
        $map = [];
        foreach ($merged as $row) {
            $map[$row['country_code']] = (int) $row['value'];
        }

        return [
            'from' => $fromDate->toDateString(),
            'to' => $toDate->toDateString(),
            'metric' => $metric,
            'summary' => [
                'registrations' => (int) array_sum(array_column($registrations, 'count')),
                'logins' => (int) array_sum(array_column($logins, 'count')),
                'visits' => (int) array_sum(array_column($visits, 'count')),
                'countries' => count($merged),
            ],
            'countries' => $merged,
            'map' => $map,
            'governorates' => $this->egyptGovernorates($fromDate, $toDate),
            'recent_logins' => $this->recentLoginSamples($fromDate, $toDate, 25),
            'phone_countries' => $registrations,
        ];
    }

    /**
     * Infer registration country from phone dial codes.
     *
     * @return list<array{country_code: string, name_ar: string, name_en: string, count: int}>
     */
    public function registrationsByCountry(Carbon $from, Carbon $to): array
    {
        if (! Schema::hasTable('users')) {
            return [];
        }

        $dialMap = $this->phoneDialMap();
        $counts = [];

        $query = User::query()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->whereBetween('created_at', [$from, $to]);

        if (Schema::hasColumn('users', 'role')) {
            $query->where(function ($q) {
                $q->where('role', 'student')
                    ->orWhereNull('role');
            });
        }

        $query->select(['id', 'phone'])->orderBy('id')->chunkById(500, function ($users) use (&$counts, $dialMap) {
            foreach ($users as $user) {
                $phone = (string) $user->phone;
                $code = $this->countryFromPhone($phone, $dialMap);
                $counts[$code] = ($counts[$code] ?? 0) + 1;
            }
        });

        return $this->formatCountryCounts($counts, $dialMap);
    }

    /**
     * Login activity origins from activity_logs IPs.
     *
     * @return list<array{country_code: string, name_ar: string, name_en: string, count: int, cities: list<string>}>
     */
    public function loginsByCountry(Carbon $from, Carbon $to): array
    {
        if (! Schema::hasTable('activity_logs')) {
            return [];
        }

        $rows = DB::table('activity_logs')
            ->where('action', 'login')
            ->whereNotNull('ip_address')
            ->where('ip_address', '!=', '')
            ->whereBetween('created_at', [$from, $to])
            ->select('ip_address', DB::raw('COUNT(*) as cnt'))
            ->groupBy('ip_address')
            ->orderByDesc('cnt')
            ->limit(300)
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $lookups = $this->geoIp->lookupMany($rows->pluck('ip_address')->all());
        $counts = [];
        $cities = [];

        foreach ($rows as $row) {
            $ip = (string) $row->ip_address;
            $geo = $lookups[$ip] ?? null;
            $code = strtoupper((string) ($geo['country_code'] ?? '')) ?: 'XX';
            $counts[$code] = ($counts[$code] ?? 0) + (int) $row->cnt;
            if (! empty($geo['city'])) {
                $cities[$code][$geo['city']] = true;
            }
        }

        $formatted = $this->formatCountryCounts($counts, $this->phoneDialMap());
        foreach ($formatted as &$row) {
            $row['cities'] = array_keys($cities[$row['country_code']] ?? []);
        }
        unset($row);

        return $formatted;
    }

    /**
     * Public page visits tracked by middleware (from install date forward).
     *
     * @return list<array{country_code: string, name_ar: string, name_en: string, count: int}>
     */
    public function visitsByCountry(Carbon $from, Carbon $to): array
    {
        if (! Schema::hasTable('marketing_region_daily_stats')) {
            return [];
        }

        $rows = MarketingRegionDailyStat::query()
            ->whereBetween('stat_date', [$from->toDateString(), $to->toDateString()])
            ->select('country_code', DB::raw('SUM(visits) as cnt'))
            ->groupBy('country_code')
            ->orderByDesc('cnt')
            ->get();

        $counts = [];
        foreach ($rows as $row) {
            $code = strtoupper((string) $row->country_code) ?: 'XX';
            $counts[$code] = (int) $row->cnt;
        }

        return $this->formatCountryCounts($counts, $this->phoneDialMap());
    }

    /**
     * Automatic Egypt audience presence from surveys, phones, addresses, and EG login IPs.
     *
     * @return array{
     *   summary: array<string, int>,
     *   governorates: list<array{key: string, name: string, count: int, sources: list<string>}>,
     *   cities: list<array{name: string, count: int}>,
     *   top: list<array{name: string, count: int}>,
     *   samples: list<array<string, mixed>>
     * }
     */
    public function egyptPresenceInsights(?Carbon $from = null, ?Carbon $to = null): array
    {
        $to = $to?->copy()->endOfDay() ?? now()->endOfDay();
        $from = $from?->copy()->startOfDay() ?? now()->subDays(90)->startOfDay();

        $labels = class_exists(\App\Models\MarketingCustomerSurvey::class)
            ? \App\Models\MarketingCustomerSurvey::governorates()
            : [];

        /** @var array<string, array{key: string, name: string, count: int, sources: array<string, bool>}> $gov */
        $gov = [];
        $bumpGov = function (string $key, int $n, string $source) use (&$gov, $labels) {
            $key = strtolower(trim($key));
            if ($key === '' || $key === 'outside_egypt') {
                return;
            }
            if (! isset($gov[$key])) {
                $gov[$key] = [
                    'key' => $key,
                    'name' => $labels[$key] ?? $key,
                    'count' => 0,
                    'sources' => [],
                ];
            }
            $gov[$key]['count'] += $n;
            $gov[$key]['sources'][$source] = true;
        };

        // 1) Surveys (strongest signal)
        $surveyPeople = 0;
        if (Schema::hasTable('marketing_customer_surveys')
            && Schema::hasColumn('marketing_customer_surveys', 'governorate')) {
            $rows = DB::table('marketing_customer_surveys')
                ->whereNotNull('governorate')
                ->where('governorate', '!=', '')
                ->where('governorate', '!=', 'outside_egypt')
                ->whereBetween('created_at', [$from, $to])
                ->select('governorate', DB::raw('COUNT(*) as cnt'))
                ->groupBy('governorate')
                ->get();
            foreach ($rows as $r) {
                $surveyPeople += (int) $r->cnt;
                $bumpGov((string) $r->governorate, (int) $r->cnt, 'survey');
            }
        }

        // 2) users.address text match against Arabic governorate names
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'address')) {
            $users = User::query()
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->whereBetween('created_at', [$from, $to])
                ->when(Schema::hasColumn('users', 'role'), fn ($q) => $q->where(function ($qq) {
                    $qq->where('role', 'student')->orWhereNull('role');
                }))
                ->select(['id', 'address'])
                ->limit(5000)
                ->get();

            foreach ($users as $user) {
                $matched = $this->matchGovernorateFromText((string) $user->address, $labels);
                if ($matched) {
                    $bumpGov($matched, 1, 'address');
                }
            }
        }

        // 3) Egyptian phone registrations volume (national, not governorate)
        $egyptianPhones = 0;
        if (Schema::hasTable('users')) {
            $egyptianPhones = (int) User::query()
                ->whereNotNull('phone')
                ->where(function ($q) {
                    $q->where('phone', 'like', '+20%')
                        ->orWhere('phone', 'like', '20%');
                })
                ->whereBetween('created_at', [$from, $to])
                ->when(Schema::hasColumn('users', 'role'), fn ($q) => $q->where(function ($qq) {
                    $qq->where('role', 'student')->orWhereNull('role');
                }))
                ->count();
        }

        // 4) Login IPs resolved to EG cities / regionName
        $cities = [];
        $samples = [];
        if (Schema::hasTable('activity_logs')) {
            $ipRows = DB::table('activity_logs')
                ->where('action', 'login')
                ->whereNotNull('ip_address')
                ->where('ip_address', '!=', '')
                ->whereBetween('created_at', [$from, $to])
                ->select('ip_address', DB::raw('COUNT(*) as cnt'))
                ->groupBy('ip_address')
                ->orderByDesc('cnt')
                ->limit(200)
                ->get();

            $lookups = $this->geoIp->lookupMany($ipRows->pluck('ip_address')->all(), 50);
            foreach ($ipRows as $row) {
                $ip = (string) $row->ip_address;
                $geo = $lookups[$ip] ?? [];
                if (strtoupper((string) ($geo['country_code'] ?? '')) !== 'EG') {
                    continue;
                }
                $city = trim((string) ($geo['city'] ?? ''));
                $region = trim((string) ($geo['region_name'] ?? ''));
                $place = $city !== '' ? $city : $region;
                if ($place !== '') {
                    $cities[$place] = ($cities[$place] ?? 0) + (int) $row->cnt;
                }
                $govKey = $this->matchGovernorateFromText($region.' '.$city, $labels);
                if ($govKey) {
                    $bumpGov($govKey, (int) $row->cnt, 'login_ip');
                }
            }

            $samples = $this->recentLoginSamples($from, $to, 15);
            $samples = array_values(array_filter(
                $samples,
                static fn ($s) => ($s['country_code'] ?? null) === 'EG' || ($s['phone_country'] ?? null) === 'EG'
            ));
        }

        $governorates = array_map(static function ($row) {
            $row['sources'] = array_keys($row['sources']);

            return $row;
        }, array_values($gov));
        usort($governorates, static fn ($a, $b) => $b['count'] <=> $a['count']);

        $cityList = [];
        foreach ($cities as $name => $count) {
            $cityList[] = ['name' => $name, 'count' => $count];
        }
        usort($cityList, static fn ($a, $b) => $b['count'] <=> $a['count']);

        $top = array_slice(array_map(static fn ($g) => [
            'name' => $g['name'],
            'count' => $g['count'],
        ], $governorates), 0, 10);

        return [
            'summary' => [
                'governorates' => count($governorates),
                'survey_people' => $surveyPeople,
                'egyptian_phones' => $egyptianPhones,
                'eg_login_cities' => count($cityList),
            ],
            'governorates' => $governorates,
            'cities' => array_slice($cityList, 0, 15),
            'top' => $top,
            'samples' => array_slice($samples, 0, 12),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ];
    }

    /**
     * @param  array<string, string>  $labels  key => Arabic name
     */
    protected function matchGovernorateFromText(string $text, array $labels): ?string
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }
        $normalized = mb_strtolower($text);

        foreach ($labels as $key => $ar) {
            if ($key === 'outside_egypt') {
                continue;
            }
            $arNorm = mb_strtolower((string) $ar);
            if ($arNorm !== '' && str_contains($normalized, $arNorm)) {
                return (string) $key;
            }
            if (str_contains($normalized, str_replace('_', ' ', (string) $key))) {
                return (string) $key;
            }
        }

        $enAliases = [
            'cairo' => 'cairo',
            'giza' => 'giza',
            'al jizah' => 'giza',
            'alexandria' => 'alexandria',
            'al iskandariyah' => 'alexandria',
            'qalyubia' => 'qalyubia',
            'sharqia' => 'sharqia',
            'dakahlia' => 'dakahlia',
            'gharbia' => 'gharbia',
            'monufia' => 'monufia',
            'port said' => 'port_said',
            'suez' => 'suez',
            'aswan' => 'aswan',
            'luxor' => 'luxor',
            'assiut' => 'assiut',
            'asyut' => 'assiut',
            'fayoum' => 'fayoum',
            'faiyum' => 'fayoum',
            'minya' => 'minya',
            'sohag' => 'sohag',
            'qena' => 'qena',
            'ismailia' => 'ismailia',
            'damietta' => 'damietta',
        ];
        foreach ($enAliases as $alias => $govKey) {
            if (isset($labels[$govKey]) && str_contains($normalized, $alias)) {
                return $govKey;
            }
        }

        return null;
    }

    /**
     * @return list<array{name: string, count: int, key?: string}>
     */
    public function egyptGovernorates(Carbon $from, Carbon $to): array
    {
        $insights = $this->egyptPresenceInsights($from, $to);

        return array_map(static fn ($g) => [
            'key' => $g['key'],
            'name' => $g['name'],
            'count' => $g['count'],
        ], $insights['governorates']);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function recentLoginSamples(Carbon $from, Carbon $to, int $limit = 25): array
    {
        if (! Schema::hasTable('activity_logs')) {
            return [];
        }

        $logs = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->where('activity_logs.action', 'login')
            ->whereNotNull('activity_logs.ip_address')
            ->whereBetween('activity_logs.created_at', [$from, $to])
            ->orderByDesc('activity_logs.created_at')
            ->limit($limit)
            ->get([
                'activity_logs.id',
                'activity_logs.ip_address',
                'activity_logs.created_at',
                'activity_logs.user_agent',
                'users.name as user_name',
                'users.phone as user_phone',
                'users.email as user_email',
            ]);

        $lookups = $this->geoIp->lookupMany($logs->pluck('ip_address')->all(), 25);
        $dialMap = $this->phoneDialMap();

        return $logs->map(function ($log) use ($lookups, $dialMap) {
            $ip = (string) $log->ip_address;
            $geo = $lookups[$ip] ?? [];
            $phoneCountry = $this->countryFromPhone((string) ($log->user_phone ?? ''), $dialMap);

            return [
                'user_name' => $log->user_name ?: '—',
                'email' => $log->user_email,
                'phone' => $log->user_phone,
                'phone_country' => $phoneCountry,
                'ip' => $ip,
                'country_code' => $geo['country_code'] ?? null,
                'country_name' => $geo['country_name'] ?? null,
                'city' => $geo['city'] ?? null,
                'region_name' => $geo['region_name'] ?? null,
                'at' => (string) $log->created_at,
            ];
        })->all();
    }

    public function recordPublicVisit(?string $ip): void
    {
        if (! Schema::hasTable('marketing_region_daily_stats') || ! $ip) {
            return;
        }

        $geo = $this->geoIp->lookup($ip);
        $code = strtoupper((string) ($geo['country_code'] ?? '')) ?: 'XX';
        $date = now()->toDateString();

        $stat = MarketingRegionDailyStat::query()->firstOrCreate(
            ['stat_date' => $date, 'country_code' => $code],
            ['visits' => 0]
        );
        $stat->increment('visits');
    }

    /**
     * @param  list<array{country_code: string, name_ar: string, name_en: string, count: int}>  $registrations
     * @param  list<array{country_code: string, name_ar: string, name_en: string, count: int}>  $logins
     * @param  list<array{country_code: string, name_ar: string, name_en: string, count: int}>  $visits
     * @return list<array{country_code: string, name_ar: string, name_en: string, registrations: int, logins: int, visits: int, value: int}>
     */
    protected function mergeCountrySeries(array $registrations, array $logins, array $visits, string $metric): array
    {
        $bag = [];
        foreach (['registrations' => $registrations, 'logins' => $logins, 'visits' => $visits] as $key => $series) {
            foreach ($series as $row) {
                $code = $row['country_code'];
                if (! isset($bag[$code])) {
                    $bag[$code] = [
                        'country_code' => $code,
                        'name_ar' => $row['name_ar'],
                        'name_en' => $row['name_en'],
                        'registrations' => 0,
                        'logins' => 0,
                        'visits' => 0,
                        'value' => 0,
                    ];
                }
                $bag[$code][$key] = (int) $row['count'];
                $bag[$code]['name_ar'] = $row['name_ar'] ?: $bag[$code]['name_ar'];
                $bag[$code]['name_en'] = $row['name_en'] ?: $bag[$code]['name_en'];
            }
        }

        foreach ($bag as &$row) {
            $row['value'] = match ($metric) {
                'visits' => $row['visits'],
                'registrations' => $row['registrations'],
                'logins' => $row['logins'],
                default => $row['visits'] + $row['registrations'] + $row['logins'],
            };
        }
        unset($row);

        $list = array_values(array_filter($bag, fn ($r) => $r['value'] > 0));
        usort($list, fn ($a, $b) => $b['value'] <=> $a['value']);

        return $list;
    }

    /**
     * @return array<string, array{code: string, dial_code: string, name_ar: string, name_en: string}>
     */
    protected function phoneDialMap(): array
    {
        $countries = config('phone_countries.countries', []);
        $map = [];
        foreach ($countries as $c) {
            $dial = (string) ($c['dial_code'] ?? '');
            $code = strtoupper((string) ($c['code'] ?? ''));
            if ($dial === '' || $code === '' || $code === 'OTHER') {
                continue;
            }
            $map[$dial] = [
                'code' => $code,
                'dial_code' => $dial,
                'name_ar' => (string) ($c['name_ar'] ?? $code),
                'name_en' => (string) ($c['name_en'] ?? $code),
            ];
        }

        // longest dial first for matching
        uksort($map, fn ($a, $b) => strlen($b) <=> strlen($a));

        return $map;
    }

    /**
     * @param  array<string, array{code: string, dial_code: string, name_ar: string, name_en: string}>  $dialMap
     */
    protected function countryFromPhone(string $phone, array $dialMap): string
    {
        $phone = preg_replace('/\s+/', '', $phone) ?? '';
        if ($phone === '' || str_starts_with($phone, 'OTHER')) {
            return 'XX';
        }
        if (! str_starts_with($phone, '+')) {
            $phone = '+'.$phone;
        }
        foreach ($dialMap as $dial => $meta) {
            if (str_starts_with($phone, $dial)) {
                return $meta['code'];
            }
        }

        return 'XX';
    }

    /**
     * @param  array<string, int>  $counts
     * @param  array<string, array{code: string, dial_code: string, name_ar: string, name_en: string}>  $dialMap
     * @return list<array{country_code: string, name_ar: string, name_en: string, count: int}>
     */
    protected function formatCountryCounts(array $counts, array $dialMap): array
    {
        $byCode = [];
        foreach ($dialMap as $meta) {
            $byCode[$meta['code']] = $meta;
        }

        $out = [];
        foreach ($counts as $code => $count) {
            $meta = $byCode[$code] ?? null;
            $out[] = [
                'country_code' => $code,
                'name_ar' => $meta['name_ar'] ?? ($code === 'XX' ? 'غير معروف' : $code),
                'name_en' => $meta['name_en'] ?? ($code === 'XX' ? 'Unknown' : $code),
                'count' => (int) $count,
            ];
        }

        usort($out, fn ($a, $b) => $b['count'] <=> $a['count']);

        return $out;
    }
}
