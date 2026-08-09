{{-- ختم إلكتروني بطابع حبر أزرق — على نمط ختم الشركات الدائري --}}
@php
    $branding = $branding ?? \App\Models\CertificateBranding::current();
    $academyEn = $stampAcademyNameEn
        ?? (filled($branding->academy_name) ? $branding->academy_name : 'Mindlytics Academy');
    $academyAr = $stampAcademyNameAr
        ?? (filled($branding->academy_tagline) && preg_match('/\p{Arabic}/u', (string) $branding->academy_tagline)
            ? $branding->academy_tagline
            : 'أكاديمية مايندليتكس');
    $taxNumber = $stampTaxNumber ?? ($branding->tax_number ?: '774-128-949');
    $location = $stampLocation ?? 'Cairo - Egypt';
    $stampId = 'seal-'.($templateDomId ?? 'cert').'-'.substr(md5($academyEn.$taxNumber), 0, 8);
    $showStamp = ($branding->stamp_enabled ?? true) !== false;
    $ink = '#1a4f9c';
@endphp
@if($showStamp)
<div class="official-ink-stamp" aria-label="Official academy stamp">
  @if($branding->stampUrl())
    <img src="{{ $branding->stampUrl() }}" alt="ختم الأكاديمية" class="official-ink-stamp__img">
  @else
    <svg class="official-ink-stamp__svg" viewBox="0 0 220 220" xmlns="http://www.w3.org/2000/svg" role="img">
      <defs>
        <path id="{{ $stampId }}-top" d="M 38,110 A 72,72 0 0 1 182,110" fill="none"/>
        <path id="{{ $stampId }}-bottom" d="M 182,118 A 72,72 0 0 1 38,118" fill="none"/>
      </defs>

      {{-- حلقات الختم --}}
      <circle cx="110" cy="110" r="104" fill="none" stroke="{{ $ink }}" stroke-width="2.4"/>
      <circle cx="110" cy="110" r="98" fill="none" stroke="{{ $ink }}" stroke-width="1.5"/>
      <circle cx="110" cy="110" r="62" fill="none" stroke="{{ $ink }}" stroke-width="1.6"/>

      {{-- نجوم فاصلة يمين ويسار --}}
      <g fill="{{ $ink }}">
        <path transform="translate(18,110) scale(0.55) translate(-12,-12)"
              d="M12 2.2l2.9 6.1 6.7.7-5 4.6 1.4 6.6L12 16.8 6 20.2l1.4-6.6-5-4.6 6.7-.7z"/>
        <path transform="translate(202,110) scale(0.55) translate(-12,-12)"
              d="M12 2.2l2.9 6.1 6.7.7-5 4.6 1.4 6.6L12 16.8 6 20.2l1.4-6.6-5-4.6 6.7-.7z"/>
      </g>

      {{-- الاسم العربي أعلى القوس --}}
      <text fill="{{ $ink }}" font-family="Tahoma, 'Segoe UI', Arial, sans-serif" font-size="13.5" font-weight="700" letter-spacing="0.5">
        <textPath href="#{{ $stampId }}-top" startOffset="50%" text-anchor="middle">{{ $academyAr }}</textPath>
      </text>

      {{-- الاسم الإنجليزي أسفل القوس --}}
      <text fill="{{ $ink }}" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700" letter-spacing="0.8">
        <textPath href="#{{ $stampId }}-bottom" startOffset="50%" text-anchor="middle">{{ $academyEn }}</textPath>
      </text>

      {{-- المركز: الرقم الضريبي والموقع --}}
      <text x="110" y="102" text-anchor="middle" fill="{{ $ink }}"
            font-family="Arial, Helvetica, sans-serif" font-size="11.5" font-weight="700">Tax No.: {{ $taxNumber }}</text>
      <text x="110" y="124" text-anchor="middle" fill="{{ $ink }}"
            font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700">{{ $location }}</text>
    </svg>
  @endif
</div>
<style>
.official-ink-stamp{
  width:96px;height:96px;position:relative;
  transform:rotate(-6deg);
}
.official-ink-stamp__svg,.official-ink-stamp__img{
  width:100%;height:100%;display:block;object-fit:contain;
  mix-blend-mode:multiply;
  opacity:.92;
}
</style>
@endif
