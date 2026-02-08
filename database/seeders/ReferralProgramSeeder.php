<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReferralProgram;

class ReferralProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود جدول referral_programs
        if (!\Illuminate\Support\Facades\Schema::hasTable('referral_programs')) {
            $this->command->warn('⚠️  جدول referral_programs غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        // إنشاء برنامج إحالة افتراضي
        ReferralProgram::firstOrCreate(
            ['name' => 'برنامج الإحالات الافتراضي'],
            [
                'description' => 'برنامج إحالات أساسي - خصم 10% للمستخدم المحال ومكافأة 20 ج.م للمحيل',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'maximum_discount' => 50,
                'minimum_order_amount' => 100,
                'discount_valid_days' => 30,
                'referrer_reward_type' => 'fixed',
                'referrer_reward_value' => 20,
                'max_referrals_per_user' => null, // غير محدود
                'max_discount_uses_per_referred' => 1,
                'allow_self_referral' => false,
                'is_active' => true,
            ]
        );

        $this->command->info('تم إنشاء برنامج الإحالات الافتراضي بنجاح!');
    }
}