<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FawaterakService;
use App\Support\PlatformSettings;
use App\Support\SiteBranding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $all = PlatformSettings::all();

        return view('admin.system-settings.index', [
            'logoUrl' => SiteBranding::logoUrl(),
            'faviconUrl' => SiteBranding::faviconUrl(),
            'platformPaymentMode' => PlatformSettings::paymentMode(),
            'fawaterakGatewayEnabled' => ! in_array($all['fawaterak_gateway_enabled'] ?? true, [false, '0', 0, 'false'], true),
            'gatewayFeeMode' => (string) ($all['gateway_fee_mode'] ?? 'none'),
            'gatewayFeePercent' => (string) ($all['gateway_fee_percent'] ?? '0'),
            'gatewayFeeFixed' => (string) ($all['gateway_fee_fixed'] ?? '0'),
            'contactPage' => PlatformSettings::contactPage(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp,gif'],
            'favicon' => ['nullable', 'file', 'max:4096', 'mimes:ico,png,svg,webp'],
            'platform_payment_mode' => ['required', Rule::in(['manual', 'kashier', 'fawaterak'])],
            'gateway_fee_mode' => ['required', Rule::in(['none', 'percent', 'fixed'])],
            'gateway_fee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'gateway_fee_fixed' => ['nullable', 'numeric', 'min:0'],
            'contact_hero_title' => ['required', 'string', 'max:255'],
            'contact_hero_subtitle' => ['nullable', 'string', 'max:500'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:60'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'contact_hours' => ['nullable', 'array', 'max:7'],
            'contact_hours.*.label' => ['nullable', 'string', 'max:120'],
            'contact_hours.*.value' => ['nullable', 'string', 'max:120'],
            'contact_hours.*.closed' => ['nullable', 'boolean'],
        ]);

        $disk = Storage::disk('public');

        if (! $disk->exists('site')) {
            $disk->makeDirectory('site');
        }

        if ($request->hasFile('logo')) {
            foreach (SiteBranding::logoExtensions() as $ext) {
                $disk->delete("site/logo.{$ext}");
            }
            $file = $validated['logo'];
            $ext = strtolower((string) $file->getClientOriginalExtension());
            if ($ext === '') {
                $ext = strtolower((string) $file->guessExtension());
            }
            if (! in_array($ext, SiteBranding::logoExtensions(), true)) {
                $ext = 'png';
            }
            $file->storeAs('site', 'logo.'.$ext, 'public');
        }

        if ($request->hasFile('favicon')) {
            foreach (SiteBranding::faviconExtensions() as $ext) {
                $disk->delete("site/favicon.{$ext}");
            }
            $file = $validated['favicon'];
            $ext = strtolower((string) $file->getClientOriginalExtension());
            if ($ext === '') {
                $ext = strtolower((string) $file->guessExtension());
            }
            if (! in_array($ext, SiteBranding::faviconExtensions(), true)) {
                $ext = 'png';
            }
            $file->storeAs('site', 'favicon.'.$ext, 'public');
        }

        $fawaterakOn = $request->boolean('fawaterak_gateway_on');

        PlatformSettings::save([
            'platform_payment_mode' => $validated['platform_payment_mode'],
            'fawaterak_gateway_enabled' => $fawaterakOn,
            'gateway_fee_mode' => $validated['gateway_fee_mode'],
            'gateway_fee_percent' => (string) ($validated['gateway_fee_percent'] ?? '0'),
            'gateway_fee_fixed' => (string) ($validated['gateway_fee_fixed'] ?? '0'),
            'contact_page' => [
                'hero_title' => $validated['contact_hero_title'],
                'hero_subtitle' => (string) ($validated['contact_hero_subtitle'] ?? ''),
                'address' => (string) ($validated['contact_address'] ?? ''),
                'phone' => (string) ($validated['contact_phone'] ?? ''),
                'email' => (string) ($validated['contact_email'] ?? ''),
                'whatsapp' => preg_replace('/\D+/', '', (string) ($validated['contact_whatsapp'] ?? '')),
                'hours' => PlatformSettings::normalizeContactHours($request->input('contact_hours', [])),
            ],
        ]);

        if ($validated['platform_payment_mode'] === 'fawaterak' && $fawaterakOn && ! app(FawaterakService::class)->isConfigured()) {
            return redirect()->route('admin.system-settings.index')
                ->with('warning', 'تم الحفظ. وضع فواتيرك مفعّل لكن مفاتيح الإطار (Vendor/Provider) غير مكتملة في ملف البيئة — لن يعمل الدفع حتى اكتمال الإعداد.');
        }

        return redirect()->route('admin.system-settings.index')
            ->with('success', 'تم حفظ إعدادات النظام.');
    }
}
