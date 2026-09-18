<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CanteenUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Canteen role exists
        $kantinRole = Role::firstOrCreate(
            ['slug' => 'kantin'],
            [
                'name' => 'Kantin / Vendor',
                'description' => 'Role Pengelola Vendor Kantin Sekolah',
                'guard_name' => 'web',
                'is_active' => true,
            ]
        );

        // List of Canteen Vendor User Accounts
        $canteenUsers = [
            [
                'name' => 'Pengelola Kantin Utama',
                'email' => 'kantin@sekolah.id',
                'phone' => '081234567899',
                'password' => 'password',
            ],
            [
                'name' => 'Kantin Ibu Ani',
                'email' => 'kantin.ani@sekolah.id',
                'phone' => '081234567890',
                'password' => 'password',
            ],
            [
                'name' => 'Kantin Mas Budi',
                'email' => 'kantin.budi@sekolah.id',
                'phone' => '081298765432',
                'password' => 'password',
            ],
            [
                'name' => 'Kantin Mbak Siti',
                'email' => 'kantin.siti@sekolah.id',
                'phone' => '081377889900',
                'password' => 'password',
            ],
            [
                'name' => 'Kantin Berkah',
                'email' => 'kantin.berkah@sekolah.id',
                'phone' => '081566778899',
                'password' => 'password',
            ],
        ];

        foreach ($canteenUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'password' => Hash::make($userData['password']),
                    'status' => 'active',
                ]
            );

            if ($kantinRole && !$user->hasRole('kantin')) {
                $user->assignRole($kantinRole);
            }
        }
    }
}
