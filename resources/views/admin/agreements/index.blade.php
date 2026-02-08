@extends('layouts.admin')

@section('title', 'نظام الاتفاقيات - Mindlytics')
@section('header', 'نظام الاتفاقيات')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-handshake text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">إدارة اتفاقيات المدربين</h2>
                    <p class="text-sm text-slate-600 mt-1">إدارة عقود العمل وأنظمة الدفع للمدربين</p>
                </div>
            </div>
            <a href="{{ route('admin.agreements.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-plus"></i>
                إضافة اتفاقية جديدة
            </a>
        </div>
    </section>

    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-blue-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-blue-800/80 mb-1">إجمالي الاتفاقيات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-handshake text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/60 via-green-100/40 to-teal-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-emerald-800/80 mb-1">اتفاقيات نشطة</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['active']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-amber-200/50 hover:border-amber-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-100/60 via-yellow-100/40 to-orange-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-amber-800/80 mb-1">مسودات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-amber-700 via-yellow-600 to-orange-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['draft']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 via-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/60 via-violet-100/40 to-fuchsia-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-purple-800/80 mb-1">إجمالي المدفوعات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-purple-700 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['total_earned'], 2) }}</p>
                        <p class="text-xs font-medium text-purple-700/70 mt-1">ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 via-violet-500 to-fuchsia-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(168, 85, 247, 0.4);">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- البحث والفلترة -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.agreements.index') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">البحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ htmlspecialchars(request('search') ?? '', ENT_QUOTES, 'UTF-8') }}" maxlength="255" placeholder="رقم الاتفاقية، اسم المدرب" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">المدرب</label>
                    <select name="instructor_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع المدربين</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ request('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ htmlspecialchars($instructor->name, ENT_QUOTES, 'UTF-8') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">النوع</label>
                    <select name="type" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الأنواع</option>
                        <option value="course_price" {{ request('type') == 'course_price' ? 'selected' : '' }}>سعر للكورس</option>
                        <option value="hourly_rate" {{ request('type') == 'hourly_rate' ? 'selected' : '' }}>سعر للساعة</option>
                        <option value="monthly_salary" {{ request('type') == 'monthly_salary' ? 'selected' : '' }}>راتب شهري</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>منتهي</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-4">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    @if(request()->anyFilled(['search', 'instructor_id', 'type', 'status']))
                    <a href="{{ route('admin.agreements.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <!-- قائمة الاتفاقيات -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-contract text-blue-600"></i>
                    قائمة الاتفاقيات
                </h3>
                <p class="text-sm text-slate-600 mt-1">
                    <span class="font-semibold text-blue-600">{{ $agreements->total() }}</span> اتفاقية
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">رقم الاتفاقية</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">المدرب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">السعر/المعدل</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">تاريخ البدء</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($agreements as $agreement)
                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ htmlspecialchars($agreement->agreement_number ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ htmlspecialchars($agreement->title ?? '', ENT_QUOTES, 'UTF-8') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        {{ mb_substr($agreement->instructor->name ?? '', 0, 1, 'UTF-8') }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ htmlspecialchars($agreement->instructor->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</p>
                                        <p class="text-xs text-slate-500">{{ htmlspecialchars($agreement->instructor->phone ?? '-', ENT_QUOTES, 'UTF-8') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeBadges = [
                                        'course_price' => ['label' => 'سعر للكورس', 'classes' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                        'hourly_rate' => ['label' => 'سعر للساعة', 'classes' => 'bg-purple-100 text-purple-700 border-purple-200'],
                                        'monthly_salary' => ['label' => 'راتب شهري', 'classes' => 'bg-indigo-100 text-indigo-700 border-indigo-200'],
                                    ];
                                    $type = $typeBadges[$agreement->type] ?? ['label' => $agreement->type, 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border {{ $type['classes'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $type['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ number_format($agreement->rate ?? 0, 2) }} ج.م</div>
                                @if($agreement->type == 'hourly_rate')
                                    <div class="text-xs text-slate-500">للساعة</div>
                                @elseif($agreement->type == 'monthly_salary')
                                    <div class="text-xs text-slate-500">شهرياً</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusBadges = [
                                        'draft' => ['label' => 'مسودة', 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'],
                                        'active' => ['label' => 'نشط', 'classes' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                        'suspended' => ['label' => 'معلق', 'classes' => 'bg-amber-100 text-amber-700 border-amber-200'],
                                        'terminated' => ['label' => 'منتهي', 'classes' => 'bg-rose-100 text-rose-700 border-rose-200'],
                                        'completed' => ['label' => 'مكتمل', 'classes' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                    ];
                                    $status = $statusBadges[$agreement->status] ?? ['label' => $agreement->status, 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border {{ $status['classes'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700">
                                <div class="font-medium">{{ $agreement->start_date ? $agreement->start_date->format('Y-m-d') : '-' }}</div>
                                @if($agreement->end_date)
                                    <div class="text-slate-500">حتى {{ $agreement->end_date->format('Y-m-d') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.agreements.show', $agreement) }}" 
                                       class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors duration-200"
                                       title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.agreements.edit', $agreement) }}" 
                                       class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors duration-200"
                                       title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-handshake text-slate-400 text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">لا توجد اتفاقيات</p>
                                        <p class="text-sm text-slate-600 mt-1">ابدأ بإنشاء اتفاقية جديدة</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($agreements->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $agreements->appends(request()->query())->links() }}
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
// حماية من XSS في البحث
const filterForm = document.getElementById('filterForm');
if (filterForm) {
    filterForm.addEventListener('submit', function(e) {
        // Sanitization يتم في الخادم
    });
}

// Sanitization للبحث
function sanitizeInput(input) {
    return input.replace(/[<>]/g, '');
}
</script>
@endpush
@endsection
