<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\WorkshopPromoService;
use Illuminate\Http\Request;

class WorkshopPromoController extends Controller
{
    public function activate(Request $request, WorkshopPromoService $workshopPromoService)
    {
        $request->validate([
            'promo_code' => 'required|string|max:32',
        ]);

        $result = $workshopPromoService->activateForUser(
            $request->user(),
            $request->input('promo_code')
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
