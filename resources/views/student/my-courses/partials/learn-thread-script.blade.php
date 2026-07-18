<script>
window.learnThreadPanel = function (cfg) {
    return {
        key: cfg.key,
        kind: cfg.kind,
        contextType: cfg.contextType,
        contextId: cfg.contextId,
        listUrl: cfg.listUrl,
        storeUrl: cfg.storeUrl,
        csrf: cfg.csrf,
        emptyLabel: cfg.emptyLabel,
        placeholder: cfg.placeholder,
        items: [],
        body: '',
        replyBody: '',
        replyOpen: null,
        loading: false,
        loaded: false,
        sending: false,
        error: '',

        onLoadEvent(detail) {
            if (detail && detail.key === this.key) this.load(true);
        },
        onTabEvent(detail) {
            if (!detail) return;
            if (detail.type === this.contextType && Number(detail.id) === Number(this.contextId) && detail.tab === this.kind) {
                this.load(false);
            }
        },

        async load(force) {
            if (this.loading) return;
            if (this.loaded && !force) return;
            this.loading = true;
            this.error = '';
            try {
                const url = new URL(this.listUrl, window.location.origin);
                url.searchParams.set('context_type', this.contextType);
                url.searchParams.set('context_id', String(this.contextId));
                url.searchParams.set('kind', this.kind);
                const res = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error('تعذر تحميل المشاركات');
                const json = await res.json();
                this.items = Array.isArray(json.data) ? json.data : [];
                this.loaded = true;
            } catch (e) {
                this.error = e.message || 'حدث خطأ';
            } finally {
                this.loading = false;
            }
        },

        toggleReply(id) {
            this.replyOpen = this.replyOpen === id ? null : id;
            this.replyBody = '';
        },

        async submit() {
            await this.postMessage(null, this.body, (created) => {
                created.replies = created.replies || [];
                this.items.unshift(created);
                this.body = '';
            });
        },

        async submitReply(parent) {
            await this.postMessage(parent.id, this.replyBody, (created) => {
                if (!parent.replies) parent.replies = [];
                parent.replies.push(created);
                this.replyBody = '';
                this.replyOpen = null;
            });
        },

        async postMessage(parentId, text, onOk) {
            const trimmed = (text || '').trim();
            if (trimmed.length < 2 || this.sending) return;
            this.sending = true;
            this.error = '';
            try {
                const res = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        context_type: this.contextType,
                        context_id: this.contextId,
                        kind: this.kind,
                        body: trimmed,
                        parent_id: parentId
                    })
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const first = json.errors ? Object.values(json.errors)[0] : null;
                    const msg = Array.isArray(first) ? first[0] : (json.message || json.error || 'تعذر الإرسال');
                    throw new Error(msg);
                }
                if (json.data && onOk) onOk(json.data);
            } catch (e) {
                this.error = e.message || 'حدث خطأ';
            } finally {
                this.sending = false;
            }
        }
    };
};
</script>
