<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('branches') || ! Schema::hasTable('advanced_courses')) {
            return;
        }

        if (Schema::hasColumn('advanced_courses', 'branch_id')) {
            return;
        }

        $defaultBranchId = DB::table('branches')->whereNull('deleted_at')->where('slug', 'main')->value('id')
            ?? DB::table('branches')->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id')->value('id');

        if (! $defaultBranchId) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('id')
                ->constrained('branches')
                ->nullOnDelete();
        });

        DB::table('advanced_courses')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('advanced_courses') || ! Schema::hasColumn('advanced_courses', 'branch_id')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
