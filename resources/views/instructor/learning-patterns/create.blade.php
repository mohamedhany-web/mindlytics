@extends('layouts.app')

@section('title', 'إنشاء نمط تعليمي جديد')
@section('header', 'إنشاء نمط تعليمي جديد')

@push('styles')
<style>
    .pattern-type-card { border: 2px solid rgb(226 232 240); transition: all 0.2s; cursor: pointer; }
    .pattern-type-card:hover { border-color: rgb(14 165 233); box-shadow: 0 2px 8px rgba(14, 165, 233, 0.12); }
    .pattern-type-card.selected { border-color: rgb(14 165 233); background: rgb(224 242 254 / 0.5); }
</style>
@endpush

@section('content')
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 mb-1">إنشاء نمط تعليمي جديد</h1>
                <p class="text-sm text-slate-500">{{ $course->title }}</p>
            </div>
            <a href="{{ route('instructor.learning-patterns.index', $course) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right ml-2"></i> العودة
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.learning-patterns.store', $course) }}" method="POST" id="patternForm">
        @csrf
        
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm mb-6">
            <h2 class="text-base font-bold text-slate-800 mb-3">اختر نوع النمط التعليمي</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                @foreach($types as $typeKey => $typeInfo)
                    <div class="pattern-type-card rounded-lg p-2.5" onclick="selectType('{{ $typeKey }}')" data-type="{{ $typeKey }}">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                                <i class="{{ $typeInfo['icon'] }} text-sm"></i>
                            </div>
                            <h3 class="font-semibold text-slate-800 text-sm leading-tight">{{ $typeInfo['name'] }}</h3>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-tight">{{ $typeInfo['description'] }}</p>
                    </div>
                @endforeach
            </div>
            <input type="hidden" name="type" id="selectedType" required>
            @error('type')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">المعلومات الأساسية</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">التعليمات</label>
                    <textarea name="instructions" rows="4" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">{{ old('instructions') }}</textarea>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">الإعدادات</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">النقاط</label>
                    <input type="number" name="points" value="{{ old('points', 0) }}" min="0" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">مستوى الصعوبة (1-5)</label>
                    <select name="difficulty_level" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('difficulty_level', 1) == $i ? 'selected' : '' }}>مستوى {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحد الزمني (دقائق)</label>
                    <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes') }}" min="1" placeholder="اختياري" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحد الأقصى للمحاولات</label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts') }}" min="1" placeholder="غير محدود" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                </div>
            </div>
            <div class="mt-4 space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                    <span class="text-sm font-semibold text-slate-700">إلزامي (يجب إكماله للمتابعة)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="allow_multiple_attempts" value="1" {{ old('allow_multiple_attempts', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                    <span class="text-sm font-semibold text-slate-700">السماح بمحاولات متعددة</span>
                </label>
            </div>
        </div>

        @if($sections->count() > 0)
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm mb-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">إضافة للمنهج</h2>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">اختر القسم</label>
                    <select name="course_section_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-200">
                        <option value="">لا تضيف للمنهج الآن</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ (request('section_id') == $section->id || old('course_section_id') == $section->id) ? 'selected' : '' }}>{{ $section->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <input type="hidden" name="course_section_id" value="">
        @endif

        <div id="patternDataSection" class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm mb-6 hidden">
            <h2 class="text-lg font-bold text-slate-800 mb-4">البيانات التفاعلية</h2>
            <div id="patternDataContent"></div>
        </div>

        @include('instructor.learning-patterns.partials.interactive-quiz-modals', ['questionBanks' => $questionBanks ?? collect()])

        <div class="flex gap-3">
            <button type="submit" class="flex-1 px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-save ml-2"></i> حفظ النمط
            </button>
            <a href="{{ route('instructor.learning-patterns.index', $course) }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectType(type) {
    document.querySelectorAll('.pattern-type-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    document.querySelector(`[data-type="${type}"]`).classList.add('selected');
    document.getElementById('selectedType').value = type;
    
    const dataSection = document.getElementById('patternDataSection');
    if (type) {
        dataSection.classList.remove('hidden');
        loadPatternDataForm(type);
    } else {
        dataSection.classList.add('hidden');
    }
}

function loadPatternDataForm(type) {
    const content = document.getElementById('patternDataContent');
    content.innerHTML = '<p class="text-slate-500 text-center py-4">قسم البيانات التفاعلية - سيتم تطويره لاحقاً حسب النوع المحدد</p>';
}

document.getElementById('patternForm').addEventListener('submit', function(e) {
    if (!document.getElementById('selectedType').value) {
        e.preventDefault();
        alert('يرجى اختيار نوع النمط التعليمي');
        return false;
    }
});
</script>
@endpush
