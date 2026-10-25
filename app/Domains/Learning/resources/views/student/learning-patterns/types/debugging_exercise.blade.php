<div class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-lg"
     x-data="debuggingHandler()"
     x-init="init()">
    <h3 class="text-xl font-black text-[#1C2C39] mb-4">تمرين تصحيح الأخطاء</h3>
    
    @if(isset($pattern->pattern_data['problem_description']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">وصف المشكلة</h4>
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
                {{ $pattern->pattern_data['problem_description'] }}
            </div>
        </div>
    @endif
    
    @if(isset($pattern->pattern_data['buggy_code']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">الكود الذي يحتوي على أخطاء</h4>
            <div class="bg-gray-900 text-red-400 rounded-xl p-4 font-mono text-sm overflow-x-auto">
                <pre>{{ $pattern->pattern_data['buggy_code'] }}</pre>
            </div>
        </div>
    @endif
    
    <div class="mb-6">
        <h4 class="font-bold text-[#1C2C39] mb-2">الكود المصحح</h4>
        <textarea x-model="attemptData.corrected_code" 
                  rows="15"
                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                  placeholder="اكتب الكود المصحح هنا..."></textarea>
    </div>
    
    <div x-show="output" class="mb-6">
        <h4 class="font-bold text-[#1C2C39] mb-2">نتيجة الاختبار</h4>
        <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm min-h-[100px]">
            <div x-html="output"></div>
        </div>
    </div>
    
    <div class="flex gap-3">
        <button @click="testCode()" 
                class="px-6 py-3 bg-[#2CA9BD] hover:bg-[#1F3A56] text-white rounded-xl font-bold">
            <i class="fas fa-vial ml-2"></i>
            اختبار الكود
        </button>
        <button @click="submitSolution()" 
                class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold">
            <i class="fas fa-check ml-2"></i>
            إرسال الحل
        </button>
    </div>
</div>

<script>
function debuggingHandler() {
    return {
        attemptData: { corrected_code: '' },
        output: '',
        parentComponent: null,
        
        init() {
            const el = this.$el.closest('[x-data*="learningPattern"]');
            if (el && el.__x) this.parentComponent = el.__x.$data;
            if (window.currentAttemptData && window.currentAttemptData.corrected_code) {
                this.attemptData.corrected_code = window.currentAttemptData.corrected_code;
            }
        },
        
        async testCode() {
            if (!this.attemptData.corrected_code || !this.attemptData.corrected_code.trim()) {
                alert('يرجى كتابة الكود المصحح أولاً');
                return;
            }
            
            this.output = 'جاري اختبار الكود...';
            
            // حفظ التقدم
            if (this.parentComponent) {
                this.parentComponent.attemptData = this.attemptData;
                await this.parentComponent.saveProgress();
            }
            
            // محاكاة الاختبار
            setTimeout(() => {
                this.output = 'تم اختبار الكود بنجاح!\n(هذه محاكاة - في الإنتاج سيتم اختبار الكود فعلياً)';
            }, 1000);
        },
        
        async submitSolution() {
            if (!this.attemptData.corrected_code || !this.attemptData.corrected_code.trim()) {
                alert('يرجى كتابة الكود المصحح أولاً');
                return;
            }
            this.$dispatch('pattern-complete', this.attemptData);
        }
    };
}
</script>
