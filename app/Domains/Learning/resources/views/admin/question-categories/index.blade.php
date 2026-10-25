@extends('layouts.admin')

@section('title', 'تصنيفات الأسئلة')
@section('header', 'تصنيفات الأسئلة')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium">{{ session('error') }}</div>
    @endif

    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.question-bank.index') }}" class="hover:text-white">بنك الأسئلة</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تصنيفات الأسئلة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تصنيفات الأسئلة</h1>
                <p class="text-sm text-white/90 mt-1">تنظيم الأسئلة في تصنيفات هرمية</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.question-bank.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-database"></i>
                    بنك الأسئلة
                </a>
                <a href="{{ route('admin.question-categories.create') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة تصنيف
                </a>
            </div>
        </div>
    </div>

    @if(isset($stats))
    <!-- إحصائيات -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-folder text-xl text-indigo-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p><p class="text-sm text-gray-500">إجمالي التصنيفات</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-xl text-green-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p><p class="text-sm text-gray-500">نشط</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-layer-group text-xl text-cyan-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['main'] }}</p><p class="text-sm text-gray-500">رئيسي</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-question-circle text-xl text-amber-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $stats['total_questions'] }}</p><p class="text-sm text-gray-500">إجمالي الأسئلة</p></div>
            </div>
        </div>
    </div>
    @endif

    <!-- التصنيفات -->
    @if($categories->count() > 0)
        <div class="space-y-4" id="categories-container">
            @foreach($categories as $category)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden" 
                     data-category-id="{{ $category->id }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <!-- أيقونة السحب -->
                                <div class="cursor-move text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                
                                <!-- معلومات التصنيف -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                                    <div class="flex items-center gap-4 text-sm text-gray-500 mt-1">
                                        <span>{{ $category->academicYear->name ?? 'غير محدد' }} - {{ $category->academicSubject->name ?? 'غير محدد' }}</span>
                                        <span>{{ $category->questions_count ?? 0 }} سؤال</span>
                                        @if($category->children->count() > 0)
                                            <span>{{ $category->children->count() }} تصنيف فرعي</span>
                                        @endif
                                    </div>
                                    @if($category->description)
                                        <p class="text-sm text-gray-600 mt-2">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- الإجراءات -->
                            <div class="flex items-center space-x-2 space-x-reverse">
                                @php
                                    $statusClass = $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                                
                                <a href="{{ route('admin.question-categories.show', $category) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.question-bank.create', ['category_id' => $category->id]) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition-colors" title="إضافة سؤال"><i class="fas fa-plus"></i></a>
                                <a href="{{ route('admin.question-categories.edit', $category) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.question-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        <!-- التصنيفات الفرعية -->
                        @if($category->children->count() > 0)
                            <div class="mt-4 mr-8 space-y-2">
                                @foreach($category->children as $subCategory)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3 space-x-reverse">
                                            <i class="fas fa-folder text-gray-400"></i>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $subCategory->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $subCategory->questions_count ?? 0 }} سؤال</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <a href="{{ route('admin.question-categories.show', $subCategory) }}" 
                                               class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.question-bank.create', ['category_id' => $subCategory->id]) }}" 
                                               class="text-green-600 hover:text-green-800 transition-colors">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-tags"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد تصنيفات</h3>
            <p class="text-gray-500 mb-6">ابدأ بإنشاء تصنيفات لتنظيم الأسئلة</p>
            <a href="{{ route('admin.question-categories.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إنشاء أول تصنيف
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// إعداد السحب والإفلات للتصنيفات
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('categories-container');
    if (container) {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'bg-blue-50',
            chosenClass: 'bg-blue-100',
            onEnd: function(evt) {
                const categories = [];
                container.querySelectorAll('[data-category-id]').forEach((element, index) => {
                    categories.push({
                        id: element.dataset.categoryId,
                        order: index + 1
                    });
                });
                
                // إرسال الترتيب الجديد
                fetch('{{ route("admin.question-categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ categories: categories })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload();
                });
            }
        });
    }
});
</script>
@endpush
@endsection
