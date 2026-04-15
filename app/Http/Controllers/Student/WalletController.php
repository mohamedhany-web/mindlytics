<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function index()
    {
        Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'name' => 'المحفظة الرئيسية',
                'balance' => 0,
                'pending_balance' => 0,
                'currency' => 'EGP',
                'is_active' => true,
            ]
        );

        $wallets = Wallet::where('user_id', auth()->id())
            ->orderBy('id')
            ->get();

        $transactions = WalletTransaction::whereIn('wallet_id', $wallets->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.wallet.index', compact('wallets', 'transactions'));
    }

    public function transfer(Request $request)
    {
        $userId = auth()->id();

        $validated = $request->validate([
            'from_wallet_id' => [
                'required',
                'integer',
                Rule::exists('wallets', 'id')->where(fn ($query) => $query->where('user_id', $userId)->where('is_active', true)),
            ],
            'to_wallet_id' => [
                'required',
                'integer',
                'different:from_wallet_id',
                Rule::exists('wallets', 'id')->where(fn ($query) => $query->where('user_id', $userId)->where('is_active', true)),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'to_wallet_id.different' => __('student.wallet_transfer_different_wallets'),
        ]);

        $amount = round((float) $validated['amount'], 2);

        DB::transaction(function () use ($validated, $amount) {
            $fromWallet = Wallet::where('id', $validated['from_wallet_id'])->lockForUpdate()->firstOrFail();
            $toWallet = Wallet::where('id', $validated['to_wallet_id'])->lockForUpdate()->firstOrFail();

            if ((float) $fromWallet->balance < $amount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => __('student.wallet_transfer_insufficient_balance'),
                ]);
            }

            $reference = 'WTR-' . now()->format('YmdHis') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $notes = trim((string) ($validated['notes'] ?? ''));

            $withdrawDescription = __('student.wallet_transfer_out_description', [
                'to' => $toWallet->name ?: ('#' . $toWallet->id),
                'reference' => $reference,
            ]);

            if ($notes !== '') {
                $withdrawDescription .= ' - ' . $notes;
            }

            $depositDescription = __('student.wallet_transfer_in_description', [
                'from' => $fromWallet->name ?: ('#' . $fromWallet->id),
                'reference' => $reference,
            ]);

            if ($notes !== '') {
                $depositDescription .= ' - ' . $notes;
            }

            $fromWallet->withdraw($amount, $withdrawDescription);
            $toWallet->deposit($amount, null, null, $depositDescription);
        });

        return redirect()->route('student.wallet.index')->with('success', __('student.wallet_transfer_success'));
    }

    public function show($id)
    {
        return $this->index();
    }
}
