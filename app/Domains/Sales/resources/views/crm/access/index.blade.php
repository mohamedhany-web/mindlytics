@extends('sales::crm.layout')
@section('title', 'صلاحيات CRM')
@section('content')
@include('sales::crm.partials.module-header', [
    'title' => 'صلاحيات CRM',
    'subtitle' => 'شوف كل موظف بيظهر له إيه في الشريط والأدوات، وعدّل من غير ما تغيّر وظيفته',
])

<section class="crm-card">
    <div class="crm-dashlet-head">
        <i class="fas fa-user-shield"></i>
        <h2>موظفو المبيعات والمديرون</h2>
    </div>
    <div style="overflow-x:auto">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>الموظف</th>
                    <th>الوظيفة / الدور</th>
                    <th>دور CRM</th>
                    <th>الظهور الفعّال</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $row)
                    <tr>
                        <td>
                            <strong>{{ $row['user']->name }}</strong>
                            <div class="crm-muted" style="font-size:12px">{{ $row['user']->email }}</div>
                        </td>
                        <td>{{ $row['job'] }}</td>
                        <td>{{ $row['role_label'] }}</td>
                        <td>
                            <span class="crm-status-pill">{{ $row['enabled'] }} / {{ $row['total'] }} ميزة</span>
                        </td>
                        <td style="text-align:end">
                            <a class="crm-btn crm-btn-primary crm-btn-sm" href="{{ route($crm->route('access.edit'), $row['user']) }}">تعديل الظهور</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="crm-muted" style="text-align:center;padding:24px">لا يوجد موظفون لعرضهم.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
