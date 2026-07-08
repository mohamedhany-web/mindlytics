@extends('layouts.admin')

@section('title', 'فريق مبيعات جديد')
@section('header', 'المبيعات — فريق جديد')

@section('content')
<div class="max-w-3xl space-y-6">
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i>{{ session('error') }}
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

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">فريق مبيعات جديد</h2>
                    <p class="text-xs text-slate-600">اربط مدير مبيعات بأعضاء السيلز في فريق واحد.</p>
                </div>
            </div>
            <a href="{{ route('admin.sales.sales-teams.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>

        <form method="POST" action="{{ route('admin.sales.sales-teams.store') }}" class="p-6">
            @csrf
            @include('admin.sales.sales-teams._form', ['team' => null])
            <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-save"></i>
                    حفظ الفريق
                </button>
                <a href="{{ route('admin.sales.sales-teams.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    إلغاء
                </a>
            </div>
        </form>
    </section>
</div>
@endsection
