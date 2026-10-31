@extends('layouts.app')

@section('title', __('instructor.review_title') . ': ' . $project->title)
@section('header', __('instructor.review_project'))

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl bg-green-50 border-2 border-green-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-red-50 border-2 border-red-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
            <span class="font-bold text-red-800">{{ session('error') }}</span>
        </div>
    @endif

    <a href="{{ route('instructor.portfolio.index') }}" class="inline-flex items-center gap-2 text-[#2CA9BD] hover:underline font-bold">
        <i class="fas fa-arrow-right"></i>
        {{ __('instructor.back_to_projects') }}
    </a>

    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-lg">
        @if($project->image_path)
            <div class="aspect-video bg-gray-100">
                <img src="{{ asset($project->image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
            </div>
        @endif
        <div class="p-8">
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">{{ $project->statusLabel() }}</span>
                @if($project->program_type)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-800">{{ $project->programTypeLabel() }}</span>
                @endif
                @if($project->is_capstone)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800">Capstone</span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-gray-900 mb-4">{{ $project->title }}</h1>
            @if($project->description)
                <div class="prose text-gray-600 mb-4">{!! nl2br(e($project->description)) !!}</div>
            @endif
            @if($project->what_i_learned)
                <div class="mb-3"><strong class="text-sm">ماذا تعلّم:</strong><p class="text-sm text-gray-600 whitespace-pre-line">{{ $project->what_i_learned }}</p></div>
            @endif
            @if($project->challenges)
                <div class="mb-3"><strong class="text-sm">التحديات:</strong><p class="text-sm text-gray-600 whitespace-pre-line">{{ $project->challenges }}</p></div>
            @endif
            @if(!empty($project->technologies))
                <div class="flex flex-wrap gap-1 mb-4">
                    @foreach($project->technologies as $tech)
                        <span class="text-xs px-2 py-0.5 rounded bg-slate-50 border border-gray-100">{{ $tech }}</span>
                    @endforeach
                </div>
            @endif
            <div class="flex flex-wrap gap-3 mb-4 text-sm">
                @if($project->github_url)<a href="{{ $project->github_url }}" target="_blank" class="text-[#2CA9BD] font-bold">GitHub</a>@endif
                @if($project->project_url)<a href="{{ $project->project_url }}" target="_blank" class="text-[#2CA9BD] font-bold">Live Demo</a>@endif
            </div>
            <p class="text-sm text-gray-500 mb-6">
                <strong>{{ __('instructor.student') }}:</strong> {{ $project->user->name ?? '—' }} |
                <strong>السياق:</strong> {{ $project->programContextLabel() ?? '—' }}
                @if($project->revision_count) | مراجعات: {{ $project->revision_count }} @endif
            </p>

            @if($project->isReviewable())
                <div class="space-y-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3" id="rubric-scores">
                        @foreach([
                            'rubric_code_quality' => 'جودة الكود',
                            'rubric_ui_ux' => 'UI/UX',
                            'rubric_functionality' => 'الوظائف',
                            'rubric_problem_solving' => 'حل المشكلات',
                            'rubric_documentation' => 'التوثيق',
                        ] as $field => $label)
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">{{ $label }} /10</label>
                                <input type="number" min="1" max="10" data-rubric="{{ $field }}" class="rubric-input w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm">
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <form action="{{ route('instructor.portfolio.approve', $project) }}" method="POST" class="review-action-form rounded-xl border border-emerald-200 bg-emerald-50/40 p-4">
                            @csrf
                            <div class="rubric-hidden-slot"></div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ملاحظات الاعتماد</label>
                            <textarea name="instructor_notes" rows="3" class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 text-sm mb-3"></textarea>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-green-600 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-green-700">{{ __('instructor.approve') }}</button>
                        </form>

                        <form action="{{ route('instructor.portfolio.request-changes', $project) }}" method="POST" class="review-action-form rounded-xl border border-amber-200 bg-amber-50/40 p-4">
                            @csrf
                            <div class="rubric-hidden-slot"></div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">التعديلات المطلوبة *</label>
                            <textarea name="instructor_notes" rows="3" required class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 text-sm mb-3"></textarea>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-amber-500 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-amber-600">طلب تعديلات</button>
                        </form>

                        <form action="{{ route('instructor.portfolio.reject', $project) }}" method="POST" class="rounded-xl border border-red-200 bg-red-50/40 p-4">
                            @csrf
                            <label class="block text-sm font-bold text-gray-700 mb-1">سبب الرفض *</label>
                            <textarea name="rejected_reason" rows="3" required class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 text-sm mb-3"></textarea>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-red-600 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-red-700">{{ __('instructor.reject') }}</button>
                        </form>
                    </div>
                </div>
            @endif

            @if($project->status === 'approved')
                <form action="{{ route('instructor.portfolio.publish', $project) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-[#2CA9BD] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[#1F3A56]">{{ __('instructor.publish_to_portfolio_btn') }}</button>
                </form>
            @endif

            @if($project->status === 'published')
                <form action="{{ route('instructor.portfolio.toggle-featured', $project) }}" method="POST" class="mt-4 inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 {{ $project->is_featured ? 'bg-amber-500 hover:bg-amber-600' : 'bg-slate-700 hover:bg-slate-800' }} text-white px-6 py-2.5 rounded-xl font-bold">
                        <i class="fas fa-star"></i>
                        {{ $project->is_featured ? 'إلغاء Featured' : 'تمييز كـ Featured by Mindlytics' }}
                    </button>
                </form>
            @endif

            @if($project->reviews->count())
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-900 mb-3">سجل المراجعات</h3>
                    <div class="space-y-2">
                        @foreach($project->reviews as $review)
                            <div class="text-sm rounded-lg bg-slate-50 border border-gray-100 px-3 py-2">
                                <span class="font-semibold">{{ $review->decision }}</span>
                                · {{ $review->reviewer->name ?? '—' }}
                                · {{ $review->created_at?->diffForHumans() }}
                                @if($review->score_average)
                                    · متوسط {{ $review->score_average }}/10
                                @endif
                                @if($review->instructor_notes)
                                    <p class="text-gray-600 mt-1">{{ $review->instructor_notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.review-action-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var slot = form.querySelector('.rubric-hidden-slot');
            if (!slot) return;
            slot.innerHTML = '';
            document.querySelectorAll('.rubric-input').forEach(function (input) {
                if (!input.value) return;
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = input.getAttribute('data-rubric');
                hidden.value = input.value;
                slot.appendChild(hidden);
            });
        });
    });
});
</script>
@endpush
@endsection
