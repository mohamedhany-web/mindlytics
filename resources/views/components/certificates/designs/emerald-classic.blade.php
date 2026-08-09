{{-- Auto-generated from certification/01-emerald-classic.html — do not edit by hand lightly --}}
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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,600;1,700&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Sacramento&display=swap" rel="stylesheet">
<style>
:root{
  --display:"Playfair Display",Georgia,serif; --body:"EB Garamond",Georgia,serif; --script:"Sacramento",cursive;
  --page-bg:linear-gradient(140deg,#0b6d4e 0%,#075a41 55%,#0a6349 100%); --panel-bg:radial-gradient(circle at 12% 18%,rgba(201,162,39,.10) 0 2px,transparent 3px),radial-gradient(circle at 78% 9%,rgba(201,162,39,.10) 0 2px,transparent 3px),radial-gradient(circle at 33% 62%,rgba(201,162,39,.08) 0 2px,transparent 3px),radial-gradient(circle at 88% 74%,rgba(201,162,39,.09) 0 2px,transparent 3px),radial-gradient(ellipse at 50% 40%,#fffefb 0%,#f8f7f3 62%,#efece2 100%); --panel-inset:30px;
  --content-pad:52px 92px 34px;
  --ink:#16265f; --muted:#16265f; --accent:#c9a227; --accent-2:#f6e39a;
  --title-color:#16265f; --title-size:63px; --title-weight:800;
  --title-tracking:.155em;
  --name-font:"Playfair Display",Georgia,serif; --name-color:#16265f; --name-size:70px;
  --name-weight:700; --name-tracking:0;
  --rule-bg:linear-gradient(90deg,#b9911f,#e2c869,#b9911f); --rule-w:912px; --dot:radial-gradient(circle at 35% 30%,#f3e19a,#b8891a); --dot-show:block;
  --ribbon-w:470px; --ribbon-h:60px; --ribbon-bg:linear-gradient(100deg,#8a6a1a 0%,#e7cf7e 16%,#f8eebb 34%,#d4af37 52%,#f3e39d 72%,#c39a20 88%,#8a6a1a 100%);
  --ribbon-clip:polygon(0 0,100% 0,calc(100% - 34px) 50%,100% 100%,0 100%,34px 50%); --ribbon-border:none; --ribbon-shadow:0 3px 7px rgba(0,0,0,.16);
  --eyebrow-font:"Playfair Display",Georgia,serif; --eyebrow-style:italic; --eyebrow-weight:700;
  --eyebrow-size:35px; --eyebrow-color:#fff;
  --eyebrow-tracking:.01em; --eyebrow-shadow:0 1px 2px rgba(80,58,8,.5);
  --script-size:44px; --sign-color:#1b2a55;
  --orn-1:#7d5f14; --orn-2:#c9a227; --orn-3:#fbf1c2;
  --seal-1:#8a6a1a; --seal-2:#d9b445; --seal-3:#fdf3c9;
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
      <g id="c">
      <path d="M 18 336 L 18 66 C 18 40 40 18 66 18 L 336 18 L 336 66 L 66 66 L 66 336 Z" fill="url(#oA)"/>
      <path d="M 96 300 L 96 96 L 300 96 L 300 122 L 122 122 L 122 300 Z" fill="url(#oB)"/>
      <path d="M 152 18 L 240 18 L 66 220 L 66 132 Z" fill="url(#oA)" opacity=".95"/>
      <path d="M 176 330 L 176 176 L 330 176" fill="none" stroke="var(--orn-2)" stroke-width="2.5" stroke-linecap="round"/>
    </g>
    </defs>
    <use href="#c"/><use href="#c" transform="rotate(180 561 396.5)"/>
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