<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionBank;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller
{
    /**
     * عرض بنك الأسئلة
     */
    public function index(Request $request)
    {
        $query = Question::with(['category', 'questionBank']);

        // فلترة حسب التصنيف
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // فلترة حسب نوع السؤال
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب مستوى الصعوبة
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // البحث في النص
        if ($request->filled('search')) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // بيانات للفلاتر
        $categories = QuestionCategory::active()->orderBy('name')->get();
        $questionTypes = Question::getQuestionTypes();
        $difficultyLevels = Question::getDifficultyLevels();

        // إحصائيات
        $stats = [
            'total_questions' => Question::count(),
            'active_questions' => Question::active()->count(),
            'categories_count' => QuestionCategory::count(),
            'by_type' => Question::selectRaw('type, count(*) as count')
                               ->groupBy('type')
                               ->pluck('count', 'type')
                               ->toArray(),
        ];

        return view('admin.question-bank.index', compact(
            'questions', 'categories', 'questionTypes', 'difficultyLevels', 'stats'
        ));
    }

    /**
     * عرض صفحة إضافة سؤال جديد
     */
    public function create(Request $request)
    {
        $categories = QuestionCategory::active()->orderBy('name')->get();
        $questionTypes = Question::getQuestionTypes();
        $difficultyLevels = Question::getDifficultyLevels();
        
        // إذا تم تمرير تصنيف محدد
        $selectedCategory = $request->get('category_id');

        return view('admin.question-bank.create', compact(
            'categories', 'questionTypes', 'difficultyLevels', 'selectedCategory'
        ));
    }

    /**
     * حفظ سؤال جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:question_categories,id',
            'question' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(Question::getQuestionTypes())),
            'difficulty_level' => 'required|in:' . implode(',', array_keys(Question::getDifficultyLevels())),
            'points' => 'required|numeric|min:0.5|max:100',
            'time_limit' => 'nullable|integer|min:10|max:600',
            'explanation' => 'nullable|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'is_active' => 'boolean',
        ], [
            'category_id.required' => 'التصنيف مطلوب',
            'question.required' => 'نص السؤال مطلوب',
            'type.required' => 'نوع السؤال مطلوب',
            'difficulty_level.required' => 'مستوى الصعوبة مطلوب',
            'points.required' => 'درجة السؤال مطلوبة',
            'points.min' => 'درجة السؤال يجب أن تكون 0.5 على الأقل',
            'points.max' => 'درجة السؤال لا يجب أن تتجاوز 100',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        // معالجة التاجز
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // رفع الصورة
        if ($request->hasFile('image')) {
            $data['image_url'] = $this->handleImageUpload($request->file('image'));
        }

        // معالجة الخيارات والإجابات حسب نوع السؤال
        $data = $this->processQuestionData($data, $request);

        Question::create($data);

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'تم إضافة السؤال بنجاح');
    }

    /**
     * عرض تفاصيل السؤال
     */
    public function show(Question $question)
    {
        $question->load(['category', 'questionBank']);
        
        return view('admin.question-bank.show', compact('question'));
    }

    /**
     * عرض صفحة تعديل السؤال
     */
    public function edit(Question $question)
    {
        $categories = QuestionCategory::active()->orderBy('name')->get();
        $questionTypes = Question::getQuestionTypes();
        $difficultyLevels = Question::getDifficultyLevels();

        return view('admin.question-bank.edit', compact(
            'question', 'categories', 'questionTypes', 'difficultyLevels'
        ));
    }

    /**
     * تحديث السؤال
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'category_id' => 'required|exists:question_categories,id',
            'question' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(Question::getQuestionTypes())),
            'difficulty_level' => 'required|in:' . implode(',', array_keys(Question::getDifficultyLevels())),
            'points' => 'required|numeric|min:0.5|max:100',
            'time_limit' => 'nullable|integer|min:10|max:600',
            'explanation' => 'nullable|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        // معالجة التاجز
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // معالجة الصور
        if ($request->has('remove_image') && $request->remove_image == '1') {
            // حذف الصورة الحالية
            if ($question->image_url && Storage::disk('public')->exists($question->image_url)) {
                Storage::disk('public')->delete($question->image_url);
            }
            $data['image_url'] = null;
        } elseif ($request->hasFile('image')) {
            // رفع صورة جديدة
            if ($question->image_url && Storage::disk('public')->exists($question->image_url)) {
                Storage::disk('public')->delete($question->image_url);
            }
            
            $data['image_url'] = $this->handleImageUpload($request->file('image'));
        }

        // معالجة الخيارات والإجابات
        $data = $this->processQuestionData($data, $request);

        $question->update($data);

        return redirect()->route('admin.question-bank.show', $question)
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    /**
     * حذف السؤال
     */
    public function destroy(Question $question)
    {
        // التحقق من عدم استخدام السؤال في امتحانات
        if ($question->examQuestions()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف السؤال لأنه مستخدم في امتحانات');
        }

        // حذف الصورة
        if ($question->image_url && Storage::disk('public')->exists($question->image_url)) {
            Storage::disk('public')->delete($question->image_url);
        }

        $question->delete();

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'تم حذف السؤال بنجاح');
    }

    /**
     * معالجة بيانات السؤال حسب نوعه
     */
    private function processQuestionData($data, $request)
    {
        switch ($data['type']) {
            case 'multiple_choice':
                $data['options'] = array_filter([
                    $request->input('option_1'),
                    $request->input('option_2'),
                    $request->input('option_3'),
                    $request->input('option_4'),
                    $request->input('option_5'),
                ]);
                $data['correct_answer'] = [$request->input('correct_option')];
                break;

            case 'true_false':
                $data['options'] = ['صح', 'خطأ'];
                $data['correct_answer'] = [$request->input('true_false_answer')];
                break;

            case 'fill_blank':
                $data['options'] = null;
                $answers = array_filter(array_map('trim', explode(',', $request->input('correct_answers', ''))));
                $data['correct_answer'] = $answers;
                break;

            case 'short_answer':
            case 'essay':
                $data['options'] = null;
                $data['correct_answer'] = $request->input('model_answer') ? [$request->input('model_answer')] : null;
                break;

            case 'matching':
                $leftItems = array_filter(explode("\n", $request->input('left_items', '')));
                $rightItems = array_filter(explode("\n", $request->input('right_items', '')));
                $data['options'] = [
                    'left' => $leftItems,
                    'right' => $rightItems,
                ];
                $data['correct_answer'] = $request->input('matching_pairs', []);
                break;

            case 'ordering':
                $items = array_filter(explode("\n", $request->input('ordering_items', '')));
                $data['options'] = $items;
                $data['correct_answer'] = $request->input('correct_order', []);
                break;
        }

        return $data;
    }

    /**
     * تصدير الأسئلة
     */
    public function export(Request $request)
    {
        // يمكن تطوير هذه الوظيفة لتصدير الأسئلة إلى Excel أو JSON
        return response()->json(['message' => 'سيتم تطوير وظيفة التصدير قريباً']);
    }

    /**
     * استيراد الأسئلة
     */
    public function import(Request $request)
    {
        // يمكن تطوير هذه الوظيفة لاستيراد الأسئلة من Excel أو JSON
        return response()->json(['message' => 'سيتم تطوير وظيفة الاستيراد قريباً']);
    }

    /**
     * نسخ السؤال
     */
    public function duplicate(Question $question)
    {
        $newQuestion = $question->replicate();
        $newQuestion->question = $question->question . ' - نسخة';
        $newQuestion->save();

        return redirect()->route('admin.question-bank.edit', $newQuestion)
            ->with('success', 'تم نسخ السؤال بنجاح');
    }

    /**
     * معالجة رفع الصور مع تحسين الجودة والحجم
     */
    private function handleImageUpload($imageFile)
    {
        // إنشاء اسم فريد للملف
        $fileName = uniqid('question_') . '.' . $imageFile->getClientOriginalExtension();
        
        // مسار التخزين
        $storagePath = 'questions/' . date('Y/m');
        $fullPath = 'storage/' . $storagePath . '/' . $fileName;
        
        // إنشاء المجلد إذا لم يكن موجوداً
        if (!Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->makeDirectory($storagePath);
        }
        
        // حفظ الصورة الأصلية
        $imageFile->storeAs($storagePath, $fileName, 'public');
        
        // تحسين الصورة باستخدام Intervention Image إذا كانت متوفرة
        try {
            $fullStoragePath = storage_path('app/public/' . $storagePath . '/' . $fileName);
            
            // قراءة الصورة
            $imageData = getimagesize($fullStoragePath);
            $mimeType = $imageData['mime'];
            
            // تحديد نوع الصورة وتحسينها
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fullStoragePath);
                    $this->optimizeImage($image, $fullStoragePath, 85); // جودة 85%
                    imagejpeg($image, $fullStoragePath, 85);
                    imagedestroy($image);
                    break;
                    
                case 'image/png':
                    $image = imagecreatefrompng($fullStoragePath);
                    $this->optimizeImage($image, $fullStoragePath, 9); // ضغط 9 للـ PNG
                    imagepng($image, $fullStoragePath, 9);
                    imagedestroy($image);
                    break;
                    
                case 'image/gif':
                    // الاحتفاظ بـ GIF كما هو لأنه قد يكون متحرك
                    break;
            }
        } catch (\Exception $e) {
            // في حالة فشل التحسين، نتجاهل الخطأ ونحتفظ بالصورة الأصلية
            \Log::warning('فشل في تحسين الصورة: ' . $e->getMessage());
        }
        
        return $storagePath . '/' . $fileName;
    }

    /**
     * تحسين الصورة (تقليل الحجم إذا كانت كبيرة)
     */
    private function optimizeImage($image, $filePath, $quality)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        // إذا كانت الصورة كبيرة جداً، قم بتقليل حجمها
        $maxWidth = 1200;
        $maxHeight = 1200;
        
        if ($width > $maxWidth || $height > $maxHeight) {
            // حساب النسبة المناسبة
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);
            
            // إنشاء صورة جديدة بالحجم المحسن
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // الحفاظ على الشفافية للـ PNG
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            
            // تغيير حجم الصورة
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // تحديث المتغير
            $image = $resizedImage;
        }
        
        return $image;
    }
}