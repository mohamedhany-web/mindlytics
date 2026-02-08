@extends('layouts.employee')

@section('title', 'التقويم')
@section('header', 'التقويم')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
<style>
    .fc {
        font-family: 'IBM Plex Sans Arabic', sans-serif;
    }
    .fc-toolbar-title {
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الأحداث</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-green-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">المهام</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['tasks'] }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-purple-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">الإجازات</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['leaves'] }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-yellow-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">الاجتماعات</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['meetings'] }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-red-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">قادمة</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- التقويم -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-6">
        <div id="calendar"></div>
    </div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '{{ route("employee.calendar.events") }}',
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_self');
                info.jsEvent.preventDefault();
            }
        },
        eventDisplay: 'block',
        height: 'auto'
    });
    calendar.render();
});
</script>
@endpush
@endsection
