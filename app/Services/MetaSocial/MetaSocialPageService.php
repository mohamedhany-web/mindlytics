<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConnection;
use App\Models\MetaSocialPage;

class MetaSocialPageService
{
    public function __construct(
        private MetaSocialGraphService $graph,
    ) {}

    /**
     * @return array{success: bool, synced?: int, connections?: int, error?: string}
     */
    public function syncPagesFromMeta(?int $connectedBy = null, ?int $connectionId = null): array
    {
        $connections = $connectionId
            ? MetaSocialConnection::query()->where('id', $connectionId)->where('status', MetaSocialConnection::STATUS_CONNECTED)->get()
            : MetaSocialConnection::connectedAll();

        if ($connections->isEmpty()) {
            return ['success' => false, 'error' => 'لا يوجد ربط Meta نشط — سجّل الدخول أولاً'];
        }

        $totalSynced = 0;
        $errors = [];

        foreach ($connections as $connection) {
            $result = $this->syncPagesForConnection($connection, $connectedBy);
            if ($result['success'] ?? false) {
                $totalSynced += (int) ($result['synced'] ?? 0);
            } else {
                $errors[] = ($connection->meta_user_name ?: 'Meta') . ': ' . ($result['error'] ?? 'فشل');
            }
        }

        if ($totalSynced === 0 && $errors !== []) {
            return ['success' => false, 'error' => implode(' | ', $errors)];
        }

        return [
            'success' => true,
            'synced' => $totalSynced,
            'connections' => $connections->count(),
            'warnings' => $errors,
        ];
    }

    /**
     * @return array{success: bool, synced?: int, error?: string}
     */
    public function syncPagesForConnection(MetaSocialConnection $connection, ?int $connectedBy = null): array
    {
        $result = $this->graph->fetchManagedPages((string) $connection->user_access_token);
        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل جلب الصفحات'];
        }

        $synced = 0;
        $seenPageIds = [];

        foreach ($result['pages'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $pageId = (string) ($row['id'] ?? '');
            if ($pageId === '') {
                continue;
            }

            $seenPageIds[] = $pageId;
            $existing = MetaSocialPage::query()->where('page_id', $pageId)->first();

            $ig = is_array($row['instagram_business_account'] ?? null) ? $row['instagram_business_account'] : null;
            $picture = null;
            if (is_array($row['picture'] ?? null)) {
                $pictureData = $row['picture']['data'] ?? null;
                if (is_array($pictureData)) {
                    $picture = $pictureData['url'] ?? null;
                }
            }
            $picture = $picture ? mb_substr((string) $picture, 0, 512) : null;
            $igPicture = $ig['profile_picture_url'] ?? null;
            $igPicture = $igPicture ? mb_substr((string) $igPicture, 0, 512) : null;

            MetaSocialPage::query()->updateOrCreate(
                ['page_id' => $pageId],
                [
                    'meta_social_connection_id' => $connection->id,
                    'page_name' => (string) ($row['name'] ?? 'صفحة'),
                    'page_username' => $row['username'] ?? null,
                    'page_access_token' => (string) ($row['access_token'] ?? ''),
                    'picture_url' => $picture,
                    'category' => $row['category'] ?? null,
                    'instagram_business_id' => $ig['id'] ?? null,
                    'instagram_username' => $ig['username'] ?? null,
                    'instagram_profile_picture' => $igPicture,
                    'is_active' => $existing?->is_active ?? false,
                    'last_synced_at' => now(),
                    'connected_by' => $connectedBy ?? $existing?->connected_by,
                    'meta' => ['raw' => ['id' => $row['id'] ?? null], 'connection_id' => $connection->id],
                ],
            );
            $synced++;
        }

        return ['success' => true, 'synced' => $synced];
    }

    /**
     * @param  list<int>  $pageIds
     * @return array{success: bool, activated?: int, errors?: list<string>}
     */
    public function activatePages(array $pageIds): array
    {
        $activated = 0;
        $errors = [];

        foreach (MetaSocialPage::query()->whereIn('id', $pageIds)->get() as $page) {
            $result = $this->activatePage($page);
            if ($result['success'] ?? false) {
                $activated++;
            } else {
                $errors[] = $page->page_name . ': ' . ($result['error'] ?? 'فشل');
            }
        }

        return [
            'success' => $activated > 0 || $errors === [],
            'activated' => $activated,
            'errors' => $errors,
        ];
    }

    /**
     * @param  list<int>  $pageIds
     */
    public function deactivatePages(array $pageIds): int
    {
        return MetaSocialPage::query()->whereIn('id', $pageIds)->update(['is_active' => false]);
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
