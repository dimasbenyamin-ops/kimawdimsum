<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Seed the menus table with popular dimsum items.
     *
     * Categories: siomay | hakau | lumpia | bao | shumai | minuman | lainnya
     */
    public function run(): void
    {
        $menus = [
            // ---- SIOMAY ----
            [
                'name'        => 'Siomay Ayam',
                'description' => 'Siomay kukus berbahan ayam cincang pilihan, disajikan dengan kuah kaldu gurih dan taburan bawang goreng.',
                'category'    => 'siomay',
                'price'       => 15000,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Siomay Udang',
                'description' => 'Siomay premium isi udang segar yang kenyal dan lembut, dibungkus kulit wonton tipis.',
                'category'    => 'siomay',
                'price'       => 18000,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Siomay Kombinasi',
                'description' => 'Paduan siomay ayam dan udang dalam satu sajian, cocok untuk yang ingin mencoba keduanya.',
                'category'    => 'siomay',
                'price'       => 22000,
                'sort_order'  => 3,
            ],

            // ---- HAKAU ----
            [
                'name'        => 'Hakau Udang',
                'description' => 'Dumpling klasik Kanton dengan isi udang segar, dibungkus kulit transparan yang tipis dan dikukus sempurna.',
                'category'    => 'hakau',
                'price'       => 20000,
                'sort_order'  => 10,
            ],
            [
                'name'        => 'Hakau Scallop',
                'description' => 'Hakau premium dengan isian scallop pilihan, lembut di dalam dengan tekstur kulit yang kenyal.',
                'category'    => 'hakau',
                'price'       => 28000,
                'sort_order'  => 11,
            ],

            // ---- LUMPIA ----
            [
                'name'        => 'Lumpia Kulit Tahu',
                'description' => 'Lumpia gurih berbungkus kulit tahu yang renyah, diisi campuran sayuran segar dan jamur.',
                'category'    => 'lumpia',
                'price'       => 12000,
                'sort_order'  => 20,
            ],
            [
                'name'        => 'Lumpia Udang Crispy',
                'description' => 'Lumpia goreng isi udang dengan lapisan luar yang super renyah dan isian yang melimpah.',
                'category'    => 'lumpia',
                'price'       => 16000,
                'sort_order'  => 21,
            ],

            // ---- BAO ----
            [
                'name'        => 'Bao Char Siu',
                'description' => 'Bakpao kukus legendaris isi daging babi merah manis ala Hong Kong, lembut dan mengenyangkan.',
                'category'    => 'bao',
                'price'       => 20000,
                'sort_order'  => 30,
            ],
            [
                'name'        => 'Bao Ayam Kari',
                'description' => 'Bakpao isi ayam kari kuning yang kaya rempah, dimasak dengan santan pilihan.',
                'category'    => 'bao',
                'price'       => 18000,
                'sort_order'  => 31,
            ],

            // ---- SHUMAI ----
            [
                'name'        => 'Shumai Daging Sapi',
                'description' => 'Shumai tradisional isi daging sapi cincang halus dengan sedikit udang, bertekstur lembut.',
                'category'    => 'shumai',
                'price'       => 16000,
                'sort_order'  => 40,
            ],
            [
                'name'        => 'Shumai Jamur Truffle',
                'description' => 'Shumai premium dengan aroma truffle yang khas, cocok untuk pecinta kuliner tingkat tinggi.',
                'category'    => 'shumai',
                'price'       => 32000,
                'sort_order'  => 41,
            ],

            // ---- MINUMAN ----
            [
                'name'        => 'Es Teh Manis',
                'description' => 'Teh manis segar disajikan dingin, minuman klasik pelengkap dimsum.',
                'category'    => 'minuman',
                'price'       => 8000,
                'sort_order'  => 90,
            ],
            [
                'name'        => 'Teh Oolong Panas',
                'description' => 'Teh Oolong premium khas Tiongkok yang menenangkan, disajikan hangat dalam teko kecil.',
                'category'    => 'minuman',
                'price'       => 12000,
                'sort_order'  => 91,
            ],
            [
                'name'        => 'Jus Jeruk Segar',
                'description' => 'Perasan jeruk segar tanpa tambahan gula, menyegarkan di setiap tegukan.',
                'category'    => 'minuman',
                'price'       => 15000,
                'sort_order'  => 92,
            ],

            // ---- LAINNYA ----
            [
                'name'        => 'Cheung Fun Udang',
                'description' => 'Kwetiaw gulung lembut isi udang segar, disiram saus kecap manis dan wijen.',
                'category'    => 'lainnya',
                'price'       => 22000,
                'sort_order'  => 100,
            ],
            [
                'name'        => 'Lo Mai Gai',
                'description' => 'Nasi ketan gurih dibungkus daun lotus, diisi ayam, jamur, dan sosis lop cheong.',
                'category'    => 'lainnya',
                'price'       => 25000,
                'sort_order'  => 101,
            ],
        ];

        foreach ($menus as $data) {
            Menu::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'         => $data['name'],
                    'slug'         => Str::slug($data['name']),
                    'description'  => $data['description'],
                    'category'     => $data['category'],
                    'price'        => $data['price'],
                    'image_path'   => null, // images will be uploaded via admin panel
                    'is_available' => true,
                    'sort_order'   => $data['sort_order'],
                    'created_by'   => null,
                ]
            );
        }

        $this->command->info('✅ MenuSeeder: ' . count($menus) . ' menu items seeded.');
    }
}
