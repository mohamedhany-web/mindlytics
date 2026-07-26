@extends('layouts.admin')

@section('title', 'استبيان العملاء')
@section('header', 'استبيان العملاء')

@section('content')
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">استبيان العملاء</h1>
                <p class="text-slate-500 mt-1">
                    ردود العملاء الذين اشتروا كورساً، وكل رد يمنح صاحبه كوبون خصم {{ $discountPercentage }}% تلقائياً.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('public.customer-survey.show') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-semibold transition-all">
                    <i class="fas fa-external-link-alt"></i>
                    <span>رابط الفورم</span>
                </a>
                <a href="{{ route('admin.marketing-customer-surveys.export') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl font-semibold shadow-lg shadow-emerald-500/30 transition-all">
                    <i class="fas fa-file-csv"></i>
                    <span>تصدير CSV</span>
                </a>
            </div>
        </div>

        <div class="p-5 sm:p-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-sky-50 to-white p-5">
                    <p class="text-xs font-semibold text-slate-500">إجمالي الردود</p>
                    <p class="text-3xl font-black text-sky-700 mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-indigo-50 to-white p-5">
                    <p class="text-xs font-semibold text-slate-500">هذا الشهر</p>
                    <p class="text-3xl font-black text-indigo-700 mt-1">{{ number_format($stats['this_month']) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-emerald-50 to-white p-5">
                    <p class="text-xs font-semibold text-slate-500">خصومات ممنوحة</p>
                    <p class="text-3xl font-black text-emerald-700 mt-1">{{ number_format($stats['rewarded']) }}</p>
                </div>
            </div>

            @if($heardFromBreakdown->count() > 0)
                <div class="rounded-2xl border border-slate-200 p-5">
                    <h2 class="text-sm font-bold text-slate-700 mb-3">عرفونا منين؟</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($heardFromBreakdown as $key => $total)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $heardFromOptions[$key] ?? $key }}
                                <span class="px-1.5 rounded-full bg-white text-slate-900">{{ $total }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">بحث (اسم / بريد / هاتف)</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/30">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الكورس</label>
                    <select name="course_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/30">
                        <option value="">الكل</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">المحافظة</label>
                    <select name="governorate" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/30">
                        <option value="">الكل</option>
                        @foreach($governorates as $key => $label)
                            <option value="{{ $key }}" @selected(request('governorate') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button class="flex-1 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold">تصفية</button>
                    @if(request()->hasAny(['search', 'course_id', 'governorate', 'heard_from']))
                        <a href="{{ route('admin.marketing-customer-surveys.index') }}"
                           class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50">مسح</a>
                    @endif
                </div>
            </form>

            @if($surveys->count() === 0)
                <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center text-slate-500">
                    <i class="fas fa-clipboard-question text-4xl text-slate-300 mb-3 block"></i>
                    <p>لا توجد ردود بعد. شارك رابط الفورم مع عملائك.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-slate-500 text-xs">
                                <th class="px-4 py-3 text-right font-semibold">العميل</th>
                                <th class="px-4 py-3 text-right font-semibold">الكورس</th>
                                <th class="px-4 py-3 text-right font-semibold">المحافظة / الوظيفة</th>
                                <th class="px-4 py-3 text-right font-semibold">عرفنا من</th>
                                <th class="px-4 py-3 text-right font-semibold">الخصم</th>
                                <th class="px-4 py-3 text-right font-semibold">التاريخ</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($surveys as $survey)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-slate-900">{{ $survey->name }}</p>
                                        <p class="text-xs text-slate-500" dir="ltr">{{ $survey->email }}</p>
                                        @if($survey->phone)
                                            <p class="text-xs text-slate-400" dir="ltr">{{ $survey->phone }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 max-w-[220px]">
                                        <span class="line-clamp-2">{{ $survey->course->title ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">
                                        <p>{{ $survey->governorate_label }}</p>
                                        <p class="text-xs text-slate-500">{{ $survey->job_label }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                            {{ $survey->heard_from_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($survey->rewardCoupon)
                                            <span class="block font-mono text-xs font-bold text-emerald-700">{{ $survey->rewardCoupon->code }}</span>
                                            <span class="text-xs {{ $survey->rewardCoupon->used_count > 0 ? 'text-slate-400' : 'text-emerald-600' }}">
                                                {{ $survey->rewardCoupon->used_count > 0 ? 'مُستخدم' : 'متاح' }}
                                                · {{ (int) $survey->rewardCoupon->discount_value }}%
                                            </span>
                                        @else
                                            <span class="text-xs text-amber-600 font-semibold">لم يُمنح</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                                        {{ $survey->created_at?->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-left whitespace-nowrap">
                                        <a href="{{ route('admin.marketing-customer-surveys.show', $survey) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-bold">
                                            <i class="fas fa-eye"></i>
                                            التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($surveys->hasPages())
                    <div>{{ $surveys->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
