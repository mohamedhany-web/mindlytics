<?php

use App\Domains\Messaging\Http\Controllers\Admin\MetaAdsCampaignController;
use App\Domains\Messaging\Http\Controllers\Admin\MetaAdsSettingsController;
use Illuminate\Support\Facades\Route;

/**
 * إعلانات Meta (حملات وإعدادات).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('meta-ads')->name('meta-ads.')->group(function () {
    Route::get('settings', [MetaAdsSettingsController::class, 'edit'])->name('settings');
    Route::put('settings', [MetaAdsSettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test', [MetaAdsSettingsController::class, 'test'])->name('settings.test');
    Route::post('settings/select-account', [MetaAdsSettingsController::class, 'selectAccount'])->name('settings.select-account');

    Route::get('campaigns', [MetaAdsCampaignController::class, 'index'])->name('campaigns.index');
    Route::get('campaigns/create', [MetaAdsCampaignController::class, 'create'])->name('campaigns.create');
    Route::post('campaigns', [MetaAdsCampaignController::class, 'store'])->name('campaigns.store');
    Route::get('campaigns/{campaign}', [MetaAdsCampaignController::class, 'show'])->name('campaigns.show');
    Route::post('campaigns/{campaign}/pause', [MetaAdsCampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('campaigns/{campaign}/resume', [MetaAdsCampaignController::class, 'resume'])->name('campaigns.resume');
    Route::put('campaigns/{campaign}/budget', [MetaAdsCampaignController::class, 'updateBudget'])->name('campaigns.budget');
    Route::put('campaigns/{campaign}/audience', [MetaAdsCampaignController::class, 'updateAudience'])->name('campaigns.audience');
});
