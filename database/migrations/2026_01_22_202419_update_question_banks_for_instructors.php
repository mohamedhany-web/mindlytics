<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('question_banks')) {
            return;
        }

        Schema::table('question_banks', function (Blueprint $table) {
            // جعل subject_id nullable لأن المدرب قد لا يربطه بمادة محددة
            if (Schema::hasColumn('question_banks', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->change();
            }
            
            // إضافة instructor_id لربط بنك الأسئلة بالمدرب
            if (!Schema::hasColumn('question_banks', 'instructor_id')) {
                $table->unsignedBigInteger('instructor_id')->nullable()->after('created_by');
            }
        });

        // إضافة Foreign Key لـ instructor_id
        if (Schema::hasColumn('question_banks', 'instructor_id')) {
            try {
                Schema::table('question_banks', function (Blueprint $table) {
                    $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key موجود بالفعل
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('question_banks')) {
            return;
        }

        Schema::table('question_banks', function (Blueprint $table) {
            if (Schema::hasColumn('question_banks', 'instructor_id')) {
                try {
                    $table->dropForeign(['instructor_id']);
                } catch (\Exception $e) {
                    // Foreign key غير موجود
                }
                $table->dropColumn('instructor_id');
            }
        });
    }
};
