@extends('layouts.admin')

@section('title', 'تفاصيل المهمة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- معلومات المهمة -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
                    <p class="text-gray-600 mt-2">المستخدم: {{ $task->user->name }}</p>
                </div>
                <div class="flex space-x-2 space-x-reverse">
                    @if($task->status != 'completed')
                    <form action="{{ route('admin.tasks.complete', $task) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check ml-2"></i>
                            إكمال المهمة
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn-secondary">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.tasks.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-right ml-2"></i>
                        رجوع
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">الحالة</p>
                    <p class="mt-2">
                        @if($task->status == 'completed')
                            <span class="badge badge-success">مكتملة</span>
                        @elseif($task->status == 'in_progress')
                            <span class="badge badge-primary">قيد التنفيذ</span>
                        @elseif($task->status == 'cancelled')
                            <span class="badge badge-danger">ملغاة</span>
                        @else
                            <span class="badge badge-warning">في الانتظار</span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">الأولوية</p>
                    <p class="mt-2">
                        @if($task->priority == 'urgent')
                            <span class="badge badge-danger">عاجلة</span>
                        @elseif($task->priority == 'high')
                            <span class="badge badge-warning">عالية</span>
                        @elseif($task->priority == 'medium')
                            <span class="badge badge-primary">متوسطة</span>
                        @else
                            <span class="badge badge-secondary">منخفضة</span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">تاريخ الاستحقاق</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $task->due_date ? $task->due_date->format('Y-m-d H:i') : '-' }}
                        @if($task->due_date && $task->due_date->isPast() && $task->status != 'completed')
                            <span class="text-red-600 text-sm block">متأخرة</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($task->description)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">الوصف</h3>
                <p class="text-gray-700">{{ $task->description }}</p>
            </div>
            @endif

            @if($task->related_type)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">مرتبطة بـ</h3>
                <p class="text-gray-700">
                    {{ ucfirst($task->related_type) }}: 
                    @if($task->relatedCourse)
                        <a href="{{ route('admin.advanced-courses.show', $task->relatedCourse) }}" class="text-sky-600 hover:text-sky-800">{{ $task->relatedCourse->title }}</a>
                    @elseif($task->relatedLecture)
                        <a href="{{ route('admin.lectures.show', $task->relatedLecture) }}" class="text-sky-600 hover:text-sky-800">{{ $task->relatedLecture->title }}</a>
                    @else
                        ID: {{ $task->related_id }}
                    @endif
                </p>
            </div>
            @endif

            @if($task->completed_at)
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    تم إكمال المهمة في: {{ $task->completed_at->format('Y-m-d H:i') }}
                </p>
            </div>
            @endif
        </div>

        <!-- التعليقات -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">التعليقات</h2>
            
            <div class="space-y-4 mb-6">
                @forelse($task->comments as $comment)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $comment->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    <p class="text-gray-700">{{ $comment->comment }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">لا توجد تعليقات</p>
                @endforelse
            </div>

            <form action="{{ route('admin.tasks.add-comment', $task) }}" method="POST">
                @csrf
                <div class="flex gap-4">
                    <input type="text" name="comment" required placeholder="أضف تعليقاً..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane ml-2"></i>
                        إرسال
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

