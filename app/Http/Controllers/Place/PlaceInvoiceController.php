<?php

namespace App\Http\Controllers\Place;

use App\Http\Controllers\Controller;
use App\Models\PlaceInvoice;
use Illuminate\Http\Request;

class PlaceInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $location = view()->shared('resolvedPlaceLocation');

        $invoices = PlaceInvoice::query()
            ->where('offline_location_id', $location->id)
            ->with('settlement')
            ->latest('issued_at')
            ->paginate(15);

        return view('place-office.invoices.index', compact('location', 'invoices'));
    }

    public function show(PlaceInvoice $invoice)
    {
        $location = view()->shared('resolvedPlaceLocation');
        abort_unless((int) $invoice->offline_location_id === (int) $location->id, 403);

        $invoice->load('settlement');

        return view('place-office.invoices.show', compact('location', 'invoice'));
    }
}
