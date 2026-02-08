@extends('layouts.admin')

@section('title', 'تفاصيل التسجيل')
@section('header', 'تفاصيل التسجيل')

@section('content')
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.online-enrollments.index') }}" class="hover:text-primary-600">التسجيلات</a>
                <span class="mx-2">/</span>
                <span>تفاصيل التسجيل</span>
            </nav>
        </div>
        <a href="{{ route('admin.enrollments.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- معلومات التسجيل -->
        <div class="xl:col-span-2">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">معلومات التسجيل</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($enrollment->status_color == 'yellow') bg-yellow-100 text-yellow-800
                        @elseif($enrollment->status_color == 'green') bg-green-100 text-green-800
                        @elseif($enrollment->status_color == 'blue') bg-blue-100 text-blue-800
                        @elseif($enrollment->status_color == 'red') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $enrollment->status_text }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الطالب</label>
                                <div class="font-semibold text-gray-900">{{ $enrollment->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $enrollment->student->email }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">رقم الهاتف</label>
                                <div class="text-gray-900">{{ $enrollment->student->phone ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الكورس</label>
                                <div class="font-semibold text-gray-900">{{ $enrollment->course->title }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $enrollment->course->academicYear->name ?? 'غير محدد' }} - 
                                    {{ $enrollment->course->academicSubject->name ?? 'غير محدد' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">التقدم</label>
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="text-gray-600">{{ $enrollment->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-primary-600 h-3 rounded-full transition-all duration-300" 
                                         style="width: {{ $enrollment->progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التسجيل</label>
                                <div class="text-gray-900">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i') : 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التفعيل</label>
                                <div class="text-gray-900">{{ $enrollment->activated_at ? $enrollment->activated_at->format('Y-m-d H:i') : 'غير مفعل' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($enrollment->activatedBy)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">تم التفعيل بواسطة</label>
                            <div class="text-gray-900">{{ $enrollment->activatedBy->name }}</div>
                        </div>
                    @endif

                    @if($enrollment->notes)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">ملاحظات</label>
                            <div class="bg-gray-50 p-3 rounded-lg text-gray-900">{{ $enrollment->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إجراءات سريعة -->
        <div class="space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">إجراءات سريعة</h4>
                </div>
                <div class="p-6 space-y-3">
                    @if($enrollment->status === 'pending')
                        <form action="{{ route('admin.online-enrollments.activate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')">
                                <i class="fas fa-check ml-1"></i>
                                تفعيل التسجيل
                            </button>
                        </form>
                    @elseif($enrollment->status === 'active')
                        <form action="{{ route('admin.online-enrollments.deactivate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد إلغاء تفعيل هذا التسجيل؟')">
                                <i class="fas fa-pause ml-1"></i>
                                إلغاء التفعيل
                            </button>
                        </form>
                    @elseif($enrollment->status === 'suspended')
                        <form action="{{ route('admin.online-enrollments.activate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد إعادة تفعيل هذا التسجيل وفتح الكورس للطالب مرة أخرى؟')">
                                <i class="fas fa-redo ml-1"></i>
                                إعادة التفعيل وفتح الكورس
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.online-enrollments.index') }}" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        <i class="fas fa-list ml-1"></i>
                        عرض جميع التسجيلات
                    </a>

                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">معلومات إضافية</h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">ID التسجيل</span>
                        <span class="text-sm text-gray-900">{{ $enrollment->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">تم الإنشاء</span>
                        <span class="text-sm text-gray-900">{{ $enrollment->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">آخر تحديث</span>
                        <span class="text-sm text-gray-900">{{ $enrollment->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الكورس -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">إحصائيات الكورس</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-4 bg-primary-50 rounded-lg">
                            <div class="text-2xl font-bold text-primary-600">{{ $enrollment->course->lessons->count() }}</div>
                            <div class="text-sm text-gray-500">دروس</div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $enrollment->course->duration_hours }}</div>
                            <div class="text-sm text-gray-500">ساعة</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $enrollment->course->enrollments->where('status', 'active')->count() }}</div>
                            <div class="text-sm text-gray-500">طالب مسجل</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
