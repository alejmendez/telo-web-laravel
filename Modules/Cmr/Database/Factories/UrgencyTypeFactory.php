<?php

namespace Modules\Cmr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cmr\Models\UrgencyType;

class UrgencyTypeFactory extends Factory
{
    protected $model = UrgencyType::class;

    public function definition(): array
    {
        $codes = ['low', 'medium', 'high'];
        $code = $this->faker->unique()->randomElement($codes);
        $map = [
            'low' => ['name' => 'Low', 'priority_weight' => 1, 'sla_hours' => 72],
            'medium' => ['name' => 'Medium', 'priority_weight' => 2, 'sla_hours' => 48],
            'high' => ['name' => 'High', 'priority_weight' => 3, 'sla_hours' => 24],
        ];

        return [
            'code' => $code,
            'name' => $map[$code]['name'],
            'priority_weight' => $map[$code]['priority_weight'],
            'sla_hours' => $map[$code]['sla_hours'],
        ];
    }
}

