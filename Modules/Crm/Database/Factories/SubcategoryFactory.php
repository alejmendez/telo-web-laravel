<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Crm\Models\Category;
use Modules\Crm\Models\Subcategory;

class SubcategoryFactory extends Factory
{
    protected $model = Subcategory::class;

    public function definition(): array
    {
        $category = Category::query()->inRandomOrder()->first();
        $name = $this->faker->unique()->words(3, true);

        return [
            'category_id' => $category?->id,
            'name' => ucfirst($name),
            'slug' => Str::slug($name.'-'.$this->faker->unique()->numberBetween(1, 10000)),
        ];
    }
}
