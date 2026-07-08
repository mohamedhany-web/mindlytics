<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalesTeamController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesTeam::query()
            ->with(['manager:id,name', 'members.user:id,name'])
            ->withCount('members')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('manager', fn ($mq) => $mq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $teams = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => SalesTeam::count(),
            'active' => SalesTeam::where('is_active', true)->count(),
            'members' => SalesTeamMember::where('role', SalesTeamMember::ROLE_MEMBER)->count(),
            'managers' => User::salesManagers()->where('is_active', true)->count(),
        ];

        return view('admin.sales.sales-teams.index', compact('teams', 'stats'));
    }

    public function create()
    {
        return view('admin.sales.sales-teams.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTeam($request);

        if (! User::salesManagers()->whereKey($validated['manager_id'])->exists()) {
            return back()->withInput()->with('error', 'المدير المختار ليس بوظيفة مدير مبيعات.');
        }

        DB::transaction(function () use ($validated) {
            $team = SalesTeam::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'manager_id' => $validated['manager_id'],
                'created_by' => Auth::id(),
                'is_active' => (bool) ($validated['is_active'] ?? true),
            ]);

            $this->syncMembers($team, $validated['member_ids'] ?? []);
        });

        return redirect()->route('admin.sales.sales-teams.index')->with('success', 'تم إنشاء فريق المبيعات.');
    }

    public function edit(SalesTeam $salesTeam)
    {
        $salesTeam->load(['members.user:id,name', 'manager:id,name']);

        return view('admin.sales.sales-teams.edit', array_merge(
            ['team' => $salesTeam],
            $this->formOptions($salesTeam)
        ));
    }

    public function update(Request $request, SalesTeam $salesTeam): RedirectResponse
    {
        $validated = $this->validateTeam($request, $salesTeam);

        if (! User::salesManagers()->whereKey($validated['manager_id'])->exists()) {
            return back()->withInput()->with('error', 'المدير المختار ليس بوظيفة مدير مبيعات.');
        }

        DB::transaction(function () use ($salesTeam, $validated) {
            $salesTeam->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'manager_id' => $validated['manager_id'],
                'is_active' => (bool) ($validated['is_active'] ?? true),
            ]);

            $this->syncMembers($salesTeam, $validated['member_ids'] ?? []);
        });

        return redirect()->route('admin.sales.sales-teams.index')->with('success', 'تم تحديث فريق المبيعات.');
    }

    public function destroy(SalesTeam $salesTeam): RedirectResponse
    {
        $salesTeam->delete();

        return redirect()->route('admin.sales.sales-teams.index')->with('success', 'تم حذف فريق المبيعات.');
    }

    /** @return array<string, mixed> */
    private function formOptions(?SalesTeam $team = null): array
    {
        $managerIdsInOtherTeams = SalesTeam::query()
            ->when($team, fn ($q) => $q->whereKeyNot($team->id))
            ->pluck('manager_id')
            ->all();

        $memberIdsInOtherTeams = SalesTeamMember::query()
            ->when($team, fn ($q) => $q->where('sales_team_id', '!=', $team->id))
            ->pluck('user_id')
            ->all();

        return [
            'managers' => User::salesManagers()
                ->where('is_active', true)
                ->where(function ($q) use ($managerIdsInOtherTeams, $team) {
                    $q->whereNotIn('id', $managerIdsInOtherTeams);
                    if ($team) {
                        $q->orWhere('id', $team->manager_id);
                    }
                })
                ->orderBy('name')
                ->get(['id', 'name']),
            'salesReps' => User::salesEmployees()
                ->where('is_active', true)
                ->where(function ($q) use ($memberIdsInOtherTeams, $team) {
                    $q->whereNotIn('id', $memberIdsInOtherTeams);
                    if ($team) {
                        $q->orWhereIn('id', $team->members()->pluck('user_id'));
                    }
                })
                ->orderBy('name')
                ->get(['id', 'name']),
            'selectedMemberIds' => $team
                ? $team->members()->where('role', SalesTeamMember::ROLE_MEMBER)->pluck('user_id')->all()
                : [],
        ];
    }

    /** @return array<string, mixed> */
    private function validateTeam(Request $request, ?SalesTeam $team = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'manager_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                Rule::unique('sales_teams', 'manager_id')->ignore($team?->id),
            ],
            'is_active' => ['nullable', 'boolean'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', Rule::exists('users', 'id')],
        ], [
            'manager_id.unique' => 'هذا المدير مرتبط بفريق آخر.',
        ]);
    }

    /** @param list<int> $memberIds */
    private function syncMembers(SalesTeam $team, array $memberIds): void
    {
        $memberIds = array_values(array_unique(array_map('intval', $memberIds)));

        foreach ($memberIds as $memberId) {
            if (! User::salesEmployees()->whereKey($memberId)->exists()) {
                continue;
            }
            if ((int) $memberId === (int) $team->manager_id) {
                continue;
            }
            if (SalesTeamMember::query()->where('user_id', $memberId)->where('sales_team_id', '!=', $team->id)->exists()) {
                continue;
            }
        }

        SalesTeamMember::query()
            ->where('sales_team_id', $team->id)
            ->where('role', SalesTeamMember::ROLE_MEMBER)
            ->whereNotIn('user_id', $memberIds)
            ->delete();

        foreach ($memberIds as $memberId) {
            if (! User::salesEmployees()->whereKey($memberId)->exists()) {
                continue;
            }
            if ((int) $memberId === (int) $team->manager_id) {
                continue;
            }

            SalesTeamMember::updateOrCreate(
                ['user_id' => $memberId],
                [
                    'sales_team_id' => $team->id,
                    'role' => SalesTeamMember::ROLE_MEMBER,
                    'joined_at' => now(),
                ]
            );
        }
    }
}
