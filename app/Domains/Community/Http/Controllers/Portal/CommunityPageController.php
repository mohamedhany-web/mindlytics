<?php

namespace App\Domains\Community\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Domains\Community\Models\CommunityCompetition;
use App\Domains\Community\Models\CommunityDataset;
use App\Domains\Community\Models\CommunityModel;
use App\Domains\Community\Models\ContributorProfile;
use App\Models\User;
use App\Domains\Community\Services\DatasetFileReaderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * صفحات مجتمع البيانات: المسابقات، مجموعات البيانات، المناقشات.
 * عرض عادي للمستخدم (قراءة فقط — بدون CRUD).
 */
class CommunityPageController extends Controller
{
    public function competitions(): View
    {
        $competitions = CommunityCompetition::active()->ordered()->paginate(12);
        return view('community::portal.competitions.index', compact('competitions'));
    }

    public function datasets(Request $request): View
    {
        $search = $request->input('q');
        $category = $request->input('category');

        $query = CommunityDataset::public()
            ->search($search)
            ->category($category)
            ->ordered();

        $datasets = $query->paginate(12)->withQueryString();

        $categoriesWithCount = collect(CommunityDataset::CATEGORIES)->map(function ($label, $key) {
            $count = CommunityDataset::public()->where('category', $key)->count();
            return ['key' => $key, 'label' => $label, 'count' => $count];
        })->values();

        return view('community::portal.datasets.index', [
            'datasets' => $datasets,
            'categoriesWithCount' => $categoriesWithCount,
            'currentSearch' => $search,
            'currentCategory' => $category,
        ]);
    }

    public function datasetShow(DatasetFileReaderService $reader, CommunityDataset $dataset): View|RedirectResponse
    {
        if ($dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }

        $disk = community_disk();
        $preview = ['headers' => [], 'rows' => []];

        if ($dataset->file_path) {
            $preview = $reader->readPreviewFromStorage($disk, $dataset->file_path);
        }

        return view('community::portal.datasets.show', [
            'dataset' => $dataset,
            'previewHeaders' => $preview['headers'],
            'previewRows' => $preview['rows'],
        ]);
    }

    public function datasetDownload(CommunityDataset $dataset): StreamedResponse
    {
        $list = $dataset->files_list;
        if (empty($list)) {
            abort(404);
        }
        $first = $list[0];
        $path = is_array($first) ? ($first['path'] ?? null) : null;
        $name = is_array($first) ? ($first['original_name'] ?? basename($path)) : basename($path);
        if (!$path || $dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }
        $disk = community_disk();
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        return Storage::disk($disk)->download($path, $name);
    }

    /**
     * تحميل ملف واحد بالرقم (من قائمة الملفات).
     */
    public function datasetDownloadFile(CommunityDataset $dataset, int $index): StreamedResponse
    {
        if ($dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }
        $list = $dataset->files_list;
        if ($index < 0 || $index >= count($list)) {
            abort(404);
        }
        $item = $list[$index];
        $path = $item['path'] ?? null;
        $name = $item['original_name'] ?? basename($path);
        if (!$path) {
            abort(404);
        }
        $disk = community_disk();
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        return Storage::disk($disk)->download($path, $name);
    }

