@extends('layouts.admin')

@section('title', 'تعديل قالب — ' . $template->name)
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'تعديل: ' . $template->name,
        'subtitle' => $template->language . ' · ' . $template->categoryLabel(),
        'icon' => 'fas fa-edit',
        'actions' => '<a href="' . route('admin.whatsapp.templates.show', $template) . '" class="' . $waBtnSecondary . '">عرض</a>',
    ])

    <form method="POST" action="{{ route('admin.whatsapp.templates.update', $template) }}">
        @csrf @method('PUT')
        <section class="{{ $waSectionClass }} p-5 sm:p-6 space-y-6">
            @include('admin.whatsapp.templates._form', ['template' => $template])

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" name="submit_now" value="0" class="{{ $waBtnSecondary }}"><i class="fas fa-save"></i> حفظ</button>
                <button type="submit" name="submit_now" value="1" class="{{ $waBtnPrimary }}"><i class="fab fa-meta"></i> حفظ وإرسال إلى Meta</button>
            </div>
        </section>
    </form>
</div>
@endsection
