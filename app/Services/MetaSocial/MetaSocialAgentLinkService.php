<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialAgentLink;
use App\Models\MetaSocialPage;
use App\Models\User;
use App\Services\WhatsAppAssignmentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class MetaSocialAgentLinkService
{
    public function __construct(
        private MetaSocialGraphService $graph,
        private WhatsAppAssignmentService $assignment,
    ) {}

    public function ready(): bool
    {
        return Schema::hasTable('meta_social_agent_links');
    }

    /**
     * @return Collection<int, MetaSocialAgentLink>
     */
    public function allLinks(): Collection
    {
        if (! $this->ready()) {
            return collect();
        }

        return MetaSocialAgentLink::query()
            ->with('user:id,name,email,is_employee,is_active,employee_job_id')
            ->orderByRaw('user_id is null desc')
            ->orderBy('meta_user_name')
            ->get();
    }

    /**
     * @return list<User>
     */
    public function linkableEmployees(): array
    {
        return User::query()
            ->where('is_active', true)
            ->where('is_employee', true)
            ->with('employeeJob:id,name,code')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'employee_job_id'])
            ->all();
    }

    /**
     * سحب يوزرز الأكسس من Meta (Page Roles + Assigned Users إن أمكن).
     *
     * @return array{success: bool, synced?: int, errors?: list<string>}
     */
    public function syncFromMeta(?int $pageLocalId = null): array
    {
        if (! $this->ready()) {
            return ['success' => false, 'errors' => ['شغّل migrate أولاً: meta_social_agent_links']];
        }

        $query = MetaSocialPage::query()->where('is_active', true)->whereNotNull('page_access_token');
        if ($pageLocalId) {
            $query->where('id', $pageLocalId);
        }
        $pages = $query->get();
        if ($pages->isEmpty()) {
            return ['success' => false, 'errors' => ['لا توجد صفحات نشطة مربوطة']];
        }

        $synced = 0;
        $errors = [];

        foreach ($pages as $page) {
            $result = $this->syncPageAgents($page);
            $synced += (int) ($result['synced'] ?? 0);
            foreach ($result['errors'] ?? [] as $err) {
                $errors[] = $page->page_name.': '.$err;
            }
        }

        return [
            'success' => $synced > 0 || $errors === [],
            'synced' => $synced,
            'errors' => $errors,
        ];
    }

    /**
     * @return array{synced: int, errors: list<string>}
     */
    public function syncPageAgents(MetaSocialPage $page): array
    {
        $token = (string) $page->page_access_token;
        if ($token === '') {
            return ['synced' => 0, 'errors' => ['لا يوجد Page Access Token']];
        }

        $discovered = [];
        $errors = [];

        // 1) Page Roles — أشخاص لديهم دور على الصفحة (غير Business في بعض الحالات)
        $roles = $this->fetchEdge($page->page_id, 'roles', $token, 'id,name,tasks');
        if (! ($roles['success'] ?? false)) {
            $errors[] = 'roles: '.($roles['error'] ?? 'فشل');
        } else {
            foreach ($roles['data'] ?? [] as $row) {
                $id = (string) ($row['id'] ?? '');
                if ($id === '') {
                    continue;
                }
                $discovered[$id] = [
                    'meta_user_id' => $id,
                    'meta_user_name' => (string) ($row['name'] ?? $id),
                    'tasks' => $row['tasks'] ?? [],
                    'source' => 'roles',
                ];
            }
        }

        // 2) Assigned Users — يحتاج business_id (من إعدادات الصفحة / الاتصال إن وُجد)
        $businessId = $this->resolveBusinessId($page);
        if ($businessId) {
            $assigned = $this->fetchEdge(
                $page->page_id,
                'assigned_users',
                $token,
                'id,name,email,tasks',
                ['business' => $businessId]
            );
            if (! ($assigned['success'] ?? false)) {
                $errors[] = 'assigned_users: '.($assigned['error'] ?? 'فشل');
            } else {
                foreach ($assigned['data'] ?? [] as $row) {
                    $id = (string) ($row['id'] ?? '');
                    if ($id === '') {
                        continue;
                    }
                    $discovered[$id] = [
                        'meta_user_id' => $id,
                        'meta_user_name' => (string) ($row['name'] ?? ($discovered[$id]['meta_user_name'] ?? $id)),
                        'meta_user_email' => (string) ($row['email'] ?? ''),
                        'tasks' => $row['tasks'] ?? ($discovered[$id]['tasks'] ?? []),
                        'source' => 'assigned_users',
                    ];
                }
            }
        }

        $synced = 0;
        foreach ($discovered as $agent) {
            $existing = MetaSocialAgentLink::query()->where('meta_user_id', $agent['meta_user_id'])->first();
            $pageIds = is_array($existing?->page_ids) ? $existing->page_ids : [];
            if (! in_array((int) $page->id, array_map('intval', $pageIds), true)) {
                $pageIds[] = (int) $page->id;
            }

            MetaSocialAgentLink::query()->updateOrCreate(
                ['meta_user_id' => $agent['meta_user_id']],
                [
                    'meta_user_name' => $agent['meta_user_name'] ?: $existing?->meta_user_name,
                    'meta_user_email' => ($agent['meta_user_email'] ?? null) ?: $existing?->meta_user_email,
                    'tasks' => $agent['tasks'] ?? $existing?->tasks,
                    'page_ids' => array_values(array_unique($pageIds)),
                    'source' => $agent['source'] ?? 'roles',
                    'last_synced_at' => now(),
                ]
            );
            $synced++;
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    public function linkToEmployee(MetaSocialAgentLink $link, ?int $userId): MetaSocialAgentLink
    {
        if ($userId) {
            $user = User::query()->findOrFail($userId);
            if (! $user->is_employee || ! $user->is_active) {
                abort(422, 'اختر موظفًا نشطًا فقط');
            }
        }

        $link->update(['user_id' => $userId ?: null]);

        return $link->fresh(['user']);
    }

    public function addManual(string $metaUserId, ?string $name = null, ?int $userId = null): MetaSocialAgentLink
    {
        $metaUserId = trim($metaUserId);
        $link = MetaSocialAgentLink::query()->updateOrCreate(
            ['meta_user_id' => $metaUserId],
            [
                'meta_user_name' => $name ?: $metaUserId,
                'source' => 'manual',
                'last_synced_at' => now(),
            ]
        );

        if ($userId) {
            $this->linkToEmployee($link, $userId);
        }

        return $link->fresh(['user']);
    }

    /**
     * موظفو المبيعات + أي موظف مربوط بيوزر Meta (للتعيين في الإنبوكس).
     *
     * @return list<User>
     */
    public function eligibleAgentsMerged(): array
    {
        $sales = $this->assignment->eligibleSalesStaff();
        $byId = [];
        foreach ($sales as $u) {
            $byId[(int) $u->id] = $u;
        }

        if ($this->ready()) {
            $linkedIds = MetaSocialAgentLink::query()
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->all();
            if ($linkedIds !== []) {
                User::query()
                    ->whereIn('id', $linkedIds)
                    ->where('is_active', true)
                    ->where('is_employee', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'role'])
                    ->each(function (User $u) use (&$byId) {
                        $byId[(int) $u->id] = $u;
                    });
            }
        }

        return array_values($byId);
    }

    public function findLocalUserIdByMetaUserId(?string $metaUserId): ?int
    {
        if (! $metaUserId || ! $this->ready()) {
            return null;
        }

        $id = MetaSocialAgentLink::query()
            ->where('meta_user_id', $metaUserId)
            ->whereNotNull('user_id')
            ->value('user_id');

        return $id ? (int) $id : null;
    }

    private function resolveBusinessId(MetaSocialPage $page): ?string
    {
        $meta = is_array($page->meta) ? $page->meta : [];
        foreach (['business_id', 'business', 'meta_business_id'] as $key) {
            if (! empty($meta[$key])) {
                return (string) $meta[$key];
            }
        }

        $page->loadMissing('connection');
        $connMeta = is_array($page->connection?->meta) ? $page->connection->meta : [];
        foreach (['business_id', 'business'] as $key) {
            if (! empty($connMeta[$key])) {
                return (string) $connMeta[$key];
            }
        }

        // محاولة جلب business من Graph
        try {
            $response = Http::timeout(20)->get($this->graph->graphUrl().'/'.$page->page_id, [
                'fields' => 'business{id,name}',
                'access_token' => $page->page_access_token,
            ]);
            if ($response->successful()) {
                $bid = data_get($response->json(), 'business.id');
                if ($bid) {
                    $meta['business_id'] = (string) $bid;
                    $page->update(['meta' => $meta]);

                    return (string) $bid;
                }
            }
        } catch (\Throwable) {
            // ignore
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    private function fetchEdge(string $pageId, string $edge, string $token, string $fields, array $extra = []): array
    {
        try {
            $params = array_merge([
                'fields' => $fields,
                'access_token' => $token,
                'limit' => 100,
            ], $extra);

            $response = Http::timeout(30)->get($this->graph->graphUrl().'/'.$pageId.'/'.$edge, $params);
            if (! $response->successful()) {
                $json = $response->json() ?? [];
                $msg = data_get($json, 'error.message') ?: ('HTTP '.$response->status());

                return ['success' => false, 'error' => $msg];
            }

            return [
                'success' => true,
                'data' => $response->json('data') ?? [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
