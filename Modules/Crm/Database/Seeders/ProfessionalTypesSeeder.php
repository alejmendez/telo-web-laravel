<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\ProfessionalType;
use Illuminate\Support\Str;

class ProfessionalTypesSeeder extends Seeder
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

        foreach ($data as $item) {
            ProfessionalType::query()->updateOrCreate(
                ['code' => Str::slug($item)],
                ['name' => $item]
            );
        }
    }
}
