@extends('layouts.admin')

@section('title', 'عرض المجموعة المهارية')
@section('header', 'عرض المجموعة المهارية')

@section('content')
<div class="space-y-6">
    <!-- معلومات المادة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white" 
                         style="background-color: {{ $academicSubject->color ?? '#3B82F6' }}">
                        <i class="{{ $academicSubject->icon ?? 'fas fa-book' }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $academicSubject->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $academicSubject->academicYear->name ?? 'غير محدد' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $academicSubject->is_active 
                            ? 'bg-green-100 text-green-800 ']
                            ': ''bg-red-100 text-red-800 }}">']
                        {{ $academicSubject->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                    <a href="{{ route('admin.academic-subjects.edit', $academicSubject) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.academic-subjects.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- معلومات أساسية -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900">المعلومات الأساسية</h4>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">اسم المجموعة:</span>
                            <span class="text-sm text-gray-900">{{ $academicSubject->name }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">رمز المجموعة:</span>
                            <span class="text-sm text-gray-900 font-mono">{{ $academicSubject->code }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">المسار التعليمي:</span>
                            <span class="text-sm text-gray-900">{{ $academicSubject->academicYear->name ?? 'غير محدد' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">ترتيب العرض:</span>
                            <span class="text-sm text-gray-900">{{ $academicSubject->order ?? '-' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">الحالة:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $academicSubject->is_active 
                                    ? 'bg-green-100 text-green-800 ']
                                    ': ''bg-red-100 text-red-800 }}">']
                                {{ $academicSubject->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- الوصف والتصميم -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900">التصميم والوصف</h4>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">اللون:</span>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full border border-gray-300" 
                                     style="background-color: {{ $academicSubject->color ?? '#3B82F6' }}"></div>
                                <span class="text-sm text-gray-900 font-mono">{{ $academicSubject->color ?? '#3B82F6' }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">الأيقونة:</span>
                            <div class="flex items-center gap-2">
                                <i class="{{ $academicSubject->icon ?? 'fas fa-book' }} text-lg" 
                                   style="color: {{ $academicSubject->color ?? '#3B82F6' }}"></i>
                                <span class="text-sm text-gray-900 font-mono">{{ $academicSubject->icon ?? 'fas fa-book' }}</span>
                            </div>
                        </div>
                        
                        @if($academicSubject->description)
                        <div>
                            <span class="text-sm font-medium text-gray-600">الوصف:</span>
                            <p class="text-sm text-gray-900 mt-2 leading-relaxed">{{ $academicSubject->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات المادة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- الكورسات -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">الكورسات</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $academicSubject->advancedCourses->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-blue-600">كورسات متاحة</span>
            </div>
        </div>

        <!-- الكورسات النشطة -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">كورسات نشطة</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $academicSubject->advancedCourses->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-green-600">جاهزة للتسجيل</span>
            </div>
        </div>

        <!-- تاريخ الإنشاء -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">تاريخ الإنشاء</p>
                    <p class="text-lg font-bold text-gray-900">{{ $academicSubject->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-purple-600">{{ $academicSubject->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    <!-- الكورسات المرتبطة -->
    @if($academicSubject->advancedCourses->count() > 0)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الكورسات المرتبطة</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($academicSubject->advancedCourses as $course)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $course->title }}</h4>
                            <p class="text-xs text-gray-500">{{ $course->description }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $course->is_active 
                                ? 'bg-green-100 text-green-800 ']
                                ': ''bg-red-100 text-red-800 }}">']
                            {{ $course->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                        <a href="{{ route('admin.advanced-courses.show', $course) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection