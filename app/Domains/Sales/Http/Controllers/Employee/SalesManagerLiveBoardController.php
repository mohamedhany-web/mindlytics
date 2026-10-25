<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Services\SalesLiveBoardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerLiveBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('sales.manager');
    }

    public function index(Request $request, SalesLiveBoardService $board): View
    {
        $data = $board->boardForManager(Auth::user());

        if ($request->boolean('partial')) {
            return view('sales::manager.live-board-partial', $data);
        }

        return view('sales::manager.live-board', $data);
    }
}
