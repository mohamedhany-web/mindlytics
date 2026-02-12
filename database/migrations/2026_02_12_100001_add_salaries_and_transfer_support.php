<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * بيانات حساب التحويل للمدرب + إيصال التحويل عند الدفع
     */
    public function up(): void
    {
        if (!Schema::hasTable('instructor_payout_details')) {
            Schema::create('instructor_payout_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('bank_name')->nullable();
                $table->string('account_holder_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('iban')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('swift_code')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique('user_id');
            });
        }

        if (Schema::hasTable('agreement_payments') && !Schema::hasColumn('agreement_payments', 'transfer_receipt_path')) {
            Schema::table('agreement_payments', function (Blueprint $table) {
                $table->string('transfer_receipt_path')->nullable()->after('paid_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('agreement_payments') && Schema::hasColumn('agreement_payments', 'transfer_receipt_path')) {
            Schema::table('agreement_payments', function (Blueprint $table) {
                $table->dropColumn('transfer_receipt_path');
            });
        }
        Schema::dropIfExists('instructor_payout_details');
    }
};
