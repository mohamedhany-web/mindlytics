@extends('layouts.admin')

@section('title', 'الماليات الخاصة بالمدربين')
@section('header', 'الماليات الخاصة بالمدربين')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الماليات الخاصة بالمدربين</h1>
            <p class="text-gray-600 mt-1">اختر مدرباً لعرض كل المطلوب دفعه والمدفوع، والدفع في أي وقت (مسبقاً أو لاحقاً)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-amber-200">
            <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي مطلوب الدفع</p>
            <p class="text-3xl font-black text-amber-700">{{ number_format($globalStats['pending_total'], 2) }} ج.م</p>
            <p class="text-xs text-gray-500 mt-1">{{ $globalStats['pending_count'] }} مدفوعة</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-green-200">
            <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي تم الدفع</p>
            <p class="text-3xl font-black text-green-700">{{ number_format($globalStats['paid_total'], 2) }} ج.م</p>
            <p class="text-xs text-gray-500 mt-1">{{ $globalStats['paid_count'] }} مدفوعة</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
            <h2 class="text-lg font-bold text-gray-900">المدربون</h2>
            <p class="text-sm text-gray-600 mt-0.5">ادخل إلى صفحة أي مدرب لرؤية جميع المطلوب دفعه والمدفوع والدفع عند الحاجة</p>
        </div>
        @if($instructors->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 text-right text-sm text-gray-700">
                    <tr>
                        <th class="px-6 py-3">المدرب</th>
                        <th class="px-6 py-3">مطلوب الدفع</th>
                        <th class="px-6 py-3">تم الدفع</th>
                        <th class="px-6 py-3">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($instructors as $instructor)
                    @php $stats = $statsByInstructor->get($instructor->id); @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $instructor->name }}</span>
                            @if($instructor->email)
                                <span class="block text-xs text-gray-500">{{ $instructor->email }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($stats && (float)$stats->pending_total > 0)
                                <span class="font-bold text-amber-700">{{ number_format($stats->pending_total, 2) }} ج.م</span>
                                <span class="block text-xs text-gray-500">{{ $stats->pending_count }} مدفوعة</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($stats && (float)$stats->paid_total > 0)
                                <span class="font-bold text-green-700">{{ number_format($stats->paid_total, 2) }} ج.م</span>
                                <span class="block text-xs text-gray-500">{{ $stats->paid_count }} مدفوعة</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.salaries.instructor', $instructor) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium">
                                <i class="fas fa-list"></i>
                                عرض المطلوب والمدفوع
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">
            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
            <p class="font-medium">لا يوجد مدربون لديهم اتفاقيات أو مدفوعات حالياً.</p>
            <p class="text-sm mt-1">أي مدرب له اتفاقية أو مدفوعة (قيد المراجعة / مطلوب الدفع / تم الدفع) يظهر في القائمة.</p>
        </div>
        @endif
    </div>
</div>
@endsection
