@extends('layouts.admin')

@section('title', 'عرض التقديم: ' . $dataset->title)
@section('header', 'عرض تقديم مجموعة البيانات')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    <div class="mb-4">
        <a href="{{ route('admin.community.submissions.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 text-sm font-semibold">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لقائمة التقديمات المعلقة</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-slate-900">{{ $dataset->title }}</h1>
                <p class="text-sm text-slate-600 mt-1">
                    من: {{ $dataset->creator->name ?? '—' }} ({{ $dataset->creator->email ?? '—' }}) — {{ $dataset->created_at->format('Y-m-d H:i') }}
                </p>
                @if($dataset->file_size)
                    <p class="text-xs text-slate-500 mt-1">الحجم: {{ $dataset->file_size }}</p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(!empty($dataset->files_list))
                    @if(count($dataset->files_list) > 1)
                        <a href="{{ route('admin.community.submissions.dataset.download-all', $dataset) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-colors">
                            <i class="fas fa-file-archive"></i>
                            <span>تحميل الكل ZIP</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.community.submissions.dataset.download', $dataset) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors text-sm">
                        <i class="fas fa-download"></i>
                        <span>تحميل الملف</span>
                    </a>
                @elseif($dataset->file_path)
                    <a href="{{ route('admin.community.submissions.dataset.download', $dataset) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors text-sm">
                        <i class="fas fa-download"></i>
                        <span>تحميل الملف</span>
                    </a>
                @endif
                @if($dataset->file_url)
                    <a href="{{ $dataset->file_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-600 text-white font-bold hover:bg-slate-700 transition-colors text-sm">
                        <i class="fas fa-external-link-alt"></i>
                        <span>فتح رابط التحميل</span>
                    </a>
                @endif
                <form action="{{ route('admin.community.submissions.dataset.approve', $dataset) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors text-sm">
                        <i class="fas fa-check ml-1"></i> موافقة ونشر
                    </button>
                </form>
                <form action="{{ route('admin.community.submissions.dataset.reject', $dataset) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-colors text-sm">
                        <i class="fas fa-times ml-1"></i> رفض
                    </button>
                </form>
            </div>
        </div>

        @if($dataset->description)
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-700 mb-2">الوصف</h2>
                <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $dataset->description }}</div>
            </div>
        @endif
    </div>

    @php $filesList = $dataset->files_list; @endphp
    @if(!empty($filesList))
        {{-- نفس تجربة الموقع العام: قائمة ملفات + معاينة كسولة --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-folder-open text-cyan-600"></i>
                            الملفات ({{ count($filesList) }})
                        </h2>
                        @if(count($filesList) > 1)
                            <a href="{{ route('admin.community.submissions.dataset.download-all', $dataset) }}" class="mt-3 inline-flex items-center gap-2 w-full justify-center px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-colors">
                                <i class="fas fa-file-archive"></i>
                                تحميل الكل كـ ZIP
                            </a>
                        @endif
                    </div>
                    <ul class="divide-y divide-slate-100 max-h-[60vh] overflow-y-auto">
                        @foreach($filesList as $idx => $file)
                            @php
                                $name = $file['original_name'] ?? basename($file['path'] ?? '');
                                $size = $file['size'] ?? '';
                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                $isZip = ($ext === 'zip');
                            @endphp
                            <li class="dataset-file-item border-b border-slate-100 last:border-0" data-index="{{ $idx }}">
                                <div class="flex items-center gap-3 p-3 hover:bg-slate-50 cursor-pointer transition-colors border-r-4 border-transparent data-[active=yes]:bg-cyan-50 data-[active=yes]:border-cyan-500" data-active="">
                                    <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $isZip ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-600' }}">
                                        <i class="fas {{ $isZip ? 'fa-file-archive' : 'fa-file' }} text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-800 truncate text-sm" title="{{ $name }}">{{ $name }}</p>
                                        @if($size)<p class="text-xs text-slate-500">{{ $size }}</p>@endif
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" class="dataset-file-preview p-2 rounded-lg text-cyan-600 hover:bg-cyan-100 transition-colors" title="عرض">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <a href="{{ route('admin.community.submissions.dataset.download-file', [$dataset, $idx]) }}" class="p-2 rounded-lg text-slate-600 hover:bg-slate-200 transition-colors" title="تحميل">
                                            <i class="fas fa-download text-sm"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="lg:col-span-8 xl:col-span-9">
                <div id="previewContainer" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-3 flex-wrap min-w-0">
                            <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                                <i class="fas fa-table text-blue-600"></i>
                                <span id="previewTitle">معاينة البيانات</span>
                            </h2>
                            <span id="previewCount" class="text-slate-500 text-sm"></span>
                        </div>
                        <div id="previewToolbar" class="hidden flex items-center gap-2 flex-wrap">
                            <input type="text" id="previewSearch" placeholder="بحث في الجدول..." class="w-40 sm:w-52 px-3 py-1.5 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 text-slate-800" dir="rtl">
                            <button type="button" id="exportCsvBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 font-bold text-sm hover:bg-emerald-200 transition-colors">
                                <i class="fas fa-file-csv"></i>
                                <span>تصدير CSV</span>
                            </button>
                        </div>
                    </div>
                    <div id="previewLoading" class="p-8 text-center text-slate-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                        <p>جاري تحميل المعاينة...</p>
                    </div>
                    <div id="previewZipBackBar" class="hidden px-4 py-2 bg-amber-50 border-b border-amber-100 flex items-center justify-between gap-2 flex-wrap">
                        <span class="text-sm text-slate-700"><i class="fas fa-file-archive text-amber-600 ml-1"></i> عرض من داخل الأرشيف: <strong id="previewZipBackBarFileName"></strong></span>
                        <button type="button" id="previewZipBackBtn" class="text-sm font-bold text-cyan-600 hover:text-cyan-700 hover:underline">
                            <i class="fas fa-arrow-right ml-1"></i> رجوع لقائمة الملفات داخل الأرشيف
                        </button>
                    </div>
                    <div id="previewTableWrap" class="overflow-auto max-h-[70vh] border-b border-slate-100 hidden dataset-preview-table-wrap">
                        <table class="w-full min-w-full border-collapse text-right dataset-preview-table" id="previewTable">
                            <thead class="sticky top-0 z-10 bg-slate-100 border-b-2 border-slate-200"><tr id="previewThead"></tr></thead>
                            <tbody class="divide-y divide-slate-100" id="previewTbody"></tbody>
                        </table>
                    </div>
                    <div id="previewZipWrap" class="overflow-auto max-h-[70vh] p-4 hidden">
                        <h3 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-file-archive text-amber-600"></i>
                            محتويات الملف المضغوط
                        </h3>
                        <ul id="previewZipList" class="divide-y divide-slate-100 space-y-1"></ul>
                    </div>
                    <div id="previewEmpty" class="p-6 text-center text-slate-500 text-sm hidden"></div>
                </div>
            </div>
        </div>
        <style>
            .dataset-preview-table-wrap::-webkit-scrollbar { width: 8px; height: 8px; }
            .dataset-preview-table-wrap::-webkit-scrollbar-track { background: rgb(241 245 249); border-radius: 4px; }
            .dataset-preview-table-wrap::-webkit-scrollbar-thumb { background: rgb(148 163 184); border-radius: 4px; }
            .dataset-preview-table { font-size: 0.875rem; }
            .dataset-preview-table thead th { padding: 0.75rem 1rem; font-weight: 700; color: rgb(30 41 59); border-left: 1px solid rgb(226 232 240); white-space: nowrap; }
            .dataset-preview-table tbody td { padding: 0.625rem 1rem; color: rgb(51 65 85); border-left: 1px solid rgb(241 245 249); }
            .dataset-preview-table tbody tr:hover { background: rgb(248 250 252); }
            .dataset-preview-table tbody tr:nth-child(even) { background: rgb(248 250 252 / 0.6); }
            .dataset-preview-table tbody tr:nth-child(even):hover { background: rgb(241 245 249); }
        </style>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">
            @if($dataset->file_url)
                <p class="text-slate-500 text-sm">لا توجد ملفات مرفقة. الرابط الخارجي فقط: <a href="{{ $dataset->file_url }}" target="_blank" rel="noopener" class="text-cyan-600 hover:underline">{{ $dataset->file_url }}</a></p>
            @else
                <p class="text-slate-500 text-sm">لا توجد ملفات مرفقة لهذا التقديم.</p>
            @endif
        </div>
    @endif
</div>

@if(!empty($filesList))
@push('scripts')
<script>
(function() {
    var previewUrl = @json(route('admin.community.submissions.dataset.preview', $dataset));
    var previewZipEntryUrl = @json(route('admin.community.submissions.dataset.preview-zip-entry', $dataset));
    var currentZipFileIndex = null;

    function hideAllPreviews() {
        document.getElementById('previewLoading').classList.add('hidden');
        document.getElementById('previewTableWrap').classList.add('hidden');
        document.getElementById('previewZipWrap').classList.add('hidden');
        document.getElementById('previewEmpty').classList.add('hidden');
        var t = document.getElementById('previewToolbar');
        if (t) t.classList.add('hidden');
        var backBar = document.getElementById('previewZipBackBar');
        if (backBar) backBar.classList.add('hidden');
    }
    function setActiveFile(index) {
        document.querySelectorAll('.dataset-file-item').forEach(function(li) {
            var div = li.querySelector('[data-active]');
            if (div) div.setAttribute('data-active', li.getAttribute('data-index') === String(index) ? 'yes' : '');
        });
    }
    function loadPreview(index) {
        setActiveFile(index);
        hideAllPreviews();
        document.getElementById('previewLoading').classList.remove('hidden');
        document.getElementById('previewLoading').querySelector('p').textContent = 'جاري تحميل المعاينة...';
        document.getElementById('previewTitle').textContent = 'معاينة البيانات';

        fetch(previewUrl + '?file=' + index, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.getElementById('previewLoading').classList.add('hidden');
                if (data.zip && data.entries) {
                    currentZipFileIndex = index;
                    document.getElementById('previewTitle').textContent = 'محتويات الملف المضغوط';
                    var wrap = document.getElementById('previewZipWrap');
                    var list = document.getElementById('previewZipList');
                    list.innerHTML = '';
                    data.entries.forEach(function(entry) {
                        var li = document.createElement('li');
                        li.className = 'flex items-center justify-between gap-3 py-2.5 px-3 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors border-r-2 border-transparent hover:border-cyan-400 hover:bg-cyan-50/50';
                        li.setAttribute('data-entry-name', entry.name);
                        var sizeStr = entry.size >= 1024 ? (entry.size / 1024).toFixed(1) + ' KB' : entry.size + ' B';
                        li.innerHTML = '<span class="flex items-center gap-2 truncate"><i class="fas fa-file text-slate-400 text-sm shrink-0"></i><span class="truncate text-sm text-slate-800">' + escapeHtml(entry.name) + '</span></span><span class="text-xs text-slate-500 shrink-0">' + sizeStr + '</span>';
                        li.addEventListener('click', function() { loadPreviewZipEntry(index, entry.name); });
                        list.appendChild(li);
                    });
                    document.getElementById('previewCount').textContent = data.entries.length + ' ملف داخل الأرشيف — انقر على أي ملف لعرض بياناته';
                    wrap.classList.remove('hidden');
                } else {
                    var headers = data.headers || [];
                    var rows = data.rows || [];
                    if (headers.length || rows.length) {
                        var thead = document.getElementById('previewThead');
                        thead.innerHTML = '';
                        headers.forEach(function(cell) {
                            var th = document.createElement('th');
                            th.className = 'px-4 py-3 text-sm font-bold text-slate-800 whitespace-nowrap border-l border-slate-200';
                            th.textContent = cell;
                            thead.appendChild(th);
                        });
                        var tbody = document.getElementById('previewTbody');
                        tbody.innerHTML = '';
                        rows.forEach(function(row) {
                            var tr = document.createElement('tr');
                            tr.className = 'hover:bg-slate-50/80 transition-colors';
                            headers.forEach(function(_, i) {
                                var td = document.createElement('td');
                                td.className = 'px-4 py-2.5 text-sm text-slate-700 whitespace-nowrap border-l border-slate-100';
                                td.textContent = row[i] != null ? row[i] : '';
                                tr.appendChild(td);
                            });
                            tbody.appendChild(tr);
                        });
                        document.getElementById('previewCount').textContent = rows.length + ' صف × ' + headers.length + ' عمود';
                        document.getElementById('previewTableWrap').classList.remove('hidden');
                        var toolbar = document.getElementById('previewToolbar');
                        if (toolbar) toolbar.classList.remove('hidden');
                        window.__previewTableData = { headers: headers, rows: rows };
                        var searchEl = document.getElementById('previewSearch');
                        if (searchEl) searchEl.value = '';
                    } else {
                        var empty = document.getElementById('previewEmpty');
                        empty.textContent = 'تعذر قراءة معاينة الملف أو الملف غير مدعوم. يمكنك تحميل الملف من القائمة.';
                        empty.classList.remove('hidden');
                    }
                }
            })
            .catch(function() {
                document.getElementById('previewLoading').classList.add('hidden');
                var empty = document.getElementById('previewEmpty');
                empty.textContent = 'تعذر تحميل المعاينة. جرّب تحديث الصفحة.';
                empty.classList.remove('hidden');
            });
    }
    function loadPreviewZipEntry(zipFileIndex, entryName) {
        hideAllPreviews();
        document.getElementById('previewLoading').classList.remove('hidden');
        document.getElementById('previewLoading').querySelector('p').textContent = 'جاري تحميل بيانات الملف...';
        document.getElementById('previewTitle').textContent = 'معاينة البيانات';
        var url = previewZipEntryUrl + '?file=' + zipFileIndex + '&entry=' + encodeURIComponent(entryName);
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.getElementById('previewLoading').classList.add('hidden');
                var headers = data.headers || [];
                var rows = data.rows || [];
                if (headers.length || rows.length) {
                    var thead = document.getElementById('previewThead');
                    thead.innerHTML = '';
                    headers.forEach(function(cell) {
                        var th = document.createElement('th');
                        th.className = 'px-4 py-3 text-sm font-bold text-slate-800 whitespace-nowrap border-l border-slate-200';
                        th.textContent = cell;
                        thead.appendChild(th);
                    });
                    var tbody = document.getElementById('previewTbody');
                    tbody.innerHTML = '';
                    rows.forEach(function(row) {
                        var tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-50/80 transition-colors';
                        headers.forEach(function(_, i) {
                            var td = document.createElement('td');
                            td.className = 'px-4 py-2.5 text-sm text-slate-700 whitespace-nowrap border-l border-slate-100';
                            td.textContent = row[i] != null ? row[i] : '';
                            tr.appendChild(td);
                        });
                        tbody.appendChild(tr);
                    });
                    document.getElementById('previewTitle').textContent = entryName;
                    document.getElementById('previewCount').textContent = rows.length + ' صف × ' + headers.length + ' عمود';
                    document.getElementById('previewZipBackBarFileName').textContent = entryName;
                    document.getElementById('previewZipBackBar').classList.remove('hidden');
                    document.getElementById('previewTableWrap').classList.remove('hidden');
                    var toolbar = document.getElementById('previewToolbar');
                    if (toolbar) toolbar.classList.remove('hidden');
                    window.__previewTableData = { headers: headers, rows: rows };
                    var searchEl = document.getElementById('previewSearch');
                    if (searchEl) searchEl.value = '';
                } else {
                    var empty = document.getElementById('previewEmpty');
                    empty.textContent = 'تعذر قراءة معاينة هذا الملف أو نوعه غير مدعوم (يدعم: CSV، Excel، JSON، TXT).';
                    empty.classList.remove('hidden');
                }
            })
            .catch(function() {
                document.getElementById('previewLoading').classList.add('hidden');
                var empty = document.getElementById('previewEmpty');
                empty.textContent = 'تعذر تحميل المعاينة. جرّب تحديث الصفحة.';
                empty.classList.remove('hidden');
            });
    }
    function escapeHtml(s) {
        var div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    document.querySelectorAll('.dataset-file-item').forEach(function(li) {
        var index = parseInt(li.getAttribute('data-index'), 10);
        li.querySelector('.dataset-file-preview').addEventListener('click', function(e) { e.preventDefault(); loadPreview(index); });
        li.querySelector('div[data-active]').addEventListener('click', function() { loadPreview(index); });
    });
    var zipBackBtn = document.getElementById('previewZipBackBtn');
    if (zipBackBtn) zipBackBtn.addEventListener('click', function() { if (currentZipFileIndex !== null) loadPreview(currentZipFileIndex); });

    var searchInput = document.getElementById('previewSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.trim().toLowerCase();
            var tbody = document.getElementById('previewTbody');
            if (!tbody) return;
            var trs = tbody.querySelectorAll('tr');
            var visible = 0;
            trs.forEach(function(tr) {
                var text = (tr.textContent || '').toLowerCase();
                var show = !q || text.indexOf(q) !== -1;
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            var countEl = document.getElementById('previewCount');
            var data = window.__previewTableData;
            if (countEl && data) {
                countEl.textContent = (q ? visible + ' من ' + data.rows.length + ' صف' : data.rows.length + ' صف × ' + data.headers.length + ' عمود');
            }
        });
    }
    var exportBtn = document.getElementById('exportCsvBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            var d = window.__previewTableData;
            if (!d || !d.headers.length) return;
            var rows = d.rows;
            var visibleRows = [];
            var tbody = document.getElementById('previewTbody');
            if (tbody) {
                var trs = tbody.querySelectorAll('tr');
                for (var i = 0; i < trs.length; i++) {
                    if (trs[i].style.display !== 'none') visibleRows.push(rows[i]);
                }
            }
            if (visibleRows.length === 0) visibleRows = rows;
            var csv = d.headers.map(function(h) { return '"' + String(h).replace(/"/g, '""') + '"'; }).join(',') + '\n';
            visibleRows.forEach(function(row) {
                csv += row.map(function(cell) { return '"' + String(cell != null ? cell : '').replace(/"/g, '""') + '"'; }).join(',') + '\n';
            });
            var blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'preview.csv';
            a.click();
            URL.revokeObjectURL(a.href);
        });
    }

    loadPreview(0);
})();
</script>
@endpush
@endif
@endsection
