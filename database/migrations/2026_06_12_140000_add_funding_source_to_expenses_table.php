<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('funding_source', 32)->default('revenue')->after('payment_method');
        });

        if (Schema::hasTable('expenses')) {
            DB::table('expenses')
                ->whereNotNull('wallet_id')
                ->update(['funding_source' => 'revenue']);

            DB::table('expenses')
                ->whereNull('wallet_id')
                ->whereIn('payment_method', ['cash', 'other'])
                ->update(['funding_source' => 'out_of_pocket']);
        }
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('funding_source');
        });
    }
};
