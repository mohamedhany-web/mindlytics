@extends('layouts.app')

@section('title', 'التقويم الأكاديمي')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
<style>
    .fc {
        direction: rtl;
    }
    .fc-toolbar {
        flex-direction: row-reverse;
    }
    .fc-button-group {
        flex-direction: row-reverse;
    }
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        padding: 2px 4px;
    }
    .fc-daygrid-event {
        white-space: normal;
        font-size: 0.85rem;
    }
    .event-legend {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">التقويم الأكاديمي</h1>
                    <p class="text-sm text-gray-500 mt-1">جميع الأحداث والمواعيد المهمة في مكان واحد</p>
                </div>
                <div class="text-sm text-gray-600 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-sky-500"></i>
                    <span>إجمالي الأحداث: <strong>{{ $stats['total'] ?? 0 }}</strong></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- التقويم الرئيسي -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 md:p-6">
                    <div id="calendar" class="calendar-container"></div>
                    
                    <!-- مفتاح الألوان -->
                    <div class="event-legend mt-4 pt-4 border-t border-gray-200">
                        <div class="legend-item">
                            <div class="legend-color bg-red-500"></div>
                            <span>امتحانات</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-sky-500"></div>
                            <span>محاضرات</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-amber-500"></div>
                            <span>واجبات</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-emerald-500"></div>
                            <span>أحداث أخرى</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="lg:col-span-1 space-y-6">
                <!-- الأحداث القادمة -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 md:p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-sky-500"></i>
                        <span>الأحداث القادمة</span>
                    </h3>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($events->where('start_date', '>=', now())->take(10) as $event)
                            <div class="p-3 rounded-lg border border-gray-200 hover:border-sky-500 transition-colors cursor-pointer"
                                 onclick="window.location.href='{{ $event->url ?? '#' }}'">
                                <div class="flex items-start gap-2 mb-2">
                                    <div class="w-3 h-3 rounded-full mt-1.5 flex-shrink-0" 
                                         style="background-color: {{ $event->color ?? '#6B7280' }}"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold text-gray-900 truncate">{{ $event->title }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-calendar text-sky-500"></i>
                                            {{ $event->start_date->format('d/m/Y') }}
                                            @if(!$event->is_all_day)
                                                {{ $event->start_date->format('h:i A') }}
                                            @endif
                                        </div>
                                        <div class="text-xs mt-1 
                                            @if($event->type == 'exam') text-red-600
                                            @elseif($event->type == 'lecture') text-blue-600
                                            @elseif($event->type == 'assignment') text-yellow-600
                                            @else text-gray-600
                                            @endif font-semibold">
                                            @if($event->type == 'exam') 
                                                <i class="fas fa-clipboard-check"></i> امتحان
                                            @elseif($event->type == 'lecture') 
                                                <i class="fas fa-chalkboard-teacher"></i> محاضرة
                                            @elseif($event->type == 'assignment') 
                                                <i class="fas fa-tasks"></i> واجب
                                            @else 
                                                <i class="fas fa-calendar-alt"></i> حدث
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-calendar-times text-3xl mb-2 opacity-30"></i>
                                <p class="text-sm">لا توجد أحداث قادمة</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- إحصائيات -->
                <div class="bg-sky-500 rounded-xl p-4 md:p-6 text-white border border-sky-600">
                    <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-pie"></i>
                        <span>إحصائيات</span>
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clipboard-check"></i>
                                <span class="text-sm font-semibold">الامتحانات</span>
                            </div>
                            <span class="text-lg font-black">{{ $stats['exams'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span class="text-sm font-semibold">المحاضرات</span>
                            </div>
                            <span class="text-lg font-black">{{ $stats['lectures'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tasks"></i>
                                <span class="text-sm font-semibold">الواجبات</span>
                            </div>
                            <span class="text-lg font-black">{{ $stats['assignments'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-white/20 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-arrow-up"></i>
                                <span class="text-sm font-semibold">القادمة</span>
                            </div>
                            <span class="text-lg font-black">{{ $stats['upcoming'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        direction: 'rtl',
        initialView: 'dayGridMonth',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'اليوم',
            month: 'شهر',
            week: 'أسبوع',
            day: 'يوم'
        },
        events: {
            url: '{{ route("calendar.events") }}',
            failure: function() {
                alert('حدث خطأ في تحميل الأحداث');
            }
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_self');
                info.jsEvent.preventDefault();
            }
        },
        eventMouseEnter: function(info) {
            info.el.style.cursor = 'pointer';
        },
        eventContent: function(arg) {
            return {
                html: '<div class="fc-event-title">' + arg.event.title + '</div>'
            };
        },
        height: 'auto',
        contentHeight: 600,
        firstDay: 6, // السبت كأول يوم
        weekends: true,
        navLinks: true,
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });
    
    calendar.render();
});
</script>
@endpush
