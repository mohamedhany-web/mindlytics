@extends('layouts.admin')

@section('title', 'خصومات الحضور التلقائية')
@section('header', 'خصومات الحضور التلقائية')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إعدادات خصومات الحضور</h1>
                <p class="text-gray-600 mt-1">يُطبَّق خصم التأخير فور الحضور، وعدم الإكمال عند الانصراف، والغياب تلقائياً ليلاً (02:30)</p>
            </div>
            <a href="{{ route('admin.employee-attendance.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للتقارير
            </a>
        </div>
    </div>

    <form method="post" action="{{ route('admin.employee-attendance.penalty-settings.update') }}" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 space-y-6">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-200 pb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">الإعدادات العامة</h2>
            <div class="flex flex-wrap gap-6">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="penalties_enabled" value="1" @checked(old('penalties_enabled', $settings['penalties_enabled'] ?? true)) class="rounded text-blue-600">
                    <span class="text-sm font-medium text-gray-700">تفعيل نظام الخصومات</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="notify_employee" value="1" @checked(old('notify_employee', $settings['notify_employee'] ?? true)) class="rounded text-blue-600">
                    <span class="text-sm font-medium text-gray-700">إشعار الموظف عند الخصم</span>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-5 space-y-4">
                <label class="flex items-center gap-2 font-semibold text-amber-900 cursor-pointer">
                    <input type="checkbox" name="late_penalty_enabled" value="1" @checked($settings['late_penalty_enabled'] ?? true) class="rounded">
                    خصم التأخير
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ج.م)</label>
                    <input type="number" step="0.01" name="late_penalty_amount" value="{{ old('late_penalty_amount', $settings['late_penalty_amount'] ?? 25) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                    <input type="text" name="late_penalty_title" value="{{ old('late_penalty_title', $settings['late_penalty_title'] ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="rounded-xl border border-red-200 bg-red-50/50 p-5 space-y-4">
                <label class="flex items-center gap-2 font-semibold text-red-900 cursor-pointer">
                    <input type="checkbox" name="absence_penalty_enabled" value="1" @checked($settings['absence_penalty_enabled'] ?? true) class="rounded">
                    خصم الغياب
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ج.م)</label>
                    <input type="number" step="0.01" name="absence_penalty_amount" value="{{ old('absence_penalty_amount', $settings['absence_penalty_amount'] ?? 100) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                    <input type="text" name="absence_penalty_title" value="{{ old('absence_penalty_title', $settings['absence_penalty_title'] ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="rounded-xl border border-orange-200 bg-orange-50/50 p-5 space-y-4">
                <label class="flex items-center gap-2 font-semibold text-orange-900 cursor-pointer">
                    <input type="checkbox" name="incomplete_penalty_enabled" value="1" @checked($settings['incomplete_penalty_enabled'] ?? true) class="rounded">
                    خصم عدم إكمال الساعات
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ج.م)</label>
                    <input type="number" step="0.01" name="incomplete_penalty_amount" value="{{ old('incomplete_penalty_amount', $settings['incomplete_penalty_amount'] ?? 50) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                    <input type="text" name="incomplete_penalty_title" value="{{ old('incomplete_penalty_title', $settings['incomplete_penalty_title'] ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع الخصم</label>
                <select name="penalty_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach(['penalty' => 'غرامة', 'other' => 'أخرى', 'tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'سلفة'] as $k => $l)
                        <option value="{{ $k }}" @selected(($settings['penalty_type'] ?? 'penalty') === $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">حالة الخصم</label>
                <select name="penalty_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach(['applied' => 'مطبّق', 'pending' => 'معلق', 'cancelled' => 'ملغى'] as $k => $l)
                        <option value="{{ $k }}" @selected(($settings['penalty_status'] ?? 'applied') === $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">سريان الخصم اعتباراً من</label>
                <input type="date" name="penalty_effective_from" value="{{ $settings['penalty_effective_from'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للاعتماد على تاريخ تعيين كل موظف. لن يُحتسب خصم حضور عن أي يوم قبل هذا التاريخ.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.employee-attendance.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ الإعدادات
            </button>
        </div>
    </form>
</div>
@endsection
