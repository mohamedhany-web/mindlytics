<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Http\Controllers\Crm\Concerns\InteractsWithCrm;
use App\Domains\Sales\Services\SalesLeadHealthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** «حالة العملاء» للمدير والأدمن: كل موظف عنده كام عميل سليم وكام عميل عنده مشكلة (docs/SALES_CRM.md). */
class CrmLeadHealthController extends Controller
{
    use InteractsWithCrm;

    public function index(Request $request, SalesLeadHealthService $health): View
    {
        $scope = $this->scope($request);
        abort_if($scope->isRep(), 404);

        return $this->crmView($scope, 'leads', 'leads.health', $health->board($scope) + ['buckets' => SalesLeadHealthService::BUCKETS]);
    }
}
