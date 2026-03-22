<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LectureVideoQuestion extends Model
{
    protected $fillable = [
        'lecture_id',
        'timestamp_seconds',
        'show_at_end',
        'question_source',
        'question_id',
        'custom_question_text',
        'custom_options',
        'custom_correct_answer',
        'on_wrong',
        'rewind_seconds',
        'points',
        'show_count',
        'order',
    ];

    protected $casts = [
        'custom_options' => 'array',
        'timestamp_seconds' => 'integer',
        'show_at_end' => 'boolean',
        'rewind_seconds' => 'integer',
        'points' => 'integer',
        'show_count' => 'integer',
        'order' => 'integer',
    ];

    public const SOURCE_BANK = 'bank';
    public const SOURCE_CUSTOM = 'custom';
    public const ON_WRONG_REWIND = 'rewind';
    public const ON_WRONG_CONTINUE = 'continue';

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LectureVideoQuestionAnswer::class, 'lecture_video_question_id');
    }

    /**
     * تطبيع عناصر الخيارات إلى نصوص (يدعم مصفوفة نصوص أو عناصر كمصفوفات/كائنات).
     */
    public static function normalizeOptionsForDisplay(mixed $options): array
    {
        if (! is_array($options)) {
            return [];
        }
        $out = [];
        foreach (array_values($options) as $item) {
            if (is_string($item) || is_numeric($item)) {
                $out[] = trim((string) $item);
            } elseif (is_array($item)) {
                $t = $item['text'] ?? $item['label'] ?? $item['value'] ?? $item['option'] ?? null;
                $out[] = $t !== null ? trim((string) $t) : '';
            } elseif (is_object($item)) {
                $t = $item->text ?? $item->label ?? $item->value ?? null;
                $out[] = $t !== null ? trim((string) $t) : '';
            } else {
                $out[] = '';
            }
        }

        return array_values(array_filter($out, fn ($s) => $s !== ''));
    }

    /**
     * بيانات السؤال جاهزة للعرض (عنوان + خيارات + إجابة صحيحة للتحقق فقط).
     */
    public function getPayloadForStudent(): array
    {
        if ($this->question_source === self::SOURCE_BANK && $this->question_id) {
            $q = $this->relationLoaded('question') ? $this->question : $this->question()->first();
            if (! $q) {
                return ['id' => $this->id, 'text' => '', 'options' => [], 'type' => 'multiple_choice', 'points' => $this->points];
            }
            // عرض السؤال المرتبط بالمحاضرة حتى لو أُلغي تفعيله في البنك لاحقاً (تجنب محتوى فارغ للطالب)
            $text = trim((string) ($q->question ?? ''));
            $type = $q->type ?? 'multiple_choice';
            $options = self::normalizeOptionsForDisplay($q->options ?? []);

            if ($type === 'true_false' && count($options) < 2) {
                $options = ['صح', 'خطأ'];
            }

            return [
                'id' => $this->id,
                'text' => $text,
                'options' => $options,
                'type' => $type,
                'points' => $this->points,
            ];
        }

        $customOpts = is_array($this->custom_options) ? self::normalizeOptionsForDisplay($this->custom_options) : [];

        return [
            'id' => $this->id,
            'text' => trim((string) ($this->custom_question_text ?? '')),
            'options' => $customOpts,
            'type' => 'multiple_choice',
            'points' => $this->points,
        ];
    }

    /**
     * التحقق من صحة الإجابة (لا نرسل الإجابة الصحيحة للعميل).
     */
    public function checkAnswer(string $answer): bool
    {
        if ($this->question_source === self::SOURCE_BANK && $this->question_id) {
            return $this->question->isCorrectAnswer($answer);
        }
        $correct = trim((string) $this->custom_correct_answer);
        return strcasecmp(trim($answer), $correct) === 0;
    }
}
