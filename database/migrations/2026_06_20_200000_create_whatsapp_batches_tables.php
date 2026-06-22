<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_batches', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('source_type', 40); // workshop, admin_bulk
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('message_template')->nullable();
            $table->string('status', 20)->default('pending'); // pending, processing, completed, cancelled
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
            $table->index('status');
        });

        Schema::create('whatsapp_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('whatsapp_batches')->cascadeOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('phone', 30);
            $table->text('message');
            $table->string('message_type', 30)->default('text');
            $table->string('status', 20)->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('whatsapp_message_id')->nullable();
            $table->unsignedBigInteger('workshop_registration_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['batch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_batch_items');
        Schema::dropIfExists('whatsapp_batches');
    }
};
