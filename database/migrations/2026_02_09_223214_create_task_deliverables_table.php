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
        Schema::create('task_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('delivery_type')->default('file')->comment('file, image, link');
            $table->string('link_url')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected', 'needs_revision'])->default('pending');
            $table->text('feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
            $table->index('task_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_deliverables');
    }
};
