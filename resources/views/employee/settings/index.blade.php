@extends('layouts.employee')

@section('title', 'الإعدادات')
@section('header', 'الإعدادات')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-8">
        <h3 class="text-xl font-black text-gray-900 mb-6">إعدادات الإشعارات</h3>
        
        <form method="POST" action="{{ route('employee.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <label class="text-sm font-bold text-gray-900">الإشعارات عبر البريد الإلكتروني</label>
                        <p class="text-xs text-gray-600 mt-1">تلقي إشعارات على بريدك الإلكتروني</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <label class="text-sm font-bold text-gray-900">الإشعارات عبر الرسائل النصية</label>
                        <p class="text-xs text-gray-600 mt-1">تلقي إشعارات على رقم هاتفك</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="sms_notifications" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <label class="text-sm font-bold text-gray-900">الإشعارات الفورية</label>
                        <p class="text-xs text-gray-600 mt-1">تلقي إشعارات فورية في المتصفح</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="push_notifications" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-900 hover:border-gray-400 transition-all">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save"></i>
                        حفظ الإعدادات
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
