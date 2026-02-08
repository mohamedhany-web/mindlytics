@extends('layouts.admin')

@section('title', 'تسجيلات الطلاب - ' . $offlineCourse->title)
@section('header', 'تسجيلات الطلاب')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <a href="{{ route('admin.offline-courses.index') }}" class="hover:text-blue-600">الكورسات الأوفلاين</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="hover:text-blue-600">{{ $offlineCourse->title }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">التسجيلات</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">تسجيلات الطلاب: {{ $offlineCourse->title }}</h1>
                <p class="text-gray-600 mt-1">إدارة تسجيل الطلاب في الكورس الأوفلاين</p>
            </div>
            <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-arrow-right mr-2"></i>
                العودة للكورس
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- إضافة تسجيل -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4">تسجيل طالب جديد</h2>
        <form action="{{ route('admin.offline-courses.enrollments.store', $offlineCourse) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">الطالب <span class="text-red-500">*</span></label>
                <select name="user_id" id="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">اختر الطالب</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ old('user_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->email }})</option>
                    @endforeach
                </select>
                @if($students->isEmpty())
                    <p class="text-amber-600 text-xs mt-1">جميع الطلاب النشطين مسجلون أو لا يوجد طلاب.</p>
                @endif
            </div>
            <div>
                <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">المجموعة</label>
                <select name="group_id" id="group_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">بدون مجموعة</option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" {{ $students->isEmpty() ? 'disabled' : '' }}>
                    <i class="fas fa-plus mr-2"></i>
                    تسجيل الطالب
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة التسجيلات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">قائمة التسجيلات ({{ $enrollments->total() }})</h2>
        </div>
        @if($enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المجموعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $enrollment->student->name ?? '—' }}</div>
                                <div class="text-sm text-gray-500">{{ $enrollment->student->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $enrollment->group->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $enrollment->enrolled_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusLabels = [
                                        'pending' => ['قيد الانتظار', 'bg-amber-100 text-amber-800'],
                                        'active' => ['نشط', 'bg-green-100 text-green-800'],
                                        'completed' => ['منتهي', 'bg-blue-100 text-blue-800'],
                                        'suspended' => ['موقوف', 'bg-red-100 text-red-800'],
                                        'cancelled' => ['ملغي', 'bg-gray-100 text-gray-800'],
                                    ];
                                    $s = $statusLabels[$enrollment->status] ?? ['—', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $s[1] }}">{{ $s[0] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form action="{{ route('admin.offline-courses.enrollments.update-status', [$offlineCourse, $enrollment]) }}" method="POST" class="inline-block ml-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="{{ $enrollment->status === 'active' ? 'suspended' : 'active' }}">
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $enrollment->status === 'active' ? 'إيقاف' : 'تفعيل' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.offline-courses.enrollments.destroy', [$offlineCourse, $enrollment]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسجيل؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($enrollments->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $enrollments->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-user-graduate text-4xl text-gray-300 mb-3"></i>
                <p>لا يوجد تسجيلات لهذا الكورس. سجّل طلاباً من النموذج أعلاه.</p>
            </div>
        @endif
    </div>
</div>
@endsection
