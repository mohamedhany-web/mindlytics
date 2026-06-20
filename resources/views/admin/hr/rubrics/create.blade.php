@extends('layouts.admin')

@section('title', 'HR — قالب تقييم جديد')
@section('header', 'HR — قالب تقييم جديد')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">قالب تقييم جديد</h2>
            <p class="text-xs text-slate-600 mt-1">أنشئ Rubric بمعايير وأوزان لحساب Score.</p>
        </div>
        <a href="{{ route('admin.hr.rubrics.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-arrow-right"></i>
            رجوع
        </a>
    </div>

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

    <form method="post" action="{{ route('admin.hr.rubrics.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 space-y-6">
        @csrf
        @include('admin.hr.rubrics._form', ['defaultCriteria' => $defaultCriteria])

        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <button class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold">
                <i class="fas fa-save"></i>
                إنشاء
            </button>
            <a href="{{ route('admin.hr.rubrics.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection

