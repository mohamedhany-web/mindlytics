<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assignments')) {
            return;
        }
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('assignments')) {
            return;
        }
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable(false)->change();
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }
};
