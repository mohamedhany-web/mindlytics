<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchUsersController extends Controller
{
    public function index(Request $request): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $q = User::query()->where('branch_id', $branch->id)->orderByDesc('id');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            if ($search !== '') {
                $like = '%'.$search.'%';
                $q->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            }
        }

        $users = $q->paginate(25)->withQueryString();

        return view('branch-office.users', compact('branch', 'users'));
    }
}
