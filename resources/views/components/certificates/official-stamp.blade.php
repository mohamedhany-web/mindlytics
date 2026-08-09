{{-- ختم إلكتروني بطابع حبر رسمي — اسم الأكاديمية + الرقم الضريبي --}}
@php
    $branding = $branding ?? \App\Models\CertificateBranding::current();
    $academyName = $stampAcademyName ?? ($branding->academy_name ?: 'Mindlytics Academy');
    $taxNumber = $stampTaxNumber ?? ($branding->tax_number ?: '774-128-949');
    $stampId = 'ink-stamp-'.($templateDomId ?? 'cert').'-'.substr(md5($academyName.$taxNumber.uniqid('', true)), 0, 8);
    $showStamp = ($branding->stamp_enabled ?? true) !== false;
@endphp
@if($showStamp)
<div class="official-ink-stamp" aria-label="Official academy stamp">
  @if($branding->stampUrl())
    <img src="{{ $branding->stampUrl() }}" alt="ختم الأكاديمية" class="official-ink-stamp__img">
  @else
    <svg class="official-ink-stamp__svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" role="img">
      <defs>
        <filter id="{{ $stampId }}-ink" x="-20%" y="-20%" width="140%" height="140%">
          <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" result="noise" seed="7"/>
          <feDisplacementMap in="SourceGraphic" in2="noise" scale="1.4" xChannelSelector="R" yChannelSelector="G"/>
        </filter>
      </defs>
      <g filter="url(#{{ $stampId }}-ink)" fill="none" stroke="#9b1c1c" stroke-opacity="0.88">
        <circle cx="100" cy="100" r="92" stroke-width="3.2"/>
        <circle cx="100" cy="100" r="84" stroke-width="1.4"/>
        <circle cx="100" cy="100" r="52" stroke-width="2"/>
        <circle cx="100" cy="100" r="46" stroke-width="1"/>
      </g>
      <defs>
        <path id="{{ $stampId }}-top" d="M 28 100 A 72 72 0 0 1 172 100"/>
        <path id="{{ $stampId }}-bottom" d="M 168 108 A 68 68 0 0 1 32 108"/>
      </defs>
      <text fill="#9b1c1c" fill-opacity="0.9" font-family="Georgia, 'Times New Roman', serif" font-size="13" font-weight="700" letter-spacing="1.5">
        <textPath href="#{{ $stampId }}-top" startOffset="50%" text-anchor="middle">{{ strtoupper($academyName) }}</textPath>
      </text>
      <text fill="#9b1c1c" fill-opacity="0.88" font-family="ui-monospace, Menlo, Consolas, monospace" font-size="11" font-weight="700" letter-spacing="0.5">
        <textPath href="#{{ $stampId }}-bottom" startOffset="50%" text-anchor="middle">TAX {{ $taxNumber }}</textPath>
      </text>
      <text x="100" y="92" text-anchor="middle" fill="#9b1c1c" fill-opacity="0.92"
            font-family="Georgia, serif" font-size="11" font-weight="700" letter-spacing="2">OFFICIAL</text>
      <text x="100" y="110" text-anchor="middle" fill="#9b1c1c" fill-opacity="0.92"
            font-family="Georgia, serif" font-size="10" font-weight="700" letter-spacing="1.5">SEAL</text>
      <text x="100" y="128" text-anchor="middle" fill="#9b1c1c" fill-opacity="0.85"
            font-family="ui-monospace, Menlo, Consolas, monospace" font-size="9" font-weight="700">{{ $taxNumber }}</text>
    </svg>
  @endif
</div>
<style>
.official-ink-stamp{
  width:90px;height:90px;position:relative;
  filter:drop-shadow(0 2px 2px rgba(120,20,20,.18));
  transform:rotate(-8deg);
}
.official-ink-stamp__svg,.official-ink-stamp__img{
  width:100%;height:100%;display:block;object-fit:contain;
  mix-blend-mode:multiply;
}
</style>
@endif
