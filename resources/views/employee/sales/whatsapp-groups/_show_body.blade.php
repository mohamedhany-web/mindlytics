<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    <div class="xl:col-span-8 space-y-4">
        <form method="post" action="{{ $r('update', $whatsappGroup) }}" class="sales-panel p-5 md:p-6 space-y-4">
            @csrf @method('PUT')
            <p class="wa-section-title">إعدادات المجموعة</p>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">الاسم</label>
                    <input type="text" name="subject" value="{{ old('subject', $whatsappGroup->subject) }}" required class="px-3 py-2.5">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="2" class="px-3 py-2.5">{{ old('description', $whatsappGroup->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">موافقة الانضمام</label>
                    <p class="text-sm font-semibold text-slate-800">{{ $whatsappGroup->join_approval_mode === 'approval_required' ? 'يتطلب موافقة' : 'تلقائي' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">المزود</label>
                    <p class="text-sm font-semibold text-slate-800">Meta Cloud API</p>
                </div>
            </div>
            <button type="submit" class="btn-wa-primary" @disabled(!$whatsappGroup->isActive())>
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
        </form>

        <div class="sales-panel p-5 md:p-6">
            <p class="wa-section-title">المدعوون والمنضمون ({{ $whatsappGroup->participants->count() }})</p>
            <div class="overflow-x-auto -mx-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-slate-500 text-xs border-b border-slate-100">
                            <th class="text-right py-2 px-2 font-semibold">الاسم</th>
                            <th class="text-right py-2 px-2 font-semibold">الرقم</th>
                            <th class="text-right py-2 px-2 font-semibold">الحالة</th>
                            <th class="py-2 px-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($whatsappGroup->participants as $p)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-2.5 px-2 font-medium text-slate-800">{{ $p->display_name ?: $p->salesLead?->name ?: '—' }}</td>
                                <td class="py-2.5 px-2 dir-ltr text-left text-slate-600">{{ $p->phone }}</td>
                                <td class="py-2.5 px-2">
                                    <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold {{ match($p->status) {
                                        'joined', 'added' => 'bg-emerald-100 text-emerald-800',
                                        'invited' => 'bg-sky-100 text-sky-800',
                                        'failed' => 'bg-rose-100 text-rose-800',
                                        'removed' => 'bg-slate-100 text-slate-500',
                                        default => 'bg-amber-100 text-amber-800',
                                    } }}">{{ $p->statusLabel() }}</span>
                                </td>
                                <td class="py-2.5 px-2 text-left">
                                    @if($whatsappGroup->isActive() && $p->status !== 'removed')
                                        <form method="post" action="{{ $r('participants.destroy', [$whatsappGroup, $p]) }}" onsubmit="return confirm('إزالة العضو؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-rose-600 text-xs font-semibold hover:underline">إزالة</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-500">لا يوجد مدعوون بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <aside class="xl:col-span-4 space-y-4">
        <div class="sales-panel p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">الحالة</span>
                <span class="text-xs px-2 py-0.5 rounded-md font-semibold {{ $whatsappGroup->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $whatsappGroup->statusLabel() }}</span>
            </div>
            @if($whatsappGroup->invite_link)
                <div>
                    <label class="text-xs text-slate-500 block mb-1">رابط الدعوة</label>
                    <input type="text" readonly value="{{ $whatsappGroup->invite_link }}" class="w-full text-xs dir-ltr px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg" onclick="this.select()">
                </div>
            @endif
            @if($whatsappGroup->wa_group_jid)
                <p class="text-[10px] text-slate-400 dir-ltr break-all">ID: {{ $whatsappGroup->wa_group_jid }}</p>
            @endif
            <div class="flex flex-wrap gap-2 pt-1">
                <form method="post" action="{{ $r('sync', $whatsappGroup) }}" class="flex-1">@csrf
                    <button type="submit" class="btn-wa-secondary w-full text-xs justify-center">مزامنة</button>
                </form>
                <form method="post" action="{{ $r('refresh-invite', $whatsappGroup) }}" class="flex-1">@csrf
                    <button type="submit" class="btn-wa-secondary w-full text-xs justify-center">تجديد الرابط</button>
                </form>
            </div>
        </div>

        @if($whatsappGroup->isActive())
            <form method="post" action="{{ $r('participants.store', $whatsappGroup) }}" class="sales-panel p-4 space-y-3">
                @csrf
                <p class="wa-section-title !mb-2 !pb-2">إرسال دعوة</p>
                <select name="invite_template_name" class="px-3 py-2 text-sm" required>
                    <option value="">قالب Group Invite</option>
                    @foreach($inviteTemplates as $tpl)
                        <option value="{{ $tpl['name'] }}" @selected($whatsappGroup->invite_template_name === $tpl['name'])>{{ $tpl['label'] ?? $tpl['name'] }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="invite_template_language" value="{{ $whatsappGroup->invite_template_language ?: 'en' }}">
                <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="px-3 py-2 dir-ltr text-sm">
                @if($availableLeads->isNotEmpty())
                    <div class="max-h-36 overflow-y-auto border border-slate-200 rounded-lg divide-y text-sm bg-slate-50/30">
                        @foreach($availableLeads->take(25) as $lead)
                            <label class="flex items-center gap-2 px-2 py-1.5 hover:bg-white cursor-pointer">
                                <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="rounded border-slate-300">
                                <span class="flex-1 truncate">{{ $lead->name }}</span>
                                <span class="text-[10px] text-slate-500 dir-ltr">{{ $lead->phone }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <button type="submit" class="btn-wa-emerald"><i class="fas fa-paper-plane"></i> إرسال الدعوات</button>
            </form>

            <form method="post" action="{{ $r('import-crm', $whatsappGroup) }}" class="sales-panel p-4 space-y-3">
                @csrf
                <p class="wa-section-title !mb-2 !pb-2">من مجموعة CRM</p>
                <select name="sales_lead_group_id" class="px-3 py-2 text-sm" required>
                    @foreach($crmGroups as $g)
                        <option value="{{ $g->id }}" @selected((int)$whatsappGroup->sales_lead_group_id === (int)$g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-wa-secondary w-full justify-center text-sm" @disabled(!$whatsappGroup->invite_template_name)>إرسال دعوات CRM</button>
            </form>

            <form method="post" action="{{ $r('leave', $whatsappGroup) }}" onsubmit="return confirm('حذف المجموعة على Meta؟')">
                @csrf
                <button type="submit" class="w-full py-2 text-sm text-rose-700 border border-rose-200 rounded-lg hover:bg-rose-50">حذف المجموعة</button>
            </form>
        @endif
    </aside>
</div>
