<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserDeletionService
{
    /**
     * @return array{unassigned_leads: int}
     */
    public function deleteEmployee(User $employee): array
    {
        if (! $employee->is_employee) {
            throw ValidationException::withMessages([
                'employee' => 'المستخدم المحدد ليس موظفاً.',
            ]);
        }

        if ($employee->id === auth()->id()) {
            throw ValidationException::withMessages([
                'employee' => 'لا يمكنك حذف حسابك الحالي.',
            ]);
        }

        return DB::transaction(function () use ($employee) {
            $unassignedLeads = SalesLead::query()
                ->where('assigned_to', $employee->id)
                ->update(['assigned_to' => null]);

            try {
                $employee->delete();
            } catch (QueryException $e) {
                if ($this->isForeignKeyViolation($e)) {
                    throw ValidationException::withMessages([
                        'employee' => 'لا يمكن حذف الموظف لوجود سجلات مرتبطة به في النظام. راجع العملاء المحتملين والمهام والسجلات المالية ثم حاول مرة أخرى.',
                    ]);
                }

                throw $e;
            }

            return ['unassigned_leads' => $unassignedLeads];
        });
    }

    private function isForeignKeyViolation(QueryException $e): bool
    {
        $code = (string) ($e->errorInfo[1] ?? '');

        return $code === '1451' || str_contains($e->getMessage(), 'Integrity constraint violation');
    }
}
