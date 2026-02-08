@extends('layouts.admin')

@section('title', 'تعديل الباقة')
@section('header', 'تعديل الباقة')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">تعديل الباقة: {{ $package->name }}</h1>
            <a href="{{ route('admin.packages.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- المعلومات الأساسية -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الباقة *</label>
                    <input type="text" name="name" required value="{{ old('name', $package->name) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرابط (Slug)</label>
                    <input type="text" name="slug" value="{{ old('slug', $package->slug) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر *</label>
                    <input type="number" name="price" step="0.01" min="0" required value="{{ old('price', $package->price) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأصلي (قبل الخصم)</label>
                    <input type="number" name="original_price" step="0.01" min="0" value="{{ old('original_price', $package->original_price) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="order" min="0" value="{{ old('order', $package->order) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مدة الصلاحية (بالأيام)</label>
                    <input type="number" name="duration_days" min="0" value="{{ old('duration_days', $package->duration_days) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $package->starts_at ? $package->starts_at->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $package->ends_at ? $package->ends_at->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('description', $package->description) }}</textarea>
            </div>

            <!-- الصورة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صورة الباقة</label>
                @if($package->thumbnail)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $package->thumbnail) }}" alt="{{ $package->name }}" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                </div>
                @endif
                <input type="file" name="thumbnail" accept="image/*" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                <p class="mt-1 text-xs text-gray-500">اتركه فارغاً للاحتفاظ بالصورة الحالية</p>
            </div>

            <!-- المميزات -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المميزات</label>
                <div id="features-container" class="space-y-2">
                    @if($package->features && count($package->features) > 0)
                        @foreach($package->features as $feature)
                        <div class="flex gap-2">
                            <input type="text" name="features[]" value="{{ $feature }}" 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 {{ count($package->features) == 1 ? 'hidden' : '' }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endforeach
                    @else
                    <div class="flex gap-2">
                        <input type="text" name="features[]" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                               placeholder="مثال: وصول لجميع الكورسات">
                        <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 hidden">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                </div>
                <button type="button" onclick="addFeature()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة ميزة
                </button>
            </div>

            <!-- اختيار الكورسات -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكورسات *</label>
                <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto">
                    @if($courses->count() > 0)
                    <div class="space-y-2">
                        @foreach($courses as $course)
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="courses[]" value="{{ $course->id }}" 
                                   {{ in_array($course->id, old('courses', $package->courses->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                            <span class="mr-3 text-sm text-gray-700">
                                {{ $course->title }}
                                @if($course->price > 0)
                                <span class="text-xs text-gray-500">({{ number_format($course->price, 2) }} ج.م)</span>
                                @else
                                <span class="text-xs text-green-600">(مجاني)</span>
                                @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500 text-center">لا توجد كورسات متاحة</p>
                    @endif
                </div>
                @error('courses')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- الخيارات -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} 
                           class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <span class="mr-2 text-sm font-medium text-gray-700">نشط</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $package->is_featured) ? 'checked' : '' }} 
                           class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <span class="mr-2 text-sm font-medium text-gray-700">مميز</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $package->is_popular) ? 'checked' : '' }} 
                           class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <span class="mr-2 text-sm font-medium text-gray-700">الأكثر شعبية</span>
                </label>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-4 pt-4 border-t border-gray-200">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
                <a href="{{ route('admin.packages.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'flex gap-2';
    newFeature.innerHTML = `
        <input type="text" name="features[]" 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
               placeholder="مثال: وصول لجميع الكورسات">
        <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(newFeature);
    
    container.querySelectorAll('button').forEach(btn => {
        if (btn.textContent.includes('times')) {
            btn.classList.remove('hidden');
        }
    });
}

function removeFeature(button) {
    button.parentElement.remove();
    
    const container = document.getElementById('features-container');
    const inputs = container.querySelectorAll('input[type="text"]');
    if (inputs.length === 1) {
        container.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes('times')) {
                btn.classList.add('hidden');
            }
        });
    }
}
</script>
@endsection

