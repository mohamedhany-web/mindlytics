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
        if (!Schema::hasTable('question_banks')) {
            return;
        }

        Schema::table('question_banks', function (Blueprint $table) {
            if (Schema::hasColumn('question_banks', 'difficulty')) {
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('question_banks')) {
            return;
        }

        Schema::table('question_banks', function (Blueprint $table) {
            if (Schema::hasColumn('question_banks', 'difficulty')) {
                // عند التراجع، نضع قيمة افتراضية للبيانات null الموجودة
                \DB::table('question_banks')
                    ->whereNull('difficulty')
                    ->update(['difficulty' => 'medium']);
                
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable(false)->change();
            }
        });
    }
};
