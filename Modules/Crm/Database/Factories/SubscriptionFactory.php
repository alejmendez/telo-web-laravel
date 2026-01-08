<?php

namespace Modules\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Crm\Models\Professional;
use Modules\Crm\Models\Subscription;
use Modules\Crm\Models\SubscriptionPlan;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $professional = Professional::query()->inRandomOrder()->first();
        $plan = SubscriptionPlan::query()->inRandomOrder()->first();
        $status = $this->faker->randomElement(['pending', 'active', 'rejected']);

        $activeAt = $status === 'active' ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
        $expiresAt = $status === 'active' && $plan ? (clone $activeAt)?->modify('+'.$plan->duration_months.' months') : null;

        return [
            'professional_id' => $professional?->id,
            'subscription_plan_id' => $plan?->id,
            'status' => $status,
            'active_at' => $activeAt,
            'expires_at' => $expiresAt,
        ];
    }
}
