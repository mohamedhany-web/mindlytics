<?php

return [
    /*
    |--------------------------------------------------------------------------
    | أكواد الدول والتحقق من أرقام الهواتف
    | للتوسع: أضف أي دولة مع dial_code و validation (regex أو min_length/max_length)
    |--------------------------------------------------------------------------
    */
    'countries' => [
        [
            'code' => 'SA',
            'dial_code' => '+966',
            'name_ar' => 'السعودية',
            'name_en' => 'Saudi Arabia',
            'validation' => ['regex' => '/^[15]\d{8}$/'], // 9 أرقام تبدأ بـ 5 أو 1
            'placeholder' => '5xxxxxxxx',
            'example' => '501234567',
        ],
        [
            'code' => 'EG',
            'dial_code' => '+20',
            'name_ar' => 'مصر',
            'name_en' => 'Egypt',
            'validation' => ['regex' => '/^1[0125]\d{8}$/'], // 10 أرقام تبدأ بـ 10, 11, 12, 15
            'placeholder' => '1xxxxxxxxx',
            'example' => '1012345678',
        ],
        [
            'code' => 'JO',
            'dial_code' => '+962',
            'name_ar' => 'الأردن',
            'name_en' => 'Jordan',
            'validation' => ['regex' => '/^[789]\d{7}$/'], // 8 أرقام تبدأ بـ 7, 8, 9
            'placeholder' => '7xxxxxxxx',
            'example' => '791234567',
        ],
        [
            'code' => 'AE',
            'dial_code' => '+971',
            'name_ar' => 'الإمارات',
            'name_en' => 'UAE',
            'validation' => ['regex' => '/^5[0-9]\d{7}$/'], // 9 أرقام تبدأ بـ 5x
            'placeholder' => '5xxxxxxxx',
            'example' => '501234567',
        ],
        [
            'code' => 'KW',
            'dial_code' => '+965',
            'name_ar' => 'الكويت',
            'name_en' => 'Kuwait',
            'validation' => ['regex' => '/^[569]\d{7}$/'], // 8 أرقام
            'placeholder' => '5xxxxxxxx',
            'example' => '50123456',
        ],
        [
            'code' => 'BH',
            'dial_code' => '+973',
            'name_ar' => 'البحرين',
            'name_en' => 'Bahrain',
            'validation' => ['regex' => '/^[36]\d{7}$/'], // 8 أرقام
            'placeholder' => '3xxxxxxxx',
            'example' => '36123456',
        ],
        [
            'code' => 'QA',
            'dial_code' => '+974',
            'name_ar' => 'قطر',
            'name_en' => 'Qatar',
            'validation' => ['regex' => '/^[3-7]\d{7}$/'], // 8 أرقام
            'placeholder' => '3xxxxxxxx',
            'example' => '33123456',
        ],
        [
            'code' => 'OM',
            'dial_code' => '+968',
            'name_ar' => 'عُمان',
            'name_en' => 'Oman',
            'validation' => ['regex' => '/^[79]\d{7}$/'], // 8 أرقام
            'placeholder' => '9xxxxxxxx',
            'example' => '91234567',
        ],
        [
            'code' => 'IQ',
            'dial_code' => '+964',
            'name_ar' => 'العراق',
            'name_en' => 'Iraq',
            'validation' => ['regex' => '/^7[0-9]\d{8}$/'], // 10 أرقام تبدأ بـ 7
            'placeholder' => '7xxxxxxxxx',
            'example' => '7912345678',
        ],
        [
            'code' => 'SY',
            'dial_code' => '+963',
            'name_ar' => 'سوريا',
            'name_en' => 'Syria',
            'validation' => ['regex' => '/^9\d{8}$/'], // 9 أرقام تبدأ بـ 9
            'placeholder' => '9xxxxxxxx',
            'example' => '912345678',
        ],
        [
            'code' => 'LB',
            'dial_code' => '+961',
            'name_ar' => 'لبنان',
            'name_en' => 'Lebanon',
            'validation' => ['regex' => '/^[37]\d{7}$/'], // 8 أرقام
            'placeholder' => '3xxxxxxxx',
            'example' => '71123456',
        ],
        [
            'code' => 'YE',
            'dial_code' => '+967',
            'name_ar' => 'اليمن',
            'name_en' => 'Yemen',
            'validation' => ['regex' => '/^7[0-9]\d{7}$/'], // 9 أرقام
            'placeholder' => '7xxxxxxxx',
            'example' => '712345678',
        ],
        [
            'code' => 'PS',
            'dial_code' => '+970',
            'name_ar' => 'فلسطين',
            'name_en' => 'Palestine',
            'validation' => ['regex' => '/^5[0-9]\d{7}$/'], // 9 أرقام
            'placeholder' => '5xxxxxxxx',
            'example' => '591234567',
        ],
        [
            'code' => 'SD',
            'dial_code' => '+249',
            'name_ar' => 'السودان',
            'name_en' => 'Sudan',
            'validation' => ['regex' => '/^9[0-9]\d{7}$/'], // 9 أرقام
            'placeholder' => '9xxxxxxxx',
            'example' => '912345678',
        ],
        [
            'code' => 'MA',
            'dial_code' => '+212',
            'name_ar' => 'المغرب',
            'name_en' => 'Morocco',
            'validation' => ['regex' => '/^[5-7]\d{8}$/'], // 9 أرقام
            'placeholder' => '6xxxxxxxx',
            'example' => '612345678',
        ],
        [
            'code' => 'DZ',
            'dial_code' => '+213',
            'name_ar' => 'الجزائر',
            'name_en' => 'Algeria',
            'validation' => ['regex' => '/^[5-7]\d{8}$/'], // 9 أرقام
            'placeholder' => '5xxxxxxxx',
            'example' => '551234567',
        ],
        [
            'code' => 'TN',
            'dial_code' => '+216',
            'name_ar' => 'تونس',
            'name_en' => 'Tunisia',
            'validation' => ['regex' => '/^[2-9]\d{7}$/'], // 8 أرقام
            'placeholder' => '2xxxxxxxx',
            'example' => '20123456',
        ],
        [
            'code' => 'LY',
            'dial_code' => '+218',
            'name_ar' => 'ليبيا',
            'name_en' => 'Libya',
            'validation' => ['regex' => '/^9[0-9]\d{7}$/'], // 9 أرقام
            'placeholder' => '9xxxxxxxx',
            'example' => '912345678',
        ],
        [
            'code' => 'OTHER',
            'dial_code' => '',
            'name_ar' => 'دولة أخرى',
            'name_en' => 'Other',
            'validation' => ['regex' => '/^\d{6,15}$/'], // من 6 إلى 15 رقم
            'placeholder' => 'رقم الهاتف',
            'example' => '',
        ],
    ],

    'default_country' => 'SA',
];
