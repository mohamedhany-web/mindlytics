<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_meta_template_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_meta_template_id')
                ->constrained('whatsapp_meta_templates')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['whatsapp_meta_template_id', 'user_id'], 'wa_tpl_user_unique');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_meta_template_user');
    }
};
