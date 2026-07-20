<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Services\SalesCourseCommissionResolver;
use Illuminate\Http\Request;

class SalesCourseCatalogController extends Controller
{
    public function index(Request $request)
    {
        $type = (string) $request->query('type', 'advanced');
        if (! array_key_exists($type, SalesLead::COURSE_TYPES)) {
            return response()->json(['data' => []]);
        }

        return response()->json(['data' => SalesCourseCommissionResolver::listCourses($type)]);
    }
}
