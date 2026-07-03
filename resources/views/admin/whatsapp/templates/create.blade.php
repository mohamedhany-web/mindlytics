@extends('layouts.admin')

@section('title', 'إنشاء قالب واتساب')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'إنشاء قالب Meta جديد',
        'subtitle' => 'صمّم القالب هنا ثم أرسله إلى Meta للمراجعة — لا حاجة لفتح WhatsApp Manager.',
        'icon' => 'fas fa-plus-circle',
        'actions' => '<a href="' . route('admin.whatsapp.templates.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> كل القوالب</a>',
    ])

    <form method="POST" action="{{ route('admin.whatsapp.templates.store') }}">
        @csrf
        <section class="{{ $waSectionClass }} p-5 sm:p-6 space-y-6">
            @include('admin.whatsapp.templates._form')

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" name="submit_now" value="0" class="{{ $waBtnSecondary }}">
                    <i class="fas fa-save"></i> حفظ كمسودة
                </button>
                <button type="submit" name="submit_now" value="1" class="{{ $waBtnPrimary }}">
                    <i class="fab fa-meta"></i> Submit to Meta
                </button>
            </div>
        </section>
    </form>
</div>
@endsection
