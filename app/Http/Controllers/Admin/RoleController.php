<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * عرض قائمة الأدوار
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])
            ->orderByDesc('is_system')
            ->orderBy('display_name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * نموذج إضافة دور
     */
    public function create()
    {
        $permissions = $this->groupedPermissions();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * حفظ دور جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:roles,name'],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
                'is_system' => false,
            ]);
            $role->permissions()->sync($validated['permissions'] ?? []);

            return $role;
        });

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    /**
     * عرض تفاصيل دور
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $permissions = $this->groupedPermissions();

        return view('admin.roles.show', compact('role', 'permissions'));
    }

    /**
     * نموذج تعديل دور
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = $this->groupedPermissions();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * تحديث دور وصلاحياته
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('roles', 'name')->ignore($role->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($role, $validated) {
            $role->update([
                // اسم الدور النظامي ثابت لأنه مستخدم في الكود
                'name' => $role->is_system ? $role->name : $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
            ]);
            $role->permissions()->sync($validated['permissions'] ?? []);
        });

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    /**
     * تحديث صلاحيات الدور فقط (من صفحة التفاصيل)
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'تم تحديث صلاحيات الدور بنجاح');
    }

    /**
     * حذف دور
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'لا يمكن حذف دور نظامي');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'لا يمكن حذف دور مرتبط بمستخدمين. أزل الدور من المستخدمين أولاً');
        }

        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->delete();
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }

    private function groupedPermissions()
    {
        return Permission::orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');
    }
}
