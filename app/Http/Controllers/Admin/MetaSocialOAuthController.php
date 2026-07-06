<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialConnection;
use App\Models\MetaSocialPage;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialPageService;
use App\Support\MetaSocialSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

        $userId = auth()->id();
        if ($userId) {
            Cache::put($this->oauthStateCacheKey($state), $userId, now()->addMinutes(20));
        }

        return redirect()->away($this->graph->oauthLoginUrl($state));
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            if (! Schema::hasTable('meta_social_connections')) {
                return redirect()->route('admin.meta-social.settings')
                    ->with('error', 'جداول Meta Social غير موجودة — نفّذ: php artisan migrate --force');
            }

            $state = (string) $request->query('state', '');
            $expectedState = (string) $request->session()->pull('meta_social_oauth_state', '');
            $cachedUserId = $state !== '' ? Cache::pull($this->oauthStateCacheKey($state)) : null;

            if ($expectedState === '' || $state === '' || ! hash_equals($expectedState, $state)) {
                return redirect()->route('admin.meta-social.settings')
                    ->with('error', 'انتهت جلسة OAuth — أعد المحاولة من نفس المتصفح بعد تسجيل الدخول');
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
                    ->with('error', $short['error'] ?? 'فشل تبادل الرمز — تأكد أن Redirect URI في Meta يطابق: ' . MetaSocialSettings::oauthRedirectUrl());
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

            $connectedBy = auth()->id() ?: (is_numeric($cachedUserId) ? (int) $cachedUserId : null);

            MetaSocialConnection::query()->updateOrCreate(
                ['meta_user_id' => $me['id']],
                [
                    'meta_user_name' => $me['name'],
                    'user_access_token' => $token,
                    'token_expires_at' => now()->addSeconds((int) ($long['expires_in'] ?? 5184000)),
                    'status' => MetaSocialConnection::STATUS_CONNECTED,
                    'connected_by' => $connectedBy,
                    'connected_at' => now(),
                ],
            );

            $sync = $this->pages->syncPagesFromMeta($connectedBy);
            if (! ($sync['success'] ?? false)) {
                return redirect()->route('admin.meta-social.pages.index')
                    ->with('error', 'تم الربط لكن فشلت مزامنة الصفحات: ' . ($sync['error'] ?? ''));
            }

            $this->graph->syncAppWebhookSubscription();

            Cache::forget('meta_social:connection_meta');

            $synced = (int) ($sync['synced'] ?? 0);
            $accounts = (int) ($sync['connections'] ?? 1);

            return redirect()->route('admin.meta-social.pages.index', ['pick' => 1])
                ->with('success', "تم ربط Meta — تم جلب {$synced} صفحة من {$accounts} حساب. اختر الصفحات التي تريد تفعيلها.");
        } catch (\Throwable $e) {
            Log::error('Meta Social OAuth callback failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $message = app()->environment('local', 'testing')
                ? 'فشل ربط Meta: ' . $e->getMessage()
                : 'فشل ربط Meta — تحقق من migrate والإعدادات ثم أعد المحاولة';

            return redirect()->route('admin.meta-social.settings')->with('error', $message);
        }
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $connectionId = (int) $request->input('connection_id');

        if ($connectionId > 0) {
            $connection = MetaSocialConnection::query()->find($connectionId);
            if ($connection) {
                $connection->update(['status' => MetaSocialConnection::STATUS_DISCONNECTED]);
                MetaSocialPage::query()
                    ->where('meta_social_connection_id', $connection->id)
                    ->update(['is_active' => false]);

                return redirect()->route('admin.meta-social.pages.index')
                    ->with('success', 'تم قطع ربط حساب Meta: ' . ($connection->meta_user_name ?: '—'));
            }
        }

        MetaSocialConnection::query()
            ->where('status', MetaSocialConnection::STATUS_CONNECTED)
            ->update(['status' => MetaSocialConnection::STATUS_DISCONNECTED]);

        MetaSocialPage::query()->update(['is_active' => false]);

        return redirect()->route('admin.meta-social.settings')
            ->with('success', 'تم قطع ربط جميع حسابات Meta');
    }

    private function oauthStateCacheKey(string $state): string
    {
        return 'meta_social_oauth_state:' . $state;
    }
}

