<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * صفحات لوحة تحكم تطبيق الطلاب — بعضها placeholder إلى أن يُربَط بالمنطق.
 */
class MobileAppPagesController extends Controller
{
    public function maintenance(): View
    {
        return view('admin.mobile-app.section-placeholder', [
            'pageTitle' => 'الصيانة والرسائل العامة',
            'pageDescription' => 'تفعيل وضع الصيانة على التطبيق، رسالة تظهر للجميع، أو شريط تنبيه.',
            'pageIcon' => 'tools',
        ]);
    }

    public function links(): View
    {
        return view('admin.mobile-app.section-placeholder', [
            'pageTitle' => 'الروابط والمسارات',
            'pageDescription' => 'ضبط روابط الكتالوج، الديب لينك، وصفحات الويب المفتوحة من التطبيق.',
            'pageIcon' => 'link',
        ]);
    }

    public function appearance(): View
    {
        return view('admin.mobile-app.section-placeholder', [
            'pageTitle' => 'المظهر والعلامة',
            'pageDescription' => 'ألوان التطبيق، الشعار، وأصول الواجهة (قيد الربط مع التطبيق).',
            'pageIcon' => 'palette',
        ]);
    }
}
