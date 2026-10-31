<div class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-lg"
     x-data="projectHandler()"
     x-init="init()">
    <h3 class="text-xl font-black text-[#1C2C39] mb-4">مشروع عملي</h3>
    
    @if(isset($pattern->pattern_data['project_description']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">وصف المشروع</h4>
            <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-wrap">{{ $pattern->pattern_data['project_description'] }}</div>
        </div>
    @endif
    
    @if(isset($pattern->pattern_data['requirements']))
        <div class="mb-6">
            <h4 class="font-bold text-[#1C2C39] mb-2">المتطلبات</h4>
            <div class="bg-blue-50 border-r-4 border-blue-500 rounded-xl p-4">
                @if(is_array($pattern->pattern_data['requirements']))
                    <ul class="list-disc list-inside space-y-2">
                        @foreach($pattern->pattern_data['requirements'] as $requirement)
                            <li>{{ $requirement }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="whitespace-pre-wrap">{{ $pattern->pattern_data['requirements'] }}</div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="mb-6">
        <h4 class="font-bold text-[#1C2C39] mb-2">حل المشروع</h4>
        <textarea x-model="attemptData.project_code" 
                  rows="20"
                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                  placeholder="اكتب كود المشروع هنا..."></textarea>
    </div>
    
    <div class="mb-6">
        <h4 class="font-bold text-[#1C2C39] mb-2">ملاحظات (اختياري)</h4>
        <textarea x-model="attemptData.notes" 
                  rows="4"
                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                  placeholder="أي ملاحظات أو تعليقات..."></textarea>
    </div>
    
    <div class="flex gap-3">
        <button @click="saveProgress()" 
                class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold">
            <i class="fas fa-save ml-2"></i>
            حفظ التقدم
        </button>
        <button @click="submitProject()" 
                class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold">
            <i class="fas fa-check ml-2"></i>
            إرسال المشروع
        </button>
    </div>
</div>

<script>
function projectHandler() {
    return {
        attemptData: { project_code: '', notes: '' },
        parentComponent: null,
        
        init() {
            this.parentComponent = this.$el.closest('[x-data*="learningPattern"]').__x.$data;
            if (window.currentAttemptData) {
                this.attemptData = { ...this.attemptData, ...window.currentAttemptData };
            }
        },
        
        async saveProgress() {
            if (this.parentComponent) {
                this.parentComponent.attemptData = this.attemptData;
                await this.parentComponent.saveProgress();
            }
        },
        
        async submitProject() {
            if (!this.attemptData.project_code || !this.attemptData.project_code.trim()) {
                alert('يرجى كتابة كود المشروع أولاً');
                return;
            }
            this.$dispatch('pattern-complete', this.attemptData);
        }
    };
}
</script>
