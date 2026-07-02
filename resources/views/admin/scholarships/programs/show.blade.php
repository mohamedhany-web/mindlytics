@extends('layouts.admin')

@section('title', $program->name)
@section('header', 'تفاصيل المنحة')

@section('content')
@include('admin.scholarships._styles')

@php
    $statusBadges = [
        'registered' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'activated' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-700 border border-rose-200',
        'deactivated' => 'bg-slate-100 text-slate-700 border border-slate-200',
    ];
@endphp

<div class="space-y-6">
    @include('admin.scholarships._alerts')
    @include('admin.scholarships._nav', ['active' => 'programs'])

    @include('admin.scholarships._header', [
        'title' => $program->name,
        'subtitle' => 'المدرب: ' . ($program->instructor?->name ?? '—') . ' | الكورس: ' . ($program->course?->title ?? '—'),
        'icon' => 'fas fa-award',
        'actions' => '
            <a href="' . route('admin.scholarships.programs.edit', $program) . '" class="' . $schBtnSecondary . '"><i class="fas fa-edit"></i><span>تعديل</span></a>
            <button type="button" onclick="navigator.clipboard.writeText(' . json_encode($program->registrationUrl()) . '); alert(\'تم نسخ رابط التسجيل\');" class="' . $schBtnPrimary . '"><i class="fas fa-link"></i><span>نسخ الرابط</span></button>
        ',
    ])

    @include('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'مسجّل', 'value' => number_format($program->registrations_count), 'icon' => 'fas fa-users', 'description' => 'إجمالي التسجيلات'],
        ['label' => 'بانتظار التفعيل', 'value' => number_format($program->pending_count), 'icon' => 'fas fa-hourglass-half', 'description' => 'يحتاج موافقة'],
        ['label' => 'مفعّل', 'value' => number_format($program->activated_count), 'icon' => 'fas fa-user-check', 'description' => 'وصول للكورس'],
        ['label' => 'مرفوض', 'value' => number_format($program->rejected_count), 'icon' => 'fas fa-user-times', 'description' => 'تسجيلات مرفوضة'],
    ]])

    <section class="{{ $schSectionClass }}">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">معلومات المنحة</h3>
        </div>
        <div class="p-6 space-y-4">
            @if($program->description)
                <p class="text-sm text-slate-700 leading-relaxed">{{ $program->description }}</p>
            @endif
            <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm break-all font-mono" dir="ltr">{{ $program->registrationUrl() }}</div>
            <div class="flex flex-wrap gap-2">
                @if($program->course)
                    <a href="{{ route('admin.scholarships.courses.show', $program->course) }}" class="{{ $schBtnSecondary }}"><i class="fas fa-book"></i> كورس المنحة</a>
                @endif
                @if($program->instructor)
                    <a href="{{ route('admin.scholarships.instructors.show', $program->instructor) }}" class="{{ $schBtnSecondary }}"><i class="fas fa-chalkboard-teacher"></i> المدرب</a>
                @endif
                <a href="{{ route('admin.scholarships.students.index', ['program_id' => $program->id]) }}" class="{{ $schBtnSecondary }}"><i class="fas fa-user-graduate"></i> كل الطلاب</a>
            </div>
        </div>
    </section>

    <section class="{{ $schSectionClass }}">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">المسجّلون في هذه المنحة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الطالب</th>
                        <th class="px-6 py-4 text-right">البريد</th>
                        <th class="px-6 py-4 text-right">الهاتف</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($registrations as $registration)
                        <tr class="sch-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $registration->user?->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $registration->user?->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $registration->user?->phone ?: '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $statusBadges[$registration->status] ?? '' }}">{{ $registration->status_label }}</span>
                            </td>
                            <td class="px-6 py-4">@include('admin.scholarships._registration-actions', ['registration' => $registration])</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">لا يوجد مسجّلون بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registrations->hasPages())<div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $registrations->links() }}</div>@endif
    </section>
</div>
@endsection
