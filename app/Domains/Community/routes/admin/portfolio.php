<?php

use App\Domains\Community\Http\Controllers\Admin\JourneyAnalyticsController;
use App\Domains\Community\Http\Controllers\Admin\PortfolioController;
use Illuminate\Support\Facades\Route;

/**
 * مراجعة البورتفوليو + تحليلات الرحلة.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('portfolio/{project}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::post('portfolio/{project}/toggle-visibility', [PortfolioController::class, 'toggleVisibility'])->name('portfolio.toggle-visibility');
Route::post('portfolio/{project}/toggle-featured', [PortfolioController::class, 'toggleFeatured'])->name('portfolio.toggle-featured');
Route::get('journey-analytics', [JourneyAnalyticsController::class, 'index'])->name('journey-analytics.index');
