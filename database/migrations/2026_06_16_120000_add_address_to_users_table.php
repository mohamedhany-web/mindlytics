<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || Schema::hasColumn('users', 'address')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $after = Schema::hasColumn('users', 'birth_date') ? 'birth_date' : 'bio';
            if (! Schema::hasColumn('users', $after)) {
                $after = 'bio';
            }
            $table->string('address', 500)->nullable()->after($after)->comment('عنوان المستخدم');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }
    }
};
