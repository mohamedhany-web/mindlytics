<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /** أسماء الصلاحيات تُستخدم في الكود مثل student.view.my-courses */
    private const NAME_PATTERN = 'regex:/^[A-Za-z0-9_.\-]+$/';

    /**
     * عرض الصلاحيات مجمّعة حسب المجموعة
     */
    public function index()
    {
        $permissions = Permission::withCount('roles')
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * نموذج إضافة صلاحية
     */
    public function create()
    {
        $groups = $this->existingGroups();

        return view('admin.permissions.create', compact('groups'));
    }

    /**
     * حفظ صلاحية جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', self::NAME_PATTERN, 'unique:permissions,name'],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create($validated);

        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', 'تم إنشاء الصلاحية بنجاح');
    }

    /**
     * عرض تفاصيل صلاحية والأدوار المرتبطة بها
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * نموذج تعديل صلاحية
     */
    public function edit(Permission $permission)
    {
        $permission->load('roles');
        $groups = $this->existingGroups();
        $nameLocked = $this->isInUse($permission);

        return view('admin.permissions.edit', compact('permission', 'groups', 'nameLocked'));
    }

    /**
     * تحديث صلاحية
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', self::NAME_PATTERN, Rule::unique('permissions', 'name')->ignore($permission->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:255',
        ]);

        // التحقق من الصلاحيات يتم بالاسم، فتغيير اسم صلاحية مرتبطة يسحبها من أصحابها
        if ($validated['name'] !== $permission->name && $this->isInUse($permission)) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'لا يمكن تغيير اسم صلاحية مرتبطة بأدوار أو مستخدمين']);
        }

        $permission->update($validated);

        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', 'تم تحديث الصلاحية بنجاح');
    }

    /**
     * حذف صلاحية
     */
    public function destroy(Permission $permission)
    {
        if ($this->isInUse($permission)) {
            return back()->with('error', 'لا يمكن حذف صلاحية مرتبطة بأدوار أو مستخدمين. أزلها منهم أولاً');
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'تم حذف الصلاحية بنجاح');
    }

    private function isInUse(Permission $permission): bool
    {
        return $permission->roles()->exists()
            || DB::table('user_permissions')->where('permission_id', $permission->id)->exists();
    }

    private function existingGroups()
    {
        return Permission::whereNotNull('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }
}
