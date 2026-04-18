@extends('layouts.app')

@section('title', 'إضافة محاضرة - كورس ' . (($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين'))
@section('header', 'إضافة محاضرة')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- هيدر الصفحة (عرض الصفحة الكامل) -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.index', ['channel' => ($channel ?? 'offline')]) }}" class="hover:text-amber-600 transition-colors">{{ ($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين' }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')]) }}" class="hover:text-amber-600 transition-colors">{{ $offlineCourse->title }}</a>
            <span class="mx-2">/</span>
            @if(old('curriculum_section_id', $curriculumSectionId ?? null))
                <a href="{{ route('instructor.offline-courses.curriculum.index', $offlineCourse) }}?channel={{ urlencode($channel ?? 'offline') }}" class="hover:text-amber-600 transition-colors">المنهج</a>
                <span class="mx-2">/</span>
            @endif
            <a href="{{ route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')]) }}" class="hover:text-amber-600 transition-colors">الجلسات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">إضافة محاضرة</span>
        </nav>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                <i class="fas fa-chalkboard-teacher text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">إضافة محاضرة ({{ ($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين' }})</h1>
                <p class="text-sm text-slate-600 mt-0.5">
                    @if(old('curriculum_section_id', $curriculumSectionId ?? null))
                        ستُضاف المحاضرة تلقائياً إلى القسم الذي اخترته في المنهج بعد الحفظ.
                    @else
                        إضافة محاضرة مع برنامج اليوم، تسجيل، روابط تحميل ومرفقات للطلاب.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('instructor.offline-courses.lectures.store', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @if(old('curriculum_section_id', $curriculumSectionId ?? null))
                <input type="hidden" name="curriculum_section_id" value="{{ old('curriculum_section_id', $curriculumSectionId) }}">
            @endif
            @error('curriculum_section_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="مثال: المحاضرة الأولى">
                    @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                @include('instructor.offline-courses.lectures.partials.session-select', [
                    'groupSessions' => $groupSessions ?? collect(),
                    'required' => ($hasGroupSessions ?? false),
                    'value' => old('offline_group_session_id'),
                ])
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="شرح عام للمحاضرة أو أهدافها">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">برنامج اليوم (نقاط)</label>
                    <textarea name="session_agenda" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 font-mono text-sm" placeholder="اكتب كل نقطة في سطر منفصل، مثال:&#10;- مراجعة الدرس السابق&#10;- شرح الوحدة الجديدة&#10;- تطبيق عملي">{{ old('session_agenda') }}</textarea>
                    <p class="text-xs text-slate-500 mt-1">يُعرض للطلاب كقائمة نقاط داخل صفحة الكورس والمنهج.</p>
                    @error('session_agenda')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                @include('instructor.offline-courses.lectures.partials.offline-mindmap-field', ['variant' => 'default', 'value' => old('offline_attendee_mindmap')])
                @if(!($hasGroupSessions ?? false))
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">موعد المحاضرة</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">المدة (دقيقة)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="0" max="600" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">عند اختيار جلسة من التقويم، يُحدَّد موعد المحاضرة ومدتها تلقائياً من بيانات الجلسة.</p>
                @endif
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رابط الميتينج (للأونلاين)</label>
                    <input type="url" name="meeting_url" value="{{ old('meeting_url') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="https://meet.google.com/...">
                    @error('meeting_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رابط تسجيل المحاضرة</label>
                    <input type="url" name="recording_url" value="{{ old('recording_url') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">روابط تحميل (اختياري)</label>
                    <div id="downloadLinks">
                        <div class="flex gap-2 mb-2">
                            <input type="text" name="download_links[0][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                            <input type="url" name="download_links[0][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                        </div>
                    </div>
                    <button type="button" id="addLink" class="text-sm text-violet-600 font-medium">+ إضافة رابط</button>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">مرفقات (ملفات)</label>
                    <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">{{ old('notes') }}</textarea>
                </div>
                @if($groups->isNotEmpty())
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة (اختياري)</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                            <option value="">كل الطلاب</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">حفظ</button>
                <a href="{{ old('curriculum_section_id', $curriculumSectionId ?? null) ? route('instructor.offline-courses.curriculum.index', $offlineCourse).'?channel='.urlencode($channel ?? 'offline') : route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')]) }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
var linkIndex = 1;
document.getElementById('addLink').addEventListener('click', function() {
    var div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = '<input type="text" name="download_links[' + linkIndex + '][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">' +
        '<input type="url" name="download_links[' + linkIndex + '][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">';
    document.getElementById('downloadLinks').appendChild(div);
    linkIndex++;
});
</script>
@endpush
@endsection
