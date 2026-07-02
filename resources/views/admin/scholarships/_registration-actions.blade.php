@php $registration = $registration ?? null; @endphp
@if($registration)
    <div class="flex items-center justify-center gap-2">
        @if($registration->status === \App\Models\ScholarshipRegistration::STATUS_REGISTERED)
            <form method="POST" action="{{ route('admin.scholarships.registrations.activate', $registration) }}">@csrf
                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md" title="تفعيل">
                    <i class="fas fa-check text-sm"></i>
                </button>
            </form>
            <form method="POST" action="{{ route('admin.scholarships.registrations.reject', $registration) }}">@csrf
                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md" title="رفض">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </form>
        @elseif($registration->status === \App\Models\ScholarshipRegistration::STATUS_ACTIVATED)
            <form method="POST" action="{{ route('admin.scholarships.registrations.deactivate', $registration) }}">@csrf
                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md" title="إلغاء التفعيل">
                    <i class="fas fa-ban text-sm"></i>
                </button>
            </form>
        @elseif($registration->status === \App\Models\ScholarshipRegistration::STATUS_DEACTIVATED)
            <form method="POST" action="{{ route('admin.scholarships.registrations.activate', $registration) }}">@csrf
                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md" title="إعادة التفعيل">
                    <i class="fas fa-redo text-sm"></i>
                </button>
            </form>
        @endif
    </div>
@endif
