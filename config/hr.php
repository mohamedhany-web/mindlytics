<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rule-Based ATS — أوزان التقييم
    |--------------------------------------------------------------------------
    */
    'scoring_weights' => [
        'skills' => 0.60,
        'experience' => 0.30,
        'education' => 0.10,
    ],

    /*
    |--------------------------------------------------------------------------
    | مستويات التعليم (من الأدنى للأعلى)
    |--------------------------------------------------------------------------
    */
    'education_levels' => [
        'high_school' => ['label' => 'ثانوية عامة', 'rank' => 1],
        'diploma' => ['label' => 'دبلوم', 'rank' => 2],
        'bachelor' => ['label' => 'بكالوريوس', 'rank' => 3],
        'master' => ['label' => 'ماجستير', 'rank' => 4],
        'phd' => ['label' => 'دكتوراه', 'rank' => 5],
    ],

    /*
    |--------------------------------------------------------------------------
    | مصفوفة تقييم التعليم (%)
    | المطلوب => [ المتقدم => النسبة ]
    |--------------------------------------------------------------------------
    */
    'education_score_matrix' => [
        'bachelor' => [
            'bachelor' => 100,
            'master' => 100,
            'phd' => 100,
            'diploma' => 50,
            'high_school' => 20,
        ],
        'master' => [
            'master' => 100,
            'phd' => 100,
            'bachelor' => 70,
            'diploma' => 40,
            'high_school' => 15,
        ],
        'diploma' => [
            'diploma' => 100,
            'bachelor' => 100,
            'master' => 100,
            'phd' => 100,
            'high_school' => 40,
        ],
        'high_school' => [
            'high_school' => 100,
            'diploma' => 100,
            'bachelor' => 100,
            'master' => 100,
            'phd' => 100,
        ],
        'phd' => [
            'phd' => 100,
            'master' => 80,
            'bachelor' => 50,
            'diploma' => 30,
            'high_school' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | قاموس مهارات شائعة للاستخراج من نص السيرة
    |--------------------------------------------------------------------------
    */
    'skill_keywords' => [
        'excel', 'sql', 'power bi', 'python', 'javascript', 'php', 'laravel',
        'react', 'vue', 'angular', 'node.js', 'nodejs', 'java', 'c#', 'c++',
        'html', 'css', 'tailwind', 'bootstrap', 'mysql', 'postgresql', 'mongodb',
        'redis', 'docker', 'kubernetes', 'aws', 'azure', 'git', 'figma',
        'photoshop', 'illustrator', 'tableau', 'spss', 'r', 'matlab',
        'machine learning', 'deep learning', 'tensorflow', 'pytorch',
        'data analysis', 'data analytics', 'business intelligence',
        'project management', 'agile', 'scrum', 'salesforce', 'sap',
        'oracle', 'wordpress', 'seo', 'digital marketing', 'content writing',
        'communication', 'leadership', 'teamwork', 'english', 'arabic',
        'إكسل', 'بايثون', 'جافا', 'تحليل بيانات', 'ذكاء اصطناعي',
    ],

    'education_keywords' => [
        'phd' => ['phd', 'doctorate', 'doctoral', 'دكتوراه', 'دكتورا'],
        'master' => ['master', 'mba', 'msc', 'm.sc', 'ma', 'm.a', 'ماجستير'],
        'bachelor' => ['bachelor', 'b.sc', 'bsc', 'ba', 'b.a', 'degree', 'university', 'بكالوريوس', 'جامعة', 'ليسانس'],
        'diploma' => ['diploma', 'associate', 'دبلوم', 'معهد'],
        'high_school' => ['high school', 'secondary', 'ثانوية', 'ثانوي'],
    ],
];
