<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function index()
    {
        $pages = MetaSocialPage::query()->orderByDesc('is_active')->orderBy('page_name')->paginate(20);
        $connectionMeta = $this->graph->connectionMeta();

        return view('admin.meta-social.pages.index', compact('pages', 'connectionMeta'));
    }

    public function sync(): RedirectResponse
    {
        $result = $this->pages->syncPagesFromMeta((int) auth()->id());

        return back()->with(
            ($result['success'] ?? false) ? 'success' : 'error',
            ($result['success'] ?? false)
                ? 'تمت مزامنة ' . ($result['synced'] ?? 0) . ' صفحة من Meta'
                : ($result['error'] ?? 'فشل')
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
