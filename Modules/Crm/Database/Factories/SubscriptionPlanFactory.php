<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\SubscriptionPlan;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->randomElement(['trial', 'standard', 'premium']);

        $map = [
            'trial' => ['name' => 'Trial', 'duration_months' => 3, 'price_cents' => 0, 'currency' => 'USD', 'is_trial' => true],
            'standard' => ['name' => 'Standard', 'duration_months' => 12, 'price_cents' => 19900, 'currency' => 'USD', 'is_trial' => false],
            'premium' => ['name' => 'Premium', 'duration_months' => 12, 'price_cents' => 39900, 'currency' => 'USD', 'is_trial' => false],
        ];

        return array_merge(['code' => $code], $map[$code]);
    }
}
