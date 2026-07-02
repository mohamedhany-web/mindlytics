@php
    $filterAction = $filterAction ?? route('instructor.scholarships.students.index');
    $showProgramFilter = $showProgramFilter ?? true;
    $programs = $programs ?? collect();
@endphp
<div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5">
    <form method="GET" action="{{ $filterAction }}" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3 sm:gap-4 items-end">
        <div class="{{ $showProgramFilter ? 'sm:col-span-2 xl:col-span-4' : 'sm:col-span-2 xl:col-span-5' }}">
            <label for="scholarship-search" class="block text-sm font-semibold text-slate-700 mb-1">بحث</label>
            <input type="text" name="search" id="scholarship-search" value="{{ request('search') }}"
                   placeholder="الاسم أو البريد أو الهاتف…"
                   class="w-full h-10 px-3 text-sm border border-slate-200 rounded-xl text-slate-800 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
        </div>

        @if($showProgramFilter)
            <div class="xl:col-span-3">
                <label for="scholarship-program" class="block text-sm font-semibold text-slate-700 mb-1">المنحة</label>
                <select name="program_id" id="scholarship-program"
                        class="w-full h-10 px-3 text-sm border border-slate-200 rounded-xl text-slate-800 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value="">كل المنح</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" @selected((string) request('program_id') === (string) $program->id)>{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="{{ $showProgramFilter ? 'xl:col-span-3' : 'xl:col-span-4' }}">
            <label for="scholarship-status" class="block text-sm font-semibold text-slate-700 mb-1">الحالة</label>
            <select name="status" id="scholarship-status"
                    class="w-full h-10 px-3 text-sm border border-slate-200 rounded-xl text-slate-800 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                <option value="">كل الحالات</option>
                @foreach(\App\Models\ScholarshipRegistration::statusLabels() as $key => $label)
                    <option value="{{ $key }}" @selected((string) request('status') === (string) $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2 xl:col-span-2 flex items-center gap-2">
            <button type="submit"
                    class="h-10 inline-flex items-center justify-center gap-1.5 px-4 text-sm font-semibold bg-sky-500 hover:bg-sky-600 text-white rounded-xl transition-colors whitespace-nowrap">
                <i class="fas fa-search text-xs"></i>
                <span>تصفية</span>
            </button>
            @php
                $hasActiveFilters = $showProgramFilter
                    ? request()->anyFilled(['search', 'status', 'program_id'])
                    : request()->anyFilled(['search', 'status']);
            @endphp
            @if($hasActiveFilters)
                <a href="{{ $filterAction }}"
                   class="h-10 w-10 inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors shrink-0"
                   title="مسح الفلاتر">
                    <i class="fas fa-times text-sm"></i>
                </a>
            @endif
        </div>
    </form>
</div>
