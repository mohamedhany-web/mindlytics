{{--
  Share bar for Journey / Portfolio
  Required: $canonicalUrl, $shareTitle, $shareableType (project|profile), $shareableId
  Optional: $cardImageUrl, $cardType
--}}
@php
    $cardImageUrl = $cardImageUrl ?? null;
    $cardType = $cardType ?? null;
    $linkedin = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($canonicalUrl);
    $facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($canonicalUrl);
    $twitter = 'https://twitter.com/intent/tweet?url=' . urlencode($canonicalUrl) . '&text=' . urlencode($shareTitle);
@endphp
<div class="rounded-xl border border-gray-200 bg-slate-50 p-4" x-data="{
    copied: false,
    track(channel) {
        fetch('{{ route('public.journey.share-track') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                shareable_type: '{{ $shareableType }}',
                shareable_id: {{ (int) $shareableId }},
                channel: channel,
                card_type: @js($cardType)
            })
        }).catch(() => {});
    },
    copyLink() {
        navigator.clipboard.writeText(@js($canonicalUrl)).then(() => {
            this.copied = true;
            this.track('copy');
            setTimeout(() => this.copied = false, 2000);
        });
    }
}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm font-bold text-gray-900">{{ __('public.journey_share_title') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ __('public.journey_share_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ $linkedin }}" target="_blank" rel="noopener" @click="track('linkedin')"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#0A66C2] text-white text-xs font-bold hover:opacity-90">
                <i class="fab fa-linkedin"></i> LinkedIn
            </a>
            <a href="{{ $facebook }}" target="_blank" rel="noopener" @click="track('facebook')"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#1877F2] text-white text-xs font-bold hover:opacity-90">
                <i class="fab fa-facebook"></i> Facebook
            </a>
            <a href="{{ $twitter }}" target="_blank" rel="noopener" @click="track('x')"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-900 text-white text-xs font-bold hover:opacity-90">
                <i class="fab fa-x-twitter"></i> X
            </a>
            <button type="button" @click="copyLink()"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-800 text-xs font-bold hover:border-blue-300">
                <i class="fas fa-link"></i>
                <span x-text="copied ? '{{ __('public.journey_copied') }}' : '{{ __('public.journey_copy_link') }}'"></span>
            </button>
            @if($cardImageUrl)
                <a href="{{ $cardImageUrl }}" target="_blank" rel="noopener" @click="track('download')"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-bold">
                    <i class="fas fa-image"></i> {{ __('public.journey_share_card') }}
                </a>
            @endif
        </div>
    </div>
</div>
