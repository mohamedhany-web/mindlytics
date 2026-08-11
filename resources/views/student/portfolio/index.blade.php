@extends('layouts.app')

@section('title', 'رحلتي - Mindlytics Journey')
@section('header', 'رحلتي')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span class="font-semibold text-emerald-800">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-600"></i>
        <span class="font-semibold text-red-800">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-sky-600 mb-1">Mindlytics Journey</p>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">رحلتي التعليمية</h1>
                <p class="text-sm text-gray-500">ارفع مشاريعك من الكورسات المسجّلة أو الدبلومات (أونلاين/أوفلاين)، راجعها المدرب، ثم انشرها كأدلة موثّقة للشركات.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('student.portfolio.journey') }}" class="inline-flex items-center gap-2 border border-gray-200 text-gray-800 px-4 py-2.5 rounded-lg text-sm font-semibold hover:border-sky-300">
                    <i class="fas fa-id-card"></i>
                    ملف الرحلة
                </a>
                <a href="{{ route('student.portfolio.create') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold">
                    <i class="fas fa-plus"></i>
                    رفع مشروع
                </a>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="rounded-lg bg-slate-50 border border-gray-100 px-3 py-2">
                <div class="text-lg font-black text-gray-900">{{ $counts['total'] }}</div>
                <div class="text-[11px] text-gray-500">كل المشاريع</div>
            </div>
            <div class="rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2">
                <div class="text-lg font-black text-emerald-800">{{ $counts['published'] }}</div>
                <div class="text-[11px] text-emerald-700/80">منشور وموثّق</div>
            </div>
            <div class="rounded-lg bg-amber-50 border border-amber-100 px-3 py-2">
                <div class="text-lg font-black text-amber-800">{{ $counts['in_review'] }}</div>
                <div class="text-[11px] text-amber-700/80">قيد المراجعة</div>
            </div>
            <div class="rounded-lg bg-rose-50 border border-rose-100 px-3 py-2">
                <div class="text-lg font-black text-rose-800">{{ $counts['needs_work'] }}</div>
                <div class="text-[11px] text-rose-700/80">يحتاج تعديلات</div>
            </div>
            <div class="rounded-lg bg-yellow-50 border border-yellow-100 px-3 py-2">
                <div class="text-lg font-black text-yellow-800">{{ $counts['featured'] ?? 0 }}</div>
                <div class="text-[11px] text-yellow-700/80">Featured</div>
            </div>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-lg border border-gray-100 bg-slate-50 px-4 py-3">
            <div>
                <div class="text-sm font-semibold text-gray-800">اكتمال الملف العام: {{ $profile->profile_completion }}%</div>
                <div class="w-48 h-2 bg-white rounded-full mt-2 overflow-hidden border border-gray-100">
                    <div class="h-full bg-sky-500" style="width: {{ min(100, (int)$profile->profile_completion) }}%"></div>
                </div>
            </div>
            <div class="text-xs text-gray-500">
                الرابط العام:
                @if($profile->isPubliclyVisible())
                    <a class="font-semibold text-sky-700" href="{{ route('public.journey.show', $profile->slug) }}" target="_blank">/j/{{ $profile->slug }}</a>
                @else
                    <span class="font-semibold text-gray-700">/j/{{ $profile->slug }}</span>
                    <span class="text-amber-700">(غير عام بعد)</span>
                @endif
            </div>
        </div>
    </div>

    @if(isset($achievements) && $achievements->count())
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <h2 class="text-base font-bold text-gray-900 mb-3">إنجازات الرحلة</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($achievements as $ua)
                @continue(!$ua->achievement)
                <div class="flex items-start gap-3 rounded-xl border border-gray-100 bg-slate-50 px-3 py-3">
                    <span class="w-9 h-9 rounded-lg bg-sky-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $ua->achievement->icon ?: 'fa-medal' }} text-sm"></i>
                    </span>
                    <div>
                        <div class="text-sm font-bold text-gray-900">{{ $ua->achievement->name }}</div>
                        <div class="text-xs text-gray-500">{{ $ua->achievement->description }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($projects->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($projects as $project)
        @php
            $statusLabels = [
                'draft' => ['label' => 'مسودة', 'class' => 'bg-gray-100 text-gray-800'],
                'pending_review' => ['label' => 'قيد المراجعة', 'class' => 'bg-amber-100 text-amber-800'],
                'changes_requested' => ['label' => 'يحتاج تعديلات', 'class' => 'bg-orange-100 text-orange-800'],
                'resubmitted' => ['label' => 'أُعيد إرساله', 'class' => 'bg-indigo-100 text-indigo-800'],
                'approved' => ['label' => 'معتمد', 'class' => 'bg-sky-100 text-sky-800'],
                'rejected' => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-800'],
                'published' => ['label' => 'منشور', 'class' => 'bg-emerald-100 text-emerald-800'],
            ];
            $s = $statusLabels[$project->status] ?? ['label' => $project->status, 'class' => 'bg-gray-100 text-gray-800'];
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            @if($project->image_path)
            <div class="aspect-video bg-gray-100">
                <img src="{{ asset($project->image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
            </div>
            @else
            <div class="aspect-video bg-sky-50 flex items-center justify-center">
                <i class="fas fa-code text-3xl text-sky-300"></i>
            </div>
            @endif
            <div class="p-4">
                <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">{{ $project->title }}</h3>
                <div class="flex flex-wrap gap-1 mb-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
                    @if($project->program_type)
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-700">{{ $project->programTypeLabel() }}</span>
                    @endif
                </div>
                @if($project->programContextLabel())
                <p class="text-xs text-gray-500 mb-2">{{ $project->programContextLabel() }}</p>
                @endif
                @if($project->rejected_reason && in_array($project->status, ['rejected', 'changes_requested']))
                <p class="text-xs text-rose-600 mb-2 line-clamp-2">{{ $project->rejected_reason }}</p>
                @endif
                @if($project->isEditableByStudent())
                <a href="{{ route('student.portfolio.edit', $project) }}" class="text-xs font-semibold text-sky-700 hover:underline">تعديل / إعادة إرسال</a>
                @elseif($project->status === 'published')
                <div class="mt-2 space-y-2">
                    <a href="{{ route('public.portfolio.show', $project->id) }}" target="_blank" class="block text-xs font-semibold text-emerald-700 hover:underline">عرض في المعرض</a>
                    @include('components.journey-share-bar', [
                        'canonicalUrl' => route('public.portfolio.show', $project->id),
                        'shareTitle' => $project->title . ' — Mindlytics Verified',
                        'shareableType' => 'project',
                        'shareableId' => $project->id,
                        'cardImageUrl' => $shareCards->projectCardUrl($project, $project->is_featured ? 'featured' : 'project_verified'),
                        'cardType' => $project->is_featured ? 'featured' : 'project_verified',
                    ])
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @if($projects->hasPages())
    <div class="flex justify-center mt-6">{{ $projects->links() }}</div>
    @endif
    @else
    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 sm:p-12 text-center">
        <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
            <i class="fas fa-route text-2xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">ابدأ ببناء رحلتك</h3>
        <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">كل مشروع تنجزه من كورس مسجّل أو دبلوم يصبح دليلاً موثّقاً بعد مراجعة المدرب — جاهزاً لعرضه على الشركات.</p>
        <a href="{{ route('student.portfolio.create') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">
            <i class="fas fa-plus"></i>
            رفع أول مشروع
        </a>
    </div>
    @endif
</div>
@endsection
