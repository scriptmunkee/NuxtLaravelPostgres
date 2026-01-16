<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Dogs',
                'slug' => 'dogs',
                'description' => 'Browse dogs for sale, adoption, and stud services',
                'icon' => '🐕',
                'display_order' => 1,
                'is_active' => true,
            ],
            // Future categories - currently inactive
            // [
            //     'name' => 'Cats',
            //     'slug' => 'cats',
            //     'description' => 'Browse cats for sale and adoption',
            //     'icon' => '🐈',
            //     'display_order' => 2,
            //     'is_active' => false,
            // ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
