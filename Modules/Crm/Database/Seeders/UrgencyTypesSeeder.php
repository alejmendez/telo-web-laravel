<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\UrgencyType;

class UrgencyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'low', 'name' => 'Low', 'priority_weight' => 1, 'sla_hours' => 72],
            ['code' => 'medium', 'name' => 'Medium', 'priority_weight' => 2, 'sla_hours' => 48],
            ['code' => 'high', 'name' => 'High', 'priority_weight' => 3, 'sla_hours' => 24],
        ];

        foreach ($data as $item) {
            UrgencyType::query()->updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name'], 'priority_weight' => $item['priority_weight'], 'sla_hours' => $item['sla_hours']]
            );
        }
    }
}
