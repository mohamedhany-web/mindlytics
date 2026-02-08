<div class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-lg" 
     x-data="codeChallengeHandler()"
     x-init="init()">
    <h3 class="text-xl font-black text-[#1C2C39] mb-4">تحدي برمجي</h3>
    
    @if(isset($pattern->pattern_data['problem_description']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">وصف التحدي</h4>
            <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-wrap">{{ $pattern->pattern_data['problem_description'] }}</div>
        </div>
    @endif
    
    @if(isset($pattern->pattern_data['input_example']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">مثال الإدخال</h4>
            <div class="bg-gray-50 rounded-xl p-4 font-mono text-sm">{{ $pattern->pattern_data['input_example'] }}</div>
        </div>
    @endif
    
    @if(isset($pattern->pattern_data['output_example']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">مثال الإخراج المتوقع</h4>
            <div class="bg-gray-50 rounded-xl p-4 font-mono text-sm">{{ $pattern->pattern_data['output_example'] }}</div>
        </div>
    @endif
    
    <div class="mb-6">
        <h4 class="font-bold text-[#1C2C39] mb-2">اكتب الحل</h4>
        <textarea x-model="attemptData.code" 
                  rows="15"
                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                  placeholder="اكتب كود الحل هنا..."></textarea>
    </div>
    
    <div x-show="output" class="mb-6">
        <h4 class="font-bold text-[#1C2C39] mb-2">النتيجة</h4>
        <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm min-h-[100px]">
            <div x-html="output"></div>
        </div>
    </div>
    
    <div class="flex gap-3">
        <button @click="runCode()" 
                class="px-6 py-3 bg-[#2CA9BD] hover:bg-[#1F3A56] text-white rounded-xl font-bold">
            <i class="fas fa-play ml-2"></i>
            تشغيل الكود
        </button>
        <button @click="submitSolution()" 
                class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold">
            <i class="fas fa-check ml-2"></i>
            إرسال الحل
        </button>
    </div>
    
    @if(isset($pattern->pattern_data['starter_code']))
        <div class="mt-4">
            <button @click="loadStarterCode()" 
                    class="text-sm text-[#2CA9BD] hover:underline">
                <i class="fas fa-code ml-1"></i>
                تحميل الكود المبدئي
            </button>
        </div>
    @endif
</div>

<script>
function codeChallengeHandler() {
    return {
        attemptData: window.currentAttemptData || { code: '' },
        output: '',
        parentComponent: null,
        
        init() {
            const el = this.$el.closest('[x-data*="learningPattern"]');
            if (el && el.__x) this.parentComponent = el.__x.$data;
            if (window.currentAttemptData && window.currentAttemptData.code) {
                this.attemptData.code = window.currentAttemptData.code;
            }
        },
        
        async runCode() {
            if (!this.attemptData.code || !this.attemptData.code.trim()) {
                alert('يرجى كتابة الكود أولاً');
                return;
            }
            
            this.output = 'جاري التشغيل...';
            
            // حفظ التقدم
            if (this.parentComponent) {
                this.parentComponent.attemptData = this.attemptData;
                await this.parentComponent.saveProgress();
            }
            
            // محاكاة تشغيل الكود
            setTimeout(() => {
                this.output = 'تم تشغيل الكود بنجاح!\n(هذه محاكاة - في الإنتاج سيتم تشغيل الكود فعلياً)';
            }, 1000);
        },
        
        async submitSolution() {
            if (!this.attemptData.code || !this.attemptData.code.trim()) {
                alert('يرجى كتابة الكود أولاً');
                return;
            }
            this.$dispatch('pattern-complete', this.attemptData);
        },
        
        loadStarterCode() {
            @if(isset($pattern->pattern_data['starter_code']))
                this.attemptData.code = @json($pattern->pattern_data['starter_code']);
            @endif
        }
    };
}
</script>
