<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $guruRole = Role::where('slug', 'guru')->first();
        $bendaharaRole = Role::where('slug', 'bendahara')->first();
        $operatorRole = Role::where('slug', 'operator')->first();
        $guruBkRole = Role::where('slug', 'guru-bk')->first();

        // Super Admin
        $hasSuperAdmin = User::whereHas('roles', function ($query) {
            $query->where('slug', 'super-admin');
        })->exists();

        if (!$hasSuperAdmin) {
            $superAdmin = User::firstOrCreate(['email' => 'admin@gmail.com'], [
                'name' => 'Super Administrator',
                'password' => Hash::make('admin'),
                'phone' => '081234567890',
                'nip' => '19700101 199503 1 001',
                'status' => 'active',
            ]);
            if ($superAdminRole) {
                $superAdmin->assignRole($superAdminRole);
            }
        }

        // Admin
        $admin = User::firstOrCreate(['email' => 'admin@sekolah.id'], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'nip' => '19820315 200801 1 003',
            'status' => 'active',
        ]);
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // Daftar Guru Resmi dengan NIP
        $teachers = [
            [
                'email' => 'guru@sekolah.id',
                'name' => 'John Teacher, S.Pd.',
                'phone' => '081234567892',
                'nip' => '19850512 201001 1 002',
            ],
            [
                'email' => 'budi.teacher@sekolah.id',
                'name' => 'Drs. Budi Santoso, M.Pd.',
                'phone' => '081234567895',
                'nip' => '19750812 200501 1 003',
            ],
            [
                'email' => 'siti.teacher@sekolah.id',
                'name' => 'Siti Aminah, S.Pd.',
                'phone' => '081234567896',
                'nip' => '19820315 200802 2 005',
            ],
            [
                'email' => 'ahmad.fauzi@sekolah.id',
                'name' => 'Ahmad Fauzi, S.Si., M.Si.',
                'phone' => '081234567897',
                'nip' => '19800625 200604 1 001',
            ],
            [
                'email' => 'dewi.lestari@sekolah.id',
                'name' => 'Dewi Lestari, S.Hum.',
                'phone' => '081234567898',
                'nip' => '19900214 201502 2 004',
            ],
            [
                'email' => 'hendra.wijaya@sekolah.id',
                'name' => 'Hendra Wijaya, S.Kom.',
                'phone' => '081234567899',
                'nip' => '19920708 201801 1 007',
            ],
            [
                'email' => 'ratna.sari@sekolah.id',
                'name' => 'Ratna Sari, S.Pd.',
                'phone' => '081234567800',
                'nip' => '19870919 201101 2 002',
            ],
            [
                'email' => 'bambang.h@sekolah.id',
                'name' => 'Bambang Hariyanto, M.T.',
                'phone' => '081234567801',
                'nip' => '19780405 200312 1 006',
            ],
        ];

        foreach ($teachers as $t) {
            $teacherUser = User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name' => $t['name'],
                    'password' => Hash::make('password'),
                    'phone' => $t['phone'],
                    'nip' => $t['nip'],
                    'status' => 'active',
                ]
            );

            if ($guruRole && !$teacherUser->hasRole('guru')) {
                $teacherUser->assignRole($guruRole);
            }
        }

        // Bendahara
        $bendahara = User::firstOrCreate(['email' => 'bendahara@sekolah.id'], [
            'name' => 'Jane Finance',
            'password' => Hash::make('password'),
            'phone' => '081234567893',
            'nip' => '19890420 201202 2 006',
            'status' => 'active',
        ]);
        if ($bendaharaRole) {
            $bendahara->assignRole($bendaharaRole);
        }

        // Operator
        $operator = User::firstOrCreate(['email' => 'operator@sekolah.id'], [
            'name' => 'Operator Sekolah',
            'password' => Hash::make('password'),
            'phone' => '081234567894',
            'nip' => '19950110 202001 1 009',
            'status' => 'active',
        ]);
        if ($operatorRole) {
            $operator->assignRole($operatorRole);
        }

        // Guru BK / Konselor
        $bkUsers = [
            [
                'email' => 'guru.bk@sekolah.id',
                'name' => 'Drs. Bambang Sujatmiko, S.Psi., M.Pd.',
                'phone' => '081234567881',
                'nip' => '19830412 200901 1 004',
            ],
            [
                'email' => 'bk@sekolah.id',
                'name' => 'Siti Rahmawati, S.Psi., M.Psi.',
                'phone' => '081234567882',
                'nip' => '19880721 201402 2 008',
            ],
        ];

        foreach ($bkUsers as $bkData) {
            $bkUser = User::firstOrCreate(['email' => $bkData['email']], [
                'name' => $bkData['name'],
                'password' => Hash::make('password'),
                'phone' => $bkData['phone'],
                'nip' => $bkData['nip'],
                'status' => 'active',
            ]);

            if ($guruBkRole && !$bkUser->hasRole('guru-bk')) {
                $bkUser->assignRole($guruBkRole);
            }
        }
    }
}
