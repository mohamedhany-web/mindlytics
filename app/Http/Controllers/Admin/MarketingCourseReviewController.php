<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseReview;
use App\Services\CourseReviewStorageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MarketingCourseReviewController extends Controller
{
    public function __construct(
        protected CourseReviewStorageService $storage
    ) {}

    public function index(Request $request)
    {
        $query = CourseReview::query()
            ->where('is_marketing', true)
            ->with('course')
            ->latest();

        if ($request->filled('course_id')) {
            $query->where('course_id', (int) $request->course_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('type')) {
            if ($request->type === 'image') {
                $query->whereNotNull('image_path')->where('image_path', '!=', '');
            } elseif ($request->type === 'text') {
                $query->where(function ($q) {
                    $q->whereNull('image_path')->orWhere('image_path', '');
                });
            }
        }

        $reviews = $query->paginate(20)->withQueryString();
        $courses = AdvancedCourse::query()
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.marketing-course-reviews.index', compact('reviews', 'courses'));
    }

    public function create()
    {
        $courses = AdvancedCourse::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title']);

        $uploadLimitLabel = ini_get('upload_max_filesize') ?: '2M';
        $reviewsDisk = (string) config('filesystems.course_reviews_disk', 'r2');

        return view('admin.marketing-course-reviews.create', compact('courses', 'uploadLimitLabel', 'reviewsDisk'));
    }

    public function store(Request $request)
    {
        $this->assertUploadedImageIsValid($request);

        // بعد ضغط المتصفح عادةً أقل من 2MB؛ نسمح حتى حد PHP الفعلي أو 12MB أيهما أصغر منطقياً للتحقق
        $maxKb = max(1536, $this->uploadMaxKilobytes());

        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'review_type' => ['required', Rule::in(['image', 'text', 'quote'])],
            'reviewer_name' => 'nullable|string|max:120',
            'source' => 'nullable|string|max:80',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => [
                Rule::requiredIf(fn () => in_array($request->input('review_type'), ['text', 'quote'], true)
                    || ! $request->hasFile('image')),
                'nullable',
                'string',
                'max:2000',
            ],
            'image' => [
                Rule::requiredIf(fn () => $request->input('review_type') === 'image'),
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,webp,gif',
                'max:'.$maxKb,
            ],
            'is_featured' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
        ], [
            'course_id.required' => 'اختر الكورس',
            'course_id.exists' => 'الكورس المحدد غير موجود',
            'review_type.required' => 'اختر نوع الريفيو',
            'review_type.in' => 'نوع الريفيو غير صالح',
            'comment.required' => 'اكتب نص التقييم',
            'image.required' => 'صورة الريفيو مطلوبة لهذا النوع',
            'image.file' => 'الملف المرفوع غير صالح',
            'image.mimes' => 'الصيغة المسموحة: JPG, PNG, WEBP, GIF فقط (HEIC غير مدعوم)',
            'image.max' => 'حجم الصورة كبير جداً بعد الرفع. الصفحة تضغط الصورة تلقائياً — أعد اختيارها أو صغّرها يدوياً.',
        ]);

        $path = null;
        $disk = null;
        if ($request->hasFile('image')) {
            try {
                $stored = $this->storage->storeImage($request->file('image'));
                $path = $stored['path'];
                $disk = $stored['disk'];
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'image' => $e->getMessage() ?: 'تعذر رفع الصورة على Cloudflare',
                ]);
            }
        }

        $comment = filled($validated['comment'] ?? null)
            ? trim((string) $validated['comment'])
            : null;

        $source = filled($validated['source'] ?? null)
            ? trim((string) $validated['source'])
            : null;

        if ($source && $comment) {
            $comment = $comment."\n\nالمصدر: ".$source;
        } elseif ($source && ! $comment) {
            $comment = 'المصدر: '.$source;
        }

        $isQuote = ($validated['review_type'] ?? '') === 'quote';

        CourseReview::create([
            'course_id' => (int) $validated['course_id'],
            'user_id' => null,
            'reviewer_name' => filled($validated['reviewer_name'] ?? null)
                ? trim((string) $validated['reviewer_name'])
                : null,
            'rating' => (int) ($validated['rating'] ?? 5),
            'review' => $comment,
            'comment' => $comment,
            'image_path' => $path,
            'image_disk' => $disk,
            'is_marketing' => true,
            'is_approved' => $request->boolean('is_approved', true),
            'is_featured' => $request->boolean('is_featured') || $isQuote,
            'status' => $request->boolean('is_approved', true) ? 'approved' : 'pending',
            'is_verified_purchase' => false,
            'helpful_count' => 0,
        ]);

        $where = $disk === 'r2' || $disk === 's3' ? 'Cloudflare R2' : 'التخزين المحلي';

        return redirect()
            ->route('admin.marketing-course-reviews.index', ['course_id' => $validated['course_id']])
            ->with('success', $path
                ? 'تم إضافة الريفيو بنجاح (الصورة على '.$where.')'
                : 'تم إضافة الريفيو بنجاح');
    }

    public function destroy(CourseReview $marketing_course_review)
    {
        $review = $marketing_course_review;
        if (! $review->is_marketing) {
            abort(404);
        }

        $this->storage->deleteIfExists($review->image_path, $review->image_disk);
        $review->image_path = null;
        $review->image_disk = null;
        $review->delete();

        return redirect()
            ->route('admin.marketing-course-reviews.index')
            ->with('success', 'تم حذف الريفيو');
    }

    public function toggleApprove(CourseReview $marketing_course_review)
    {
        $review = $marketing_course_review;
        if (! $review->is_marketing) {
            abort(404);
        }

        $review->is_approved = ! $review->is_approved;
        $review->status = $review->is_approved ? 'approved' : 'pending';
        $review->save();

        return back()->with('success', $review->is_approved ? 'تم نشر الريفيو' : 'تم إخفاء الريفيو');
    }

    private function assertUploadedImageIsValid(Request $request): void
    {
        if (! $request->hasFile('image')) {
            $fileError = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
            if ((int) $fileError !== UPLOAD_ERR_NO_FILE) {
                throw ValidationException::withMessages([
                    'image' => $this->uploadErrorMessage((int) $fileError),
                ]);
            }

            return;
        }

        $file = $request->file('image');
        if ($file && ! $file->isValid()) {
            throw ValidationException::withMessages([
                'image' => $this->uploadErrorMessage($file->getError()),
            ]);
        }
    }

    private function uploadErrorMessage(int $error): string
    {
        $limit = ini_get('upload_max_filesize') ?: '2M';

        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'حجم الصورة أكبر من حد السيرفر ('.$limit.'). الصفحة تضغط الصورة تلقائياً قبل الإرسال — أعد اختيار الصورة وانتظر رسالة «تم الضغط»، أو شغّل السيرفر بـ: php -c php-upload.ini artisan serve',
            UPLOAD_ERR_PARTIAL => 'تم رفع جزء من الصورة فقط. حاول مرة أخرى.',
            UPLOAD_ERR_NO_FILE => 'لم يتم اختيار صورة.',
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE => 'تعذر حفظ الصورة على السيرفر. تأكد من صلاحيات مجلد التخزين.',
            default => 'فشل رفع الصورة. جرّب صورة JPG أو PNG.',
        };
    }

    private function uploadMaxKilobytes(): int
    {
        $bytes = $this->iniBytes(ini_get('upload_max_filesize') ?: '2M');
        $postBytes = $this->iniBytes(ini_get('post_max_size') ?: '8M');
        $allowed = min($bytes, $postBytes);
        $kb = (int) floor(($allowed * 0.95) / 1024);

        return max(512, min(12288, $kb));
    }

    private function iniBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 2 * 1024 * 1024;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $number,
        };
    }
}
