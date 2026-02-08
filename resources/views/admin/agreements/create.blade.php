@extends('layouts.admin')

@section('title', 'إضافة اتفاقية جديدة - Mindlytics')
@section('header', 'إضافة اتفاقية جديدة')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-plus-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">إضافة اتفاقية جديدة</h2>
                    <p class="text-sm text-slate-600 mt-1">إنشاء اتفاقية عمل جديدة مع أحد المدربين</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.agreements.store') }}" class="px-5 py-6 sm:px-8 lg:px-12">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المدرب <span class="text-red-500">*</span></label>
                    <select name="instructor_id" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all">
                        <option value="">اختر المدرب</option>
                        @forelse($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->name }} 
                                @if($instructor->phone)
                                    - {{ $instructor->phone }}
                                @endif
                                @if($instructor->email)
                                    ({{ $instructor->email }})
                                @endif
                            </option>
                        @empty
                            <option value="" disabled>لا يوجد مدربين متاحين</option>
                        @endforelse
                    </select>
                    @error('instructor_id')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                    @if($instructors->isEmpty())
                        <p class="mt-1 text-xs text-amber-600 font-medium">
                            <i class="fas fa-exclamation-triangle ml-1"></i>
                            لا يوجد مدربين في النظام. يرجى إضافة مدربين أولاً.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الاتفاقية <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all">
                        <option value="course_price" {{ old('type') == 'course_price' ? 'selected' : '' }}>سعر للكورس كاملاً</option>
                        <option value="hourly_rate" {{ old('type') == 'hourly_rate' ? 'selected' : '' }}>سعر للساعة المسجلة</option>
                        <option value="monthly_salary" {{ old('type') == 'monthly_salary' ? 'selected' : '' }}>راتب شهري</option>
                    </select>
                    @error('type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">السعر/المعدل (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="rate" step="0.01" min="0" value="{{ old('rate') }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" placeholder="0.00" />
                    <p class="mt-1 text-xs text-slate-500" id="rate-help">المبلغ المحدد لكل كورس</p>
                    @error('rate')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الاتفاقية <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" placeholder="مثال: اتفاقية عمل مع المدرب..." />
                    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البدء <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" />
                    @error('start_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" />
                    @error('end_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all">
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" placeholder="وصف مختصر للاتفاقية...">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">شروط العقد</label>
                    <textarea name="terms" rows="5" class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-all" placeholder="شروط وأحكام الاتفاقية...">{{ old('terms') }}</textarea>
                    @error('terms')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all" placeholder="ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.agreements.index') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ الاتفاقية
                </button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
<script>
    document.getElementById('type').addEventListener('change', function() {
        const rateHelp = document.getElementById('rate-help');
        const type = this.value;
        
        if (type === 'course_price') {
            rateHelp.textContent = 'المبلغ المحدد لكل كورس';
        } else if (type === 'hourly_rate') {
            rateHelp.textContent = 'المبلغ المحدد لكل ساعة تدريس';
        } else if (type === 'monthly_salary') {
            rateHelp.textContent = 'الراتب الشهري الثابت';
        }
    });
</script>
@endpush
@endsection
