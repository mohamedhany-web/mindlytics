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
        // جدول الكوبونات
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
                $table->decimal('discount_value', 10, 2);
                $table->decimal('minimum_amount', 10, 2)->nullable(); // الحد الأدنى للطلب
                $table->decimal('maximum_discount', 10, 2)->nullable(); // الحد الأقصى للخصم
                $table->integer('usage_limit')->nullable(); // حد الاستخدام العام
                $table->integer('usage_limit_per_user')->default(1); // حد الاستخدام لكل مستخدم
                $table->integer('used_count')->default(0);
                $table->date('starts_at')->nullable();
                $table->date('expires_at')->nullable();
                $table->enum('applicable_to', ['all', 'courses', 'subscriptions', 'specific'])->default('all');
                $table->json('applicable_course_ids')->nullable(); // قائمة الكورسات المحددة
                $table->json('applicable_user_ids')->nullable(); // قائمة المستخدمين المحددين
                $table->boolean('is_active')->default(true);
                $table->boolean('is_public')->default(true); // مرئي للجميع أو خاص
                $table->timestamps();
                
                $table->index(['code', 'is_active']);
                $table->index(['starts_at', 'expires_at']);
                $table->index('is_active');
            });
        }
        
        // جدول استخدامات الكوبونات
        if (!Schema::hasTable('coupon_usages')) {
            Schema::create('coupon_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
                $table->decimal('discount_amount', 10, 2);
                $table->decimal('order_amount', 10, 2);
                $table->decimal('final_amount', 10, 2);
                $table->timestamps();
                
                $table->index(['coupon_id', 'user_id']);
                $table->index('invoice_id');
            });
        }
        
        // جدول برامج الولاء والخصومات التلقائية
        if (!Schema::hasTable('loyalty_programs')) {
            Schema::create('loyalty_programs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('type', ['points', 'tier', 'referral', 'volume'])->default('points');
                $table->json('rules')->nullable(); // قواعد البرنامج
                $table->boolean('is_active')->default(true);
                $table->date('starts_at')->nullable();
                $table->date('expires_at')->nullable();
                $table->timestamps();
            });
        }
        
        // جدول نقاط المستخدمين
        if (!Schema::hasTable('user_points')) {
            Schema::create('user_points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('loyalty_program_id')->nullable()->constrained('loyalty_programs')->onDelete('set null');
                $table->integer('points')->default(0);
                $table->integer('total_earned')->default(0);
                $table->integer('total_redeemed')->default(0);
                $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum', 'diamond'])->default('bronze');
                $table->timestamps();
                
                $table->unique('user_id');
                $table->index('tier');
            });
        }
        
        // جدول معاملات النقاط
        if (!Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('user_points_id')->constrained('user_points')->onDelete('cascade');
                $table->enum('type', ['earned', 'redeemed', 'expired', 'adjusted'])->default('earned');
                $table->integer('points');
                $table->integer('points_before');
                $table->integer('points_after');
                $table->text('description');
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'type']);
                $table->index('invoice_id');
            });
        }
        
        // جدول الإحالات
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade'); // من أحال
                $table->foreignId('referred_id')->unique()->constrained('users')->onDelete('cascade'); // من تمت إحالته
                $table->string('referral_code')->unique();
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
                $table->dateTime('completed_at')->nullable();
                $table->decimal('reward_amount', 10, 2)->nullable();
                $table->integer('reward_points')->nullable();
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['referrer_id', 'status']);
                $table->index('referral_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('user_points');
        Schema::dropIfExists('loyalty_programs');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
