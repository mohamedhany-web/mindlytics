@php
    $isEdit = isset($project);
    $techValue = old('technologies', $isEdit && is_array($project->technologies) ? implode(', ', $project->technologies) : '');
    $types = [
        'web_app', 'mobile_app', 'api', 'library', 'script',
        'design', 'game', 'desktop', 'cli', 'other',
    ];
    $oldType = old('project_type', $isEdit ? $project->project_type : '');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
    <div class="lg:col-span-5">
        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">
            {{ __('student.pf_field_title') }} <span class="text-[#7a3b2e]">*</span>
        </label>
        <input type="text" name="title" value="{{ old('title', $isEdit ? $project->title : '') }}" required
               placeholder="{{ __('student.pf_field_title_placeholder') }}"
               class="sp-pf-input">
    </div>
    <div class="lg:col-span-7">
        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-2 uppercase tracking-wide">{{ __('student.pf_field_type') }}</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
            @foreach($types as $value)
                <label class="relative block cursor-pointer select-none">
                    <input type="radio" name="project_type" value="{{ $value }}"
                           {{ $oldType == $value ? 'checked' : '' }}
                           class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <span class="flex items-center justify-center px-3 py-2.5 rounded-[16px] border border-[#f0f0ec] text-xs font-extrabold text-center transition-colors bg-white peer-checked:border-[var(--sp-accent)] peer-checked:bg-[rgba(174,217,234,.2)]">
                        {{ __('student.pf_type_' . $value) }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>
</div>

<div class="mb-5">
    <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_field_description') }}</label>
    <textarea name="description" rows="3" placeholder="{{ __('student.pf_field_description_placeholder') }}"
              class="sp-pf-input sp-pf-textarea">{{ old('description', $isEdit ? $project->description : '') }}</textarea>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
    <div>
        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_field_learned') }}</label>
        <textarea name="what_i_learned" rows="3" placeholder="{{ __('student.pf_field_learned_placeholder') }}"
                  class="sp-pf-input sp-pf-textarea">{{ old('what_i_learned', $isEdit ? $project->what_i_learned : '') }}</textarea>
    </div>
    <div>
        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_field_challenges') }}</label>
        <textarea name="challenges" rows="3" placeholder="{{ __('student.pf_field_challenges_placeholder') }}"
                  class="sp-pf-input sp-pf-textarea">{{ old('challenges', $isEdit ? $project->challenges : '') }}</textarea>
    </div>
</div>

<div class="mb-5">
    <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_field_technologies') }}</label>
    <input type="text" name="technologies" value="{{ $techValue }}"
           placeholder="{{ __('student.pf_field_technologies_placeholder') }}"
           class="sp-pf-input" dir="ltr">
</div>

<div class="sp-card p-4 sm:p-5 mb-5 border border-[#f0f0ec] !shadow-none" style="background:#fafaf8">
    <h3 class="font-extrabold text-sm m-0 mb-1">
        {{ __('student.pf_program_link_title') }} <span class="text-[#7a3b2e]">*</span>
    </h3>
    <p class="text-xs text-[var(--sp-muted)] m-0 mb-4 font-bold">{{ __('student.pf_program_link_hint') }}</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.pf_program_course') }}</label>
            <select name="advanced_course_id" class="sp-pf-input">
                <option value="">{{ __('student.pf_program_none') }}</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ (string)old('advanced_course_id', $isEdit ? $project->advanced_course_id : '') === (string)$course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.pf_program_path') }}</label>
            <select name="academic_year_id" class="sp-pf-input">
                <option value="">{{ __('student.pf_program_none') }}</option>
                @foreach($learningPaths as $path)
                    <option value="{{ $path->id }}" {{ (string)old('academic_year_id', $isEdit ? $project->academic_year_id : '') === (string)$path->id ? 'selected' : '' }}>{{ $path->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.pf_program_offline') }}</label>
            <select name="offline_course_id" class="sp-pf-input">
                <option value="">{{ __('student.pf_program_none') }}</option>
                @foreach($offlineCourses as $oc)
                    <option value="{{ $oc->id }}" {{ (string)old('offline_course_id', $isEdit ? $project->offline_course_id : '') === (string)$oc->id ? 'selected' : '' }}>
                        {{ $oc->title }} ({{ $oc->online_only ? __('student.pf_program_online') : __('student.pf_program_offline') }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
    <div>
        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_github') }}</label>
        <input type="url" name="github_url" value="{{ old('github_url', $isEdit ? $project->github_url : '') }}" placeholder="https://github.com/..."
               class="sp-pf-input" dir="ltr">
    </div>
    <div>
        <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_field_demo') }}</label>
        <input type="url" name="project_url" value="{{ old('project_url', $isEdit ? $project->project_url : '') }}" placeholder="https://..."
               class="sp-pf-input" dir="ltr">
    </div>
</div>

<label class="inline-flex items-center gap-2 mb-5 text-sm font-bold cursor-pointer">
    <input type="checkbox" name="is_capstone" value="1" class="rounded border-black/10 text-[var(--sp-accent)]"
           {{ old('is_capstone', $isEdit ? $project->is_capstone : false) ? 'checked' : '' }}>
    {{ __('student.pf_field_capstone') }}
</label>

<div class="mb-2">
    <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">
        {{ __('student.pf_field_images') }}
        <span class="normal-case font-bold text-[var(--sp-muted)]">({{ __('student.pf_field_images_hint') }})</span>
    </label>
    <input type="file" name="images[]" accept="image/*" multiple
           class="w-full text-sm font-bold text-[var(--sp-muted)] file:me-4 file:py-2.5 file:px-4 file:rounded-[20px] file:border-0 file:font-extrabold file:bg-[var(--sp-accent)] file:text-[var(--sp-accent-text)]">
</div>
