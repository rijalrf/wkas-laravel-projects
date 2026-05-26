<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Iuran Wajib',
                'image_url' => 'https://cdn-icons-png.flaticon.com/512/3135/3135706.png',
            ],
            [
                'name' => 'Konsumsi',
                'image_url' => 'https://cdn-icons-png.flaticon.com/512/3075/3075977.png',
            ],
            [
                'name' => 'Operasional',
                'image_url' => 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
            ],
            [
                'name' => 'Transportasi',
                'image_url' => 'https://cdn-icons-png.flaticon.com/512/3097/3097180.png',
            ],
            [
                'name' => 'Kesehatan',
                'image_url' => 'https://cdn-icons-png.flaticon.com/512/2966/2966327.png',
            ],
            [
                'name' => 'Lain-lain',
                'image_url' => 'https://cdn-icons-png.flaticon.com/512/3502/3502685.png',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
