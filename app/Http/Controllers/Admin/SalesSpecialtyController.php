<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesSpecialtyService;
use Illuminate\Http\Request;

class SalesSpecialtyController extends Controller
{
    public function index(SalesSpecialtyService $specialtyService)
    {
        $types = $specialtyService->activeTypes();
        $reps = User::salesEmployees()
            ->where('is_active', true)
            ->with('salesInterestTypes')
            ->orderBy('name')
            ->get();

        return view('admin.sales.specialties.index', compact('types', 'reps'));
    }

    public function update(Request $request, User $user, SalesSpecialtyService $specialtyService)
    {
        if (! $user->isSalesEmployee()) {
            abort(404);
        }

        $validated = $request->validate([
            'interest_type_ids' => 'nullable|array',
            'interest_type_ids.*' => 'integer|exists:sales_interest_types,id',
        ]);

        $specialtyService->syncForUser($user, $validated['interest_type_ids'] ?? []);

        return back()->with('success', 'تم تحديث تخصصات '.$user->name);
    }
}
