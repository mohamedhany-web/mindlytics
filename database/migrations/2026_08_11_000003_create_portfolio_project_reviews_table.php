<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_project_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();
            $table->string('decision', 32); // approved | rejected | changes_requested | published
            $table->unsignedTinyInteger('score_code_quality')->nullable();
            $table->unsignedTinyInteger('score_ui_ux')->nullable();
            $table->unsignedTinyInteger('score_functionality')->nullable();
            $table->unsignedTinyInteger('score_problem_solving')->nullable();
            $table->unsignedTinyInteger('score_documentation')->nullable();
            $table->decimal('score_average', 4, 2)->nullable();
            $table->text('instructor_notes')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamps();

            $table->index(['portfolio_project_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_project_reviews');
    }
};
