<?php

namespace App\Services;

use App\Support\PlatformSettings;

class PaymentGatewaySettings
{
    /**
     * بوابة فواتيرك (iframe) مفعّلة للكورسات: وضع المنصة + العلم + مفاتيح الإطار.
     */
    public static function isFawaterakEnabled(): bool
    {
        if (PlatformSettings::paymentMode() !== 'fawaterak') {
            return false;
        }

        $enabled = PlatformSettings::all()['fawaterak_gateway_enabled'] ?? true;
        if ($enabled === false || $enabled === '0' || $enabled === 0 || $enabled === 'false') {
            return false;
        }

        return app(FawaterakService::class)->isConfigured();
    }
}
