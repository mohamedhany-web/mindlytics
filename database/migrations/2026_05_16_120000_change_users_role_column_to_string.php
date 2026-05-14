<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * عمود users.role كان ENUM محدوداً (مثلاً super_admin/instructor/student فقط)،
     * فيرفض MySQL قيمة branch_manager أو admin أو employee → خطأ 500 عند إنشاء مدير فرع.
     * تحويل العمود إلى VARCHAR يزيل القيد ويتوافق مع التحقق في لوحة الإدارة.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $database = DB::getDatabaseName();
            if (is_string($database) && $database !== '') {
                $row = DB::selectOne(
                    'SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$database, 'users', 'role']
                );
                if ($row && isset($row->DATA_TYPE) && strtolower((string) $row->DATA_TYPE) === 'varchar') {
                    return;
                }
            }

            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` VARCHAR(64) NOT NULL DEFAULT 'student'");
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(64) USING (role::text)");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'student'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            try {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('super_admin','admin','instructor','teacher','student','parent','employee','branch_manager') NOT NULL DEFAULT 'student'");
            } catch (\Throwable) {
                // إذا فشل (قيم غير مدعومة في البيانات)، نُبقي VARCHAR
            }
        }
    }
};
