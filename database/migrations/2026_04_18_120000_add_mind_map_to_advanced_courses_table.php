<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->json('mind_map_steps')->nullable()->after('what_you_learn');
            $table->boolean('mind_map_published')->default(false)->after('mind_map_steps');
        });
    }

    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->dropColumn(['mind_map_steps', 'mind_map_published']);
        });
    }
};
