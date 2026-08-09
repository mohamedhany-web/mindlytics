@extends('layouts.admin')

@section('title', 'هوية الشهادات')
@section('header', 'هوية الشهادات')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">هوية الشهادات</h1>
                <p class="text-sm text-gray-600 mt-1">ارفع لوجو الأكاديمية، الإمضاء، والختم — تُحفظ وتُطبَّق تلقائيًا على كل شهادة جديدة.</p>
            </div>
            <a href="{{ route('admin.certificates.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                <i class="fas fa-arrow-right"></i>
                رجوع للشهادات
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc mr-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.certificates.branding.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 space-y-4">
            <h2 class="text-lg font-bold text-gray-900">بيانات الأكاديمية</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">اسم الأكاديمية</label>
                    <input type="text" name="academy_name" value="{{ old('academy_name', $branding->academy_name) }}" required
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">الشعار النصي</label>
                    <input type="text" name="academy_tagline" value="{{ old('academy_tagline', $branding->academy_tagline) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">الرقم الضريبي</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $branding->tax_number ?: '774-128-949') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl font-mono" dir="ltr">
                    <p class="text-xs text-slate-500 mt-1">يظهر على الختم الإلكتروني الرسمي لكل شهادة.</p>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2.5">
                        <input type="hidden" name="stamp_enabled" value="0">
                        <input type="checkbox" name="stamp_enabled" value="1" class="rounded border-gray-300 text-emerald-600"
                               @checked(old('stamp_enabled', $branding->stamp_enabled ?? true))>
                        تفعيل الختم الإلكتروني على الشهادات
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">اسم صاحب الإمضاء</label>
                    <input type="text" name="signature_name" value="{{ old('signature_name', $branding->signature_name) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">المسمى الوظيفي</label>
                    <input type="text" name="signature_title" value="{{ old('signature_title', $branding->signature_title) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">نص الشريط على الختم</label>
                    <input type="text" name="seal_label" value="{{ old('seal_label', $branding->seal_label) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">سنة التأسيس (على الختم)</label>
                    <input type="text" name="seal_since" value="{{ old('seal_since', $branding->seal_since) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">القالب الافتراضي</label>
                    <select name="default_template" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                        @foreach($templates as $key => $tpl)
                            <option value="{{ $key }}" @selected(old('default_template', $branding->default_template) === $key)>
                                {{ $tpl['name'] }} — {{ $tpl['description'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                'logo' => ['label' => 'لوجو الأكاديمية', 'field' => 'logo_path', 'url' => $branding->logoUrl(), 'hint' => 'PNG شفاف مفضّل'],
                'signature' => ['label' => 'الإمضاء', 'field' => 'signature_path', 'url' => $branding->signatureUrl(), 'hint' => 'صورة الإمضاء'],
                'stamp' => ['label' => 'صورة ختم بديلة (اختياري)', 'field' => 'stamp_path', 'url' => $branding->stampUrl(), 'hint' => 'لو رفعت صورة تُستخدم بدل الختم الإلكتروني التلقائي'],
            ] as $input => $meta)
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5 space-y-3">
                    <h3 class="font-bold text-gray-900">{{ $meta['label'] }}</h3>
                    <p class="text-xs text-gray-500">{{ $meta['hint'] }}</p>
                    <div class="h-36 rounded-xl border border-dashed border-gray-300 bg-slate-50 flex items-center justify-center overflow-hidden">
                        @if($meta['url'])
                            <img src="{{ $meta['url'] }}" alt="{{ $meta['label'] }}" class="max-h-32 max-w-full object-contain">
                        @else
                            <span class="text-sm text-gray-400">لا توجد صورة</span>
                        @endif
                    </div>
                    <input type="file" name="{{ $input }}" accept="image/*"
                           class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-sky-50 file:text-sky-700">
                    @if($meta['url'])
                        <label class="inline-flex items-center gap-2 text-xs text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_{{ $input }}" value="1" class="rounded">
                            حذف الصورة الحالية
                        </label>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold">
                <i class="fas fa-save"></i>
                حفظ الهوية
            </button>
        </div>
    </form>
</div>
@endsection
