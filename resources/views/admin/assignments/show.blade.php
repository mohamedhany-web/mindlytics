@extends('layouts.admin')

@section('title', 'تفاصيل الواجب: ' . $assignment->title)
@section('header', 'تفاصيل الواجب')

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
                    <a href="{{ route('admin.assignments.index') }}" class="hover:text-white">الواجبات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white truncate">{{ Str::limit($assignment->title, 35) }}</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1 truncate">{{ $assignment->title }}</h1>
                <p class="text-sm text-white/90 mt-1">
                    {{ $assignment->course->title ?? '—' }} · {{ $assignment->course->instructor->name ?? '—' }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.assignments.submissions', $assignment) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-inbox"></i>
                    التسليمات
                </a>
                <a href="{{ route('admin.assignments.edit', $assignment) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    العودة للواجبات
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <div class="xl:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-bold text-gray-900">معلومات الواجب</h3>
                    @php
                        $statusClass = $assignment->status == 'published' ? 'bg-green-100 text-green-800' : ($assignment->status == 'draft' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800');
                        $statusText = $assignment->status == 'published' ? 'منشور' : ($assignment->status == 'draft' ? 'مسودة' : 'مؤرشف');
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">{{ $statusText }}</span>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">الكورس</div>
                            <div class="text-sm font-semibold text-gray-900 truncate" title="{{ $assignment->course->title ?? '' }}">{{ Str::limit($assignment->course->title ?? '—', 25) }}</div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">المدرب</div>
                            <div class="text-sm font-semibold text-gray-900">{{ $assignment->course->instructor->name ?? '—' }}</div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">الاستحقاق</div>
                            <div class="text-sm font-semibold text-gray-900">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—' }}</div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">الدرجة الكلية</div>
                            <div class="text-sm font-semibold text-gray-900">{{ $assignment->max_score }}</div>
                        </div>
                    </div>
                    @if($assignment->description)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                            <div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100">{{ $assignment->description }}</div>
                        </div>
                    @endif
                    @if($assignment->instructions)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">التعليمات</label>
                            <div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-wrap">{{ $assignment->instructions }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h4 class="text-lg font-bold text-gray-900">آخر التسليمات</h4>
                </div>
                @if($submissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الطالب</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">التاريخ</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الدرجة</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($submissions as $sub)
                                    @php
                                        $subStatusClass = $sub->status == 'graded' || $sub->status == 'returned' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800';
                                        $subStatusText = $sub->status == 'returned' ? 'مُرجع' : ($sub->status == 'graded' ? 'مقيّم' : 'مُسلّم');
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sub->student->name ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sub->score !== null ? $sub->score . ' / ' . $assignment->max_score : '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $subStatusClass }}">{{ $subStatusText }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.assignments.submissions', $assignment) }}?grade={{ $sub->id }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">تقييم</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                        {{ $submissions->withQueryString()->links() }}
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p>لا توجد تسليمات حتى الآن</p>
                        <a href="{{ route('admin.assignments.submissions', $assignment) }}" class="inline-flex items-center gap-2 mt-3 text-indigo-600 font-medium">عرض صفحة التسليمات</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-inbox text-2xl text-indigo-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900">{{ $submissionStats['total'] }}</p>
                        <p class="text-sm text-gray-500">إجمالي التسليمات</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-paper-plane text-2xl text-amber-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900">{{ $submissionStats['submitted'] }}</p>
                        <p class="text-sm text-gray-500">مُسلّم</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-double text-2xl text-green-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900">{{ $submissionStats['graded'] }}</p>
                        <p class="text-sm text-gray-500">مقيّم</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-reply text-2xl text-cyan-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900">{{ $submissionStats['returned'] }}</p>
                        <p class="text-sm text-gray-500">مُرجع</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
