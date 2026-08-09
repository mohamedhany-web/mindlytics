@extends('layouts.app')

@section('title', 'اختر تصميم شهادتك')
@section('header', 'إصدار الشهادة')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 py-6 px-4" x-data="{ template: '{{ old('template', 'emerald-classic') }}', name: @js(old('display_name', $defaultName)) }">
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc pe-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">إكمال 100٪</p>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $course->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">اختر تصميم الشهادة — هتظهر باسمك واسم الكورس، مع رقم تسلسلي موثّق بدل توقيع مدير البرنامج، وتوقيع المدرب كما هو.</p>
            </div>
            <a href="{{ route('student.certificates.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800">رجوع</a>
        </div>
    </div>

    <form method="POST" action="{{ route('student.certificates.claim.store', $course) }}" class="space-y-6">
        @csrf
        <input type="hidden" name="template" :value="template">

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <label class="block text-sm font-bold text-slate-800 mb-2">الاسم على الشهادة</label>
            <input type="text" name="display_name" x-model="name" maxlength="120" required
                   class="w-full max-w-xl rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <p class="text-xs text-slate-500 mt-2">اكتب الاسم كما تريد ظهوره على الشهادة.</p>
        </div>

        <div>
            <h2 class="text-base font-black text-slate-900 mb-3">اختر التصميم</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($designs as $key => $design)
                    <button type="button" @click="template = '{{ $key }}'"
                            class="text-start rounded-2xl border-2 overflow-hidden bg-white transition-all"
                            :class="template === '{{ $key }}' ? 'border-emerald-500 shadow-lg ring-2 ring-emerald-200' : 'border-slate-200 hover:border-slate-300'">
                        <div class="h-40 bg-slate-900 overflow-hidden relative">
                            <iframe src="{{ route('student.certificates.design-preview', $key) }}" class="pointer-events-none absolute inset-0 w-[1122px] h-[793px] origin-top-left" style="transform: scale(0.32);" tabindex="-1" loading="lazy"></iframe>
                        </div>
                        <div class="p-4">
                            <p class="font-bold text-slate-900">{{ $design['name'] }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $design['description'] }}</p>
                            <p class="text-[11px] font-bold mt-2" :class="template === '{{ $key }}' ? 'text-emerald-700' : 'text-slate-400'">
                                <span x-show="template === '{{ $key }}'">✓ محدد</span>
                                <span x-show="template !== '{{ $key }}'">اضغط للاختيار</span>
                            </p>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 text-sm shadow-lg shadow-emerald-600/20">
                إصدار الشهادة بالتصميم المختار
            </button>
            <a href="{{ route('public.certificates.verify') }}" target="_blank" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                صفحة التحقق بالسيريال
            </a>
        </div>
    </form>
</div>
@endsection
