@extends('layouts.app')

@section('title', 'تسليمات: ' . $assignment->title . ' - Mindlytics')
@section('header', 'تسليمات الواجب')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.assignments.index') }}" class="hover:text-sky-600">الواجبات</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.assignments.show', $assignment) }}" class="hover:text-sky-600">{{ $assignment->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">التسليمات</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">تسليمات: {{ $assignment->title }}</h1>
                <p class="text-sm text-slate-600 mt-0.5">الحد الأقصى: {{ $assignment->max_score }} نقطة</p>
            </div>
            <a href="{{ route('instructor.assignments.show', $assignment) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                <i class="fas fa-arrow-right"></i> العودة للواجب
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 font-bold text-slate-800">قائمة التسليمات</div>
        <div class="overflow-x-auto">
            @if($submissions->count() > 0)
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">الطالب / المجموعة</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">تاريخ التسليم</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">الدرجة</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">الحالة</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3 text-sm text-slate-800">
                                    @if($sub->group_id && $sub->relationLoaded('group'))
                                        <span class="text-sky-600">{{ $sub->group->name ?? 'مجموعة' }}</span>
                                        <span class="text-slate-500">({{ $sub->student->name ?? '—' }})</span>
                                    @else
                                        {{ $sub->student->name ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $sub->submitted_at?->format('Y/m/d H:i') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($sub->score !== null)
                                        <span class="font-semibold text-slate-800">{{ $sub->score }}/{{ $assignment->max_score }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($sub->status === 'graded')
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-emerald-100 text-emerald-700">مقيّم</span>
                                    @elseif($sub->status === 'returned')
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-sky-100 text-sky-700">مُرجع</span>
                                    @else
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-amber-100 text-amber-700">قيد المراجعة</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" onclick="toggleDetail({{ $sub->id }})" class="text-sky-600 hover:text-sky-700 text-sm font-semibold">
                                        <i class="fas fa-eye"></i> عرض / تقييم
                                    </button>
                                </td>
                            </tr>
                            <tr id="detail-{{ $sub->id }}" class="hidden bg-slate-50/70">
                                <td colspan="5" class="px-4 py-4">
                                    <div class="space-y-4 max-w-3xl">
                                        @if($sub->content)
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 mb-1">المحتوى</h4>
                                                <div class="text-sm text-slate-600 whitespace-pre-wrap rounded-lg bg-white p-3 border border-slate-200">{{ $sub->content }}</div>
                                            </div>
                                        @endif
                                        @if($sub->attachments && count($sub->attachments) > 0)
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 mb-1">المرفقات</h4>
                                                <ul class="text-sm space-y-1">
                                                    @foreach($sub->attachments as $att)
                                                        @php
                                                            $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                                            $url = $path ? (str_starts_with($path, 'http') ? $path : url('storage/' . $path)) : '#';
                                                            $label = is_array($att) ? ($att['name'] ?? basename($path ?? 'مرفق')) : basename($att);
                                                        @endphp
                                                        <li>
                                                            <a href="{{ $url }}" target="_blank" rel="noopener" class="text-sky-600 hover:underline">
                                                                {{ $label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        @if($sub->feedback)
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-700 mb-1">التغذية الراجعة</h4>
                                                <p class="text-sm text-slate-600">{{ $sub->feedback }}</p>
                                            </div>
                                        @endif
                                        <form action="{{ route('instructor.assignments.grade', [$assignment, $sub]) }}" method="POST" class="flex flex-wrap items-end gap-4 pt-2 border-t border-slate-200">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 mb-1">الدرجة (0–{{ $assignment->max_score }})</label>
                                                <input type="number" name="score" min="0" max="{{ $assignment->max_score }}" value="{{ old('score', $sub->score) }}" class="w-24 px-3 py-2 border border-slate-200 rounded-lg text-slate-800">
                                            </div>
                                            <div class="flex-1 min-w-[200px]">
                                                <label class="block text-xs font-semibold text-slate-600 mb-1">التغذية الراجعة</label>
                                                <input type="text" name="feedback" value="{{ old('feedback', $sub->feedback) }}" placeholder="تعليق اختياري" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-800">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
                                                <select name="status" class="px-3 py-2 border border-slate-200 rounded-lg text-slate-800">
                                                    <option value="submitted" {{ $sub->status === 'submitted' ? 'selected' : '' }}>قيد المراجعة</option>
                                                    <option value="graded" {{ $sub->status === 'graded' ? 'selected' : '' }}>مقيّم</option>
                                                    <option value="returned" {{ $sub->status === 'returned' ? 'selected' : '' }}>مُرجع</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg font-semibold text-sm">
                                                <i class="fas fa-check"></i> حفظ التقييم
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3 border-t border-slate-200">{{ $submissions->links() }}</div>
            @else
                <p class="p-8 text-center text-slate-500">لا توجد تسليمات لهذا الواجب بعد.</p>
            @endif
        </div>
    </div>
</div>

@if($submissions->count() > 0)
<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (!row) return;
    row.classList.toggle('hidden');
}
</script>
@endif
@endsection
