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
        Schema::table('users', function (Blueprint $table) {
            // كود الإحالة الخاص بالمستخدم (يستخدمه لإحالة الآخرين)
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code')->unique()->nullable()->after('is_active');
            }
            
            // المستخدم الذي أحال هذا المستخدم
            if (!Schema::hasColumn('users', 'referred_by')) {
                $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');
            }
            
            // تاريخ الإحالة
            if (!Schema::hasColumn('users', 'referred_at')) {
                $table->timestamp('referred_at')->nullable()->after('referred_by');
            }
            
            // إجمالي الإحالات
            if (!Schema::hasColumn('users', 'total_referrals')) {
                $table->integer('total_referrals')->default(0)->after('referred_at');
            }
            
            // إجمالي الإحالات المكتملة (المستخدمين الذين اشتروا)
            if (!Schema::hasColumn('users', 'completed_referrals')) {
                $table->integer('completed_referrals')->default(0)->after('total_referrals');
            }
            
            // إضافة فهارس فقط إذا كانت الأعمدة موجودة
            // سيتم إضافة الفهارس بعد إنشاء الأعمدة
        });
        
        // إضافة الفهارس بشكل منفصل
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referral_code')) {
                try {
                    $connection = Schema::getConnection();
                    $indexes = $connection->select("SHOW INDEX FROM users WHERE Key_name = 'users_referral_code_index'");
                    if (empty($indexes)) {
                        $table->index('referral_code');
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
            if (Schema::hasColumn('users', 'referred_by')) {
                try {
                    $connection = Schema::getConnection();
                    $indexes = $connection->select("SHOW INDEX FROM users WHERE Key_name = 'users_referred_by_index'");
                    if (empty($indexes)) {
                        $table->index('referred_by');
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء
                }
            }
        });

        // إضافة foreign key constraint بشكل منفصل
        try {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'referred_by')) {
                    $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        } catch (\Exception $e) {
            // Foreign key قد يكون موجوداً بالفعل
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'completed_referrals')) {
                $table->dropColumn('completed_referrals');
            }
            if (Schema::hasColumn('users', 'total_referrals')) {
                $table->dropColumn('total_referrals');
            }
            if (Schema::hasColumn('users', 'referred_at')) {
                $table->dropColumn('referred_at');
            }
            if (Schema::hasColumn('users', 'referred_by')) {
                $table->dropForeign(['referred_by']);
                $table->dropColumn('referred_by');
            }
            if (Schema::hasColumn('users', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
        });
    }
};