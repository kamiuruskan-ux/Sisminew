<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\FinancialCategory;
use App\Models\PaymentPost;
use Illuminate\Database\Seeder;

class FinancialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bank Accounts / Rekening Kas
        $b1 = BankAccount::updateOrCreate(
            ['bank_name' => 'Kas Tunai Bendahara'],
            [
                'account_name' => 'Kas Operasional Sekolah (Tunai)',
                'account_number' => null,
                'type' => 'cash',
                'initial_balance' => 5000000,
                'current_balance' => 5000000,
                'description' => 'Uang tunai fisik pada kasir bendahara sekolah',
                'is_active' => true,
            ]
        );

        $b2 = BankAccount::updateOrCreate(
            ['bank_name' => 'Bank BNI'],
            [
                'account_name' => 'Rekening Utama Sekolah BNI',
                'account_number' => '0987654321',
                'type' => 'bank',
                'initial_balance' => 25000000,
                'current_balance' => 25000000,
                'description' => 'Rekening giro penerimaan pembayaran sekolah',
                'is_active' => true,
            ]
        );

        // 2. Financial Categories
        $categories = [
            [
                'code' => 'KAT-IN-01',
                'name' => 'Pembayaran Siswa (SPP & Tagihan)',
                'type' => 'pemasukan',
                'description' => 'Pemasukan dari pembayaran SPP bulanan & uang pangkal siswa',
                'is_active' => true,
            ],
            [
                'code' => 'KAT-IN-02',
                'name' => 'Dana BOS (Bantuan Operasional Sekolah)',
                'type' => 'pemasukan',
                'description' => 'Pemasukan dana BOS dari pemerintah',
                'is_active' => true,
            ],
            [
                'code' => 'KAT-IN-03',
                'name' => 'Sumbangan & Donasi',
                'type' => 'pemasukan',
                'description' => 'Sumbangan, donasi, atau bantuan alumni/masyarakat',
                'is_active' => true,
            ],
            [
                'code' => 'KAT-EX-01',
                'name' => 'Gaji & Honor Guru/Staf',
                'type' => 'pengeluaran',
                'description' => 'Pengeluaran untuk konsumsi & honor mengajar guru/staf',
                'is_active' => true,
            ],
            [
                'code' => 'KAT-EX-02',
                'name' => 'Belanja ATK & Peralatan Kantor',
                'type' => 'pengeluaran',
                'description' => 'Pembelian kertas, tinta, alat tulis, & inventaris kelas',
                'is_active' => true,
            ],
            [
                'code' => 'KAT-EX-03',
                'name' => 'Listrik, Air & Internet',
                'type' => 'pengeluaran',
                'description' => 'Pembayaran tagihan rutin bulanan utilitas sekolah',
                'is_active' => true,
            ],
            [
                'code' => 'KAT-EX-04',
                'name' => 'Pemeliharaan Gedung & Fasilitas',
                'type' => 'pengeluaran',
                'description' => 'Renovasi ringan, AC, kebersihan, & perawatan gedung',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            FinancialCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Sample Payment Posts
        $posts = [
            [
                'code' => 'SPP',
                'name' => 'Sumbangan Pembinaan Pendidikan (SPP)',
                'description' => 'Tagihan iuran bulanan siswa',
            ],
            [
                'code' => 'GEDUNG',
                'name' => 'Uang Pangkal / Pembangunan Gedung',
                'description' => 'Tagihan bebas cicilan penerimaan siswa baru',
            ],
            [
                'code' => 'SERAGAM',
                'name' => 'Uang Seragam & Atribut',
                'description' => 'Tagihan seragam sekolah dan kartu siswa',
            ],
        ];

        foreach ($posts as $post) {
            PaymentPost::firstOrCreate(['code' => $post['code']], $post);
        }
    }
}
