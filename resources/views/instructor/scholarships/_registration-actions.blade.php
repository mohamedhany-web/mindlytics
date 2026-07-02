@php $registration = $registration ?? null; @endphp
@if($registration)
    <div class="flex flex-wrap gap-2 justify-end">
        @if($registration->status === \App\Models\ScholarshipRegistration::STATUS_REGISTERED)
            <form method="POST" action="{{ route('instructor.scholarships.registrations.activate', $registration) }}">@csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-check"></i>
                    <span>تفعيل</span>
                </button>
            </form>
            <form method="POST" action="{{ route('instructor.scholarships.registrations.reject', $registration) }}">@csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-times"></i>
                    <span>رفض</span>
                </button>
            </form>
        @elseif($registration->status === \App\Models\ScholarshipRegistration::STATUS_ACTIVATED)
            <form method="POST" action="{{ route('instructor.scholarships.registrations.deactivate', $registration) }}">@csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-ban"></i>
                    <span>إلغاء التفعيل</span>
                </button>
            </form>
        @elseif($registration->status === \App\Models\ScholarshipRegistration::STATUS_DEACTIVATED)
            <form method="POST" action="{{ route('instructor.scholarships.registrations.activate', $registration) }}">@csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-redo"></i>
                    <span>إعادة التفعيل</span>
                </button>
            </form>
        @endif
    </div>
@endif
