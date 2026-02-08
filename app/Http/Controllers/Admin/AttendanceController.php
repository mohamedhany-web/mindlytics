<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Lecture;
use App\Models\TeamsAttendanceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendanceRecord::with(['lecture', 'student']);

        if ($request->filled('lecture_id')) {
            $query->where('lecture_id', $request->lecture_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20);
        $lectures = Lecture::with('course')->orderBy('scheduled_at', 'desc')->get();

        return view('admin.attendance.index', compact('records', 'lectures'));
    }

    public function uploadTeamsFile(Request $request, Lecture $lecture)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('attendance/teams', $fileName, 'public');

        $teamsFile = TeamsAttendanceFile::create([
            'lecture_id' => $lecture->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'status' => 'uploaded',
            'uploaded_by' => auth()->id(),
        ]);

        // TODO: معالجة الملف وتحديث سجلات الحضور

        return redirect()->back()
            ->with('success', 'تم رفع الملف بنجاح. جاري معالجته...');
    }

    public function showLectureAttendance(Lecture $lecture)
    {
        $lecture->load(['attendanceRecords.student', 'course']);
        $attendanceRecords = $lecture->attendanceRecords;

        return view('admin.attendance.lecture', compact('lecture', 'attendanceRecords'));
    }
}
