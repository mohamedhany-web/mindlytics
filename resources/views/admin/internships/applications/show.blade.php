@extends('layouts.admin')

@section('title', 'طلب تدريب: ' . $application->name)
@section('header', 'تفاصيل طلب التدريب')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500';
    $labelClass = 'block text-sm font-semibold text-slate-800 mb-1.5';
@endphp

<div class="p-4 sm:p-6 lg:p-8 space-y-6" style="background: #f8fafc; min-height: 100%;">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-user text-cyan-600"></i>
                <span>{{ $application->name }}</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">{{ $application->internship->title ?? '—' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700">{{ $application->statusLabel() }}</span>
            <a href="{{ route('admin.internship-applications.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
                <i class="fas fa-arrow-right"></i>
                رجوع للطلبات
            </a>
        </div>
    </div>

    <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-id-card text-cyan-600"></i>
                بيانات المتقدم
            </h3>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                <div class="text-xs font-semibold text-slate-500 mb-1">البريد</div>
                <div class="font-semibold text-slate-900 break-all">{{ $application->email }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                <div class="text-xs font-semibold text-slate-500 mb-1">الهاتف</div>
                <div class="font-semibold text-slate-900">{{ $application->phone ?: '—' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                <div class="text-xs font-semibold text-slate-500 mb-1">الجامعة</div>
                <div class="font-semibold text-slate-900">{{ $application->university ?: '—' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                <div class="text-xs font-semibold text-slate-500 mb-1">التخصص</div>
                <div class="font-semibold text-slate-900">{{ $application->major ?: '—' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                <div class="text-xs font-semibold text-slate-500 mb-1">السنة الدراسية</div>
                <div class="font-semibold text-slate-900">{{ $application->year_of_study ?: '—' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                <div class="text-xs font-semibold text-slate-500 mb-1">تاريخ التقديم</div>
                <div class="font-semibold text-slate-900">{{ $application->created_at?->format('Y-m-d H:i') }}</div>
            </div>

            @if($application->portfolio_url)
                <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 md:col-span-2">
                    <div class="text-xs font-semibold text-slate-500 mb-1">Portfolio</div>
                    <a class="font-semibold text-cyan-700 hover:underline break-all" href="{{ $application->portfolio_url }}" target="_blank">{{ $application->portfolio_url }}</a>
                </div>
            @endif
            @if($application->github_url)
                <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                    <div class="text-xs font-semibold text-slate-500 mb-1">GitHub</div>
                    <a class="font-semibold text-cyan-700 hover:underline break-all" href="{{ $application->github_url }}" target="_blank">{{ $application->github_url }}</a>
                </div>
            @endif
            @if($application->linkedin_url)
                <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                    <div class="text-xs font-semibold text-slate-500 mb-1">LinkedIn</div>
                    <a class="font-semibold text-cyan-700 hover:underline break-all" href="{{ $application->linkedin_url }}" target="_blank">{{ $application->linkedin_url }}</a>
                </div>
            @endif
            @if($application->cv_path)
                <div class="rounded-xl bg-cyan-50 border border-cyan-100 px-4 py-3 md:col-span-2">
                    <div class="text-xs font-semibold text-cyan-700 mb-1">السيرة الذاتية</div>
                    <a class="inline-flex items-center gap-2 font-semibold text-cyan-800 hover:underline" href="{{ $application->cvUrl() }}" target="_blank">
                        <i class="fas fa-file-download"></i>
                        تحميل / عرض CV
                    </a>
                </div>
            @endif
        </div>

        @if($application->cover_letter)
            <div class="px-5 sm:px-6 pb-6">
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-4">
                    <h2 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-quote-right text-cyan-600 text-sm"></i>
                        خطاب التقديم
                    </h2>
                    <p class="text-slate-600 whitespace-pre-line text-sm leading-relaxed">{{ $application->cover_letter }}</p>
                </div>
            </div>
        @endif
    </section>

    <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-tasks text-cyan-600"></i>
                تحديث الحالة
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.internship-applications.status', $application) }}" class="p-5 sm:p-6 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="{{ $labelClass }}">الحالة</label>
                <select name="status" class="{{ $inputClass }}">
                    @foreach(\App\Models\InternshipApplication::statuses() as $key => $label)
                        <option value="{{ $key }}" @selected($application->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">ملاحظات الإدارة</label>
                <textarea name="admin_notes" rows="4" class="{{ $inputClass }}">{{ old('admin_notes', $application->admin_notes) }}</textarea>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                <button type="submit" form="delete-app"
                        class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100"
                        onclick="return confirm('حذف الطلب؟')">
                    <i class="fas fa-trash"></i>
                    حذف الطلب
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-lg">
                    <i class="fas fa-save"></i>
                    حفظ الحالة
                </button>
            </div>
            @if($application->reviewer)
                <p class="text-xs text-slate-500">آخر مراجعة: {{ $application->reviewer->name }} · {{ $application->reviewed_at?->diffForHumans() }}</p>
            @endif
        </form>
    </section>

    <form id="delete-app" method="POST" action="{{ route('admin.internship-applications.destroy', $application) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
