<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_kpi_targets')) {
            Schema::create('sales_kpi_targets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('year_month', 7);
                $table->json('targets')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'year_month']);
            });
        }

        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'csat_rating')) {
                $table->unsignedTinyInteger('csat_rating')->nullable()->after('lost_reason');
            }
            if (! Schema::hasColumn('sales_leads', 'csat_comment')) {
                $table->text('csat_comment')->nullable()->after('csat_rating');
            }
            if (! Schema::hasColumn('sales_leads', 'csat_recorded_at')) {
                $table->timestamp('csat_recorded_at')->nullable()->after('csat_comment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            if (Schema::hasColumn('sales_leads', 'csat_recorded_at')) {
                $table->dropColumn(['csat_rating', 'csat_comment', 'csat_recorded_at']);
            }
        });

        Schema::dropIfExists('sales_kpi_targets');
    }
};
