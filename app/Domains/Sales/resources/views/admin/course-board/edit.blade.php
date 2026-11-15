@extends('sales::crm.layout')

@section('title', 'تعديل '.$entry->name)
@section('header', 'تعديل كورس المبيعات')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <a href="{{ route('admin.sales-course-board.index') }}" class="text-sm text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right ml-1"></i> رجوع للقائمة</a>
                <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $entry->name }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($linkedCourse ?? null)
                    <a href="{{ route('public.course.show', $linkedCourse->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-800 border border-blue-200 text-sm font-semibold">
                        <i class="fas fa-external-link-alt"></i> معاينة صفحة الكورس
                    </a>
                @endif
                @if($entry->landingUrl())
                    <a href="{{ $entry->landingUrl() }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm font-semibold">
                        <i class="fas fa-external-link-alt"></i> معاينة Landing
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
                <ul class="list-disc pr-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sales-course-board.update', $entry) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('sales::admin.course-board._form', ['entry' => $entry, 'courses' => $courses, 'linkedCourse' => $linkedCourse ?? null])
            <div class="pt-4 flex flex-wrap gap-3">
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>
@endsection
