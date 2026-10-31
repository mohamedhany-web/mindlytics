<?php

use App\Domains\Community\Http\Controllers\Admin\CommunityCompetitionController;
use App\Domains\Community\Http\Controllers\Admin\CommunityController;
use App\Domains\Community\Http\Controllers\Admin\CommunityDatasetController;
use Illuminate\Support\Facades\Route;

/**
 * إدارة بوابة المجتمع (super_admin).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('community')->name('community.')->middleware('role:super_admin')->group(function () {
    Route::get('/', [CommunityController::class, 'dashboard'])->name('dashboard');
    Route::resource('competitions', CommunityCompetitionController::class)->except(['show']);
    Route::resource('datasets', CommunityDatasetController::class)->except(['show']);
    Route::get('/submissions', [CommunityController::class, 'submissions'])->name('submissions.index');
    Route::get('/submissions/dataset/{dataset}', [CommunityController::class, 'showSubmission'])->name('submissions.dataset.show');
    Route::get('/submissions/dataset/{dataset}/download', [CommunityController::class, 'downloadSubmission'])->name('submissions.dataset.download');
    Route::get('/submissions/dataset/{dataset}/preview', [CommunityController::class, 'submissionPreview'])->name('submissions.dataset.preview');
    Route::get('/submissions/dataset/{dataset}/preview-zip-entry', [CommunityController::class, 'submissionPreviewZipEntry'])->name('submissions.dataset.preview-zip-entry');
    Route::get('/submissions/dataset/{dataset}/download/{index}', [CommunityController::class, 'submissionDownloadFile'])->name('submissions.dataset.download-file')->whereNumber('index');
    Route::get('/submissions/dataset/{dataset}/download-all', [CommunityController::class, 'submissionDownloadAll'])->name('submissions.dataset.download-all');
    Route::get('/submissions/dataset/{dataset}/zip-contents', [CommunityController::class, 'submissionZipContents'])->name('submissions.dataset.zip-contents');
    Route::post('/submissions/dataset/{dataset}/approve', [CommunityController::class, 'approveDataset'])->name('submissions.dataset.approve');
    Route::post('/submissions/dataset/{dataset}/reject', [CommunityController::class, 'rejectDataset'])->name('submissions.dataset.reject');
    Route::get('/submissions/models', [CommunityController::class, 'modelSubmissions'])->name('submissions.models.index');
    Route::get('/submissions/model/{community_model}', [CommunityController::class, 'showModelSubmission'])->name('submissions.model.show');
    Route::post('/submissions/model/{community_model}/approve', [CommunityController::class, 'approveModel'])->name('submissions.model.approve');
    Route::post('/submissions/model/{community_model}/reject', [CommunityController::class, 'rejectModel'])->name('submissions.model.reject');
    Route::get('/contributors', [CommunityController::class, 'contributors'])->name('contributors.index');
    Route::post('/contributors', [CommunityController::class, 'addContributor'])->name('contributors.store');
    Route::post('/contributors/new', [CommunityController::class, 'storeNewContributor'])->name('contributors.new.store');
    Route::post('/contributors/profiles/{profile}/approve', [CommunityController::class, 'approveContributorProfile'])->name('contributors.profiles.approve');
    Route::post('/contributors/profiles/{profile}/reject', [CommunityController::class, 'rejectContributorProfile'])->name('contributors.profiles.reject');
    Route::delete('/contributors/{user}', [CommunityController::class, 'removeContributor'])->name('contributors.destroy');
    Route::get('/notifications', [CommunityController::class, 'notificationsForm'])->name('notifications.index');
    Route::post('/notifications/send', [CommunityController::class, 'sendNotifications'])->name('notifications.send');
    Route::get('/discussions', [CommunityController::class, 'discussions'])->name('discussions.index');
    Route::get('/settings', [CommunityController::class, 'settings'])->name('settings.index');
});
