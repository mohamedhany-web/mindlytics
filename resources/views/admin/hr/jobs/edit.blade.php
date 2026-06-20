@extends('layouts.admin')

@section('title', 'HR — تعديل وظيفة')
@section('header', 'HR — تعديل وظيفة')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">تعديل وظيفة</h2>
            <p class="text-xs text-slate-600 mt-1">طلبات: <strong>{{ $job->applications_count ?? 0 }}</strong> — رابط عام: <a class="underline text-sky-700" target="_blank" href="{{ route('careers.show', $job) }}">{{ route('careers.show', $job) }}</a></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.hr.jobs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
            <form method="post" action="{{ route('admin.hr.jobs.destroy', $job) }}" onsubmit="return confirm('حذف الوظيفة؟ سيتم حذف جميع التقديمات التابعة لها.');">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-rose-300 bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100">
                    <i class="fas fa-trash"></i>
                    حذف
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('admin.hr.jobs.update', $job) }}" class="rounded-2xl border border-slate-200 bg-white p-6 space-y-6">
        @csrf @method('PUT')
        @include('admin.hr.jobs._form', ['job' => $job])

        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <button class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold">
                <i class="fas fa-save"></i>
                حفظ
            </button>
        </div>
    </form>
</div>
@endsection

