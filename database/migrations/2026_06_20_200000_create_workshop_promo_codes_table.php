<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->nullable()->constrained('workshops')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->boolean('applies_to_online')->default(true);
            $table->boolean('applies_to_offline')->default(true);
            $table->boolean('applies_to_recorded')->default(true);
            $table->json('applicable_advanced_course_ids')->nullable();
            $table->json('applicable_offline_course_ids')->nullable();
            $table->unsignedInteger('max_activations')->nullable();
            $table->unsignedInteger('activation_count')->default(0);
            $table->unsignedTinyInteger('usage_limit_per_user')->default(1);
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'expires_at']);
            $table->index('workshop_id');
        });

        Schema::create('workshop_promo_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_promo_code_id')->constrained('workshop_promo_codes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->timestamp('activated_at');
            $table->timestamp('used_at')->nullable();
            $table->string('used_on_type')->nullable();
            $table->unsignedBigInteger('used_on_id')->nullable();
            $table->timestamps();

            $table->unique(['workshop_promo_code_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_promo_activations');
        Schema::dropIfExists('workshop_promo_codes');
    }
};
