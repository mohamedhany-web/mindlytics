@extends('layouts.admin')

@section('title', 'مستخدمو الفرع')
@section('header', 'مستخدمو الفرع — ' . $branch->name)

@section('content')
<div class="p-6 lg:p-8 max-w-6xl mx-auto space-y-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">بحث</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="اسم أو بريد"
                   class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-56">
        </div>
        <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-bold">تصفية</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">الاسم</th>
                        <th class="text-right px-4 py-3 font-semibold">البريد</th>
                        <th class="text-right px-4 py-3 font-semibold">الدور</th>
                        <th class="text-right px-4 py-3 font-semibold">آخر دخول</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $u->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
                            <td class="px-4 py-3" dir="ltr">{{ $u->email }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs">{{ $u->role }}</span></td>
                            <td class="px-4 py-3 text-slate-600">{{ $u->last_login_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $users->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
