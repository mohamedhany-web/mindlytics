<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('advanced_courses', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (Schema::hasColumn('advanced_courses', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
        });
    }
};
