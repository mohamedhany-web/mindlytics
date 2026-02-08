<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class AssignInstructorPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('roles') || !Schema::hasTable('permissions')) {
            $this->command->warn('⚠️  الجداول المطلوبة غير موجودة. يرجى تشغيل migrations أولاً.');
            return;
        }

        echo "\n👨‍🏫 إعطاء صلاحيات المدرب للمستخدمين...\n";
        echo "=" . str_repeat("=", 60) . "\n";

        // الحصول على دور المدرب
        $instructorRole = Role::where('name', 'instructor')->first();
        
        if (!$instructorRole) {
            $this->command->error('❌ دور المدرب غير موجود. يرجى تشغيل PermissionsAndRolesSeeder أولاً.');
            return;
        }

        // الحصول على جميع صلاحيات المدرب
        $instructorPermissionNames = [
            // صلاحيات الكورسات
            'courses.view',
            'courses.manage_own',
            'courses.create',
            'courses.edit',
            'courses.delete',
            
            // صلاحيات المحاضرات
            'lectures.view',
            'lectures.manage_own',
            'lectures.create',
            'lectures.edit',
            'lectures.delete',
            
            // صلاحيات الواجبات
            'assignments.view',
            'assignments.create',
            'assignments.grade',
            'assignments.delete',
            
            // صلاحيات الامتحانات
            'exams.view',
            'exams.create',
            'exams.edit',
            'exams.delete',
            
            // صلاحيات المهام
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
            'view.tasks',
            'manage.tasks',
            
            // صلاحيات الإشعارات
            'notifications.view',
            'notifications.send',
            
            // صلاحيات الشهادات
            'certificates.view',
            'certificates.generate',
            
            // صلاحيات المدرب المخصصة
            'instructor.view.courses',
            'instructor.manage.lectures',
            'instructor.manage.groups',
            'instructor.manage.assignments',
            'instructor.manage.exams',
            'instructor.manage.attendance',
            'instructor.view.tasks',
        ];
        
        // جلب الصلاحيات الموجودة فقط
        $instructorPermissions = Permission::whereIn('name', $instructorPermissionNames)->pluck('id');
        
        if ($instructorPermissions->isEmpty()) {
            $this->command->warn('⚠️  لم يتم العثور على صلاحيات المدرب. يرجى تشغيل PermissionsSeeder أولاً.');
            return;
        }

        // تحديث صلاحيات دور المدرب
        $instructorRole->permissions()->sync($instructorPermissions);
        echo "✅ تم تحديث صلاحيات دور المدرب\n";

        // إعطاء دور المدرب لجميع المستخدمين الذين لديهم role = 'instructor' أو 'teacher'
        $instructors = User::whereIn('role', ['instructor', 'teacher'])->get();
        
        $assigned = 0;
        $permissionsAssigned = 0;
        foreach ($instructors as $instructor) {
            // إعطاء دور المدرب
            if (!$instructor->hasRole('instructor')) {
                $instructor->assignRole('instructor');
                $assigned++;
                echo "✅ تم إعطاء دور المدرب ل: {$instructor->name} ({$instructor->email})\n";
            } else {
                echo "ℹ️  المستخدم {$instructor->name} لديه دور المدرب بالفعل\n";
            }
            
            // إعطاء جميع صلاحيات المدرب مباشرة للمستخدم (للتأكد)
            $existingPermissions = $instructor->directPermissions()->pluck('permissions.id')->toArray();
            $missingPermissions = $instructorPermissions->diff($existingPermissions);
            
            if ($missingPermissions->isNotEmpty()) {
                $instructor->directPermissions()->attach($missingPermissions->toArray());
                $permissionsAssigned++;
                echo "✅ تم إعطاء " . $missingPermissions->count() . " صلاحية مباشرة ل: {$instructor->name}\n";
            } else {
                echo "ℹ️  المستخدم {$instructor->name} لديه جميع صلاحيات المدرب بالفعل\n";
            }
        }

        echo "\n🎉 تم إعطاء صلاحيات المدرب لـ {$assigned} مستخدم جديد و {$permissionsAssigned} مستخدم حصل على صلاحيات مباشرة!\n";
        echo "=" . str_repeat("=", 60) . "\n";
    }
}
