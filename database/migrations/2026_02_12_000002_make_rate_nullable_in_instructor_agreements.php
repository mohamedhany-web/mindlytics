<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * عمود rate من migration قديم وبدون default - جعله له قيمة افتراضية 0
     */
    public function up(): void
    {
        if (!Schema::hasTable('instructor_agreements') || !Schema::hasColumn('instructor_agreements', 'rate')) {
            return;
        }
        DB::statement('ALTER TABLE instructor_agreements MODIFY rate DECIMAL(12,2) DEFAULT 0 NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('instructor_agreements') || !Schema::hasColumn('instructor_agreements', 'rate')) {
            return;
        }
        DB::statement('ALTER TABLE instructor_agreements MODIFY rate DECIMAL(12,2) NOT NULL DEFAULT 0');
    }
};
