<?php

namespace Database\Seeders;

use App\Models\CanteenCategory;
use App\Models\CanteenItem;
use App\Models\CanteenStall;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CanteenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Kantin role exists
        Role::firstOrCreate(
            ['slug' => 'kantin'],
            [
                'name' => 'Kantin / Vendor',
                'description' => 'Role Pengelola Vendor Kantin Sekolah',
                'guard_name' => 'web',
                'is_active' => true,
            ]
        );

        // Seed Canteen Stalls (Vendors)
        $stallsData = [
            [
                'name' => 'Kantin Ibu Ani',
                'owner_name' => 'Ibu Ani Srimulyani',
                'phone' => '081234567890',
                'banner' => null,
                'logo' => null,
                'description' => 'Menyediakan masakan nusantara rumahan lezat, nasi goreng spesial, piscok crispy, dan olahan makanan higienis.',
                'open_time' => '07:00',
                'close_time' => '16:00',
                'operating_hours' => '07:00 - 16:00 WIB',
                'rating' => 4.9,
            ],
            [
                'name' => 'Kantin Mas Budi',
                'owner_name' => 'Budi Santoso',
                'phone' => '081298765432',
                'banner' => null,
                'logo' => null,
                'description' => 'Ahlinya ayam geprek krispi sambal korek pedas gurih, kentang goreng keju, dan olahan ayam kekinian.',
                'open_time' => '07:00',
                'close_time' => '16:00',
                'operating_hours' => '07:00 - 16:00 WIB',
                'rating' => 4.8,
            ],
            [
                'name' => 'Kantin Mbak Siti',
                'owner_name' => 'Siti Nurhaliza',
                'phone' => '081377889900',
                'banner' => null,
                'logo' => null,
                'description' => 'Spesialis roti bakar lumer, mie goreng jawa khas, mie jawa kuah, dan aneka jajanan hangat anak sekolah.',
                'open_time' => '07:00',
                'close_time' => '16:00',
                'operating_hours' => '07:00 - 16:00 WIB',
                'rating' => 4.7,
            ],
            [
                'name' => 'Kantin Berkah',
                'owner_name' => 'H. Ahmad Supardi',
                'phone' => '081566778899',
                'banner' => null,
                'logo' => null,
                'description' => 'Kedai minuman segar dingin, es teh manis jumbo, jus alpukat kocok choco, jeruk peras alami, dan es susu.',
                'open_time' => '07:00',
                'close_time' => '16:00',
                'operating_hours' => '07:00 - 16:00 WIB',
                'rating' => 4.9,
            ],
        ];

        $stallsMap = [];
        foreach ($stallsData as $sData) {
            $stall = CanteenStall::updateOrCreate(
                ['slug' => Str::slug($sData['name'])],
                $sData
            );
            $stallsMap[$sData['name']] = $stall;
        }

        $categories = [
            [
                'name' => 'Makanan Utama',
                'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                'items' => [
                    [
                        'name' => 'Nasi Goreng Spesial Kantin',
                        'description' => 'Nasi goreng harum dengan telur mata sapi, ayam suwir, kerupuk, dan acak sayuran segar.',
                        'price' => 13500,
                        'original_price' => 15000,
                        'discount_percent' => 10,
                        'stock' => 50,
                        'image' => null,
                        'stall_name' => 'Kantin Ibu Ani',
                    ],
                    [
                        'name' => 'Ayam Geprek Sambal Korek',
                        'description' => 'Dada/paha ayam crispy dibalut sambal korek pedas gurih komplit dengan nasi hangat.',
                        'price' => 14400,
                        'original_price' => 16000,
                        'discount_percent' => 10,
                        'stock' => 45,
                        'image' => null,
                        'stall_name' => 'Kantin Mas Budi',
                    ],
                    [
                        'name' => 'Mie Goreng Telur Sosis',
                        'description' => 'Mie goreng jawa lezat bertabur potongan sosis panggang dan telur orak-arik.',
                        'price' => 10200,
                        'original_price' => 12000,
                        'discount_percent' => 15,
                        'stock' => 60,
                        'image' => null,
                        'stall_name' => 'Kantin Mbak Siti',
                    ],
                    [
                        'name' => 'Bakso Kuah Sapi Mantap',
                        'description' => 'Bakso daging sapi halus & urat dengan mie kuning, bihun, dan kuah kaldu sapi kaya rempah.',
                        'price' => 14000,
                        'original_price' => 14000,
                        'discount_percent' => 0,
                        'stock' => 40,
                        'image' => null,
                        'stall_name' => 'Kantin Ibu Ani',
                    ],
                ]
            ],
            [
                'name' => 'Minuman Segar',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'items' => [
                    [
                        'name' => 'Es Teh Manis Jumbo',
                        'description' => 'Teh melati seduh alami dengan gula asli yang segar dingin menghempas dahaga.',
                        'price' => 4000,
                        'original_price' => 5000,
                        'discount_percent' => 20,
                        'stock' => 150,
                        'image' => null,
                        'stall_name' => 'Kantin Berkah',
                    ],
                    [
                        'name' => 'Es Jeruk Peras Alami',
                        'description' => 'Perasan jeruk segar asli tanpa pemanis buatan, kaya vitamin C.',
                        'price' => 5000,
                        'original_price' => 6000,
                        'discount_percent' => 16,
                        'stock' => 100,
                        'image' => null,
                        'stall_name' => 'Kantin Berkah',
                    ],
                    [
                        'name' => 'Jus Alpukat Kocok Choco',
                        'description' => 'Jus alpukat mentega kental disiram susu coklat manis lezat.',
                        'price' => 8500,
                        'original_price' => 10000,
                        'discount_percent' => 15,
                        'stock' => 30,
                        'image' => null,
                        'stall_name' => 'Kantin Berkah',
                    ],
                    [
                        'name' => 'Susu Coklat Dingin',
                        'description' => 'Susu uht rasa coklat kental nikmat dan menyegarkan.',
                        'price' => 7000,
                        'original_price' => 7000,
                        'discount_percent' => 0,
                        'stock' => 80,
                        'image' => null,
                        'stall_name' => 'Kantin Berkah',
                    ],
                ]
            ],
            [
                'name' => 'Camilan & Snack',
                'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                'items' => [
                    [
                        'name' => 'Roti Bakar Coklat Keju',
                        'description' => 'Roti tawar lembut dipanggang dengan mentega, isian coklat leleh dan parutan keju melimpah.',
                        'price' => 8000,
                        'original_price' => 10000,
                        'discount_percent' => 20,
                        'stock' => 40,
                        'image' => null,
                        'stall_name' => 'Kantin Mbak Siti',
                    ],
                    [
                        'name' => 'Pisang Coklat Crispy (Piscok)',
                        'description' => 'Pisang raja manis terbungkus kulit lumpia renyah dengan lelehan coklat premium (isi 4 pcs).',
                        'price' => 6400,
                        'original_price' => 8000,
                        'discount_percent' => 20,
                        'stock' => 50,
                        'image' => null,
                        'stall_name' => 'Kantin Ibu Ani',
                    ],
                    [
                        'name' => 'Kentang Goreng Keju',
                        'description' => 'French fries stik renyah bertabur bumbu keju gurih hangat.',
                        'price' => 8500,
                        'original_price' => 10000,
                        'discount_percent' => 15,
                        'stock' => 60,
                        'image' => null,
                        'stall_name' => 'Kantin Mas Budi',
                    ],
                    [
                        'name' => 'Risoles Mayones Daging',
                        'description' => 'Risoles isi smoked beef, telur, dan mayones lumer gurih (isi 3 pcs).',
                        'price' => 9000,
                        'original_price' => 9000,
                        'discount_percent' => 0,
                        'stock' => 35,
                        'image' => null,
                        'stall_name' => 'Kantin Ibu Ani',
                    ],
                ]
            ],
            [
                'name' => 'Paket Hemat',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'items' => [
                    [
                        'name' => 'Paket Hemat Nasi Goreng + Es Teh',
                        'description' => 'Porsi kenyang Nasi Goreng Spesial komplit + Es Teh Manis Jumbo segar.',
                        'price' => 14400,
                        'original_price' => 18000,
                        'discount_percent' => 20,
                        'stock' => 30,
                        'image' => null,
                        'stall_name' => 'Kantin Ibu Ani',
                    ],
                    [
                        'name' => 'Paket Hemat Ayam Geprek + Es Jeruk',
                        'description' => 'Ayam Geprek Sambal Korek pedas nikmat + Es Jeruk Peras Alami.',
                        'price' => 15000,
                        'original_price' => 20000,
                        'discount_percent' => 25,
                        'stock' => 30,
                        'image' => null,
                        'stall_name' => 'Kantin Mas Budi',
                    ],
                ]
            ]
        ];

        foreach ($categories as $catData) {
            $category = CanteenCategory::updateOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'icon' => $catData['icon'],
                    'is_active' => true,
                ]
            );

            foreach ($catData['items'] as $itemData) {
                $stall = $stallsMap[$itemData['stall_name']] ?? null;
                
                CanteenItem::updateOrCreate(
                    ['slug' => Str::slug($itemData['name'])],
                    array_merge($itemData, [
                        'category_id' => $category->id,
                        'stall_id' => $stall?->id,
                    ])
                );
            }
        }
    }
}
