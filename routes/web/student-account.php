<?php

use Illuminate\Support\Facades\Route;

/**
 * Student account area: invoices, wallet, certificates, assignments, and groups.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

    // مسارات الطلاب - محمية للطلاب فقط
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        require app_path('Domains/Accounting/routes/student/invoices-wallet.php');
        require app_path('Domains/Learning/routes/student/certificates.php');
        require app_path('Domains/Community/routes/student/achievements.php');
        require app_path('Domains/Learning/routes/student/assignments-groups.php');
    });
