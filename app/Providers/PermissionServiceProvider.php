<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Blade directive للتحقق من الصلاحية
        Blade::if('hasPermission', function ($permission) {
            if (!auth()->check()) {
                return false;
            }
            return auth()->user()->hasPermission($permission);
        });

        // Blade directive للتحقق من أي صلاحية من قائمة
        Blade::if('hasAnyPermission', function (...$permissions) {
            if (!auth()->check()) {
                return false;
            }
            
            $user = auth()->user();
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    return true;
                }
            }
            
            return false;
        });

        // Blade directive للتحقق من جميع الصلاحيات
        Blade::if('hasAllPermissions', function (...$permissions) {
            if (!auth()->check()) {
                return false;
            }
            
            foreach ($permissions as $permission) {
                if (!auth()->user()->hasPermission($permission)) {
                    return false;
                }
            }
            
            return true;
        });

        // Blade directive للتحقق من الدور
        Blade::if('hasRole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
    }
}
