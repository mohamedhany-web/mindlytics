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
        if (!Schema::hasTable('referral_programs')) {
            Schema::create('referral_programs', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // اسم البرنامج
                $table->text('description')->nullable(); // وصف البرنامج
                
                // خصم للشخص المحال (المستخدم الجديد)
                $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage'); // نوع الخصم: نسبة أو مبلغ ثابت
                $table->decimal('discount_value', 10, 2); // قيمة الخصم (نسبة % أو مبلغ)
                $table->decimal('maximum_discount', 10, 2)->nullable(); // الحد الأقصى للخصم
                $table->decimal('minimum_order_amount', 10, 2)->nullable(); // الحد الأدنى لمبلغ الطلب
                
                // مكافأة للمحيل
                $table->enum('referrer_reward_type', ['percentage', 'fixed', 'points'])->default('fixed'); // نوع المكافأة للمحيل
                $table->decimal('referrer_reward_value', 10, 2)->nullable(); // قيمة المكافأة
                
                // مدة صلاحية الخصم
                $table->integer('discount_valid_days')->default(30); // مدة صلاحية الخصم بالأيام
                $table->integer('referral_code_valid_days')->nullable(); // مدة صلاحية كود الإحالة (null = غير محدود)
                
                // شروط الإحالة
                $table->integer('max_referrals_per_user')->nullable(); // الحد الأقصى للإحالات لكل مستخدم (null = غير محدود)
                $table->integer('max_discount_uses_per_referred')->default(1); // الحد الأقصى لاستخدام الخصم للمحال
                $table->boolean('allow_self_referral')->default(false); // السماح بالإحالة الذاتية
                
                // تواريخ البدء والانتهاء
                $table->date('starts_at')->nullable(); // تاريخ بدء البرنامج
                $table->date('expires_at')->nullable(); // تاريخ انتهاء البرنامج
                
                // الحالة
                $table->boolean('is_active')->default(true); // البرنامج نشط أم لا
                
                // الإعدادات الإضافية
                $table->json('settings')->nullable(); // إعدادات إضافية
                
                $table->timestamps();
                
                $table->index(['is_active', 'starts_at', 'expires_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_programs');
    }
};