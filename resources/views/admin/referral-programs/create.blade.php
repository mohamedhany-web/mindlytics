@extends('layouts.admin')

@section('title', 'إنشاء برنامج إحالات جديد - Mindlytics')
@section('header', 'إنشاء برنامج إحالات جديد')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">إنشاء برنامج إحالات جديد</h1>
            
            <form action="{{ route('admin.referral-programs.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- اسم البرنامج -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم البرنامج *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الوصف -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- نوع الخصم للمحال -->
                    <div>
                        <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">نوع الخصم للمحال *</label>
                        <select name="discount_type" id="discount_type" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        </select>
                    </div>

                    <!-- قيمة الخصم -->
                    <div>
                        <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-2">قيمة الخصم *</label>
                        <input type="number" name="discount_value" id="discount_value" step="0.01" min="0" required value="{{ old('discount_value') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    </div>

                    <!-- الحد الأقصى للخصم -->
                    <div>
                        <label for="maximum_discount" class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للخصم</label>
                        <input type="number" name="maximum_discount" id="maximum_discount" step="0.01" min="0" value="{{ old('maximum_discount') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <p class="text-xs text-gray-500 mt-1">لنوع الخصم النسبة المئوية</p>
                    </div>

                    <!-- الحد الأدنى لمبلغ الطلب -->
                    <div>
                        <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 mb-2">الحد الأدنى لمبلغ الطلب</label>
                        <input type="number" name="minimum_order_amount" id="minimum_order_amount" step="0.01" min="0" value="{{ old('minimum_order_amount') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">مكافأة المحيل</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- نوع المكافأة -->
                        <div>
                            <label for="referrer_reward_type" class="block text-sm font-medium text-gray-700 mb-2">نوع المكافأة *</label>
                            <select name="referrer_reward_type" id="referrer_reward_type" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                                <option value="fixed" {{ old('referrer_reward_type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                                <option value="percentage" {{ old('referrer_reward_type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                <option value="points" {{ old('referrer_reward_type') == 'points' ? 'selected' : '' }}>نقاط</option>
                            </select>
                        </div>

                        <!-- قيمة المكافأة -->
                        <div>
                            <label for="referrer_reward_value" class="block text-sm font-medium text-gray-700 mb-2">قيمة المكافأة</label>
                            <input type="number" name="referrer_reward_value" id="referrer_reward_value" step="0.01" min="0" value="{{ old('referrer_reward_value') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">الإعدادات</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- مدة صلاحية الخصم -->
                        <div>
                            <label for="discount_valid_days" class="block text-sm font-medium text-gray-700 mb-2">مدة صلاحية الخصم (بالأيام) *</label>
                            <input type="number" name="discount_valid_days" id="discount_valid_days" min="1" required value="{{ old('discount_valid_days', 30) }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        </div>

                        <!-- الحد الأقصى لاستخدام الخصم -->
                        <div>
                            <label for="max_discount_uses_per_referred" class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى لاستخدام الخصم للمحال *</label>
                            <input type="number" name="max_discount_uses_per_referred" id="max_discount_uses_per_referred" min="1" required value="{{ old('max_discount_uses_per_referred', 1) }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        </div>

                        <!-- الحد الأقصى للإحالات لكل مستخدم -->
                        <div>
                            <label for="max_referrals_per_user" class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للإحالات لكل مستخدم</label>
                            <input type="number" name="max_referrals_per_user" id="max_referrals_per_user" min="1" value="{{ old('max_referrals_per_user') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                            <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للسماح بإحالات غير محدودة</p>
                        </div>

                        <!-- السماح بالإحالة الذاتية -->
                        <div class="flex items-center gap-3 pt-8">
                            <input type="checkbox" name="allow_self_referral" id="allow_self_referral" value="1" {{ old('allow_self_referral') ? 'checked' : '' }}
                                class="h-5 w-5 text-sky-600 focus:ring-sky-500 border-gray-300 rounded">
                            <label for="allow_self_referral" class="text-sm font-medium text-gray-700">السماح بالإحالة الذاتية</label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">الفترة الزمنية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- تاريخ البدء -->
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء</label>
                            <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        </div>

                        <!-- تاريخ الانتهاء -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                            <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- الحالة -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-5 w-5 text-sky-600 focus:ring-sky-500 border-gray-300 rounded">
                    <label for="is_active" class="text-sm font-medium text-gray-700">تفعيل البرنامج</label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.referral-programs.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                        <i class="fas fa-save ml-2"></i>
                        حفظ البرنامج
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
