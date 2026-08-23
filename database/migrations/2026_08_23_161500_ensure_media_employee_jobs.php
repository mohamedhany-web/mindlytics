<?php

use App\Models\EmployeeJob;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        EmployeeJob::ensureMediaJobs();
    }

    public function down(): void
    {
        // لا نحذف الوظائف — قد تكون مربوطة بموظفين فعليين.
    }
};
