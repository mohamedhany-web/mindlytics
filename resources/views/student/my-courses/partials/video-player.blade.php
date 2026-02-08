{{-- مشغل الفيديو - يتحكم فيه المشغل بالكامل (بدون x-html، نملأ الوعاء من JS) --}}
<div class="relative w-full h-full bg-black" id="video-container"
     x-data="videoPlayer()"
     x-init="init()">
    <div class="absolute inset-0 z-10 pointer-events-none select-none screenshot-protection"></div>
    <div class="absolute inset-0 z-15 pointer-events-none select-none" id="protection-overlay">
        <div class="absolute top-0 left-0 w-full h-full opacity-0 bg-black screenshot-blocker"></div>
    </div>

    {{-- وعاء الفيديو: نملؤه من JavaScript (لا نستخدم x-html) --}}
    <div class="video-player-area absolute inset-0" id="video-player">
        <div x-show="!currentLessonVideoUrl" class="absolute inset-0 flex items-center justify-center text-white p-8">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-4xl mb-4 opacity-70"></i>
                <p>لا يوجد فيديو متاح لهذا الدرس</p>
            </div>
        </div>
        <div x-show="currentLessonVideoUrl" class="video-display-wrapper absolute inset-0 w-full h-full" id="video-surface">
            {{-- يُملأ من loadVideo() بعنصر واحد: div (يوتيوب) أو iframe (فيميو/درايف) أو video (مباشر) --}}
        </div>
    </div>

    {{-- شريط التحكم الخاص بنا فقط --}}
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-4 z-30">
        <div class="flex items-center gap-3">
            <button type="button" @click="togglePlayPause()"
                    class="w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                <i class="fas" :class="isPlaying ? 'fa-pause' : 'fa-play'"></i>
            </button>
            <div class="flex-1 min-w-0 h-2 bg-gray-600 rounded-full cursor-pointer overflow-hidden" @click="seekTo($event)">
                <div class="h-full bg-blue-500 rounded-full transition-[width] duration-150" :style="'width:' + progressPercent + '%'"></div>
            </div>
            <button type="button" @click="toggleMute()"
                    class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                <i class="fas" :class="isMuted ? 'fa-volume-mute' : 'fa-volume-up'"></i>
            </button>
            <button type="button" @click="toggleFullscreen()"
                    class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                <i class="fas" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
            </button>
        </div>
    </div>
</div>
