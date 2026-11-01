<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Accounting\Models\Order;
use App\Domains\Learning\Models\StudentCourseEnrollment;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Services\SalesLeadOrderLinker;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * وحدة الطلاب (Contacts في SuiteCRM): الطالب كسجل CRM — كورساته، طلباته، العملاء المرتبطين بيه، وأنشطته.
 * الموظف والمدير يشوفوا الطلاب اللي جم من عملاء نطاقهم؛ الأدمن يشوف كل الطلاب. قراءة فقط هنا؛ التعديل في شاشات الإدارة.
 */
class SalesCrmStudentController extends Controller
{
    use InteractsWithCrm;

    public function index(Request $request): View
    {
        $scope = $this->scope($request);
        $q = trim((string) $request->query('q', ''));

        $query = $scope->students();
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('phone', 'like', $like)->orWhere('email', 'like', $like));
        }

        $students = $query->withCount(['courseEnrollments as courses_count'])
            ->latest('id')->paginate(25, ['id', 'name', 'phone', 'email', 'created_at', 'is_active'])->withQueryString();

        $ids = $students->pluck('id')->all() ?: [0];
        $paid = Order::query()->whereIn('user_id', $ids)->where('status', Order::STATUS_APPROVED)
            ->selectRaw('user_id, SUM(amount) as total')->groupBy('user_id')->pluck('total', 'user_id');
        $owners = SalesLead::query()->with('assignee:id,name')->whereIn('student_user_id', $ids)->latest('id')->get(['id', 'student_user_id', 'assigned_to'])
            ->groupBy('student_user_id')->map(fn ($leads) => $leads->first()->assignee?->name);

        return $this->view($scope, 'students', 'students.index', [
            'students' => $students,
            'paid' => $paid,
            'owners' => $owners,
            'q' => $q,
            'total' => $scope->students()->count(),
        ]);
    }

    public function show(Request $request, int $student): View
    {
        $scope = $this->scope($request);
        $student = User::query()->where('role', 'student')->find($student);
        abort_unless($student && $scope->canSeeStudent($student), 404);

        $orders = Order::query()->with(['course:id,title', 'payment'])->where('user_id', $student->id)->latest('id')->limit(30)->get();
        $leadQuery = $scope->isAdmin() ? SalesLead::query() : $scope->leads();
        $leads = $leadQuery->with('assignee:id,name')
            ->where(function ($q) use ($student, $orders) {
                $q->where('student_user_id', $student->id);
                if (SalesLeadOrderLinker::ready() && $orders->isNotEmpty()) {
                    $q->orWhereIn('order_id', $orders->pluck('id'));
                }
            })->latest('id')->get();

        $leadIds = $leads->pluck('id')->all() ?: [0];

        return $this->view($scope, 'students', 'students.show', [
            'student' => $student,
            'enrollments' => StudentCourseEnrollment::query()->with('course:id,title')->where('user_id', $student->id)->latest('id')->get(),
            'orders' => $orders,
            'paidTotal' => (float) $orders->where('status', Order::STATUS_APPROVED)->sum('amount'),
            'leads' => $leads,
            'planned' => $scope->activities()->with(['assignee:id,name', 'lead:id,name'])
                ->where(fn ($q) => $q->where('student_user_id', $student->id)->orWhereIn('sales_lead_id', $leadIds))
                ->orderByRaw("status = 'planned' desc")->orderBy('starts_at')->limit(20)->get(),
            'history' => SalesActivity::query()->with(['user:id,name', 'lead:id,name'])->whereIn('sales_lead_id', $leadIds)->latest('id')->limit(20)->get(),
        ]);
    }
}
