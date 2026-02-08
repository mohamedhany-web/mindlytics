@extends('layouts.admin')

@section('title', 'إدارة تسجيل الطلاب - الأونلاين')
@section('header', 'إدارة تسجيل الطلاب - الأونلاين')

@section('content')
<div class="space-y-6">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- إجمالي التسجيلات -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي التسجيلات</p>
                        <p class="text-3xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600">جميع تسجيلات الطلاب</p>
            </div>
        </div>

        <!-- في الانتظار -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">في الانتظار</p>
                        <p class="text-3xl font-black text-yellow-700">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-yellow-600">بحاجة للتفعيل</p>
            </div>
        </div>

        <!-- نشط -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                        <p class="text-3xl font-black text-green-700">{{ number_format($stats['active']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-green-600">مفعل ويتعلم</p>
            </div>
        </div>

        <!-- مكتمل -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مكتمل</p>
                        <p class="text-3xl font-black text-purple-700">{{ number_format($stats['completed']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-purple-600">أنهى الكورس</p>
            </div>
        </div>
    </div>

    <!-- البحث والفلترة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">البحث والفلترة</h3>
            <a href="{{ route('admin.online-enrollments.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                تسجيل طالب جديد
            </a>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم أو رقم الهاتف..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                </select>
            </div>

            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">الكورس</label>
                <select name="course_id" id="course_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 items-end">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-search mr-2"></i>
                    بحث
                </button>
                <a href="{{ route('admin.online-enrollments.index') }}" class="btn-secondary">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- البحث السريع بالهاتف -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">البحث السريع بالهاتف</h3>
        <div class="flex gap-4">
            <div class="flex-1">
                <input type="text" id="quickSearchPhone" placeholder="أدخل رقم هاتف الطالب أو ولي الأمر..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="button" onclick="quickSearchByPhone()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                بحث سريع
            </button>
        </div>
        <div id="quickSearchResult" class="mt-4 hidden">
            <!-- نتائج البحث السريع ستظهر هنا -->
        </div>
    </div>

    <!-- قائمة التسجيلات -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        @if($enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $enrollment->student->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $enrollment->student->phone }}</div>
                                        @if($enrollment->student->parent_phone)
                                            <div class="text-xs text-gray-400">ولي الأمر: {{ $enrollment->student->parent_phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $enrollment->course->title }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $enrollment->course->academicYear->name ?? 'غير محدد' }} - 
                                    {{ $enrollment->course->academicSubject->name ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $enrollment->status_color == 'green' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $enrollment->status_color == 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $enrollment->status_color == 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $enrollment->status_color == 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $enrollment->status_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $enrollment->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $enrollment->enrolled_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.online-enrollments.show', $enrollment) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($enrollment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.online-enrollments.activate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" 
                                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')"
                                                    title="تفعيل التسجيل">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @elseif($enrollment->status === 'active')
                                        <form method="POST" action="{{ route('admin.online-enrollments.deactivate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" 
                                                    onclick="return confirm('هل تريد إيقاف هذا التسجيل؟')"
                                                    title="إيقاف التسجيل">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @elseif($enrollment->status === 'suspended')
                                        <form method="POST" action="{{ route('admin.online-enrollments.activate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-900" 
                                                    onclick="return confirm('هل تريد إعادة تفعيل هذا التسجيل وفتح الكورس للطالب مرة أخرى؟')"
                                                    title="إعادة تفعيل التسجيل">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.online-enrollments.destroy', $enrollment) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('هل تريد حذف هذا التسجيل؟')">
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
                {{ $enrollments->appends(request()->query())->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد تسجيلات</h3>
                <p class="text-gray-500 mb-4">لم يتم العثور على تسجيلات تطابق معايير البحث</p>
                <a href="{{ route('admin.online-enrollments.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة أول تسجيل
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function quickSearchByPhone() {
    const phone = document.getElementById('quickSearchPhone').value.trim();
    const resultDiv = document.getElementById('quickSearchResult');
    
    if (!phone) {
        alert('يرجى إدخال رقم الهاتف');
        return;
    }
    
    // إظهار loader
    resultDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-600"></i> جاري البحث...</div>';
    resultDiv.classList.remove('hidden');
    
    fetch(`{{ route('admin.online-enrollments.search-by-phone') }}?phone=${encodeURIComponent(phone)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.student;
                resultDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-2">تم العثور على الطالب:</h4>
                        <div class="text-sm">
                            <p><strong>الاسم:</strong> ${student.name}</p>
                            <p><strong>هاتف الطالب:</strong> ${student.phone}</p>
                            ${student.parent_phone ? `<p><strong>هاتف ولي الأمر:</strong> ${student.parent_phone}</p>` : ''}
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.online-enrollments.create') }}?student_id=${student.id}" 
                               class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                <i class="fas fa-plus mr-1"></i>
                                تسجيل في كورس
                            </a>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-900">${data.error}</h4>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-medium text-red-900">حدث خطأ في البحث</h4>
                </div>
            `;
        });
}

// البحث عند الضغط على Enter
document.getElementById('quickSearchPhone').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        quickSearchByPhone();
    }
});
</script>
@endsection
