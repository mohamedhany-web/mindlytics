<div class="flex {{ $msg->isInbound() ? 'justify-start' : 'justify-end' }}">
    <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl px-4 py-2.5 shadow-sm text-sm whitespace-pre-wrap break-words
        {{ $msg->isInbound()
            ? 'bg-white text-slate-800 rounded-tl-sm'
            : 'bg-emerald-100 text-emerald-950 rounded-tr-sm border border-emerald-200' }}">
        <p>{{ $msg->displayBody() }}</p>
        @if($msg->error_message && $msg->status === 'failed')
            <p class="text-[10px] text-rose-600 mt-1">{{ $msg->error_message }}</p>
        @endif
        <div class="flex items-center gap-2 mt-1 text-[10px] opacity-60">
            <span>{{ $msg->created_at?->format('Y-m-d H:i') }}</span>
            @if(!$msg->isInbound())
                <span>· {{ $msg->sentBy?->name ?? 'النظام' }}</span>
                @if(in_array($msg->status, ['delivered', 'read'], true))
                    <i class="fas fa-check-double text-sky-600"></i>
                @elseif($msg->status === 'sent')
                    <i class="fas fa-check"></i>
                @elseif($msg->status === 'failed')
                    <i class="fas fa-times text-rose-500"></i>
                @endif
            @endif
        </div>
    </div>
</div>
