@extends('layouts.app')

@section('title', 'الإعدادات')
@section('header', 'الإعدادات')

@push('styles')
<style>
    .settings-card {
        background: white;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        transition: all 0.2s ease;
    }
    .settings-card:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.08);
        border-color: rgb(186 230 253);
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: rgb(203 213 225);
        transition: 0.25s;
        border-radius: 28px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.25s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .toggle-switch input:checked + .toggle-slider {
        background: rgb(14 165 233);
    }
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
    .toggle-switch input:focus + .toggle-slider {
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.25);
    }

    .danger-card {
        background: rgb(254 242 242);
        border: 1px solid rgb(254 202 202);
        border-radius: 12px;
    }
    .warning-card {
        background: rgb(255 251 235);
        border: 1px solid rgb(254 243 199);
        border-radius: 12px;
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">الإعدادات</h1>
        <p class="text-sm text-gray-500">إدارة إعدادات الحساب والتفضيلات</p>
    </div>

    <div class="space-y-6">
        <!-- إعدادات الإشعارات -->
        <div class="settings-card p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 border border-sky-200 flex items-center justify-center">
                    <i class="fas fa-bell"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">إعدادات الإشعارات</h2>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-sky-50/50 rounded-xl border border-sky-100">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 mb-0.5">إشعارات الكورسات الجديدة</h3>
                        <p class="text-xs text-gray-600">تلقي إشعار عند إضافة كورسات جديدة</p>
                    </div>
                    <label class="toggle-switch flex-shrink-0 mr-2">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="flex items-center justify-between p-4 bg-sky-50/50 rounded-xl border border-sky-100">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 mb-0.5">إشعارات الطلبات</h3>
                        <p class="text-xs text-gray-600">تلقي إشعار عند تحديث حالة طلباتك</p>
                    </div>
                    <label class="toggle-switch flex-shrink-0 mr-2">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="flex items-center justify-between p-4 bg-sky-50/50 rounded-xl border border-sky-100">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 mb-0.5">إشعارات الامتحانات</h3>
                        <p class="text-xs text-gray-600">تلقي تذكير قبل مواعيد الامتحانات</p>
                    </div>
                    <label class="toggle-switch flex-shrink-0 mr-2">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- إعدادات الخصوصية -->
        <div class="settings-card p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 border border-violet-200 flex items-center justify-center">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">إعدادات الخصوصية</h2>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 mb-0.5">إظهار التقدم للآخرين</h3>
                        <p class="text-xs text-gray-600">السماح للمعلمين برؤية تقدمك في الكورسات</p>
                    </div>
                    <label class="toggle-switch flex-shrink-0 mr-2">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 mb-0.5">إظهار النشاط</h3>
                        <p class="text-xs text-gray-600">إظهار آخر نشاط لك في المنصة</p>
                    </div>
                    <label class="toggle-switch flex-shrink-0 mr-2">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- إعدادات العرض -->
        <div class="settings-card p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 border border-amber-200 flex items-center justify-center">
                    <i class="fas fa-palette"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">إعدادات العرض</h2>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-2">المظهر</label>
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="light">فاتح</option>
                        <option value="dark">داكن</option>
                        <option value="auto">تلقائي</option>
                    </select>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-2">اللغة</label>
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="ar">العربية</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- إعدادات الحساب -->
        <div class="settings-card p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">إعدادات الحساب</h2>
            </div>
            <div class="space-y-4">
                <div class="warning-card flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-gray-900 mb-0.5">تنزيل البيانات</h3>
                            <p class="text-xs text-gray-600">احصل على نسخة من جميع بياناتك</p>
                        </div>
                    </div>
                    <button class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex-shrink-0">
                        <i class="fas fa-download"></i>
                        تنزيل
                    </button>
                </div>
                <div class="danger-card flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-gray-900 mb-0.5">حذف الحساب</h3>
                            <p class="text-xs text-gray-600">حذف الحساب نهائياً مع جميع البيانات</p>
                        </div>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors flex-shrink-0"
                            onclick="if(confirm('هل أنت متأكد من حذف الحساب؟ هذا الإجراء لا يمكن التراجع عنه.')) { /* Handle delete */ }">
                        <i class="fas fa-trash-alt"></i>
                        حذف الحساب
                    </button>
                </div>
            </div>
        </div>

        <!-- حفظ الإعدادات -->
        <div class="flex justify-end pt-2">
            <button type="button" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                <i class="fas fa-save"></i>
                <span>حفظ جميع الإعدادات</span>
            </button>
        </div>
    </div>
</div>
@endsection
