{{-- عرض افتراضي للأنماط التي لا تملك واجهة مخصصة (مثل: pair_programming, code_review, algorithm_practice, gamification) --}}
@php
    $typeInfo = $pattern->getTypeInfo();
@endphp
<div class="rounded-xl bg-white border border-slate-200 p-6 shadow-sm" x-data="genericPatternHandler()" x-init="init()">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
            <i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }} text-xl"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-800">{{ $typeInfo['name'] ?? 'نمط تعليمي' }}</h3>
            <p class="text-sm text-slate-500">اتبع التعليمات ثم اضغط إكمال المحاولة</p>
        </div>
    </div>
    @if($pattern->description)
        <div class="mb-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
            <p class="text-slate-700">{{ $pattern->description }}</p>
        </div>
    @endif
    @if($pattern->instructions)
        <div class="mb-4 p-4 bg-sky-50 rounded-xl border border-sky-100">
            <h4 class="text-sm font-bold text-sky-800 mb-2"><i class="fas fa-info-circle ml-1"></i> التعليمات</h4>
            <div class="text-sky-900 whitespace-pre-wrap">{{ $pattern->instructions }}</div>
        </div>
    @endif
    <div class="pt-4 border-t border-slate-200">
        <p class="text-sm text-slate-600 mb-4">عند الانتهاء من تنفيذ المطلوب، اضغط الزر أدناه لتسجيل إكمال المحاولة.</p>
        <button type="button" @click="completeAttempt()" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
            <i class="fas fa-check ml-2"></i>
            إكمال المحاولة
        </button>
    </div>
</div>
<script>
function genericPatternHandler() {
    return {
        parentComponent: null,
        init() {
            const el = this.$el.closest('[x-data*="learningPattern"]');
            if (el && el.__x) this.parentComponent = el.__x.$data;
        },
        async completeAttempt() {
            this.$dispatch('pattern-complete', { completed: true });
        }
    };
}
</script>
