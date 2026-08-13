<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InternshipController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->get('type');
        $q = trim((string) $request->get('q', ''));

        $internships = Internship::query()
            ->openForApply()
            ->when(in_array($type, ['onsite', 'remote', 'hybrid'], true), fn ($builder) => $builder->where('type', $type))
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', "%{$q}%")
                        ->orWhere('department', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%")
                        ->orWhere('summary', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'open' => Internship::openForApply()->count(),
            'featured' => Internship::openForApply()->where('is_featured', true)->count(),
        ];

        return view('public.internships.index', compact('internships', 'type', 'q', 'stats'));
    }

    public function show(string $slug): View
    {
        $internship = Internship::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        abort_unless(in_array($internship->status, [Internship::STATUS_OPEN, Internship::STATUS_CLOSED], true), 404);

        return view('public.internships.show', compact('internship'));
    }

    public function apply(Request $request, string $slug): RedirectResponse
    {
        $internship = Internship::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        abort_unless($internship->isOpenForApply(), 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:60',
            'university' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'year_of_study' => 'nullable|string|max:50',
            'portfolio_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'cover_letter' => 'nullable|string|max:5000',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد غير صحيحة.',
            'cv.mimes' => 'صيغة السيرة الذاتية يجب أن تكون PDF أو Word.',
            'cv.max' => 'حجم ملف السيرة الذاتية حد أقصى 5 ميجابايت.',
        ]);

        $exists = InternshipApplication::query()
            ->where('internship_id', $internship->id)
            ->where('email', $data['email'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'لقد قدّمت بالفعل على هذه الفرصة بهذا البريد.']);
        }

        InternshipApplication::create([
            'internship_id' => $internship->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'university' => $data['university'] ?? null,
            'major' => $data['major'] ?? null,
            'year_of_study' => $data['year_of_study'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'cover_letter' => $data['cover_letter'] ?? null,
            'cv_path' => $this->storeCv($request),
            'status' => InternshipApplication::STATUS_PENDING,
            'source' => 'website',
        ]);

        return redirect()
            ->route('public.internships.show', $internship->slug)
            ->with('success', 'تم إرسال طلبك بنجاح. سيتواصل معك فريق التدريب بعد المراجعة.');
    }

    private function storeCv(Request $request): ?string
    {
        if (! $request->hasFile('cv')) {
            return null;
        }

        $dir = public_path('internships/cvs');
        File::ensureDirectoryExists($dir);

        $file = $request->file('cv');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');
        $name = 'cv-' . Str::uuid() . '.' . $ext;
        $file->move($dir, $name);

        return 'internships/cvs/' . $name;
    }
}
