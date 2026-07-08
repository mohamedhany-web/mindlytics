@extends('layouts.admin')

@section('title', 'تعديل فريق — '.$team->name)
@section('header', 'المبيعات — تعديل الفريق')

@section('content')
<div class="max-w-3xl space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif
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
                    <i class="fas fa-pen"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">{{ $team->name }}</h2>
                    <p class="text-xs text-slate-600">تعديل المدير والأعضاء وإعدادات الفريق.</p>
                </div>
            </div>
            <a href="{{ route('admin.sales.sales-teams.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>

        <form method="POST" action="{{ route('admin.sales.sales-teams.update', $team) }}" class="p-6">
            @csrf
            @method('PUT')
            @include('admin.sales.sales-teams._form')
            <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
                <a href="{{ route('admin.sales.sales-teams.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    إلغاء
                </a>
            </div>
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-rose-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-rose-100 bg-rose-50">
            <h3 class="text-sm font-black text-rose-900 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                منطقة خطرة
            </h3>
        </div>
        <div class="p-4">
            <p class="text-sm text-slate-600 mb-4">حذف الفريق يزيل ربط الأعضاء — لن يُحذف الموظفون أنفسهم.</p>
            <form method="POST" action="{{ route('admin.sales.sales-teams.destroy', $team) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفريق؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-rose-600 hover:bg-rose-700">
                    <i class="fas fa-trash"></i>
                    حذف الفريق
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
