@extends('layouts.admin')

@section('title', 'الواجبات والمشاريع')
@section('header', 'الواجبات والمشاريع')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium">{{ session('error') }}</div>
    @endif

    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">الواجبات والمشاريع</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">الواجبات والمشاريع</h1>
                <p class="text-sm text-white/90 mt-1">عرض وإدارة جميع الواجبات لكل المدربين (رقابة)</p>
            </div>
            <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة واجب جديد
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-tasks text-xl text-indigo-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p><p class="text-sm text-gray-500">إجمالي</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-xl text-green-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['published'] }}</p><p class="text-sm text-gray-500">منشور</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-pen text-xl text-amber-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</p><p class="text-sm text-gray-500">مسودة</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-archive text-xl text-gray-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['archived'] }}</p><p class="text-sm text-gray-500">مؤرشف</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-inbox text-xl text-cyan-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['total_submissions'] }}</p><p class="text-sm text-gray-500">تسليمات</p></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="العنوان أو الوصف"
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الكورس</label>
                <select name="course_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ Str::limit($course->title, 40) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المدرب</label>
                <select name="instructor_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">جميع المدربين</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ request('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">الكل</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold">
                    <i class="fas fa-search"></i> بحث
                </button>
            </div>
        </form>
    </div>

    @if($assignments->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h4 class="text-lg font-bold text-gray-900">قائمة الواجبات</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">المدرب</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الاستحقاق</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">التسليمات</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                            @php
                                $statusClass = $assignment->status == 'published' ? 'bg-green-100 text-green-800' : ($assignment->status == 'draft' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800');
                                $statusText = $assignment->status == 'published' ? 'منشور' : ($assignment->status == 'draft' ? 'مسودة' : 'مؤرشف');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $assignment->title }}</div>
                                    @if($assignment->description)
                                        <div class="text-xs text-gray-500 mt-0.5 truncate max-w-[200px]">{{ Str::limit($assignment->description, 55) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 truncate max-w-[140px]" title="{{ $assignment->course->title ?? '' }}">{{ $assignment->course->title ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $assignment->course->instructor->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">{{ $assignment->submissions_count }} تسليم</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.assignments.show', $assignment) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.assignments.submissions', $assignment) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors" title="التسليمات"><i class="fas fa-inbox"></i></a>
                                        <a href="{{ route('admin.assignments.edit', $assignment) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $assignments->withQueryString()->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-tasks"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد واجبات</h3>
            <p class="text-gray-500 mb-6">لم يتم العثور على واجبات تطابق معايير البحث، أو لم تُنشأ واجبات بعد</p>
            <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة واجب جديد
            </a>
        </div>
    @endif
</div>
@endsection
