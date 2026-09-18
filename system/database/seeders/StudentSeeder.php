<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\Major;
use App\Models\Role;
use App\Models\SpmbRegistration;
use App\Models\Student;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentRole = Role::where('slug', 'student')->first();
        $calonSiswaRole = Role::where('slug', 'calon-siswa')->first();
        $wave = Wave::where('status', 'active')->first() ?? Wave::first();
        $currentYear = date('Y');

        $classes = ClassModel::with('major')->get();

        if ($classes->isEmpty()) {
            $this->call(ClassSeeder::class);
            $classes = ClassModel::with('major')->get();
        }

        // Map classes by slug or index for easy distribution
        $classByMajor = [];
        foreach ($classes as $cls) {
            $classByMajor[$cls->major?->code ?? 'GENERIC'][] = $cls;
        }

        // ============================================
        // 1. SISWA UTAMA DEMO (Active Student)
        // Email: siswa@gmail.com | Password: siswa
        // ============================================
        $siswaUser = User::firstOrCreate(
            ['email' => 'siswa@gmail.com'],
            [
                'name' => 'Ahmad Siswa',
                'password' => Hash::make('siswa'),
                'phone' => '081234567895',
                'status' => 'active',
            ]
        );
        if ($studentRole && !$siswaUser->hasRole('student')) {
            $siswaUser->assignRole($studentRole);
        }

        $defaultClass = $classes->where('slug', 'x-ipa-1')->first() ?? $classes->first();

        Student::updateOrCreate(
            ['nisn' => '123456789'],
            [
                'user_id' => $siswaUser->id,
                'nik' => '3201234567890001',
                'gender' => 'male',
                'birth_place' => 'Jakarta',
                'birth_date' => '2008-01-15',
                'address' => 'Jl. Pendidikan No. 123, Jakarta',
                'phone' => '081234567895',
                'email' => 'siswa@gmail.com',
                'class_id' => $defaultClass?->id,
                'major_id' => $defaultClass?->major_id,
                'parent_name' => 'Bapak Orang Tua',
                'parent_phone' => '081234567896',
                'parent_address' => 'Jl. Pendidikan No. 123, Jakarta',
                'photo' => null,
                'pin' => Hash::make('123456'),
            ]
        );

        // ============================================
        // 2. CALON SISWA DEMO (Prospective SPMB)
        // Email: calonsiswa@gmail.com | Password: siswa
        // ============================================
        $calonSiswaUser = User::firstOrCreate(
            ['email' => 'calonsiswa@gmail.com'],
            [
                'name' => 'Budi Calon Siswa',
                'password' => Hash::make('siswa'),
                'phone' => '081234567897',
                'status' => 'active',
            ]
        );
        if ($calonSiswaRole && !$calonSiswaUser->hasRole('calon-siswa')) {
            $calonSiswaUser->assignRole($calonSiswaRole);
        }

        $targetMajor = Major::where('code', 'TKJ')->first() ?? Major::first();

        SpmbRegistration::updateOrCreate(
            ['registration_number' => 'PMB-' . $currentYear . '-0001'],
            [
                'user_id' => $calonSiswaUser->id,
                'wave_id' => $wave?->id,
                'nisn' => '0987654321',
                'nik' => '3201234567890002',
                'full_name' => 'Budi Calon Siswa',
                'gender' => 'male',
                'birth_place' => 'Bandung',
                'birth_date' => '2009-05-20',
                'address' => 'Jl. Merdeka No. 456, Bandung',
                'phone' => '081234567897',
                'email' => 'calonsiswa@gmail.com',
                'parent_name' => 'Ibu Orang Tua',
                'parent_phone' => '081234567898',
                'parent_address' => 'Jl. Merdeka No. 456, Bandung',
                'origin_school' => 'SMP Negeri 1 Bandung',
                'class_id' => null,
                'major_id' => $targetMajor?->id,
                'status' => 'submitted',
                'verified_by' => null,
                'verified_at' => null,
                'verification_notes' => null,
                'payment_proof' => null,
                'payment_status' => 'pending',
            ]
        );

        // ============================================
        // 3. DAFTAR SISWA AKTIF TERLETAK DI SETIAP KELAS & JURUSAN
        // ============================================
        $studentsList = [
            // IPA
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.siswa@gmail.com', 'nisn' => '1234567891', 'nik' => '3201234567890003', 'gender' => 'female', 'city' => 'Surabaya', 'class_slug' => 'x-ipa-1'],
            ['name' => 'Andi Pratama', 'email' => 'andi.siswa@gmail.com', 'nisn' => '1234567892', 'nik' => '3201234567890004', 'gender' => 'male', 'city' => 'Yogyakarta', 'class_slug' => 'x-ipa-2'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.siswa@gmail.com', 'nisn' => '1234567893', 'nik' => '3201234567890005', 'gender' => 'female', 'city' => 'Semarang', 'class_slug' => 'xi-ipa-1'],
            ['name' => 'Rian Hidayat', 'email' => 'rian.siswa@gmail.com', 'nisn' => '1234567894', 'nik' => '3201234567890006', 'gender' => 'male', 'city' => 'Bogor', 'class_slug' => 'xii-ipa-1'],
            
            // IPS
            ['name' => 'Fajar Nugraha', 'email' => 'fajar.siswa@gmail.com', 'nisn' => '1234567895', 'nik' => '3201234567890007', 'gender' => 'male', 'city' => 'Bandung', 'class_slug' => 'x-ips-1'],
            ['name' => 'Nadia Rahma', 'email' => 'nadia.siswa@gmail.com', 'nisn' => '1234567896', 'nik' => '3201234567890008', 'gender' => 'female', 'city' => 'Surakarta', 'class_slug' => 'xi-ips-1'],
            ['name' => 'Rizky Maulana', 'email' => 'rizky.siswa@gmail.com', 'nisn' => '1234567897', 'nik' => '3201234567890009', 'gender' => 'male', 'city' => 'Malang', 'class_slug' => 'xii-ips-1'],
            
            // BHS
            ['name' => 'Putri Ayu', 'email' => 'putri.siswa@gmail.com', 'nisn' => '1234567898', 'nik' => '3201234567890010', 'gender' => 'female', 'city' => 'Denpasar', 'class_slug' => 'x-bhs-1'],

            // TKJ
            ['name' => 'Bayu Saputra', 'email' => 'bayu.siswa@gmail.com', 'nisn' => '1234567899', 'nik' => '3201234567890011', 'gender' => 'male', 'city' => 'Tangerang', 'class_slug' => 'x-tkj-1'],
            ['name' => 'Dinda Kirana', 'email' => 'dinda.siswa@gmail.com', 'nisn' => '1234567900', 'nik' => '3201234567890012', 'gender' => 'female', 'city' => 'Bekasi', 'class_slug' => 'xi-tkj-1'],

            // AKL
            ['name' => 'Eka Wahyuni', 'email' => 'eka.siswa@gmail.com', 'nisn' => '1234567901', 'nik' => '3201234567890013', 'gender' => 'female', 'city' => 'Depok', 'class_slug' => 'x-akl-1'],
            ['name' => 'Gilang Ramadhan', 'email' => 'gilang.siswa@gmail.com', 'nisn' => '1234567902', 'nik' => '3201234567890014', 'gender' => 'male', 'city' => 'Cirebon', 'class_slug' => 'xi-akl-1'],

            // PMN
            ['name' => 'Hani Pertiwi', 'email' => 'hani.siswa@gmail.com', 'nisn' => '1234567903', 'nik' => '3201234567890015', 'gender' => 'female', 'city' => 'Cimahi', 'class_slug' => 'x-pmn-1'],
        ];

        foreach ($studentsList as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('siswa'),
                    'phone' => '08123' . rand(1000000, 9999999),
                    'status' => 'active',
                ]
            );

            if ($studentRole && !$user->hasRole('student')) {
                $user->assignRole($studentRole);
            }

            $targetClass = $classes->where('slug', $data['class_slug'])->first() ?? $classes->random();

            Student::updateOrCreate(
                ['nisn' => $data['nisn']],
                [
                    'user_id' => $user->id,
                    'nik' => $data['nik'],
                    'gender' => $data['gender'],
                    'birth_place' => $data['city'],
                    'birth_date' => '2008-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'address' => 'Jl. Nusantara No. ' . rand(1, 100) . ', ' . $data['city'],
                    'phone' => $user->phone,
                    'email' => $data['email'],
                    'class_id' => $targetClass->id,
                    'major_id' => $targetClass->major_id,
                    'parent_name' => 'Orang Tua ' . $data['name'],
                    'parent_phone' => '0813' . rand(1000000, 9999999),
                    'parent_address' => 'Jl. Nusantara No. ' . rand(1, 100) . ', ' . $data['city'],
                    'photo' => null,
                    'pin' => Hash::make('123456'),
                ]
            );
        }

        // ============================================
        // 4. DAFTAR CALON SISWA (SPMB) LAGI
        // ============================================
        $additionalCalonSiswa = [
            ['name' => 'Eko Prasetyo', 'email' => 'eko.calon@gmail.com', 'nisn' => '0987654322', 'nik' => '3201234567890016', 'gender' => 'male', 'city' => 'Malang', 'school' => 'SMP Negeri 2 Malang', 'major_code' => 'TKJ'],
            ['name' => 'Fitri Handayani', 'email' => 'fitri.calon@gmail.com', 'nisn' => '0987654323', 'nik' => '3201234567890017', 'gender' => 'female', 'city' => 'Medan', 'school' => 'SMP Negeri 1 Medan', 'major_code' => 'IPA'],
            ['name' => 'Gita Gutawa', 'email' => 'gita.calon@gmail.com', 'nisn' => '0987654324', 'nik' => '3201234567890018', 'gender' => 'female', 'city' => 'Palembang', 'school' => 'SMP Negeri 3 Palembang', 'major_code' => 'AKL'],
        ];

        $regNum = 2;
        foreach ($additionalCalonSiswa as $calonData) {
            $user = User::firstOrCreate(
                ['email' => $calonData['email']],
                [
                    'name' => $calonData['name'],
                    'password' => Hash::make('siswa'),
                    'phone' => '0814' . rand(1000000, 9999999),
                    'status' => 'active',
                ]
            );

            if ($calonSiswaRole && !$user->hasRole('calon-siswa')) {
                $user->assignRole($calonSiswaRole);
            }

            $major = Major::where('code', $calonData['major_code'])->first() ?? Major::first();

            SpmbRegistration::updateOrCreate(
                ['registration_number' => 'PMB-' . $currentYear . '-' . str_pad($regNum, 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $user->id,
                    'wave_id' => $wave?->id,
                    'nisn' => $calonData['nisn'],
                    'nik' => $calonData['nik'],
                    'full_name' => $calonData['name'],
                    'gender' => $calonData['gender'],
                    'birth_place' => $calonData['city'],
                    'birth_date' => '2009-04-12',
                    'address' => 'Jl. Calon No. ' . rand(1, 50),
                    'phone' => $user->phone,
                    'email' => $calonData['email'],
                    'parent_name' => 'Orang Tua ' . $calonData['name'],
                    'parent_phone' => '0815' . rand(1000000, 9999999),
                    'parent_address' => 'Jl. Calon No. ' . rand(1, 50),
                    'origin_school' => $calonData['school'],
                    'class_id' => null,
                    'major_id' => $major?->id,
                    'status' => 'submitted',
                    'verified_by' => null,
                    'verified_at' => null,
                    'verification_notes' => null,
                    'payment_proof' => null,
                    'payment_status' => 'pending',
                ]
            );
            $regNum++;
        }
    }
}
