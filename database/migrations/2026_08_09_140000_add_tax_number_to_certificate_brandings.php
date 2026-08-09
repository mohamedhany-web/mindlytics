<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificate_brandings')) {
            return;
        }

        Schema::table('certificate_brandings', function (Blueprint $table) {
            if (! Schema::hasColumn('certificate_brandings', 'tax_number')) {
                $table->string('tax_number', 40)->nullable()->after('academy_tagline');
            }
            if (! Schema::hasColumn('certificate_brandings', 'stamp_enabled')) {
                $table->boolean('stamp_enabled')->default(true)->after('stamp_path');
            }
        });

        if (Schema::hasColumn('certificate_brandings', 'tax_number')) {
            \Illuminate\Support\Facades\DB::table('certificate_brandings')
                ->where(function ($q) {
                    $q->whereNull('tax_number')->orWhere('tax_number', '');
                })
                ->update(['tax_number' => '774-128-949']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('certificate_brandings')) {
            return;
        }

        Schema::table('certificate_brandings', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_brandings', 'stamp_enabled')) {
                $table->dropColumn('stamp_enabled');
            }
            if (Schema::hasColumn('certificate_brandings', 'tax_number')) {
                $table->dropColumn('tax_number');
            }
        });
    }
};
