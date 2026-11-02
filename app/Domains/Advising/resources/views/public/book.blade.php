@extends('layouts.public')

@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $goals = \App\Domains\Advising\Models\AdvisingBooking::GOALS;
    $levels = \App\Domains\Advising\Models\AdvisingBooking::LEVELS;
    $booked = session('advising_booked');
@endphp

@section('title', $t('احجز جلسة إرشاد مهني مجانية', 'Book a free career guidance session').' - Mindlytics')
@section('meta_description', $t('جلسة مجانية مع مرشد من Mindlytics تساعدك تعرف احتياجاتك، وتختار المسار المناسب، وتدخل سوق العمل في البرمجيات.', 'A free session with a Mindlytics advisor to understand your needs, choose the right path and get into the software job market.'))

@section('content')
@include('components.site.page-head', [
    'eyebrow' => $t('جلسة إرشاد مهني مجانية', 'Free career guidance session'),
    'title' => $t('خلّينا نساعدك تعرف تبدأ منين', 'Let us help you find where to start'),
    'subtitle' => $t('جلسة قصيرة مع مرشد من الأكاديمية: نفهم هدفك ومستواك، ونرشّحلك المسار المناسب، ونرسم معاك خطة دخولك لسوق العمل.', 'A short session with one of our advisors: we understand your goal and level, recommend the right path, and map your way into the job market.'),
    'crumbs' => [['label' => $t('الإرشاد المهني', 'Career guidance')]],
])

<section class="h-section">
    <div class="h-wrap" style="max-width: 760px">
        @if($booked)
            <div class="h-adv-done" role="status">
                <b>{{ $t('اتحجزت جلستك', 'Your session is booked') }}</b>
                <p>{{ $booked['date'] }} — {{ $booked['time'] }} — {{ $booked['mode'] }}</p>
                <p>{{ $t('هنكلمك على واتساب قبل الجلسة نأكّد الميعاد ونبعتلك اللينك أو العنوان.', 'We will message you on WhatsApp before the session to confirm and send the link or address.') }}</p>
            </div>
        @elseif($days === [])
            <div class="h-empty">
                <span>{{ $t('مفيش مواعيد متاحة للحجز دلوقتي. كلّمنا ونحدد معاك ميعاد.', 'No times are open right now. Contact us and we will set one with you.') }}</span>
                <a href="{{ route('public.contact') }}" class="h-btn h-btn-outline">{{ $t('تواصل معنا', 'Contact us') }}</a>
            </div>
        @else
            <form method="post" action="{{ route('advising.store') }}" style="display: flex; flex-direction: column; gap: 22px">
                @csrf
                <div style="position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0)" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                @if($errors->any())<p class="h-field-error" role="alert" style="margin: 0">{{ $errors->first() }}</p>@endif

                <fieldset class="h-adv-set">
                    <legend>{{ $t('إيه اللي عايز توصله؟', 'What do you want to achieve?') }}</legend>
                    <div class="h-adv-options">
                        @foreach($goals as $k => $l)
                            <label class="h-adv-option"><input type="radio" name="goal" value="{{ $k }}" required @checked(old('goal') === $k)><span>{{ $l[$siteAr ? 0 : 1] }}</span></label>
                        @endforeach
                    </div>
                </fieldset>
                <fieldset class="h-adv-set">
                    <legend>{{ $t('مستواك دلوقتي', 'Your current level') }}</legend>
                    <div class="h-adv-options h-adv-options-row">
                        @foreach($levels as $k => $l)
                            <label class="h-adv-option"><input type="radio" name="level" value="{{ $k }}" @checked(old('level') === $k)><span>{{ $l[$siteAr ? 0 : 1] }}</span></label>
                        @endforeach
                    </div>
                </fieldset>
                <label class="h-field">{{ $t('الميعاد', 'Time') }}
                    <select name="slot" required>
                        <option value="">{{ $t('اختار ميعاد', 'Pick a time') }}</option>
                        @foreach($days as $day)
                            <optgroup label="{{ $day['label'] }} {{ $day['short'] }}">
                                @foreach($day['slots'] as $s)<option value="{{ $s['key'] }}" @selected(old('slot') === $s['key'])>{{ $s['time_label'] }} — {{ $s['mode_label'] }}</option>@endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </label>
                <label class="h-field">{{ $t('المجال أو البرنامج اللي مهتم بيه (اختياري)', 'Field or program you are interested in (optional)') }}
                    <input type="text" name="interest" maxlength="255" value="{{ old('interest') }}">
                </label>
                <div class="h-adv-grid">
                    <label class="h-field">{{ $t('الاسم', 'Name') }}<input type="text" name="name" required maxlength="120" autocomplete="name" value="{{ old('name', auth()->user()?->name) }}"></label>
                    <label class="h-field">{{ $t('رقم الموبايل (واتساب)', 'Mobile (WhatsApp)') }}<input type="tel" name="phone" required maxlength="40" autocomplete="tel" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}" value="{{ old('phone', auth()->user()?->phone) }}"></label>
                </div>
                <label class="h-field">{{ $t('البريد الإلكتروني (اختياري)', 'Email (optional)') }}<input type="email" name="email" maxlength="190" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}" value="{{ old('email', auth()->user()?->email) }}"></label>
                <label class="h-field">{{ $t('احكيلنا عن وضعك (اختياري)', 'Tell us about your situation (optional)') }}<textarea name="notes" rows="3" maxlength="2000">{{ old('notes') }}</textarea></label>
                <button type="submit" class="h-btn h-btn-primary h-btn-lg">{{ $t('احجز الجلسة', 'Book the session') }}</button>
            </form>
        @endif
    </div>
</section>
@endsection