    /**
     * تحميل جميع الملفات كأرشيف ZIP واحد.
     */
    public function datasetDownloadAll(CommunityDataset $dataset): Response
    {
        if ($dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }
        $list = $dataset->files_list;
        if (empty($list)) {
            abort(404);
        }
        $disk = community_disk();
        $zipPath = tempnam(sys_get_temp_dir(), 'dataset_zip_') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'تعذر إنشاء الأرشيف');
        }
        foreach ($list as $i => $item) {
            $path = $item['path'] ?? null;
            $name = $item['original_name'] ?? ('file_' . $i);
            if (!$path || !Storage::disk($disk)->exists($path)) {
                continue;
            }
            $content = Storage::disk($disk)->get($path);
            $zip->addFromString($name, $content);
        }
        $zip->close();
        $downloadName = \Illuminate\Support\Str::slug($dataset->title) . '-all.zip';
        try {
            return response()->download($zipPath, $downloadName, ['Content-Type' => 'application/zip'])->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            @unlink($zipPath);
            throw $e;
        }
    }

    /**
     * معاينة البيانات كـ JSON (تحميل كسول). دعم معاينة ملف معيّن بـ ?file=0 وإرجاع محتويات ZIP إن كان الملف مضغوطاً.
     */
    public function datasetPreview(Request $request, DatasetFileReaderService $reader, CommunityDataset $dataset): JsonResponse
    {
        if ($dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }
        $disk = community_disk();
        $list = $dataset->files_list;
        $fileIndex = (int) $request->input('file', 0);
        if ($fileIndex < 0 || $fileIndex >= count($list)) {
            $fileIndex = 0;
        }
        $item = $list[$fileIndex] ?? null;
        if (!$item) {
            return response()->json(['headers' => [], 'rows' => []]);
        }
        $pathToRead = $item['path'] ?? null;
        if (!$pathToRead || !Storage::disk($disk)->exists($pathToRead)) {
            return response()->json(['headers' => [], 'rows' => []]);
        }
        $ext = strtolower(pathinfo($pathToRead, PATHINFO_EXTENSION));
        if ($ext === 'zip') {
            $entries = $reader->listZipEntriesFromStorage($disk, $pathToRead);
            return response()->json(['zip' => true, 'entries' => $entries]);
        }
        $preview = $reader->readPreviewFromStorage($disk, $pathToRead);
        return response()->json([
            'headers' => $preview['headers'],
            'rows' => $preview['rows'],
        ]);
    }

    /**
     * معاينة ملف داخل أرشيف ZIP (مثل CSV داخل الـ ZIP).
     * المعاملات: file=رقم الملف المضغوط، entry=اسم الملف داخل الأرشيف.
     */
    public function datasetPreviewZipEntry(Request $request, DatasetFileReaderService $reader, CommunityDataset $dataset): JsonResponse
    {
        if ($dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }
        $disk = community_disk();
        $list = $dataset->files_list;
        $fileIndex = (int) $request->input('file', 0);
        $entryName = $request->input('entry', '');
        if ($fileIndex < 0 || $fileIndex >= count($list) || $entryName === '') {
            return response()->json(['headers' => [], 'rows' => []]);
        }
        $item = $list[$fileIndex] ?? null;
        $path = $item['path'] ?? null;
        if (!$path || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'zip') {
            return response()->json(['headers' => [], 'rows' => []]);
        }
        $preview = $reader->readPreviewFromZipEntry($disk, $path, $entryName);
        return response()->json([
            'headers' => $preview['headers'],
            'rows' => $preview['rows'],
        ]);
    }

    /**
     * إرجاع قائمة ملفات داخل ZIP (للمعاينة في الصفحة).
     */
    public function datasetZipContents(Request $request, DatasetFileReaderService $reader, CommunityDataset $dataset): JsonResponse
    {
        if ($dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }
        $list = $dataset->files_list;
        $fileIndex = (int) $request->input('file', 0);
        if ($fileIndex < 0 || $fileIndex >= count($list)) {
            return response()->json(['entries' => []]);
        }
        $item = $list[$fileIndex] ?? null;
        $path = $item['path'] ?? null;
        if (!$path || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'zip') {
            return response()->json(['entries' => []]);
        }
        $disk = community_disk();
        $entries = $reader->listZipEntriesFromStorage($disk, $path);
        return response()->json(['entries' => $entries]);
    }

    public function discussions(): View
    {
        return view('community::portal.discussions.index');
    }

    /**
     * صفحة النماذج العامة (Model Zoo) — بدون تسجيل دخول — مثل البيانات.
     */
    public function publicModels(Request $request): View
    {
        $search = $request->input('q');
        $query = CommunityModel::public()
            ->with(['creator', 'dataset'])
            ->search($search)
            ->ordered();
        $models = $query->paginate(24)->withQueryString();
        return view('community::public.community.models', [
            'models' => $models,
            'currentSearch' => $search,
        ]);
    }

    /** عرض نموذج واحد (عام — بدون تسجيل) */
    public function publicModelShow(CommunityModel $model): View|RedirectResponse
    {
        if ($model->status !== CommunityModel::STATUS_APPROVED || !$model->is_active) {
            abort(404);
        }
        $model->load(['creator', 'dataset']);
        return view('community::public.community.model-show', compact('model'));
    }

    /** تحميل ملف النموذج (أول ملف) — عام */
    public function publicModelDownload(CommunityModel $model): StreamedResponse
    {
        $list = $model->files_list;
        if (empty($list)) {
            abort(404);
        }
        $first = $list[0];
        $path = is_array($first) ? ($first['path'] ?? null) : null;
        $name = is_array($first) ? ($first['original_name'] ?? basename($path)) : basename($path);
        if (!$path || $model->status !== CommunityModel::STATUS_APPROVED || !$model->is_active) {
            abort(404);
        }
        $disk = community_disk();
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        $model->increment('downloads_count');
        return Storage::disk($disk)->download($path, $name);
    }

    /** تحميل ملف بالرقم — عام */
    public function publicModelDownloadFile(CommunityModel $model, int $index): StreamedResponse
    {
        if ($model->status !== CommunityModel::STATUS_APPROVED || !$model->is_active) {
            abort(404);
        }
        $list = $model->files_list;
        if ($index < 0 || $index >= count($list)) {
            abort(404);
        }
        $item = $list[$index];
        $path = is_array($item) ? ($item['path'] ?? null) : null;
        $name = is_array($item) ? ($item['original_name'] ?? basename($path)) : basename($path);
        if (!$path) {
            abort(404);
        }
        $disk = community_disk();
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        $model->increment('downloads_count');
        return Storage::disk($disk)->download($path, $name);
    }

    /** تحميل ملف النموذج (أول ملف) */
    public function modelDownload(CommunityModel $model): StreamedResponse
    {
        $list = $model->files_list;
        if (empty($list)) {
            abort(404);
        }
        $first = $list[0];
        $path = is_array($first) ? ($first['path'] ?? null) : null;
        $name = is_array($first) ? ($first['original_name'] ?? basename($path)) : basename($path);
        if (!$path || $model->status !== CommunityModel::STATUS_APPROVED || !$model->is_active) {
            abort(404);
        }
        $disk = community_disk();
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        $model->increment('downloads_count');
        return Storage::disk($disk)->download($path, $name);
    }

    /** تحميل ملف بالرقم من قائمة الملفات */
    public function modelDownloadFile(CommunityModel $model, int $index): StreamedResponse
    {
        if ($model->status !== CommunityModel::STATUS_APPROVED || !$model->is_active) {
            abort(404);
        }
        $list = $model->files_list;
        if ($index < 0 || $index >= count($list)) {
            abort(404);
        }
        $item = $list[$index];
        $path = is_array($item) ? ($item['path'] ?? null) : null;
        $name = is_array($item) ? ($item['original_name'] ?? basename($path)) : basename($path);
        if (!$path) {
            abort(404);
        }
        $disk = community_disk();
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        $model->increment('downloads_count');
        return Storage::disk($disk)->download($path, $name);
    }

    /** امتدادات الملفات التي يمكن عرض محتواها (كود/نص) */
    private const PREVIEWABLE_EXTENSIONS = ['py', 'pyw', 'ipynb', 'json', 'txt', 'md'];

    /** أقصى حجم للمعاينة (512 كيلوبايت) */
    private const PREVIEW_MAX_BYTES = 524288;

    /** عرض محتوى ملف نصي/كود (مثل .py) — للمعاينة فقط */
    public function publicModelFilePreview(CommunityModel $model, int $index): \Illuminate\Http\Response
    {
        if ($model->status !== CommunityModel::STATUS_APPROVED || !$model->is_active) {
            abort(404);
        }
        $list = $model->files_list;
        if ($index < 0 || $index >= count($list)) {
            abort(404);
        }
        $item = $list[$index];
        $path = is_array($item) ? ($item['path'] ?? null) : null;
        $name = is_array($item) ? ($item['original_name'] ?? basename($path)) : basename($path);
        if (!$path) {
            abort(404);
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, self::PREVIEWABLE_EXTENSIONS, true)) {
            abort(404);
        }
        $disk = community_disk();
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }
        $content = Storage::disk($disk)->get($path);
        $size = strlen($content);
        if ($size > self::PREVIEW_MAX_BYTES) {
            $content = substr($content, 0, self::PREVIEW_MAX_BYTES) . "\n\n... (تم اقتطاع العرض — حجم الملف أكبر من " . (self::PREVIEW_MAX_BYTES / 1024) . " كيلوبايت. حمّل الملف لعرضه بالكامل)";
        }
        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'inline; filename="' . $name . '"',
        ]);
    }

    /**
     * صفحة البيانات العامة — بدون تسجيل دخول.
     * بحث، تصنيفات، بطاقات، ترقيم؛ تتحمل آلاف السجلات.
     */
    public function publicDatasets(Request $request): View
    {
        $search = $request->input('q');
        $category = $request->input('category');

        $query = CommunityDataset::public()
            ->with('creator')
            ->search($search)
            ->category($category)
            ->ordered();

        $datasets = $query->paginate(24)->withQueryString();

        $categoriesWithCount = collect(CommunityDataset::CATEGORIES)->map(function ($label, $key) {
            $count = CommunityDataset::public()->where('category', $key)->count();
            return ['key' => $key, 'label' => $label, 'count' => $count];
        })->values();

        return view('community::public.community.datasets', [
            'datasets' => $datasets,
            'categoriesWithCount' => $categoriesWithCount,
            'currentSearch' => $search,
            'currentCategory' => $category,
        ]);
    }

    /**
     * عرض مجموعة بيانات واحدة (عام — بدون تسجيل).
     */
    public function publicDatasetShow(CommunityDataset $dataset): View|RedirectResponse
    {
        if ($dataset->status !== CommunityDataset::STATUS_APPROVED || !$dataset->is_active) {
            abort(404);
        }

        return view('community::public.community.dataset-show', [
            'dataset' => $dataset,
            'previewUrl' => route('community.data.preview', $dataset),
        ]);
    }

    /** صفحة المساهمين (عامة — بدون تسجيل دخول) */
    public function contributors(): View
    {
        $contributors = ContributorProfile::approved()
            ->with('user')
            ->orderBy('reviewed_at', 'desc')
            ->get();
        return view('community::portal.contributors.index', compact('contributors'));
    }

    /** صفحة عرض ملف مساهم واحد (مثل الملف التعريفي للمدرب) */
    public function contributorShow(User $user): View
    {
        $profile = ContributorProfile::where('user_id', $user->id)
            ->approved()
            ->with('user')
            ->firstOrFail();
        return view('community::portal.contributors.show', compact('profile'));
    }
}
