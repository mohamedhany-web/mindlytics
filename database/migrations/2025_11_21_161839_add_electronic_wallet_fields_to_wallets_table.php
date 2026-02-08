<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // إضافة الأعمدة المفقودة إذا لم تكن موجودة
            if (!Schema::hasColumn('wallets', 'name')) {
                $table->string('name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('wallets', 'type')) {
                $table->enum('type', ['vodafone_cash', 'instapay', 'bank_transfer', 'cash', 'other'])->nullable()->after('name');
            }
            if (!Schema::hasColumn('wallets', 'account_number')) {
                $table->string('account_number')->nullable()->after('type');
            }
            if (!Schema::hasColumn('wallets', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('wallets', 'account_holder')) {
                $table->string('account_holder')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('wallets', 'notes')) {
                $table->text('notes')->nullable()->after('account_holder');
            }
            if (!Schema::hasColumn('wallets', 'pending_balance')) {
                $table->decimal('pending_balance', 10, 2)->default(0)->after('balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // حذف الأعمدة إذا كانت موجودة
            if (Schema::hasColumn('wallets', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('wallets', 'account_holder')) {
                $table->dropColumn('account_holder');
            }
            if (Schema::hasColumn('wallets', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('wallets', 'account_number')) {
                $table->dropColumn('account_number');
            }
            if (Schema::hasColumn('wallets', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('wallets', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
