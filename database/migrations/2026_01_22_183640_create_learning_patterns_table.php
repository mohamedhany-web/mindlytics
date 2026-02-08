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
        // هذا migration فارغ - تم استبداله بـ 2026_01_22_183749_create_learning_patterns_table.php
        // لا حاجة لإنشاء جدول هنا
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_patterns');
    }
};
