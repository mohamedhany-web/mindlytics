@extends('layouts.admin')

@section('title', 'المحاضرات')
@section('header', 'المحاضرات')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">المحاضرات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">المحاضرات</h1>
                <p class="text-sm text-white/90 mt-1">إدارة المحاضرات والجلسات</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.lectures.create') }}" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة محاضرة جديدة
                </a>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الكورس</label>
                <select name="course_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ Str::limit($course->title, 50) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع الحالات</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
            <div class="flex items-end sm:col-span-2 lg:col-span-1">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة المحاضرات -->
    @if($lectures->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h4 class="text-lg font-bold text-gray-900">قائمة المحاضرات</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">المحاضر</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($lectures as $lecture)
                            @php
                                $statusClass = $lecture->status == 'completed' ? 'bg-green-100 text-green-800' : ($lecture->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : ($lecture->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'));
                                $statusText = $lecture->status == 'scheduled' ? 'مجدولة' : ($lecture->status == 'in_progress' ? 'قيد التنفيذ' : ($lecture->status == 'completed' ? 'مكتملة' : 'ملغاة'));
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $lecture->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 truncate max-w-[200px]" title="{{ $lecture->course->title ?? '' }}">{{ $lecture->course->title ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $lecture->instructor->name ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $lecture->scheduled_at ? $lecture->scheduled_at->format('Y-m-d H:i') : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.lectures.show', $lecture) }}" 
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.lectures.edit', $lecture) }}" 
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $lectures->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد محاضرات</h3>
            <p class="text-gray-500 mb-6">لم يتم العثور على محاضرات تطابق معايير البحث، أو لم تُضف محاضرات بعد</p>
            <a href="{{ route('admin.lectures.create') }}" 
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة محاضرة جديدة
            </a>
        </div>
    @endif
</div>
@endsection

