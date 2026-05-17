<div class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-lg">
    <h3 class="text-xl font-black text-[#1C2C39] mb-4">جلسة برمجة مباشرة</h3>
    
    <?php if(isset($pattern->pattern_data['video_url'])): ?>
        <div class="mb-6">
            <div class="aspect-video bg-black rounded-xl overflow-hidden">
                <?php
                    $videoUrl = $pattern->pattern_data['video_url'];
                    $platform = $pattern->pattern_data['video_platform'] ?? 'youtube';
                ?>
                
                <?php if($platform === 'youtube'): ?>
                    <?php
                        // استخراج video ID
                        $videoId = null;
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)) {
                            $videoId = $matches[1];
                        }
                    ?>
                    <?php if($videoId): ?>
                        <iframe src="https://www.youtube.com/embed/<?php echo e($videoId); ?>?rel=0&modestbranding=1&controls=1" 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen></iframe>
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full text-white">
                            <p>رابط YouTube غير صحيح</p>
                        </div>
                    <?php endif; ?>
                <?php elseif($platform === 'vimeo'): ?>
                    <?php
                        $videoId = null;
                        if (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches)) {
                            $videoId = $matches[1];
                        }
                    ?>
                    <?php if($videoId): ?>
                        <iframe src="https://player.vimeo.com/video/<?php echo e($videoId); ?>" 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                allow="autoplay; fullscreen; picture-in-picture" 
                                allowfullscreen></iframe>
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full text-white">
                            <p>رابط Vimeo غير صحيح</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <video width="100%" height="100%" controls>
                        <source src="<?php echo e($videoUrl); ?>" type="video/mp4">
                        متصفحك لا يدعم تشغيل الفيديو.
                    </video>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if(isset($pattern->pattern_data['topic'])): ?>
        <div class="mb-4">
            <span class="px-3 py-1 bg-[#2CA9BD] text-white rounded-lg text-sm font-bold">
                <?php echo e($pattern->pattern_data['topic']); ?>

            </span>
        </div>
    <?php endif; ?>
    
    <div class="mt-6" x-data="liveCodingHandler()" x-init="init()">
        <p class="text-gray-600 mb-4">شاهد الفيديو وأكمل الجلسة</p>
        <button @click="completeSession()" 
                class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold">
            <i class="fas fa-check ml-2"></i>
            إكمال الجلسة
        </button>
    </div>
</div>

<script>
function liveCodingHandler() {
    return {
        parentComponent: null,
        
        init() {
            const el = this.$el.closest('[x-data*="learningPattern"]');
            if (el && el.__x) this.parentComponent = el.__x.$data;
        },
        
        async completeSession() {
            this.$dispatch('pattern-complete', { completed: true, watched: true });
        }
    };
}
</script>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/learning-patterns/types/live_coding.blade.php ENDPATH**/ ?>