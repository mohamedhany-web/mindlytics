@extends('layouts.admin')

@section('title', 'تعديل الكوبون')
@section('header', 'تعديل الكوبون')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">تعديل الكوبون</h1>
        
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكود *</label>
                    <input type="text" name="code" required value="{{ old('code', $coupon->code) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 uppercase">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                    <input type="text" name="title" required value="{{ old('title', $coupon->title ?? $coupon->name) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الخصم *</label>
                    <select name="discount_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="percentage" {{ ($coupon->discount_type ?? old('discount_type')) == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                        <option value="fixed" {{ ($coupon->discount_type ?? old('discount_type')) == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">قيمة الخصم *</label>
                    <input type="number" name="discount_value" step="0.01" min="0" required value="{{ old('discount_value', $coupon->discount_value) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأدنى للمبلغ</label>
                    <input type="number" name="minimum_amount" step="0.01" min="0" value="{{ old('minimum_amount', $coupon->minimum_amount) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للاستخدامات</label>
                    <input type="number" name="max_uses" min="1" value="{{ old('max_uses', $coupon->usage_limit) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية</label>
                    <input type="date" name="valid_from" value="{{ old('valid_from', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('description', $coupon->description) }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }} 
                       class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                <label class="mr-2 text-sm font-medium text-gray-700">نشط</label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    تحديث الكوبون
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

