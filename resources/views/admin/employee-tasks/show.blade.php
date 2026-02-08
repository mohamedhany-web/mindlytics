@extends('layouts.admin')

@section('title', 'تفاصيل المهمة')
@section('header', 'تفاصيل المهمة')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- الهيدر -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-4 py-6 sm:px-8 sm:py-8 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                        <i class="fas fa-tasks"></i>
                        تفاصيل المهمة
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight">{{ $employeeTask->title }}</h1>
                    <p class="text-gray-600 text-lg">
                        عرض تفاصيل المهمة المخصصة للموظف
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.employee-tasks.edit', $employeeTask) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-edit"></i>
                        تعديل
                    </a>
                    <form action="{{ route('admin.employee-tasks.destroy', $employeeTask) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                            <i class="fas fa-trash"></i>
                            حذف
                        </button>
                    </form>
                    <a href="{{ route('admin.employee-tasks.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-500 hover:bg-gray-600 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المهمة -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="p-6 sm:p-8 space-y-6">
            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">معلومات المهمة</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الموظف -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الموظف</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-lg">{{ $employeeTask->employee->name }}</p>
                            @if($employeeTask->employee->employeeJob)
                                <p class="text-sm text-gray-600">{{ $employeeTask->employee->employeeJob->name }}</p>
                            @endif
                            @if($employeeTask->employee->employee_code)
                                <p class="text-xs text-gray-500">كود: {{ $employeeTask->employee->employee_code }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- المكلف -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">المكلف</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-user-tie text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-lg">{{ $employeeTask->assigner->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- الأولوية -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الأولوية</p>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold
                        @if($employeeTask->priority === 'urgent') bg-red-100 text-red-800 border-2 border-red-300
                        @elseif($employeeTask->priority === 'high') bg-orange-100 text-orange-800 border-2 border-orange-300
                        @elseif($employeeTask->priority === 'medium') bg-yellow-100 text-yellow-800 border-2 border-yellow-300
                        @else bg-gray-100 text-gray-800 border-2 border-gray-300
                        @endif">
                        @if($employeeTask->priority === 'urgent')
                            <i class="fas fa-exclamation-circle"></i>عاجل
                        @elseif($employeeTask->priority === 'high')
                            <i class="fas fa-arrow-up"></i>عالي
                        @elseif($employeeTask->priority === 'medium')
                            <i class="fas fa-minus"></i>متوسط
                        @else
                            <i class="fas fa-arrow-down"></i>منخفض
                        @endif
                    </span>
                </div>

                <!-- الحالة -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الحالة</p>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold
                        @if($employeeTask->status === 'completed') bg-green-100 text-green-800 border-2 border-green-300
                        @elseif($employeeTask->status === 'in_progress') bg-blue-100 text-blue-800 border-2 border-blue-300
                        @elseif($employeeTask->status === 'pending') bg-yellow-100 text-yellow-800 border-2 border-yellow-300
                        @elseif($employeeTask->status === 'cancelled') bg-red-100 text-red-800 border-2 border-red-300
                        @else bg-gray-100 text-gray-800 border-2 border-gray-300
                        @endif">
                        @if($employeeTask->status === 'completed')
                            <i class="fas fa-check-circle"></i>مكتملة
                        @elseif($employeeTask->status === 'in_progress')
                            <i class="fas fa-spinner fa-spin"></i>قيد التنفيذ
                        @elseif($employeeTask->status === 'pending')
                            <i class="fas fa-clock"></i>معلقة
                        @elseif($employeeTask->status === 'cancelled')
                            <i class="fas fa-times-circle"></i>ملغاة
                        @else
                            <i class="fas fa-pause"></i>معلقة مؤقتاً
                        @endif
                    </span>
                </div>

                <!-- الموعد النهائي -->
                @if($employeeTask->deadline)
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الموعد النهائي</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br {{ $employeeTask->deadline < now() && !in_array($employeeTask->status, ['completed', 'cancelled']) ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600' }} rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-lg {{ $employeeTask->deadline < now() && !in_array($employeeTask->status, ['completed', 'cancelled']) ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $employeeTask->deadline->format('Y-m-d') }}
                            </p>
                            @if($employeeTask->deadline < now() && !in_array($employeeTask->status, ['completed', 'cancelled']))
                                <p class="text-xs text-red-600 font-semibold">متأخرة</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- التقدم -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">التقدم</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700">{{ $employeeTask->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $employeeTask->progress }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- تاريخ البدء -->
                @if($employeeTask->started_at)
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ البدء</p>
                    <p class="font-semibold text-gray-900">{{ $employeeTask->started_at->format('Y-m-d H:i') }}</p>
                </div>
                @endif

                <!-- تاريخ الإكمال -->
                @if($employeeTask->completed_at)
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ الإكمال</p>
                    <p class="font-semibold text-gray-900">{{ $employeeTask->completed_at->format('Y-m-d H:i') }}</p>
                </div>
                @endif
            </div>

            <!-- الوصف -->
            @if($employeeTask->description)
            <div class="pt-6 border-t border-gray-200">
                <p class="text-sm font-semibold text-gray-600 mb-3">الوصف</p>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <p class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $employeeTask->description }}</p>
                </div>
            </div>
            @endif

            <!-- الملاحظات -->
            @if($employeeTask->notes)
            <div class="pt-6 border-t border-gray-200">
                <p class="text-sm font-semibold text-gray-600 mb-3">ملاحظات إضافية</p>
                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                    <p class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $employeeTask->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- التسليمات -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                <h2 class="text-xl font-bold text-gray-900">التسليمات</h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                    {{ $employeeTask->deliverables->count() }}
                </span>
            </div>

            @if($employeeTask->deliverables->count() > 0)
                <div class="space-y-4">
                    @foreach($employeeTask->deliverables as $deliverable)
                        <div class="bg-white rounded-xl p-5 border-2 border-gray-200 hover:border-blue-300 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0
                                            @if($deliverable->delivery_type === 'image') bg-gradient-to-br from-pink-500 to-pink-600
                                            @elseif($deliverable->delivery_type === 'link') bg-gradient-to-br from-purple-500 to-purple-600
                                            @else bg-gradient-to-br from-green-500 to-green-600
                                            @endif">
                                            @if($deliverable->delivery_type === 'image')
                                                <i class="fas fa-image text-xl"></i>
                                            @elseif($deliverable->delivery_type === 'link')
                                                <i class="fas fa-link text-xl"></i>
                                            @else
                                                <i class="fas fa-file-upload text-xl"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h3 class="font-bold text-gray-900 text-lg">{{ $deliverable->title }}</h3>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($deliverable->delivery_type === 'image') bg-pink-100 text-pink-800
                                                    @elseif($deliverable->delivery_type === 'link') bg-purple-100 text-purple-800
                                                    @else bg-blue-100 text-blue-800
                                                    @endif">
                                                    @if($deliverable->delivery_type === 'image')
                                                        <i class="fas fa-image"></i> صورة
                                                    @elseif($deliverable->delivery_type === 'link')
                                                        <i class="fas fa-link"></i> رابط
                                                    @else
                                                        <i class="fas fa-file"></i> ملف
                                                    @endif
                                                </span>
                                            </div>
                                            @if($deliverable->description)
                                                <p class="text-gray-600 mb-3 leading-relaxed">{{ $deliverable->description }}</p>
                                            @endif
                                            
                                            @if($deliverable->delivery_type === 'link' && $deliverable->link_url)
                                                <div class="mb-3">
                                                    <a href="{{ $deliverable->link_url }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                        <i class="fas fa-external-link-alt"></i>
                                                        {{ $deliverable->link_url }}
                                                    </a>
                                                </div>
                                            @elseif($deliverable->file_name)
                                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                                    <i class="fas fa-file"></i>
                                                    <span>{{ $deliverable->file_name }}</span>
                                                    @if($deliverable->file_size)
                                                        <span class="text-gray-400">({{ number_format($deliverable->file_size / 1024, 2) }} KB)</span>
                                                    @endif
                                                    @if($deliverable->file_path)
                                                        <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 ml-2">
                                                            <i class="fas fa-download"></i> تحميل
                                                        </a>
                                                    @endif
                                                </div>
                                                @if($deliverable->delivery_type === 'image' && $deliverable->file_path)
                                                    <div class="mt-3 mb-3">
                                                        <img src="{{ Storage::url($deliverable->file_path) }}" alt="{{ $deliverable->title }}" class="max-w-md rounded-lg border-2 border-gray-200 shadow-sm">
                                                    </div>
                                                @endif
                                            @endif
                                            
                                            @if($deliverable->feedback)
                                                <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                                    <p class="text-sm font-semibold text-gray-700 mb-1">ملاحظات المراجع:</p>
                                                    <p class="text-gray-900">{{ $deliverable->feedback }}</p>
                                                </div>
                                            @endif
                                            @if($deliverable->reviewer)
                                                <p class="text-xs text-gray-500 mt-2">
                                                    <i class="fas fa-user-check"></i> مراجع: {{ $deliverable->reviewer->name }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-clock"></i> {{ $deliverable->created_at->format('Y-m-d H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold
                                        @if($deliverable->status === 'approved') bg-green-100 text-green-800 border-2 border-green-300
                                        @elseif($deliverable->status === 'rejected') bg-red-100 text-red-800 border-2 border-red-300
                                        @elseif($deliverable->status === 'submitted') bg-blue-100 text-blue-800 border-2 border-blue-300
                                        @else bg-gray-100 text-gray-800 border-2 border-gray-300
                                        @endif">
                                        @if($deliverable->status === 'approved')
                                            <i class="fas fa-check-circle"></i>معتمد
                                        @elseif($deliverable->status === 'rejected')
                                            <i class="fas fa-times-circle"></i>مرفوض
                                        @elseif($deliverable->status === 'submitted')
                                            <i class="fas fa-paper-plane"></i>مقدم
                                        @else
                                            <i class="fas fa-clock"></i>معلق
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 font-semibold">لا توجد تسليمات حتى الآن</p>
                    <p class="text-sm text-gray-500 mt-2">لم يقم الموظف بتسليم أي ملفات لهذه المهمة</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
