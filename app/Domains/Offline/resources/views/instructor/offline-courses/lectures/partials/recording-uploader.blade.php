{{-- رفع تسجيل المحاضرة إلى Cloudflare R2 مع شريط تقدّم --}}
@php
    $lecture = $lecture ?? null;
    $maxMb = (int) config('filesystems.offline_lecture_recording_max_mb', 2048);
@endphp
<div class="rounded-2xl border border-violet-100 bg-violet-50/40 p-4 space-y-3" id="r2-recording-uploader"
     data-upload-url-endpoint="{{ route('instructor.offline-courses.lectures.recording-upload-url', $offlineCourse) }}"
     data-server-upload-endpoint="{{ route('instructor.offline-courses.lectures.recording-upload', $offlineCourse) }}"
     data-max-bytes="{{ offline_lecture_recording_max_bytes() }}"
     data-csrf="{{ csrf_token() }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <label class="block text-sm font-bold text-slate-800 mb-1">رفع تسجيل المحاضرة (Cloudflare R2)</label>
            <p class="text-xs text-slate-500">ارفع الفيديو من جهازك — يُخزَّن على Cloudflare R2 ويُعرض للطالب داخل المنصة بمشغّل HTML5. الحد الأقصى: {{ $maxMb }} ميجابايت.
                @unless(config('filesystems.offline_lecture_recording_direct_upload'))
                    <span class="block mt-1 text-amber-700">الرفع يتم عبر سيرفر المنصة حالياً — تأكد أن حد رفع PHP (upload_max_filesize / post_max_size) كافٍ لحجم الفيديو.</span>
                @endunless
            </p>
        </div>
        @if($lecture && $lecture->hasStoredRecording())
            <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                <i class="fas fa-cloud-upload-alt"></i> مرفوع
            </span>
        @endif
    </div>

    <input type="file" id="r2RecordingFile" accept="video/mp4,video/webm,video/quicktime,video/*"
           class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-600 file:text-white file:font-semibold hover:file:bg-violet-700">

    <div id="r2UploadProgressWrap" class="hidden space-y-1">
        <div class="flex justify-between text-xs font-semibold text-slate-600">
            <span id="r2UploadStatus">جاري الرفع...</span>
            <span id="r2UploadPercent">0%</span>
        </div>
        <div class="h-2.5 rounded-full bg-slate-200 overflow-hidden">
            <div id="r2UploadBar" class="h-full w-0 bg-gradient-to-l from-violet-600 to-fuchsia-500 transition-all duration-200"></div>
        </div>
    </div>
    <p id="r2UploadError" class="hidden text-sm text-red-600 font-medium"></p>
    <p id="r2UploadSuccess" class="hidden text-sm text-emerald-700 font-medium"></p>

    <input type="hidden" name="recording_path" id="recording_path" value="{{ old('recording_path', $lecture->recording_path ?? '') }}">
    <input type="hidden" name="recording_disk" id="recording_disk" value="{{ old('recording_disk', $lecture->recording_disk ?? '') }}">
    <input type="hidden" name="recording_original_name" id="recording_original_name" value="{{ old('recording_original_name', $lecture->recording_original_name ?? '') }}">
    <input type="hidden" name="recording_mime" id="recording_mime" value="{{ old('recording_mime', $lecture->recording_mime ?? '') }}">
    <input type="hidden" name="recording_size" id="recording_size" value="{{ old('recording_size', $lecture->recording_size ?? '') }}">

    @if($lecture && $lecture->hasStoredRecording())
        <label class="inline-flex items-center gap-2 text-xs text-red-600 cursor-pointer">
            <input type="checkbox" name="remove_recording_file" value="1" class="rounded">
            حذف الفيديو المرفوع الحالي
        </label>
        <p class="text-xs text-slate-500">الملف الحالي: {{ $lecture->recording_original_name ?: basename($lecture->recording_path) }}</p>
    @endif

    <div class="border-t border-violet-100 pt-3">
        <label class="block text-sm font-semibold text-slate-700 mb-1">أو رابط خارجي (اختياري)</label>
        <input type="url" name="recording_url" id="recording_url_input"
               value="{{ old('recording_url', $lecture->recording_url ?? '') }}"
               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 bg-white"
               placeholder="https://... (YouTube / رابط مباشر — إن لم ترفع ملفًا)">
        <p class="text-[11px] text-slate-500 mt-1">لو رفعت فيديو على R2، الرابط بيتولّد تلقائيًا عند توفر CDN. الأفضل للطالب: الرفع على R2.</p>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    function initR2Uploader(root) {
        if (!root || root.dataset.bound === '1') return;
        root.dataset.bound = '1';

        const fileInput = root.querySelector('#r2RecordingFile');
        const progressWrap = root.querySelector('#r2UploadProgressWrap');
        const bar = root.querySelector('#r2UploadBar');
        const percentEl = root.querySelector('#r2UploadPercent');
        const statusEl = root.querySelector('#r2UploadStatus');
        const errEl = root.querySelector('#r2UploadError');
        const okEl = root.querySelector('#r2UploadSuccess');
        const maxBytes = parseInt(root.dataset.maxBytes || '0', 10) || (2 * 1024 * 1024 * 1024);
        const csrf = root.dataset.csrf;
        const urlEndpoint = root.dataset.uploadUrlEndpoint;
        const serverEndpoint = root.dataset.serverUploadEndpoint;

        function setProgress(pct, label) {
            progressWrap.classList.remove('hidden');
            bar.style.width = pct + '%';
            percentEl.textContent = pct + '%';
            if (label) statusEl.textContent = label;
        }
        function showError(msg) {
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
            okEl.classList.add('hidden');
        }
        function showOk(msg) {
            okEl.textContent = msg;
            okEl.classList.remove('hidden');
            errEl.classList.add('hidden');
        }

        function fillHidden(data) {
            root.querySelector('#recording_path').value = data.path || '';
            root.querySelector('#recording_disk').value = data.disk || '';
            root.querySelector('#recording_original_name').value = data.original_name || '';
            root.querySelector('#recording_mime').value = data.mime || '';
            root.querySelector('#recording_size').value = data.size || '';
            if (data.public_url) {
                const urlInput = root.querySelector('#recording_url_input');
                if (urlInput) urlInput.value = data.public_url;
            }
        }

        async function requestUploadPlan(file) {
            const res = await fetch(urlEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    filename: file.name,
                    content_type: file.type || 'video/mp4',
                    size: file.size
                })
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || 'تعذر تجهيز الرفع');
            return data;
        }

        function putDirect(uploadUrl, file, headers) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('PUT', uploadUrl, true);
                Object.keys(headers || {}).forEach((k) => {
                    try { xhr.setRequestHeader(k, headers[k]); } catch (e) {}
                });
                if (!headers || !headers['Content-Type']) {
                    xhr.setRequestHeader('Content-Type', file.type || 'video/mp4');
                }
                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        const pct = Math.min(99, Math.round((e.loaded / e.total) * 100));
                        setProgress(pct, 'جاري الرفع إلى Cloudflare R2...');
                    }
                };
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) resolve();
                    else reject(new Error('فشل الرفع المباشر (' + xhr.status + ')'));
                };
                xhr.onerror = function () { reject(new Error('خطأ شبكة أثناء الرفع المباشر')); };
                xhr.send(file);
            });
        }

        function postServer(file, plannedPath) {
            return new Promise((resolve, reject) => {
                const fd = new FormData();
                fd.append('video', file);
                if (plannedPath) fd.append('path', plannedPath);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', serverEndpoint, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        const pct = Math.min(99, Math.round((e.loaded / e.total) * 100));
                        setProgress(pct, 'جاري الرفع عبر المنصة إلى التخزين...');
                    }
                };
                xhr.onload = function () {
                    let data = {};
                    try { data = JSON.parse(xhr.responseText); } catch (e) {}
                    if (xhr.status >= 200 && xhr.status < 300) resolve(data);
                    else reject(new Error(data.message || 'فشل رفع الفيديو عبر السيرفر'));
                };
                xhr.onerror = function () { reject(new Error('خطأ شبكة أثناء الرفع')); };
                xhr.send(fd);
            });
        }

        fileInput.addEventListener('change', async function () {
            const file = fileInput.files && fileInput.files[0];
            if (!file) return;
            errEl.classList.add('hidden');
            okEl.classList.add('hidden');
            if (file.size > maxBytes) {
                showError('حجم الفيديو أكبر من الحد المسموح.');
                fileInput.value = '';
                return;
            }
            setProgress(1, 'جاري تجهيز الرفع...');
            try {
                const plan = await requestUploadPlan(file);
                let uploadedMeta = null;

                if (plan.mode === 'direct' && plan.upload_url) {
                    try {
                        await putDirect(plan.upload_url, file, plan.headers || {});
                        uploadedMeta = {
                            path: plan.path,
                            disk: plan.disk,
                            original_name: file.name,
                            mime: file.type || 'video/mp4',
                            size: file.size,
                            public_url: plan.public_url || null
                        };
                    } catch (directErr) {
                        // غالباً CORS على R2 — نعيد المحاولة عبر سيرفر المنصة
                        setProgress(5, 'الرفع المباشر تعذر — جاري الرفع عبر المنصة...');
                        const uploaded = await postServer(file, plan.path || null);
                        uploadedMeta = {
                            path: uploaded.path,
                            disk: uploaded.disk,
                            original_name: uploaded.original_name || file.name,
                            mime: uploaded.mime || file.type || 'video/mp4',
                            size: uploaded.size || file.size,
                            public_url: uploaded.public_url || null
                        };
                    }
                } else {
                    const uploaded = await postServer(file, plan.path || null);
                    uploadedMeta = {
                        path: uploaded.path,
                        disk: uploaded.disk,
                        original_name: uploaded.original_name || file.name,
                        mime: uploaded.mime || file.type || 'video/mp4',
                        size: uploaded.size || file.size,
                        public_url: uploaded.public_url || null
                    };
                }

                fillHidden(uploadedMeta);
                setProgress(100, 'اكتمل الرفع');
                showOk('تم رفع الفيديو بنجاح — احفظ المحاضرة لتظهر للطالب داخل المنصة.');
            } catch (e) {
                showError((e && e.message) ? e.message : 'حدث خطأ أثناء الرفع. تأكد من إعدادات Cloudflare R2 أو ارفع ملفاً أصغر.');
                progressWrap.classList.add('hidden');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#r2-recording-uploader').forEach(initR2Uploader);
    });
})();
</script>
@endpush
@endonce
