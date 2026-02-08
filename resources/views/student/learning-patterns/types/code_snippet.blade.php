<div class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-lg"
     x-data="snippetHandler()"
     x-init="init()">
    <h3 class="text-xl font-black text-[#1C2C39] mb-4">مثال كود تفاعلي</h3>
    
    @if(isset($pattern->pattern_data['code_example']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">مثال الكود</h4>
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm overflow-x-auto">
                <pre>{{ $pattern->pattern_data['code_example'] }}</pre>
            </div>
        </div>
    @endif
    
    @if(isset($pattern->pattern_data['explanation']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">الشرح</h4>
            <div class="bg-blue-50 border-r-4 border-blue-500 rounded-xl p-4 whitespace-pre-wrap">
                {{ $pattern->pattern_data['explanation'] }}
            </div>
        </div>
    @endif
    
    <div class="mb-6">
        <h4 class="font-bold text-[#1C2C39] mb-2">جرب الكود</h4>
        <textarea x-model="attemptData.code" 
                  rows="10"
                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                  placeholder="يمكنك تعديل الكود وتجربته..."></textarea>
    </div>
    
    <div class="mb-6" x-show="output">
        <h4 class="font-bold text-[#1C2C39] mb-2">النتيجة</h4>
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
        <button @click="resetCode()" 
                class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold">
            <i class="fas fa-undo ml-2"></i>
            إعادة تعيين
        </button>
    </div>
</div>

<script>
function snippetHandler() {
    return {
        attemptData: { code: '' },
        output: '',
        originalCode: '',
        parentComponent: null,
        
        init() {
            this.parentComponent = this.$el.closest('[x-data*="learningPattern"]').__x.$data;
            @if(isset($pattern->pattern_data['code_example']))
                this.originalCode = @json($pattern->pattern_data['code_example']);
                if (!window.currentAttemptData || !window.currentAttemptData.code) {
                    this.attemptData.code = this.originalCode;
                } else {
                    this.attemptData.code = window.currentAttemptData.code;
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
            if (this.parentComponent) {
                this.parentComponent.attemptData = this.attemptData;
                await this.parentComponent.saveProgress();
            }
            
            // محاكاة تشغيل الكود
            setTimeout(() => {
                this.output = 'تم تشغيل الكود بنجاح!\n(هذه محاكاة - في الإنتاج سيتم تشغيل الكود فعلياً)';
            }, 1000);
        },
        
        resetCode() {
            this.attemptData.code = this.originalCode;
            this.output = '';
        }
    };
}
</script>
