{{-- رأس الصفحة لصفحات الوظائف والتدريب والتواصل وغيرها: بقى نفس رأس الموقع الموحّد (components/site/page-head). --}}
@include('components.site.page-head', [
    'title' => $title,
    'subtitle' => $subtitle ?? null,
    'eyebrow' => $badge ?? null,
    'crumbs' => ! empty($backUrl) ? [['label' => $backLabel ?? 'جميع الوظائف', 'url' => $backUrl], ['label' => $title]] : [['label' => $title]],
    'chips' => collect($metaChips ?? [])->map(fn ($c) => ['label' => $c['label'] ?? '', 'icon' => $c['icon'] ?? null])->all(),
])
