

<?php $__env->startSection('title', 'تعبئة التقرير اليومي'); ?>
<?php $__env->startSection('header', 'تعبئة التقرير اليومي'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $r = $report;
    $existingContacts = old('contacts', $r?->contacts?->map(fn ($c) => [
        'sales_lead_id' => $c->sales_lead_id,
        'contact_name' => $c->contact_name,
        'contact_phone' => $c->contact_phone,
        'interaction_type' => $c->interaction_type,
        'client_status' => $c->client_status,
        'client_problems' => $c->client_problems,
    ])->values()->all() ?? []);
?>
<div class="max-w-5xl mx-auto space-y-6" x-data="dailyReportForm(<?php echo \Illuminate\Support\Js::from($existingContacts)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($leads->map(fn($l) => ['id' => $l->id, 'name' => $l->name, 'phone' => $l->phone])->values())->toHtml() ?>)">
    <a href="<?php echo e(route('employee.sales.daily-reports.index', ['date' => $date->toDateString()])); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> العودة</a>

    <?php if($errors->any()): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('employee.sales.daily-reports.store')); ?>" class="space-y-8">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="report_date" value="<?php echo e($date->toDateString()); ?>">

        <section class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-1">١ — نشاط اليوم</h2>
            <p class="text-xs text-gray-500 mb-4">ماذا فعلت على مدار اليوم: رسائل، تأهيل، حجوزات</p>
            <div class="grid sm:grid-cols-3 gap-4">
                <?php $__currentLoopData = ['messages_replied' => 'ردود على الرسائل', 'leads_qualified' => 'عملاء مؤهّلون', 'bookings_from_leads' => 'حجوزات من Leads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1"><?php echo e($label); ?> <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="<?php echo e($key); ?>" value="<?php echo e(old($key, $r?->$key)); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-bold text-gray-600 mb-1">ملاحظات النشاط</label>
                <textarea name="activity_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('activity_notes', $r?->activity_notes)); ?></textarea>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-1">٢ — الإنتاجية</h2>
            <p class="text-xs text-gray-500 mb-4">أرقام عملت عليها، متابعات، مكالمات واجتماعات</p>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                <?php $__currentLoopData = ['numbers_worked' => 'أرقام تم العمل عليها', 'followups_done' => 'متابعات', 'calls_made' => 'مكالمات أُجريت', 'meetings_held' => 'اجتماعات', 'calls_answered' => 'مكالمات تم الرد عليها']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1"><?php echo e($label); ?> <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="<?php echo e($key); ?>" value="<?php echo e(old($key, $r?->$key)); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-bold text-gray-600 mb-1">ملاحظات الإنتاجية</label>
                <textarea name="productivity_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('productivity_notes', $r?->productivity_notes)); ?></textarea>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-emerald-200 p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">٣ — تفاصيل المكالمات والاجتماعات</h2>
                    <p class="text-xs text-rose-700 font-semibold mt-1">إلزامي: رقم العميل + حالة العميل + المشاكل — يمكن الاختيار من Leads الخاصة بك</p>
                </div>
                <button type="button" @click="addContact('call')" class="text-xs font-bold px-3 py-2 bg-emerald-100 text-emerald-800 rounded-lg">+ مكالمة</button>
                <button type="button" @click="addContact('meeting')" class="text-xs font-bold px-3 py-2 bg-violet-100 text-violet-800 rounded-lg">+ اجتماع</button>
            </div>

            <template x-for="(row, index) in contacts" :key="index">
                <div class="border border-gray-200 rounded-xl p-4 mb-3 bg-gray-50/80">
                    <input type="hidden" :name="'contacts['+index+'][interaction_type]'" x-model="row.interaction_type">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-gray-700" x-text="row.interaction_type === 'meeting' ? 'اجتماع' : 'مكالمة'"></span>
                        <button type="button" @click="contacts.splice(index, 1)" class="text-rose-600 text-xs font-bold">حذف</button>
                    </div>
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600">من Leads</label>
                            <select class="w-full rounded-lg border border-gray-300 text-sm py-2" :name="'contacts['+index+'][sales_lead_id]'" x-model="row.sales_lead_id" @change="onLeadPick(index)">
                                <option value="">— يدوي —</option>
                                <template x-for="l in leads" :key="l.id">
                                    <option :value="l.id" x-text="l.name + (l.phone ? ' — ' + l.phone : '')"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">رقم الهاتف <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 text-sm py-2 px-3" :name="'contacts['+index+'][contact_phone]'" x-model="row.contact_phone" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">اسم العميل</label>
                            <input type="text" class="w-full rounded-lg border border-gray-300 text-sm py-2 px-3" :name="'contacts['+index+'][contact_name]'" x-model="row.contact_name">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-600">حالة العميل <span class="text-red-500">*</span></label>
                            <textarea rows="2" class="w-full rounded-lg border border-gray-300 text-sm py-2 px-3" :name="'contacts['+index+'][client_status]'" x-model="row.client_status" required></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-600">مشاكل / احتياجات العميل <span class="text-red-500">*</span></label>
                            <textarea rows="2" class="w-full rounded-lg border border-gray-300 text-sm py-2 px-3" :name="'contacts['+index+'][client_problems]'" x-model="row.client_problems" required></textarea>
                        </div>
                    </div>
                </div>
            </template>
            <p x-show="contacts.length === 0" class="text-sm text-gray-500">أضف صفاً لكل مكالمة أو اجتماع أجريته اليوم.</p>
        </section>

        <div class="flex flex-wrap gap-3">
            <button type="submit" name="action" value="draft" class="px-6 py-3 border-2 border-gray-300 rounded-xl font-bold text-sm text-gray-800">حفظ مسودة</button>
            <button type="submit" name="action" value="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm">تسليم نهائي</button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function dailyReportForm(initialContacts, leads) {
    return {
        leads,
        contacts: initialContacts.length ? initialContacts : [],
        counts: {},
        addContact(type) {
            this.contacts.push({
                sales_lead_id: '',
                contact_name: '',
                contact_phone: '',
                interaction_type: type,
                client_status: '',
                client_problems: ''
            });
        },
        onLeadPick(index) {
            const row = this.contacts[index];
            const lead = this.leads.find(l => String(l.id) === String(row.sales_lead_id));
            if (lead) {
                row.contact_name = lead.name;
                row.contact_phone = lead.phone || row.contact_phone;
            }
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/sales/daily-reports/edit.blade.php ENDPATH**/ ?>