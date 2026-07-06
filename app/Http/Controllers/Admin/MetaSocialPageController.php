<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialConnection;
use App\Models\MetaSocialPage;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialInboxService;
use App\Services\MetaSocial\MetaSocialPageService;
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

        return view('admin.meta-social.pages.index', compact('pages', 'connectionMeta', 'connections', 'showPicker'));
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
        $platform = $request->query('platform', 'messenger');
        $result = $this->inbox->syncConversationsForPage($page, $platform);

        if ($page->hasInstagram()) {
            $this->inbox->syncConversationsForPage($page, 'instagram');
        }

        return redirect()->route('admin.meta-social.inbox.index', ['page' => $page->id])
            ->with(
                ($result['success'] ?? false) ? 'success' : 'error',
                ($result['success'] ?? false) ? 'تمت مزامنة المحادثات' : ($result['error'] ?? 'فشل')
            );
    }
}
