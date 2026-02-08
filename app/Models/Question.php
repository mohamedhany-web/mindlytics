<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'category_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'difficulty_level',
        'image_url',
        'audio_url',
        'video_url',
        'time_limit',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'time_limit' => 'integer',
        'points' => 'decimal:2',
    ];

    /**
     * العلاقة مع بنك الأسئلة
     */
    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    /**
     * علاقة مع تصنيف السؤال
     */
    public function category()
    {
        return $this->belongsTo(QuestionCategory::class, 'category_id');
    }

    /**
     * العلاقة مع امتحانات السؤال
     */
    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    /**
     * العلاقة مع الامتحانات
     */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')
                    ->withPivot('order', 'marks')
                    ->withTimestamps();
    }

    /**
     * التحقق من صحة الإجابة
     */
    public function isCorrectAnswer($answer)
    {
        if ($this->type === 'multiple_choice') {
            return $answer === $this->correct_answer;
        }
        
        if ($this->type === 'true_false') {
            return strtolower($answer) === strtolower($this->correct_answer);
        }
        
        if ($this->type === 'fill_blank') {
            $correctAnswers = is_array($this->correct_answer) 
                ? $this->correct_answer 
                : [$this->correct_answer];
            
            return in_array(strtolower(trim($answer)), array_map('strtolower', $correctAnswers));
        }
        
        // للأسئلة المقالية، نحتاج تقييم يدوي
        return null;
    }

    /**
     * الحصول على الخيارات مخلوطة
     */
    public function getShuffledOptionsAttribute()
    {
        if ($this->type !== 'multiple_choice' || !$this->options) {
            return $this->options;
        }
        
        $options = $this->options;
        shuffle($options);
        return $options;
    }

    /**
     * تنسيق السؤال للعرض
     */
    public function getFormattedQuestionAttribute()
    {
        return $this->question;
    }

    /**
     * الحصول على أنواع الأسئلة المتاحة
     */
    public static function getQuestionTypes()
    {
        return [
            'multiple_choice' => 'اختيار متعدد',
            'true_false' => 'صح أو خطأ',
            'fill_blank' => 'املأ الفراغ',
            'short_answer' => 'إجابة قصيرة',
            'essay' => 'مقالي',
            'matching' => 'مطابقة',
            'ordering' => 'ترتيب',
        ];
    }

    /**
     * الحصول على مستويات الصعوبة
     */
    public static function getDifficultyLevels()
    {
        return [
            'easy' => 'سهل',
            'medium' => 'متوسط',
            'hard' => 'صعب',
        ];
    }

    /**
     * الحصول على لون مستوى الصعوبة
     */
    public function getDifficultyColorAttribute()
    {
        $colors = [
            'easy' => 'green',
            'medium' => 'yellow',
            'hard' => 'red',
        ];

        return $colors[$this->difficulty_level] ?? 'gray';
    }

    /**
     * الحصول على نص مستوى الصعوبة
     */
    public function getDifficultyTextAttribute()
    {
        $levels = self::getDifficultyLevels();
        return $levels[$this->difficulty_level] ?? 'غير محدد';
    }

    /**
     * الحصول على نوع السؤال
     */
    public function getTypeTextAttribute()
    {
        $types = self::getQuestionTypes();
        return $types[$this->type] ?? 'غير محدد';
    }

    /**
     * الحصول على نوع السؤال (alias for getTypeTextAttribute)
     */
    public function getTypeLabel()
    {
        return $this->getTypeTextAttribute();
    }

    /**
     * الحصول على مستوى الصعوبة (alias for getDifficultyTextAttribute)
     */
    public function getDifficultyLabel()
    {
        return $this->getDifficultyTextAttribute();
    }

    /**
     * scope للأسئلة حسب النوع
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * scope للأسئلة حسب مستوى الصعوبة
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * scope للأسئلة النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * التحقق من وجود وسائط
     */
    public function hasMedia()
    {
        return !empty($this->image_url) || !empty($this->audio_url) || !empty($this->video_url);
    }

    /**
     * الحصول على رابط الصورة الآمن
     */
    public function getImageUrl()
    {
        if (empty($this->image_url)) {
            return null;
        }
        
        // إذا كان الرابط خارجي
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }
        
        // إذا كان ملف محلي
        return asset('storage/' . $this->image_url);
    }

    /**
     * الحصول على رابط الصورة الآمن (attribute)
     */
    public function getSecureImageUrlAttribute()
    {
        return $this->getImageUrl();
    }
}