<?php

use Illuminate\Support\Facades\Route;

/**
 * Shared task routes for any authenticated role.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

    // المهام (للجميع)
    require app_path('Domains/Learning/routes/tasks.php');
