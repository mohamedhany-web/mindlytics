@extends('layouts.app')

@section('title', 'منهج الكورس الأوفلاين — ' . $offlineCourse->title)
@section('header', 'بناء المنهج (أوفلاين)')

@section('content')
@php
    $attachUrl = route('instructor.offline-courses.curriculum.attach-item', $offlineCourse);
@endphp
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="{{ route('instructor.offline-courses.index') }}" class="hover:text-amber-600">كورساتي الأوفلاين</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('instructor.offline-courses.show', $offlineCourse) }}" class="hover:text-amber-600">{{ $offlineCourse->title }}</a>
                    <span class="mx-1">/</span>
                    <span class="text-slate-700 font-semibold">المنهج</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-800">بناء منهج الكورس الأوفلاين</h1>
                <p class="text-sm text-slate-500 mt-1">نظّم المحاضرات والموارد والأنشطة والاختبارات في أقسام، كما في منهج الكورسات الأونلاين.</p>
            </div>
            <a href="{{ route('instructor.offline-courses.show', $offlineCourse) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-4">
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5">
                <h2 class="font-bold text-slate-800 mb-3 flex items-center gap-2"><i class="fas fa-plus-circle text-amber-600"></i> قسم رئيسي جديد</h2>
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
                    @include('instructor.offline-curriculum.partials.section-block', ['section' => $section, 'depth' => 0, 'offlineCourse' => $offlineCourse])
                @empty
                    <div class="text-center py-14 rounded-2xl border border-dashed border-slate-200 bg-white">
                        <i class="fas fa-sitemap text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-600 mb-2">لا توجد أقسام بعد.</p>
                        <p class="text-sm text-slate-500">أنشئ قسمًا رئيسيًا أعلاه، ثم أضف عناصر من العمود الأيمن.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 h-fit xl:sticky xl:top-4">
            <h2 class="text-lg font-bold text-slate-800 mb-1">إضافة للمنهج</h2>
            <p class="text-xs text-slate-500 mb-4">اختر القسم من القائمة ثم أرسل الإضافة.</p>

            @if(count($sectionsFlat) === 0)
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-100 rounded-xl p-3">أنشئ قسمًا أولًا.</p>
            @else
                <div class="space-y-5 max-h-[70vh] overflow-y-auto pr-1">
                    @if($lectures->isNotEmpty())
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-violet-500"></i> محاضرات</h3>
                            <div class="space-y-2">
                                @foreach($lectures as $lecture)
                                    <form action="{{ $attachUrl }}" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        @csrf
                                        <input type="hidden" name="item_type" value="{{ \App\Models\OfflineLecture::class }}">
                                        <input type="hidden" name="item_id" value="{{ $lecture->id }}">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $lecture->title }}</p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            @foreach($sectionsFlat as $opt)
                                                <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-violet-500 hover:bg-violet-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($resources->isNotEmpty())
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-file-alt text-sky-500"></i> موارد</h3>
                            <div class="space-y-2">
                                @foreach($resources as $resource)
                                    <form action="{{ $attachUrl }}" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        @csrf
                                        <input type="hidden" name="item_type" value="{{ \App\Models\OfflineCourseResource::class }}">
                                        <input type="hidden" name="item_id" value="{{ $resource->id }}">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $resource->title }}</p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            @foreach($sectionsFlat as $opt)
                                                <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($activities->isNotEmpty())
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-tasks text-amber-500"></i> أنشطة</h3>
                            <div class="space-y-2">
                                @foreach($activities as $activity)
                                    <form action="{{ $attachUrl }}" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        @csrf
                                        <input type="hidden" name="item_type" value="{{ \App\Models\OfflineActivity::class }}">
                                        <input type="hidden" name="item_id" value="{{ $activity->id }}">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $activity->title }}</p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            @foreach($sectionsFlat as $opt)
                                                <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($exams->isNotEmpty())
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-clipboard-check text-emerald-500"></i> اختبارات</h3>
                            <div class="space-y-2">
                                @foreach($exams as $exam)
                                    <form action="{{ $attachUrl }}" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        @csrf
                                        <input type="hidden" name="item_type" value="{{ \App\Models\AdvancedExam::class }}">
                                        <input type="hidden" name="item_id" value="{{ $exam->id }}">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $exam->title }}</p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            @foreach($sectionsFlat as $opt)
                                                <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($lectures->isEmpty() && $resources->isEmpty() && $activities->isEmpty() && $exams->isEmpty())
                        <p class="text-sm text-slate-500">لا يوجد محتوى بعد. أضف محاضرات أو موارد أو أنشطة من صفحة الكورس.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
