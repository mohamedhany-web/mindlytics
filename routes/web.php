<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
| This file is the only web entry registered in bootstrap/app.php.
| Domain routes live in routes/web/* and are loaded here in the same
| order as the previous single file, so URIs, names, and middleware
| stay unchanged.
|
*/

require __DIR__.'/web/infrastructure.php';
require __DIR__.'/web/public.php';
require __DIR__.'/web/auth.php';

    Route::middleware(['auth', 'prevent-concurrent'])->group(function () {
    require __DIR__.'/web/portal.php';
    require __DIR__.'/web/student.php';
    require __DIR__.'/web/employee.php';
    require __DIR__.'/web/admin.php';
    require __DIR__.'/web/tasks.php';
    require __DIR__.'/web/student-account.php';
    require __DIR__.'/web/instructor.php';
});
