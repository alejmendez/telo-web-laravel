<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\Category;
use Modules\Crm\Models\Subcategory;

class CategoriesSubcategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Plumbing' => ['Leak Repair', 'Installation'],
            'Electrical' => ['Wiring', 'Panel Upgrade'],
        ];

        foreach ($data as $categoryName => $subcats) {
            $category = Category::query()->updateOrCreate(
                ['name' => $categoryName],
                ['slug' => strtolower(str_replace(' ', '-', $categoryName))]
            );

            foreach ($subcats as $sub) {
                Subcategory::query()->updateOrCreate(
                    ['category_id' => $category->id, 'name' => $sub],
                    ['slug' => strtolower(str_replace(' ', '-', $sub))]
                );
            }
        }
    }
}
