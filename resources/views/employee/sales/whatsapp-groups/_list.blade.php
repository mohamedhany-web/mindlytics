@if($groups->isEmpty())
    <div class="sales-panel p-8 text-center text-slate-600">
        <p class="mb-4">لا توجد مجموعات واتساب.</p>
        <a href="{{ $r('create') }}" class="inline-flex px-5 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold">إنشاء مجموعة</a>
    </div>
@else
    <div class="grid gap-3">
        @foreach($groups as $group)
            <a href="{{ $r('show', $group) }}" class="sales-panel p-4 block hover:border-emerald-300">
                <div class="flex justify-between gap-2">
                    <h3 class="font-bold">{{ $group->subject }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $group->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100' }}">{{ $group->statusLabel() }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">{{ $group->participants_count }} عضو · {{ $group->creator?->name }}</p>
            </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $groups->links() }}</div>
@endif
