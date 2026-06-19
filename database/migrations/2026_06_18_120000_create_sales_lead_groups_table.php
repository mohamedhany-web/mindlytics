<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_lead_groups')) {
            Schema::create('sales_lead_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_admin_managed')->default(false);
                $table->timestamps();

                $table->index(['assigned_to', 'name']);
            });
        }

        if (Schema::hasTable('sales_leads') && ! Schema::hasColumn('sales_leads', 'sales_lead_group_id')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                $table->foreignId('sales_lead_group_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('sales_lead_groups')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_leads') && Schema::hasColumn('sales_leads', 'sales_lead_group_id')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                $table->dropConstrainedForeignId('sales_lead_group_id');
            });
        }

        Schema::dropIfExists('sales_lead_groups');
    }
};
