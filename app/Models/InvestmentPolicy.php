<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentPolicy extends Model
{
    protected $fillable = [
        'overview',
        'eligibility_rules',
        'legal_framework',
        'terms_conditions',
        'privacy_notice',
        'process_description',
        'disclaimer',
        'contact_email',
        'contact_phone',
        'updated_by',
    ];

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): self
    {
        $policy = self::query()->first();

        if ($policy) {
            return $policy;
        }

        return self::query()->create(self::defaultContent());
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultContent(): array
    {
        $contact = \App\Support\PlatformSettings::contactPage();
        $appName = config('app.name', 'Mindlytics');

        return [
            'overview' => "قسم الاستثمار في {$appName} مخصص للمستثمرين والشركاء الاستراتيجيين الراغبين في المشاركة في نمو أكاديمية البرمجة والتعليم التقني. نلتزم بشفافية كاملة في عرض الفرص، المخاطر، والعوائد المتوقعة قبل أي اتفاق رسمي.",
            'eligibility_rules' => "• أن يكون المتقدم فرداً أو كياناً قانونياً مصرحاً له بالاستثمار وفق القانون المصري.\n• الالتزام بالحد الأدنى والحد الأقصى لكل خطة استثمارية.\n• تقديم بيانات صحيحة وقابلة للتحقق (هوية، سجل تجاري عند الحاجة).\n• قبول إجراء العناية الواجبة (Due Diligence) قبل توقيع أي عقد.",
            'legal_framework' => "• تخضع جميع العلاقات الاستثمارية للقوانين المصرية المعمول بها، بما في ذلك قوانين الشركات والاستثمار وحماية المستهلك حيث ينطبق.\n• لا تُعد المعلومات المنشورة على المنصة عرضاً عاماً أو التزاماً ملزماً — يتم التعاقد فقط بموجب عقد موقع بين الطرفين.\n• تُحفظ بيانات المستثمرين بسرية وفق سياسة الخصوصية الخاصة بالمنصة.\n• تحتفظ {$appName} بحق قبول أو رفض أي طلب دون إبداء أسباب.",
            'terms_conditions' => "• العوائد والأرقام المذكورة في الخطط تقديرية وليست ضماناً للربح.\n• تُحدَّد آلية التوزيع، مدة الاستثمار، وحقوق الحصص في العقد النهائي فقط.\n• قد تُطلب مستندات إضافية (هوية، سجل تجاري، إثبات مصدر أموال).\n• لا يحق للمستثمر التنازل عن حصته أو حقوقه إلا وفق شروط العقد الموقّع.",
            'privacy_notice' => 'نستخدم بياناتك للتواصل بخصوص طلب الاستثمار ومتابعة مراحل المراجعة فقط. لا نشارك بياناتك مع أطراف ثالثة إلا بموافقتك الصريحة أو بموجب القانون.',
            'process_description' => "1. استعراض الخطط الاستثمارية المتاحة على صفحة الاستثمار العامة.\n2. اختيار الخطة المناسبة وتعبئة نموذج التقديم (الاسم، التواصل، المبلغ المقترح).\n3. مراجعة طلبك من فريق {$appName} خلال 3–5 أيام عمل.\n4. اجتماع تعريفي (أونلاين أو حضوري) لمناقشة التفاصيل.\n5. إجراءات العناية الواجبة والتفاوض على الشروط.\n6. توقيع العقد وإتمام الإجراءات القانونية والمالية.",
            'disclaimer' => 'تحذير: الاستثمار في قطاع التعليم والتقنية ينطوي على مخاطر. الأرقام المعروضة للتوضيح فقط وليست نصيحة مالية أو قانونية. يُنصح باستشارة مستشارك القانوني والمالي قبل أي التزام.',
            'contact_email' => $contact['email'] ?? 'info@mindlytics-academy.com',
            'contact_phone' => $contact['phone'] ?? '01044610507',
        ];
    }
}
