@extends('layouts.app')

@section('title', $assignment->title . ' - Mindlytics')
@section('header', $assignment->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.assignments.index') }}" class="hover:text-sky-600">الواجبات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ $assignment->title }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $assignment->title }}</h1>
                <p class="text-sm text-slate-600 mt-0.5">{{ $assignment->course->title ?? '—' }}</p>
                @if($assignment->group)
                    <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                        <i class="fas fa-users"></i> واجب جماعي: {{ $assignment->group->name }}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('instructor.assignments.edit', $assignment) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('instructor.assignments.submissions', $assignment) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold">
                    <i class="fas fa-inbox"></i> التسليمات ({{ $submissionStats['total'] ?? 0 }})
                </a>
            </div>
        </div>
    </div>

    @if($assignment->description)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 mb-6">
            <h3 class="font-bold text-slate-800 mb-2">الوصف</h3>
            <p class="text-slate-600">{{ $assignment->description }}</p>
        </div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 font-bold text-slate-800">آخر التسليمات</div>
        <div class="overflow-x-auto">
            @if($submissions->count() > 0)
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">الطالب / المجموعة</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">تاريخ التسليم</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($submissions as $sub)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-800">
                                    @if($sub->group_id)
                                        <span class="text-sky-600">{{ $sub->group->name ?? 'مجموعة' }}</span>
                                        <span class="text-slate-500">({{ $sub->student->name ?? '—' }})</span>
                                    @else
                                        {{ $sub->student->name ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $sub->submitted_at?->format('Y/m/d H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold px-2 py-1 rounded {{ $sub->status === 'graded' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $sub->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3 border-t border-slate-200">{{ $submissions->links() }}</div>
            @else
                <p class="p-6 text-center text-slate-500">لا توجد تسليمات بعد</p>
            @endif
        </div>
    </div>
</div>
@endsection
