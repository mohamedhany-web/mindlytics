<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileAppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileAppSettingsController extends Controller
{
    public function edit(): View
    {
        $s = MobileAppSetting::singleton();

        return view('admin.mobile-app.edit', ['settings' => $s]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'welcome_title_ar' => ['nullable', 'string', 'max:255'],
            'welcome_title_en' => ['nullable', 'string', 'max:255'],
            'welcome_subtitle_ar' => ['nullable', 'string', 'max:500'],
            'welcome_subtitle_en' => ['nullable', 'string', 'max:500'],
            'mission_headline_ar' => ['nullable', 'string', 'max:255'],
            'mission_headline_en' => ['nullable', 'string', 'max:255'],
            'mission_body_ar' => ['nullable', 'string', 'max:5000'],
            'mission_body_en' => ['nullable', 'string', 'max:5000'],
            'no_subscription_title_ar' => ['nullable', 'string', 'max:255'],
            'no_subscription_title_en' => ['nullable', 'string', 'max:255'],
            'no_subscription_body_ar' => ['nullable', 'string', 'max:5000'],
            'no_subscription_body_en' => ['nullable', 'string', 'max:5000'],
            'catalog_web_path' => ['required', 'string', 'max:255', 'regex:/^\\/\\S*$/'],
        ], [
            'catalog_web_path.regex' => 'المسار يجب أن يبدأ بـ / (مثال: /courses).',
        ]);

        $s = MobileAppSetting::singleton();
        $s->fill($validated);
        $s->save();

        return redirect()->route('admin.mobile-app.edit')->with('success', 'تم حفظ إعدادات تطبيق الطلاب.');
    }
}
