<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialConnection;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialPageService;
use App\Support\MetaSocialSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MetaSocialOAuthController extends Controller
{
    public function __construct(
        private MetaSocialGraphService $graph,
        private MetaSocialPageService $pages,
    ) {}

    public function redirect(Request $request): RedirectResponse
    {
        if (! MetaSocialSettings::isAppConfigured()) {
            return redirect()->route('admin.meta-social.settings')
                ->with('error', 'أكمل App ID و App Secret قبل الربط');
        }

        $state = Str::random(40);
        $request->session()->put('meta_social_oauth_state', $state);

        return redirect()->away($this->graph->oauthLoginUrl($state));
    }

    public function callback(Request $request): RedirectResponse
    {
        $expectedState = (string) $request->session()->pull('meta_social_oauth_state', '');
        $state = (string) $request->query('state', '');

        if ($expectedState === '' || ! hash_equals($expectedState, $state)) {
            return redirect()->route('admin.meta-social.settings')
                ->with('error', 'انتهت جلسة OAuth — أعد المحاولة');
        }

        if ($request->filled('error')) {
            return redirect()->route('admin.meta-social.settings')
                ->with('error', 'رفض Meta: ' . $request->query('error_description', $request->query('error')));
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            return redirect()->route('admin.meta-social.settings')
                ->with('error', 'لم يُرجَع authorization code من Meta');
        }

        $short = $this->graph->exchangeCodeForToken($code);
        if (! ($short['success'] ?? false)) {
            return redirect()->route('admin.meta-social.settings')
                ->with('error', $short['error'] ?? 'فشل تبادل الرمز');
        }

        $long = $this->graph->exchangeForLongLivedToken((string) $short['access_token']);
        if (! ($long['success'] ?? false)) {
            return redirect()->route('admin.meta-social.settings')
                ->with('error', $long['error'] ?? 'فشل Long-Lived Token');
        }

        $token = (string) $long['access_token'];
        $me = $this->graph->fetchMe($token);
        if (! ($me['success'] ?? false)) {
            return redirect()->route('admin.meta-social.settings')
                ->with('error', $me['error'] ?? 'تعذّر قراءة حساب Meta');
        }

        MetaSocialConnection::query()
            ->where('status', MetaSocialConnection::STATUS_CONNECTED)
            ->update(['status' => MetaSocialConnection::STATUS_DISCONNECTED]);

        MetaSocialConnection::query()->create([
            'meta_user_id' => $me['id'],
            'meta_user_name' => $me['name'],
            'user_access_token' => $token,
            'token_expires_at' => now()->addSeconds((int) ($long['expires_in'] ?? 5184000)),
            'status' => MetaSocialConnection::STATUS_CONNECTED,
            'connected_by' => auth()->id(),
            'connected_at' => now(),
        ]);

        $sync = $this->pages->syncPagesFromMeta((int) auth()->id());
        if (! ($sync['success'] ?? false)) {
            return redirect()->route('admin.meta-social.pages.index')
                ->with('warning', 'تم الربط لكن فشلت مزامنة الصفحات: ' . ($sync['error'] ?? ''));
        }

        $this->graph->syncAppWebhookSubscription();

        Cache::forget('meta_social:connection_meta');

        return redirect()->route('admin.meta-social.pages.index')
            ->with('success', 'تم ربط Meta Business بنجاح — تمت مزامنة ' . ($sync['synced'] ?? 0) . ' صفحة');
    }

    public function disconnect(): RedirectResponse
    {
        MetaSocialConnection::query()
            ->where('status', MetaSocialConnection::STATUS_CONNECTED)
            ->update(['status' => MetaSocialConnection::STATUS_DISCONNECTED]);

        return redirect()->route('admin.meta-social.settings')
            ->with('success', 'تم قطع ربط حساب Meta');
    }
}
