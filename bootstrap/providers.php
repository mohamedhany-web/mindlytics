<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\PermissionServiceProvider::class,

    // Domain modules (docs/MODULARIZATION_PLAN.md)
    App\Domains\Investment\InvestmentServiceProvider::class,
    App\Domains\Scholarship\ScholarshipServiceProvider::class,
    App\Domains\Advising\AdvisingServiceProvider::class,
    App\Domains\Hr\HrServiceProvider::class,
    App\Domains\Sales\SalesServiceProvider::class,
    App\Domains\Messaging\MessagingServiceProvider::class,
    App\Domains\Accounting\AccountingServiceProvider::class,
    App\Domains\Places\PlacesServiceProvider::class,
    App\Domains\Marketing\MarketingServiceProvider::class,
    App\Domains\Community\CommunityServiceProvider::class,
    App\Domains\Cms\CmsServiceProvider::class,
    App\Domains\Offline\OfflineServiceProvider::class,
    App\Domains\Learning\LearningServiceProvider::class,
];
