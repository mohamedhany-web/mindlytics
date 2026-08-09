{{-- Auto-generated from certification/06-tech-gradient.html — do not edit by hand lightly --}}
@php
    $studentName = $studentName ?? 'Student';
    $courseTitle = $courseTitle ?? 'Course';
    $serialNumber = $serialNumber ?? 'MIND-SERIAL';
    $issueDateFormatted = $issueDateFormatted ?? now()->format('j / n / Y');
    $instructorName = $instructorName ?? 'Instructor';
    $instructorLabel = $instructorLabel ?? 'Instructor';
    $serialLabel = $serialLabel ?? 'Serial Number';
    $dateLabel = $dateLabel ?? 'Date';
    $certTitle = $certTitle ?? 'CERTIFICATE';
    $certEyebrow = $certEyebrow ?? 'Of Completion';
    $certLead = $certLead ?? 'This certificate is proudly presented to:';
    $certFor = $certFor ?? 'For successfully completing the';
    $certBy = $certBy ?? 'Presented by Mindlytics Academy';
    $certNote = $certNote ?? 'In recognition of their commitment, active participation, and dedication to learning future-ready skills.<br>With our best wishes for continued success and excellence.';
    $templateDomId = $templateDomId ?? 'certificate-template';
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Inter:wght@300;400;500;600&family=Sacramento&display=swap" rel="stylesheet">
<style>
:root{
  --display:"Space Grotesk",system-ui,sans-serif; --body:"Inter",system-ui,sans-serif; --script:"Sacramento",cursive;
  --page-bg:linear-gradient(135deg,#0f1e3d 0%,#123a63 45%,#1b2a5c 100%); --panel-bg:linear-gradient(160deg,#ffffff 0%,#f2f6fb 55%,#e9eff8 100%); --panel-inset:26px;
  --content-pad:58px 96px 34px;
  --ink:#10203f; --muted:#41536f; --accent:#2f6fe4; --accent-2:#7fd8e4;
  --title-color:#10203f; --title-size:52px; --title-weight:700;
  --title-tracking:.22em;
  --name-font:"Space Grotesk",system-ui,sans-serif; --name-color:#0d5ea8; --name-size:64px;
  --name-weight:700; --name-tracking:-.01em;
  --rule-bg:linear-gradient(90deg,#28c6d8,#2f6fe4,#7b5cf0); --rule-w:880px; --dot:#2f6fe4; --dot-show:none;
  --ribbon-w:420px; --ribbon-h:54px; --ribbon-bg:linear-gradient(100deg,#28c6d8,#2f6fe4 55%,#7b5cf0);
  --ribbon-clip:polygon(22px 0,100% 0,calc(100% - 22px) 100%,0 100%); --ribbon-border:none; --ribbon-shadow:0 6px 18px rgba(47,111,228,.35);
  --eyebrow-font:"Space Grotesk",sans-serif; --eyebrow-style:normal; --eyebrow-weight:500;
  --eyebrow-size:24px; --eyebrow-color:#fff;
  --eyebrow-tracking:.14em; --eyebrow-shadow:none;
  --script-size:44px; --sign-color:#10203f;
  --orn-1:#28c6d8; --orn-2:#7b5cf0; --orn-3:#2f6fe4;
  --seal-1:#1d4ea8; --seal-2:#4f8ce8; --seal-3:#cfe3ff;
}

*{box-sizing:border-box;margin:0;padding:0}
.cert-shell{background:#1e2225;display:flex;flex-direction:column;align-items:center;gap:16px;
     padding:24px 12px 44px;font-family:var(--body)}
.toolbar{display:flex;gap:14px;align-items:center;flex-wrap:wrap;justify-content:center;
     color:#c8cfcb;font-family:system-ui,sans-serif;font-size:13px}
.toolbar button{font:inherit;font-weight:600;border:none;border-radius:6px;padding:8px 16px;
     cursor:pointer;color:#14181a;background:linear-gradient(180deg,var(--accent-2),var(--accent))}
.toolbar button:hover{filter:brightness(1.08)}
.toolbar button:focus-visible{outline:2px solid var(--accent-2);outline-offset:2px}
.toolbar a{color:var(--accent-2)}

.page{position:relative;width:1122px;height:793px;flex:none;overflow:hidden;
     background:var(--page-bg);box-shadow:0 22px 55px rgba(0,0,0,.5)}
.ornaments{position:absolute;inset:0;width:100%;height:100%;pointer-events:none}
.panel{position:absolute;inset:var(--panel-inset);background:var(--panel-bg)}
.panel::after{content:"";position:absolute;inset:0;opacity:.45;pointer-events:none;
     background-image:repeating-linear-gradient(90deg,rgba(0,0,0,.013) 0 1px,transparent 1px 3px),
                      repeating-linear-gradient(0deg,rgba(0,0,0,.013) 0 1px,transparent 1px 3px)}

.content{position:absolute;inset:var(--panel-inset);display:flex;flex-direction:column;
     align-items:center;text-align:center;color:var(--ink);padding:var(--content-pad)}

.title{font-family:var(--display);font-weight:var(--title-weight);font-size:var(--title-size);
     line-height:1;letter-spacing:var(--title-tracking);text-indent:var(--title-tracking);
     color:var(--title-color)}
.eyebrow{margin-top:22px;display:flex;align-items:center;justify-content:center;
     width:var(--ribbon-w);height:var(--ribbon-h);background:var(--ribbon-bg);
     clip-path:var(--ribbon-clip);border:var(--ribbon-border);box-shadow:var(--ribbon-shadow)}
.eyebrow span{font-family:var(--eyebrow-font);font-style:var(--eyebrow-style);
     font-weight:var(--eyebrow-weight);font-size:var(--eyebrow-size);color:var(--eyebrow-color);
     letter-spacing:var(--eyebrow-tracking);text-shadow:var(--eyebrow-shadow)}
.lead{margin-top:24px;font-weight:600;font-size:19px;letter-spacing:.02em;color:var(--muted)}
.name{font-family:var(--name-font);font-weight:var(--name-weight);font-size:var(--name-size);
     line-height:1.12;margin-top:18px;color:var(--name-color);letter-spacing:var(--name-tracking)}
.rule{position:relative;width:100%;max-width:var(--rule-w);height:2px;margin:16px 0 18px;
     background:var(--rule-bg)}
.rule::before,.rule::after{content:"";position:absolute;top:50%;width:11px;height:11px;
     border-radius:50%;background:var(--dot);transform:translateY(-50%);display:var(--dot-show)}
.rule::before{left:-5px}.rule::after{right:-5px}
.for{font-size:20px;color:var(--muted)}
.course{font-family:var(--display);font-weight:700;font-size:33px;margin-top:10px;color:var(--ink)}
.by{font-weight:600;font-size:18px;margin-top:11px;letter-spacing:.03em;color:var(--muted)}
.note{font-size:18px;line-height:1.5;margin-top:9px;max-width:830px;color:var(--muted)}

.footer{margin-top:auto;width:100%;display:grid;grid-template-columns:1fr 250px 1fr;
     align-items:end;column-gap:22px}
.block{display:flex;flex-direction:column;align-items:center}
.sign{font-family:var(--script);font-size:var(--script-size);line-height:1;height:60px;
     display:flex;align-items:flex-end;white-space:nowrap;color:var(--sign-color)}
.sign--alt{transform:rotate(-5deg)}
.line{position:relative;width:232px;height:2px;margin-top:8px;background:var(--rule-bg)}
.line::before,.line::after{content:"";position:absolute;top:50%;width:10px;height:10px;
     border-radius:50%;background:var(--dot);transform:translateY(-50%);display:var(--dot-show)}
.line::before{left:-5px}.line::after{right:-5px}
.role{font-family:var(--display);font-style:italic;font-weight:600;font-size:21px;
     margin-top:11px;color:var(--ink)}
.seal{width:90px;height:90px;filter:drop-shadow(0 3px 5px rgba(0,0,0,.3))}
.date{font-family:var(--display);font-size:26px;margin-top:13px;color:var(--ink)}
.date-line{width:180px}
[contenteditable]:focus{outline:2px dashed var(--accent);outline-offset:5px;border-radius:3px}

@page{size:A4 landscape;margin:0}
@media print{.cert-shell{background:#fff;padding:0;display:block}
  .toolbar{display:none}.page{box-shadow:none;width:297mm;height:210mm}}
@media (max-width:1180px){.page{transform:scale(.76);transform-origin:top center;margin-bottom:-180px}}

.title{text-transform:uppercase}.eyebrow span{text-transform:uppercase}

.toolbar{display:none!important}
.sign.serial{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace!important;font-size:18px!important;font-style:normal!important;letter-spacing:.04em;transform:none!important;white-space:normal;word-break:break-all;max-width:240px;justify-content:center;text-align:center;line-height:1.25;height:auto;min-height:48px;align-items:center}
.cert-shell{background:transparent;padding:0;display:block}
.cert-shell .page{margin:0 auto;box-shadow:0 12px 40px rgba(0,0,0,.25)}
</style>
<div class="cert-shell" dir="ltr" lang="en">
  <div class="page" id="{{ $templateDomId }}">

  <svg class="ornaments" viewBox="0 0 1122 793" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <linearGradient id="oA" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="var(--orn-1)"/><stop offset="30%" stop-color="var(--orn-3)"/>
        <stop offset="55%" stop-color="var(--orn-2)"/><stop offset="100%" stop-color="var(--orn-1)"/>
      </linearGradient>
      <linearGradient id="oB" x1="1" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="var(--orn-2)"/><stop offset="45%" stop-color="var(--orn-3)"/>
        <stop offset="100%" stop-color="var(--orn-1)"/>
      </linearGradient>
      
    </defs>
    <path d="M 1122 0 L 1122 300 L 830 0 Z" fill="url(#oA)" opacity=".95"/><path d="M 1122 0 L 1122 150 L 976 0 Z" fill="var(--orn-2)" opacity=".55"/><path d="M 0 793 L 0 493 L 292 793 Z" fill="url(#oB)" opacity=".95"/><path d="M 0 793 L 0 643 L 146 793 Z" fill="var(--orn-2)" opacity=".55"/><path d="M 700 0 L 780 0 L 560 240" fill="none" stroke="var(--orn-2)" stroke-width="2" opacity=".5"/>
  </svg>

  <div class="panel"></div>

  <div class="content">
    <h1 class="title">{{ $certTitle }}</h1>
    <div class="eyebrow"><span>{{ $certEyebrow }}</span></div>
    <p class="lead">{{ $certLead }}</p>
    <p class="name">{{ $studentName }}</p>
    <div class="rule"></div>
    <p class="for">{{ $certFor }}</p>
    <p class="course">{{ $courseTitle }}</p>
    <p class="by">{{ $certBy }}</p>
    <p class="note">{!! $certNote !!}</p>

    <div class="footer">
      <div class="block">
        <div class="sign serial">{{ $serialNumber }}</div>
        <div class="line"></div>
        <div class="role">{{ $serialLabel }}</div>
      </div>
      <div class="block">
        @include('components.certificates.official-stamp', ['branding' => $branding ?? null, 'templateDomId' => $templateDomId ?? 'certificate-template'])
        <div class="date">{{ $issueDateFormatted }}</div>
        <div class="line date-line"></div>
        <div class="role" style="font-style:normal;font-weight:400">{{ $dateLabel }}</div>
      </div>
      <div class="block">
        <div class="sign sign--alt">{{ $instructorName }}</div>
        <div class="line"></div>
        <div class="role">{{ $instructorLabel }}</div>
      </div>
    </div>
  </div>

  </div>
</div>