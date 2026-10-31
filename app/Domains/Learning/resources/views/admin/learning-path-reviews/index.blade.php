@extends('layouts.admin')

@section('title', 'تقييمات المسارات التعليمية')
@section('header', 'تقييمات المسارات التعليمية')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تقييمات المسارات التعليمية</h1>
            <p class="text-gray-600 mt-1">مراجعة وقبول/رفض تقييمات المسارات قبل ظهورها للزوار</p>
        </div>
    </div>

    @if(isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="text-sm text-gray-600">إجمالي المراجعات</div>
                <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="text-sm text-gray-600">متوسط التقييم</div>
                <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['average_rating'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="text-sm text-gray-600">المقبولة</div>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['approved'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="text-sm text-gray-600">المعلقة</div>
                <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['pending'] ?? 0 }}</div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <form method="get" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                <select name="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">الكل</option>
                    <option value="pending" @selected(request('status') === 'pending')>معلقة</option>
                    <option value="approved" @selected(request('status') === 'approved')>مقبولة</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>مرفوضة</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">التقييم</label>
                <select name="rating" class="rounded-lg border-gray-300 text-sm">
                    <option value="">الكل</option>
                    @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}" @selected((string) request('rating') === (string) $i)>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 text-sm" placeholder="اسم الطالب أو المسار أو نص التعليق">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">تصفية</button>
            <a href="{{ route('admin.learning-path-reviews.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">إعادة تعيين</a>
        </form>
    </div>

    @if(isset($reviews) && $reviews->count() > 0)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المسار</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التقييم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التعليق</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($reviews as $review)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $review->user->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $review->learningPath->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="mr-2 text-xs text-gray-600">({{ $review->rating }})</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $review->comment ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $status = $review->status ?? ($review->is_approved ? 'approved' : 'pending'); @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($status == 'approved' || $review->is_approved) bg-green-100 text-green-800
                                        @elseif($status == 'pending' || !$review->is_approved) bg-yellow-100 text-yellow-800
                                        @elseif($status == 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($status == 'approved' || $review->is_approved) مقبولة
                                        @elseif($status == 'pending' || !$review->is_approved) معلقة
                                        @elseif($status == 'rejected') مرفوضة
                                        @else {{ $status }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $review->created_at?->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    <a href="{{ route('admin.learning-path-reviews.show', $review) }}" class="text-sky-600 hover:text-sky-900">عرض</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reviews->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <i class="fas fa-star-half-alt text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-600 text-lg">لا توجد مراجعات</p>
        </div>
    @endif
</div>
@endsection

