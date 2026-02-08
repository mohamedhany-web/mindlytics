@extends('layouts.student-dashboard')

@section('title', 'لوحة تحكم الطالب')

@push('styles')
<style>
    /* Global Icon Alignment Fix */
    .card-icon,
    .section-icon,
    .course-icon {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        line-height: 1 !important;
    }
    
    .card-icon i,
    .section-icon i,
    .course-icon i {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
        vertical-align: middle !important;
    }
    
    /* Welcome Section - أنيق ومتسق */
    .welcome-section {
        background: white;
        border-radius: 16px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid rgb(226 232 240);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .welcome-section:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        border-color: rgb(186 230 253);
    }
    .welcome-section .welcome-accent {
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, rgb(14 165 233), rgb(2 132 199));
        border-radius: 0 16px 16px 0;
    }

    .welcome-section .progress-ring-svg {
        transform: rotate(-90deg);
    }

    .welcome-section .progress-ring-bg {
        fill: none;
        stroke: rgb(224 242 254);
        stroke-width: 6;
    }

    .welcome-section .progress-ring-fill {
        fill: none;
        stroke: url(#welcomeProgressGradient);
        stroke-width: 6;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Dashboard Cards - Like Admin */
    .dashboard-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid rgb(226 232 240);
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.12);
        border-color: rgb(186 230 253);
    }

    .dashboard-card.blue { border-color: rgba(14, 165, 233, 0.25); }
    .dashboard-card.green { border-color: rgba(16, 185, 129, 0.25); }
    .dashboard-card.purple { border-color: rgba(139, 92, 246, 0.25); }
    .dashboard-card.amber { border-color: rgba(245, 158, 11, 0.25); }

    .card-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        transition: all 0.3s ease;
        line-height: 1;
        text-align: center;
    }
    .card-icon i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        margin: 0;
        padding: 0;
        vertical-align: middle;
    }
    .dashboard-card:hover .card-icon { transform: scale(1.05); }
    .dashboard-card.blue .card-icon { background: linear-gradient(135deg, rgb(14 165 233), rgb(2 132 199)); color: white; }
    .dashboard-card.green .card-icon { background: linear-gradient(135deg, rgb(16 185 129), rgb(5 150 105)); color: white; }
    .dashboard-card.purple .card-icon { background: linear-gradient(135deg, rgb(139 92 246), rgb(99 102 241)); color: white; }
    .dashboard-card.amber .card-icon { background: linear-gradient(135deg, rgb(245 158 11), rgb(217 119 6)); color: white; }

    /* Section Cards */
    .section-card {
        background: white;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        padding: 24px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        position: relative;
        overflow: hidden;
    }
    .section-card:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.08);
        border-color: rgb(186 230 253);
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(243, 244, 246, 0.8);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 17px;
        font-weight: 700;
        color: rgb(17 24 39);
    }

    .section-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        line-height: 1;
        text-align: center;
    }
    
    .section-icon i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        margin: 0;
        padding: 0;
        vertical-align: middle;
    }

    .section-card:hover .section-icon {
        transform: scale(1.1) rotate(5deg);
    }

    /* Course Cards */
    .course-card {
        background: white;
        border: 1px solid rgb(226 232 240);
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .course-card:hover {
        border-color: rgb(186 230 253);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        background: rgb(248 250 252);
    }
    .course-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: rgb(224 242 254);
        color: rgb(14 165 233);
        transition: all 0.2s;
        line-height: 1;
        text-align: center;
    }
    .course-icon i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        margin: 0;
        padding: 0;
        vertical-align: middle;
    }
    .course-card:hover .course-icon {
        background: rgb(14 165 233);
        color: white;
        transform: scale(1.05);
    }

    /* Progress Bar */
    .progress-container {
        position: relative;
        height: 8px;
        background: rgb(243 244 246);
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, rgb(14 165 233), rgb(2 132 199));
        border-radius: 4px;
        transition: width 0.5s ease;
        position: relative;
        overflow: hidden;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* Item Cards */
    .item-card {
        background: rgb(249 250 251);
        border: 1px solid rgb(229 231 235);
        border-radius: 10px;
        padding: 14px;
        transition: all 0.2s;
    }
    .item-card:hover {
        background: white;
        border-color: rgb(203 213 225);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="w-full max-w-full space-y-6">
    <!-- Welcome Section -->
    @php
        $progress = min((int) $stats['total_progress'], 100);
        $circumference = 2 * 3.14159 * 42;
        $strokeDashoffset = $circumference - ($progress / 100) * $circumference;
    @endphp
    <div class="welcome-section relative">
        <div class="welcome-accent" aria-hidden="true"></div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-2">لوحة تحكمك</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 leading-tight">
                    مرحباً، {{ auth()->user()->name }}
                </h1>
                <p class="text-gray-600 text-sm sm:text-base max-w-xl leading-relaxed">
                    استمر في رحلتك التعليمية واجعل كل يوم خطوة نحو تحقيق أهدافك
                </p>
            </div>
            <div class="flex items-center gap-5 flex-shrink-0">
                <div class="relative flex items-center justify-center">
                    <svg class="progress-ring-svg w-24 h-24" viewBox="0 0 96 96" aria-hidden="true">
                        <defs>
                            <linearGradient id="welcomeProgressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0ea5e9"/>
                                <stop offset="100%" stop-color="#0284c7"/>
                            </linearGradient>
                        </defs>
                        <circle class="progress-ring-bg" cx="48" cy="48" r="42"/>
                        <circle class="progress-ring-fill" cx="48" cy="48" r="42"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $strokeDashoffset }}"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-lg font-bold text-sky-700">{{ $stats['total_progress'] }}%</span>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-700">التقدم الكلي</p>
                    <p class="text-xs text-gray-500 mt-0.5">من إكمال الكورسات</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-100 to-sky-50 flex items-center justify-center text-sky-600 border border-sky-100 shadow-sm hidden sm:flex">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Active Courses -->
        <a href="{{ route('my-courses.index') }}" class="dashboard-card blue group">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-sky-700 mb-2">كورساتي النشطة</p>
                        <p class="text-4xl font-black text-sky-600">{{ $stats['active_courses'] }}</p>
                    </div>
                    <div class="card-icon flex-shrink-0">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600">كورسات نشطة حالياً</p>
            </div>
        </a>

        <!-- Completed Courses -->
        <a href="{{ route('student.certificates.index') }}" class="dashboard-card green group">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-emerald-700 mb-2">مكتملة</p>
                        <p class="text-4xl font-black text-emerald-600">{{ $stats['completed_courses'] }}</p>
                    </div>
                    <div class="card-icon flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600">كورسات مكتملة</p>
            </div>
        </a>

        <!-- Progress -->
        <div class="dashboard-card purple group">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-sky-700 mb-2">التقدم الكلي</p>
                        <p class="text-4xl font-black text-sky-600">{{ $stats['total_progress'] }}%</p>
                    </div>
                    <div class="card-icon flex-shrink-0">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="progress-container mt-3">
                    <div class="progress-fill" style="width: {{ $stats['total_progress'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <a href="{{ route('orders.index') }}" class="dashboard-card amber group">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-amber-700 mb-2">طلبات معلقة</p>
                        <p class="text-4xl font-black text-amber-600">{{ $stats['pending_orders'] }}</p>
                    </div>
                    <div class="card-icon flex-shrink-0">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600">طلبات قيد المعالجة</p>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 w-full">
        <!-- My Courses -->
        <div class="lg:col-span-2 min-w-0">
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-icon bg-sky-100 text-sky-600 border-2 border-sky-200">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <span>كورساتي النشطة</span>
                    </div>
                    <a href="{{ route('my-courses.index') }}" class="text-sm text-sky-600 hover:text-sky-700 font-semibold transition-colors flex items-center gap-1">
                        عرض الكل <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($activeCourses->take(5) as $course)
                        @php
                            $progress = (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0);
                        @endphp
                        <a href="{{ route('my-courses.show', $course->id) }}" class="course-card block">
                            <div class="flex items-center gap-4">
                                <div class="course-icon flex-shrink-0">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 mb-1.5 truncate text-base">{{ $course->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-3 truncate">
                                        {{ $course->academicSubject->name ?? 'غير محدد' }} - {{ $course->academicYear->name ?? 'غير محدد' }}
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 progress-container">
                                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700 min-w-[45px] text-left">{{ $progress }}%</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-12 text-gray-500">
                            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-book-open text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-base font-semibold mb-2 text-gray-700">لا توجد كورسات نشطة</p>
                            <p class="text-sm text-gray-600 mb-6">ابدأ رحلتك التعليمية الآن</p>
                            <a href="{{ route('academic-years') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-sm font-semibold transition-all shadow-md hover:shadow-lg">
                                <i class="fas fa-search"></i>
                                <span>استكشف الكورسات</span>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6 min-w-0">
            <!-- Upcoming Assignments -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-icon bg-amber-100 text-amber-600 border-2 border-amber-200">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span>الواجبات</span>
                        @if($upcomingAssignments->count() > 0)
                            <span class="bg-amber-100 text-amber-700 status-badge mr-auto">
                                {{ $upcomingAssignments->count() }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingAssignments->take(3) as $assignment)
                        @php
                            $lecture = $assignment->lecture;
                            $course = optional($lecture)->course;
                            $dueDate = optional($assignment->due_date);
                            $isOverdue = $dueDate && $dueDate->isPast();
                        @endphp
                        <div class="item-card">
                            <div class="font-semibold text-gray-900 text-sm mb-1.5 truncate">{{ $assignment->title }}</div>
                            @if($course)
                                <div class="text-xs text-gray-600 mb-2.5 truncate">{{ $course->title }}</div>
                            @endif
                            @if($dueDate)
                                <span class="status-badge {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-sky-100 text-sky-700' }}">
                                    {{ $dueDate->translatedFormat('d M') }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clipboard-check text-3xl mb-3 opacity-30"></i>
                            <p class="text-sm font-medium">لا توجد واجبات</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Exams -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-icon bg-sky-100 text-sky-600 border-2 border-sky-200">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <span>الاختبارات</span>
                        @if($upcomingExams->count() > 0)
                            <span class="bg-sky-100 text-sky-700 status-badge mr-auto">
                                {{ $upcomingExams->count() }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingExams->take(3) as $exam)
                        @php
                            $course = $exam->course;
                            $startAt = $exam->start_time ?? ($exam->start_date ? $exam->start_date->copy()->startOfDay() : null);
                            $isAvailableNow = $startAt ? $startAt->isPast() : true;
                        @endphp
                        <a href="{{ route('student.exams.show', $exam) }}" class="item-card block hover:border-sky-200">
                            <div class="font-semibold text-gray-900 text-sm mb-1.5 truncate">{{ $exam->title }}</div>
                            @if($course)
                                <div class="text-xs text-gray-600 mb-2.5 truncate">{{ $course->title }}</div>
                            @endif
                            <span class="status-badge {{ $isAvailableNow ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                                {{ $isAvailableNow ? 'متاح' : 'قريباً' }}
                            </span>
                        </a>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-file-alt text-3xl mb-3 opacity-30"></i>
                            <p class="text-sm font-medium">لا توجد اختبارات</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Results & Certificates -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 w-full">
        <!-- Exam Results -->
        <div class="section-card min-w-0">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon bg-emerald-100 text-emerald-600 border-2 border-emerald-200">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <span>نتائج الامتحانات</span>
                </div>
            </div>
            <div class="space-y-3">
                @forelse($recentExamAttempts->take(4) as $attempt)
                    @php
                        $exam = $attempt->exam;
                        $course = optional($exam)->course;
                    @endphp
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-emerald-200 transition-colors">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-award text-emerald-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 text-sm mb-1 truncate">{{ $exam->title ?? 'امتحان محذوف' }}</div>
                            @if($course)
                                <div class="text-xs text-gray-600 mb-2 truncate">{{ $course->title }}</div>
                            @endif
                            <div class="flex items-center gap-2">
                                <span class="status-badge bg-emerald-100 text-emerald-700">
                                    {{ $attempt->result_status }}
                                </span>
                                @if(!is_null($attempt->percentage))
                                    <span class="text-sm font-semibold text-gray-700">{{ number_format($attempt->percentage, 1) }}%</span>
                                @endif
                            </div>
                        </div>
                        @if($exam)
                            <a href="{{ route('student.exams.result', [$exam, $attempt]) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
                                عرض
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">
                        <i class="fas fa-poll text-4xl mb-3 opacity-30"></i>
                        <p class="text-sm font-medium">لم يتم تسجيل نتائج بعد</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Certificates -->
        <div class="section-card min-w-0">
            <div class="section-header">
                <div class="section-title">
<div class="section-icon bg-amber-100 text-amber-600 border-2 border-amber-200">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <span>الشهادات الصادرة</span>
                </div>
            </div>
            <div class="space-y-3">
                @forelse($recentCertificates->take(4) as $certificate)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-amber-300 transition-colors">
                        <div class="w-12 h-12 bg-sky-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-ribbon text-sky-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 text-sm mb-1 truncate">
                                {{ $certificate->title ?? $certificate->course_name ?? 'شهادة بدون عنوان' }}
                            </div>
                            @if($certificate->course)
                                <div class="text-xs text-gray-600 mb-2 truncate">{{ $certificate->course->title }}</div>
                            @endif
                            @if($certificate->certificate_number)
                                <span class="status-badge bg-sky-100 text-sky-700">
                                    رقم: {{ $certificate->certificate_number }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">
                        <i class="fas fa-certificate text-4xl mb-3 opacity-30"></i>
                        <p class="text-sm font-medium">لم يتم إصدار شهادات حتى الآن</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
