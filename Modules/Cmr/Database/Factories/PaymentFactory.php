<?php

namespace Modules\Cmr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cmr\Models\Payment;
use Modules\Cmr\Models\Professional;
use Modules\Cmr\Models\Subscription;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $professional = Professional::query()->inRandomOrder()->first();
        $subscription = Subscription::query()->inRandomOrder()->first();
        $status = $this->faker->randomElement(['pending', 'paid', 'failed']);

        return [
            'professional_id' => $professional?->id,
            'subscription_id' => $this->faker->boolean(70) ? $subscription?->id : null,
            'amount_cents' => $this->faker->numberBetween(1000, 100000),
            'currency' => $this->faker->randomElement(['USD', 'CLP', 'ARS']),
            'status' => $status,
            'paid_at' => $status === 'paid' ? $this->faker->dateTimeBetween('-3 months', 'now') : null,
            'external_ref' => $this->faker->optional()->uuid(),
        ];
    }
}

