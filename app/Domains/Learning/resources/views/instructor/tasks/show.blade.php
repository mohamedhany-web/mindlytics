@extends('layouts.app')

@section('title', $task->title . ' - ' . __('instructor.tasks_from_management'))
@section('header', __('instructor.task_details'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="{{ route('instructor.tasks.index') }}" class="hover:text-sky-600">{{ __('instructor.tasks_from_management') }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ $task->title }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $task->title }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    @if($task->assigner)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600">{{ __('instructor.from_management') }}</span>
                    @endif
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                        @if($task->priority == 'urgent') bg-rose-100 text-rose-700
                        @elseif($task->priority == 'high') bg-amber-100 text-amber-700
                        @elseif($task->priority == 'medium') bg-sky-100 text-sky-700
                        @else bg-slate-100 text-slate-600
                        @endif">
                        @if($task->priority == 'urgent') {{ __('instructor.urgent') }}
                        @elseif($task->priority == 'high') {{ __('instructor.high') }}
                        @elseif($task->priority == 'medium') {{ __('instructor.medium') }}
                        @else {{ __('instructor.low') }}
                        @endif
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                        @if($task->status == 'completed') bg-emerald-100 text-emerald-700
                        @elseif($task->status == 'in_progress') bg-blue-100 text-blue-700
                        @else bg-amber-100 text-amber-700
                        @endif">
                        @if($task->status == 'completed') {{ __('instructor.completed') }}
                        @elseif($task->status == 'in_progress') {{ __('instructor.in_progress') }}
                        @else {{ __('instructor.pending') }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if(!$task->assigned_by)
                    <a href="{{ route('instructor.tasks.edit', $task) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-colors">
                        <i class="fas fa-edit"></i>
                        {{ __('common.edit') }}
                    </a>
                @endif
                <a href="{{ route('instructor.tasks.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold text-sm transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    {{ __('instructor.back') }}
                </a>
            </div>
        </div>
    </div>

    @if($task->description)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="font-bold text-slate-800 mb-2">{{ __('instructor.description') }}</h3>
            <p class="text-slate-600 whitespace-pre-wrap">{{ $task->description }}</p>
        </div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <h3 class="font-bold text-slate-800 mb-4">{{ __('instructor.additional_details') }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($task->relatedCourse)
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600">
                        <i class="fas fa-book"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">{{ __('instructor.course') }}</p>
                        <p class="text-slate-800 font-medium">{{ $task->relatedCourse->title }}</p>
                    </div>
                </div>
            @endif
            @if($task->relatedLecture)
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">{{ __('instructor.lecture') }}</p>
                        <p class="text-slate-800 font-medium">{{ $task->relatedLecture->title }}</p>
                    </div>
                </div>
            @endif
            @if($task->due_date)
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">{{ __('instructor.due_date') }}</p>
                        <p class="text-slate-800 font-medium">{{ $task->due_date->format('Y-m-d H:i') }}</p>
                        @if($task->due_date->isPast() && $task->status != 'completed')
                            <p class="text-rose-600 text-sm font-semibold mt-0.5">{{ __('instructor.late') }}</p>
                        @endif
                    </div>
                </div>
            @endif
            @if($task->completed_at)
                <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fas fa-check-double"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-emerald-700">{{ __('instructor.completed') }}</p>
                        <p class="text-slate-800 font-medium">{{ $task->completed_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            @endif
            @if($task->assigned_by && isset($task->progress))
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">{{ __('instructor.progress_label') }}</p>
                        <p class="text-slate-800 font-medium">{{ (int)($task->progress ?? 0) }}%</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($task->assigned_by)
        {{-- تحديث التقدم (مهام من الإدارة) --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="font-bold text-slate-800 mb-4">{{ __('instructor.update_progress') }}</h3>
            <form action="{{ route('instructor.tasks.update-progress', $task) }}" method="POST" class="flex flex-wrap items-end gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('common.status') }}</label>
                    <select name="status" class="px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>{{ __('instructor.pending') }}</option>
                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress') }}</option>
                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>{{ __('instructor.completed') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.progress_percent') }}</label>
                    <input type="number" name="progress" min="0" max="100" value="{{ (int)($task->progress ?? 0) }}"
                           class="w-24 px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save ml-1"></i>
                    {{ __('instructor.save_progress') }}
                </button>
            </form>
        </div>

        {{-- التسليمات --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="font-bold text-slate-800 mb-4">{{ __('instructor.my_submissions') }}</h3>
            @if($task->deliverables->count() > 0)
                <ul class="space-y-3 mb-6">
                    @foreach($task->deliverables as $d)
                        <li class="flex flex-wrap items-start justify-between gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $d->title }}</p>
                                @if($d->description)
                                    <p class="text-sm text-slate-600 mt-1">{{ $d->description }}</p>
                                @endif
                                <p class="text-xs text-slate-500 mt-2">
                                    {{ $d->submitted_at?->format('Y-m-d H:i') }}
                                    @if($d->delivery_type === 'link' && $d->link_url)
                                        · <a href="{{ $d->link_url }}" target="_blank" class="text-sky-600 hover:underline">{{ __('instructor.open_link') }}</a>
                                    @endif
                                    @if($d->file_path)
                                        · <a href="{{ Storage::url($d->file_path) }}" target="_blank" class="text-sky-600 hover:underline">{{ __('instructor.download_file') }}</a>
                                    @endif
                                </p>
                                @if($d->feedback)
                                    <p class="text-sm mt-2 p-2 bg-amber-50 rounded-lg text-amber-800 border border-amber-100"><strong>{{ __('instructor.admin_notes_label') }}:</strong> {{ $d->feedback }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                @if($d->status === 'approved') bg-emerald-100 text-emerald-700
                                @elseif($d->status === 'rejected' || $d->status === 'needs_revision') bg-rose-100 text-rose-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                @if($d->status === 'approved') {{ __('instructor.approved') }}
                                @elseif($d->status === 'rejected') {{ __('instructor.rejected') }}
                                @elseif($d->status === 'needs_revision') {{ __('instructor.needs_revision') }}
                                @else {{ __('instructor.submitted_status') }}
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
            <form action="{{ route('instructor.tasks.submit-deliverable', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.submission_title_label') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required maxlength="255" value="{{ old('title') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                               placeholder="{{ __('instructor.submission_title_placeholder') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.submission_type_label') }}</label>
                        <select name="delivery_type" id="delivery_type" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                            <option value="file">{{ __('instructor.file_type') }}</option>
                            <option value="image">{{ __('instructor.image_type') }}</option>
                            <option value="link">{{ __('instructor.link_type') }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.description_optional') }}</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" placeholder="{{ __('instructor.submission_description_placeholder') }}">{{ old('description') }}</textarea>
                </div>
                <div id="file_input" class="flex items-center gap-2">
                    <label class="block text-sm font-semibold text-slate-700">{{ __('instructor.file_label') }}</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" class="border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    <span class="text-xs text-slate-500">{{ __('instructor.max_10mb') }}</span>
                </div>
                <div id="link_input" class="hidden">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.submission_link_label') }}</label>
                    <input type="url" name="link_url" value="{{ old('link_url') }}" placeholder="https://..."
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-paper-plane"></i>
                    {{ __('instructor.submit_work') }}
                </button>
            </form>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('delivery_type').addEventListener('change', function() {
    var type = this.value;
    document.getElementById('file_input').classList.toggle('hidden', type === 'link');
    document.getElementById('link_input').classList.toggle('hidden', type !== 'link');
});
</script>
@endpush
