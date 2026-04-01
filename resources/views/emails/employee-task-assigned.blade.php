<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مهمة جديدة</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f1f5f9; margin: 0; padding: 24px; color: #334155; }
        .box { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; }
        h1 { font-size: 1.25rem; color: #0f172a; margin: 0 0 16px; }
        p { margin: 0 0 10px; font-size: 0.9375rem; line-height: 1.6; }
        .card { background: #f8fafc; border-radius: 12px; padding: 14px; margin: 12px 0; border-right: 4px solid #3b82f6; }
        .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .value { font-weight: 600; color: #0f172a; }
        .btn { display: inline-block; margin-top: 16px; padding: 12px 24px; background: #2563eb; color: #fff !important; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.9375rem; }
        .btn:hover { background: #1d4ed8; }
        .note { font-size: 0.8125rem; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>تم تعيين مهمة جديدة لك</h1>
        <p>مرحباً {{ $task->employee->name }}،</p>
        <p>تم تعيين مهمة جديدة لك من قبل <strong>{{ $task->assigner->name ?? 'الإدارة' }}</strong>.</p>

        <div class="card">
            <div class="label">نوع المهمة</div>
            <div class="value">{{ \App\Models\EmployeeTask::taskTypeLabel($task->task_type) }}</div>
        </div>
        <div class="card">
            <div class="label">عنوان المهمة</div>
            <div class="value">{{ $task->title }}</div>
        </div>
        @if($task->description)
            <div class="card">
                <div class="label">الوصف</div>
                <div class="value" style="font-weight: normal;">{{ $task->description }}</div>
            </div>
        @endif
        <p>
            <span class="label">الأولوية:</span>
            @if($task->priority === 'urgent') عاجلة
            @elseif($task->priority === 'high') عالية
            @elseif($task->priority === 'medium') متوسطة
            @else منخفضة
            @endif
            @if($task->deadline)
                &nbsp;|&nbsp; <span class="label">الموعد النهائي:</span> {{ $task->deadline->format('Y-m-d') }}
            @endif
        </p>
        <a href="{{ url(route('employee.tasks.show', $task)) }}" class="btn">عرض المهمة والتسليمات</a>
        <p class="note">يمكنك الدخول إلى لوحة الموظف وعرض تفاصيل المهمة وإضافة التسليمات من هناك.</p>
    </div>
</body>
</html>
