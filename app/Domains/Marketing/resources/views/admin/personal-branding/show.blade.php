@extends('layouts.admin')

@section('title', 'مراجعة الملف التعريفي - ' . ($personal_branding->user->name ?? ''))
@section('header', 'مراجعة الملف التعريفي')

@section('content')
<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 mb-2">
        <a href="{{ route('admin.personal-branding.index') }}" class="text-sky-600 hover:text-sky-700">التسويق الشخصي</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">{{ $personal_branding->user->name ?? 'مدرب' }}</span>
    </nav>

    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-900">الملف التعريفي — {{ $personal_branding->user->name }}</h1>
            <span class="rounded-full px-3 py-1 text-sm font-semibold
                @if($personal_branding->status == 'approved') bg-emerald-100 text-emerald-700
                @elseif($personal_branding->status == 'pending_review') bg-amber-100 text-amber-700
                @elseif($personal_branding->status == 'rejected') bg-rose-100 text-rose-700
                @else bg-slate-100 text-slate-600
                @endif">
                {{ \App\Models\InstructorProfile::statusLabel($personal_branding->status) }}
            </span>
        </div>
        <div class="p-5 sm:p-8 space-y-6">
            <div class="flex flex-wrap gap-4 items-start">
                @if($personal_branding->photo_path)
                    @php $photoPath = str_replace('\\', '/', trim($personal_branding->photo_path)); @endphp
                    <div class="w-28 h-28 rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 relative">
                        <img src="{{ asset('storage/' . $photoPath) }}" alt="صورة المدرب" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-4xl"></i></div>
                    </div>
                @else
                    <div class="w-28 h-28 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-4xl"></i></div>
                @endif
                <div>
                    <p class="text-slate-500 text-sm">البريد: {{ $personal_branding->user->email ?? '—' }}</p>
                    @if(!empty($personal_branding->social_links['linkedin']))
                    <p class="text-slate-500 text-sm mt-1">LinkedIn: <a href="{{ $personal_branding->social_links['linkedin'] }}" target="_blank" rel="noopener" class="text-sky-600 hover:text-sky-700">{{ $personal_branding->social_links['linkedin'] }}</a></p>
                    @endif
                    <p class="text-slate-500 text-sm mt-1">تاريخ التقديم: {{ $personal_branding->submitted_at ? $personal_branding->submitted_at->format('Y-m-d H:i') : '—' }}</p>
                    @if($personal_branding->reviewed_at)
                        <p class="text-slate-500 text-sm">تمت المراجعة: {{ $personal_branding->reviewed_at->format('Y-m-d H:i') }} — {{ $personal_branding->reviewedByUser->name ?? '' }}</p>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-1">العنوان التعريفي</h3>
                <p class="text-slate-900">{{ $personal_branding->headline ?? '—' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-1">النبذة</h3>
                <p class="text-slate-900 whitespace-pre-line">{{ $personal_branding->bio ?? '—' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">الخبرات في المجال</h3>
                @if(count($personal_branding->experience_list) > 0)
                <ul class="space-y-2">
                    @foreach($personal_branding->experience_list as $item)
                    <li class="flex gap-2 text-slate-900">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-bold">•</span>
                        <span>{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-slate-900">{{ $personal_branding->experience ?: '—' }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-600 mb-2">المهارات</h3>
                @if(count($personal_branding->skills_list) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($personal_branding->skills_list as $skill)
                    <span class="inline-flex items-center rounded-xl bg-sky-50 text-sky-800 px-3 py-1.5 text-sm font-medium border border-sky-200">{{ $skill }}</span>
                    @endforeach
                </div>
                @else
                <p class="text-slate-900">{{ $personal_branding->skills ?: '—' }}</p>
                @endif
            </div>
            @if($personal_branding->rejection_reason)
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200">
                <h3 class="text-sm font-semibold text-rose-700 mb-1">سبب الرفض</h3>
                <p class="text-rose-900">{{ $personal_branding->rejection_reason }}</p>
            </div>
            @endif
        </div>
        <div class="px-5 py-6 sm:px-8 border-t border-slate-200 bg-slate-50/80">
            <h3 class="text-sm font-bold text-slate-700 mb-3">إجراءات المراجعة</h3>
            @if($personal_branding->status == 'pending_review')
                <div class="flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('admin.personal-branding.approve', $personal_branding) }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-emerald-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-emerald-700 shadow-sm">موافقة ونشر على الموقع</button>
                    </form>
                    <form method="POST" action="{{ route('admin.personal-branding.reject', $personal_branding) }}" class="inline" x-data="{ open: false }">
                        @csrf
                        <template x-if="!open">
                            <button type="button" @click="open = true" class="rounded-2xl bg-rose-100 text-rose-700 px-5 py-2.5 text-sm font-semibold hover:bg-rose-200 border border-rose-200">رفض</button>
                        </template>
                        <template x-if="open">
                            <div class="flex flex-wrap items-end gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">سبب الرفض (اختياري)</label>
                                    <textarea name="rejection_reason" rows="2" class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64" placeholder="اكتب سبب الرفض للمدرب..."></textarea>
                                </div>
                                <button type="submit" class="rounded-2xl bg-rose-600 text-white px-4 py-2 text-sm font-semibold">تأكيد الرفض</button>
                                <button type="button" @click="open = false" class="rounded-2xl bg-slate-200 text-slate-700 px-4 py-2 text-sm">إلغاء</button>
                            </div>
                        </template>
                    </form>
                </div>
            @elseif(in_array($personal_branding->status, ['approved', 'rejected']))
                <form method="POST" action="{{ route('admin.personal-branding.send-back', $personal_branding) }}" class="inline" onsubmit="return confirm('إعادة هذا الملف إلى قيد المراجعة؟');">
                    @csrf
                    <button type="submit" class="rounded-2xl bg-amber-100 text-amber-800 px-5 py-2.5 text-sm font-semibold hover:bg-amber-200 border border-amber-200">إعادة للمراجعة</button>
                </form>
            @else
                <p class="text-slate-600 text-sm">هذا الملف ما زال <strong>مسودة</strong> ولم يُرسل من المدرب للمراجعة بعد. أزرار الموافقة والرفض تظهر عندما يكون الحالة <strong>قيد المراجعة</strong>.</p>
            @endif
        </div>
    </div>
</div>
@endsection
