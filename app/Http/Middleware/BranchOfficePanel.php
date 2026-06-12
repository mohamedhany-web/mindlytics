<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Support\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * يربط طلبات مسار /branch-office بفرع المستخدم (مدير الفرع) ويمنع التضارب مع فرع آخر من المضيف.
 */
class BranchOfficePanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->role !== 'branch_manager') {
            abort(403, 'هذه المنطقة مخصصة لمديري الفروع.');
        }

        $branch = Branch::query()->whereKey($user->branch_id)->whereNull('deleted_at')->first();
        if (! $branch) {
            abort(403, 'لم يُربط حسابك بفرع صالح. تواصل مع الإدارة المركزية.');
        }

        $fromHost = app(BranchContext::class)->branch;
        if ($fromHost && (int) $fromHost->id !== (int) $branch->id) {
            abort(403, 'أنت مسجل كمدير لفرع مختلف عن الموقع الذي تزوره.');
        }

        App::instance(BranchContext::class, new BranchContext($branch));
        view()->share('resolvedBranch', $branch);

        return $next($request);
    }
}
