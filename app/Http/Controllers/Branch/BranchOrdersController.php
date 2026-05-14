<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchOrdersController extends Controller
{
    public function index(Request $request): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $q = Order::query()
            ->where('branch_id', $branch->id)
            ->with(['user', 'course', 'learningPath'])
            ->orderByDesc('id');

        if ($request->filled('status') && in_array($request->input('status'), [Order::STATUS_PENDING, Order::STATUS_APPROVED, Order::STATUS_REJECTED], true)) {
            $q->where('status', $request->input('status'));
        }

        $orders = $q->paginate(25)->withQueryString();

        return view('branch-office.orders', compact('branch', 'orders'));
    }
}
