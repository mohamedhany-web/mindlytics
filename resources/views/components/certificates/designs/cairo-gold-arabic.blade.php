{{-- Auto-generated from certification/09-cairo-gold-arabic.html — do not edit by hand lightly --}}
@php
    $studentName = $studentName ?? 'Student';
    $courseTitle = $courseTitle ?? 'Course';
    $serialNumber = $serialNumber ?? 'MIND-SERIAL';
    $issueDateFormatted = $issueDateFormatted ?? now()->format('j / n / Y');
    $instructorName = $instructorName ?? 'المدرّب';
    $instructorLabel = $instructorLabel ?? 'المدرّب';
    $serialLabel = $serialLabel ?? 'الرقم التسلسلي';
    $dateLabel = $dateLabel ?? 'التاريخ';
    $certTitle = $certTitle ?? 'شهـادة تقديـر';
    $certEyebrow = $certEyebrow ?? 'إتمام الدورة';
    $certLead = $certLead ?? 'تُمنح هذه الشهادة بكل فخر إلى:';
    $certFor = $certFor ?? 'لإتمامه بنجاح دورة';
    $certBy = $certBy ?? 'مقدَّمة من أكاديمية Mindlytics';
    $certNote = $certNote ?? 'تقديراً لالتزامه ومشاركته الفاعلة وتفانيه في اكتساب مهارات المستقبل.<br>مع أطيب التمنيات بمزيد من النجاح والتميّز.';
    $templateDomId = $templateDomId ?? 'certificate-template';
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Tajawal:wght@400;500;700&family=Reem+Kufi:wght@400;600&display=swap" rel="stylesheet">
<style>
:root{
  --display:"Amiri",Georgia,serif; --body:"Tajawal",system-ui,sans-serif; --script:"Amiri",cursive;
  --page-bg:linear-gradient(140deg,#0d6b52 0%,#08543f 58%,#0b7357 100%); --panel-bg:linear-gradient(180deg,#fffef9,#f5f1e6); --panel-inset:30px;
  --content-pad:50px 92px 34px;
  --ink:#123f6b; --muted:#3a5a80; --accent:#c9a227; --accent-2:#f6e39a;
  --title-color:#123f6b; --title-size:58px; --title-weight:800;
  --title-tracking:0;
  --name-font:"Amiri",Georgia,serif; --name-color:#123f6b; --name-size:64px;
  --name-weight:700; --name-tracking:0;
  --rule-bg:linear-gradient(90deg,#b9911f,#e2c869,#b9911f); --rule-w:912px; --dot:radial-gradient(circle at 35% 30%,#f3e19a,#b8891a); --dot-show:block;
  --ribbon-w:440px; --ribbon-h:60px; --ribbon-bg:linear-gradient(100deg,#8a6a1a 0%,#e7cf7e 16%,#f8eebb 34%,#d4af37 52%,#f3e39d 72%,#c39a20 88%,#8a6a1a 100%);
  --ribbon-clip:polygon(0 0,100% 0,calc(100% - 34px) 50%,100% 100%,0 100%,34px 50%); --ribbon-border:none; --ribbon-shadow:0 3px 7px rgba(0,0,0,.16);
  --eyebrow-font:"Reem Kufi",sans-serif; --eyebrow-style:normal; --eyebrow-weight:600;
  --eyebrow-size:28px; --eyebrow-color:#fff;
  --eyebrow-tracking:.01em; --eyebrow-shadow:0 1px 2px rgba(80,58,8,.5);
  --script-size:34px; --sign-color:#123f6b;
  --orn-1:#7d5f14; --orn-2:#c9a227; --orn-3:#fbf1c2;
  --seal-1:#8a6a1a; --seal-2:#d9b445; --seal-3:#fdf3c9;
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

.sign{font-weight:700}.role{font-style:normal}.note{line-height:1.8}.title{text-indent:0}

.toolbar{display:none!important}
.sign.serial{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace!important;font-size:18px!important;font-style:normal!important;letter-spacing:.04em;transform:none!important;white-space:normal;word-break:break-all;max-width:240px;justify-content:center;text-align:center;line-height:1.25;height:auto;min-height:48px;align-items:center}
.cert-shell{background:transparent;padding:0;display:block}
.cert-shell .page{margin:0 auto;box-shadow:0 12px 40px rgba(0,0,0,.25)}
</style>
<div class="cert-shell" dir="rtl" lang="ar">
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
      <g id="c"><path d="M 83.0 38.6 L 80.1 59.9 L 101.4 57.0 L 84.3 70.0 L 101.4 83.0 L 80.1 80.1 L 83.0 101.4 L 70.0 84.3 L 57.0 101.4 L 59.9 80.1 L 38.6 83.0 L 55.7 70.0 L 38.6 57.0 L 59.9 59.9 L 57.0 38.6 L 70.0 55.7 Z" fill="url(#oA)" opacity="1"/><path d="M 159.2 47.8 L 157.1 62.9 L 172.2 60.8 L 160.1 70.0 L 172.2 79.2 L 157.1 77.1 L 159.2 92.2 L 150.0 80.1 L 140.8 92.2 L 142.9 77.1 L 127.8 79.2 L 139.9 70.0 L 127.8 60.8 L 142.9 62.9 L 140.8 47.8 L 150.0 59.9 Z" fill="url(#oA)" opacity="0.8"/><path d="M 79.2 127.8 L 77.1 142.9 L 92.2 140.8 L 80.1 150.0 L 92.2 159.2 L 77.1 157.1 L 79.2 172.2 L 70.0 160.1 L 60.8 172.2 L 62.9 157.1 L 47.8 159.2 L 59.9 150.0 L 47.8 140.8 L 62.9 142.9 L 60.8 127.8 L 70.0 139.9 Z" fill="url(#oA)" opacity="0.8"/><path d="M 239.2 47.8 L 237.1 62.9 L 252.2 60.8 L 240.1 70.0 L 252.2 79.2 L 237.1 77.1 L 239.2 92.2 L 230.0 80.1 L 220.8 92.2 L 222.9 77.1 L 207.8 79.2 L 219.9 70.0 L 207.8 60.8 L 222.9 62.9 L 220.8 47.8 L 230.0 59.9 Z" fill="url(#oA)" opacity="0.8"/><path d="M 79.2 207.8 L 77.1 222.9 L 92.2 220.8 L 80.1 230.0 L 92.2 239.2 L 77.1 237.1 L 79.2 252.2 L 70.0 240.1 L 60.8 252.2 L 62.9 237.1 L 47.8 239.2 L 59.9 230.0 L 47.8 220.8 L 62.9 222.9 L 60.8 207.8 L 70.0 219.9 Z" fill="url(#oA)" opacity="0.8"/></g>
    </defs>
    <rect x="34" y="34" width="1054" height="725" fill="none" stroke="var(--orn-2)" stroke-width="2"/><use href="#c"/><use href="#c" transform="translate(1122 0) scale(-1 1)"/><use href="#c" transform="translate(0 793) scale(1 -1)"/><use href="#c" transform="rotate(180 561 396.5)"/>
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