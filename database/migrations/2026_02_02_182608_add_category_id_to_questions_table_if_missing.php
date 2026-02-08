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
        if (!Schema::hasTable('questions') || !Schema::hasTable('question_categories')) {
            return;
        }
        if (Schema::hasColumn('questions', 'category_id')) {
            return;
        }
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('question_bank_id');
            $table->foreign('category_id')->references('id')->on('question_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('questions') || !Schema::hasColumn('questions', 'category_id')) {
            return;
        }
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
