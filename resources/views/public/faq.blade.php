@extends('layouts.public')

@section('title', 'الأسئلة الشائعة - Mindlytics')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            الأسئلة الشائعة
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            إجابات على أكثر الأسئلة شيوعاً
        </p>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        @if($categories->count() > 0)
        <!-- Categories Filter -->
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button onclick="filterCategory('all')" class="category-filter px-6 py-2 bg-sky-600 text-white rounded-lg font-medium shadow-lg card-hover" data-category="all">
                الكل
            </button>
            @foreach($categories as $category)
            <button onclick="filterCategory('{{ $category }}')" class="category-filter px-6 py-2 bg-white text-gray-700 rounded-lg font-medium hover:bg-sky-600 hover:text-white transition-all shadow-md card-hover" data-category="{{ $category }}">
                {{ $category }}
            </button>
            @endforeach
        </div>
        @endif

        <!-- FAQ Accordion -->
        <div class="max-w-4xl mx-auto space-y-4">
            @foreach($faqs as $categoryName => $categoryFaqs)
            <div class="faq-category" data-category="{{ $categoryName ?? 'general' }}">
                @if($categoryName)
                <h2 class="text-3xl font-bold text-gray-900 mb-6 mt-8 flex items-center">
                    <i class="fas fa-folder-open text-sky-500 ml-3"></i>
                    {{ $categoryName }}
                </h2>
                @endif
                
                @foreach($categoryFaqs as $faq)
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mb-4 card-hover">
                    <button onclick="toggleFAQ({{ $faq->id }})" class="w-full px-6 py-4 text-right flex items-center justify-between hover:bg-gradient-to-r hover:from-sky-50 hover:to-blue-50 transition-all">
                        <span class="text-lg font-semibold text-gray-900">{{ $faq->question }}</span>
                        <i class="fas fa-chevron-down text-sky-500 faq-icon transition-transform" id="icon-{{ $faq->id }}"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4" id="answer-{{ $faq->id }}">
                        <div class="text-gray-700 leading-relaxed border-t border-gray-100 pt-4">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>

        @if($faqs->isEmpty())
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-question-circle text-gray-400 text-5xl"></i>
            </div>
            <p class="text-gray-600 text-xl">لا توجد أسئلة شائعة متاحة حالياً</p>
        </div>
        @endif
    </div>
</section>

<!-- Contact Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4 text-center">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">لم تجد إجابة؟</h3>
        <p class="text-gray-600 mb-6">تواصل معنا وسنكون سعداء لمساعدتك</p>
        <a href="{{ route('public.contact') }}" class="btn-primary">
            <i class="fas fa-envelope ml-2"></i>
            تواصل معنا
        </a>
    </div>
</section>

<script>
function toggleFAQ(id) {
    const answer = document.getElementById('answer-' + id);
    const icon = document.getElementById('icon-' + id);
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        answer.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function filterCategory(category) {
    const categories = document.querySelectorAll('.faq-category');
    const buttons = document.querySelectorAll('.category-filter');
    
    buttons.forEach(btn => {
        if (btn.dataset.category === category) {
            btn.classList.add('bg-sky-600', 'text-white');
            btn.classList.remove('bg-white', 'text-gray-700');
        } else {
            btn.classList.remove('bg-sky-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700');
        }
    });
    
    categories.forEach(cat => {
        if (category === 'all' || cat.dataset.category === category) {
            cat.style.display = 'block';
        } else {
            cat.style.display = 'none';
        }
    });
}
</script>
@endsection
