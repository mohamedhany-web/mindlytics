<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_salary_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_salary_payments', 'total_additions')) {
                $table->decimal('total_additions', 10, 2)->default(0)->after('total_deductions');
            }
            if (! Schema::hasColumn('employee_salary_payments', 'period_month')) {
                $table->unsignedTinyInteger('period_month')->nullable()->after('net_salary');
            }
            if (! Schema::hasColumn('employee_salary_payments', 'period_year')) {
                $table->unsignedSmallInteger('period_year')->nullable()->after('period_month');
            }
            if (! Schema::hasColumn('employee_salary_payments', 'wallet_id')) {
                $table->unsignedBigInteger('wallet_id')->nullable()->after('transfer_receipt_path');
            }
            if (! Schema::hasColumn('employee_salary_payments', 'expense_id')) {
                $table->unsignedBigInteger('expense_id')->nullable()->after('wallet_id');
            }
        });

        if (Schema::hasColumn('employee_salary_payments', 'period_month')) {
            Schema::table('employee_salary_payments', function (Blueprint $table) {
                $table->index(['period_year', 'period_month'], 'idx_emp_pay_period');
            });
        }

        if (Schema::hasTable('wallets') && Schema::hasColumn('employee_salary_payments', 'wallet_id')) {
            Schema::table('employee_salary_payments', function (Blueprint $table) {
                $table->foreign('wallet_id', 'fk_emp_pay_wallet')
                    ->references('id')->on('wallets')->nullOnDelete();
            });
        }

        if (Schema::hasTable('expenses') && Schema::hasColumn('employee_salary_payments', 'expense_id')) {
            Schema::table('employee_salary_payments', function (Blueprint $table) {
                $table->foreign('expense_id', 'fk_emp_pay_expense')
                    ->references('id')->on('expenses')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('employee_salary_payments', function (Blueprint $table) {
            foreach (['wallet_id', 'expense_id'] as $fk) {
                try {
                    $table->dropForeign([$fk]);
                } catch (\Throwable $e) {
                }
            }
            foreach (['total_additions', 'period_month', 'period_year', 'wallet_id', 'expense_id'] as $col) {
                if (Schema::hasColumn('employee_salary_payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
