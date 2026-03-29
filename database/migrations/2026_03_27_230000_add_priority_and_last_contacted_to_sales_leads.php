<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_leads')) {
            return;
        }

        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'priority')) {
                $table->string('priority', 16)->default('normal')->after('stage');
                $table->index(['assigned_to', 'priority']);
            }
            if (! Schema::hasColumn('sales_leads', 'last_contacted_at')) {
                $table->timestamp('last_contacted_at')->nullable()->after('next_follow_up_at');
                $table->index(['assigned_to', 'last_contacted_at']);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sales_leads')) {
            return;
        }

        Schema::table('sales_leads', function (Blueprint $table) {
            if (Schema::hasColumn('sales_leads', 'last_contacted_at')) {
                $table->dropIndex(['assigned_to', 'last_contacted_at']);
                $table->dropColumn('last_contacted_at');
            }
            if (Schema::hasColumn('sales_leads', 'priority')) {
                $table->dropIndex(['assigned_to', 'priority']);
                $table->dropColumn('priority');
            }
        });
    }
};
