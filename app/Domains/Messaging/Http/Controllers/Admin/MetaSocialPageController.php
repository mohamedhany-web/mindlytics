<?php

namespace App\Domains\Messaging\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Messaging\Models\MetaSocialConnection;
use App\Domains\Messaging\Models\MetaSocialPage;
use App\Domains\Messaging\Services\MetaSocialGraphService;
use App\Domains\Messaging\Services\MetaSocialInboxService;
use App\Domains\Messaging\Services\MetaSocialPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MetaSocialPageController extends Controller
{
    public function __construct(
        private MetaSocialPageService $pages,
        private MetaSocialGraphService $graph,
        private MetaSocialInboxService $inbox,
    ) {}

    public function index(Request $request)
    {
        $pages = MetaSocialPage::query()
            ->with('connection')
            ->orderByDesc('is_active')
            ->orderBy('page_name')
            ->paginate(50)
            ->withQueryString();

        $connectionMeta = $this->graph->connectionMeta();
        $connections = MetaSocialConnection::connectedAll();
        $showPicker = $request->boolean('pick') || session('show_page_picker');

        return view('messaging::admin.meta-social.pages.index', compact('pages', 'connectionMeta', 'connections', 'showPicker'));
    }

    public function sync(): RedirectResponse
    {
        $result = $this->pages->syncPagesFromMeta((int) auth()->id());

        $message = ($result['success'] ?? false)
            ? 'تمت مزامنة ' . ($result['synced'] ?? 0) . ' صفحة من ' . ($result['connections'] ?? 1) . ' حساب Meta'
            : ($result['error'] ?? 'فشل');

        if (! empty($result['warnings'])) {
            $message .= ' — تحذيرات: ' . implode(' | ', $result['warnings']);
        }

        return back()->with(
            ($result['success'] ?? false) ? 'success' : 'error',
            $message
        );
    }

    public function bulkActivate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_ids' => 'required|array|min:1',
            'page_ids.*' => 'integer|exists:meta_social_pages,id',
        ]);

        $result = $this->pages->activatePages($validated['page_ids']);

        $message = 'تم تفعيل ' . ($result['activated'] ?? 0) . ' صفحة';
        if (! empty($result['errors'])) {
            $message .= ' — بعض الأخطاء: ' . implode(' | ', array_slice($result['errors'], 0, 3));
        }

        return back()->with(
            ($result['success'] ?? false) ? 'success' : 'error',
            $message
        );
    }

    public function bulkDeactivate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_ids' => 'required|array|min:1',
            'page_ids.*' => 'integer|exists:meta_social_pages,id',
        ]);

        $count = $this->pages->deactivatePages($validated['page_ids']);

        return back()->with('success', 'تم إيقاف ' . $count . ' صفحة');
    }

    public function activateAll(): RedirectResponse
    {
        $ids = MetaSocialPage::query()->pluck('id')->all();
        if ($ids === []) {
            return back()->with('error', 'لا توجد صفحات — مزامنة من Meta أولاً');
        }

        $result = $this->pages->activatePages($ids);

        return back()->with(
            ($result['success'] ?? false) ? 'success' : 'error',
            'تم تفعيل ' . ($result['activated'] ?? 0) . ' صفحة من ' . count($ids)
        );
    }

    public function activate(MetaSocialPage $page): RedirectResponse
    {
        $result = $this->pages->activatePage($page);

        return back()->with(
            ($result['success'] ?? false) ? 'success' : 'error',
            ($result['success'] ?? false) ? 'تم تفعيل الصفحة واشتراك Webhook' : ($result['error'] ?? 'فشل')
        );
    }

    public function deactivate(MetaSocialPage $page): RedirectResponse
    {
        $page->update(['is_active' => false]);

        return back()->with('success', 'تم إيقاف الصفحة');
    }

    public function syncConversations(MetaSocialPage $page, Request $request): RedirectResponse
    {
        $result = $this->inbox->syncHistoryFromMeta((int) $page->id);

        $totalConversations = (int) ($result['synced'] ?? 0);
        $totalMessages = (int) ($result['messages'] ?? 0);
        $ok = (bool) ($result['success'] ?? false);
        $message = $ok
            ? "تمت مزامنة التاريخ من Meta: {$totalConversations} محادثة و {$totalMessages} رسالة جديدة"
            : ($result['error'] ?? 'فشل');

        if (! empty($result['errors'])) {
            $message .= ' — '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        $redirect = $request->input('redirect_to') === 'social'
            ? (redirect()->route('admin.crm.reports.social'))
            : redirect()->route('admin.meta-social.inbox.index', ['page' => $page->id]);

        return $redirect->with($ok ? 'success' : 'error', $message);
    }

    /**
     * مزامنة تاريخ كل الصفحات النشطة (أو صفحة محددة) من Meta.
     */
    public function syncHistory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_id' => 'nullable|integer',
            'redirect_to' => 'nullable|string|max:40',
        ]);

        $pageId = isset($validated['page_id']) ? (int) $validated['page_id'] : null;
        if ($pageId === 0) {
            $pageId = null;
        }

        $result = $this->inbox->syncHistoryFromMeta($pageId);
        $ok = (bool) ($result['success'] ?? false);
        $message = $ok
            ? sprintf(
                'تمت مزامنة التاريخ من Meta: %d صفحة · %d محادثة · %d رسالة جديدة',
                (int) ($result['pages'] ?? 0),
                (int) ($result['synced'] ?? 0),
                (int) ($result['messages'] ?? 0)
            )
            : ($result['error'] ?? 'فشل المزامنة من Meta');

        if (! empty($result['errors'])) {
            $message .= ' — '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        $redirectTo = (string) ($validated['redirect_to'] ?? '');
        if ($redirectTo === 'social' && \Illuminate\Support\Facades\Route::has('admin.crm.reports.social')) {
            return redirect()->route('admin.crm.reports.social')->with($ok ? 'success' : 'error', $message);
        }
        if ($redirectTo === 'inbox') {
            return redirect()->route('admin.meta-social.inbox.index', array_filter([
                'page' => $pageId,
            ]))->with($ok ? 'success' : 'error', $message);
        }

        return back()->with($ok ? 'success' : 'error', $message);
    }
}
