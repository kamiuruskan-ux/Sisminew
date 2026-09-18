<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Major;
use App\Models\PaymentBill;
use App\Models\PaymentPost;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentPaymentBill;
use App\Models\StudentPaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentBillController extends Controller
{
    public function index(Request $request)
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->get('academic_year_id', $activeAcademicYear ? $activeAcademicYear->id : null);

        $academicYears = AcademicYear::latest()->get();
        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();
        $majors = Major::where('is_active', true)->orderBy('name')->get();
        $students = Student::with(['user', 'schoolClass'])->get()->sortBy(fn($s) => $s->user->name ?? '')->values();
        $paymentPosts = PaymentPost::all();
        $isVocational = Setting::get('is_vocational', '1') == '1';

        $query = PaymentBill::with(['paymentPost', 'academicYear', 'schoolClass', 'major', 'student']);
        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        $paymentBills = $query->latest()->get();

        return view('admin.payment-bills.index', compact(
            'paymentBills',
            'academicYears',
            'classes',
            'majors',
            'students',
            'paymentPosts',
            'selectedYearId',
            'isVocational'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_post_id' => 'required|exists:payment_posts,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'target_type' => 'required|in:all,class,major,student',
            'class_id' => 'nullable|required_if:target_type,class|exists:classes,id',
            'major_id' => 'nullable|required_if:target_type,major|exists:majors,id',
            'student_id' => 'nullable|required_if:target_type,student|exists:students,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:bulanan,bebas',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'auto_generate' => 'nullable|boolean',
        ]);

        if ($validated['target_type'] === 'all') {
            $validated['class_id'] = null;
            $validated['major_id'] = null;
            $validated['student_id'] = null;
        } elseif ($validated['target_type'] === 'class') {
            $validated['major_id'] = null;
            $validated['student_id'] = null;
        } elseif ($validated['target_type'] === 'major') {
            $validated['class_id'] = null;
            $validated['student_id'] = null;
        } elseif ($validated['target_type'] === 'student') {
            $validated['class_id'] = null;
            $validated['major_id'] = null;
        }

        DB::beginTransaction();
        try {
            $bill = PaymentBill::create($validated);

            if ($request->has('auto_generate') && $request->auto_generate) {
                $this->generateBillsForStudents($bill);
            }

            DB::commit();
            return back()->with('success', 'Tarif Pembayaran berhasil dibuat ' . ($request->auto_generate ? 'dan tagihan siswa telah dibuat.' : '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat tarif pembayaran: ' . $e->getMessage());
        }
    }

    public function show(PaymentBill $paymentBill)
    {
        $paymentBill->load([
            'paymentPost',
            'academicYear',
            'schoolClass',
            'major',
            'student',
            'studentPaymentBills.student.schoolClass',
            'studentPaymentBills.student.major',
            'studentPaymentBills.details'
        ]);

        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();
        $majors = Major::where('is_active', true)->orderBy('name')->get();
        $students = Student::with(['user', 'schoolClass'])->get()->sortBy(fn($s) => $s->user->name ?? '')->values();
        $paymentPosts = PaymentPost::all();
        $academicYears = AcademicYear::latest()->get();
        $isVocational = Setting::get('is_vocational', '1') == '1';

        return view('admin.payment-bills.show', compact(
            'paymentBill',
            'classes',
            'majors',
            'students',
            'paymentPosts',
            'academicYears',
            'isVocational'
        ));
    }

    public function update(Request $request, PaymentBill $paymentBill)
    {
        $validated = $request->validate([
            'payment_post_id' => 'required|exists:payment_posts,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'target_type' => 'required|in:all,class,major,student',
            'class_id' => 'nullable|required_if:target_type,class|exists:classes,id',
            'major_id' => 'nullable|required_if:target_type,major|exists:majors,id',
            'student_id' => 'nullable|required_if:target_type,student|exists:students,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:bulanan,bebas',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'update_unpaid_bills' => 'nullable|boolean',
        ]);

        if ($validated['target_type'] === 'all') {
            $validated['class_id'] = null;
            $validated['major_id'] = null;
            $validated['student_id'] = null;
        } elseif ($validated['target_type'] === 'class') {
            $validated['major_id'] = null;
            $validated['student_id'] = null;
        } elseif ($validated['target_type'] === 'major') {
            $validated['class_id'] = null;
            $validated['student_id'] = null;
        } elseif ($validated['target_type'] === 'student') {
            $validated['class_id'] = null;
            $validated['major_id'] = null;
        }

        DB::beginTransaction();
        try {
            $oldAmount = $paymentBill->amount;
            $oldType = $paymentBill->type;

            $paymentBill->update($validated);

            if ($request->has('update_unpaid_bills') && $request->update_unpaid_bills) {
                $newAmount = $paymentBill->amount;
                $studentBills = StudentPaymentBill::where('payment_bill_id', $paymentBill->id)
                    ->where('paid_amount', 0)
                    ->with('details')
                    ->get();

                foreach ($studentBills as $sb) {
                    if ($paymentBill->type === 'bulanan') {
                        $monthlyAmount = $newAmount;
                        if ($paymentBill->paymentPost && strtoupper($paymentBill->paymentPost->code) === 'SPP') {
                            $monthlyAmount = max(0, $newAmount - ($sb->student->spp_discount ?? 0));
                        }
                        $totalAmount = $monthlyAmount * 12;
                        $sb->update(['total_amount' => $totalAmount]);

                        foreach ($sb->details as $detail) {
                            if ($detail->status === 'unpaid') {
                                $detail->update(['amount' => $monthlyAmount]);
                            }
                        }
                    } else {
                        $sb->update(['total_amount' => $newAmount]);
                        foreach ($sb->details as $detail) {
                            if ($detail->status === 'unpaid') {
                                $detail->update(['amount' => $newAmount]);
                            }
                        }
                    }
                    $sb->recalculateStatus();
                }
            }

            DB::commit();
            return back()->with('success', 'Tarif pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui tarif pembayaran: ' . $e->getMessage());
        }
    }

    public function generate(Request $request, PaymentBill $paymentBill)
    {
        DB::beginTransaction();
        try {
            $targetType = $request->input('target_type', $paymentBill->target_type);
            $classId = $request->input('class_id');
            $majorId = $request->input('major_id');
            $studentId = $request->input('student_id');

            $count = $this->generateBillsForStudents($paymentBill, $targetType, $classId, $majorId, $studentId);
            DB::commit();
            return back()->with('success', "Berhasil membuat tagihan untuk {$count} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menggenerate tagihan: ' . $e->getMessage());
        }
    }

    public function updateStudentBill(Request $request, PaymentBill $paymentBill, StudentPaymentBill $studentPaymentBill)
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'monthly_amounts' => 'nullable|array',
            'monthly_amounts.*' => 'numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            if ($paymentBill->type === 'bulanan' && !empty($validated['monthly_amounts'])) {
                $newTotal = 0;
                foreach ($studentPaymentBill->details as $detail) {
                    if (isset($validated['monthly_amounts'][$detail->id])) {
                        $mAmount = (float) $validated['monthly_amounts'][$detail->id];
                        $detail->update(['amount' => $mAmount]);
                        $newTotal += $mAmount;
                    } else {
                        $newTotal += $detail->amount;
                    }
                }
                $studentPaymentBill->update(['total_amount' => $newTotal]);
            } else {
                $newTotal = (float) $validated['total_amount'];
                $studentPaymentBill->update(['total_amount' => $newTotal]);

                if ($paymentBill->type === 'bulanan') {
                    $monthlyAmount = round($newTotal / 12);
                    foreach ($studentPaymentBill->details as $detail) {
                        if ($detail->status === 'unpaid') {
                            $detail->update(['amount' => $monthlyAmount]);
                        }
                    }
                } else {
                    foreach ($studentPaymentBill->details as $detail) {
                        if ($detail->status === 'unpaid') {
                            $detail->update(['amount' => $newTotal]);
                        }
                    }
                }
            }

            $studentPaymentBill->recalculateStatus();

            DB::commit();
            return back()->with('success', "Tagihan siswa {$studentPaymentBill->student->name} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui tagihan siswa: ' . $e->getMessage());
        }
    }

    public function bulkUpdateStudentBills(Request $request, PaymentBill $paymentBill)
    {
        $validated = $request->validate([
            'student_bill_ids' => 'required|array|min:1',
            'student_bill_ids.*' => 'exists:student_payment_bills,id',
            'action_type' => 'required|in:set_amount,delete',
            'new_amount' => 'nullable|required_if:action_type,set_amount|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $studentBills = StudentPaymentBill::whereIn('id', $validated['student_bill_ids'])
                ->where('payment_bill_id', $paymentBill->id)
                ->with('details')
                ->get();

            $updatedCount = 0;
            $deletedCount = 0;

            if ($validated['action_type'] === 'set_amount') {
                $newAmount = (float) $validated['new_amount'];

                foreach ($studentBills as $sb) {
                    $sb->update(['total_amount' => $newAmount]);

                    if ($paymentBill->type === 'bulanan') {
                        $monthlyAmount = round($newAmount / 12);
                        foreach ($sb->details as $detail) {
                            if ($detail->status === 'unpaid') {
                                $detail->update(['amount' => $monthlyAmount]);
                            }
                        }
                    } else {
                        foreach ($sb->details as $detail) {
                            if ($detail->status === 'unpaid') {
                                $detail->update(['amount' => $newAmount]);
                            }
                        }
                    }
                    $sb->recalculateStatus();
                    $updatedCount++;
                }
                DB::commit();
                return back()->with('success', "Berhasil memperbarui total nominal {$updatedCount} tagihan siswa menjadi Rp " . number_format($newAmount, 0, ',', '.'));
            } elseif ($validated['action_type'] === 'delete') {
                foreach ($studentBills as $sb) {
                    if ($sb->paid_amount == 0) {
                        $sb->delete();
                        $deletedCount++;
                    }
                }
                DB::commit();
                return back()->with('success', "Berhasil menghapus {$deletedCount} tagihan siswa.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses update massal tagihan: ' . $e->getMessage());
        }
    }

    public function destroyStudentBill(PaymentBill $paymentBill, StudentPaymentBill $studentPaymentBill)
    {
        if ($studentPaymentBill->paid_amount > 0) {
            return back()->with('error', 'Tagihan yang sudah memiliki riwayat pembayaran tidak dapat dihapus.');
        }

        $studentPaymentBill->delete();
        return back()->with('success', 'Tagihan siswa berhasil dihapus.');
    }

    public function destroy(PaymentBill $paymentBill)
    {
        $paymentBill->delete();
        return redirect()->route('admin.payment-bills.index')->with('success', 'Tarif pembayaran berhasil dihapus.');
    }

    private function generateBillsForStudents(
        PaymentBill $bill,
        $targetType = null,
        $targetClassId = null,
        $targetMajorId = null,
        $targetStudentId = null
    ): int {
        $query = Student::query();

        $effectiveTarget = $targetType ?: $bill->target_type;

        if ($targetStudentId || ($effectiveTarget === 'student' && $bill->student_id)) {
            $sId = $targetStudentId ?: $bill->student_id;
            $query->where('id', $sId);
        } elseif ($targetMajorId || ($effectiveTarget === 'major' && $bill->major_id)) {
            $mId = $targetMajorId ?: $bill->major_id;
            $query->where(function ($q) use ($mId) {
                $q->where('major_id', $mId)
                  ->orWhereHas('schoolClass', fn($cq) => $cq->where('major_id', $mId));
            });
        } elseif ($targetClassId || ($effectiveTarget === 'class' && $bill->class_id)) {
            $cId = $targetClassId ?: $bill->class_id;
            $query->where('class_id', $cId);
        }

        $students = $query->get();
        $generatedCount = 0;

        $months = [
            1 => 'Juli',
            2 => 'Agustus',
            3 => 'September',
            4 => 'Oktober',
            5 => 'November',
            6 => 'Desember',
            7 => 'Januari',
            8 => 'Februari',
            9 => 'Maret',
            10 => 'April',
            11 => 'Mei',
            12 => 'Juni',
        ];

        $bill->loadMissing('paymentPost');

        foreach ($students as $student) {
            $existing = StudentPaymentBill::where('student_id', $student->id)
                ->where('payment_bill_id', $bill->id)
                ->first();

            if ($existing) {
                continue;
            }

            $monthlyAmount = $bill->amount;
            if ($bill->type === 'bulanan' && $bill->paymentPost && strtoupper($bill->paymentPost->code) === 'SPP') {
                $monthlyAmount = max(0, $bill->amount - ($student->spp_discount ?? 0));
            }

            $totalAmount = ($bill->type === 'bulanan') ? ($monthlyAmount * 12) : $bill->amount;

            $studentBill = StudentPaymentBill::create([
                'student_id' => $student->id,
                'payment_bill_id' => $bill->id,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'unpaid',
            ]);

            if ($bill->type === 'bulanan') {
                foreach ($months as $monthNo => $monthName) {
                    StudentPaymentDetail::create([
                        'student_payment_bill_id' => $studentBill->id,
                        'month_no' => $monthNo,
                        'month_name' => $monthName,
                        'amount' => $monthlyAmount,
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                    ]);
                }
            } else {
                StudentPaymentDetail::create([
                    'student_payment_bill_id' => $studentBill->id,
                    'month_no' => null,
                    'month_name' => 'Tagihan Cicilan/Bebas',
                    'amount' => $bill->amount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                ]);
            }

            $generatedCount++;
        }

        return $generatedCount;
    }
}
