<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء دور Super Admin مع جميع الصلاحيات
        $superAdmin = Role::updateOrCreate(
            ['name' => 'Super Admin'],
            [
                'display_name' => 'مدير عام',
                'description' => 'مدير عام للنظام - يمتلك جميع الصلاحيات',
            ]
        );

        // تعيين جميع الصلاحيات لدور Super Admin
        $allPermissions = Permission::all();
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        $this->command->info('تم إنشاء دور Super Admin مع جميع الصلاحيات بنجاح!');
    }
}
