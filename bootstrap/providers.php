<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\PermissionServiceProvider::class,

    // Domain modules (docs/MODULARIZATION_PLAN.md)
    App\Domains\Investment\InvestmentServiceProvider::class,
];
