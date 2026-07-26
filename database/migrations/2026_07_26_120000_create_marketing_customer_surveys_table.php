<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('marketing_customer_surveys')) {
            return;
        }

        Schema::create('marketing_customer_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->nullOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('governorate');
            $table->string('job');
            $table->string('job_other')->nullable();
            $table->string('heard_from');
            $table->string('heard_from_other')->nullable();

            $table->text('interested_in');
            $table->text('opinion');
            $table->text('needed_courses')->nullable();
            $table->text('recommendations')->nullable();

            $table->foreignId('reward_coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->unsignedTinyInteger('reward_percentage')->default(20);
            $table->timestamp('reward_granted_at')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['advanced_course_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_customer_surveys');
    }
};
