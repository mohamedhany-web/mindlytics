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
        Schema::table('referrals', function (Blueprint $table) {
            // برنامج الإحالة المستخدم
            if (!Schema::hasColumn('referrals', 'referral_program_id')) {
                $table->unsignedBigInteger('referral_program_id')->nullable()->after('id');
            }
            
            // الكوبون التلقائي الذي تم إنشاؤه للمحال
            if (!Schema::hasColumn('referrals', 'auto_coupon_id')) {
                $table->unsignedBigInteger('auto_coupon_id')->nullable()->after('referral_code');
            }
            
            // مبلغ الخصم الذي تم تطبيقه
            if (!Schema::hasColumn('referrals', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('reward_amount');
            }
            
            // عدد مرات استخدام الخصم
            if (!Schema::hasColumn('referrals', 'discount_used_count')) {
                $table->integer('discount_used_count')->default(0)->after('discount_amount');
            }
            
            // تاريخ انتهاء صلاحية الخصم
            if (!Schema::hasColumn('referrals', 'discount_expires_at')) {
                $table->timestamp('discount_expires_at')->nullable()->after('discount_used_count');
            }
            
            // سيتم إضافة الفهارس بعد إنشاء الأعمدة
        });
        
        // إضافة الفهارس بشكل منفصل
        Schema::table('referrals', function (Blueprint $table) {
            if (Schema::hasColumn('referrals', 'referral_program_id')) {
                try {
                    $connection = Schema::getConnection();
                    $indexes = $connection->select("SHOW INDEX FROM referrals WHERE Key_name = 'referrals_referral_program_id_index'");
                    if (empty($indexes)) {
                        $table->index('referral_program_id');
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
            if (Schema::hasColumn('referrals', 'auto_coupon_id')) {
                try {
                    $connection = Schema::getConnection();
                    $indexes = $connection->select("SHOW INDEX FROM referrals WHERE Key_name = 'referrals_auto_coupon_id_index'");
                    if (empty($indexes)) {
                        $table->index('auto_coupon_id');
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
        });

        // إضافة foreign key constraints بشكل منفصل
        try {
            Schema::table('referrals', function (Blueprint $table) {
                if (Schema::hasColumn('referrals', 'referral_program_id')) {
                    $table->foreign('referral_program_id')->references('id')->on('referral_programs')->onDelete('set null');
                }
                if (Schema::hasColumn('referrals', 'auto_coupon_id')) {
                    $table->foreign('auto_coupon_id')->references('id')->on('coupons')->onDelete('set null');
                }
            });
        } catch (\Exception $e) {
            // Foreign keys قد تكون موجودة بالفعل
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            if (Schema::hasColumn('referrals', 'discount_expires_at')) {
                $table->dropColumn('discount_expires_at');
            }
            if (Schema::hasColumn('referrals', 'discount_used_count')) {
                $table->dropColumn('discount_used_count');
            }
            if (Schema::hasColumn('referrals', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('referrals', 'auto_coupon_id')) {
                $table->dropForeign(['auto_coupon_id']);
                $table->dropColumn('auto_coupon_id');
            }
            if (Schema::hasColumn('referrals', 'referral_program_id')) {
                $table->dropForeign(['referral_program_id']);
                $table->dropColumn('referral_program_id');
            }
        });
    }
};