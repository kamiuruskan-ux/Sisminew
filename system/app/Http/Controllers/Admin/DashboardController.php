<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\BankAccount;
use App\Models\BkCounseling;
use App\Models\BkStudentViolation;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\FinancialTransaction;
use App\Models\Gallery;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Major;
use App\Models\PaymentBill;
use App\Models\Post;
use App\Models\Schedule;
use App\Models\Slider;
use App\Models\SpmbRegistration;
use App\Models\Student;
use App\Models\StudentPaymentBill;
use App\Models\StudentPaymentDetail;
use App\Models\StudentPermit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $isTeacher = $user->isTeacher() || $user->hasRole('guru');
        $isBendahara = $user->hasRole('bendahara') || $user->hasRole('bendahara-sekolah') || $user->hasPermission('view-payments') || $user->hasPermission('view-financial');
        $isBk = $user->hasRole('bk') || $user->hasRole('guru-bk') || $user->hasRole('konselor') || $user->hasPermission('view-bk') || $user->hasPermission('view-counseling');
        $isOperator = $user->hasRole('operator');
        $isStaff = $user->hasRole('staff');

        // Determine default tab view
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $defaultTab = 'admin';
        } elseif ($isBendahara) {
            $defaultTab = 'bendahara';
        } elseif ($isBk) {
            $defaultTab = 'bk';
        } elseif ($isTeacher) {
            $defaultTab = 'guru';
        } elseif ($isOperator) {
            $defaultTab = 'operator';
        } elseif ($isStaff) {
            $defaultTab = 'staff';
        } else {
            $defaultTab = 'admin';
        }

        // ── 1. Financial Data for Bendahara ──────────────────────────────────
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $incomeTx = FinancialTransaction::where('type', 'pemasukan')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');
            
        $incomeSpp = StudentPaymentDetail::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('paid_amount');

        $totalIncomeMonth = $incomeTx + $incomeSpp;

        $totalExpenseMonth = FinancialTransaction::where('type', 'pengeluaran')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $totalUnpaidBills = StudentPaymentBill::whereIn('status', ['unpaid', 'partial'])
            ->selectRaw('SUM(total_amount - paid_amount) as remaining')
            ->value('remaining') ?? 0;

        $totalBankBalance = BankAccount::where('is_active', true)->sum('current_balance');

        $financialStats = [
            'total_income_month'  => $totalIncomeMonth,
            'total_expense_month' => $totalExpenseMonth,
            'total_unpaid_bills'  => $totalUnpaidBills,
            'total_bank_balance'  => $totalBankBalance,
            'spp_income_month'    => $incomeSpp,
            'tx_income_month'     => $incomeTx,
        ];

        $financialTransactions = FinancialTransaction::with(['financialCategory', 'bankAccount', 'creator'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(6)
            ->get();

        $recentStudentPayments = StudentPaymentDetail::with(['studentPaymentBill.student.user', 'studentPaymentBill.paymentBill'])
            ->latest()
            ->take(6)
            ->get();

        $financialAccounts = BankAccount::where('is_active', true)->get();

        $monthlyIncomeExpense = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $m = $date->month;
            $y = $date->year;
            $label = $date->translatedFormat('M Y');

            $incTx = FinancialTransaction::where('type', 'pemasukan')->whereMonth('transaction_date', $m)->whereYear('transaction_date', $y)->sum('amount');
            $incSpp = StudentPaymentDetail::whereMonth('created_at', $m)->whereYear('created_at', $y)->sum('paid_amount');
            $exp = FinancialTransaction::where('type', 'pengeluaran')->whereMonth('transaction_date', $m)->whereYear('transaction_date', $y)->sum('amount');

            $monthlyIncomeExpense[] = [
                'month'   => $label,
                'income'  => $incTx + $incSpp,
                'expense' => $exp,
            ];
        }

        $billsStatus = [
            'paid'    => StudentPaymentBill::where('status', 'paid')->count(),
            'partial' => StudentPaymentBill::where('status', 'partial')->count(),
            'unpaid'  => StudentPaymentBill::where('status', 'unpaid')->count(),
        ];

        $financialChartData = [
            'monthlyTrend' => $monthlyIncomeExpense,
            'billsStatus'  => $billsStatus,
        ];

        // ── 2. Teacher Data for Guru ──────────────────────────────────────────
        $hasExamTeacherId = \Illuminate\Support\Facades\Schema::hasColumn('exams', 'teacher_id');
        $hasExamCreatedBy = \Illuminate\Support\Facades\Schema::hasColumn('exams', 'created_by');

        $pendingGradingQuery = \App\Models\ExamResult::where('status', 'needs_grading')
            ->when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user, $hasExamTeacherId, $hasExamCreatedBy) {
                return $q->whereHas('exam', function($sq) use ($user, $hasExamTeacherId, $hasExamCreatedBy) {
                    if ($hasExamTeacherId) {
                        $sq->where('teacher_id', $user->id);
                    }
                    if ($hasExamCreatedBy) {
                        if ($hasExamTeacherId) {
                            $sq->orWhere('created_by', $user->id);
                        } else {
                            $sq->where('created_by', $user->id);
                        }
                    }
                });
            });

        $pendingGradingResults = $pendingGradingQuery->with(['exam', 'student.user', 'student.class'])->latest()->take(6)->get();
        $pendingGradingCount = $pendingGradingQuery->count();

        $teacherStats = [
            'my_schedules_count'   => Schedule::where('is_active', true)->when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user) {
                return $q->where('teacher', 'like', "%{$user->name}%");
            })->count(),
            'my_materials_count'   => Material::when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user) {
                return $q->where('teacher_id', $user->id);
            })->count(),
            'my_assignments_count' => Assignment::when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user) {
                return $q->where('teacher_id', $user->id);
            })->count(),
            'my_exams_count'       => Exam::when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user, $hasExamTeacherId, $hasExamCreatedBy) {
                return $q->where(function($sq) use ($user, $hasExamTeacherId, $hasExamCreatedBy) {
                    if ($hasExamTeacherId) {
                        $sq->where('teacher_id', $user->id);
                    }
                    if ($hasExamCreatedBy) {
                        if ($hasExamTeacherId) {
                            $sq->orWhere('created_by', $user->id);
                        } else {
                            $sq->where('created_by', $user->id);
                        }
                    }
                });
            })->count(),
            'pending_grading_count' => $pendingGradingCount,
        ];

        $teacherSchedules = Schedule::where('is_active', true)
            ->when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user) {
                return $q->where('teacher', 'like', "%{$user->name}%");
            })
            ->take(6)
            ->get();

        $teacherAssignments = Assignment::with(['class', 'teacher'])
            ->when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user) {
                return $q->where('teacher_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        $teacherMaterials = Material::with(['class', 'teacher'])
            ->when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user) {
                return $q->where('teacher_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        $teacherExams = Exam::with(['class'])
            ->when(!$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user, $hasExamTeacherId, $hasExamCreatedBy) {
                return $q->where(function($sq) use ($user, $hasExamTeacherId, $hasExamCreatedBy) {
                    if ($hasExamTeacherId) {
                        $sq->where('teacher_id', $user->id);
                    }
                    if ($hasExamCreatedBy) {
                        if ($hasExamTeacherId) {
                            $sq->orWhere('created_by', $user->id);
                        } else {
                            $sq->where('created_by', $user->id);
                        }
                    }
                });
            })
            ->latest()
            ->take(5)
            ->get();

        // ── 3. BK Data for Bimbingan Konseling ─────────────────────────────────
        $bkStats = [
            'total_counselings'       => BkCounseling::count(),
            'in_progress_counselings' => BkCounseling::whereIn('status', ['in_progress', 'scheduled'])->count(),
            'total_violations'        => BkStudentViolation::count(),
            'pending_permits'         => StudentPermit::where('status', 'pending')->count(),
        ];

        $recentCounselings = BkCounseling::with(['student.user', 'counselor'])
            ->latest()
            ->take(5)
            ->get();

        $recentViolations = BkStudentViolation::with(['student.user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $recentPermits = StudentPermit::with(['student.user', 'class'])
            ->latest()
            ->take(5)
            ->get();

        // ── 4. Operator TU Data ───────────────────────────────────────────────
        $operatorStats = [
            'total_students'  => Student::count(),
            'total_classes'   => ClassModel::count(),
            'total_spmb'      => SpmbRegistration::count(),
            'total_schedules' => Schedule::where('is_active', true)->count(),
        ];

        $recentClasses = ClassModel::withCount('students')->orderBy('name')->take(6)->get();

        // ── 5. Staff Data (Administrasi & Informasional) ──────────────────────
        $staffStats = [
            'total_posts'         => Post::count(),
            'total_announcements' => Announcement::count(),
            'total_gallery'       => Gallery::count(),
            'total_sliders'       => Slider::count(),
        ];

        $recentPosts = Post::latest()->take(5)->get();

        // ── 6. Main Stats (Admin) ──────────────────────────────────────────────
        $stats = [
            'total_students'      => Student::count(),
            'total_teachers'      => User::role('guru')->count(),
            'total_spmb'          => SpmbRegistration::count(),
            'pending_spmb'        => SpmbRegistration::where('status', 'submitted')->count(),
            'total_posts'         => Post::count(),
            'total_users'         => User::count(),
            'total_schedules'     => Schedule::where('is_active', true)->count(),
            'total_assignments'   => Assignment::count(),
            'pending_assignments' => Assignment::where('status', 'published')->where('due_date', '>', now())->count(),
            'total_materials'     => Material::where('is_published', true)->count(),
        ];

        $studentsPerClass = ClassModel::withCount('students')->orderBy('name')->get();
        $studentsPerMajor = Major::withCount('students')->orderBy('name')->get();
        $spmbPerWave = SpmbRegistration::with('wave')->selectRaw('wave_id, COUNT(*) as count')->groupBy('wave_id')->get();
        $spmbStatus = SpmbRegistration::selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
        $postsPerMonth = Post::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $recentSpmb = SpmbRegistration::with(['user', 'wave'])->latest()->take(5)->get();
        $announcements = Announcement::published()->latest()->take(5)->get();
        $recentAssignments = Assignment::with(['class', 'teacher'])->latest()->take(5)->get();

        $chartData = [
            'studentsPerClass' => $studentsPerClass,
            'studentsPerMajor' => $studentsPerMajor,
            'spmbPerWave'      => $spmbPerWave,
            'spmbStatus'       => $spmbStatus,
            'postsPerMonth'    => $postsPerMonth,
        ];

        $students = Student::with(['user', 'class'])->get();

        return view('admin.dashboard', compact(
            'defaultTab',
            'isBendahara',
            'isTeacher',
            'isBk',
            'isOperator',
            'isStaff',
            'stats',
            'recentSpmb',
            'announcements',
            'chartData',
            'recentAssignments',
            'students',
            'financialStats',
            'financialTransactions',
            'recentStudentPayments',
            'financialAccounts',
            'financialChartData',
            'teacherStats',
            'teacherSchedules',
            'teacherAssignments',
            'teacherMaterials',
            'teacherExams',
            'pendingGradingResults',
            'bkStats',
            'recentCounselings',
            'recentViolations',
            'recentPermits',
            'operatorStats',
            'recentClasses',
            'staffStats',
            'recentPosts'
        ));
    }
}


