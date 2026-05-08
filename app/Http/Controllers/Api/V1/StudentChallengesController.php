<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommunityCompetition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * تحديات الطالب في التطبيق — قائمة تحديات/مسابقات قابلة للإدارة من لوحة التحكم.
 */
class StudentChallengesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min(48, $perPage));

        $rows = CommunityCompetition::query()
            ->active()
            ->ordered()
            ->paginate($perPage);

        $data = $rows->getCollection()->map(function (CommunityCompetition $c) {
            return [
                'id' => $c->id,
                'title' => (string) $c->title,
                'description' => $c->description ? (string) $c->description : null,
                'start_at' => $c->start_at?->toISOString(),
                'end_at' => $c->end_at?->toISOString(),
                'rules' => $c->rules ? (string) $c->rules : null,
            ];
        })->values();

        return response()->json([
            'competitions' => $data,
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ]);
    }
}

