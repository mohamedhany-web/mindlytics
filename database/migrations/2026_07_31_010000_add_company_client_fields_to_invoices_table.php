<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'client_type')) {
                $table->string('client_type', 20)->default('student')->after('user_id');
            }
            if (! Schema::hasColumn('invoices', 'company_name')) {
                $table->string('company_name')->nullable()->after('client_type');
            }
        });

        // جعل user_id قابلاً للقيمة الفارغة لفواتير الجهات الخارجية (شركات)
        try {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Throwable $e) {
            // قد يختلف اسم قيد المفتاح الأجنبي بين البيئات
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE invoices MODIFY user_id BIGINT UNSIGNED NULL');
        } else {
            Schema::table('invoices', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }

        try {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // تجاهل إن كان القيد موجوداً مسبقاً
        }

        DB::table('invoices')
            ->whereNull('client_type')
            ->orWhere('client_type', '')
            ->update(['client_type' => 'student']);

        // المدفوعات والمعاملات المرتبطة بفواتير شركات تحتاج user_id اختياري
        if (Schema::hasTable('payments')) {
            try {
                Schema::table('payments', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            } catch (\Throwable $e) {
            }

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE payments MODIFY user_id BIGINT UNSIGNED NULL');
            } else {
                Schema::table('payments', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->change();
                });
            }

            try {
                Schema::table('payments', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasTable('transactions')) {
            try {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            } catch (\Throwable $e) {
            }

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE transactions MODIFY user_id BIGINT UNSIGNED NULL');
            } else {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->change();
                });
            }

            try {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                });
            } catch (\Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'company_name')) {
                $table->dropColumn('company_name');
            }
            if (Schema::hasColumn('invoices', 'client_type')) {
                $table->dropColumn('client_type');
            }
        });
    }
};
