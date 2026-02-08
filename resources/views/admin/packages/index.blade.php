@extends('layouts.admin')

@section('title', 'إدارة الباقات والأسعار')
@section('header', 'إدارة الباقات والأسعار')

@section('content')
<style>
    @media (max-width: 640px) {
        .course-card {
            min-width: 100%;
        }
        .filter-section {
            padding: 1rem;
        }
        .course-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
    /* إخفاء العناصر المخفية بـ x-cloak */
    [x-cloak] {
        display: none !important;
    }
    /* منع عرض الكود كـ text - إخفاء أي نص يحتوي على كود JavaScript */
    script, style {
        display: none !important;
    }
    /* تحسين overflow على الجوال */
    @media (max-width: 768px) {
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
        /* منع overflow-x على الجوال */
        body {
            overflow-x: hidden;
        }
        /* تحسين عرض النصوص */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    }
    /* تحسين عرض الأزرار على الجوال */
    @media (max-width: 640px) {
        button, a {
            touch-action: manipulation;
        }
    }
</style>

<div class="space-y-4 sm:space-y-6" x-data="{ activeTab: '{{ request('tab', 'packages') }}' }">
    <!-- الهيدر -->
    <div class="bg-white rounded-lg sm:rounded-xl shadow-lg p-4 sm:p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">إدارة الباقات والأسعار</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">إدارة باقات الكورسات وأسعار الكورسات الفردية</p>
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button x-show="activeTab === 'courses'" 
                        @click="activeTab = 'packages'"
                        class="flex-1 sm:flex-none bg-gray-600 hover:bg-gray-700 text-white px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-colors">
                    <i class="fas fa-box mr-2"></i>
                    الباقات
                </button>
                <button x-show="activeTab === 'packages'" 
                        @click="activeTab = 'courses'"
                        class="flex-1 sm:flex-none bg-gray-600 hover:bg-gray-700 text-white px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-colors">
                    <i class="fas fa-tags mr-2"></i>
                    أسعار الكورسات
                </button>
                <a href="{{ route('admin.packages.create') }}" 
                   x-show="activeTab === 'packages'"
                   class="flex-1 sm:flex-none bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base font-medium transition-colors shadow-lg shadow-sky-500/30 text-center">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة باقة جديدة
                </a>
            </div>
        </div>
    </div>

    <!-- التبويبات -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 space-x-reverse px-6">
                <button @click="activeTab = 'packages'" 
                        :class="activeTab === 'packages' ? 'border-sky-500 text-sky-600 ': ''border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-box ml-2"></i>
                    الباقات ({{ $packageStats['total'] ?? 0 }})
                </button>
                <button @click="activeTab = 'courses'" 
                        :class="activeTab === 'courses' ? 'border-sky-500 text-sky-600 ': ''border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-tags ml-2"></i>
                    إدارة أسعار الكورسات ({{ $courseStats['total'] ?? 0 }})
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- تبويب الباقات -->
            <div x-show="activeTab === 'packages'">
                <!-- إحصائيات الباقات -->
                @if(isset($packageStats))
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-sky-50 to-sky-100 rounded-xl shadow-lg p-6 border border-sky-200">
                        <div class="text-sm text-gray-600">إجمالي الباقات</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $packageStats['total'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-6 border border-green-200">
                        <div class="text-sm text-gray-600">الباقات النشطة</div>
                        <div class="text-2xl font-bold text-green-600 mt-2">{{ $packageStats['active'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-6 border border-red-200">
                        <div class="text-sm text-gray-600">الباقات المعطلة</div>
                        <div class="text-2xl font-bold text-red-600 mt-2">{{ $packageStats['inactive'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-lg p-6 border border-yellow-200">
                        <div class="text-sm text-gray-600">الباقات المميزة</div>
                        <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $packageStats['featured'] ?? 0 }}</div>
                    </div>
                </div>
                @endif

                <!-- فلاتر الباقات -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <form method="GET" action="{{ route('admin.packages.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="hidden" name="tab" value="packages">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="البحث في أسماء الباقات..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="">جميع الباقات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطلة</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                                <i class="fas fa-search mr-2"></i>
                                بحث
                            </button>
                        </div>
                    </form>
                </div>

                <!-- قائمة الباقات -->
                @if(isset($packages) && $packages->count() > 0)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الباقة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد الكورسات</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">السعر</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($packages as $package)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($package->thumbnail)
                                            <img src="{{ asset('storage/' . $package->thumbnail) }}" alt="{{ $package->name }}" class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center">
                                                <i class="fas fa-box text-white"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $package->name }}</div>
                                                @if($package->description)
                                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($package->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $package->courses_count ?? 0 }} كورس</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if($package->original_price && $package->original_price > $package->price)
                                            <span class="text-sm text-gray-400 line-through">{{ number_format($package->original_price, 2) }} ج.م</span>
                                            <span class="text-lg font-bold text-sky-600">{{ number_format($package->price, 2) }} ج.م</span>
                                            @else
                                            <span class="text-lg font-bold text-gray-900">{{ number_format($package->price, 2) }} ج.م</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $package->is_active ? 'نشط' : 'معطل' }}
                                            </span>
                                            @if($package->is_featured)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                مميز
                                            </span>
                                            @endif
                                            @if($package->is_popular)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                شائع
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.packages.show', $package) }}" class="text-sky-600 hover:text-sky-900" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.packages.edit', $package) }}" class="text-blue-600 hover:text-blue-900" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الباقة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $packages->appends(['tab' => 'packages'])->links() }}
                    </div>
                </div>
                @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
                    <i class="fas fa-box text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-600 text-lg">لا توجد باقات</p>
                    <a href="{{ route('admin.packages.create') }}" class="mt-4 inline-block bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة باقة جديدة
                    </a>
                </div>
                @endif
            </div>

            <!-- تبويب إدارة أسعار الكورسات -->
            <div x-show="activeTab === 'courses'">
                <!-- إحصائيات الكورسات -->
                @if(isset($courseStats))
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-6 border border-blue-200">
                        <div class="text-sm text-gray-600">إجمالي الكورسات</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $courseStats['total'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-6 border border-green-200">
                        <div class="text-sm text-gray-600">الكورسات المجانية</div>
                        <div class="text-2xl font-bold text-green-600 mt-2">{{ $courseStats['free'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-6 border border-purple-200">
                        <div class="text-sm text-gray-600">الكورسات المدفوعة</div>
                        <div class="text-2xl font-bold text-purple-600 mt-2">{{ $courseStats['paid'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-sky-50 to-sky-100 rounded-xl shadow-lg p-6 border border-sky-200">
                        <div class="text-sm text-gray-600">إجمالي القيمة</div>
                        <div class="text-2xl font-bold text-sky-600 mt-2">{{ number_format($courseStats['total_revenue'] ?? 0, 2) }} ج.م</div>
                    </div>
                </div>
                @endif

                <!-- فلاتر الكورسات -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-lg border border-gray-200 p-3 sm:p-4 md:p-6 mb-4 sm:mb-6 overflow-hidden" 
                     x-data="{ showFilters: false }">
                    <form method="GET" action="{{ route('admin.packages.index') }}" id="courses-filter-form">
                        <input type="hidden" name="tab" value="courses">
                        
                        <!-- البحث الرئيسي -->
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" name="course_search" id="course_search" value="{{ request('course_search') }}" 
                                       placeholder="ابحث في الكورسات..."
                                       class="w-full px-4 py-2.5 sm:py-3 pr-10 sm:pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm sm:text-base">
                                <button type="submit" class="absolute left-2 sm:left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-sky-600 p-2">
                                    <i class="fas fa-search text-sm sm:text-base"></i>
                                </button>
                            </div>
                        </div>

                        <!-- الفلاتر المتقدمة -->
                        <div class="border-t border-gray-200 pt-4">
                            <button type="button" @click="showFilters = !showFilters" 
                                    class="flex items-center justify-between w-full text-sm font-medium text-gray-700 hover:text-sky-600 transition-colors">
                                <span>
                                    <i class="fas fa-filter ml-2"></i>
                                    فلاتر متقدمة
                                </span>
                                <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': showFilters }"></i>
                            </button>
                            
                            <div x-show="showFilters" x-transition class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
                                <!-- فلتر الحالة (مجاني/مدفوع) -->
                                <div>
                                    <label for="course_status" class="block text-xs font-medium text-gray-700 mb-1.5 sm:mb-2">نوع السعر</label>
                                    <select name="course_status" id="course_status" 
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                        <option value="">الكل</option>
                                        <option value="free" {{ request('course_status') == 'free' ? 'selected' : '' }}>مجانية</option>
                                        <option value="paid" {{ request('course_status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                                    </select>
                                </div>

                                <!-- فلتر المستوى -->
                                <div>
                                    <label for="course_level" class="block text-xs font-medium text-gray-700 mb-1.5 sm:mb-2">المستوى</label>
                                    <select name="course_level" id="course_level" 
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                        <option value="">الكل</option>
                                        <option value="beginner" {{ request('course_level') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                        <option value="intermediate" {{ request('course_level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                        <option value="advanced" {{ request('course_level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                                    </select>
                                </div>

                                <!-- فلتر لغة البرمجة -->
                                <div>
                                    <label for="course_language" class="block text-xs font-medium text-gray-700 mb-1.5 sm:mb-2">لغة البرمجة</label>
                                    <select name="course_language" id="course_language" 
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                        <option value="">الكل</option>
                                        @if(isset($programmingLanguages) && $programmingLanguages->count() > 0)
                                            @foreach($programmingLanguages as $lang)
                                                <option value="{{ $lang }}" {{ request('course_language') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- فلتر التصنيف -->
                                <div>
                                    <label for="course_category" class="block text-xs font-medium text-gray-700 mb-1.5 sm:mb-2">التصنيف</label>
                                    <select name="course_category" id="course_category" 
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                        <option value="">الكل</option>
                                        @if(isset($categories) && $categories->count() > 0)
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}" {{ request('course_category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- فلتر الحالة (نشط/معطل) -->
                                <div>
                                    <label for="course_active" class="block text-xs font-medium text-gray-700 mb-1.5 sm:mb-2">الحالة</label>
                                    <select name="course_active" id="course_active" 
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                        <option value="">الكل</option>
                                        <option value="1" {{ request('course_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('course_active') == '0' ? 'selected' : '' }}>معطل</option>
                                    </select>
                                </div>
                            </div>

                            <!-- أزرار الإجراءات -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-0 mt-4 pt-4 border-t border-gray-200">
                                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30 text-sm">
                                    <i class="fas fa-search ml-2"></i>
                                    تطبيق الفلاتر
                                </button>
                                @if(request()->hasAny(['course_search', 'course_status', 'course_level', 'course_language', 'course_category', 'course_active']))
                                <a href="{{ route('admin.packages.index', ['tab' => 'courses']) }}" class="w-full sm:w-auto text-center text-gray-600 hover:text-gray-900 text-sm font-medium py-2.5 sm:py-0">
                                    <i class="fas fa-times ml-2"></i>
                                    إلغاء الفلاتر
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <!-- قائمة الكورسات -->
                @if(isset($courses) && $courses->count() > 0)
                <!-- معلومات النتائج -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0 mb-4">
                    <div class="text-xs sm:text-sm text-gray-600">
                        عرض <span class="font-semibold text-gray-900">{{ $courses->firstItem() }}</span> 
                        إلى <span class="font-semibold text-gray-900">{{ $courses->lastItem() }}</span> 
                        من أصل <span class="font-semibold text-gray-900">{{ $courses->total() }}</span> كورس
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 course-grid">
                    @foreach($courses as $index => $course)
                    <div class="bg-white rounded-lg sm:rounded-xl shadow-md sm:shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 course-card" 
                         x-data="{
                             editing: false, 
                             price: {{ $course->price ?? 0 }}, 
                             isFree: {{ $course->is_free ? 'true' : 'false' }},
                             courseId: {{ $course->id }},
                             updatePrice(event) {
                                 event.preventDefault();
                                 const form = event.target;
                                 fetch(form.action, {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                         'Accept': 'application/json'
                                     },
                                     body: JSON.stringify({
                                         price: this.price,
                                         is_free: this.isFree || this.price == 0
                                     })
                                 })
                                 .then(response => response.json())
                                 .then(data => {
                                     if (data.success) {
                                         this.editing = false;
                                         location.reload();
                                     } else {
                                         alert('حدث خطأ أثناء تحديث السعر');
                                     }
                                 })
                                 .catch(error => {
                                     console.error('Error:', error);
                                     alert('حدث خطأ أثناء تحديث السعر');
                                 });
                             }
                         }">
                        <!-- Course Header -->
                        <div class="h-28 sm:h-32 bg-gradient-to-br flex items-center justify-center relative overflow-hidden
                            @if($index % 6 == 0) from-sky-400 to-sky-600
                            @elseif($index % 6 == 1) from-blue-500 to-blue-700
                            @elseif($index % 6 == 2) from-indigo-500 to-indigo-700
                            @elseif($index % 6 == 3) from-purple-500 to-purple-700
                            @elseif($index % 6 == 4) from-pink-500 to-pink-700
                            @else from-red-500 to-red-700
                            @endif">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-code text-white text-4xl"></i>
                            @endif
                            @if($course->is_featured)
                            <div class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-bold">
                                مميز
                            </div>
                            @endif
                            @if($course->level)
                            <div class="absolute top-2 right-2 bg-white/20 backdrop-blur-sm rounded-full px-2 py-1">
                                <span class="text-white text-xs font-medium">
                                    @if($course->level == 'beginner') مبتدئ
                                    @elseif($course->level == 'intermediate') متوسط
                                    @else متقدم
                                    @endif
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-4 sm:p-5">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                            @if($course->description)
                            <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 line-clamp-2">{{ Str::limit($course->description, 80) }}</p>
                            @endif
                            
                            <!-- Course Info -->
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-3 sm:mb-4 text-[10px] sm:text-xs">
                                @if($course->programming_language)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-code ml-1 text-[10px]"></i>
                                    {{ $course->programming_language }}
                                </span>
                                @endif
                                @if($course->category)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-tag ml-1 text-[10px]"></i>
                                    {{ $course->category }}
                                </span>
                                @endif
                                @if($course->lessons_count)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    <i class="fas fa-play-circle ml-1 text-[10px]"></i>
                                    {{ $course->lessons_count }} درس
                                </span>
                                @endif
                                @if($course->duration_hours)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    <i class="fas fa-clock ml-1 text-[10px]"></i>
                                    {{ $course->duration_hours }} ساعة
                                </span>
                                @endif
                            </div>
                            
                            <!-- Price Section -->
                            <div class="border-t border-gray-200 pt-3 sm:pt-4 space-y-2 sm:space-y-3">
                                <!-- Current Price -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm text-gray-600">السعر الحالي:</span>
                                    @if($course->is_free || $course->price == 0)
                                        <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-gift ml-1 text-[10px] sm:text-xs"></i>
                                            مجاني
                                        </span>
                                    @else
                                        <span class="text-base sm:text-lg font-bold text-sky-600">{{ number_format($course->price, 2) }} ج.م</span>
                                    @endif
                                </div>
                                
                                <!-- Edit Price -->
                                <div x-show="!editing" class="flex items-center justify-between">
                                    <span class="text-xs sm:text-sm text-gray-600">السعر الجديد:</span>
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <span class="text-xs sm:text-sm font-medium text-gray-700" x-text="isFree || price == 0 ? 'مجاني' : price.toFixed(2) + ' ج.م'"></span>
                                        <button x-on:click="editing = true" class="p-1.5 sm:p-2 text-sky-600 hover:bg-sky-50 rounded-lg transition-colors">
                                            <i class="fas fa-edit text-xs sm:text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <form x-show="editing" 
                                      x-cloak
                                      action="{{ route('admin.packages.update-price', $course) }}" 
                                      method="POST"
                                      x-on:submit.prevent="updatePrice($event)"
                                      class="space-y-2">
                                    @csrf
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <input type="number" x-model.number="price" step="0.01" min="0" 
                                               class="flex-1 px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-sky-500">
                                        <label class="flex items-center text-[10px] sm:text-xs text-gray-600 whitespace-nowrap">
                                            <input type="checkbox" x-model="isFree" class="ml-1 w-3 h-3 sm:w-4 sm:h-4">
                                            مجاني
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <button type="submit" class="flex-1 px-2 sm:px-3 py-1.5 sm:py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                                            <i class="fas fa-check ml-1 text-[10px] sm:text-xs"></i>
                                            حفظ
                                        </button>
                                        <button type="button" x-on:click="editing = false; price = {{ $course->price ?? 0 }}; isFree = {{ $course->is_free ? 'true' : 'false' }}" class="px-2 sm:px-3 py-1.5 sm:py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs sm:text-sm transition-colors">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Status and Actions -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0 mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
                                <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium
                                    {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-circle ml-1 text-[6px]"></i>
                                    {{ $course->is_active ? 'نشط' : 'معطل' }}
                                </span>
                                <a href="{{ route('admin.advanced-courses.show', $course) }}" class="text-sky-600 hover:text-sky-900 text-xs sm:text-sm font-medium">
                                    <i class="fas fa-eye ml-1 text-xs"></i>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-4 sm:mt-6 flex justify-center overflow-x-auto">
                    <div class="min-w-0">
                        {{ $courses->appends(request()->except('courses_page'))->links() }}
                    </div>
                </div>
                @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد كورسات</h3>
                    <p class="text-gray-600 mb-4">لم يتم العثور على كورسات تطابق معايير البحث</p>
                    <a href="{{ route('admin.advanced-courses.create') }}" class="inline-block bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة كورس جديد
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
