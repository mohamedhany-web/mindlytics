<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_social_connections', function (Blueprint $table) {
            $table->id();
            $table->string('meta_user_id')->nullable()->index();
            $table->string('meta_user_name')->nullable();
            $table->text('user_access_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('status', 32)->default('disconnected')->index();
            $table->foreignId('connected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('connected_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('meta_social_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_id')->unique();
            $table->string('page_name');
            $table->string('page_username')->nullable();
            $table->text('page_access_token')->nullable();
            $table->string('picture_url', 512)->nullable();
            $table->string('category')->nullable();
            $table->string('instagram_business_id')->nullable()->index();
            $table->string('instagram_username')->nullable();
            $table->string('instagram_profile_picture', 512)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('webhook_subscribed_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->foreignId('connected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('meta_social_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meta_social_page_id')->constrained('meta_social_pages')->cascadeOnDelete();
            $table->string('platform', 20)->index(); // messenger | instagram
            $table->string('participant_id')->index();
            $table->string('participant_name')->nullable();
            $table->string('participant_username')->nullable();
            $table->string('participant_profile_pic', 512)->nullable();
            $table->string('thread_id')->nullable()->index();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->string('last_message_preview', 500)->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->string('status', 32)->default('open')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['meta_social_page_id', 'platform', 'participant_id'], 'meta_social_conv_unique');
        });

        Schema::create('meta_social_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meta_social_conversation_id')->constrained('meta_social_conversations')->cascadeOnDelete();
            $table->string('meta_message_id')->nullable()->index();
            $table->string('direction', 16)->index(); // inbound | outbound
            $table->string('message_type', 32)->default('text');
            $table->text('body')->nullable();
            $table->string('attachment_url', 512)->nullable();
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_social_messages');
        Schema::dropIfExists('meta_social_conversations');
        Schema::dropIfExists('meta_social_pages');
        Schema::dropIfExists('meta_social_connections');
    }
};
