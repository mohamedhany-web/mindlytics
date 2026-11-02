@extends('sales::crm.admin')
@section('title', 'جدول يوم السيلز (Blocks)')
@section('header', 'جدول يوم السيلز — Blocks')
@use('App\Domains\Sales\Models\SalesDayBlock')
@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div>
        <h1 class="crm-title">جدول الإنتاجية اليومي</h1>
        <p class="crm-muted" style="margin: 4px 0 0; font-size: 12px">البلوكات بتظهر لموظفي المبيعات ومديريهم: مكالمات، متابعات، إغلاق، تقرير.</p>
    </div>

    <form method="post" action="{{ route('admin.sales.day-blocks.store') }}" class="crm-card">
        @csrf
        <div class="crm-dashlet-head"><i class="fas fa-plus"></i><h2>إضافة بلوك</h2></div>
        <div style="padding: 14px; display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 12px; align-items: end">
            <label class="crm-label">اسم البلوك *<input class="crm-input" type="text" name="name" required></label>
            <label class="crm-label">الكود *<input class="crm-input" type="text" name="code" required placeholder="call_block_1" dir="ltr"></label>
            <label class="crm-label">من *<input class="crm-input" type="time" name="start_time" required></label>
            <label class="crm-label">إلى *<input class="crm-input" type="time" name="end_time" required></label>
            <label class="crm-label">النوع *
                <select class="crm-select" name="activity_type" required>
                    @foreach(SalesDayBlock::ACTIVITY_TYPES as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                </select>
            </label>
            <label class="crm-label" style="grid-column: span 2">الهدف<input class="crm-input" type="text" name="goal_text"></label>
            <label class="crm-label">الترتيب<input class="crm-input" type="number" name="sort_order" value="100"></label>
            <label style="display: inline-flex; align-items: center; gap: 6px; height: 38px; font-size: 13px"><input type="checkbox" name="is_active" value="1" checked> نشط</label>
            <div><button type="submit" class="crm-btn crm-btn-primary"><i class="fas fa-plus"></i> إضافة بلوك</button></div>
        </div>
    </form>

    <section class="crm-card">
        <div class="crm-toolbar"><span style="flex: 1; font-size: 13px; font-weight: 700">البلوكات ({{ $blocks->count() }})</span></div>
        <div style="overflow-x: auto">
            <table class="crm-table" style="min-width: 1000px">
                <thead><tr><th style="width: 80px">الترتيب</th><th>الاسم</th><th>الكود</th><th style="width: 120px">من</th><th style="width: 120px">إلى</th><th style="width: 150px">النوع</th><th>الهدف</th><th style="width: 60px">نشط</th><th style="width: 130px"></th></tr></thead>
                <tbody>
                    @forelse($blocks as $block)
                        @php($f = 'block-'.$block->id)
                        <tr>
                            <td><input form="{{ $f }}" class="crm-input" style="width: 100%" type="number" name="sort_order" value="{{ $block->sort_order }}" aria-label="الترتيب"></td>
                            <td><input form="{{ $f }}" class="crm-input" style="width: 100%; font-weight: 700" type="text" name="name" value="{{ $block->name }}" aria-label="الاسم"></td>
                            <td><input form="{{ $f }}" class="crm-input" style="width: 100%" type="text" name="code" value="{{ $block->code }}" dir="ltr" aria-label="الكود"></td>
                            <td><input form="{{ $f }}" class="crm-input" style="width: 100%" type="time" name="start_time" value="{{ $block->startTimeHm() }}" aria-label="من"></td>
                            <td><input form="{{ $f }}" class="crm-input" style="width: 100%" type="time" name="end_time" value="{{ $block->endTimeHm() }}" aria-label="إلى"></td>
                            <td>
                                <select form="{{ $f }}" class="crm-select" style="width: 100%" name="activity_type" aria-label="النوع">
                                    @foreach(SalesDayBlock::ACTIVITY_TYPES as $k => $label)<option value="{{ $k }}" @selected($block->activity_type === $k)>{{ $label }}</option>@endforeach
                                </select>
                            </td>
                            <td><input form="{{ $f }}" class="crm-input" style="width: 100%" type="text" name="goal_text" value="{{ $block->goal_text }}" aria-label="الهدف"></td>
                            <td style="text-align: center"><input form="{{ $f }}" type="checkbox" name="is_active" value="1" @checked($block->is_active) aria-label="نشط"></td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: flex-end">
                                    <form id="{{ $f }}" method="post" action="{{ route('admin.sales.day-blocks.update', $block) }}">@csrf @method('PUT')<button type="submit" class="crm-btn crm-btn-sm crm-btn-dark">حفظ</button></form>
                                    <form method="post" action="{{ route('admin.sales.day-blocks.destroy', $block) }}" onsubmit="return confirm('حذف البلوك؟');">@csrf @method('DELETE')<button type="submit" class="crm-btn crm-btn-sm crm-btn-outline" style="color: var(--crm-bad)">حذف</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="crm-muted" style="text-align: center; padding: 24px">مفيش بلوكات لسه. ضيف من الفورم اللي فوق.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
