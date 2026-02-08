<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\AdvancedCourse;
use App\Models\User;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status == 'issued') {
                $query->where(function($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                });
            } elseif ($request->status == 'pending') {
                $query->where(function($q) {
                    $q->where('status', 'pending')->orWhere('is_verified', false);
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $query->paginate(20);

        $stats = [
            'total' => Certificate::count(),
            'issued' => Certificate::where(function($q) {
                $q->where('status', 'issued')->orWhere('is_verified', true);
            })->count(),
            'pending' => Certificate::where(function($q) {
                $q->where('status', 'pending')->orWhere('is_verified', false);
            })->count(),
        ];

        return view('admin.certificates.index', compact('certificates', 'stats'));
    }

    public function create()
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        $courses = AdvancedCourse::where('is_active', true)->get();
        return view('admin.certificates.create', compact('users', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:advanced_courses,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'issued_at' => 'nullable|date',
            'status' => 'required|in:pending,issued,revoked',
        ]);

        $certificate = Certificate::create([
            'certificate_number' => 'CERT-' . str_pad(Certificate::count() + 1, 8, '0', STR_PAD_LEFT),
            'serial_number' => Certificate::generateSerialNumber(),
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'] ?? null,
            'course_name' => $validated['course_id'] ? AdvancedCourse::find($validated['course_id'])->title ?? '' : ($validated['title'] ?? ''),
            'certificate_type' => 'completion',
            'title' => $validated['title'] ?? '',
            'description' => $validated['description'] ?? null,
            'issue_date' => $validated['issued_at'] ?? now(),
            'issued_at' => $validated['issued_at'] ?? now(),
            'verification_code' => strtoupper(uniqid('CERT')),
            'status' => $validated['status'] ?? 'pending',
            'is_verified' => $validated['status'] === 'issued',
            'instructor_id' => $validated['instructor_id'] ?? null,
            'academy_signature_name' => $validated['academy_signature_name'] ?? 'المدير العام',
            'academy_signature_title' => $validated['academy_signature_title'] ?? 'Mindlytics Academy',
            'instructor_signature_name' => $validated['instructor_signature_name'] ?? null,
            'instructor_signature_title' => $validated['instructor_signature_title'] ?? 'المدرب المعتمد',
        ]);

        // Generate certificate hash and verification URL
        if ($validated['status'] === 'issued') {
            $certificate->certificate_hash = $certificate->generateHash();
            $certificate->verification_url = $certificate->verification_url;
            $certificate->certified_at = now();
            $certificate->save();
        }

        return redirect()->route('admin.certificates.index')
            ->with('success', 'تم إنشاء الشهادة بنجاح');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'course']);
        return view('admin.certificates.show', compact('certificate'));
    }

    public function edit(Certificate $certificate)
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        $courses = AdvancedCourse::where('is_active', true)->get();
        return view('admin.certificates.edit', compact('certificate', 'users', 'courses'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:advanced_courses,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'issued_at' => 'nullable|date',
            'status' => 'required|in:pending,issued,revoked',
        ]);

        $updateData = $validated;
        if (isset($validated['issued_at'])) {
            $updateData['issue_date'] = $validated['issued_at'];
        }
        if (isset($validated['course_id']) && $validated['course_id']) {
            $updateData['course_name'] = AdvancedCourse::find($validated['course_id'])->title ?? '';
        }
        if (isset($validated['status'])) {
            $updateData['is_verified'] = $validated['status'] === 'issued';
        }
        $certificate->update($updateData);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'تم تحديث الشهادة بنجاح');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return redirect()->route('admin.certificates.index')
            ->with('success', 'تم حذف الشهادة بنجاح');
    }
}
