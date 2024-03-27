<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Samsung',
                'description' => 'Samsung is a South Korean multinational conglomerate headquartered in Samsung Town, Seoul, South Korea. It is the world\'s largest electronic manufacturing company.',
                'image' => 'samsung.png',
                'user_id' => 1
            ],
            [
                'name' => 'Apple',
                'description' => 'Apple Inc. is an American multinational technology company headquartered in Cupertino, California. Apple is the world\'s largest technology company by revenue.',
                'image' => 'apple.png',
                'user_id' => 1
            ],
            [
                'name' => 'Xiaomi',
                'description' => 'Xiaomi is a Chinese multinational conglomerate headquartered in Hangzhou, Zhejiang, China. It is the world\'s largest mobile phone manufacturer.',
                'image' => 'xiaomi.png',
                'user_id' => 1
            ]
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
