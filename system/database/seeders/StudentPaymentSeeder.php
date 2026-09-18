<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\PaymentBill;
use App\Models\PaymentPost;
use App\Models\Student;
use App\Models\StudentPaymentBill;
use App\Models\StudentPaymentDetail;
use Illuminate\Database\Seeder;

class StudentPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Payment Posts
        $posts = [
            ['code' => 'SPP', 'name' => 'Sumbangan Pembinaan Pendidikan', 'description' => 'Tagihan bulanan SPP Siswa'],
            ['code' => 'SRG', 'name' => 'Uang Seragam Sekolah', 'description' => 'Uang pembelian seragam sekolah lengkap'],
            ['code' => 'GDG', 'name' => 'Uang Pembangunan Gedung', 'description' => 'Uang gedung/pembangunan sarana sekolah'],
        ];

        $paymentPosts = [];
        foreach ($posts as $postData) {
            $paymentPosts[] = PaymentPost::firstOrCreate(['code' => $postData['code']], $postData);
        }

        // Get Active Academic Year
        $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        if (!$activeYear) {
            return;
        }

        // 2. Create Payment Bills (Tarif Pembayaran)
        // Let's create:
        // - SPP Bill: Bulanan, Rp. 250.000
        // - Seragam Bill: Bebas, Rp. 1.200.000
        // - Gedung Bill: Bebas, Rp. 3.000.000
        $bills = [
            [
                'payment_post_code' => 'SPP',
                'name' => 'SPP Bulanan - TA ' . $activeYear->name,
                'type' => 'bulanan',
                'amount' => 250000.00,
                'description' => 'Sumbangan Pembinaan Pendidikan Bulanan',
            ],
            [
                'payment_post_code' => 'SRG',
                'name' => 'Seragam Lengkap - TA ' . $activeYear->name,
                'type' => 'bebas',
                'amount' => 1200000.00,
                'description' => 'Biaya pengadaan kain, batik, jas almamater, olahraga, & topi dasi',
            ],
            [
                'payment_post_code' => 'GDG',
                'name' => 'Uang Gedung & Sarpras - TA ' . $activeYear->name,
                'type' => 'bebas',
                'amount' => 3000000.00,
                'description' => 'Dana pengembangan sarana prasarana sekolah',
            ],
        ];

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

        $students = Student::all();

        foreach ($bills as $billData) {
            $post = collect($paymentPosts)->firstWhere('code', $billData['payment_post_code']);
            if (!$post) continue;

            $bill = PaymentBill::firstOrCreate(
                [
                    'payment_post_id' => $post->id,
                    'academic_year_id' => $activeYear->id,
                    'name' => $billData['name'],
                ],
                [
                    'type' => $billData['type'],
                    'amount' => $billData['amount'],
                    'description' => $billData['description'],
                ]
            );

            // Assign this bill to all active students
            foreach ($students as $student) {
                // Check if already assigned
                $existing = StudentPaymentBill::where('student_id', $student->id)
                    ->where('payment_bill_id', $bill->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $monthlyAmount = $bill->amount;
                if ($bill->type === 'bulanan' && strtoupper($post->code) === 'SPP') {
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
            }
        }
    }
}
