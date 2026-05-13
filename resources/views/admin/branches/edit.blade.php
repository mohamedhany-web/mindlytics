@extends('layouts.admin')

@section('title', 'تعديل فرع — ' . $branch->name)
@section('header', 'تعديل الفرع')

@section('content')
<div class="space-y-6 pb-16">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shrink-0">
                    <i class="fas fa-pen text-lg"></i>
                </div>
                <div class="min-w-0">
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ route('admin.branches.index') }}" class="text-blue-600 hover:text-blue-700">الفروع</a>
                        <span>/</span>
                        <span class="text-slate-600">تعديل</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1 truncate">{{ $branch->name }}</h2>
                    <p class="text-sm text-slate-600 mt-1 font-mono truncate" dir="ltr">{{ $branch->slug }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('admin.branches.show', $branch) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-eye"></i>
                    عرض التفاصيل
                </a>
                <a href="{{ route('admin.branches.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <form action="{{ route('admin.branches.update', $branch) }}" method="POST" class="space-y-0">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <div class="lg:col-span-2 space-y-6">
                    @include('admin.branches._form', ['branch' => $branch])
                </div>
                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-amber-500"></i>
                            عند تغيير slug أو الدومين
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed">قد تحتاج لتحديث DNS أو انتظار إبطال كاش المطابقة تلقائياً بعد الحفظ.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                        <div class="flex flex-col gap-3">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all duration-200">
                                <i class="fas fa-save"></i>
                                <span>حفظ التعديلات</span>
                            </button>
                            <a href="{{ route('admin.branches.show', $branch) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-300 px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all">
                                <i class="fas fa-eye"></i>
                                <span>عرض فقط</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
