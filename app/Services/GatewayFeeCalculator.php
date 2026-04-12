<?php

namespace App\Services;

use App\Support\PlatformSettings;

class GatewayFeeCalculator
{
    /**
     * بوابات يُحتسب لها عمولة (إعدادات المنصة).
     *
     * @var list<string>
     */
    private const GATEWAYS_WITH_FEE = ['kashier', 'fawaterak', 'moyasar', 'stripe', 'paypal'];

    public static function appliesToGateway(?string $gateway): bool
    {
        $g = strtolower(trim((string) $gateway));

        return $g !== '' && in_array($g, self::GATEWAYS_WITH_FEE, true);
    }

    /**
     * @return array{fee: float, net: float, detail: array<string, mixed>}
     */
    public static function calculate(float $grossAmount): array
    {
        if ($grossAmount <= 0) {
            return ['fee' => 0.0, 'net' => 0.0, 'detail' => ['mode' => 'none']];
        }

        $all = PlatformSettings::all();
        $mode = strtolower(trim((string) ($all['gateway_fee_mode'] ?? env('GATEWAY_FEE_MODE', 'none'))));

        if ($mode === 'percent') {
            $p = (float) ($all['gateway_fee_percent'] ?? env('GATEWAY_FEE_PERCENT', 0));
            $p = max(0, min($p, 100));
            $fee = round($grossAmount * $p / 100, 2);

            return [
                'fee' => $fee,
                'net' => round($grossAmount - $fee, 2),
                'detail' => [
                    'mode' => 'percent',
                    'percent' => $p,
                ],
            ];
        }

        if ($mode === 'fixed') {
            $f = (float) ($all['gateway_fee_fixed'] ?? env('GATEWAY_FEE_FIXED_EGP', 0));
            $f = max(0, $f);
            $fee = round(min($f, $grossAmount), 2);

            return [
                'fee' => $fee,
                'net' => round($grossAmount - $fee, 2),
                'detail' => [
                    'mode' => 'fixed',
                    'fixed_egp' => $f,
                ],
            ];
        }

        return [
            'fee' => 0.0,
            'net' => round($grossAmount, 2),
            'detail' => ['mode' => 'none'],
        ];
    }
}
