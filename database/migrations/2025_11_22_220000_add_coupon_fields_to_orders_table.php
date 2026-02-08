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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('advanced_course_id');
            }
            if (!Schema::hasColumn('orders', 'original_amount')) {
                $table->decimal('original_amount', 10, 2)->nullable()->after('coupon_id');
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('original_amount');
            }
        });

        // إضافة foreign key constraint بشكل منفصل
        try {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'coupon_id')) {
                    $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('orders');
                    $fkExists = false;
                    foreach ($foreignKeys as $foreignKey) {
                        if ($foreignKey->getLocalColumns()[0] === 'coupon_id') {
                            $fkExists = true;
                            break;
                        }
                    }
                    if (!$fkExists) {
                        $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
                    }
                }
            });
        } catch (\Exception $e) {
            // Foreign key قد يكون موجوداً بالفعل أو حدث خطأ آخر
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('orders', 'original_amount')) {
                $table->dropColumn('original_amount');
            }
            if (Schema::hasColumn('orders', 'coupon_id')) {
                $table->dropForeign(['coupon_id']);
                $table->dropColumn('coupon_id');
            }
        });
    }
};
