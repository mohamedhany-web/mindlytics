@extends('layouts.admin')

@section('title', 'تعديل كورس أوفلاين')
@section('header', 'تعديل كورس أوفلاين')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تعديل كورس أوفلاين</h1>
                <p class="text-gray-600 mt-1">تحديث معلومات الكورس الأوفلاين</p>
            </div>
            <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للتفاصيل
            </a>
        </div>
    </div>

    <form action="{{ route('admin.offline-courses.update', $offlineCourse) }}" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- القسم الأساسي -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- العنوان -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الكورس *</label>
                        <input type="text" name="title" value="{{ old('title', $offlineCourse->title) }}" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- الوصف -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('description', $offlineCourse->description) }}</textarea>
                    </div>

                    <!-- المدرب -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المدرب المسؤول *</label>
                        <select name="instructor_id" required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">اختر المدرب</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ old('instructor_id', $offlineCourse->instructor_id) == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                        @error('instructor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الفرع</label>
                        <select name="branch_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">الافتراضي (من المدرب أو إعدادات المنصة)</option>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" {{ (string) old('branch_id', $offlineCourse->branch_id ?? '') === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }} @if($branch->slug) ({{ $branch->slug }}) @endif</option>
                            @endforeach
                        </select>
                        @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- المكان -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المكان</label>
                        <select name="location_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">اختر المكان</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id', $offlineCourse->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">أو</p>
                    </div>

                    <!-- الموقع (نص حر) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">موقع الكورس (نص حر)</label>
                        <input type="text" name="location" value="{{ old('location', $offlineCourse->location) }}" placeholder="أو أدخل موقع مخصص" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                </div>
            </div>

            <!-- القسم التفاصيل -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">التفاصيل والمواعيد</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- تاريخ البدء -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $offlineCourse->start_date?->format('Y-m-d')) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- تاريخ الانتهاء -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $offlineCourse->end_date?->format('Y-m-d')) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- عدد الساعات -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد الساعات</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours', $offlineCourse->duration_hours) }}" min="0" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- عدد الجلسات -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد الجلسات</label>
                        <input type="number" name="sessions_count" value="{{ old('sessions_count', $offlineCourse->sessions_count) }}" min="0" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>
            </div>

            <!-- القسم المالي -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات المالية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- السعر -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السعر</label>
                        <input type="number" name="price" value="{{ old('price', $offlineCourse->price) }}" min="0" step="0.01" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- الحد الأقصى للطلاب -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للطلاب *</label>
                        <input type="number" name="max_students" value="{{ old('max_students', $offlineCourse->max_students) }}" min="1" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('max_students')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- الحجز العام للطلاب -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">الحجز العام (الطلاب)</h2>
                <p class="text-sm text-gray-600 mb-2">تواريخ بداية/نهاية الحجز تُطبَّق على <strong>رابط حجز المجموعة</strong> ولطلبات الحجز عند مشاركة رابط مباشر. يجب أن يكون الكورس <strong>نشطاً</strong> (الحالة: نشط) وليس مسودة.</p>
                <p class="text-xs text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-4">إن اخترت «نهاية الحجز» من التقويم بدون وقت محدد (يظهر كـ 12:00 ص)، يُحفظ تلقائياً كـ <strong>نهاية ذلك اليوم</strong> حتى لا يُغلق الحجز من أول دقيقة في اليوم.</p>
                <p class="text-sm text-gray-600 mb-4">«تفعيل الحجز العام للطلاب» يسمح بطلب الحجز عبر رابط مباشر للكورس (إن وُجد) ضمن نافذة التواريخ أدناه. رابط المجموعة يعمل بمجرد تفعيل الحجز على المجموعة + صلاحية التواريخ هنا.</p>
                <div class="space-y-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="public_booking_enabled" value="1"
                               {{ old('public_booking_enabled', $offlineCourse->public_booking_enabled) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-800">تفعيل الحجز العام للطلاب</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">بداية الحجز</label>
                            <input type="datetime-local" name="booking_opens_at"
                                   value="{{ old('booking_opens_at', $offlineCourse->booking_opens_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نهاية الحجز</label>
                            <input type="datetime-local" name="booking_closes_at"
                                   value="{{ old('booking_closes_at', $offlineCourse->booking_closes_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                    @error('booking_closes_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- بوابة الطالب — كورسات الأونلاين -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">بوابة الطالب (كورس أونلاين)</h2>
                <p class="text-sm text-gray-600 mb-4">عند تسجيل الطالب في <strong>مجموعة أونلاين</strong>، لا يظهر الكورس في قائمة «كورساتي الأونلاين» للطالب إلا بعد تفعيل الخيار التالي.</p>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="student_online_portal_enabled" value="1"
                           {{ old('student_online_portal_enabled', $offlineCourse->student_online_portal_enabled) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-800">إظهار هذا الكورس للطلاب في بوابة الأونلاين</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer mt-4">
                    <input type="checkbox" name="online_only" value="1"
                           {{ old('online_only', $offlineCourse->online_only) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    <span class="text-sm font-medium text-gray-800">كورس أونلاين فقط (يُبرز في «إدارة الأونلاين» — لا يمنع الحضوري إن وُجدت مجموعات حضورية)</span>
                </label>
                <p class="text-xs text-gray-500 mt-2">لظهور الكورس في قائمة كورسات الأونلاين للإدارة، فعّل أيضاً <strong>الحجز الأونلاين</strong> لمجموعة من صفحة المجموعات.</p>
            </div>

            <!-- القسم الإداري -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">الإعدادات الإدارية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- الحالة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                        <select name="status" required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="draft" {{ old('status', $offlineCourse->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="active" {{ old('status', $offlineCourse->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="completed" {{ old('status', $offlineCourse->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ old('status', $offlineCourse->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>

                    <!-- الملاحظات -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إدارية</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('notes', $offlineCourse->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
            <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
