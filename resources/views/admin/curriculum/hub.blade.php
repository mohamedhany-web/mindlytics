@extends('layouts.admin')

@section('title', 'إدارة هيكل الكورسات')
@section('header', 'إدارة هيكل الكورسات')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">بناء المنهج وإعدادات الفيديو</h1>
                <p class="text-sm text-gray-600 mt-1">إدارة أقسام الكورسات وعناصر المنهج، والتحكم في فتح الفيديوهات للطلاب.</p>
            </div>
            <a href="{{ route('admin.advanced-courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                <i class="fas fa-graduation-cap"></i>
                الكورسات
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الكورس</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">المدرب</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">أقسام</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">محاضرات</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">قيود الفيديو</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $course)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-800">{{ $course->title }}</div>
                                <div class="text-xs text-slate-500">#{{ $course->id }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-4 text-center text-sm font-semibold text-slate-700">{{ $course->sections_count }}</td>
                            <td class="px-4 py-4 text-center text-sm font-semibold text-slate-700">{{ $course->lectures_count }}</td>
                            <td class="px-4 py-4 text-center">
                                <form method="POST" action="{{ route('admin.advanced-courses.unlock-policy', $course) }}" class="inline-flex items-center justify-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="admin_unlock_all_videos" value="0">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="admin_unlock_all_videos" value="1"
                                               class="rounded text-teal-600"
                                               {{ $course->admin_unlock_all_videos ? 'checked' : '' }}
                                               onchange="this.form.submit()">
                                        <span class="text-xs font-bold {{ $course->admin_unlock_all_videos ? 'text-teal-700' : 'text-slate-500' }}">
                                            {{ $course->admin_unlock_all_videos ? 'مفتوح بالكامل' : 'عادي' }}
                                        </span>
                                    </label>
                                </form>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('admin.advanced-courses.curriculum', $course) }}"
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-sm font-semibold">
                                    <i class="fas fa-sitemap"></i>
                                    بناء المنهج
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد كورسات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($courses->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $courses->links() }}</div>
        @endif
    </div>
</div>
@endsection
