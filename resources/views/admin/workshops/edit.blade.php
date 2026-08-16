@extends('layouts.admin')

@section('title', 'تعديل الورشة')

@section('content')
<div class="p-6 lg:p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-people-arrows text-blue-600"></i>
            <span>تعديل الورشة: {{ $workshop->title }}</span>
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            يمكنك تحديث بيانات الورشة وعدد المقاعد وحالة النشر في أي وقت.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ route('admin.workshops.update', $workshop) }}" class="px-5 py-6 sm:px-8 lg:px-10 space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="title" class="block text-sm font-semibold text-slate-800">
                        عنوان الورشة
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $workshop->title) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500"
                           required>
                </div>

                <div class="space-y-2">
                    <label for="location" class="block text-sm font-semibold text-slate-800">
                        عنوان / مكان الورشة
                    </label>
                    <input type="text" id="location" name="location" value="{{ old('location', $workshop->location) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500"
                           placeholder="مثال: مقر Mindlytics - مدينة نصر، أو أونلاين عبر Zoom">
                    <p class="text-xs text-slate-500">يظهر هذا العنوان للطلاب، خاصة في حالة الحضور أوفلاين في المكان.</p>
                </div>

                <div class="space-y-2">
                    <label for="whatsapp_group_link" class="block text-sm font-semibold text-slate-800">
                        رابط جروب واتساب
                    </label>
                    <input type="url" id="whatsapp_group_link" name="whatsapp_group_link"
                           value="{{ old('whatsapp_group_link', $workshop->whatsapp_group_link ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500"
                           placeholder="https://chat.whatsapp.com/..." dir="ltr">
                    <p class="text-xs text-slate-500">بعد ما الطالب يقدّم طلب التسجيل تظهر له رسالة بزر «انضم إلى جروب الواتساب» يفتح هذا الرابط مباشرة.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-800">
                        حالة النشر
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                               {{ old('is_active', $workshop->is_active) ? 'checked' : '' }}>
                        <span>نشطة (يمكن للطلاب التسجيل)</span>
                    </label>
                </div>

                <div class="space-y-2">
                    <label for="mode" class="block text-sm font-semibold text-slate-800">
                        نوع حضور الورشة
                    </label>
                    <select id="mode" name="mode"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                        <option value="online" {{ old('mode', $workshop->mode) === 'online' ? 'selected' : '' }}>أونلاين (عن بُعد)</option>
                        <option value="offline" {{ old('mode', $workshop->mode) === 'offline' ? 'selected' : '' }}>في المكان (أوفلاين)</option>
                        <option value="both" {{ old('mode', $workshop->mode) === 'both' ? 'selected' : '' }}>إتاحة الاختيار (أونلاين أو أوفلاين)</option>
                    </select>
                    <p class="text-xs text-slate-500">سيتم إظهار هذا النوع للطلاب أثناء التسجيل ويمكنهم اختيار طريقة الحضور إن كانت الورشة تدعم الطريقتين.</p>
                </div>

                <div class="space-y-2">
                    <label for="starts_at" class="block text-sm font-semibold text-slate-800">
                        تاريخ ووقت البداية
                    </label>
                    <input type="datetime-local" id="starts_at" name="starts_at"
                           value="{{ old('starts_at', optional($workshop->starts_at)->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                </div>

                <div class="space-y-2">
                    <label for="ends_at" class="block text-sm font-semibold text-slate-800">
                        تاريخ ووقت النهاية
                    </label>
                    <input type="datetime-local" id="ends_at" name="ends_at"
                           value="{{ old('ends_at', optional($workshop->ends_at)->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                </div>

                <div class="space-y-2">
                    <label for="max_seats" class="block text-sm font-semibold text-slate-800">
                        الحد الأقصى لعدد المقاعد
                    </label>
                    <input type="number" id="max_seats" name="max_seats" value="{{ old('max_seats', $workshop->max_seats) }}" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                    <p class="text-xs text-slate-500">ضع 0 لجعل المقاعد غير محدودة.</p>
                </div>

                <div class="space-y-2">
                    <label for="seats_online" class="block text-sm font-semibold text-slate-800">
                        عدد مقاعد الحضور أونلاين
                    </label>
                    <input type="number" id="seats_online" name="seats_online" value="{{ old('seats_online', $workshop->seats_online) }}" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                    <p class="text-xs text-slate-500">اتركه 0 إذا لم يكن هناك حد محدد للحضور أونلاين.</p>
                </div>

                <div class="space-y-2">
                    <label for="seats_offline" class="block text-sm font-semibold text-slate-800">
                        عدد مقاعد الحضور أوفلاين (في المكان)
                    </label>
                    <input type="number" id="seats_offline" name="seats_offline" value="{{ old('seats_offline', $workshop->seats_offline) }}" min="0"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                    <p class="text-xs text-slate-500">ضع هنا عدد الكراسي المتاحة في القاعة للحضور في المكان.</p>
                </div>
            </div>

            <div class="space-y-2">
                <label for="description" class="block text-sm font-semibold text-slate-800">
                    وصف الورشة وتفاصيلها
                </label>
                <textarea id="description" name="description" rows="5"
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500"
                          placeholder="اكتب هنا محاور الورشة، المتطلبات، الأدوات التي سيحتاجها الطالب، وسطر عن المدرب.">{{ old('description', $workshop->description) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.workshops.show', $workshop) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة لتفاصيل الورشة</span>
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 px-6 py-2.5 text-sm font-semibold text-white shadow-lg hover:shadow-xl">
                    <i class="fas fa-save"></i>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

