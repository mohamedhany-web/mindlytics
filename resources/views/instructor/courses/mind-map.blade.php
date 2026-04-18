@extends('layouts.app')

@section('title', __('instructor.mind_map_page_title') . ' - ' . $course->title)
@section('header', __('instructor.mind_map_page_title'))

@section('content')
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.courses.index') }}" class="hover:text-sky-600">{{ __('instructor.my_courses') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.courses.show', $course->id) }}" class="hover:text-sky-600">{{ $course->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ __('instructor.mind_map_page_title') }}</span>
        </nav>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ __('instructor.mind_map_page_title') }}</h1>
        <p class="text-slate-600 mt-2 text-sm leading-relaxed">{{ __('instructor.mind_map_intro') }}</p>
        <p class="text-slate-500 text-xs mt-2">{{ __('instructor.mind_map_roles_hint') }}</p>
        @if($course->mind_map_published && is_array($course->mind_map_steps) && count($course->mind_map_steps) >= 2)
            <a href="{{ route('public.course.mind-map', $course->id) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-sky-600 hover:text-sky-800">
                <i class="fas fa-external-link-alt"></i>
                {{ __('instructor.mind_map_open_public') }}
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    @if($errors->has('steps'))
        <div class="rounded-xl bg-red-50 text-red-800 border border-red-200 px-4 py-3">{{ $errors->first('steps') }}</div>
    @endif

    <form method="post" action="{{ route('instructor.courses.mind-map.update', $course) }}" class="grid grid-cols-1 xl:grid-cols-12 gap-6 xl:gap-8 items-start">
        @csrf
        @method('PUT')

        <div class="xl:col-span-8 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6"
                 x-data="{
                    steps: {{ \Illuminate\Support\Js::from($steps) }},
                    lblFirst: {{ \Illuminate\Support\Js::from(__('instructor.mind_map_label_first')) }},
                    lblLast: {{ \Illuminate\Support\Js::from(__('instructor.mind_map_label_last')) }},
                    lblMiddle: {{ \Illuminate\Support\Js::from(__('instructor.mind_map_label_middle')) }},
                    addStep() { this.steps.push({ title: '', description: '' }); },
                    removeStep(i) { if (this.steps.length > 1) this.steps.splice(i, 1); },
                    moveStep(i, dir) {
                        const j = i + dir;
                        if (j < 0 || j >= this.steps.length) return;
                        const a = this.steps[i], b = this.steps[j];
                        this.steps[i] = b; this.steps[j] = a;
                    },
                    stepBadge(index) {
                        if (index === 0) return this.lblFirst;
                        if (index === this.steps.length - 1) return this.lblLast;
                        return this.lblMiddle.replace(':n', String(index));
                    }
                 }">
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-diagram-project text-sky-500"></i>
                    {{ __('instructor.mind_map_steps_section') }}
                </h2>

                <template x-for="(step, index) in steps" :key="'mind-step-' + index">
                    <div class="mb-4 p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-500" x-text="stepBadge(index)"></span>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="moveStep(index, -1)" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100" title="{{ __('instructor.mind_map_move_up') }}"><i class="fas fa-chevron-up text-xs"></i></button>
                                <button type="button" @click="moveStep(index, 1)" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100" title="{{ __('instructor.mind_map_move_down') }}"><i class="fas fa-chevron-down text-xs"></i></button>
                                <button type="button" @click="removeStep(index)" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="{{ __('instructor.mind_map_remove_step') }}"><i class="fas fa-trash-alt text-xs"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('instructor.mind_map_step_title') }}</label>
                            <input type="text" :name="'steps[' + index + '][title]'" x-model="step.title" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" maxlength="200" placeholder="{{ __('instructor.mind_map_title_placeholder') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('instructor.mind_map_step_desc') }}</label>
                            <textarea :name="'steps[' + index + '][description]'" x-model="step.description" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="{{ __('instructor.mind_map_desc_placeholder') }}"></textarea>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addStep()" class="mb-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-800 text-sm font-semibold hover:bg-slate-200">
                    <i class="fas fa-plus"></i>
                    {{ __('instructor.mind_map_add_step') }}
                </button>
            </div>
        </div>

        <div class="xl:col-span-4 space-y-6 xl:sticky xl:top-24">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 border-t-4 border-t-amber-400">
                <h2 class="text-base font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    {{ __('instructor.mind_map_timetable_label') }}
                </h2>
                <p class="text-xs text-slate-600 leading-relaxed mb-3">{{ __('instructor.mind_map_timetable_help') }}</p>
                <textarea name="mind_map_timetable" rows="12" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 font-mono leading-relaxed" placeholder="{{ __('instructor.mind_map_timetable_placeholder') }}">{{ old('mind_map_timetable', $course->mind_map_timetable) }}</textarea>
                @error('mind_map_timetable')
                    <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-sky-600"></i>
                    {{ __('instructor.mind_map_lectures_ref_title') }}
                </h3>
                @if($lecturesForTimetable->isEmpty())
                    <p class="text-xs text-slate-500 leading-relaxed">{{ __('instructor.mind_map_lectures_empty') }}</p>
                @else
                    <ul class="space-y-2 max-h-72 overflow-y-auto pe-1">
                        @foreach($lecturesForTimetable as $lec)
                            <li class="text-xs rounded-lg bg-white border border-slate-200 px-3 py-2">
                                <div class="font-bold text-slate-800">{{ $lec->title }}</div>
                                <div class="text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <span><i class="far fa-clock ml-1 opacity-70"></i>{{ $lec->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</span>
                                    @if($lec->duration_minutes)
                                        <span>· {{ $lec->duration_minutes }} {{ __('instructor.mind_map_minutes_abbr') }}</span>
                                    @endif
                                    @if($lec->status)
                                        <span class="inline-flex px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $lec->status }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-sky-50/50 p-4">
                <label class="inline-flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="mind_map_published" value="1" class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500" {{ old('mind_map_published', $course->mind_map_published) ? 'checked' : '' }}>
                    <span>
                        <span class="block font-bold text-slate-800 text-sm">{{ __('instructor.mind_map_publish_label') }}</span>
                        <span class="block text-xs text-slate-600 mt-1">{{ __('instructor.mind_map_publish_help') }}</span>
                    </span>
                </label>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">{{ __('instructor.save_changes') }}</button>
                <a href="{{ route('instructor.courses.show', $course->id) }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">{{ __('instructor.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
