<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('expenses')) {
            // إضافة الأعمدة المفقودة إذا كان الجدول موجوداً
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'expense_number')) {
                    $table->string('expense_number')->unique()->after('id');
                }
                if (!Schema::hasColumn('expenses', 'title')) {
                    $table->string('title')->after('expense_number');
                }
                if (!Schema::hasColumn('expenses', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (!Schema::hasColumn('expenses', 'category')) {
                    $table->enum('category', ['operational', 'marketing', 'salaries', 'utilities', 'equipment', 'maintenance', 'other'])->default('other')->after('description');
                }
                if (!Schema::hasColumn('expenses', 'amount')) {
                    $table->decimal('amount', 10, 2)->after('category');
                }
                if (!Schema::hasColumn('expenses', 'currency')) {
                    $table->string('currency', 3)->default('EGP')->after('amount');
                }
                if (!Schema::hasColumn('expenses', 'expense_date')) {
                    $table->date('expense_date')->after('currency');
                }
                if (!Schema::hasColumn('expenses', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'wallet', 'other'])->default('cash')->after('expense_date');
                }
                if (!Schema::hasColumn('expenses', 'wallet_id')) {
                    $table->unsignedBigInteger('wallet_id')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('expenses', 'reference_number')) {
                    $table->string('reference_number')->nullable()->after('wallet_id');
                }
                if (!Schema::hasColumn('expenses', 'attachment')) {
                    $table->string('attachment')->nullable()->after('reference_number');
                }
                if (!Schema::hasColumn('expenses', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('attachment');
                }
                if (!Schema::hasColumn('expenses', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                }
                if (!Schema::hasColumn('expenses', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('expenses', 'notes')) {
                    $table->text('notes')->nullable()->after('approved_at');
                }
                if (!Schema::hasColumn('expenses', 'metadata')) {
                    $table->json('metadata')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('expenses', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('metadata');
                }
            });
            return;
        }

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->string('title'); // عنوان المصروف
            $table->text('description')->nullable(); // وصف المصروف
            $table->enum('category', ['operational', 'marketing', 'salaries', 'utilities', 'equipment', 'maintenance', 'other'])->default('other');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EGP');
            $table->date('expense_date'); // تاريخ المصروف
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'wallet', 'other'])->default('cash');
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->string('reference_number')->nullable(); // رقم المرجع (رقم فاتورة، رقم شيك، إلخ)
            $table->string('attachment')->nullable(); // مرفق (صورة فاتورة، إلخ)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // بيانات إضافية
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index(['category', 'status']);
            $table->index('expense_date');
            $table->index('expense_number');
            $table->index('wallet_id');
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
