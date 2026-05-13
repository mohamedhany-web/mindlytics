<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branches') || !Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'branch_id')) {
            return;
        }

        $now = now();

        $mainId = DB::table('branches')->where('slug', 'main')->whereNull('deleted_at')->value('id');

        if (!$mainId) {
            $first = DB::table('branches')->whereNull('deleted_at')->orderBy('id')->value('id');
            if ($first) {
                $mainId = $first;
            } else {
                DB::table('branches')->insert([
                    'name' => 'الفرع الرئيسي',
                    'slug' => 'main',
                    'custom_domain' => null,
                    'country_code' => null,
                    'timezone' => null,
                    'currency' => 'EGP',
                    'is_active' => true,
                    'primary_color' => null,
                    'logo_path' => null,
                    'internal_notes' => 'أُنشئ تلقائياً عند ربط المستخدمين بالفروع.',
                    'settings' => null,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $mainId = (int) DB::table('branches')->where('slug', 'main')->value('id');
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('id')
                ->constrained('branches')
                ->nullOnDelete();
        });

        DB::table('users')->whereNull('branch_id')->update(['branch_id' => $mainId]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'branch_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
