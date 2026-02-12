<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorPayoutDetail;
use Illuminate\Http\Request;

class TransferAccountController extends Controller
{
    /**
     * صفحة حساب التحويل - عرض وتعديل بيانات استلام المبالغ
     */
    public function index()
    {
        $user = auth()->user();
        $detail = $user->payoutDetail;

        if (!$detail) {
            $detail = new InstructorPayoutDetail(['user_id' => $user->id]);
        }

        return view('instructor.transfer-account.index', compact('detail'));
    }

    /**
     * حفظ بيانات حساب التحويل
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ], [
            'bank_name.max' => 'اسم البنك لا يتجاوز 255 حرفاً',
            'account_holder_name.max' => 'اسم صاحب الحساب لا يتجاوز 255 حرفاً',
        ]);

        $user = auth()->user();
        $user->payoutDetail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'iban' => $request->iban,
                'branch_name' => $request->branch_name,
                'swift_code' => $request->swift_code,
                'notes' => $request->notes,
            ]
        );

        return redirect()->route('instructor.transfer-account.index')
            ->with('success', 'تم حفظ بيانات حساب التحويل بنجاح. سيتم استخدامها عند تحويل مستحقاتك.');
    }
}
