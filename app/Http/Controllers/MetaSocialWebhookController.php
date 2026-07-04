<?php

namespace App\Http\Controllers;

use App\Models\MetaSocialPage;
use App\Services\MetaSocial\MetaSocialInboxService;
use App\Support\MetaSocialSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MetaSocialWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $mode = (string) ($request->query('hub_mode') ?? $request->input('hub.mode') ?? '');
        $token = (string) ($request->query('hub_verify_token') ?? $request->input('hub.verify_token') ?? '');
        $challenge = (string) ($request->query('hub_challenge') ?? $request->input('hub.challenge') ?? '');
        $expected = MetaSocialSettings::webhookVerifyToken();

        if ($mode === 'subscribe' && $expected !== '' && hash_equals($expected, $token)) {
            return response($challenge, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        return response('Forbidden', 403, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function handle(Request $request, MetaSocialInboxService $inbox): Response
    {
        $payload = $request->all();
        if ($payload === []) {
            $decoded = json_decode((string) $request->getContent(), true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        Log::info('Meta Social webhook received', ['object' => $payload['object'] ?? null]);

        MetaSocialSettings::recordWebhookHit();

        if (($payload['object'] ?? '') !== 'page') {
            return response()->json(['status' => 'ignored']);
        }

        foreach ($payload['entry'] ?? [] as $entry) {
            $pageId = (string) ($entry['id'] ?? '');
            $page = MetaSocialPage::query()->where('page_id', $pageId)->where('is_active', true)->first();
            if (! $page) {
                continue;
            }

            foreach ($entry['messaging'] ?? [] as $event) {
                $platform = $this->detectPlatform($event, $page);
                $inbox->ingestMessagingEvent($page, $event, $platform);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function detectPlatform(array $event, MetaSocialPage $page): string
    {
        if ($page->instagram_business_id && isset($event['recipient']['id'])
            && (string) $event['recipient']['id'] === (string) $page->instagram_business_id) {
            return \App\Models\MetaSocialConversation::PLATFORM_INSTAGRAM;
        }

        return \App\Models\MetaSocialConversation::PLATFORM_MESSENGER;
    }
}
