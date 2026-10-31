<div class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-lg"
     x-data="playgroundHandler()"
     x-init="init()">
    <h3 class="text-xl font-black text-[#1C2C39] mb-4">محرر كود مباشر</h3>
    
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-bold text-[#1C2C39]">اكتب الكود</h4>
            <span class="text-sm text-gray-500">
                لغة: {{ $pattern->pattern_data['language'] ?? 'Python' }}
            </span>
        </div>
        <textarea x-model="attemptData.code" 
                  rows="15"
                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                  placeholder="اكتب الكود هنا..."></textarea>
    </div>
    
    <div class="mb-6" x-show="output">
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-bold text-[#1C2C39]">النتيجة</h4>
            <button @click="clearOutput()" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-trash ml-1"></i>
                مسح
            </button>
        </div>
        <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm min-h-[100px]">
            <div x-html="output || 'النتيجة ستظهر هنا...'"></div>
        </div>
    </div>
    
    <div class="flex gap-3">
        <button @click="runCode()" 
                class="px-6 py-3 bg-[#2CA9BD] hover:bg-[#1F3A56] text-white rounded-xl font-bold">
            <i class="fas fa-play ml-2"></i>
            تشغيل الكود
        </button>
        <button @click="saveProgress()" 
                class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold">
            <i class="fas fa-save ml-2"></i>
            حفظ
        </button>
    </div>
</div>

<script>
function playgroundHandler() {
    return {
        attemptData: { code: '' },
        output: '',
        parentComponent: null,
        
        init() {
            this.parentComponent = this.$el.closest('[x-data*="learningPattern"]').__x.$data;
            if (window.currentAttemptData && window.currentAttemptData.code) {
                this.attemptData.code = window.currentAttemptData.code;
            }
            @if(isset($pattern->pattern_data['starter_code']))
                if (!this.attemptData.code) {
                    this.attemptData.code = @json($pattern->pattern_data['starter_code']);
                }
            @endif
        },
        
        async runCode() {
            if (!this.attemptData.code || !this.attemptData.code.trim()) {
                alert('يرجى كتابة الكود أولاً');
                return;
            }
            
            this.output = 'جاري التشغيل...';
            
            // حفظ التقدم
            await this.saveProgress();
            
            // محاكاة تشغيل الكود
            setTimeout(() => {
                this.output = 'تم تشغيل الكود بنجاح!\n(هذه محاكاة - في الإنتاج سيتم تشغيل الكود فعلياً)';
            }, 1000);
        },
        
        async saveProgress() {
            if (this.parentComponent) {
                this.parentComponent.attemptData = this.attemptData;
                await this.parentComponent.saveProgress();
            }
        },
        
        clearOutput() {
            this.output = '';
        }
    };
}
</script>
