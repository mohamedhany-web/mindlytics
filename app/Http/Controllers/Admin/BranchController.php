<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BranchController extends Controller
{
    /**
     * عرض خطة التوسع والفروع (من ملف docs) داخل لوحة الإدارة.
     */
    public function rolloutPlan(): View
    {
        $path = base_path('docs/branches-platform-rollout.md');
        $markdown = File::exists($path) ? File::get($path) : "# ملف الخطة غير موجود\n\nالمسار المتوقع: `docs/branches-platform-rollout.md`";
        $rolloutHtml = Str::markdown($markdown);

        return view('admin.branches.rollout-plan', compact('rolloutHtml'));
    }

    public function index(): View
    {
        $branches = Branch::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $stats = [
            'total' => Branch::query()->count(),
            'active' => Branch::query()->where('is_active', true)->count(),
            'with_custom_domain' => Branch::query()
                ->whereNotNull('custom_domain')
                ->where('custom_domain', '!=', '')
                ->count(),
        ];

        return view('admin.branches.index', compact('branches', 'stats'));
    }

    public function create(): View
    {
        return view('admin.branches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $validated['slug'] = Str::lower($validated['slug']);
        if (!empty($validated['custom_domain'])) {
            $validated['custom_domain'] = Str::lower(trim($validated['custom_domain']));
        } else {
            $validated['custom_domain'] = null;
        }
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($request->input('sort_order', 0));

        Branch::create($validated);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'تم إنشاء الفرع بنجاح.');
    }

    /**
     * نموذج إنشاء مدير فرع من السايدبار (اختيار الفرع ثم البيانات).
     */
    public function createBranchManager(): View
    {
        $branches = Branch::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'is_active']);

        return view('admin.branches.branch-managers-create', compact('branches'));
    }

    /**
     * حفظ مدير فرع بعد اختيار الفرع من الصفحة العامة.
     */
    public function storeBranchManagerGlobal(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            array_merge(
                ['branch_id' => 'required|exists:branches,id'],
                $this->branchManagerUserRules()
            ),
            array_merge(
                [
                    'branch_id.required' => 'اختر الفرع.',
                    'branch_id.exists' => 'الفرع المحدد غير موجود.',
                ],
                $this->branchManagerUserMessages()
            )
        );

        $branch = Branch::query()->findOrFail($validated['branch_id']);

        return $this->persistBranchManager(
            $branch,
            [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'] ?? null,
            ]
        );
    }

    public function show(Branch $branch): View
    {
        $branch->loadCount([
            'users',
            'orders',
            'studentCourseEnrollments',
            'invoices',
            'payments',
            'wallets',
            'walletTransactions',
            'accountingTransactions',
        ]);

        $branchManagers = User::query()
            ->where('branch_id', $branch->id)
            ->where('role', 'branch_manager')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_active', 'last_login_at', 'created_at']);

        return view('admin.branches.show', compact('branch', 'branchManagers'));
    }

    public function edit(Branch $branch): View
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate($this->rules($branch->id), $this->messages());

        $validated['slug'] = Str::lower($validated['slug']);
        if (!empty($validated['custom_domain'])) {
            $validated['custom_domain'] = Str::lower(trim($validated['custom_domain']));
        } else {
            $validated['custom_domain'] = null;
        }
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($request->input('sort_order', $branch->sort_order));

        $branch->update($validated);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'تم تحديث الفرع بنجاح.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'تم أرشفة الفرع (يمكن استعادته من قاعدة البيانات عبر soft delete إن لزم).');
    }

    /**
     * إنشاء حساب مدير فرع (بريد فريد، وكلمة مرور اختيارية أو توليد تلقائي).
     */
    public function storeBranchManager(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate(
            $this->branchManagerUserRules(),
            $this->branchManagerUserMessages()
        );

        return $this->persistBranchManager($branch, $validated);
    }

    /**
     * @param  array{name: string, email: string, password?: string|null}  $validated
     */
    private function persistBranchManager(Branch $branch, array $validated): RedirectResponse
    {
        $plainPassword = $validated['password'] ?? null;
        if ($plainPassword === null || $plainPassword === '') {
            $plainPassword = Str::password(14, true, true, true, false);
        }

        // رقم هاتف وهمي فريد: عمود users.phone مطلوب وغالباً UNIQUE في MySQL، ولا يُجمع في نموذج مدير الفرع.
        $phone = 'bm'.Str::lower(Str::ulid());

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $phone,
            'password' => $plainPassword,
            'role' => 'branch_manager',
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('success', 'تم إنشاء حساب مدير الفرع.')
            ->with('generated_branch_manager_password', $plainPassword)
            ->with('generated_branch_manager_email', $validated['email']);
    }

    /**
     * @return array<string, mixed>
     */
    private function branchManagerUserRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:10|max:255',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function branchManagerUserMessages(): array
    {
        return [
            'name.required' => 'اسم مدير الفرع مطلوب.',
            'email.required' => 'البريد مطلوب.',
            'email.unique' => 'هذا البريد مستخدم لمستخدم آخر.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 10 أحرف إن أدخلتها.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $ignoreBranchId = null): array
    {
        $slugRule = 'required|string|max:80|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
        $slugRule .= '|unique:branches,slug';
        if ($ignoreBranchId !== null) {
            $slugRule .= ','.$ignoreBranchId;
        }

        $domainRule = 'nullable|string|max:255|regex:/^[a-z0-9.-]+$/';
        $domainRule .= '|unique:branches,custom_domain';
        if ($ignoreBranchId !== null) {
            $domainRule .= ','.$ignoreBranchId;
        }

        return [
            'name' => 'required|string|max:255',
            'slug' => $slugRule,
            'custom_domain' => $domainRule,
            'country_code' => 'nullable|string|size:2',
            'timezone' => 'nullable|string|max:64',
            'currency' => 'required|string|size:3',
            'primary_color' => 'nullable|string|regex:/^#([0-9a-fA-F]{6})$/',
            'logo_path' => 'nullable|string|max:2048',
            'internal_notes' => 'nullable|string|max:10000',
            'sort_order' => 'nullable|integer|min:0|max:999999',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'name.required' => 'اسم الفرع مطلوب.',
            'slug.required' => 'المعرّف اللاتيني (slug) مطلوب لربط الدومين الفرعي.',
            'slug.regex' => 'slug يجب أن يكون أحرفاً لاتينية صغيرة وأرقاماً وشرطات فقط (مثل cairo-01).',
            'slug.unique' => 'هذا slug مستخدم لفرع آخر.',
            'custom_domain.regex' => 'صيغة الدومين غير صالحة.',
            'custom_domain.unique' => 'هذا الدومين مربوط بفرع آخر.',
            'country_code.size' => 'رمز الدولة يجب أن يحرفين (مثل EG).',
            'currency.size' => 'رمز العملة ثلاثة أحرف (مثل EGP).',
            'primary_color.regex' => 'لون العلامة يجب أن يكون بصيغة HEX مثل #2563eb.',
        ];
    }
}
