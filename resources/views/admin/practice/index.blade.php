@extends('layouts.admin')

@section('title', 'Practice')

@section('content')
    <div class="p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Practice (التمارين العملية)</h1>
                <p class="text-sm text-slate-600 mt-1">إدارة التمارين/الأنماط التعليمية (Learning Patterns) المرتبطة بالكورسات المتطورة.</p>
            </div>
        </div>

        <form method="GET" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-600">بحث</label>
                    <input name="q" value="{{ request('q') }}" class="mt-1 w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="عنوان / وصف..." />
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600">النوع</label>
                    <select name="type" class="mt-1 w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">الكل</option>
                        @foreach($availableTypes as $key => $info)
                            <option value="{{ $key }}" @selected(request('type') === $key)>{{ $info['name'] ?? $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600">Course ID</label>
                    <input name="course_id" value="{{ request('course_id') }}" class="mt-1 w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: 12" />
                </div>
                <div class="flex items-end gap-2">
                    <button class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold">تصفية</button>
                    <a href="{{ route('admin.practice.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold">مسح</a>
                </div>
            </div>
        </form>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="text-start px-4 py-3 font-black">#</th>
                            <th class="text-start px-4 py-3 font-black">العنوان</th>
                            <th class="text-start px-4 py-3 font-black">النوع</th>
                            <th class="text-start px-4 py-3 font-black">الكورس</th>
                            <th class="text-start px-4 py-3 font-black">المدرب</th>
                            <th class="text-start px-4 py-3 font-black">النقاط</th>
                            <th class="text-start px-4 py-3 font-black">الحالة</th>
                            <th class="text-start px-4 py-3 font-black"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patterns as $pattern)
                            @php $typeInfo = $pattern->getTypeInfo(); @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 text-slate-600 font-semibold">{{ $pattern->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-black text-slate-900">{{ $pattern->title ?: 'بدون عنوان' }}</div>
                                    @if($pattern->description)
                                        <div class="text-xs text-slate-500 line-clamp-1">{{ \Illuminate\Support\Str::limit($pattern->description, 90) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-bold">
                                        <i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }}"></i>
                                        <span>{{ $typeInfo['name'] ?? $pattern->type }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    <div class="font-semibold">{{ $pattern->course?->title ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">ID: {{ $pattern->advanced_course_id }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $pattern->instructor?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700 font-semibold">{{ $pattern->points ?? 0 }}</td>
                                <td class="px-4 py-3">
                                    @if($pattern->is_active)
                                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-black text-xs">نشط</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 font-black text-xs">غير نشط</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.practice.show', $pattern) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-slate-500 font-semibold">لا توجد تمارين مطابقة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $patterns->links() }}
            </div>
        </div>
    </div>
@endsection

