@extends('layouts.admin')

@section('title', 'HR — تفاصيل التقديم')
@section('header', 'HR — تفاصيل التقديم')

@section('content')
@php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $score = $application->score;
    $selectedRubricId = (int) (request('rubric_id')
        ?? old('rubric_id')
        ?? $score?->rubric_id
        ?? ($rubrics->firstWhere('is_default', true)?->id ?? ($rubrics->first()?->id)));
    $selectedRubric = $rubrics->firstWhere('id', (int) $selectedRubricId);
    $criteria = $selectedRubric?->criteria_json ?? [];
    $existingScores = is_array($score?->scores_json) ? $score->scores_json : [];
@endphp

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">تفاصيل التقديم</h2>
            <p class="text-xs text-slate-600 mt-1">وظيفة: <strong>{{ $application->job?->title ?: '—' }}</strong> — تاريخ: {{ $application->submitted_at?->format('Y-m-d H:i') ?: '—' }}</p>
        </div>
        <a href="{{ route('admin.hr.applications.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-arrow-right"></i>
            رجوع
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
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

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <section class="xl:col-span-7 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user text-pink-600"></i>
                    بيانات المتقدم
                </h3>
                <form method="post" action="{{ route('admin.hr.applications.status', $application) }}" class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <select name="status" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($statusLabels as $k => $label)
                            <option value="{{ $k }}" @selected(old('status', $application->status) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                        <i class="fas fa-save"></i>
                        حفظ الحالة
                    </button>
                </form>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">الاسم</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">الهاتف</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->phone ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">البريد</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->email ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">المصدر</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->source ?: '—' }}</p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">LinkedIn</p>
                        @if($application->linkedin_url)
                            <a target="_blank" href="{{ $application->linkedin_url }}" class="text-sm font-semibold text-sky-700 underline">فتح</a>
                        @else
                            <p class="text-sm font-bold text-slate-900">—</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Portfolio</p>
                        @if($application->portfolio_url)
                            <a target="_blank" href="{{ $application->portfolio_url }}" class="text-sm font-semibold text-sky-700 underline">فتح</a>
                        @else
                            <p class="text-sm font-bold text-slate-900">—</p>
                        @endif
                    </div>
                </div>

                @if($application->cover_letter)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">Cover letter</p>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-800 whitespace-pre-line">{{ $application->cover_letter }}</div>
                    </div>
                @endif

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-2">الملفات</p>
                    <div class="space-y-2">
                        @forelse($application->files as $file)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $file->original_name ?: $file->path }}</p>
                                    <p class="text-[11px] text-slate-500">نوع: {{ $file->kind }} — {{ $file->mime ?: '—' }} — {{ $file->size ? number_format($file->size/1024, 1) . ' KB' : '—' }}</p>
                                </div>
                                <a href="{{ stored_upload_file_url($file->asStoredUploadArray()) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </a>
                            </div>
                        @empty
                            <div class="text-sm text-slate-600">لا توجد ملفات.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <aside class="xl:col-span-5 space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-rose-50/60">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-star text-rose-600"></i>
                        التقييم (Score)
                    </h3>
                    <p class="text-xs text-slate-600 mt-1">Rubric + درجات لكل معيار. يتم حساب الـ total تلقائياً.</p>
                </div>
                <div class="p-5 space-y-4">
                    @if($rubrics->isEmpty())
                        <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
                            لا يوجد قوالب تقييم. أنشئ واحداً من <a class="underline font-semibold" href="{{ route('admin.hr.rubrics.create') }}">هنا</a>.
                        </div>
                    @else
                        <form method="post" action="{{ route('admin.hr.applications.score', $application) }}" class="space-y-4">
                            @csrf @method('PUT')

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">قالب التقييم</label>
                                <select name="rubric_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                                        onchange="window.location.search = new URLSearchParams({ rubric_id: this.value }).toString();">
                                    @foreach($rubrics as $r)
                                        <option value="{{ $r->id }}" @selected((int) $selectedRubricId === (int) $r->id)>{{ $r->name }}@if($r->is_default) (افتراضي) @endif</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">عند تغيير القالب سيتم إعادة تحميل الصفحة لعرض المعايير.</p>
                            </div>

                            <div class="space-y-3">
                                @forelse($criteria as $c)
                                    @php
                                        $key = (string) ($c['key'] ?? '');
                                        $label = (string) ($c['label'] ?? $key);
                                        $weight = (float) ($c['weight'] ?? 1);
                                        $max = (float) ($c['max'] ?? 10);
                                    @endphp
                                    @if($key !== '')
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-bold text-slate-900">{{ $label }}</p>
                                                <p class="text-xs text-slate-500">وزن: {{ $weight }} · Max: {{ $max }}</p>
                                            </div>
                                            <input type="number" name="scores[{{ $key }}]" step="0.1" min="0" max="{{ $max }}"
                                                   value="{{ old('scores.'.$key, $existingScores[$key] ?? 0) }}"
                                                   class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        </div>
                                    @endif
                                @empty
                                    <div class="text-sm text-slate-600">لا توجد معايير في هذا القالب.</div>
                                @endforelse
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظات</label>
                                <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('notes', $score?->notes ?? '') }}</textarea>
                            </div>

                            <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold">
                                <i class="fas fa-save"></i>
                                حفظ التقييم
                            </button>
                        </form>
                    @endif

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold text-slate-500">النتيجة الحالية</p>
                        <p class="text-2xl font-black text-slate-900 tabular-nums">{{ $score?->total_score !== null ? number_format((float) $score->total_score, 2) : '—' }}</p>
                        <p class="text-[11px] text-slate-500 mt-1">آخر تقييم: {{ $score?->scored_at?->format('Y-m-d H:i') ?: '—' }}</p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

