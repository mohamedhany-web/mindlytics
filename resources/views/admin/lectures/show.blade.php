@extends('layouts.admin')

@section('title', 'تفاصيل المحاضرة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- معلومات المحاضرة -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $lecture->title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $lecture->course->title ?? '' }}</p>
                </div>
                <div class="flex space-x-2 space-x-reverse">
                    <a href="{{ route('admin.lectures.edit', $lecture) }}" class="btn-primary">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.lectures.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-right ml-2"></i>
                        رجوع
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">المحاضر</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $lecture->instructor->name ?? '-' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">تاريخ ووقت المحاضرة</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $lecture->scheduled_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">المدة</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $lecture->duration_minutes }} دقيقة</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">الحالة</p>
                    <p>
                        @if($lecture->status == 'completed')
                            <span class="badge badge-success">مكتملة</span>
                        @elseif($lecture->status == 'in_progress')
                            <span class="badge badge-primary">قيد التنفيذ</span>
                        @elseif($lecture->status == 'cancelled')
                            <span class="badge badge-danger">ملغاة</span>
                        @else
                            <span class="badge badge-warning">مجدولة</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($lecture->description)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">الوصف</h3>
                <p class="text-gray-700">{{ $lecture->description }}</p>
            </div>
            @endif

            <!-- روابط Teams -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @if($lecture->teams_registration_link)
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <h4 class="font-semibold text-gray-900 mb-2">رابط تسجيل Teams</h4>
                    <a href="{{ $lecture->teams_registration_link }}" target="_blank" class="text-sky-600 hover:text-sky-800">
                        {{ $lecture->teams_registration_link }} <i class="fas fa-external-link-alt mr-1"></i>
                    </a>
                </div>
                @endif

                @if($lecture->teams_meeting_link)
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <h4 class="font-semibold text-gray-900 mb-2">رابط اجتماع Teams</h4>
                    <a href="{{ $lecture->teams_meeting_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        {{ $lecture->teams_meeting_link }} <i class="fas fa-external-link-alt mr-1"></i>
                    </a>
                </div>
                @endif

                @if($lecture->recording_url)
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <h4 class="font-semibold text-gray-900 mb-2">رابط تسجيل المحاضرة</h4>
                    <a href="{{ $lecture->recording_url }}" target="_blank" class="text-purple-600 hover:text-purple-800">
                        {{ $lecture->recording_url }} <i class="fas fa-external-link-alt mr-1"></i>
                    </a>
                </div>
                @endif
            </div>

            <!-- خيارات المحاضرة -->
            <div class="flex flex-wrap gap-4 mb-6">
                @if($lecture->has_attendance_tracking)
                    <span class="badge badge-success">تتبع الحضور</span>
                @endif
                @if($lecture->has_assignment)
                    <span class="badge badge-primary">يوجد واجب</span>
                @endif
                @if($lecture->has_evaluation)
                    <span class="badge badge-warning">يوجد تقييم</span>
                @endif
            </div>

            @if($lecture->notes)
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                <h4 class="font-semibold text-gray-900 mb-2">ملاحظات</h4>
                <p class="text-gray-700">{{ $lecture->notes }}</p>
            </div>
            @endif
        </div>

        <!-- الحضور -->
        @if($lecture->has_attendance_tracking)
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">الحضور والانصراف</h2>
                <form action="{{ route('admin.lectures.sync-teams-attendance', $lecture) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sync ml-2"></i>
                        مزامنة من Teams
                    </button>
                </form>
            </div>
            <p class="text-gray-600 mb-4">سيتم استيراد الحضور تلقائياً من ملف Teams</p>
        </div>
        @endif

        <!-- الواجبات -->
        @if($lecture->has_assignment && $lecture->assignments->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">واجبات المحاضرة</h2>
            <div class="space-y-4">
                @foreach($lecture->assignments as $assignment)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900">{{ $assignment->title }}</h4>
                    <p class="text-sm text-gray-600 mt-1">تاريخ التسليم: {{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '-' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

