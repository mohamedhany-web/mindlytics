{{-- Auto-generated from certification/07-platinum-noir.html — do not edit by hand lightly --}}
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
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,600;1,400&family=Sacramento&display=swap" rel="stylesheet">
<style>
:root{
  --display:"Cinzel",Georgia,serif; --body:"Lora",Georgia,serif; --script:"Sacramento",cursive;
  --page-bg:linear-gradient(140deg,#1a1c1f 0%,#0b0c0e 60%,#202327 100%); --panel-bg:transparent; --panel-inset:0px;
  --content-pad:78px 118px 54px;
  --ink:#e6e8ea; --muted:#a9aeb4; --accent:#c3c9cf; --accent-2:#eef1f3;
  --title-color:#f2f4f6; --title-size:54px; --title-weight:800;
  --title-tracking:.3em;
  --name-font:"Cinzel",Georgia,serif; --name-color:#dfe4e8; --name-size:60px;
  --name-weight:700; --name-tracking:.04em;
  --rule-bg:linear-gradient(90deg,transparent,#c3c9cf,transparent); --rule-w:912px; --dot:radial-gradient(circle at 35% 30%,#fff,#8d949a); --dot-show:block;
  --ribbon-w:470px; --ribbon-h:60px; --ribbon-bg:linear-gradient(100deg,#4a5055,#d5dade 35%,#9aa1a7 55%,#eef1f3 80%,#4a5055);
  --ribbon-clip:polygon(0 0,100% 0,calc(100% - 34px) 50%,100% 100%,0 100%,34px 50%); --ribbon-border:none; --ribbon-shadow:0 3px 7px rgba(0,0,0,.16);
  --eyebrow-font:"Playfair Display",Georgia,serif; --eyebrow-style:italic; --eyebrow-weight:700;
  --eyebrow-size:35px; --eyebrow-color:#15171a;
  --eyebrow-tracking:.01em; --eyebrow-shadow:none;
  --script-size:44px; --sign-color:#e6e8ea;
  --orn-1:#5c6268; --orn-2:#d5dade; --orn-3:#ffffff;
  --seal-1:#585e64; --seal-2:#c3c9cf; --seal-3:#ffffff;
}

*{box-sizing:border-box;margin:0;padding:0}
body{background:#1e2225;display:flex;flex-direction:column;align-items:center;gap:16px;
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
@media print{body{background:#fff;padding:0;display:block}
  .toolbar{display:none}.page{box-shadow:none;width:297mm;height:210mm}}
@media (max-width:1180px){.page{transform:scale(.76);transform-origin:top center;margin-bottom:-180px}}

.panel::after{display:none}

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
      <g id="c"><path d="M 56 260 L 56 56 L 260 56" fill="none" stroke="url(#oA)" stroke-width="6"/><path d="M 84 200 L 84 84 L 200 84" fill="none" stroke="var(--orn-2)" stroke-width="1.6"/></g>
    </defs>
    <use href="#c"/><use href="#c" transform="translate(1122 0) scale(-1 1)"/><use href="#c" transform="translate(0 793) scale(1 -1)"/><use href="#c" transform="rotate(180 561 396.5)"/>
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