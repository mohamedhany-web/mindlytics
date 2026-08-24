<?php

use App\Models\EmployeeJob;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        EmployeeJob::ensurePresetJob('business_developer');
    }

    public function down(): void
    {
        // لا نحذف الوظيفة — قد تكون مربوطة بموظفين فعليين.
    }
};
