@extends('layouts.app')

@section('title', 'تعديل محاضرة - كورس أوفلاين')
@section('header', 'تعديل محاضرة')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.lectures.index', $offlineCourse) }}" class="hover:text-amber-600">المحاضرات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تعديل</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800">تعديل المحاضرة</h1>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="{{ route('instructor.offline-courses.lectures.update', [$offlineCourse, $lecture]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $lecture->title) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                    @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">{{ old('description', $lecture->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">موعد المحاضرة</label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $lecture->scheduled_at ? $lecture->scheduled_at->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">المدة (دقيقة)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lecture->duration_minutes) }}" min="0" max="600" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رابط تسجيل المحاضرة</label>
                    <input type="url" name="recording_url" value="{{ old('recording_url', $lecture->recording_url) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">روابط تحميل</label>
                    <div id="downloadLinks">
                        @php $links = $lecture->download_links ?? []; @endphp
                        @forelse($links as $i => $link)
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="download_links[{{ $i }}][label]" value="{{ $link['label'] ?? '' }}" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                                <input type="url" name="download_links[{{ $i }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                            </div>
                        @empty
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="download_links[0][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                                <input type="url" name="download_links[0][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                            </div>
                        @endforelse
                    </div>
                    <button type="button" id="addLink" class="text-sm text-violet-600 hover:text-violet-700 font-medium">+ إضافة رابط</button>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">مرفقات إضافية (ملفات جديدة)</label>
                    <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">{{ old('notes', $lecture->notes) }}</textarea>
                </div>
                @if($groups->isNotEmpty())
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                            <option value="">كل الطلاب</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ old('group_id', $lecture->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $lecture->is_active) ? 'checked' : '' }} class="rounded border-slate-300">
                        <span class="text-sm font-semibold text-slate-700">نشط (يظهر للطلاب)</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">حفظ</button>
                <a href="{{ route('instructor.offline-courses.lectures.index', $offlineCourse) }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<script>
var linkIndex = {{ count($lecture->download_links ?? []) }};
document.getElementById('addLink').addEventListener('click', function() {
    var div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = '<input type="text" name="download_links[' + linkIndex + '][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">' +
        '<input type="url" name="download_links[' + linkIndex + '][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">';
    document.getElementById('downloadLinks').appendChild(div);
    linkIndex++;
});
</script>
@endsection
