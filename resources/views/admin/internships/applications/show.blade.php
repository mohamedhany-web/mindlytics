@extends('layouts.admin')

@section('title', 'طلب تدريب: ' . $application->name)
@section('header', 'تفاصيل طلب التدريب')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 font-semibold">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.internship-applications.index') }}" class="text-sky-700 font-semibold hover:underline">← العودة للطلبات</a>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">{{ $application->name }}</h1>
                <p class="text-slate-500 text-sm mt-1">{{ $application->internship->title ?? '—' }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100">{{ $application->statusLabel() }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <p><strong>البريد:</strong> {{ $application->email }}</p>
            <p><strong>الهاتف:</strong> {{ $application->phone ?: '—' }}</p>
            <p><strong>الجامعة:</strong> {{ $application->university ?: '—' }}</p>
            <p><strong>التخصص:</strong> {{ $application->major ?: '—' }}</p>
            <p><strong>السنة الدراسية:</strong> {{ $application->year_of_study ?: '—' }}</p>
            <p><strong>تاريخ التقديم:</strong> {{ $application->created_at?->format('Y-m-d H:i') }}</p>
            @if($application->portfolio_url)
                <p><strong>Portfolio:</strong> <a class="text-sky-700" href="{{ $application->portfolio_url }}" target="_blank">{{ $application->portfolio_url }}</a></p>
            @endif
            @if($application->github_url)
                <p><strong>GitHub:</strong> <a class="text-sky-700" href="{{ $application->github_url }}" target="_blank">{{ $application->github_url }}</a></p>
            @endif
            @if($application->linkedin_url)
                <p><strong>LinkedIn:</strong> <a class="text-sky-700" href="{{ $application->linkedin_url }}" target="_blank">{{ $application->linkedin_url }}</a></p>
            @endif
            @if($application->cv_path)
                <p><strong>السيرة الذاتية:</strong> <a class="text-sky-700 font-semibold" href="{{ $application->cvUrl() }}" target="_blank">تحميل / عرض CV</a></p>
            @endif
        </div>

        @if($application->cover_letter)
            <div>
                <h2 class="font-bold text-slate-900 mb-2">خطاب التقديم</h2>
                <p class="text-slate-600 whitespace-pre-line text-sm">{{ $application->cover_letter }}</p>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.internship-applications.status', $application) }}" class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4">
        @csrf
        @method('PUT')
        <h2 class="font-bold text-slate-900">تحديث الحالة</h2>
        <div>
            <label class="block text-sm font-bold mb-1">الحالة</label>
            <select name="status" class="w-full rounded-xl border-slate-200">
                @foreach(\App\Models\InternshipApplication::statuses() as $key => $label)
                    <option value="{{ $key }}" @selected($application->status === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1">ملاحظات الإدارة</label>
            <textarea name="admin_notes" rows="4" class="w-full rounded-xl border-slate-200">{{ old('admin_notes', $application->admin_notes) }}</textarea>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="px-5 py-2.5 rounded-xl bg-sky-600 text-white font-semibold">حفظ الحالة</button>
            <button form="delete-app" class="px-5 py-2.5 rounded-xl bg-rose-600 text-white font-semibold" onclick="return confirm('حذف الطلب؟')">حذف الطلب</button>
        </div>
        @if($application->reviewer)
            <p class="text-xs text-slate-500">آخر مراجعة: {{ $application->reviewer->name }} · {{ $application->reviewed_at?->diffForHumans() }}</p>
        @endif
    </form>

    <form id="delete-app" method="POST" action="{{ route('admin.internship-applications.destroy', $application) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
