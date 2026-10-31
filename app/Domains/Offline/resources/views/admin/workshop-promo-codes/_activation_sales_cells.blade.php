@php
    $lead = $act->resolvedLead ?? $act->salesLead;
    $assignee = $lead?->assignee;
    $formId = 'promo-sales-'.$act->id;
@endphp

<td class="px-4 py-3 whitespace-nowrap">
    @if($assignee)
        <div class="font-semibold text-slate-800">{{ $assignee->name }}</div>
        @if($lead)
            <a href="{{ route('admin.sales.leads.show', $lead) }}" class="text-xs text-blue-600 hover:underline">عرض Lead</a>
        @endif
    @else
        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">غير مسند</span>
    @endif
</td>
<td class="px-4 py-3 whitespace-nowrap text-slate-600">
    @if($lead?->next_follow_up_at)
        <span class="@if($lead->isFollowUpOverdue()) text-rose-600 font-semibold @endif">
            {{ $lead->next_follow_up_at->format('Y-m-d H:i') }}
        </span>
    @else
        <span class="text-slate-400">—</span>
    @endif
</td>
<td class="px-4 py-3">
    <details class="group">
        <summary class="cursor-pointer list-none inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold
            {{ $assignee ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
            <i class="fas fa-user-plus text-[10px]"></i>
            {{ $assignee ? 'إعادة إسناد / متابعة' : 'إسناد للمبيعات' }}
        </summary>
        <form method="POST" action="{{ route('admin.workshop-promo-activations.sales-task', $act) }}"
              class="mt-3 p-3 rounded-xl border border-slate-200 bg-slate-50 space-y-2 min-w-[240px]">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-slate-600 block mb-1">موظف المبيعات</label>
                <select name="assigned_to" required class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                    <option value="">— اختر —</option>
                    @foreach($salesReps ?? [] as $rep)
                        <option value="{{ $rep->id }}" @selected(old('assigned_to', $assignee?->id) == $rep->id)>{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-600 block mb-1">متابعة يوم</label>
                <input type="datetime-local" name="next_follow_up_at" required
                       value="{{ old('next_follow_up_at', $lead?->next_follow_up_at?->format('Y-m-d\TH:i') ?: now()->addDay()->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
            </div>
            @if(($salesLeadGroups ?? collect())->isNotEmpty())
                <div>
                    <label class="text-[10px] font-bold text-slate-600 block mb-1">مجموعة (اختياري)</label>
                    <select name="sales_lead_group_id" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                        <option value="">بدون مجموعة</option>
                        @foreach($salesLeadGroups as $group)
                            <option value="{{ $group->id }}" @selected(old('sales_lead_group_id', $lead?->sales_lead_group_id) == $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <label class="text-[10px] font-bold text-slate-600 block mb-1">ملاحظات المتابعة (اختياري)</label>
                <textarea name="task_notes" rows="2" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="تفاصيل للموظف…">{{ old('task_notes') }}</textarea>
            </div>
            <button type="submit" class="w-full px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold">
                {{ $assignee ? 'تحديث الإسناد والمتابعة' : 'إسناد وإنشاء متابعة' }}
            </button>
        </form>
    </details>
</td>
