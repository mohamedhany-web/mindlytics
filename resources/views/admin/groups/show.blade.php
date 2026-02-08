@extends('layouts.admin')

@section('title', 'تفاصيل المجموعة')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium">{{ session('error') }}</div>
    @endif
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.groups.index') }}" class="hover:text-white">المجموعات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white truncate">{{ Str::limit($group->name, 35) }}</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1 truncate">{{ $group->name }}</h1>
                <p class="text-sm text-white/90 mt-1">{{ $group->course->title ?? '' }} · مدرب: {{ $group->course->instructor->name ?? '—' }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.groups.edit', $group) }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟ سيتم حذف ربط الأعضاء أيضاً.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 bg-red-500/90 hover:bg-red-600 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors border border-white/30">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </form>
                <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات المجموعة -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-1">قائد المجموعة</p>
                <p class="text-sm font-semibold text-gray-900">{{ $group->leader->name ?? 'غير محدد' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-1">عدد الأعضاء</p>
                <p class="text-sm font-semibold text-gray-900">{{ $group->members->count() }} / {{ $group->max_members }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-1">الحالة</p>
                @php
                    $statusClass = $group->status == 'active' ? 'bg-green-100 text-green-800' : ($group->status == 'inactive' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800');
                    $statusText = $group->status == 'active' ? 'نشط' : ($group->status == 'inactive' ? 'غير نشط' : 'مؤرشف');
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusText }}</span>
            </div>
        </div>

        @if($group->description)
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">الوصف</h3>
                <p class="text-gray-700 text-sm">{{ $group->description }}</p>
            </div>
        @endif
    </div>

    <!-- أعضاء المجموعة -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-900">أعضاء المجموعة ({{ $group->members->count() }})</h2>
            @if($group->members->count() < $group->max_members)
                <button type="button" onclick="document.getElementById('addMemberModal').classList.remove('hidden'); document.getElementById('addMemberModal').classList.add('flex');" 
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-user-plus"></i> إضافة عضو
                </button>
            @endif
        </div>

        @if($group->members->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">البريد</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الدور</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">تاريخ الانضمام</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($group->members as $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $member->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $member->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($member->pivot->role == 'leader')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">قائد</span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">عضو</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('Y-m-d') : '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($member->pivot->role != 'leader')
                                        <form action="{{ route('admin.groups.remove-member', [$group, $member->id]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا العضو؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="إزالة"><i class="fas fa-user-minus"></i></button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">لا يوجد أعضاء في هذه المجموعة. استخدم زر «إضافة عضو» لإضافة طلاب مسجلين في الكورس.</p>
        @endif
    </div>
</div>

<!-- Modal إضافة عضو -->
<div id="addMemberModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">إضافة عضو جديد</h3>
        </div>
        <form action="{{ route('admin.groups.add-member', $group) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">الطالب (المسجلون في الكورس)</label>
                <select name="user_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">اختر الطالب</option>
                    @foreach($availableStudents as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                    @endforeach
                </select>
                @if($availableStudents->isEmpty())
                    <p class="mt-2 text-sm text-amber-600">لا يوجد طلاب مسجلون في الكورس غير مضافين للمجموعة.</p>
                @endif
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addMemberModal').classList.add('hidden'); document.getElementById('addMemberModal').classList.remove('flex');" 
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">إلغاء</button>
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">إضافة</button>
            </div>
        </form>
    </div>
</div>
@endsection
