<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_lead_group_members')) {
            return;
        }

        Schema::create('sales_lead_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_lead_group_id')->constrained('sales_lead_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['sales_lead_group_id', 'user_id']);
            $table->index('user_id');
        });

        if (Schema::hasTable('sales_lead_groups')) {
            $groups = DB::table('sales_lead_groups')->select('id', 'assigned_to')->get();
            foreach ($groups as $group) {
                if (! $group->assigned_to) {
                    continue;
                }

                DB::table('sales_lead_group_members')->insertOrIgnore([
                    'sales_lead_group_id' => $group->id,
                    'user_id' => $group->assigned_to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_lead_group_members');
    }
};
