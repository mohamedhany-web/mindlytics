<div class="rounded-xl bg-white border border-slate-200 p-6 shadow-sm" 
     x-data="flashcardsHandler()"
     x-init="init()">
    <h3 class="text-lg font-bold text-slate-800 mb-4">بطاقات تعليمية</h3>
    
    @php
        $flashcards = $pattern->pattern_data['flashcards'] ?? [];
    @endphp
    
    @if(count($flashcards) > 0)
        <div class="mb-6">
            <div class="text-center mb-4">
                <span class="text-sm text-slate-500">
                    بطاقة <span x-text="currentIndex + 1"></span> من {{ count($flashcards) }}
                </span>
            </div>
            
            <div class="relative h-64 mb-4 perspective-1000">
                <div class="relative w-full h-full preserve-3d transition-transform duration-500"
                     :class="{ 'rotate-y-180': isFlipped }"
                     @click="isFlipped = !isFlipped">
                    <!-- الوجه الأمامي -->
                    <div class="absolute inset-0 backface-hidden bg-sky-500 rounded-xl p-6 flex items-center justify-center text-white text-center">
                        <div>
                            <p class="text-sm mb-2 opacity-90">السؤال</p>
                            <p class="text-xl font-bold" x-text="currentCard.front"></p>
                        </div>
                    </div>
                    
                    <!-- الوجه الخلفي -->
                    <div class="absolute inset-0 backface-hidden bg-slate-700 rounded-xl p-6 flex items-center justify-center text-white text-center rotate-y-180">
                        <div>
                            <p class="text-sm mb-2 opacity-75">الإجابة</p>
                            <p class="text-xl font-bold" x-text="currentCard.back"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-center gap-4">
                <button @click="previousCard()" 
                        :disabled="currentIndex === 0"
                        class="px-4 py-2 bg-slate-200 hover:bg-slate-300 disabled:opacity-50 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button @click="isFlipped = !isFlipped" 
                        class="px-6 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-sync-alt ml-2"></i>
                    قلب البطاقة
                </button>
                <button @click="nextCard()" 
                        :disabled="currentIndex >= {{ count($flashcards) - 1 }}"
                        class="px-4 py-2 bg-slate-200 hover:bg-slate-300 disabled:opacity-50 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
            
            <div class="mt-6 text-center">
                <button @click="completeFlashcards()" 
                        class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-check ml-2"></i>
                    إكمال البطاقات
                </button>
            </div>
        </div>
    @else
        <div class="text-center py-8 text-slate-500">
            <p>لا توجد بطاقات متاحة</p>
        </div>
    @endif
</div>

<script>
function flashcardsHandler() {
    return {
        currentIndex: 0,
        isFlipped: false,
        flashcards: @json($flashcards),
        parentComponent: null,
        
        init() {
            this.parentComponent = this.$el.closest('[x-data*="learningPattern"]').__x.$data;
        },
        
        get currentCard() {
            return this.flashcards[this.currentIndex] || { front: '', back: '' };
        },
        
        previousCard() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.isFlipped = false;
            }
        },
        
        nextCard() {
            if (this.currentIndex < this.flashcards.length - 1) {
                this.currentIndex++;
                this.isFlipped = false;
            }
        },
        
        async completeFlashcards() {
            this.$dispatch('pattern-complete', {
                completed: true,
                cards_viewed: this.currentIndex + 1,
                total_cards: this.flashcards.length
            });
        }
    };
}
</script>

<style>
.perspective-1000 {
    perspective: 1000px;
}
.preserve-3d {
    transform-style: preserve-3d;
}
.backface-hidden {
    backface-visibility: hidden;
}
.rotate-y-180 {
    transform: rotateY(180deg);
}
</style>
