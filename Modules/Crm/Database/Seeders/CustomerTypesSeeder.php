<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\CustomerType;

class CustomerTypesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'dueno-de-casa', 'name' => 'Dueño de casa'],
            ['code' => 'dueno-de-negocio', 'name' => 'Dueño de negocio'],
            ['code' => 'empresa', 'name' => 'Empresa'],
        ];

        foreach ($data as $item) {
            CustomerType::query()->updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name']]
            );
        }
    }
}
