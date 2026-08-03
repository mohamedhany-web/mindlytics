@php
    /** @var array{user: \App\Models\User, children: array} $node */
    $user = $node['user'];
    $depth = $depth ?? 0;
    $role = $user->isSalesManager() ? 'مدير' : 'موظف';
    $specs = $user->relationLoaded('salesInterestTypes')
        ? ($user->salesInterestTypes->pluck('name_ar')->implode(' · ') ?: '—')
        : '—';
@endphp
<div class="mb-3" style="margin-right: {{ $depth * 1.75 }}rem">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 {{ $readonly ?? false ? '' : '' }}">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0">
                <p class="font-black text-slate-900">
                    {{ $user->name }}
                    <span class="text-[11px] font-semibold text-slate-500">({{ $role }})</span>
                </p>
                <p class="text-xs text-slate-600 mt-0.5">تخصصات: {{ $specs }}</p>
                <p class="text-[11px] text-slate-500">Leads مفتوحة: {{ $openCounts[$user->id] ?? 0 }}</p>
            </div>
            @unless($readonly ?? false)
                <form method="post" action="{{ route('admin.sales.org-chart.update', $user) }}" class="flex gap-2 items-center">
                    @csrf
                    @method('PUT')
                    <select name="sales_reports_to_id" class="rounded-xl border px-3 py-2 text-sm">
                        <option value="">— بدون مدير مباشر —</option>
                        @foreach($staff as $cand)
                            @if((int) $cand->id !== (int) $user->id)
                                <option value="{{ $cand->id }}" @selected((int) $user->sales_reports_to_id === (int) $cand->id)>{{ $cand->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button class="rounded-xl bg-emerald-600 text-white px-3 py-2 text-xs font-semibold">حفظ</button>
                </form>
            @endunless
        </div>
    </div>
    @foreach($node['children'] as $child)
        @include('admin.sales.org-chart._node', [
            'node' => $child,
            'depth' => $depth + 1,
            'staff' => $staff,
            'openCounts' => $openCounts,
            'readonly' => $readonly ?? false,
        ])
    @endforeach
</div>
