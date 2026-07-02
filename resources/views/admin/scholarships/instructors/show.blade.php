@extends('layouts.admin')

@section('title', $instructor->name)
@section('header', 'مدرب المنح')

@section('content')
@include('admin.scholarships._styles')

<div class="space-y-6">
    @include('admin.scholarships._alerts')
    @include('admin.scholarships._nav', ['active' => 'instructors'])

    @include('admin.scholarships._header', [
        'title' => $instructor->name,
        'subtitle' => $instructor->email . ($instructor->phone ? ' | ' . $instructor->phone : ''),
        'icon' => 'fas fa-chalkboard-teacher',
    ])

    @include('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'منح', 'value' => number_format($programs->count()), 'icon' => 'fas fa-award', 'description' => 'برامج معيّنة'],
        ['label' => 'طلاب مفعّلون', 'value' => number_format($programs->sum('activated_count')), 'icon' => 'fas fa-user-check', 'description' => 'وصول نشط'],
        ['label' => 'بانتظار التفعيل', 'value' => number_format($programs->sum('pending_count')), 'icon' => 'fas fa-hourglass-half', 'description' => 'طلبات معلّقة'],
    ]])

    <section class="{{ $schSectionClass }}">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">منح هذا المدرب</h3>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($programs as $program)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="font-bold text-slate-900">{{ $program->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $program->pending_count }} بانتظار — {{ $program->activated_count }} مفعّل</p>
                    </div>
                    <a href="{{ route('admin.scholarships.programs.show', $program) }}" class="text-sm font-semibold text-blue-600 hover:underline">إدارة المنحة</a>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-slate-500">لا توجد منح</div>
            @endforelse
        </div>
    </section>

    <section class="{{ $schSectionClass }}">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">طلاب مفعّلون</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الطالب</th>
                        <th class="px-6 py-4 text-right">المنحة</th>
                        <th class="px-6 py-4 text-center">تاريخ التفعيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($activatedStudents as $registration)
                        <tr class="sch-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $registration->user?->name }}</td>
                            <td class="px-6 py-4">{{ $registration->program?->name }}</td>
                            <td class="px-6 py-4 text-center">{{ $registration->activated_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">لا يوجد طلاب مفعّلون</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activatedStudents->hasPages())<div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $activatedStudents->links() }}</div>@endif
    </section>
</div>
@endsection
