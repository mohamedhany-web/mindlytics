<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/** بيانات وصفية للتطبيق (متطابقة مع إعدادات الويب) */
class MetaController extends Controller
{
    public function phoneCountries(): JsonResponse
    {
        $countries = config('phone_countries.countries', []);

        $mapped = array_map(static function (array $c): array {
            return [
                'code' => $c['code'],
                'dial_code' => $c['dial_code'],
                'name_ar' => $c['name_ar'],
                'name_en' => $c['name_en'],
                'placeholder' => $c['placeholder'] ?? '',
                'example' => $c['example'] ?? '',
            ];
        }, $countries);

        return response()->json([
            'default_country_code' => config('phone_countries.default_country', 'SA'),
            'countries' => $mapped,
        ]);
    }
}
