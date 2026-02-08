<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        return view('student.settings.index');
    }
}