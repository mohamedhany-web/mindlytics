<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConnection;
use App\Models\MetaSocialConversation;
use App\Models\MetaSocialMessage;
use App\Models\MetaSocialPage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class MetaSocialPageService
{
    public function __construct(
        private MetaSocialGraphService $graph,
    ) {}

    /**
     * @return array{success: bool, synced?: int, error?: string}
     */
    public function syncPagesFromMeta(?int $connectedBy = null): array
    {
        $connection = MetaSocialConnection::active();
        if (! $connection) {
            return ['success' => false, 'error' => 'لا يوجد ربط Meta نشط'];
        }

        $result = $this->graph->fetchManagedPages((string) $connection->user_access_token);
        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل جلب الصفحات'];
        }

        $synced = 0;
        foreach ($result['pages'] ?? [] as $row) {
            $ig = is_array($row['instagram_business_account'] ?? null) ? $row['instagram_business_account'] : null;
            $picture = is_array($row['picture']['data'] ?? null) ? ($row['picture']['data']['url'] ?? null) : null;

            MetaSocialPage::query()->updateOrCreate(
                ['page_id' => (string) $row['id']],
                [
                    'page_name' => (string) ($row['name'] ?? 'صفحة'),
                    'page_username' => $row['username'] ?? null,
                    'page_access_token' => (string) ($row['access_token'] ?? ''),
                    'picture_url' => $picture,
                    'category' => $row['category'] ?? null,
                    'instagram_business_id' => $ig['id'] ?? null,
                    'instagram_username' => $ig['username'] ?? null,
                    'instagram_profile_picture' => $ig['profile_picture_url'] ?? null,
                    'is_active' => true,
                    'last_synced_at' => now(),
                    'connected_by' => $connectedBy,
                    'meta' => ['raw' => ['id' => $row['id']]],
                ],
            );
            $synced++;
        }

        return ['success' => true, 'synced' => $synced];
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function activatePage(MetaSocialPage $page): array
    {
        $sub = $this->graph->subscribePageWebhook($page);
        if (! ($sub['success'] ?? false)) {
            return $sub;
        }

        $page->update(['is_active' => true, 'webhook_subscribed_at' => now()]);

        return ['success' => true];
    }
}
