<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\ProfessionalType;

class ProfessionalTypesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'plumber', 'name' => 'Plumber'],
            ['code' => 'electrician', 'name' => 'Electrician'],
            ['code' => 'general_contractor', 'name' => 'General Contractor'],
        ];

        foreach ($data as $item) {
            ProfessionalType::query()->updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name']]
            );
        }
    }
}
