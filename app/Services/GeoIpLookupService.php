<?php

namespace App\Services;

use App\Models\GeoIpLookup;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Resolve IP → country/city with local cache (ip-api.com free endpoint).
 */
class GeoIpLookupService
{
    /**
     * @return array{country_code: ?string, country_name: ?string, region_name: ?string, city: ?string}
     */
    public function lookup(string $ip): array
    {
        $empty = [
            'country_code' => null,
            'country_name' => null,
            'region_name' => null,
            'city' => null,
        ];

        $ip = trim($ip);
        if ($ip === '' || $this->isPrivateOrLocal($ip)) {
            return $empty;
        }

        if (! Schema::hasTable('geo_ip_lookups')) {
            return $this->fetchRemote($ip) ?? $empty;
        }

        $cached = GeoIpLookup::query()->where('ip', $ip)->first();
        if ($cached) {
            return [
                'country_code' => $cached->country_code ? strtoupper((string) $cached->country_code) : null,
                'country_name' => $cached->country_name,
                'region_name' => $cached->region_name,
                'city' => $cached->city,
            ];
        }

        $remote = $this->fetchRemote($ip);
        if ($remote === null) {
            return $empty;
        }

        try {
            GeoIpLookup::query()->updateOrCreate(
                ['ip' => $ip],
                [
                    'country_code' => $remote['country_code'],
                    'country_name' => $remote['country_name'],
                    'region_name' => $remote['region_name'],
                    'city' => $remote['city'],
                    'raw' => $remote['raw'] ?? null,
                    'looked_up_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::debug('geo_ip_lookups save failed', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return [
            'country_code' => $remote['country_code'],
            'country_name' => $remote['country_name'],
            'region_name' => $remote['region_name'],
            'city' => $remote['city'],
        ];
    }

    /**
     * @param  list<string>  $ips
     * @return array<string, array{country_code: ?string, country_name: ?string, region_name: ?string, city: ?string}>
     */
    public function lookupMany(array $ips, int $maxRemote = 40): array
    {
        $out = [];
        $remoteBudget = $maxRemote;

        foreach (array_unique(array_filter(array_map('trim', $ips))) as $ip) {
            if ($this->isPrivateOrLocal($ip)) {
                $out[$ip] = [
                    'country_code' => null,
                    'country_name' => null,
                    'region_name' => null,
                    'city' => null,
                ];
                continue;
            }

            if (Schema::hasTable('geo_ip_lookups')) {
                $cached = GeoIpLookup::query()->where('ip', $ip)->first();
                if ($cached) {
                    $out[$ip] = [
                        'country_code' => $cached->country_code ? strtoupper((string) $cached->country_code) : null,
                        'country_name' => $cached->country_name,
                        'region_name' => $cached->region_name,
                        'city' => $cached->city,
                    ];
                    continue;
                }
            }

            if ($remoteBudget <= 0) {
                $out[$ip] = [
                    'country_code' => null,
                    'country_name' => null,
                    'region_name' => null,
                    'city' => null,
                ];
                continue;
            }

            $remoteBudget--;
            $out[$ip] = $this->lookup($ip);
            usleep(50_000); // gentle rate limit for free API
        }

        return $out;
    }

    public function isPrivateOrLocal(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        }

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }

    /**
     * @return array{country_code: ?string, country_name: ?string, region_name: ?string, city: ?string, raw?: array}|null
     */
    protected function fetchRemote(string $ip): ?array
    {
        try {
            $response = Http::timeout(4)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,message,country,countryCode,regionName,city',
                'lang' => 'en',
            ]);

            if (! $response->successful()) {
                return null;
            }

            $json = $response->json();
            if (! is_array($json) || ($json['status'] ?? '') !== 'success') {
                return [
                    'country_code' => null,
                    'country_name' => null,
                    'region_name' => null,
                    'city' => null,
                    'raw' => is_array($json) ? $json : null,
                ];
            }

            $code = strtoupper(trim((string) ($json['countryCode'] ?? '')));

            return [
                'country_code' => $code !== '' ? $code : null,
                'country_name' => (string) ($json['country'] ?? '') ?: null,
                'region_name' => (string) ($json['regionName'] ?? '') ?: null,
                'city' => (string) ($json['city'] ?? '') ?: null,
                'raw' => $json,
            ];
        } catch (\Throwable $e) {
            Log::debug('Geo IP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
