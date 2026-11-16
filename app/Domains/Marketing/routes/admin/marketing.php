<?php

use App\Domains\Marketing\Http\Controllers\Admin\AdvertisingCampaignController;
use App\Domains\Marketing\Http\Controllers\Admin\InstructorPersonalBrandingController;
use App\Domains\Marketing\Http\Controllers\Admin\MarketingRegionsController;
use App\Domains\Marketing\Http\Controllers\Admin\MarketingWebAnalyticsController;
use App\Domains\Marketing\Http\Controllers\Admin\MetaCampaignLeadsController;
use App\Domains\Marketing\Http\Controllers\Admin\PopupAdController;
use Illuminate\Support\Facades\Route;

/**
 * التسويق: البراندينغ الشخصي، الإعلانات المنبثقة، الحملات، تحليلات الويب، المناطق.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('/personal-branding', [InstructorPersonalBrandingController::class, 'index'])->name('personal-branding.index');
Route::resource('popup-ads', PopupAdController::class)->except(['show']);

// الحملات الإعلانية (القائمة من Meta) + تقارير السيلز المحلية
Route::get('advertising-campaigns/reports', [AdvertisingCampaignController::class, 'reports'])->name('advertising-campaigns.reports');
Route::get('advertising-campaigns/reports/export', [AdvertisingCampaignController::class, 'reportsExport'])->name('advertising-campaigns.reports.export');
Route::get('advertising-campaigns/meta/{metaCampaign}/local-settings', [AdvertisingCampaignController::class, 'localSettings'])
    ->where('metaCampaign', '[0-9]+')
    ->name('advertising-campaigns.meta.local-settings');
Route::post('advertising-campaigns/meta/{metaCampaign}/sync-leads', [AdvertisingCampaignController::class, 'syncMetaCampaignLeads'])
    ->where('metaCampaign', '[0-9]+')
    ->name('advertising-campaigns.meta.sync-leads');
Route::get('advertising-campaigns/meta/{metaCampaign}/leads', [MetaCampaignLeadsController::class, 'index'])
    ->where('metaCampaign', '[0-9]+')
    ->name('advertising-campaigns.meta.leads');
Route::get('advertising-campaigns/meta/{metaCampaign}/leads/export', [MetaCampaignLeadsController::class, 'export'])
    ->where('metaCampaign', '[0-9]+')
    ->name('advertising-campaigns.meta.leads.export');
Route::post('advertising-campaigns/{advertising_campaign}/sync-meta-leads', [AdvertisingCampaignController::class, 'syncMetaLeads'])
    ->name('advertising-campaigns.sync-meta-leads');
Route::resource('advertising-campaigns', AdvertisingCampaignController::class)->except(['show']);

Route::get('marketing-web-analytics', [MarketingWebAnalyticsController::class, 'edit'])->name('marketing-web-analytics.settings');
Route::put('marketing-web-analytics', [MarketingWebAnalyticsController::class, 'update'])->name('marketing-web-analytics.settings.update');

Route::get('marketing-regions', [MarketingRegionsController::class, 'index'])->name('marketing-regions.index');
