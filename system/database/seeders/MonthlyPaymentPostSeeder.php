<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\PaymentBill;
use App\Models\PaymentPost;
use App\Models\Student;
use App\Models\StudentPaymentBill;
use App\Models\StudentPaymentDetail;
use Illuminate\Database\Seeder;

class MonthlyPaymentPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pos Pembayaran Bulanan selain SPP
        $posts = [
            [
                'code' => 'KTR',
                'name' => 'Uang Katering & Makan Siang',
                'description' => 'Iuran bulanan katering makan siang harian siswa di sekolah',
                'amount' => 150000.00,
            ],
            [
                'code' => 'JMP',
                'name' => 'Uang Antar Jemput / Bus Sekolah',
                'description' => 'Biaya layanan operasional bus & antar jemput bulanan',
                'amount' => 200000.00,
            ],
            [
                'code' => 'LAB',
                'name' => 'Iuran Lab & Komputer',
                'description' => 'Iuran operasional laboratorium komputer, internet, & sains',
                'amount' => 50000.00,
            ],
            [
                'code' => 'EKS',
                'name' => 'Iuran Ekstrakurikuler & Olahraga',
                'description' => 'Iuran kegiatan minat bakat, klub sains, seni, & olahraga',
                'amount' => 35000.00,
            ],
            [
                'code' => 'KOM',
                'name' => 'Iuran Komite Sekolah',
                'description' => 'Iuran paguyuban komite sekolah & pengembangan karakter',
                'amount' => 40000.00,
            ],
        ];

        $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        if (!$activeYear) {
            return;
        }

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

        foreach ($posts as $postData) {
            $post = PaymentPost::firstOrCreate(
                ['code' => $postData['code']],
                [
                    'name' => $postData['name'],
                    'description' => $postData['description'],
                ]
            );

            $bill = PaymentBill::firstOrCreate(
                [
                    'payment_post_id' => $post->id,
                    'academic_year_id' => $activeYear->id,
                    'name' => $postData['name'] . ' - TA ' . $activeYear->name,
                ],
                [
                    'type' => 'bulanan',
                    'amount' => $postData['amount'],
                    'description' => $postData['description'],
                ]
            );

            foreach ($students as $student) {
                $existing = StudentPaymentBill::where('student_id', $student->id)
                    ->where('payment_bill_id', $bill->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $monthlyAmount = $bill->amount;
                $totalAmount = $monthlyAmount * 12;

                $studentBill = StudentPaymentBill::create([
                    'student_id' => $student->id,
                    'payment_bill_id' => $bill->id,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                ]);

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

                $studentBill->recalculateStatus();
            }
        }
    }
}
