<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\Category;
use Modules\Crm\Models\Subcategory;
use Illuminate\Support\Str;

class CategoriesSubcategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Electricidad',
            'Gas',
            'Plomería',
            'Climatización y Aire Acondicionado',
            'Calefacción',
            'Cerrajería',
            'Pintura',
            'Carpintería',
            'Albañilería',
            'Obras Menores',
            'Mantención General',
            'Instalación de Electrodomésticos',
            'Reparación de Electrodomésticos',
            'Impermeabilización',
            'Techos y Canaletas',
            'Pisos y Revestimientos',
            'Vidriería',
            'Mallas de Seguridad',
            'Control de Plagas',
            'Jardinería',
            'Limpieza Técnica',
            'Remodelación Parcial',
            'Energía Solar y Eficiencia Energética',
        ];

        foreach ($data as $categoryName) {
            $category = Category::query()->updateOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
        }
    }
}
