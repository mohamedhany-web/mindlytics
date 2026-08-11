@php
    $isEdit = isset($project);
    $techValue = old('technologies', $isEdit && is_array($project->technologies) ? implode(', ', $project->technologies) : '');
@endphp

{{-- عنوان + نوع --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    <div class="lg:col-span-5">
        <label class="block text-sm font-bold text-gray-900 mb-2">عنوان المشروع <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $isEdit ? $project->title : '') }}" required
               placeholder="مثال: لوحة تحكم لمتجر إلكتروني"
               class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20">
    </div>
    <div class="lg:col-span-7">
        <label class="block text-sm font-bold text-gray-900 mb-3">نوع المشروع</label>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-2">
            @php
                $types = [
                    'web_app' => ['تطبيق ويب', 'fa-globe', 'from-blue-500 to-blue-600'],
                    'mobile_app' => ['موبايل', 'fa-mobile-alt', 'from-green-500 to-green-600'],
                    'api' => ['API', 'fa-plug', 'from-purple-500 to-purple-600'],
                    'library' => ['مكتبة', 'fa-book', 'from-amber-500 to-amber-600'],
                    'script' => ['سكربت', 'fa-file-code', 'from-teal-500 to-teal-600'],
                    'design' => ['تصميم', 'fa-palette', 'from-pink-500 to-pink-600'],
                    'game' => ['لعبة', 'fa-gamepad', 'from-red-500 to-red-600'],
                    'desktop' => ['سطح مكتب', 'fa-desktop', 'from-indigo-500 to-indigo-600'],
                    'cli' => ['CLI', 'fa-terminal', 'from-gray-600 to-gray-700'],
                    'other' => ['أخرى', 'fa-folder', 'from-gray-400 to-gray-500'],
                ];
                $oldType = old('project_type', $isEdit ? $project->project_type : '');
            @endphp
            @foreach($types as $value => $label)
                <label class="relative block cursor-pointer select-none min-h-[3.5rem]">
                    <input type="radio" name="project_type" value="{{ $value }}"
                           {{ $oldType == $value ? 'checked' : '' }}
                           class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <span class="flex items-center gap-2 p-2.5 rounded-xl border-2 transition-all border-gray-200 hover:border-[#2CA9BD]/50 hover:bg-gray-50 h-full pointer-events-none peer-checked:border-[#2CA9BD] peer-checked:bg-[#2CA9BD]/10 peer-checked:ring-2 peer-checked:ring-[#2CA9BD]/30">
                        <span class="w-7 h-7 rounded-lg bg-gradient-to-br {{ $label[2] }} flex items-center justify-center text-white text-xs flex-shrink-0">
                            <i class="fas {{ $label[1] }}"></i>
                        </span>
                        <span class="text-xs font-semibold text-gray-800 truncate">{{ $label[0] }}</span>
                    </span>
                </label>
            @endforeach
        </div>
    </div>
</div>

<div class="mb-6">
    <label class="block text-sm font-bold text-gray-900 mb-2">الوصف</label>
    <textarea name="description" rows="3" placeholder="اشرح فكرة المشروع وما الذي يحلّه..."
              class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20">{{ old('description', $isEdit ? $project->description : '') }}</textarea>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-bold text-gray-900 mb-2">ماذا تعلمت؟</label>
        <textarea name="what_i_learned" rows="3" placeholder="المهارات والدروس المستفادة من بناء المشروع"
                  class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3">{{ old('what_i_learned', $isEdit ? $project->what_i_learned : '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-bold text-gray-900 mb-2">التحديات وكيف حللتها</label>
        <textarea name="challenges" rows="3" placeholder="ما الصعوبات التي واجهتها وكيف تجاوزتها؟"
                  class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3">{{ old('challenges', $isEdit ? $project->challenges : '') }}</textarea>
    </div>
</div>

<div class="mb-6">
    <label class="block text-sm font-bold text-gray-900 mb-2">التقنيات (افصل بفاصلة)</label>
    <input type="text" name="technologies" value="{{ $techValue }}"
           placeholder="React, Tailwind, Laravel, MySQL"
           class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3">
</div>

<div class="rounded-xl border border-sky-100 bg-sky-50/50 p-4 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-3">ربط المشروع بالبرنامج التعليمي <span class="text-red-500">*</span></h3>
    <p class="text-xs text-gray-500 mb-4">اختر مصدرًا واحدًا على الأقل: كورس مسجّل، أو دبلوم (مسار تعليمي / أونلاين / أوفلاين).</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">كورس مسجّل</label>
            <select name="advanced_course_id" class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 text-sm">
                <option value="">-- بدون --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ (string)old('advanced_course_id', $isEdit ? $project->advanced_course_id : '') === (string)$course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">دبلوم — مسار تعليمي</label>
            <select name="academic_year_id" class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 text-sm">
                <option value="">-- بدون --</option>
                @foreach($learningPaths as $path)
                    <option value="{{ $path->id }}" {{ (string)old('academic_year_id', $isEdit ? $project->academic_year_id : '') === (string)$path->id ? 'selected' : '' }}>{{ $path->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">دبلوم — أونلاين / أوفلاين</label>
            <select name="offline_course_id" class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 text-sm">
                <option value="">-- بدون --</option>
                @foreach($offlineCourses as $oc)
                    <option value="{{ $oc->id }}" {{ (string)old('offline_course_id', $isEdit ? $project->offline_course_id : '') === (string)$oc->id ? 'selected' : '' }}>
                        {{ $oc->title }} ({{ $oc->online_only ? 'أونلاين' : 'أوفلاين' }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-bold text-gray-900 mb-2"><i class="fab fa-github ml-1"></i> GitHub</label>
        <input type="url" name="github_url" value="{{ old('github_url', $isEdit ? $project->github_url : '') }}" placeholder="https://github.com/..."
               class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3">
    </div>
    <div>
        <label class="block text-sm font-bold text-gray-900 mb-2">رابط Live Demo</label>
        <input type="url" name="project_url" value="{{ old('project_url', $isEdit ? $project->project_url : '') }}" placeholder="https://..."
               class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3">
    </div>
</div>

<label class="inline-flex items-center gap-2 mb-6 text-sm text-gray-800">
    <input type="checkbox" name="is_capstone" value="1" class="rounded border-gray-300 text-[#2CA9BD]"
           {{ old('is_capstone', $isEdit ? $project->is_capstone : false) ? 'checked' : '' }}>
    هذا مشروع Capstone (مشروع التخرج / النهائي)
</label>

<div class="mb-6">
    <label class="block text-sm font-bold text-gray-900 mb-2">صور من المشروع <span class="text-gray-500 font-normal">(حد أقصى 5)</span></label>
    <input type="file" name="images[]" accept="image/*" multiple
           class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-[#2CA9BD]/10 file:text-[#2CA9BD]">
</div>
