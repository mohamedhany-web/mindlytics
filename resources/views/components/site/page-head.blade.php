{{--
    رأس الصفحات الداخلية الموحّد (docs/SITE_DESIGN.md).
    @include('components.site.page-head', ['title' => '…', 'subtitle' => '…', 'eyebrow' => '…', 'crumbs' => [['label' => '…', 'url' => '…']], 'chips' => [['label' => '…', 'icon' => 'fas fa-…']]])
--}}
@php
    $siteAr = app()->getLocale() === 'ar';
    $crumbs = $crumbs ?? [];
    $chips = $chips ?? [];
@endphp
<section class="h-page-head">
    <div class="h-wrap">
        <nav class="h-crumbs" aria-label="{{ $siteAr ? 'مسار الصفحة' : 'Breadcrumb' }}">
            <a href="{{ route('home') }}">{{ $siteAr ? 'الرئيسية' : 'Home' }}</a>
            @foreach($crumbs as $c)
                <span aria-hidden="true">/</span>
                @if(! empty($c['url']))<a href="{{ $c['url'] }}">{{ $c['label'] }}</a>@else<span>{{ $c['label'] }}</span>@endif
            @endforeach
        </nav>
        @if(! empty($eyebrow))<span class="h-eyebrow h-eyebrow-accent">{{ $eyebrow }}</span>@endif
        <h1>{{ $title }}</h1>
        @if(! empty($subtitle))<p>{{ $subtitle }}</p>@endif
        @if($chips !== [])
            <ul class="h-page-chips">
                @foreach($chips as $chip)
                    <li>@if(! empty($chip['icon']))<i class="{{ $chip['icon'] }}" aria-hidden="true"></i> @endif{{ $chip['label'] }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
