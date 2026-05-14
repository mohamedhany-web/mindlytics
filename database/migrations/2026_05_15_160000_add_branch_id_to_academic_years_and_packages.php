<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('branches')) {
            return;
        }

        $defaultBranchId = DB::table('branches')->whereNull('deleted_at')->where('slug', 'main')->value('id')
            ?? DB::table('branches')->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id')->value('id');

        if (! $defaultBranchId) {
            return;
        }

        if (Schema::hasTable('academic_years') && ! Schema::hasColumn('academic_years', 'branch_id')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('branches')
                    ->nullOnDelete();
            });

            DB::table('academic_years')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }

        if (Schema::hasTable('packages') && ! Schema::hasColumn('packages', 'branch_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('branches')
                    ->nullOnDelete();
            });

            DB::table('packages')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('academic_years') && Schema::hasColumn('academic_years', 'branch_id')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('packages') && Schema::hasColumn('packages', 'branch_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
