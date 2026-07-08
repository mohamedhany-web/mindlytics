<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\EmployeePresenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeePresenceController extends Controller
{
    public function __construct(
        private EmployeePresenceService $presence,
    ) {}

    public function heartbeat(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->isEmployee(), 403);

        $result = $this->presence->heartbeat($user, $request);

        if (! ($result['success'] ?? false)) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->isEmployee(), 403);

        return response()->json([
            'success' => true,
            'presence' => $this->presence->memberPresenceStatus($user),
            'interval' => $this->presence->heartbeatInterval(),
        ]);
    }
}
