<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('branches') || ! Schema::hasTable('offline_courses')) {
            return;
        }

        if (Schema::hasColumn('offline_courses', 'branch_id')) {
            return;
        }

        $defaultBranchId = DB::table('branches')->whereNull('deleted_at')->where('slug', 'main')->value('id')
            ?? DB::table('branches')->whereNull('deleted_at')->orderBy('sort_order')->orderBy('id')->value('id');

        if (! $defaultBranchId) {
            return;
        }

        Schema::table('offline_courses', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('id')
                ->constrained('branches')
                ->nullOnDelete();
        });

        DB::table('offline_courses')->whereNull('branch_id')->orderBy('id')->chunkById(200, function ($rows) use ($defaultBranchId) {
            foreach ($rows as $row) {
                $bid = null;
                if (! empty($row->instructor_id)) {
                    $bid = DB::table('users')->where('id', $row->instructor_id)->value('branch_id');
                }
                DB::table('offline_courses')->where('id', $row->id)->update([
                    'branch_id' => $bid ?? $defaultBranchId,
                ]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('offline_courses') || ! Schema::hasColumn('offline_courses', 'branch_id')) {
            return;
        }

        Schema::table('offline_courses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
