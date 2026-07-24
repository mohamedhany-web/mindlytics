<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Certificate;
use App\Models\CertificateBranding;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status == 'issued') {
                $query->where(function ($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                });
            } elseif ($request->status == 'pending') {
                $query->where(function ($q) {
                    $q->where('status', 'pending')->orWhere('is_verified', false);
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $certificates = $query->paginate(20);
        $branding = CertificateBranding::current();
        $templates = Certificate::availableTemplates();

        $stats = [
            'total' => Certificate::count(),
            'issued' => Certificate::where(function ($q) {
                $q->where('status', 'issued')->orWhere('is_verified', true);
            })->count(),
            'pending' => Certificate::where(function ($q) {
                $q->where('status', 'pending')->orWhere('is_verified', false);
            })->count(),
        ];

        return view('admin.certificates.index', compact('certificates', 'stats', 'branding', 'templates'));
    }

    public function branding()
    {
        $branding = CertificateBranding::current();
        $templates = Certificate::availableTemplates();

        return view('admin.certificates.branding', compact('branding', 'templates'));
    }

    public function updateBranding(Request $request)
    {
        $branding = CertificateBranding::current();

        $validated = $request->validate([
            'academy_name' => 'required|string|max:120',
            'academy_tagline' => 'nullable|string|max:180',
            'signature_name' => 'nullable|string|max:120',
            'signature_title' => 'nullable|string|max:120',
            'seal_label' => 'nullable|string|max:80',
            'seal_since' => 'nullable|string|max:20',
            'default_template' => 'required|in:' . implode(',', array_keys(Certificate::availableTemplates())),
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:4096',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:4096',
            'stamp' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:4096',
            'remove_logo' => 'nullable|boolean',
            'remove_signature' => 'nullable|boolean',
            'remove_stamp' => 'nullable|boolean',
        ]);

        $branding->fill([
            'academy_name' => $validated['academy_name'],
            'academy_tagline' => $validated['academy_tagline'] ?? null,
            'signature_name' => $validated['signature_name'] ?? null,
            'signature_title' => $validated['signature_title'] ?? null,
            'seal_label' => $validated['seal_label'] ?? 'CERTIFICATION',
            'seal_since' => $validated['seal_since'] ?? null,
            'default_template' => $validated['default_template'],
        ]);

        if ($request->boolean('remove_logo')) {
            $branding->deleteAsset('logo_path');
        }
        if ($request->boolean('remove_signature')) {
            $branding->deleteAsset('signature_path');
        }
        if ($request->boolean('remove_stamp')) {
            $branding->deleteAsset('stamp_path');
        }

        foreach (['logo' => 'logo_path', 'signature' => 'signature_path', 'stamp' => 'stamp_path'] as $input => $field) {
            if ($request->hasFile($input)) {
                if ($branding->{$field} && Storage::disk('public')->exists($branding->{$field})) {
                    Storage::disk('public')->delete($branding->{$field});
                }
                $branding->{$field} = $request->file($input)->store('certificates/branding', 'public');
            }
        }

        $branding->save();

        return redirect()->route('admin.certificates.branding')
            ->with('success', 'تم حفظ هوية الشهادات بنجاح');
    }

    public function create()
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        $courses = AdvancedCourse::where('is_active', true)->get();
        $branding = CertificateBranding::current();
        $templates = Certificate::availableTemplates();

        return view('admin.certificates.create', compact('users', 'courses', 'branding', 'templates'));
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
            'template' => 'nullable|in:' . implode(',', array_keys(Certificate::availableTemplates())),
        ]);

        $branding = CertificateBranding::current();
        $template = $validated['template'] ?? $branding->default_template ?? 'achievement';

        $certificate = Certificate::create([
            'certificate_number' => 'CERT-' . str_pad((int) Certificate::max('id') + 1, 8, '0', STR_PAD_LEFT),
            'serial_number' => Certificate::generateSerialNumber(),
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'] ?? null,
            'course_name' => $validated['course_id'] ? (AdvancedCourse::find($validated['course_id'])->title ?? '') : ($validated['title'] ?? ''),
            'certificate_type' => 'completion',
            'title' => $validated['title'] ?? '',
            'description' => $validated['description'] ?? null,
            'issue_date' => $validated['issued_at'] ?? now(),
            'issued_at' => $validated['issued_at'] ?? now(),
            'verification_code' => strtoupper(uniqid('CERT')),
            'status' => $validated['status'] ?? 'pending',
            'is_verified' => ($validated['status'] ?? '') === 'issued',
            'template' => $template,
            'academy_signature' => $branding->signature_path,
            'academy_signature_name' => $branding->signature_name ?: 'المدير العام',
            'academy_signature_title' => $branding->signature_title ?: 'Mindlytics Academy',
            'logo_path' => $branding->logo_path,
            'stamp_path' => $branding->stamp_path,
        ]);

        if (($validated['status'] ?? '') === 'issued') {
            $certificate->certificate_hash = $certificate->generateHash();
            $certificate->verification_url = $certificate->verification_url;
            $certificate->certified_at = now();
            if (! $certificate->serial_number) {
                $certificate->serial_number = Certificate::generateSerialNumber();
            }
            $certificate->save();
        }

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'تم إنشاء الشهادة بنجاح — السيريال: ' . $certificate->serial_number);
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'course']);
        if (! $certificate->serial_number) {
            $certificate->serial_number = Certificate::generateSerialNumber();
            $certificate->save();
        }
        $branding = CertificateBranding::current();
        $templates = Certificate::availableTemplates();

        return view('admin.certificates.show', compact('certificate', 'branding', 'templates'));
    }

    public function edit(Certificate $certificate)
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        $courses = AdvancedCourse::where('is_active', true)->get();
        $branding = CertificateBranding::current();
        $templates = Certificate::availableTemplates();

        return view('admin.certificates.edit', compact('certificate', 'users', 'courses', 'branding', 'templates'));
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
            'template' => 'nullable|in:' . implode(',', array_keys(Certificate::availableTemplates())),
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
            if ($validated['status'] === 'issued' && ! $certificate->certified_at) {
                $updateData['certified_at'] = now();
            }
        }
        if (! $certificate->serial_number) {
            $updateData['serial_number'] = Certificate::generateSerialNumber();
        }

        $certificate->update($updateData);

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'تم تحديث الشهادة بنجاح');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'تم حذف الشهادة بنجاح');
    }
}
