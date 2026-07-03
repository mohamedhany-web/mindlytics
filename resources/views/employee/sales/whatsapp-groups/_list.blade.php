@if($groups->isEmpty())
    <div class="sales-panel p-10 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-emerald-50 flex items-center justify-center">
            <i class="fab fa-whatsapp text-2xl text-emerald-500"></i>
        </div>
        <p class="text-slate-700 font-semibold mb-1">لا توجد مجموعات واتساب بعد</p>
        <p class="text-sm text-slate-500 mb-5">أنشئ مجموعة على Meta Cloud وأرسل دعوات للعملاء بقالب Group Invite</p>
        <a href="{{ $r('create') }}" class="btn-wa-primary">
            <i class="fas fa-plus"></i> إنشاء مجموعة
        </a>
    </div>
@else
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($groups as $group)
            <a href="{{ $r('show', $group) }}" class="wa-group-card block">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-bold text-slate-900 line-clamp-2">{{ $group->subject }}</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold shrink-0 {{ $group->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ $group->statusLabel() }}
                    </span>
                </div>
                @if($group->description)
                    <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $group->description }}</p>
                @endif
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600">
                    <span><i class="fas fa-users ml-1 text-slate-400"></i> {{ $group->participants_count }} مدعو</span>
                    <span class="text-slate-800 font-semibold">إدارة ←</span>
                </div>
                @if($group->salesLeadGroup)
                    <p class="text-[10px] text-sky-700 mt-2 truncate"><i class="fas fa-layer-group ml-1"></i> {{ $group->salesLeadGroup->name }}</p>
                @endif
            </a>
        @endforeach
    </div>
    @if($groups->hasPages())
        <div class="mt-4">{{ $groups->links() }}</div>
    @endif
@endif
