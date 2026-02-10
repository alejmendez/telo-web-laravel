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
            'Empresa',
            'Independiente',
        ];

        foreach ($data as $item) {
            ProfessionalType::query()->updateOrCreate(
                ['code' => Str::slug($item)],
                ['name' => $item]
            );
        }
    }
}
