<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_work_unlocks')) {
            return;
        }

        Schema::create('employee_work_unlocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unlocked_by')->constrained('users')->cascadeOnDelete();
            $table->date('work_date');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->string('reason', 500);
            $table->string('duration_label', 50)->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('revoke_reason', 500)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'work_date', 'expires_at']);
            $table->index(['unlocked_by', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_work_unlocks');
    }
};
