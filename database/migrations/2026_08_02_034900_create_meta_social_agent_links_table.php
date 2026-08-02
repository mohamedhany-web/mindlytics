<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meta_social_agent_links')) {
            return;
        }

        Schema::create('meta_social_agent_links', function (Blueprint $table) {
            $table->id();
            $table->string('meta_user_id')->unique();
            $table->string('meta_user_name')->nullable();
            $table->string('meta_user_email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('tasks')->nullable();
            $table->json('page_ids')->nullable(); // local meta_social_pages.id list seen on
            $table->string('source', 40)->default('roles'); // roles|assigned_users|manual
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_social_agent_links');
    }
};
