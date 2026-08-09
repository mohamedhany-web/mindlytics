{{-- ختم حبر دائري احترافي — نمط الأختام الرسمية المصرية --}}
@php
    $branding = $branding ?? \App\Models\CertificateBranding::current();
    $academyEn = $stampAcademyNameEn
        ?? (filled($branding->academy_name) ? $branding->academy_name : 'Mindlytics Academy');
    $academyAr = $stampAcademyNameAr
        ?? (filled($branding->academy_tagline) && preg_match('/\p{Arabic}/u', (string) $branding->academy_tagline)
            ? $branding->academy_tagline
            : 'أكاديمية مايندليتكس');
    $taxNumber = $stampTaxNumber ?? ($branding->tax_number ?: '774-128-949');
    $location = $stampLocation ?? 'Cairo · Egypt';
    $stampId = 'seal-'.($templateDomId ?? 'cert').'-'.substr(md5($academyEn.$taxNumber), 0, 8);
    $showStamp = ($branding->stamp_enabled ?? true) !== false;
    $ink = '#0c3d8c';
    $inkSoft = '#1a5bb8';
@endphp
@if($showStamp)
<div class="official-ink-stamp" aria-label="Official academy stamp">
  @if($branding->stampUrl())
    <img src="{{ $branding->stampUrl() }}" alt="ختم الأكاديمية" class="official-ink-stamp__img">
  @else
    <svg class="official-ink-stamp__svg" viewBox="0 0 320 320" xmlns="http://www.w3.org/2000/svg" role="img">
      <defs>
        {{-- قوس علوي للنص العربي (من اليسار لليمين فوق) --}}
        <path id="{{ $stampId }}-top" d="M 52,160 A 108,108 0 0 1 268,160" fill="none"/>
        {{-- قوس سفلي للنص الإنجليزي --}}
        <path id="{{ $stampId }}-bottom" d="M 268,168 A 108,108 0 0 1 52,168" fill="none"/>
        <filter id="{{ $stampId }}-ink" x="-8%" y="-8%" width="116%" height="116%">
          <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" result="noise" seed="7"/>
          <feDisplacementMap in="SourceGraphic" in2="noise" scale="0.55" xChannelSelector="R" yChannelSelector="G"/>
        </filter>
      </defs>

      <g filter="url(#{{ $stampId }}-ink)" fill="none" stroke="{{ $ink }}" stroke-linecap="round">
        {{-- الحلقات الخارجية السميكة --}}
        <circle cx="160" cy="160" r="148" stroke-width="7"/>
        <circle cx="160" cy="160" r="138" stroke-width="2.2"/>
        <circle cx="160" cy="160" r="132" stroke-width="5.5"/>
        {{-- حلقة منقّطة زخرفية --}}
        <circle cx="160" cy="160" r="122" stroke-width="1.8" stroke-dasharray="2.2 5.5" stroke="{{ $inkSoft }}"/>
        {{-- الحلقة الداخلية حول المحتوى --}}
        <circle cx="160" cy="160" r="78" stroke-width="2.8"/>
      </g>

      {{-- فواصل زخرفية يمين ويسار --}}
      <g fill="{{ $ink }}" filter="url(#{{ $stampId }}-ink)">
        <circle cx="38" cy="160" r="3.2"/>
        <circle cx="282" cy="160" r="3.2"/>
        <path transform="translate(28,160) scale(0.42) translate(-12,-12)"
              d="M12 1.8l3.1 6.4 7 .8-5.2 4.8 1.5 6.9L12 17.2 5.6 20.7l1.5-6.9-5.2-4.8 7-.8z"/>
        <path transform="translate(292,160) scale(0.42) translate(-12,-12)"
              d="M12 1.8l3.1 6.4 7 .8-5.2 4.8 1.5 6.9L12 17.2 5.6 20.7l1.5-6.9-5.2-4.8 7-.8z"/>
      </g>

      {{-- الاسم العربي أعلى القوس --}}
      <text fill="{{ $ink }}" font-family="'Segoe UI', Tahoma, Arial, sans-serif" font-size="17" font-weight="700"
            letter-spacing="0.4" direction="rtl"
            filter="url(#{{ $stampId }}-ink)">
        <textPath href="#{{ $stampId }}-top" startOffset="50%" text-anchor="middle">{{ $academyAr }}</textPath>
      </text>

      {{-- الاسم الإنجليزي أسفل القوس --}}
      <text fill="{{ $ink }}" font-family="Arial, Helvetica, sans-serif" font-size="14.5" font-weight="700"
            letter-spacing="1.6" filter="url(#{{ $stampId }}-ink)">
        <textPath href="#{{ $stampId }}-bottom" startOffset="50%" text-anchor="middle">{{ strtoupper($academyEn) }}</textPath>
      </text>

      {{-- مركز الختم --}}
      <g fill="{{ $ink }}" text-anchor="middle" filter="url(#{{ $stampId }}-ink)">
        <text x="160" y="138" font-family="'Segoe UI', Tahoma, Arial, sans-serif" font-size="12.5" font-weight="700">الرقم الضريبي</text>
        <text x="160" y="162" font-family="Arial, Helvetica, sans-serif" font-size="16.5" font-weight="800" letter-spacing="0.8">{{ $taxNumber }}</text>
        <line x1="108" y1="172" x2="212" y2="172" stroke="{{ $ink }}" stroke-width="1.6"/>
        <text x="160" y="192" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700" letter-spacing="0.6">{{ strtoupper($location) }}</text>
      </g>
    </svg>
  @endif
</div>
<style>
.official-ink-stamp{
  width:118px;height:118px;position:relative;flex-shrink:0;
  transform:rotate(-8deg);
  margin:0 auto 4px;
}
.official-ink-stamp__svg,.official-ink-stamp__img{
  width:100%;height:100%;display:block;object-fit:contain;
  mix-blend-mode:multiply;
  opacity:.88;
  filter:contrast(1.05);
}
@media print{
  .official-ink-stamp{transform:rotate(-8deg)}
  .official-ink-stamp__svg,.official-ink-stamp__img{mix-blend-mode:multiply;opacity:.9}
}
</style>
@endif
