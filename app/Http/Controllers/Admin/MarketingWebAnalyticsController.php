<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\MarketingWebAnalyticsSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingWebAnalyticsController extends Controller
{
    public function edit(): View
    {
        return view('admin.marketing.web-analytics.settings', [
            'settings' => MarketingWebAnalyticsSettings::all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'gtm_container_id' => ['nullable', 'string', 'max:64'],
            'ga4_measurement_id' => ['nullable', 'string', 'max:64'],
            'clarity_project_id' => ['nullable', 'string', 'max:64'],
            'meta_pixel_id' => ['nullable', 'string', 'max:64'],
            'meta_pixel_enabled' => ['nullable', 'boolean'],
            'currency' => ['nullable', 'string', 'max:8'],
            'item_brand' => ['nullable', 'string', 'max:100'],
        ]);

        $normalizeId = static function (?string $value): string {
            $value = trim((string) $value);
            // Strip accidental quotes / spaces from paste
            return trim($value, " \t\n\r\0\x0B\"'");
        };

        MarketingWebAnalyticsSettings::save([
            'enabled' => $request->boolean('enabled'),
            'gtm_container_id' => $normalizeId($validated['gtm_container_id'] ?? ''),
            'ga4_measurement_id' => $normalizeId($validated['ga4_measurement_id'] ?? ''),
            'clarity_project_id' => $normalizeId($validated['clarity_project_id'] ?? ''),
            'meta_pixel_id' => preg_replace('/\D+/', '', $normalizeId($validated['meta_pixel_id'] ?? '')) ?: '',
            'meta_pixel_enabled' => $request->boolean('meta_pixel_enabled'),
            'currency' => strtoupper($normalizeId($validated['currency'] ?? 'EGP') ?: 'EGP'),
            'item_brand' => $normalizeId($validated['item_brand'] ?? 'Mindlytics') ?: 'Mindlytics',
        ]);

        return redirect()
            ->route('admin.marketing-web-analytics.settings')
            ->with('success', 'تم حفظ إعدادات تتبع التسويق بنجاح.');
    }
}
