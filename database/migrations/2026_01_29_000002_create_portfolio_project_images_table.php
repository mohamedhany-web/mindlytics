<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_project_id')->constrained('portfolio_projects')->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_project_images');
    }
};
