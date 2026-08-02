<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialAgentLink;
use App\Models\MetaSocialPage;
use App\Services\MetaSocial\MetaSocialAgentLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaSocialAgentLinkController extends Controller
{
    public function __construct(
        private MetaSocialAgentLinkService $agents,
    ) {}

    public function index(): View
    {
        $ready = $this->agents->ready();
        $links = $ready ? $this->agents->allLinks() : collect();
        $employees = $ready ? $this->agents->linkableEmployees() : [];
        $pages = $ready
            ? MetaSocialPage::query()->where('is_active', true)->orderBy('page_name')->get(['id', 'page_name'])
            : collect();

        return view('admin.meta-social.agents', compact('ready', 'links', 'employees', 'pages'));
    }

    public function sync(Request $request): RedirectResponse
    {
        $pageId = $request->filled('page_id') ? (int) $request->input('page_id') : null;
        $result = $this->agents->syncFromMeta($pageId);

        if (! ($result['success'] ?? false) && empty($result['synced'])) {
            return back()->with('error', implode(' · ', $result['errors'] ?? ['فشل المزامنة من Meta']));
        }

        $msg = 'تم جلب '.(int) ($result['synced'] ?? 0).' يوزر من أكسس Meta';
        if (! empty($result['errors'])) {
            $msg .= ' — تنبيهات: '.implode(' · ', array_slice($result['errors'], 0, 3));
        }

        return back()->with('success', $msg);
    }

    public function update(Request $request, MetaSocialAgentLink $agent): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        try {
            $this->agents->linkToEmployee($agent, isset($validated['user_id']) ? (int) $validated['user_id'] : null);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم ربط يوزر Meta بالموظف في السيستم');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meta_user_id' => 'required|string|max:64',
            'meta_user_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $this->agents->addManual(
            $validated['meta_user_id'],
            $validated['meta_user_name'] ?? null,
            isset($validated['user_id']) ? (int) $validated['user_id'] : null,
        );

        return back()->with('success', 'تمت إضافة يوزر Meta وربطه (إن اخترت موظفًا)');
    }
}
