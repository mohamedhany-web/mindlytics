<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->string('project_type')->nullable()->after('title'); // نوع المشروع
            $table->string('github_url')->nullable()->after('project_url'); // رابط GitHub
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn(['project_type', 'github_url']);
        });
    }
};
