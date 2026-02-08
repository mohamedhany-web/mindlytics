<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        return view('employee.settings.index', compact('user'));
    }

    /**
     * تحديث الإعدادات
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'language' => 'nullable|in:ar,en',
            'timezone' => 'nullable|string',
        ]);

        // حفظ الإعدادات (يمكن إضافة جدول settings لاحقاً)
        // حالياً سنستخدم session أو cache
        
        return back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
