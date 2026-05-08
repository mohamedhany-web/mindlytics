<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'headline')) {
                $table->string('headline')->nullable()->after('bio');
            }
            if (! Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable()->after('headline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'skills')) {
                $table->dropColumn('skills');
            }
            if (Schema::hasColumn('users', 'headline')) {
                $table->dropColumn('headline');
            }
        });
    }
};

