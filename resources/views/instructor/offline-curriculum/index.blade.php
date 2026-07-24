@extends('layouts.app')

@section('title', 'منهج الكورس الأوفلاين — ' . $offlineCourse->title)
@section('header', 'بناء المنهج (أوفلاين)')

@section('content')
@php
    $attachUrl = route('instructor.offline-courses.curriculum.attach-item', $offlineCourse);
    $curriculumChannel = $curriculumChannel ?? (request()->query('channel') === 'online' ? 'online' : 'offline');
@endphp
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="{{ route('instructor.offline-courses.index', ['channel' => $curriculumChannel]) }}" class="hover:text-amber-600">كورساتي الأوفلاين</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('instructor.offline-courses.show', $offlineCourse) }}?channel={{ urlencode($curriculumChannel) }}" class="hover:text-amber-600">{{ $offlineCourse->title }}</a>
                    <span class="mx-1">/</span>
                    <span class="text-slate-700 font-semibold">المنهج</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-800">بناء منهج الكورس الأوفلاين</h1>
                <p class="text-sm text-slate-500 mt-1">نظّم المحاضرات والموارد والأنشطة والاختبارات في أقسام، كما في منهج الكورسات الأونلاين.</p>
            </div>
            <a href="{{ route('instructor.offline-courses.show', $offlineCourse) }}?channel={{ urlencode($curriculumChannel) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">
                <i class="fas fa-arrow-right"></i> صفحة الكورس
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="space-y-4">
        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5">
            <h2 class="font-bold text-slate-800 mb-3 flex items-center gap-2"><i class="fas fa-plus-circle text-amber-600"></i> قسم رئيسي جديد</h2>
            <p class="text-xs text-slate-600 mb-3">
                من كل بطاقة قسم يمكنك <strong>إنشاء محاضرة جديدة</strong> مرتبطة بالقسم مباشرة، أو ربط عناصر أُنشئت مسبقاً (مورد، نشاط، اختبار).
                المحاضرة الأوفلاين تدعم وصف الجلسة، <strong>نقاط اليوم</strong> (سطر لكل نقطة)، تسجيل إعادة الاستماع، مرفقات وروابط تحميل — وتظهر ملخصاتها داخل المنهج بعد الحفظ.
            </p>
            <form action="{{ route('instructor.offline-courses.curriculum.sections.store', $offlineCourse) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">العنوان</label>
                    <input type="text" name="title" required maxlength="255" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="مثال: الوحدة الأولى — مقدمة">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">وصف القسم (اختياري)</label>
                    <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="نبذة للطلاب عن محتوى القسم"></textarea>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold">إنشاء القسم</button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse($sections as $section)
                @include('instructor.offline-curriculum.partials.section-block', ['section' => $section, 'depth' => 0, 'offlineCourse' => $offlineCourse, 'curriculumChannel' => $curriculumChannel, 'groupSessions' => $groupSessions])
            @empty
                <div class="text-center py-14 rounded-2xl border border-dashed border-slate-200 bg-white">
                    <i class="fas fa-sitemap text-4xl text-slate-300 mb-3"></i>
                    <p class="text-slate-600 mb-2">لا توجد أقسام بعد.</p>
                    <p class="text-sm text-slate-500">أنشئ قسمًا رئيسيًا أعلاه.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- بوب أب مشاهدة تسجيل المحاضرة داخل المنصة --}}
<div id="curriculumRecordingModal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" data-close-recording-modal></div>
    <div class="relative z-10 flex min-h-full items-center justify-center p-3 sm:p-6">
        <div class="w-full max-w-4xl rounded-2xl bg-white shadow-2xl overflow-hidden border border-slate-200">
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-100 bg-slate-50">
                <h3 id="curriculumRecordingModalTitle" class="text-sm sm:text-base font-bold text-slate-800 truncate">تسجيل المحاضرة</h3>
                <button type="button" data-close-recording-modal class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100" aria-label="إغلاق">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="bg-black aspect-video w-full">
                <iframe id="curriculumRecordingFrame" src="about:blank" class="w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('curriculumRecordingModal');
    const frame = document.getElementById('curriculumRecordingFrame');
    const titleEl = document.getElementById('curriculumRecordingModalTitle');
    if (!modal || !frame) return;

    function openModal(url, title) {
        titleEl.textContent = title || 'تسجيل المحاضرة';
        frame.src = url;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        frame.src = 'about:blank';
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-open-recording-modal');
        if (btn) {
            e.preventDefault();
            openModal(btn.getAttribute('data-watch-url'), btn.getAttribute('data-title'));
            return;
        }
        if (e.target.closest('[data-close-recording-modal]')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
})();
</script>
@endpush
