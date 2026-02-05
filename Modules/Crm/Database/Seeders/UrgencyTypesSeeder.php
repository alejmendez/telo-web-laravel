<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Crm\Models\UrgencyType;

class UrgencyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Alta', 'priority_weight' => 3, 'sla_hours' => 24],
            ['name' => 'Media', 'priority_weight' => 2, 'sla_hours' => 48],
            ['name' => 'Baja', 'priority_weight' => 1, 'sla_hours' => 72],
        ];

        foreach ($data as $item) {
            UrgencyType::query()->updateOrCreate(
                ['name' => $item['name']],
                ['code' => Str::slug($item['name']), 'priority_weight' => $item['priority_weight'], 'sla_hours' => $item['sla_hours']]
            );
        }
    }
}
