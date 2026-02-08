<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class AddCalendarPermissionToStudents extends Command
{
    protected $signature = 'students:add-calendar-permission';
    protected $description = 'إضافة صلاحية التقويم للطلاب';

    public function handle()
    {
        $this->info('جارٍ التحقق من الصلاحية...');
        
        // التحقق من وجود الصلاحية
        $permission = Permission::where('name', 'student.view.calendar')->first();
        
        if (!$permission) {
            $this->error('الصلاحية غير موجودة! جارٍ إنشاؤها...');
            $permission = Permission::create([
                'name' => 'student.view.calendar',
                'display_name' => 'عرض التقويم',
                'description' => 'عرض التقويم الأكاديمي',
                'group' => 'صلاحيات الطالب',
            ]);
            $this->info('تم إنشاء الصلاحية بنجاح!');
        } else {
            $this->info('الصلاحية موجودة بالفعل.');
        }
        
        // التحقق من دور الطالب
        $studentRole = Role::where('name', 'student')->first();
        
        if (!$studentRole) {
            $this->error('دور الطالب غير موجود!');
            return 1;
        }
        
        // إضافة الصلاحية لدور الطالب
        if (!$studentRole->permissions()->where('name', 'student.view.calendar')->exists()) {
            $studentRole->permissions()->attach($permission->id);
            $this->info('تم إضافة الصلاحية لدور الطالب بنجاح!');
        } else {
            $this->info('الصلاحية موجودة بالفعل في دور الطالب.');
        }
        
        // إضافة الصلاحية مباشرة للطلاب الموجودين (إذا لم تكن موجودة)
        $students = User::where('role', 'student')->get();
        $addedCount = 0;
        
        foreach ($students as $student) {
            // التأكد من أن الطالب لديه دور student
            if (!$student->hasRole('student')) {
                $student->assignRole('student');
            }
            
            // إضافة الصلاحية مباشرة للطالب (إذا لم تكن موجودة)
            if (!$student->directPermissions()->where('name', 'student.view.calendar')->exists()) {
                $student->directPermissions()->attach($permission->id);
                $addedCount++;
            }
        }
        
        if ($addedCount > 0) {
            $this->info("تم إضافة الصلاحية مباشرة لـ {$addedCount} طالب.");
        } else {
            $this->info('جميع الطلاب لديهم الصلاحية بالفعل.');
        }
        
        $this->info('تمت العملية بنجاح!');
        return 0;
    }
}
