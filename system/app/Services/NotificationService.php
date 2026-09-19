<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\SpmbRegistration;
use App\Models\StudentPaymentBill;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Get aggregated system notifications.
     */
    public static function getNotifications(?string $category = null, ?int $limit = null): Collection
    {
        $notifications = collect();

        $user = auth()->user();
        $isConsole = app()->runningInConsole();

        // 1. SPMB Notifications (Pendaftaran SPMB Baru & Menunggu Verifikasi)
        if ((!$category || $category === 'all' || $category === 'spmb') && ($isConsole || !$user || $user->hasPermission('view-spmb'))) {
            try {
                $spmbItems = SpmbRegistration::with('wave')
                    ->whereIn('status', ['submitted', 'verified'])
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(function ($item) {
                        $isSubmitted = $item->status === 'submitted';
                        return [
                            'id' => 'spmb-' . $item->id,
                            'category' => 'spmb',
                            'badge' => 'SPMB',
                            'badge_class' => $isSubmitted ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'icon_bg' => $isSubmitted ? 'bg-blue-100 text-blue-600' : 'bg-indigo-100 text-indigo-600',
                            'title' => $isSubmitted ? 'Pendaftaran SPMB Baru' : 'SPMB Terverifikasi',
                            'message' => 'Pendaftar ' . $item->full_name . ' (No: ' . $item->registration_number . ') ' . ($isSubmitted ? 'menunggu verifikasi berkas.' : 'telah diverifikasi.'),
                            'url' => route('admin.spmb.show', $item->id),
                            'created_at' => $item->created_at,
                            'is_urgent' => $isSubmitted,
                        ];
                    });
                $notifications = $notifications->concat($spmbItems);
            } catch (\Exception $e) {
                // Ignore if table/query fails
            }
        }

        // 2. Payment Notifications (Pembayaran Belum Lunas / Telat)
        if ((!$category || $category === 'all' || $category === 'payment') && ($isConsole || !$user || $user->hasPermission('view-financial'))) {
            try {
                $paymentItems = StudentPaymentBill::with(['student', 'paymentBill'])
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(function ($item) {
                        $isUnpaid = $item->status === 'unpaid';
                        $studentName = $item->student->name ?? 'Siswa';
                        $billName = $item->paymentBill->name ?? 'Tagihan Sekolah';
                        $remaining = $item->total_amount - $item->paid_amount;

                        return [
                            'id' => 'pay-' . $item->id,
                            'category' => 'payment',
                            'badge' => 'Pembayaran',
                            'badge_class' => $isUnpaid ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200',
                            'icon_bg' => $isUnpaid ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600',
                            'title' => $isUnpaid ? 'Tagihan Belum Lunas' : 'Pembayaran Sebagian (Telat)',
                            'message' => 'Tagihan ' . $billName . ' a.n. ' . $studentName . ' sisa Rp ' . number_format($remaining, 0, ',', '.'),
                            'url' => route('admin.student-payments.index'),
                            'created_at' => $item->created_at,
                            'is_urgent' => true,
                        ];
                    });
                $notifications = $notifications->concat($paymentItems);

                // Fetch pending manual billing transfers requiring confirmation
                $manualPayments = \App\Models\PaymentTransaction::with('user.student')
                    ->where('reference_type', 'spp')
                    ->where('payment_gateway', 'manual')
                    ->where('status', 'pending')
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(function ($item) {
                        $studentName = $item->user->student->name ?? 'Siswa';
                        return [
                            'id' => 'manual-pay-' . $item->id,
                            'category' => 'payment',
                            'badge' => 'Manual Transfer',
                            'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'icon_bg' => 'bg-amber-100 text-amber-600',
                            'title' => 'Verifikasi Transfer Manual SPP',
                            'message' => 'Pembayaran tagihan a.n. ' . $studentName . ' (Nominal: Rp ' . number_format($item->amount, 0, ',', '.') . ') memerlukan konfirmasi bukti transfer.',
                            'url' => route('admin.student-payments.manual-confirm'),
                            'created_at' => $item->created_at,
                            'is_urgent' => true,
                        ];
                    });
                $notifications = $notifications->concat($manualPayments);

                // Fetch pending manual savings deposits requiring confirmation
                $manualSavings = \App\Models\PaymentTransaction::with('user.student')
                    ->where('reference_type', 'savings_deposit')
                    ->where('payment_gateway', 'manual')
                    ->where('status', 'pending')
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(function ($item) {
                        $studentName = $item->user->student->name ?? 'Siswa';
                        return [
                            'id' => 'manual-savings-' . $item->id,
                            'category' => 'payment',
                            'badge' => 'Setoran Manual',
                            'badge_class' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'icon_bg' => 'bg-indigo-100 text-indigo-600',
                            'title' => 'Verifikasi Setoran Manual Tabungan',
                            'message' => 'Setoran tabungan a.n. ' . $studentName . ' (Nominal: Rp ' . number_format($item->amount, 0, ',', '.') . ') memerlukan konfirmasi bukti transfer.',
                            'url' => route('admin.savings.manual-confirm'),
                            'created_at' => $item->created_at,
                            'is_urgent' => true,
                        ];
                    });
                $notifications = $notifications->concat($manualSavings);
            } catch (\Exception $e) {
                // Ignore if table/query fails
            }
        }

        // 3. Assignment / Academic Notifications (Tugas Mendatang)
        if ((!$category || $category === 'all' || $category === 'assignment') && ($isConsole || !$user || $user->hasPermission('view-learning'))) {
            try {
                $assignmentItems = Assignment::with('class')
                    ->where('status', 'published')
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(function ($item) {
                        $isOverdue = $item->due_date && $item->due_date->isPast();
                        return [
                            'id' => 'assign-' . $item->id,
                            'category' => 'assignment',
                            'badge' => 'Tugas',
                            'badge_class' => $isOverdue ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-purple-50 text-purple-700 border-purple-200',
                            'icon_bg' => $isOverdue ? 'bg-rose-100 text-rose-600' : 'bg-purple-100 text-purple-600',
                            'title' => $isOverdue ? 'Tugas Melewati Tenggat' : 'Tugas Aktif Dipublikasi',
                            'message' => $item->title . ' (Kelas ' . ($item->class->name ?? '-') . ') - Tenggat: ' . ($item->due_date ? $item->due_date->format('d M Y H:i') : '-'),
                            'url' => route('admin.assignments.index'),
                            'created_at' => $item->created_at,
                            'is_urgent' => false,
                        ];
                    });
                $notifications = $notifications->concat($assignmentItems);
            } catch (\Exception $e) {
                // Ignore if table/query fails
            }
        }

        // 4. Announcement Notifications (Pengumuman)
        if ((!$category || $category === 'all' || $category === 'announcement') && ($isConsole || !$user || $user->hasPermission('view-announcements'))) {
            try {
                $announcementItems = Announcement::latest()
                    ->take(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => 'ann-' . $item->id,
                            'category' => 'announcement',
                            'badge' => 'Pengumuman',
                            'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'icon_bg' => 'bg-emerald-100 text-emerald-600',
                            'title' => 'Pengumuman: ' . $item->title,
                            'message' => \Illuminate\Support\Str::limit(strip_tags($item->content), 80),
                            'url' => route('admin.announcements.index'),
                            'created_at' => $item->created_at,
                            'is_urgent' => false,
                        ];
                    });
                $notifications = $notifications->concat($announcementItems);
            } catch (\Exception $e) {
                // Ignore if table/query fails
            }
        }

        // 5. Canteen Withdrawal Notifications (Pencairan Saldo Kantin)
        if ((!$category || $category === 'all' || $category === 'canteen') && ($isConsole || !$user || $user->hasPermission('view-canteen-admin'))) {
            try {
                $canteenItems = \App\Models\CanteenWithdrawal::with('stall')
                    ->where('status', 'pending')
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(function ($item) {
                        $stallName = $item->stall->name ?? 'Kantin';
                        return [
                            'id' => 'canteen-wd-' . $item->id,
                            'category' => 'canteen',
                            'badge' => 'E-Kantin',
                            'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'icon_bg' => 'bg-rose-100 text-rose-600',
                            'title' => 'Permintaan Pencairan Saldo',
                            'message' => 'Stand ' . $stallName . ' mengajukan penarikan Rp ' . number_format($item->amount, 0, ',', '.') . '.',
                            'url' => route('admin.canteen.withdrawals'),
                            'created_at' => $item->created_at,
                            'is_urgent' => true,
                        ];
                    });
                $notifications = $notifications->concat($canteenItems);
            } catch (\Exception $e) {
                // Ignore if table/query fails
            }
        }

        // 6. CBT Exam Essay Grading Notifications (Ujian CBT Menunggu Koreksi Essay)
        if ((!$category || $category === 'all' || $category === 'cbt') && ($isConsole || !$user || $user->hasPermission('view-cbt') || $user->isTeacher() || $user->hasRole('guru'))) {
            try {
                $cbtItems = \App\Models\ExamResult::with(['exam', 'student.user'])
                    ->where('status', 'needs_grading')
                    ->when($user && !$user->hasRole('admin') && !$user->hasRole('super-admin'), function($q) use ($user) {
                        return $q->whereHas('exam', function($eq) use ($user) {
                            $eq->where('teacher_id', $user->id);
                        });
                    })
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(function ($item) {
                        $studentName = $item->student->user->name ?? $item->student->name ?? 'Siswa';
                        $examTitle = $item->exam->title ?? 'Ujian CBT';
                        return [
                            'id' => 'cbt-grade-' . $item->id,
                            'category' => 'cbt',
                            'badge' => 'CBT Essay',
                            'badge_class' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'icon_bg' => 'bg-amber-100 text-amber-600',
                            'title' => 'Jawaban Essay Menunggu Koreksi',
                            'message' => 'Jawaban ' . $studentName . ' pada "' . $examTitle . '" membutuhkan koreksi manual.',
                            'url' => route('admin.exams.grade-student', [$item->exam_id, $item->id]),
                            'created_at' => $item->updated_at ?? $item->created_at,
                            'is_urgent' => true,
                        ];
                    });
                $notifications = $notifications->concat($cbtItems);
            } catch (\Exception $e) {
                // Ignore if table/query fails
            }
        }

        // 7. Employee Leave & Permit Notifications (Izin Pegawai Menunggu Verifikasi Kepala Sekolah & Super Admin)
        if ((!$category || $category === 'all' || $category === 'permit') && ($isConsole || !$user || $user->hasRole('kepala-sekolah') || $user->hasRole('super-admin') || $user->hasRole('admin'))) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('employee_permits')) {
                    $permitItems = \App\Models\EmployeePermit::with('user')
                        ->where('status', 'pending')
                        ->latest()
                        ->take(15)
                        ->get()
                        ->map(function ($item) {
                            $userName = $item->user->name ?? 'Pegawai';
                            return [
                                'id' => 'permit-' . $item->id,
                                'category' => 'permit',
                                'badge' => 'Izin Pegawai',
                                'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'icon_bg' => 'bg-rose-100 text-rose-600',
                                'title' => 'Pengajuan Izin: ' . $userName,
                                'message' => $item->type_label . ' (' . $item->duration_days . ' hari) menunggu verifikasi Kepala Sekolah.',
                                'url' => route('admin.employee-permits.index'),
                                'created_at' => $item->created_at,
                                'is_urgent' => true,
                            ];
                        });
                    $notifications = $notifications->concat($permitItems);
                }
            } catch (\Exception $e) {
                // Ignore if table/query fails
            }
        }

        // Sort all by created_at descending
        $sorted = $notifications->sortByDesc('created_at')->values();

        if ($limit) {
            return $sorted->take($limit);
        }

        return $sorted;
    }

    /**
     * Get counts breakdown per category.
     */
    public static function getCounts(): array
    {
        $all = self::getNotifications('all');
        return [
            'all' => $all->count(),
            'spmb' => $all->where('category', 'spmb')->count(),
            'payment' => $all->where('category', 'payment')->count(),
            'assignment' => $all->where('category', 'assignment')->count(),
            'announcement' => $all->where('category', 'announcement')->count(),
            'canteen' => $all->where('category', 'canteen')->count(),
            'cbt' => $all->where('category', 'cbt')->count(),
            'permit' => $all->where('category', 'permit')->count(),
            'urgent' => $all->where('is_urgent', true)->count(),
        ];
    }
}
