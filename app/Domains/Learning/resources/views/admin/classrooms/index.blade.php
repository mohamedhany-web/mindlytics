@extends('layouts.admin')

@section('title', 'إدارة الفصول - Mindlytics')
@section('header', 'إدارة الفصول')

@section('content')
<div class="space-y-6">
        <!-- الهيدر -->
        <div class="bg-white shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">إدارة الفصول الدراسية</h1>
                        <p class="text-sm text-gray-500 mt-1">إدارة وتنظيم الفصول في المنصة</p>
                    </div>
                    <a href="{{ route('admin.classrooms.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة فصل جديد
                    </a>
                </div>
            </div>

            <!-- الفلاتر -->
            <div class="px-6 py-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="البحث في أسماء الفصول..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="school_id" class="block text-sm font-medium text-gray-700 mb-1">المدرسة</label>
                        <select name="school_id" id="school_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">جميع المدارس</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            بحث
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- قائمة الفصول -->
        @if($classrooms->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($classrooms as $classroom)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- هيدر البطاقة -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $classroom->name }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classroom->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $classroom->is_active ? 'نشط' : 'معطل' }}
                            </span>
                        </div>
                    </div>

                    <!-- محتوى البطاقة -->
                    <div class="px-6 py-4">
                        @if($classroom->description)
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($classroom->description, 100) }}</p>
                        @endif

                        <div class="space-y-2">
                            <!-- المدرسة -->
                            <div class="flex items-center text-sm">
                                <i class="fas fa-school text-gray-400 w-4 ml-2"></i>
                                <span class="text-gray-600">المدرسة:</span>
                                <span class="text-gray-900 mr-2">{{ $classroom->school->name ?? 'غير محدد' }}</span>
                            </div>

                            <!-- المعلم -->
                            <div class="flex items-center text-sm">
                                <i class="fas fa-user-tie text-gray-400 w-4 ml-2"></i>
                                <span class="text-gray-600">المعلم:</span>
                                <span class="text-gray-900 mr-2">{{ $classroom->teacher->name ?? 'غير محدد' }}</span>
                            </div>

                            <!-- عدد الطلاب -->
                            <div class="flex items-center text-sm">
                                <i class="fas fa-users text-gray-400 w-4 ml-2"></i>
                                <span class="text-gray-600">الطلاب:</span>
                                <span class="text-gray-900 mr-2">{{ $classroom->students_count }} طالب</span>
                            </div>

                            <!-- تاريخ الإنشاء -->
                            <div class="flex items-center text-sm">
                                <i class="fas fa-calendar text-gray-400 w-4 ml-2"></i>
                                <span class="text-gray-600">تاريخ الإنشاء:</span>
                                <span class="text-gray-900 mr-2">{{ $classroom->created_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.classrooms.show', $classroom) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>
                                    عرض
                                </a>
                                <a href="{{ route('admin.classrooms.edit', $classroom) }}" 
                                   class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>
                                    تعديل
                                </a>
                                <a href="{{ route('admin.classrooms.students', $classroom) }}" 
                                   class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                    <i class="fas fa-users mr-1"></i>
                                    الطلاب
                                </a>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <!-- تبديل الحالة -->
                                <button onclick="toggleStatus({{ $classroom->id }})"
                                        class="text-{{ $classroom->is_active ? 'red' : 'green' }}-600 hover:text-{{ $classroom->is_active ? 'red' : 'green' }}-800 text-sm font-medium">
                                    <i class="fas fa-{{ $classroom->is_active ? 'ban' : 'check' }} mr-1"></i>
                                    {{ $classroom->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}
                                </button>

                                <!-- حذف -->
                                <form method="POST" action="{{ route('admin.classrooms.destroy', $classroom) }}" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الفصل؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        <i class="fas fa-trash mr-1"></i>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- التصفح -->
            <div class="mt-8">
                {{ $classrooms->appends(request()->query())->links() }}
            </div>
        @else
            <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-chalkboard text-4xl mb-4"></i>
                    <p class="text-lg font-medium">لا توجد فصول دراسية</p>
                    <p class="text-sm mt-2">لم يتم العثور على أي فصول تطابق معايير البحث</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('admin.classrooms.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة أول فصل
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function toggleStatus(classroomId) {
    fetch(`/admin/classrooms/${classroomId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ أثناء تغيير حالة الفصل');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تغيير حالة الفصل');
    });
}
</script>
@endsection